<?php
require_once 'logging.php'; // Ensure logging functions are available

// Check if the current access token has expired
function token_is_expired() {
	$expiration_time = get_option('jobadder_access_token_expiration', 0);
	return time() >= $expiration_time;
}

// Refresh the access token using a refresh token
function jobadder_refresh_access_token($refresh_token) {
	$token_url = 'https://id.jobadder.com/connect/token';
	$body = [
		'grant_type' => 'refresh_token',
		'client_id' => JOBADDER_CLIENT_ID,
		'client_secret' => JOBADDER_CLIENT_SECRET,
		'refresh_token' => $refresh_token,
	];

	$response = wp_remote_post($token_url, [
		'body' => $body,
		'headers' => [
			'Content-Type' => 'application/x-www-form-urlencoded',
		],
	]);

	if (is_wp_error($response)) {
		jobadder_log('Failed to refresh token: ' . $response->get_error_message(), 'error');
		return [];
	}

	$body = wp_remote_retrieve_body($response);
	$data = json_decode($body, true);

	if (!isset($data['access_token'])) {
		jobadder_log('Access token not received in the refresh token response.', 'error');
		// Clear the stored tokens so we know re-auth is needed
		delete_option('jobadder_access_token');
		delete_option('jobadder_refresh_token');
		delete_option('jobadder_access_token_expiration');
		update_option('jobadder_needs_reauth', true);
		return [];
	}

	// Update token information in the database
	update_option('jobadder_access_token', $data['access_token']);
	$expires_in = $data['expires_in']; // Assuming the API response includes a new expires_in value
	$expiration_time = time() + $expires_in;
	update_option('jobadder_access_token_expiration', $expiration_time);
	if (isset($data['refresh_token'])) {
		update_option('jobadder_refresh_token', $data['refresh_token']); // Save new refresh token if provided
	}

	jobadder_log('Token refreshed successfully.', 'info');
	return $data;
}
