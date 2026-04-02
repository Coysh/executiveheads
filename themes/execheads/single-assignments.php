<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

get_header();

$assignment_locations = wp_get_post_terms(get_the_ID(), 'assignment_location');
$location = get_field('location');
$salary = get_field('salary');
$short_description = get_field('short_description');
$job_content = get_field('content');
$posted = get_field('posted');
$closing_date = get_field('closing_date');
$industries = get_field('industry');
$job_function = get_field('job_function');
$video = get_field('video');
$enquiry_text = get_field('enquiry_text');
$quote = get_field('quote');
$apply_url = get_field('apply_url');
$featured_image = get_the_post_thumbnail_url(get_the_ID(), 'large');

$location_items = [];

if (!empty($location)) {
	$location_items[] = $location;
} elseif (!empty($assignment_locations) && !is_wp_error($assignment_locations)) {
	foreach ($assignment_locations as $assignment_location) {
		if (!empty($assignment_location->name)) {
			$location_items[] = $assignment_location->name;
		}
	}
}

$location_markup = '';

if (!empty($location_items)) {
	$location_markup = implode('', array_map(static function ($location_item) {
		return '<div>' . esc_html($location_item) . '</div>';
	}, $location_items));
}

$hero_salary = get_field('custom_salary_display');

if (empty($hero_salary)) {
	$hero_salary = $salary;
}

$hero_bits = [];

if (!empty($location_items)) {
	$hero_bits[] = $location_markup;
}

if (!empty($hero_salary)) {
	$hero_bits[] = esc_html($hero_salary);
}

$author_id = $post->post_author;
$author = get_user_by('ID', $author_id);
$role_manager = get_field('role_managed_by');
$photo = [];

if (!is_array($role_manager)) {
	$role_manager = [];
}

$photo = !empty($role_manager['photograph']) && is_array($role_manager['photograph']) ? $role_manager['photograph'] : [];

if (empty($role_manager['name']) && $author) {
	$role_manager['name'] = $author->display_name;
}

if (empty($role_manager['email_address'])) {
	$role_manager['email_address'] = get_field('user_display_email', 'user_' . $author_id);
}

if (empty($role_manager['telephone'])) {
	$role_manager['telephone'] = get_field('user_contact_number', 'user_' . $author_id);
}

if (empty($role_manager['linkedin'])) {
	$role_manager['linkedin'] = get_field('user_linkedin_url', 'user_' . $author_id);
}

if (empty($photo)) {
	$author_photo = get_field('user_image', 'user_' . $author_id);
	if (is_array($author_photo)) {
		$photo = $author_photo;
	}
}

$photo_medium_url = !empty($photo['sizes']['medium']) ? $photo['sizes']['medium'] : '';
$photo_alt = !empty($photo['alt']) ? $photo['alt'] : (!empty($role_manager['name']) ? $role_manager['name'] : '');
?>

<?php $filled = false; ?>
<?php $categories = get_the_category(); ?>
<?php foreach ($categories as $category): ?>
	<?php if($category->cat_name == 'Recent'): $filled = true; endif; ?>
<?php endforeach; ?>

<div class="hero" style="background-image: url('<?php echo esc_url($featured_image ? $featured_image : get_template_directory_uri() . '/img/bg--brief.jpg'); ?>');">
	<div class="hero__content">
	  <div class="row">
	    <div class="column small-12 medium-10 large-6">
	      <?php if($filled == true): ?>
	      	<p class="no-margin-bottom"><strong class="uppercase">This role has been appointed</strong></p>
	      <?php endif ?>
	      <h1 class="hero__h1"><?php the_title(); ?></h1>
	      <?php if(!empty($hero_bits)): ?>
	      	<p><strong><?php echo implode('<br />', $hero_bits); ?></strong></p>
	      <?php endif; ?>
	      <?php if($short_description): ?>
	      	<p><?php echo esc_html($short_description); ?></p>
	      <?php endif; ?>
	      <div class="gm-top--2 gm-bottom--2">
	      	<?php if($filled != true): ?>
	        	<p><a href="#enquire" class="button button--white-outline">Enquire about this role</a></p>
        	<?php endif; ?>
	        <p><a href="<?php echo esc_url(get_site_url() . '/assignments'); ?>" class="white-text"><img src="<?php bloginfo('template_directory'); ?>/img/white-arrow--reversed.svg" width="15" />&nbsp;Back to current assignments</a></p>
	      </div>
	    </div>
	  </div>
	</div>
</div>
		
	<div class="row show-for-medium"><div class="column gm-top--2"></div></div>

      <div class="row shrink-width gm-top--2 gm-bottom--4">
        <div class="column small-12 medium-6 large-7 xlarge-8 float-right assignment__contents">
			<?php echo wp_kses_post($job_content); ?>
        </div>
		<div class="column small-12 medium-6 large-5 xlarge-4 assignment__details">
          <div class="summary bg--light-gray">
            <table class="summary__table">
              <tbody>
	              <?php if(!empty($location_markup)): ?>
	                <tr>
	                  <td class="uppercase">Location:</td>
	                  <td><?php echo $location_markup; ?></td>
	                </tr>
                <?php endif; ?>
	                <?php if($salary): ?>
	                <tr>
	                  <td class="uppercase">Salary:</td>
	                  <td><?php echo esc_html($salary); ?></td>
	                </tr>
                <?php endif; ?>
	                <?php if($posted): ?>
					<tr>
						<td class="uppercase">Posted:</td>
						<td><?php echo esc_html(date('jS F Y', strtotime($posted))); ?></td>
					</tr>
				<?php endif; ?>
				<?php if($closing_date): ?>
					<tr>
						<td class="uppercase">Closing date:</td>
						<td><?php echo esc_html(date('jS F Y', strtotime($closing_date))); ?></td>
					</tr>
				<?php endif; ?>
                <?php if($industries): ?>
	                <tr>
	                  <td class="uppercase">Industry:</td>
	                  <td>
	                  	<?php foreach ($industries as $industry): ?>
	                  		<?php echo esc_html($industry->name); ?>,
	                  	<?php endforeach; ?>
	                  </td>
	                </tr>
            	<?php endif; ?>
	                <?php if($job_function): ?>
	                <tr>
	                  <td class="uppercase">Job function:</td>
	                  <td><?php echo esc_html($job_function); ?></td>
	                </tr>
                <?php endif; ?>
              </tbody>
            </table>
	            <?php if($video): ?>
	            <div class="responsive-embed widescreen">
	              <?php echo $video; ?>
	            </div>
	            <p class="uppercase text-center">THE ROLE EXPLAINED: watch the video</p>
            <?php endif; ?>
          </div>
          <div class="summary bg--light-gray text-center medium-text-left">
	            <?php if(!empty($enquiry_text['title'])): ?>
	            	<h4 class="gm-bottom--1 regular"><?php echo esc_html($enquiry_text['title']); ?></h4>
	            <?php if(!empty($enquiry_text['text'])): ?>
	            		<p class="gm-bottom--2"><?php echo esc_html($enquiry_text['text']); ?></p>
	        	<?php endif; ?>
            	<a href="#" class="button button--green">Enquire here</a>
        	<?php endif; ?>

	            <?php if(!empty($role_manager['name'])): ?>
	            <div class="row gm-top--2 text-center medium-text-left">
	              <?php if($photo_medium_url): ?>
		              <div class="column small-12 medium-6 large-4 gm-bottom--2">
		                <img src="<?php echo esc_url($photo_medium_url); ?>" alt="<?php echo esc_attr($photo_alt); ?>" class="width-100 managedby__image" />
		              </div>
	          	  <?php endif; ?>
	              <div class="word-break column small-12 <?php if($photo_medium_url): ?>medium-6 large-8<?php endif; ?>">
	                <p>This role is managed by:<br /><?php echo esc_html($role_manager['name']); ?>
						<?php if(!empty($role_manager['email_address'])): ?>
							<br />
	                		<a class="gm-top--1 small button button--green" href="mailto:<?php echo antispambot(esc_attr($role_manager['email_address'])); ?>">Get in touch</a>
	            		<?php endif; ?>
						<?php if(!empty($role_manager['telephone'])): ?>
	                		<br />
	                		<a href="tel:<?php echo esc_attr(preg_replace('/[^\d\+]/', '', $role_manager['telephone'])); ?>">
	                			<?php echo esc_html($role_manager['telephone']); ?>
	                		</a>
                		<?php endif; ?>
	                	<?php if(!empty($role_manager['linkedin'])): ?>
	                		<br />
	                		<a href="<?php echo esc_url($role_manager['linkedin']); ?>">LinkedIn</a>
	            		<?php endif; ?></p>
	                	<p class="gm-top--2"><a target="_blank" rel="noopener noreferrer" href="mailto:?subject=<?php echo rawurlencode(get_the_title() . ': ' . get_permalink()); ?>" class="uppercase"><img src="<?php bloginfo('template_directory'); ?>/img/share.svg" alt="Share this role" width="18" />&nbsp;&nbsp;Share this role</a></p>
	              </div>
	            </div>
            <?php endif; ?>
          </div>

		  <?php if(!empty($quote['quote'])): ?>
	          <div class="summary--testimonial">
	            <div class="clearfix"></div>
	            <div class="column small-12 large-10 large-offset-1 testimonial__text">
	              <p class="blue-text"><?php echo esc_html($quote['quote']); ?></p>
	              <p><strong class="blue-text futura uppercase"><?php echo esc_html($quote['author']); ?></strong></p>
	            </div>
	            <div class="clearfix"></div>
	          </div>
          <?php endif ?>

          <h4 class="gm-bottom--2 gm-top--3 regular">More assignments like this</h4>
		  <?php $posts = get_posts(['post_type' => 'assignments', 'post_status' => 'publish', 'numberposts' => 4, 'orderby' => 'date', 'order' => 'DESC', 'category' => 1 ]); ?>
      	  <ul>
      	  	<?php foreach ($posts as $post): ?>          
	            <li><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></li>
            <?php endforeach; ?>
            <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>     

          </ul>
        </div>        
      </div>
		
	  <?php if($filled == true): ?>
	      <div class="bg--light-gray relative" id="enquire">
	        <div class="form">
	          <div class="row align-center shrink-width">
	            <div class="column small-12 medium-10 large-8 xlarge-6 gm-top--4 gm-bottom--1 text-center">
	              <h3 class="regular">Get in touch</h3>
	                <?php if(!empty($enquiry_text['text'])): ?>
	            		<p class="white-text"><?php echo esc_html($enquiry_text['text']); ?></p>
		        	<?php endif; ?>
	            </div>
	            <div class="column small-12 large-10 gm-bottom--4 text-center">
				  <?php if($apply_url): ?>
				  <iframe src="<?php echo esc_url($apply_url); ?>" width="600" height="900" style="width:100%;border:0;"></iframe>
				  <?php endif; ?>
	            </div>
	          </div>      
	        </div>
	      </div>
      <?php else: ?>
	      <div class="bg--blue-gradient relative" id="enquire">
	        <div class="form">
	          <div class="row align-center shrink-width">
	            <div class="column small-12 medium-10 large-8 xlarge-6 gm-top--4 gm-bottom--1 text-center">
	                <h3 class="white-text regular">Enquire about this role:<br /><?php the_title(); ?></h3>
	                <?php if(!empty($enquiry_text['text'])): ?>
	            		<p class="white-text"><?php echo esc_html($enquiry_text['text']); ?></p>
		        	<?php endif; ?>
	            </div>
	            <div class="column small-12 large-10 gm-bottom--4 text-center">
	              <?php if($apply_url): ?>
	              <iframe src="<?php echo esc_url($apply_url); ?>" width="600" height="900" style="width:100%;border:0;"></iframe>
	              <?php endif; ?>
	            </div>
	          </div>      
	        </div>
	      </div>

  	  <?php endif; ?>


<?php
get_footer();
