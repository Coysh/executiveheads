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
        <p><?php the_excerpt(); ?></p>
      </div>
    </div>
  </div>
</div>


      <div class="row shrink-width gm-top--4 gm-bottom--4 align-center">
        <div class="column small-12 medium-10 large-8">          
          <?php echo get_field('content'); ?>
          <div class="row">
            <div class="column small-12 gm-top--2">
              <div class="responsive-embed widescreen">
                <?php echo get_field('video_embed'); ?>
              </div>
            </div>
          </div>
        </div>    
      </div>


<?php endwhile; ?>

<?php
get_footer();