<div class="row shrink-width" id="current">
  <div class="column small-12 text-center gm-top--4 gm-bottom--2">
    <h4 class="regular">Current Assignments</h4>
  </div>
  <div class="column small-12">
    <div class="row small-up-1 medium-up-2 large-up-3 assignments" id="current-assignments">
      <?php $posts = get_posts(['post_type' => 'assignments', 'post_status' => 'publish', 'numberposts' => 6, 'orderby' => 'date', 'order' => 'DESC', 'category' => 1 ]); ?>
      <?php $i=0; foreach ($posts as $post): ?>
        <div class="column assignment">
          <div class="assignment__content">
            <p class="p--large"><strong class="extrabold"><a href="<?php the_permalink(); ?>" class="blue-text"><?php the_title(); ?></a></strong></p>                
            <p><strong><?php echo get_field('location'); ?> <?php echo get_field('salary'); ?></strong></p>
            <p><?php echo get_field('short_description'); ?></p>
            <a href="<?php the_permalink(); ?>" class="no-margin-bottom button button--rounded-gray">View details</a>
          </div>
        </div>
      <?php $i++; endforeach; ?>

      <?php wp_reset_postdata(); // IMPORTANT - reset the $post object so the rest of the page works correctly ?>     
    </div>
  </div>
  <div class="float-left width-100" id="current-assignments-load"></div>
  <div class="column small-12 gm-top--2 gm-bottom--4 text-center">
    <button onclick="loadCurrents();" class="button button--green" id="loadCurrent">Load more</button>
  </div>
</div>