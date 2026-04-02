<?php
// Configuration for JobAdder OAuth 2.0 flow
define('JOBADDER_CLIENT_ID', 'zuufk3corpkubaubl52esrpsvm');
define('JOBADDER_CLIENT_SECRET', 'cdot4ef42ape5khgdhowxxdig4ehr2apae3emenm4dn5x4ct2l4u');
define('JOBADDER_REDIRECT_URI', 'https://fluent-plainly-magpie.ngrok-free.app/?page=jobadder_access_token');
define('JOBADDER_SCOPE', 'read write offline_access read_jobad'); // Define the scopes your application requires.
define('JOBADDER_JOB_BOARD', get_option('jobadder_settings')['jobadder_job_board'] ?? 0); // Example Job Board ID, replace with your actual Job Board ID if needed.

// Initialize plugin settings by adding option fields to store access and refresh tokens, including their expiration.
add_option('jobadder_access_token', '');
add_option('jobadder_refresh_token', '');
add_option('jobadder_access_token_expiration', 0);


// Hook to add the options page to the WordPress admin menu
add_action('admin_menu', 'jobadder_add_admin_menu');

function jobadder_add_admin_menu() {
	add_options_page(
		'JobAdder Settings', // Page title
		'JobAdder Settings', // Menu title
		'manage_options', // Capability required to see this option
		'jobadder-settings', // Menu slug
		'jobadder_options_page' // Function that outputs the options page content
	);
}

// Register settings, sections, and fields
add_action('admin_init', 'jobadder_settings_init');

function jobadder_settings_init() {
	// Register a new setting for "jobadder" page
	register_setting('jobadder', 'jobadder_settings');

	// Add a new section to a settings page
	add_settings_section(
		'jobadder_jobadder_section', // Section ID
		__('Job Board information', 'jobadder'), // Section title
		'jobadder_settings_section_callback', // Callback function
		'jobadder' // Page slug
	);

	// Add a new field to a section of a settings page
	add_settings_field(
		'jobadder_job_board', // Field ID
		__('Job Board ID', 'jobadder'), // Field title
		'jobadder_job_board_render', // Callback function to render the input field
		'jobadder', // Page slug
		'jobadder_jobadder_section' // Section ID
	);

	// Add a new section to a settings page
	add_settings_section(
		'jobadder_jobadder_section2', // Section ID
		__('Refresh options', 'jobadder'), // Section title
		'jobadder_settings_section_callback2', // Callback function
		'jobadder' // Page slug
	);
	// Add a new section to a settings page
	add_settings_section(
		'jobadder_jobadder_section3', // Section ID
		__('Last refreshed', 'jobadder'), // Section title
		'jobadder_settings_section_callback3', // Callback function
		'jobadder' // Page slug
	);

	 add_settings_field(
        'jobadder_last_refresh', // Field ID
        __('Last Refreshed', 'jobadder'), // Field title
        'jobadder_last_refresh_render', // Callback function to render the last refresh date
        'jobadder', // Page slug
        'jobadder_jobadder_section3' // Section ID
    );

	// Add a new button link
	add_settings_field(
        'jobadder_refresh_button', // Field ID
        __('Refresh all jobs', 'jobadder'), // Field title
        'jobadder_refresh_button_render', // Callback function to render the button
        'jobadder', // Page slug
        'jobadder_jobadder_section2' // Section ID
    );
}

function jobadder_last_refresh_render() {
    $last_refresh = get_option('jobadder_last_refresh');
    echo '<p id="lastrefresh">' . $last_refresh . '</p>';
}

function jobadder_refresh_button_render() {
    echo '<button class="button button-primary" type="button" id="jobadder-refresh-button" onclick="jobadderRefresh()">Refresh now</button>';
    echo '<script>
        function jobadderRefresh() {
			jQuery("#jobadder-refresh-button").text("Refreshing...");
            jQuery.post(ajaxurl, { action: "jobadder_refresh" }, function(response) {
				jQuery("#jobadder-refresh-button").text("Refreshed");
				jQuery("#lastrefresh").text("Just now");

            });
			return false;
        }
    </script>';
}

add_action('wp_ajax_jobadder_refresh', 'jobadder_refresh');
function jobadder_refresh() {
    // Perform the desired action here
    update_jobs_from_jobadder();
    wp_die(); // This is required to terminate immediately and return a proper response
}

// Section callback function is required but can be empty
function jobadder_settings_section_callback() {
	echo __('Set your JobAdder Job Board ID.', 'jobadder');
}
function jobadder_settings_section_callback2() {
	echo __('Manually refresh the jobs.', 'jobadder');
}
function jobadder_settings_section_callback3() {}

// Field callback function to render the input field
function jobadder_job_board_render() {
	$options = get_option('jobadder_settings');
	?>
	<input type='text' name='jobadder_settings[jobadder_job_board]' value='<?php echo $options['jobadder_job_board']; ?>'>
	<?php
}

function jobadder_logs_page_content() {
	$log_dir = plugin_dir_path(__FILE__) . 'logs';
	$log_file = $log_dir . '/log-' . date('Y-m-d') . '.log'; // Adjust if you want to view logs from different days

	echo '<div class="wrap">';

	// Check if today's log file exists
	if (file_exists($log_file)) {
		$logs = file_get_contents($log_file); // Read the log file content
		echo '<textarea readonly style="width: 100%; height: 500px;">' . esc_textarea($logs) . '</textarea>';
	} else {
		echo '<p>No logs found for today.</p>';
	}

	echo '</div>';
}

// Options page callback function to output the page HTML
function jobadder_options_page() {
	?>
	<form action='options.php' method='post'>
		<h2>JobAdder Settings</h2>
		<?php
		settings_fields('jobadder');
		do_settings_sections('jobadder');
		submit_button();
		?>
	</form>

	<h2>JobAdder Logs</h2>
	<?php
	jobadder_logs_page_content();
	?>
	<?php
}


add_filter('cron_schedules', 'add_custom_cron_interval');

function add_custom_cron_interval($schedules) {
	$schedules['every_thirty_minutes'] = array(
		'interval' => 1800, // 1800 seconds = 30 minutes
		'display'  => esc_html__('Every Thirty Minutes'),
	);

	return $schedules;
}

if (!wp_next_scheduled('update_jobs_from_jobadder_hook')) {
	wp_schedule_event(time(), 'every_thirty_minutes', 'update_jobs_from_jobadder_hook');
}

add_action('update_jobs_from_jobadder_hook', 'update_jobs_from_jobadder');

add_action('template_redirect', 'run_update_jobs_from_jobadder');

function run_update_jobs_from_jobadder() {
	if ($_SERVER['REQUEST_URI'] == '/jobadder-refresh') {
		update_jobs_from_jobadder();
	}
}

function override_plugins( $options ) {

	$options['clear_destination'] = true;
	$options['abort_if_destination_exists'] = false;

	return $options;
}
add_filter( 'upgrader_package_options', 'override_plugins' );