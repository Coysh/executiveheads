<?php
/**
 * Finds the ACF choice value for a given label from field choices.
 *
 * @param string $label The human-readable label you want to find the value for.
 * @param string $field_key The ACF field key.
 * @return string|null The value corresponding to the label, or null if not found.
 */
function find_acf_choice_value_by_label($label, $field_key) {
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

function update_jobs_from_jobadder() {
    if (get_option('jobadder_needs_reauth')) {
        jobadder_log('Re-authentication required. Visit /jobadder-import to reconnect.', 'error');
        return;
    }

	jobadder_log('', 'info');  
	jobadder_log('#####Start updating jobs from JobAdder#####', 'info');  
	jobadder_log('', 'info');  

	// Assuming jobadder_get_job_ads_from_board() returns an array of job ads
	$job_ads = jobadder_get_job_ads_from_board();
	if (empty($job_ads) || !isset($job_ads['items']) || empty($job_ads['items'])) {
		jobadder_log('No job ads found or unable to retrieve job ads. Stop update.', 'error');
		return;
	}

	// Check each 'assignments' post and set it to draft if two weeks past the 'closing_date'
	$assignments = new WP_Query([
		'post_type' => 'assignments',
		'post_status' => 'publish',
		'posts_per_page' => -1,
	]);

	jobadder_log('Looping through assignments', 'info');  
	if ($assignments->have_posts()) {
		while ($assignments->have_posts()) {
			$assignments->the_post();
			$closing_date = get_field('closing_date', get_the_ID()); // ACF get_field to fetch 'closing_date'
			$expire_date = new DateTime($closing_date);
			$expire_date->add(new DateInterval('P2M')); // Add 2 months to the closing date
			
			$current_date = new DateTime(); // Today's date
			
			
			if ($current_date >= $expire_date) {
				jobadder_log('Assignment [' . get_the_title() . '] has expired, setting to draft', 'info');
				wp_update_post([
					'ID' => get_the_ID(),
					'post_status' => 'draft',
				]);
			} else {
				jobadder_log('Assignment [' . get_the_title() . '] is still active', 'info');
			}
		}
	}

	//25/04/2024

	wp_reset_postdata();

	// Fetch assignments with a past 'closing_date'

	$recent_term_id = get_term_by('slug', 'recent', 'category')->term_id; // Get "Recent" category ID
	$current_term_id = get_term_by('slug', 'current', 'category')->term_id; // Get "Current" category ID

	$current_date = current_time('Ymd'); // Get current date in same format as ACF date field
	$args = [
		'post_type' => 'assignments',
		'posts_per_page' => -1,
		'meta_query' => [
			[
				'key' => 'closing_date', // ACF Field Key
				'value' => $current_date,
				'compare' => '<', // Less than today's date
				'type' => 'DATE',
			],
		],
		'tax_query' => [ // Exclude assignments in the 'recent' category
			[
				'taxonomy' => 'category',
				'field' => 'term_id',
				'terms' => $recent_term_id,
				'operator' => 'NOT IN',
			],
		],
	];

	$assignments = new WP_Query($args);
	if ($assignments->have_posts()) {

		while ($assignments->have_posts()) {
			$assignments->the_post();
			$post_id = get_the_ID();

			// Add to "Recent" category
			wp_set_post_terms($post_id, [$recent_term_id], 'category', true);

			// Remove "Current" category
			wp_remove_object_terms($post_id, $current_term_id, 'category');

			$post_title = get_the_title($post_id);

			jobadder_log('Assignment ['.$post_title.'] has expired, add to recent assignments', 'info');  
		}
	}

	wp_reset_postdata();


	foreach ($job_ads['items'] as $ad) {

		jobadder_log('', 'info');  
		jobadder_log('-----Start Processing Job-----', 'info');  

		$ad = jobadder_get_job_ad_details($ad['adId']);
		$job = jobadder_get_job_details($ad['reference']);

		#echo "<pre>Ad:";print_r($ad);echo "\n\n\nJob:";print_r($job);echo "</pre>";

		if (empty($ad)) {
			jobadder_log('Unable to retrieve job ad details.', 'error');
			continue;
		}

		if (empty($job)) {
			jobadder_log('Unable to retrieve job details for: '.$ad['title'], 'error');
			continue;
		}

		jobadder_log('Job title: ' . $ad['title'], 'info');
		jobadder_log('Job ID: ' . $ad['adId'], 'info');

		//Get user id
		$recruiter_email = $job['owner']['email'] ?? null;
		$recruiter_id = null;
		if ($recruiter_email) {
			jobadder_log('Found recruiter email: '.$recruiter_email, 'info');

			//look up the assignment by jid
			$user_args = array(
				'post_type' => 'team',
				'posts_per_page' => 1,
				'post_status' => array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit'),							
				'meta_query' => array(
					array(
						'key' => 'tm_email',
						'value' => (string) $recruiter_email,
						'compare' => '='
					)
				),
			);
			#echo "<pre>";print_r($user_args);echo "</pre>";
			$user_query = new WP_Query($user_args);		   
			if($user_query->have_posts()) {
				while($user_query->have_posts()) {
					//update the assignment fields
					$user_query->the_post();
					$user_id = get_the_ID();
				}
			}


			if(!isset($user_id) || $user_id == '' || $user_id == null) {
				$recruiter_id = null;
				jobadder_log('Could not find a user with the email', 'error');
			} else {
				$recruiter_id = [$user_id];
				jobadder_log('Found user id: '.$user_id, 'info');
			}
		}



		$owner_email = $job['owner']['email'] ?? null;

		$author = get_user_by('email', $owner_email) ?? null;
		if ($author) {
			$author_id = $author->ID;
		} else {
			$author_id = null;
		}

		//Closing date
		$closing_date = $ad['expiresAt'] ?? null;

		// Update ACF date picker field `closing_date`
		if ($closing_date) {
			//update_field('closing_date', $closing_date, $post_id);
		}


		// Initialize variables
		$locationId = null;
		$locationName = null;
		$workTypeId = null;
		$workTypeName = null;
		$categoryId = null;
		$categoryName = null;
		$category_terms = [];
		$location_terms = [];
		$workType_terms = [];
		$salary = '';

		// Check if the 'fields' array exists and is not empty
		if (isset($ad['portal']['fields']) && !empty($ad['portal']['fields'])) {
			// Loop through each field
			foreach ($ad['portal']['fields'] as $field) {
				// Check for 'Category' field and assign values
				if ($field['fieldName'] == 'Category') {
					$categoryId = $field['valueId'];
					$categoryName = $field['value'];
					$category_term = get_term_by('name', $categoryName, 'jobs_category')->term_id ?? null;
				}
				// Check for 'Location' field and assign values
				if ($field['fieldName'] == 'Location') {
					$locationId = $field['valueId'];
					$locationName = $field['value'];
					$location_term = get_term_by('name', $locationName, 'jobs_category')->term_id ?? null;
				}
				// Check for 'Work Type' field and assign values
				if ($field['fieldName'] == 'Work Type') {
					$workTypeId = $field['valueId'];
					$workTypeName = $field['value'];
					$workType_term = get_term_by('name', $workTypeName, 'jobs_category')->term_id ?? null;
				}
				//Check for 'Salary text' field and assign values
				if ($field['fieldName'] == 'Salary text') {
					#echo "Found salary: ".$field['value']."\n";
					$salary = $field['value'];
				}
			}
		}

		//Get terms
		if($category_term) {
			$category_terms[] = $category_term;
		}
		if($location_term) {
			$location_terms[] = $location_term;
		}
		if($workType_term) {
			$workType_terms[] = $workType_term;
		}

		


		// Prepare data for custom post type 'jobs'
		$metaValues = array(
			'custom_salary_display' => $salary,
			'salary' => $salary,
			'description' => $ad['description'],
			'reference' => $ad['reference'],
			'short_description' => $ad['summary'],
			'location' => $locationName,
			'content' => $ad['description'],
			//'bullet_points' => implode("\n", $ad['bulletPoints']), // Convert bullet points array to a string
			//'screening_questions' => serialize($ad['screening']), // Serialize the screening questions array for storage
			'posted' => $ad['postedAt'],
			'closing_date' => $ad['expiresAt'],
			'apply_url' => $ad['links']['ui']['self'],
			'jid' => $ad['adId'],
			'choose_member' => $recruiter_id,
			// Add more meta values as needed
		);

		//echo "<pre>";print_r($metaValues);echo "</pre>";exit;

		// Convert job ad details to post args for wp_insert_post or wp_update_post
		$post_args = array(
			'post_type' => 'assignments',
			'post_title' => $ad['title'],
			'post_name'	 => sanitize_title($ad['title'] . '-' . $ad['adId']),
			'post_category' => array(1), //current
			'post_status' => 'publish',
			'meta_input' => $metaValues,
			'post_author'   => $author_id
		);

		// Find or create the job post
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

		if ($existing_job_query->have_posts()) {
			$existing_job_query->the_post();
			$post_id = get_the_ID();
			$post_args['ID'] = $post_id; // Specify ID to update the existing post
			wp_update_post($post_args);

			if (defined('WP_CLI') && WP_CLI) {
				WP_CLI::line('Updated existing job - ID: '.$post_id);
			}
			jobadder_log('Updated existing job - ID: ' . $post_id, 'info');
		} else {
			$post_id = wp_insert_post($post_args);
			if (defined('WP_CLI') && WP_CLI) {
				WP_CLI::line('Created new job - ID: '.$post_id);
			}
			jobadder_log('Created new job - ID: ' . $post_id, 'info');
		}

		// Example usage for a location field
		$locationValue = find_acf_choice_value_by_label($locationName, 'field_5863d63f45e80');
		if ($locationValue !== null) {
			update_field('field_5863d63f45e80', array($locationValue), $post_id);
		}

		// Example usage for a work type field
		$workTypeValue = find_acf_choice_value_by_label($workTypeName, 'field_5863a37bb379c');
		if ($workTypeValue !== null) {
			update_field('field_5863a37bb379c', array($workTypeValue), $post_id);
		}

		// Update taxonomy terms

		wp_set_post_terms($post_id, array_merge($location_terms,$workType_terms), 'jobs_category'); 
		

		wp_reset_postdata(); // Reset after each job ad processed

		// Handle taxonomy terms (e.g., categories, locations) assignment here
		// Example:
		// wp_set_post_terms($post_id, $term_ids, 'taxonomy_name');
		jobadder_log('-----End Processing Job-----', 'info');  
		jobadder_log(' ', 'info');  
		#exit; // Remove this line to process all job ads
	}

	update_option('jobadder_last_refresh', current_time('mysql'));

	jobadder_log('', 'info');  
	jobadder_log('#####End updating jobs from JobAdder#####', 'info');  
	jobadder_log('', 'info');  
}

if (defined('WP_CLI') && WP_CLI) {
	/**
	 * Updates jobs from JobAdder.
	 */
	function wp_cli_update_jobs_from_jobadder() {
		// Call your function here
		update_jobs_from_jobadder();

		// Output a success message
		WP_CLI::success('Jobs updated from JobAdder successfully.');
	}

	WP_CLI::add_command('jobadder_update_jobs', 'wp_cli_update_jobs_from_jobadder');
}




// If not an admin request run the function
#update_jobs_from_jobadder();
#1314996
#1187965

#add_action('wp_loaded', 'update_jobs_from_jobadder');

