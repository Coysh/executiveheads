<div class="bg--blue-gradient relative" id="enquire">
  <div class="form">
    <div class="row align-center shrink-width">
      <div class="column small-12 medium-10 large-8 xlarge-6 gm-top--4 gm-bottom--1 text-center">
        <h3 class="regular white-text"><?php echo get_sub_field('title'); ?></h3>
        <p class="white-text"><?php echo get_sub_field('text'); ?></p>
      </div>
      <div class="column small-12 large-10 gm-bottom--4 text-center">
        <?php echo do_shortcode( ' '. the_field('form_shortcode') .' ' ); ?>
      </div>
    </div>      
  </div>
</div>