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
<?php while(have_posts()): the_post(); ?>

<div class="hero" style="background-image: url('<?php bloginfo('template_directory'); ?>/img/single-bg.jpg');">
	<div class="hero__content">
	  <div class="row">
	    <div class="column small-12 medium-10 large-6">
	      <h1 class="hero__h1"><?php the_title(); ?></h1>
	      <p><strong class="futura uppercase"><?php echo get_the_date('d / M / Y'); ?></strong></p>
	      <p><?php the_excerpt(); ?></p>
        <?php if(get_field('author')): ?>
          <p><?php echo get_field('author'); ?></p>
        <?php endif; ?>
	    </div>
	  </div>
	</div>
</div>


      <div class="row shrink-width gm-top--4 gm-bottom--4">
        <div class="column small-12 medium-7 large-8 rte-content">
          
          <?php the_content(); ?>

        </div>
        <div class="column small-12 medium-5 large-4">
          <div class="summary bg--light-gray">
	        <h4 class="gm-bottom--2 regular">Latest Blogs</h4>
			<?php $posts = get_posts(['post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 4, 'orderby' => 'date', 'order' => 'DESC' ]); ?>
	  		<?php foreach ($posts as $post): ?>
            	<p><a href="<?php the_permalink(); ?>" class="blue-text"><?php the_title(); ?></a></p>
        	<?php endforeach; ?>
        	<?php wp_reset_postdata(); ?>
          </div>
          
          <?php $auths = []; ?>
          <?php $authors = get_posts(['post_type' => 'post', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'date', 'order' => 'DESC' ]); ?>
          <?php foreach($authors as $author): ?>
            <?php $author_id = $author->post_author; ?>
            <?php $authorName = get_the_author_meta( 'display_name' , $author_id ); ?>
            <?php if(!in_array($authorName, $auths)): ?>
              <?php array_push($auths, $authorName); ?>
            <?php endif; ?>
          <?php endforeach; ?>
          <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>

          <form method="get" action="/blog/" class="summary bg--light-gray">
            <h4 class="gm-bottom--1 regular">Search our blog</h4>
            <select name="auth">
              <option value="">All Authors</option>
              <?php foreach($auths as $author): ?>
                <option value="<?php echo $author; ?>"><?php echo $author; ?></option>
              <?php endforeach; ?>
            </select>
            <select name="subject">
              <option value="">All Subjects</option>
              <option value="executive-insight">Executive Insight</option>
			  <option value="head-2-head">Head 2 Head</option>
              <option value="thought-leadership">Thought Leadership</option>
            </select>
            <button type="submit" class="button button--green">Search</button>
          </form>
          <div class="summary bg--light-gray">
            <h4 class="gm-bottom--2 regular">Upcoming Events</h4>
			<?php $posts = get_posts(['post_type' => 'events', 'post_status' => 'publish', 'numberposts' => 4, 'meta_key' => 'event_dates','orderby' => 'meta_value','order' => 'ASC' ]); ?>
	  		<?php foreach ($posts as $post): ?>
	            <p>
	              <a href="<?php the_permalink(); ?>" class="blue-text"><?php the_title(); ?></a><br />
	              <strong class="futura uppercase gray-text"><small><?php echo get_field('event_dates'); ?></small></strong>
	            </p>
            <?php endforeach; ?>
            <?php wp_reset_postdata(); ?>
          </div>

        </div>        
      </div>


      <div class="bg--blue-gradient angle-top">
        <div class="clearfix"></div>
        <div class="row shrink-width">
          <div class="column small-12 text-center gm-top--8 gm-bottom--2">
            <h3 class="white-text">Our latest vlog posts</h3>
          </div>
          <div class="column small-12">
            <div class="row small-up-1 large-up-3">
              <?php $posts = get_posts(['post_type' => 'vlogs', 'post_status' => 'publish', 'numberposts' => 3, 'orderby' => 'date', 'order' => 'DESC' ]); ?>
        <?php foreach ($posts as $post): ?>
              <div class="column">
                  <div class="responsive-embed widescreen">
                    <?php echo get_field('video_embed'); ?>
                  </div>    
                  <p class="p--large white-text"><strong><a href="<?php the_permalink(); ?>" class="white-text"><?php the_title(); ?></a></strong></p>
              </div>
        <?php endforeach; ?>
        <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
            </div>
          </div>

          <div class="column small-12 gm-top--2 gm-bottom--4">
            <a href="/vlogs" class="button button--white-outline">See all Vlogs</a>
          </div>

        </div>
        <div class="clearfix"></div>
      </div>


<?php endwhile; ?>

<?php
get_footer();