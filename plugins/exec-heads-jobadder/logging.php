<?php
function jobadder_log($message, $level = 'info') {
	$log_dir = plugin_dir_path(__FILE__) . 'logs';
	if (!file_exists($log_dir)) {
		mkdir($log_dir, 0755, true); // Create the log directory if it doesn't exist
	}

	$log_file = $log_dir . '/log-' . date('Y-m-d') . '.log'; // One log file per day
	$log_message = "[" . date('Y-m-d H:i:s') . "] [$level] $message" . PHP_EOL; // Format the log message

	file_put_contents($log_file, $log_message, FILE_APPEND); // Append the message to the log file
}

// Scheduled task for cleaning up old log files
function jobadder_cleanup_old_logs() {
	$log_dir = plugin_dir_path(__FILE__) . 'logs';
	$files = glob($log_dir . '/log-*.log'); // Get all log files in the directory

	foreach ($files as $file) {
		if (filemtime($file) < time() - 14 * DAY_IN_SECONDS) { // WordPress constant for number of seconds in a day
			unlink($file); // Delete files older than 14 days
		}
	}
}

// Schedule the cleanup function if it's not already scheduled
if (!wp_next_scheduled('jobadder_daily_log_cleanup')) {
	wp_schedule_event(time(), 'daily', 'jobadder_daily_log_cleanup');
}

add_action('jobadder_daily_log_cleanup', 'jobadder_cleanup_old_logs'); // Hook the cleanup function to the scheduled action
