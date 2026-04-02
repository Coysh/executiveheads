<?php
/**
 *
 * Template Name: Vlogs
 * Description: Page template for the Vlogs.
 *
 */
get_header(); ?>

<?php
$image = get_field('background_image');
$links = get_field('links');
$buttons = get_field('buttons');
$hero_title = get_field('title');
$hero_preview = get_field('preview');
$featured_image = get_the_post_thumbnail_url(get_the_ID(), 'large');

$hero_image_url = '';

if (is_array($image) && !empty($image['sizes']['large'])) {
	$hero_image_url = $image['sizes']['large'];
} elseif (is_array($image) && !empty($image['url'])) {
	$hero_image_url = $image['url'];
} elseif (!empty($featured_image)) {
	$hero_image_url = $featured_image;
}

if (empty($hero_title)) {
	$hero_title = get_the_title();
}

if (empty($hero_preview) && has_excerpt()) {
	$hero_preview = get_the_excerpt();
}
?>

<div class="hero <?php if(get_field('show_triangles') == 1): ?>hero--with-triangles<?php endif; ?>"<?php if($hero_image_url): ?> style="background-image:url(<?php echo esc_url($hero_image_url); ?>);"<?php endif; ?>>
	<div class="hero__content">
	  <div class="row">
	    <div class="column small-12 medium-10 large-6">
	      <?php if(have_rows('hero_text_slider')): ?>
      	  	<div class="hero__slider slick" data-slick='{"adaptiveHeight": true , "fade": true,"slidesToShow": 1, "arrows": false, "dots": false, "autoplay": true, "autoplaySpeed": 5000 }'>
			    <?php while(have_rows('hero_text_slider')): the_row(); ?>
				  <div>
					  <h1 class="hero__h1"><?php echo esc_html(get_sub_field('hero_title')); ?></h1>
					  <?php if(get_sub_field('hero_text')): ?>
						<p><?php echo wp_kses_post(get_sub_field('hero_text')); ?></p>
		      	  	  <?php endif; ?>
	      	  	  </div>
			    <?php endwhile; ?>
		    </div>
		  <?php elseif($hero_title || $hero_preview): ?>
			<div>
			  <?php if($hero_title): ?>
				<h1 class="hero__h1"><?php echo esc_html($hero_title); ?></h1>
			  <?php endif; ?>
			  <?php if($hero_preview): ?>
				<p><?php echo wp_kses_post($hero_preview); ?></p>
			  <?php endif; ?>
			</div>
		  <?php endif; ?>
	      <?php if(!empty($links) && is_array($links)): ?>
		      <div class="gm-top--2 gm-bottom--2">
		      	<?php foreach ($links as $link): ?>
		        	<p><a href="<?php echo esc_url($link['link_url']); ?>" class="link link--white"><?php echo esc_html($link['link_text']); ?></a></p>
	        	<?php endforeach; ?>
		      </div>
	  	  <?php endif; ?>
	      <?php if(!empty($buttons) && is_array($buttons)): ?>
		      <?php foreach ($buttons as $button): ?>
		        	<p><a href="<?php echo esc_url($button['button_url']); ?>" class="button button--hero"><?php echo esc_html($button['button_text']); ?></a></p>
	          <?php endforeach; ?>
          <?php endif; ?>
	    </div>
	  </div>
	</div>
</div>

<?php if(have_rows('content')): ?>
    <?php while(have_rows('content')): the_row(); ?>
		<?php get_template_part('content/'. get_row_layout()); ?>
    <?php endwhile; ?>
<?php endif; ?>

      <div class="row align-stretch shrink-width gm-top--4">

		<?php $posts = get_posts(['post_type' => 'vlogs', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'date', 'order' => 'DESC' ]); ?>
		<?php foreach ($posts as $post): ?>
 			
				<div class="column small-12 medium-6 article">
		          <div class="article__content">
		            <div class="row">
		              <div class="column small-12">
			              <div class="responsive-embed widescreen">
		                  	<?php echo get_field('video_embed'); ?>
		                  </div>
		              </div>
		              <div class="column small-12">
		                <p><strong class="blue-text"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong></p>                
		              </div>
		            </div>
		          </div>
		        </div>

		<?php endforeach; ?>
		<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
      </div>


      <div class="bg--blue-gradient angle-top">
        <div class="clearfix"></div>
        <div class="row shrink-width">
          <div class="column small-12 text-center gm-top--8 gm-bottom--2">
            <h3 class="white-text">Our latest blog posts</h3>
          </div>
          <div class="column small-12">
            <div class="row">
              <?php $posts = get_posts(['post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 2, 'orderby' => 'date', 'order' => 'DESC' ]); ?>
			  <?php foreach ($posts as $post): ?>
              <div class="column small-12 large-6">
			      <div class="row">
			        <div class="column small-12 large-10">
			          <p><a class="white-text" href="<?php the_permalink(); ?>"><strong><?php the_title(); ?></strong></a></p>
			          <p class="white-text"><?php echo get_the_excerpt(); ?></p>
			          <p><a href="<?php the_permalink(); ?>" class="boldlink boldlink--white">Read more</a></p>
			        </div>
			      </div>
			  </div>
			  <?php endforeach; ?>
			  <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
            </div>
          </div>

          <div class="column small-12 gm-top--2 gm-bottom--4">
            <a href="/blog" class="button button--white-outline">Visit the knowledge hub</a>
          </div>

        </div>
        <div class="clearfix"></div>
      </div>

	<div class="bg--black darkbg" style="background-image:url('<?php bloginfo('template_directory'); ?>/img/dark_bg.jpg')">
		<div class="clearfix"></div>
		<div class="row shrink-width gm-top--4 gm-bottom--4">
		  <div class="column small-12">
		    <h4 class="underline white-text gm-bottom--2">Upcoming events</h4>
		  </div>
		  <div class="column small-12">
		    <div class="row white-text">
			<?php $posts = get_posts(array('post_type' => 'events','posts_per_page' => -1,'meta_key' => 'event_dates','orderby' => 'meta_value','order' => 'ASC' )); ?>
	 		<?php foreach ($posts as $post): ?>
		      <div class="column small-12 large-4">
		        <div class="row">
		          <div class="column small-12 large-10">
		            <p><a class=" white-text" href="<?php the_permalink(); ?>"><strong style="font-weight: 800;"><?php the_title(); ?></strong></a></p>
		            <p><strong class="futura uppercase"><?php echo get_field('event_dates'); ?></strong></p>
		            <p><?php the_excerpt(); ?></p>
		            <p><a href="<?php the_permalink(); ?>" class="boldlink">Read more</a></p>
		          </div>
		        </div>
		      </div>
	        <?php endforeach; ?>

		    </div>
		  </div>
		  <div class="column small-12 gm-top--2">
		    <a href="/events" class="button button--white-outline">View all events</a>
		  </div>
		</div>
		<div class="clearfix"></div>
	</div>
	<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>


<?php get_footer(); ?>