<?php
/**
 *
 * Template Name: Contact
 * Description: Page template for the Contact Page.
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
		        	<p><a href="<?php echo esc_url($link['link_url']); ?>" class="white-text uppercase">> <?php echo esc_html($link['link_text']); ?></a></p>
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

<div class="row">
	<div class="column gm-top--4"></div>
</div>


<div class="row">
	<div class="column small-12 medium-6 static-form">
		<?php echo do_shortcode('[contact-form-7 id="248" title="Get In Touch_copy"]'); ?>
	</div>
	<div class="column small-12 medium-6">
		<?php echo get_field('text'); ?>
		<?php if(get_field('google_map_embed')): ?>
			<div class="responsive-embed widescreen gm-top--2">
				<?php echo get_field('google_map_embed'); ?>
			</div>
		<?php endif; ?>
	</div>
</div>

<div class="row">
	<div class="column gm-top--4"></div>
</div>

<style>
	.static-form .white-text {
		color:#212121;
	}
</style>

<?php get_footer(); ?>