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
?>

<?php $filled = false; ?>
<?php $categories = get_the_category(); ?>
<?php foreach ($categories as $category): ?>
	<?php if($category->cat_name == 'Recent'): $filled = true; endif; ?>
<?php endforeach; ?>

<div class="hero" style="background-image: url('<?php bloginfo('template_directory'); ?>/img/bg--brief.jpg');">
	<div class="hero__content">
	  <div class="row">
	    <div class="column small-12 medium-10 large-6">
	      <?php if($filled == true): ?>
	      	<p class="no-margin-bottom"><strong class="uppercase">This role has been filled</strong></p>
	      <?php endif ?>
	      <h1 class="hero__h1"><?php the_title(); ?></h1>
	      <p><strong><?php echo get_field('location'); ?><br /><?php echo get_field('salary'); ?></strong></p>
	      <p><?php echo get_field('short_description'); ?></p>
	      <div class="gm-top--2 gm-bottom--2">
	      	<?php if($filled != true): ?>
	        	<p><a href="#enquire" class="button button--white-outline">Enquire about this role</a></p>
        	<?php endif; ?>
	        <p><a href="<?php echo get_site_url(); ?>/assignments" class="white-text"><img src="<?php bloginfo('template_directory'); ?>/img/white-arrow--reversed.svg" width="15" />&nbsp;Back to current assignments</a></p>
	      </div>
	    </div>
	  </div>
	</div>
</div>
		
	<div class="row show-for-medium"><div class="column gm-top--2"></div></div>

      <div class="row shrink-width gm-top--2 gm-bottom--4">
        <div class="column small-12 medium-6 large-7 xlarge-8 float-right assignment__contents">
			<?php echo get_field('content'); ?>
        </div>
		<div class="column small-12 medium-6 large-5 xlarge-4 assignment__details">
          <div class="summary bg--light-gray">
            <table class="summary__table">
              <tbody>
              	<?php if(get_field('location')): ?>
	                <tr>
	                  <td class="uppercase">Location:</td>
	                  <td><?php echo get_field('location'); ?></td>
	                </tr>
                <?php endif; ?>
                <?php if(get_field('salary')): ?>
	                <tr>
	                  <td class="uppercase">Salary:</td>
	                  <td><?php echo get_field('salary'); ?></td>
	                </tr>
                <?php endif; ?>
                <?php if(get_field('posted')): ?>
	                <tr>
	                  <td class="uppercase">Posted:</td>
	                  <td><?php echo get_field('posted'); ?></td>
	                </tr>
                <?php endif; ?>
                <?php if(get_field('closing_date')): ?>
	                <tr>
	                  <td class="uppercase">Closing date:</td>
	                  <td><?php echo get_field('closing_date'); ?></td>
	                </tr>
                <?php endif; ?>
                <?php $industries = get_field('industry'); ?>
                <?php if($industries): ?>
	                <tr>
	                  <td class="uppercase">Industry:</td>
	                  <td>
	                  	<?php foreach ($industries as $industry): ?>
	                  		<?php echo $industry->name; ?>,
	                  	<?php endforeach; ?>
	                  </td>
	                </tr>
            	<?php endif; ?>
                <?php if(get_field('job_function')): ?>
	                <tr>
	                  <td class="uppercase">Job function:</td>
	                  <td><?php echo get_field('job_function'); ?></td>
	                </tr>
                <?php endif; ?>
              </tbody>
            </table>
            <?php if(get_field('video')): ?>
	            <div class="responsive-embed widescreen">
	              <?php echo get_field('video'); ?>
	            </div>
	            <p class="uppercase text-center">THE ROLE EXPLAINED: watch the video</p>
            <?php endif; ?>
          </div>
          <div class="summary bg--light-gray text-center medium-text-left">
            <h4 class="gm-bottom--1 regular">Enquire about this role</h4>
            <p class="gm-bottom--2">At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd  gubergren, no sea takimata 02380 123 456 Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr.</p>
            <a href="#" class="button button--green">Enquire here</a>
            <?php $roleManager = get_field('role_managed_by'); ?>
            <?php $photo = $roleManager['photograph']; ?>
            <?php if($roleManager['name']): ?>
	            <div class="row gm-top--2 text-center medium-text-left">
	              <div class="column small-12 medium-6 large-4 gm-bottom--2">
	                <img src="<?php echo $photo['sizes']['medium']; ?>" alt="<?php echo $photo['alt']; ?>" class="width-100 managedby__image" />
	              </div>
	              <div class="column small-12 medium-6 large-8">
	                <p>This role is managed by:<br /><?php echo $roleManager['name']; ?>
						<?php if($roleManager['email_address']): ?>
							<br />
	                		<a href="mailto:<?php echo $roleManager['email_address']; ?>"><?php echo $roleManager['email_address']; ?></a>
	            		<?php endif; ?>
						<?php if($roleManager['telephone']): ?>
	                		<br />
	                		<a href="tel:<?php echo $roleManager['telephone']; ?>">
	                			<?php echo $roleManager['telephone']; ?>
	                		</a>
                		<?php endif; ?>
	                	<?php if($roleManager['linkedin']): ?>
	                		<br />
	                		<a href="<?php echo $roleManager['linkedin']; ?>">LinkedIn</a>
	            		<?php endif; ?></p>
	                	<p class="gm-top--2"><a href="#" class="uppercase"><img src="<?php bloginfo('template_directory'); ?>/img/share.svg" alt="Share this role" width="18" />&nbsp;&nbsp;Share this role</a></p>
	              </div>
	            </div>
            <?php endif; ?>
          </div>

		  <?php $quote = get_field('quote'); ?>
          <?php if($quote): ?>
	          <div class="summary--testimonial">
	            <div class="clearfix"></div>
	            <div class="column small-12 large-10 large-offset-1 testimonial__text">
	              <p class="blue-text"><?php echo $quote['quote']; ?></p>
	              <p><strong class="blue-text futura uppercase"><?php echo $quote['author']; ?></strong></p>
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
	              <p class="">Questions? Call 02380 123 456 to speak to a Executive Heads representative or complete the form below.</p>
	            </div>
	            <div class="column small-12 large-10 gm-bottom--4 text-center">
				  <?php echo do_shortcode('[contact-form-7 id="169" title="Enquiry Form_copy"]'); ?>
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
	              <p class="white-text">At vero eos et accusam et justo duo dolores et ea rebum 02380 123456 Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, consetetur sadipscing elitr,  sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliqu.</p>
	            </div>
	            <div class="column small-12 large-10 gm-bottom--4 text-center">
	              <?php echo do_shortcode('[contact-form-7 id="168" title="Enquiry Form"]'); ?>
	            </div>
	          </div>      
	        </div>
	      </div>

  	  <?php endif; ?>


<?php
get_footer();