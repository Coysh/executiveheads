<?php
// Include the logging functionality
require_once 'logging.php';

// Exchanges authorization code for an access token
function jobadder_exchange_token($code) {
	$token_url = 'https://id.jobadder.com/connect/token';
	$body = [
		'grant_type' => 'authorization_code',
		'client_id' => JOBADDER_CLIENT_ID,
		'client_secret' => JOBADDER_CLIENT_SECRET,
		'redirect_uri' => JOBADDER_REDIRECT_URI,
		'code' => $code,
	];

	$response = wp_remote_post($token_url, [
		'body' => $body,
		'headers' => [
			'Content-Type' => 'application/x-www-form-urlencoded',
		],
	]);

	if (is_wp_error($response)) {
		// Log the error and return an empty array to indicate failure
		jobadder_log('Failed to exchange token: ' . $response->get_error_message(), 'error');
		return [];
	}

	$body = wp_remote_retrieve_body($response);
	$data = json_decode($body, true);

	if (!isset($data['access_token'])) {
		// Log the error if access token is not present in the response
		jobadder_log('Access token not received in the exchange token response.', 'error');
		return [];
	}

	// Update token information in the database
	update_option('jobadder_access_token', $data['access_token']);
	$expires_in = $data['expires_in'];
	$expiration_time = time() + $expires_in;
	update_option('jobadder_access_token_expiration', $expiration_time);
	// Check if refresh token is provided before trying to save it
	if (isset($data['refresh_token'])) {
		update_option('jobadder_refresh_token', $data['refresh_token']);
	}

	jobadder_log('Token exchanged successfully.', 'info');
	return $data;
}
