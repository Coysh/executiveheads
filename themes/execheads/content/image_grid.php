<?php $images = get_sub_field('image'); ?>
<?php $link = get_sub_field('link'); ?>

<div class="bg--<?php echo get_sub_field('background_colour'); ?> image-grid-block">        
  <?php if(is_front_page() == 1): ?>
    <div class="triangle-1"></div>
  <?php endif; ?>
  <div class="row align-center shrink-width">
      <div class="column small-12 large-10 xlarge-8 text-center gm-top--3 gm-bottom--1">
        <?php if(get_sub_field('link_url')): ?>
          <a href="<?php echo get_sub_field('link_url'); ?>">
        <?php endif; ?>
        <h3 class="gm-bottom--2"><?php echo get_sub_field('title'); ?></h3>
        <?php if(get_sub_field('link_url')): ?>
          </a>
        <?php endif; ?>
      </div>
  </div>
  <div class="row align-center shrink-width">
    <div class="column small-12 large-10 xlarge-8 text-center gm-bottom--3">
      <div class="row small-up-2 large-up-<?php echo count($images); ?>">
        <?php foreach ($images as $image): ?>
          <?php $img = $image['image']; ?>
          <div class="column gm-bottom--1">
            <div class="tooltip">
              <img src="<?php echo $img['sizes']['medium_large']; ?>" alt="<?php echo $img['alt']; ?>" class="width-100" />
              <?php if($image['text'] || $image['linkedin_link']): ?>
                <div class="tooltip__content">
                  <p><?php echo $image['text']; ?></p>
                  <?php if($image['phone_number']): ?>
                    <p>DD: <a href="tel:<?php echo $image['phone_number']; ?>"><?php echo $image['phone_number']; ?></a><br />
                  <?php endif; ?>
                  <?php if($image['email_address']): ?>
                    <strong><a href="mailto:<?php echo $image['email_address']; ?>">Email</a></strong>
                  <?php endif; ?>
                  </p>
                  <?php if($image['linkedin_link']): ?>
                    <p><a href="<?php echo $image['linkedin_link']; ?>" target="_blank"><img src="<?php bloginfo('template_directory'); ?>/img/linkedin-icon.svg" width="20" /></a></p>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  <?php /*
  <div class="row align-center shrink-width">
    <div class="column text-center gm-bottom--4">
      <?php if($link['link_url']): ?>
        <a href="<?php echo $link['link_url']; ?>" class="boldlink"><?php echo $link['link_text']; ?></a>
      <?php endif; ?>
    </div>
  </div>
  */ ?>
  <?php if(is_front_page() == 1): ?>
    <div class="triangle-2"></div>
  <?php endif; ?>

</div>

<?php if(get_sub_field('bottom_border') == 1): ?>
  <div class="row shrink-width">
    <div class="column small-12">
      <hr>
    </div>
  </div>
<?php endif; ?>