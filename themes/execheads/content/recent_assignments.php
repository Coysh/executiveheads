<div class="row shrink-width" id="recent">
  <div class="column small-12 text-center gm-top--4 gm-bottom--2">
    <h4 class="regular">Recent Assignments</h4>
  </div>
  <div class="column small-12">
    <div class="row small-up-1 medium-up-2 large-up-3 assignments assignments--blue" id="recent-assignments">
      <?php $posts = get_posts(['post_type' => 'assignments', 'post_status' => 'publish', 'numberposts' => 6, 'orderby' => 'date', 'order' => 'DESC','category' => 7 ]); ?>
      <?php foreach ($posts as $post): ?>
        <div class="column assignment">
          <div class="assignment__content">
            <p class="p--large"><strong class="extrabold"><a href="<?php the_permalink(); ?>" class="blue-text"><?php the_title(); ?></a></strong></p>                
            <p><strong><?php echo get_field('location'); ?> <?php echo get_field('salary'); ?></strong></p>
            <p><?php echo get_field('short_description'); ?></p>
            <a href="<?php the_permalink(); ?>" class="no-margin-bottom button button--rounded-blue">View details</a>
          </div>
        </div>
      <?php endforeach; ?>
      <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>     
    </div>
  </div>
  <div class="column small-12 gm-top--2 gm-bottom--4 text-center">
    <?php /*
    <a href="" class="button button--green">Load more</a>
    */ ?>
  </div>
</div>

<div class="bg--light-gray relative" id="enquire">
  <div class="form">
    <div class="row align-center shrink-width">
      <div class="column small-12 medium-10 large-8 xlarge-6 gm-top--4 gm-bottom--1 text-center">
        <h3 class="regular">Get in touch</h3>
        <p class="">Questions? Call <a href="tel:02078708259">020 7870 8259</a> to speak to an Executive Heads representative or complete the form below.</p>
      </div>
      <div class="column small-12 large-10 gm-bottom--4 text-center">
        <?php echo do_shortcode('[contact-form-7 id="248" title="Get In Touch_copy"]'); ?>
      </div>
    </div>      
  </div>
</div>