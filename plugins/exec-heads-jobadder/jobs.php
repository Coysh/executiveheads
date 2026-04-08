<?php
/**
 * Finds the ACF choice value for a given label from field choices.
 *
 * @param string $label The human-readable label you want to find the value for.
 * @param string $field_key The ACF field key.
 * @return string|null The value corresponding to the label, or null if not found.
 */
function find_acf_choice_value_by_label($label, $field_key) {
	if (!function_exists('get_field_object')) {
		return null;
	}

	// Get the field object using ACF's get_field_object function
	$field = get_field_object($field_key);
	
	if (!$field || !isset($field['choices'])) {
		// Field not found or doesn't have choices
		return null;
	}
	
	// Loop through the choices to find a matching label
	foreach ($field['choices'] as $value => $choice_label) {
		if ($choice_label === $label) {
			return $value; // Return the value that matches the label
		}
	}
	
	// Label not found in choices
	return null;
}

function jobadder_refresh_default_options($options = array()) {
	$defaults = array(
		'cli_mode' => false,
		'verbose' => false,
		'debug' => false,
		'dry_run' => false,
		'limit' => 0,
	);

	return array_merge($defaults, $options);
}

function jobadder_refresh_cli_write($message, $level, $options, $force = false) {
	if (!(defined('WP_CLI') && WP_CLI) || empty($options['cli_mode'])) {
		return;
	}

	$is_verbose = !empty($options['verbose']);
	$is_debug = !empty($options['debug']);
	$should_write = $force || in_array($level, array('warning', 'error'), true) || $is_verbose || $is_debug;

	if (!$should_write) {
		return;
	}

	if ($level === 'warning') {
		WP_CLI::warning($message);
		return;
	}

	if ($level === 'error') {
		WP_CLI::line('[error] ' . $message);
		return;
	}

	if ($level === 'debug' && !$is_debug) {
		return;
	}

	WP_CLI::line('[' . $level . '] ' . $message);
}

function jobadder_refresh_report(&$result, $options, $message, $level = 'info', $force_cli = false) {
	jobadder_log($message, $level);
	jobadder_refresh_cli_write($message, $level, $options, $force_cli);

	if ($level === 'warning') {
		$result['warnings'][] = $message;
	}

	if ($level === 'error') {
		$result['errors'][] = $message;
	}
}

function jobadder_refresh_result_template($options) {
	return array(
		'success' => true,
		'dry_run' => !empty($options['dry_run']),
		'start_time' => microtime(true),
		'duration_seconds' => 0,
		'warnings' => array(),
		'errors' => array(),
		'counts' => array(
			'fetched_ads' => 0,
			'processed_ads' => 0,
			'created_posts' => 0,
			'updated_posts' => 0,
			'skipped_ads' => 0,
			'expired_to_draft' => 0,
			'moved_to_recent' => 0,
			'recruiter_misses' => 0,
			'api_failures' => 0,
		),
	);
}

function jobadder_run_refresh($options = array()) {
	$options = jobadder_refresh_default_options($options);
	$result = jobadder_refresh_result_template($options);

	if (get_option('jobadder_needs_reauth')) {
		$result['success'] = false;
		jobadder_refresh_report($result, $options, 'Re-authentication required. Visit /jobadder-import to reconnect.', 'error', true);
		$result['duration_seconds'] = microtime(true) - $result['start_time'];
		return $result;
	}

	jobadder_refresh_report($result, $options, '', 'info');
	jobadder_refresh_report($result, $options, '#####Start updating jobs from JobAdder#####', 'info', true);
	jobadder_refresh_report($result, $options, 'Refresh options: dry_run=' . (!empty($options['dry_run']) ? 'yes' : 'no') . ', verbose=' . (!empty($options['verbose']) ? 'yes' : 'no') . ', debug=' . (!empty($options['debug']) ? 'yes' : 'no') . ', limit=' . (int) $options['limit'], 'debug');
	jobadder_refresh_report($result, $options, '', 'info');

	$job_ads = jobadder_get_job_ads_from_board();
	if (!empty($job_ads['_error'])) {
		$result['success'] = false;
		$result['counts']['api_failures']++;
		jobadder_refresh_report($result, $options, 'Unable to retrieve job ads. ' . $job_ads['_error'], 'error', true);
		$result['duration_seconds'] = microtime(true) - $result['start_time'];
		return $result;
	}

	if (!isset($job_ads['items']) || !is_array($job_ads['items'])) {
		$result['success'] = false;
		$result['counts']['api_failures']++;
		jobadder_refresh_report($result, $options, 'JobAdder ads payload did not include a valid items array.', 'error', true);
		$result['duration_seconds'] = microtime(true) - $result['start_time'];
		return $result;
	}

	$result['counts']['fetched_ads'] = count($job_ads['items']);
	$jobadder_board_id = isset($job_ads['_board_id']) ? $job_ads['_board_id'] : JOBADDER_JOB_BOARD;
	$jobadder_status = isset($job_ads['_status_code']) ? (int) $job_ads['_status_code'] : 0;
	$jobadder_total = isset($job_ads['_total_count']) ? (int) $job_ads['_total_count'] : $result['counts']['fetched_ads'];
	jobadder_refresh_report($result, $options, 'Fetched ' . $result['counts']['fetched_ads'] . ' ads from JobAdder board ' . $jobadder_board_id . ' (HTTP ' . $jobadder_status . ', totalCount=' . $jobadder_total . ').', 'info', true);

	if ($result['counts']['fetched_ads'] === 0) {
		jobadder_refresh_report($result, $options, 'No active ads were returned by JobAdder for this board. This run will continue for housekeeping but no jobs will be imported.', 'warning', true);
	}

	$assignments = new WP_Query(array(
		'post_type' => 'assignments',
		'post_status' => 'publish',
		'posts_per_page' => -1,
	));

	jobadder_refresh_report($result, $options, 'Looping through assignments to detect expired posts.', 'info', true);
	if ($assignments->have_posts()) {
		while ($assignments->have_posts()) {
			$assignments->the_post();
			$assignment_id = get_the_ID();
			$assignment_title = get_the_title();
			$closing_date = function_exists('get_field') ? get_field('closing_date', $assignment_id) : get_post_meta($assignment_id, 'closing_date', true);

			if (empty($closing_date)) {
				jobadder_refresh_report($result, $options, 'Assignment [' . $assignment_title . '] has no closing date. Skipping expiry draft check.', 'debug');
				continue;
			}

			try {
				$expire_date = new DateTime($closing_date);
				$expire_date->add(new DateInterval('P2M'));
			} catch (Exception $e) {
				jobadder_refresh_report($result, $options, 'Unable to parse closing date for assignment [' . $assignment_title . ']: ' . $closing_date, 'warning');
				continue;
			}

			$current_date = new DateTime();
			if ($current_date >= $expire_date) {
				$result['counts']['expired_to_draft']++;
				if (empty($options['dry_run'])) {
					wp_update_post(array(
						'ID' => $assignment_id,
						'post_status' => 'draft',
					));
					jobadder_refresh_report($result, $options, 'Assignment [' . $assignment_title . '] has expired, setting to draft.', 'info');
				} else {
					jobadder_refresh_report($result, $options, '[dry-run] Assignment [' . $assignment_title . '] would be set to draft.', 'info');
				}
			} else {
				jobadder_refresh_report($result, $options, 'Assignment [' . $assignment_title . '] is still active.', 'debug');
			}
		}
	}

	wp_reset_postdata();

	$recent_term = get_term_by('slug', 'recent', 'category');
	$current_term = get_term_by('slug', 'current', 'category');
	$recent_term_id = $recent_term ? $recent_term->term_id : 0;
	$current_term_id = $current_term ? $current_term->term_id : 0;

	if (!$recent_term_id || !$current_term_id) {
		jobadder_refresh_report($result, $options, 'Could not resolve one or more required category terms: recent/current.', 'warning', true);
	}

	$current_date = current_time('Ymd');
	$args = array(
		'post_type' => 'assignments',
		'posts_per_page' => -1,
		'meta_query' => array(
			array(
				'key' => 'closing_date',
				'value' => $current_date,
				'compare' => '<',
				'type' => 'DATE',
			),
		),
	);

	if ($recent_term_id) {
		$args['tax_query'] = array(
			array(
				'taxonomy' => 'category',
				'field' => 'term_id',
				'terms' => $recent_term_id,
				'operator' => 'NOT IN',
			),
		);
	}

	$assignments = new WP_Query($args);
	if ($assignments->have_posts()) {
		while ($assignments->have_posts()) {
			$assignments->the_post();
			$post_id = get_the_ID();
			$post_title = get_the_title($post_id);

			$result['counts']['moved_to_recent']++;
			if (empty($options['dry_run'])) {
				if ($recent_term_id) {
					wp_set_post_terms($post_id, array($recent_term_id), 'category', true);
				}

				if ($current_term_id) {
					wp_remove_object_terms($post_id, $current_term_id, 'category');
				}

				jobadder_refresh_report($result, $options, 'Assignment [' . $post_title . '] has expired, add to recent assignments.', 'info');
			} else {
				jobadder_refresh_report($result, $options, '[dry-run] Assignment [' . $post_title . '] would be moved to recent assignments.', 'info');
			}
		}
	}

	wp_reset_postdata();

	$job_count = 0;
	foreach ($job_ads['items'] as $ad_index => $ad_preview) {
		if (!empty($options['limit']) && $job_count >= (int) $options['limit']) {
			jobadder_refresh_report($result, $options, 'Reached --limit value of ' . (int) $options['limit'] . '. Remaining jobs were skipped.', 'warning', true);
			break;
		}

		$job_count++;
		jobadder_refresh_report($result, $options, '', 'info');
		jobadder_refresh_report($result, $options, '-----Start Processing Job #' . ($ad_index + 1) . '-----', 'info', true);

		if (empty($ad_preview['adId']) || empty($ad_preview['reference'])) {
			$result['counts']['skipped_ads']++;
			jobadder_refresh_report($result, $options, 'Ad payload is missing adId or reference. Skipping record.', 'warning');
			continue;
		}

		$ad = jobadder_get_job_ad_details($ad_preview['adId']);
		$job = jobadder_get_job_details($ad_preview['reference']);

		if (empty($ad)) {
			$result['counts']['api_failures']++;
			$result['counts']['skipped_ads']++;
			jobadder_refresh_report($result, $options, 'Unable to retrieve job ad details for adId ' . $ad_preview['adId'] . '.', 'error');
			continue;
		}

		if (empty($job)) {
			$result['counts']['api_failures']++;
			$result['counts']['skipped_ads']++;
			jobadder_refresh_report($result, $options, 'Unable to retrieve job details for: ' . ($ad['title'] ?? $ad_preview['adId']), 'error');
			continue;
		}

		$result['counts']['processed_ads']++;
		jobadder_refresh_report($result, $options, 'Job title: ' . ($ad['title'] ?? '(untitled)'), 'info');
		jobadder_refresh_report($result, $options, 'Job ID: ' . ($ad['adId'] ?? '(missing)'), 'info');

		$recruiter_email = $job['owner']['email'] ?? null;
		$recruiter_id = null;
		if ($recruiter_email) {
			jobadder_refresh_report($result, $options, 'Found recruiter email: ' . $recruiter_email, 'debug');

			$user_query = new WP_Query(array(
				'post_type' => 'team',
				'posts_per_page' => 1,
				'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'),
				'meta_query' => array(
					array(
						'key' => 'tm_email',
						'value' => (string) $recruiter_email,
						'compare' => '=',
					),
				),
			));

			$user_id = 0;
			if ($user_query->have_posts()) {
				while ($user_query->have_posts()) {
					$user_query->the_post();
					$user_id = get_the_ID();
				}
			}
			wp_reset_postdata();

			if (!$user_id) {
				$result['counts']['recruiter_misses']++;
				jobadder_refresh_report($result, $options, 'Could not find a team member with recruiter email ' . $recruiter_email . '.', 'warning');
			} else {
				$recruiter_id = array($user_id);
				jobadder_refresh_report($result, $options, 'Found team member id: ' . $user_id, 'debug');
			}
		}

		$owner_email = $job['owner']['email'] ?? null;
		$author = $owner_email ? get_user_by('email', $owner_email) : null;
		$author_id = $author ? $author->ID : null;

		if (!$author_id) {
			jobadder_refresh_report($result, $options, 'No WordPress user found for owner email: ' . ($owner_email ?: '(empty)') . '. Post author will be empty.', 'debug');
		}

		$locationName = null;
		$workTypeName = null;
		$location_term = null;
		$workType_term = null;
		$category_term = null;
		$category_terms = array();
		$location_terms = array();
		$workType_terms = array();
		$salary = '';

		if (isset($ad['portal']['fields']) && !empty($ad['portal']['fields'])) {
			foreach ($ad['portal']['fields'] as $field) {
				if (($field['fieldName'] ?? '') === 'Category') {
					$categoryName = $field['value'] ?? '';
					$category_term_obj = get_term_by('name', $categoryName, 'jobs_category');
					$category_term = $category_term_obj ? $category_term_obj->term_id : null;
				}

				if (($field['fieldName'] ?? '') === 'Location') {
					$locationName = $field['value'] ?? null;
					$location_term_obj = get_term_by('name', $locationName, 'jobs_category');
					$location_term = $location_term_obj ? $location_term_obj->term_id : null;
				}

				if (($field['fieldName'] ?? '') === 'Work Type') {
					$workTypeName = $field['value'] ?? null;
					$work_type_term_obj = get_term_by('name', $workTypeName, 'jobs_category');
					$workType_term = $work_type_term_obj ? $work_type_term_obj->term_id : null;
				}

				if (($field['fieldName'] ?? '') === 'Salary text') {
					$salary = $field['value'] ?? '';
				}
			}
		}

		if ($category_term) {
			$category_terms[] = $category_term;
		}

		if ($location_term) {
			$location_terms[] = $location_term;
		}

		if ($workType_term) {
			$workType_terms[] = $workType_term;
		}

		$metaValues = array(
			'custom_salary_display' => $salary,
			'salary' => $salary,
			'description' => $ad['description'] ?? '',
			'reference' => $ad['reference'] ?? '',
			'short_description' => $ad['summary'] ?? '',
			'location' => $locationName,
			'content' => $ad['description'] ?? '',
			'posted' => $ad['postedAt'] ?? '',
			'closing_date' => $ad['expiresAt'] ?? '',
			'apply_url' => $ad['links']['ui']['self'] ?? '',
			'jid' => $ad['adId'] ?? '',
			'choose_member' => $recruiter_id,
		);

		$post_args = array(
			'post_type' => 'assignments',
			'post_title' => $ad['title'] ?? '(untitled)',
			'post_name' => sanitize_title(($ad['title'] ?? 'job') . '-' . ($ad['adId'] ?? 'unknown')),
			'post_category' => array(1),
			'post_status' => 'publish',
			'meta_input' => $metaValues,
			'post_author' => $author_id,
		);

		$existing_job_query = new WP_Query(array(
			'post_type' => 'assignments',
			'post_status' => 'any',
			'meta_query' => array(
				array(
					'key' => 'jid',
					'value' => $ad['adId'],
					'compare' => '=',
				),
			),
			'posts_per_page' => 1,
		));

		$post_id = 0;
		if ($existing_job_query->have_posts()) {
			$existing_job_query->the_post();
			$post_id = get_the_ID();
			$post_args['ID'] = $post_id;

			if (empty($options['dry_run'])) {
				$update_result = wp_update_post($post_args, true);
				if (is_wp_error($update_result)) {
					$result['counts']['skipped_ads']++;
					jobadder_refresh_report($result, $options, 'Failed to update existing job [' . $post_id . ']: ' . $update_result->get_error_message(), 'error');
					wp_reset_postdata();
					continue;
				}
				$result['counts']['updated_posts']++;
				jobadder_refresh_report($result, $options, 'Updated existing job - ID: ' . $post_id, 'info', true);
			} else {
				$result['counts']['updated_posts']++;
				jobadder_refresh_report($result, $options, '[dry-run] Would update existing job - ID: ' . $post_id, 'info', true);
			}
		} else {
			if (empty($options['dry_run'])) {
				$insert_result = wp_insert_post($post_args, true);
				if (is_wp_error($insert_result)) {
					$result['counts']['skipped_ads']++;
					jobadder_refresh_report($result, $options, 'Failed to create new job [' . ($ad['title'] ?? '(untitled)') . ']: ' . $insert_result->get_error_message(), 'error');
					wp_reset_postdata();
					continue;
				}

				$post_id = (int) $insert_result;
				$result['counts']['created_posts']++;
				jobadder_refresh_report($result, $options, 'Created new job - ID: ' . $post_id, 'info', true);
			} else {
				$result['counts']['created_posts']++;
				jobadder_refresh_report($result, $options, '[dry-run] Would create new job [' . ($ad['title'] ?? '(untitled)') . '].', 'info', true);
			}
		}

		wp_reset_postdata();

		if (empty($options['dry_run']) && $post_id) {
			$locationValue = find_acf_choice_value_by_label($locationName, 'field_5863d63f45e80');
			if ($locationValue !== null && function_exists('update_field')) {
				update_field('field_5863d63f45e80', array($locationValue), $post_id);
			}

			$workTypeValue = find_acf_choice_value_by_label($workTypeName, 'field_5863a37bb379c');
			if ($workTypeValue !== null && function_exists('update_field')) {
				update_field('field_5863a37bb379c', array($workTypeValue), $post_id);
			}

			if (!function_exists('update_field')) {
				jobadder_refresh_report($result, $options, 'ACF update_field() is unavailable in this runtime; skipping ACF field writes for post ' . $post_id . '.', 'warning');
			}

			wp_set_post_terms($post_id, array_merge($location_terms, $workType_terms), 'jobs_category');
		} else {
			jobadder_refresh_report($result, $options, '[dry-run] Skipped ACF and taxonomy updates for this job.', 'debug');
		}

		jobadder_refresh_report($result, $options, '-----End Processing Job-----', 'info');
		jobadder_refresh_report($result, $options, ' ', 'info');
	}

	if (empty($options['dry_run'])) {
		update_option('jobadder_last_refresh', current_time('mysql'));
	} else {
		jobadder_refresh_report($result, $options, '[dry-run] Skipped updating option jobadder_last_refresh.', 'debug');
	}

	$result['duration_seconds'] = microtime(true) - $result['start_time'];

	jobadder_refresh_report($result, $options, '', 'info');
	jobadder_refresh_report($result, $options, '#####End updating jobs from JobAdder#####', 'info', true);
	jobadder_refresh_report($result, $options, 'Duration: ' . number_format($result['duration_seconds'], 2) . ' seconds.', 'info', true);
	jobadder_refresh_report($result, $options, '', 'info');

	if (!empty($result['errors'])) {
		$result['success'] = false;
	}

	return $result;
}

function update_jobs_from_jobadder() {
	jobadder_run_refresh();
}

if (defined('WP_CLI') && WP_CLI) {
	/**
	 * Updates jobs from JobAdder.
	 *
	 * ## OPTIONS
	 *
	 * [--verbose]
	 * : Show more detailed output.
	 *
	 * [--jobadder-debug]
	 * : Show very detailed debug output.
	 *
	 * [--dry-run]
	 * : Simulate changes without writing updates.
	 *
	 * [--limit=<number>]
	 * : Limit number of job ads processed.
	 *
	 * ## EXAMPLES
	 *
	 *     wp jobadder_update_jobs --verbose --jobadder-debug
	 *     wp jobadder_update_jobs --dry-run --limit=10
	 */
	function wp_cli_update_jobs_from_jobadder($args, $assoc_args) {
		$limit = 0;
		if (isset($assoc_args['limit'])) {
			$limit = (int) $assoc_args['limit'];
			if ($limit < 1) {
				WP_CLI::error('--limit must be a positive integer.');
			}
		}

		$options = array(
			'cli_mode' => true,
			'verbose' => isset($assoc_args['verbose']),
			'debug' => isset($assoc_args['jobadder-debug']),
			'dry_run' => isset($assoc_args['dry-run']),
			'limit' => $limit,
		);

		$result = jobadder_run_refresh($options);

		$summary = sprintf(
			'Summary: fetched=%d processed=%d created=%d updated=%d skipped=%d expired_to_draft=%d moved_to_recent=%d recruiter_misses=%d api_failures=%d duration=%.2fs',
			(int) $result['counts']['fetched_ads'],
			(int) $result['counts']['processed_ads'],
			(int) $result['counts']['created_posts'],
			(int) $result['counts']['updated_posts'],
			(int) $result['counts']['skipped_ads'],
			(int) $result['counts']['expired_to_draft'],
			(int) $result['counts']['moved_to_recent'],
			(int) $result['counts']['recruiter_misses'],
			(int) $result['counts']['api_failures'],
			(float) $result['duration_seconds']
		);

		WP_CLI::line($summary);

		if (!empty($result['warnings'])) {
			WP_CLI::line('Warnings:');
			foreach ($result['warnings'] as $warning_message) {
				WP_CLI::line(' - ' . $warning_message);
			}
		}

		if (!empty($result['errors'])) {
			WP_CLI::line('Errors:');
			foreach ($result['errors'] as $error_message) {
				WP_CLI::line(' - ' . $error_message);
			}

			WP_CLI::error('JobAdder refresh completed with errors.');
		}

		if (!empty($result['dry_run'])) {
			WP_CLI::success('Dry run completed successfully.');
			return;
		}

		WP_CLI::success('Jobs updated from JobAdder successfully.');
	}

	WP_CLI::add_command('jobadder_update_jobs', 'wp_cli_update_jobs_from_jobadder');
}




// If not an admin request run the function
#update_jobs_from_jobadder();
#1314996
#1187965

#add_action('wp_loaded', 'update_jobs_from_jobadder');

