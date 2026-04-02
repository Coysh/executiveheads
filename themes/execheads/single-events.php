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
	      <p><strong class="futura uppercase"><?php echo get_field('event_dates'); ?></strong></p>
	      <p><?php the_excerpt(); ?></p>
	    </div>
	  </div>
	</div>
</div>


      <div class="row shrink-width gm-top--4 gm-bottom--4">
        <div class="column small-12 medium-7 large-8">
          
          <?php the_content(); ?>

        </div>
        <div class="column small-12 medium-5 large-4">
          
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
          <div class="summary bg--light-gray">
          <h4 class="gm-bottom--2 regular">Latest Blogs</h4>
      <?php $posts = get_posts(['post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 4, 'orderby' => 'date', 'order' => 'DESC' ]); ?>
        <?php foreach ($posts as $post): ?>
              <p><a href="<?php the_permalink(); ?>" class="blue-text"><?php the_title(); ?></a></p>
          <?php endforeach; ?>
          <?php wp_reset_postdata(); ?>
          </div>

        </div>        
      </div>

      <?php if(have_rows('content')): ?>
          <?php while(have_rows('content')): the_row(); ?>
          <?php get_template_part('single/'. get_row_layout()); ?>
          <?php endwhile; ?>
      <?php endif; ?>


<?php endwhile; ?>

<?php
get_footer();