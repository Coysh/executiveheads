<?php
/**
 * Plugin Name: HR Heads JobAdder Integration
 * Description: A WordPress plugin to integrate with the JobAdder API.
 * Version: 1.0
 * Author: Tim Coysh
 */

// Include the necessary files.
require_once 'config.php';
require_once 'token-exchange.php';
require_once 'auth.php';
require_once 'refresh-token.php';
require_once 'api-calls.php';
require_once 'logging.php';
require_once 'jobs.php';

// Initialize the plugin.
add_action('template_redirect', 'check_specific_page_slug');

function check_specific_page_slug() {
	if (is_page('jobadder-import')) { // Replace 'my-custom-page' with your specific page slug

		jobadder_auth_init(); // Example function call
		exit;
	}
}
