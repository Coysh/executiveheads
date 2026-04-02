<?php
/**
 *
 * Template Name: Blog
 * Description: Page template for the Blog.
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
<?php 
	$auth = '';
	$subject = '';
	if(isset($_GET['auth'])):
		$auth = $_GET['auth'];
 	endif; 
	if(isset($_GET['subject'])):
		$subject = $_GET['subject'];
 	endif; 
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

<form method="get" action="/blog/" class="row align-center shrink-width">
	<div class="column small-12 text-center">
	  <h4 class="gm-bottom--1 regular">Search our blog</h4>
	</div>
	<div class="column small-12 medium-10 large-8">
	  <div class="row small-up-2">
	    <div class="column gm-bottom--1">
	      <select name="auth">
	        <option value="">All Authors</option>
			<?php foreach($auths as $author): ?>
				<option <?php if($auth == $author): ?> selected="selected" <?php endif; ?> value="<?php echo $author; ?>"><?php echo $author; ?></option>
			<?php endforeach; ?>
	      </select>             
	    </div>
	    <div class="column gm-bottom--1">
	      <select name="subject">
	        <option value="">All Subjects</option>
	        <option <?php if($_GET['subject'] == 'executive-insight'): ?>selected="selected"<?php endif; ?> value="executive-insight">Executive Insight</option>
	        <option <?php if($_GET['subject'] == 'head-2-head'): ?>selected="selected"<?php endif; ?> value="head-2-head">Head 2 Head</option>
	        <option <?php if($_GET['subject'] == 'thought-leadership'): ?>selected="selected"<?php endif; ?> value="thought-leadership">Thought Leadership</option>
	      </select>              
	    </div>
	  </div>
	</div>
	<div class="column small-12 text-center gm-bottom--2">
	  <button type="submit" class="button button--green button--wide">Search</button>
	</div>
</form>

      <div class="row align-stretch shrink-width">

		<?php $posts = get_posts(['post_type' => 'post', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'date', 'order' => 'DESC', 'author_name' => $auth, 'category_name' => $subject ]); ?>
		<?php $i=1; foreach ($posts as $post): ?>
 			<?php if($i==1): ?>
				<div class="column small-12 article">
		          <div class="article__content gm-bottom--2">
		            <div class="row">
		              <div class="column small-12 medium-6 large-5">
		                <p><strong><a href="<?php the_permalink(); ?>" class="blue-text"><?php the_title(); ?></a></strong></p>
		                <p><strong class="futura uppercase"><small><?php echo get_the_date('d / M / Y'); ?></small></strong></p>
		                <p><small><?php the_excerpt(); ?></small></p>
		              </div>
		              <div class="column small-12 medium-6 large-7">
		                <?php if(get_the_post_thumbnail_url($post->ID, 'medium_large')): ?>
		                	<a href="<?php the_permalink(); ?>">
		                		<img src="<?php echo get_the_post_thumbnail_url($post->ID, 'medium_large'); ?>" alt="<?php the_title(); ?>" class="width-100" />
	                		</a>
		            	<?php endif; ?>
		              </div>
		              <div class="column small-12 gm-top--1">
		                <a href="<?php the_permalink(); ?>" class="no-margin-bottom boldlink">Read more</a>
		              </div>
		            </div>
		          </div>
		        </div>
			<?php endif; ?>
			<?php if($i==2): ?>
				<div class="column small-12 medium-6 large-4 article">
		          <div class="article__content">
		            <div class="row">
		              <div class="column small-12">
		                <p><strong><a href="<?php the_permalink(); ?>" class="blue-text"><?php the_title(); ?></a></strong></p>
		                <p><strong class="futura uppercase"><small><?php echo get_the_date('d / M / Y'); ?></small></strong></p>
		                <p><small><?php the_excerpt(); ?></small></p>
		              </div>
		              <div class="column small-12 gm-top--1">
		                <a href="<?php the_permalink(); ?>" class="no-margin-bottom boldlink">Read more</a>
		              </div>
		            </div>
		          </div>
		        </div>
			<?php endif; ?>
			<?php if($i==3): ?>
				<div class="column small-12 medium-6 large-8 article">
		          <div class="article__content">
		            <div class="row">
		              <div class="column small-12 medium-6 large-5">
						<p><strong><a href="<?php the_permalink(); ?>" class="blue-text"><?php the_title(); ?></a></strong></p>
		                <p><strong class="futura uppercase"><small><?php echo get_the_date('d / M / Y'); ?></small></strong></p>
		                <p><small><?php the_excerpt(); ?></small></p>
		              </div>
		              <div class="column small-12 medium-6 large-7">
		              	<?php if(get_the_post_thumbnail_url($post->ID, 'medium_large')): ?>
		                	<a href="<?php the_permalink(); ?>">
		                		<img src="<?php echo get_the_post_thumbnail_url($post->ID, 'medium_large'); ?>" alt="<?php the_title(); ?>" class="width-100" />
	                		</a>
	                	<?php endif; ?>
		              </div>
		              <div class="column small-12 gm-top--1">
		                <a href="<?php the_permalink(); ?>" class="no-margin-bottom boldlink">Read more</a>
		              </div>
		            </div>
		          </div>
		        </div>
			<?php endif; ?>
			<?php if($i==4 || $i==5 || $i==6): ?>
				<div class="column small-12 medium-6 large-4 article">
		          <div class="article__content">
		            <div class="row">
		              <div class="column small-12">
		                <p><strong><a href="<?php the_permalink(); ?>" class="blue-text"><?php the_title(); ?></a></strong></p>                
		              </div>
		              <div class="column small-12">
		                <p><strong class="futura uppercase"><small><?php echo get_the_date('d / M / Y'); ?></small></strong></p>
		                <p><small><?php the_excerpt(); ?></small></p>
		              </div>
		              <div class="column small-12 gm-top--1">
		                <a href="<?php the_permalink(); ?>" class="no-margin-bottom boldlink">Read more</a>
		              </div>
		            </div>
		          </div>
		        </div>
			<?php endif; ?>

		<?php if($i>5): $i=1; else: $i++; endif; endforeach; ?>
		<?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>
      </div>

	  <?php /*
      <div class="row">
        <div class="column small-12 text-center gm-top--2 gm-bottom--4">
          <a href="#" class="button button--green">Load more</a>
        </div>
      </div>
      */ ?>


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
                  <p class="p--large white-text"><strong><a class="white-text" href="<?php the_permalink(); ?>"><?php the_title(); ?></a></strong></p>
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