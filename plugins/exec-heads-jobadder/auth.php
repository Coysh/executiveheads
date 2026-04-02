<?php
require_once 'token-exchange.php'; // Ensure token exchange function is available
require_once 'logging.php'; // Ensure logging functionality is available

// Redirects user to JobAdder authorization page
function jobadder_auth_init() {
	$authorization_url = 'https://id.jobadder.com/connect/authorize' . 
						'?response_type=code' .
						'&client_id=' . JOBADDER_CLIENT_ID .
						'&scope=' . urlencode(JOBADDER_SCOPE) .
						'&redirect_uri=' . urlencode(JOBADDER_REDIRECT_URI) .
						'&state=' . wp_create_nonce('jobadder_auth');

	header('Location: ' . $authorization_url);
	exit;
}

// Example usage based on the provided script
if (isset($_GET['code']) && isset($_GET['state']) && isset($_GET['page']) && $_GET['page'] == 'jobadder_access_token') {
	$tokens = jobadder_exchange_token($_GET['code']);
	if (!empty($tokens)) {
		jobadder_log('Access token and refresh token saved successfully.', 'info');
		echo "Access token saved<br>";
		echo "Access token: " . esc_html($tokens['access_token']) . "<br>";
	} else {
		jobadder_log('Failed to save access token and refresh token.', 'error');
		echo "Failed to save access token.<br>";
	}
	exit;
}
