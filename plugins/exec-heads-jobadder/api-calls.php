<?php
require_once 'logging.php'; // Include logging functionality

function jobadder_normalize_api_url($url) {
	if (empty($url) || !is_string($url)) {
		return '';
	}

	if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
		return $url;
	}

	if (strpos($url, '/v2/') === 0) {
		return 'https://api.jobadder.com' . $url;
	}

	if (strpos($url, '/') === 0) {
		return 'https://api.jobadder.com/v2' . $url;
	}

	return 'https://api.jobadder.com/v2/' . ltrim($url, '/');
}

function jobadder_get_json($url, $access_token) {
	$response = wp_remote_get($url, [
		'headers' => [
			'Authorization' => 'Bearer ' . $access_token,
			'Content-Type' => 'application/json',
		],
	]);

	if (is_wp_error($response)) {
		return array(
			'ok' => false,
			'status_code' => 0,
			'error' => $response->get_error_message(),
			'data' => array(),
		);
	}

	$status_code = wp_remote_retrieve_response_code($response);
	$body = wp_remote_retrieve_body($response);
	$data = json_decode($body, true);

	if (!is_array($data)) {
		return array(
			'ok' => false,
			'status_code' => (int) $status_code,
			'error' => 'Response was not valid JSON.',
			'data' => array(),
		);
	}

	if ($status_code >= 400 || isset($data['error'])) {
		$error = $data['error'] ?? ($data['message'] ?? 'Unknown API error');
		return array(
			'ok' => false,
			'status_code' => (int) $status_code,
			'error' => $error,
			'data' => $data,
		);
	}

	return array(
		'ok' => true,
		'status_code' => (int) $status_code,
		'error' => '',
		'data' => $data,
	);
}

// Ensure the API call prerequisites are met (token validity and refresh if necessary)
function pre_jobadder_api_call() {
    if (token_is_expired()) {
        $refresh_token = get_option('jobadder_refresh_token');
        $new_tokens = jobadder_refresh_access_token($refresh_token);
        if (isset($new_tokens['access_token'])) {
            update_option('jobadder_access_token', $new_tokens['access_token']);
            $expiration_time = time() + $new_tokens['expires_in'];
            update_option('jobadder_access_token_expiration', $expiration_time);
            if (isset($new_tokens['refresh_token'])) {
                update_option('jobadder_refresh_token', $new_tokens['refresh_token']);
            }
            delete_option('jobadder_needs_reauth');
            return true;
        } else {
            jobadder_log('Unable to refresh token.', 'error');
            // Only redirect if we're in a normal web request (not CLI or cron)
            if (!defined('DOING_CRON') && !defined('WP_CLI') && !headers_sent()) {
                wp_redirect(home_url('/jobadder-import'));
                exit;
            }
            return false;
        }
    }
    return true;
}

// Function to retrieve jobs, ensuring token is valid or refreshed if needed
function jobadder_get_jobs() {
	if (!pre_jobadder_api_call()) return []; // Pre-check for token validity

	$access_token = get_option('jobadder_access_token'); // Re-fetch in case it was refreshed
	$api_url = 'https://api.jobadder.com/v2/jobs'; // API endpoint for jobs

	$response = wp_remote_get($api_url, [
		'headers' => [
			'Authorization' => 'Bearer ' . $access_token,
			'Content-Type' => 'application/json'
		],
	]);

	if (is_wp_error($response)) {
		jobadder_log('Failed to retrieve jobs: ' . $response->get_error_message(), 'error');
		return [];
	}

	$body = wp_remote_retrieve_body($response);
	$jobs = json_decode($body, true);

	if (isset($jobs['error'])) {
		jobadder_log('API returned an error: ' . $jobs['error'], 'error');
		return [];
	}

	return $jobs; // Assuming $jobs is an array of job listings
}

// Function to retrieve job boards, ensuring token is valid or refreshed if needed
function jobadder_get_job_boards() {
	if (!pre_jobadder_api_call()) return []; // Pre-check for token validity

	$access_token = get_option('jobadder_access_token'); // Re-fetch in case it was refreshed
	$api_url = 'https://api.jobadder.com/v2/jobboards'; // API endpoint for job boards

	$response = wp_remote_get($api_url, [
		'headers' => [
			'Authorization' => 'Bearer ' . $access_token,
			'Content-Type' => 'application/json'
		],
	]);

	if (is_wp_error($response)) {
		jobadder_log('Failed to retrieve job boards: ' . $response->get_error_message(), 'error');
		return [];
	}

	$body = wp_remote_retrieve_body($response);
	$job_boards = json_decode($body, true);

	if (isset($job_boards['error'])) {
		jobadder_log('API returned an error: ' . $job_boards['error'], 'error');
		return [];
	}

	return $job_boards; // Assuming $job_boards is an array of job boards
}


function jobadder_get_job_ads_from_board() {
	if (!pre_jobadder_api_call()) {
		return array(
			'items' => array(),
			'_error' => 'Access token invalid or refresh failed before API call.',
		);
	}

	$access_token = get_option('jobadder_access_token'); // Fetch the possibly refreshed token
	$job_board_id = JOBADDER_JOB_BOARD; // Use the predefined job board ID
	$api_url = 'https://api.jobadder.com/v2/jobboards/' . $job_board_id . '/ads'; // Construct the API endpoint URL

	if (empty($job_board_id)) {
		jobadder_log('Job board ID is not configured. Please set jobadder_job_board in plugin settings.', 'error');
		return array(
			'items' => array(),
			'_error' => 'Job board ID is not configured.',
		);
	}

	$all_items = array();
	$page_count = 0;
	$total_count = 0;
	$status_code = 0;
	$next_url = $api_url;
	$max_pages = (int) apply_filters('jobadder_ads_max_pages', 25);

	while (!empty($next_url) && $page_count < $max_pages) {
		$page_count++;
		$normalized_url = jobadder_normalize_api_url($next_url);
		$request = jobadder_get_json($normalized_url, $access_token);

		if (!$request['ok']) {
			$error_message = 'Failed to retrieve job ads for board ' . $job_board_id . ' on page ' . $page_count . ': HTTP ' . $request['status_code'] . ' - ' . $request['error'];
			jobadder_log($error_message, 'error');
			return array(
				'items' => array(),
				'_error' => $error_message,
				'_status_code' => (int) $request['status_code'],
				'_board_id' => $job_board_id,
			);
		}

		$status_code = (int) $request['status_code'];
		$job_ads = $request['data'];

		if (!isset($job_ads['items']) || !is_array($job_ads['items'])) {
			$job_ads['items'] = array();
		}

		$all_items = array_merge($all_items, $job_ads['items']);
		$total_count = isset($job_ads['totalCount']) ? (int) $job_ads['totalCount'] : count($all_items);
		jobadder_log('Fetched ads page ' . $page_count . ' for board ' . $job_board_id . ': items=' . count($job_ads['items']) . ', cumulative=' . count($all_items) . ', totalCount=' . $total_count, 'info');

		$next_url = $job_ads['links']['next'] ?? '';
	}

	if (!empty($next_url) && $page_count >= $max_pages) {
		jobadder_log('Stopped pagination for board ' . $job_board_id . ' after reaching max pages (' . $max_pages . ').', 'warning');
	}

	$job_ads = array(
		'items' => $all_items,
		'totalCount' => $total_count,
		'_status_code' => $status_code,
		'_board_id' => $job_board_id,
		'_total_count' => $total_count,
		'_pages_fetched' => $page_count,
	);

	jobadder_log('Fetched ads payload for board ' . $job_board_id . ': HTTP ' . $status_code . ', items=' . count($all_items) . ', totalCount=' . $total_count . ', pages=' . $page_count, 'info');

	return $job_ads; // Assuming $job_ads contains the list of job ads
}


function jobadder_get_job_ad_details($adId) {
	jobadder_log('Get job ad details for job ad: '.$adId, 'info');
	
	if (!pre_jobadder_api_call()) {
		jobadder_log('Access token invalid or expired, unable to retrieve job ad.', 'error');
		return []; // Ensure the token is valid or refreshed
	}

	$job_board_id = JOBADDER_JOB_BOARD; // Use the predefined job board ID
	$access_token = get_option('jobadder_access_token'); // Fetch the possibly refreshed token
	$api_url = 'https://api.jobadder.com/v2/jobboards/' . $job_board_id . '/ads/' . $adId; // Construct the API endpoint URL with both boardId and adId

	$response = wp_remote_get($api_url, [
		'headers' => [
			'Authorization' => 'Bearer ' . $access_token,
			'Content-Type' => 'application/json'
		],
	]);

	//Get status code and response for debugging
	if ( is_wp_error( $response ) ) {
		jobadder_log('Failed to retrieve job ad details: ' . $response->get_error_message(), 'error');
		return [];
	}

	if (is_wp_error($response)) {
		jobadder_log('Failed to retrieve job ad details: ' . $response->get_error_message(), 'error');
		return [];
	}

	$body = wp_remote_retrieve_body($response);
	$job_ad_details = json_decode($body, true);

	if (isset($job_ad_details['error'])) {
		jobadder_log('API returned an error while fetching job ad details: ' . $job_ad_details['error'], 'error');
		return [];
	}

	//If empty job ad details
	if (empty($job_ad_details)) {
		jobadder_log('Job ad details are empty for jobad : '.$adId, 'error');
	}

	return $job_ad_details; // Return the job ad details
}

function jobadder_get_job_details($jobId) {
	jobadder_log('Get job details for job: '.$jobId, 'info');

	if (!pre_jobadder_api_call()) {
		jobadder_log('Access token invalid or expired, unable to retrieve job details.', 'error');
		return []; // Ensure the token is valid or refreshed
	}

	$access_token = get_option('jobadder_access_token'); // Fetch the possibly refreshed token
	$api_url = 'https://api.jobadder.com/v2/jobs/' . $jobId; // Construct the API endpoint URL for jobs

	$response = wp_remote_get($api_url, [
		'headers' => [
			'Authorization' => 'Bearer ' . $access_token,
			'Content-Type' => 'application/json'
		],
	]);

	if (is_wp_error($response)) {
		jobadder_log('Failed to retrieve job details: ' . $response->get_error_message(), 'error');
		return [];
	}

	$body = wp_remote_retrieve_body($response);
	$job_details = json_decode($body, true);

	if (isset($job_details['error'])) {
		jobadder_log('API returned an error while fetching job details: ' . $job_details['error'], 'error');
		return [];
	}

	//If empty job details
	if (empty($job_details)) {
		jobadder_log('Job details are empty for job: '.$jobId, 'error');
	}

	return $job_details; // Return the job details
}




function test() {
	$job_ads = jobadder_get_job_ads_from_board();
	foreach ($job_ads['items'] as $ad) {
		//Get job ad
		$job_ad = jobadder_get_job_ad_details($ad['adId']);
		echo "<pre>";print_r($job_ad);echo "</pre>";
		exit;
	}
}
#test();
#echo "<pre>";print_r(jobadder_get_job_ads_from_board());echo "</pre>";exit;