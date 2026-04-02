<?php $image = get_sub_field('image'); ?>
<?php $button = get_sub_field('button'); ?>

<div class="bg--blue-gradient">
	<div class="row align-center shrink-width">
	  <div class="column small-12 medium-10 large-8 xlarge-6 gm-top--4 gm-bottom--4 text-center white-text">
	    <h3 class="white-text"><?php echo get_sub_field('title'); ?></h3>
	  </div>
	  <div class="column small-12">
	    <div class="row align-middle gm-bottom--4">
	      <?php if(get_sub_field('image_alignment') == 'left'): ?>
		      <div class="column small-12 large-6">
		        <img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>" class="width-100" />
		      </div>
	      <?php endif; ?>
	      <div class="column small-12 large-6 white-text">
	        <div class="row">
	          <div class="column small-12 large-10">
	            <h4><?php echo get_sub_field('subtitle'); ?></h4>
	            <?php echo get_sub_field('text'); ?>
	          </div>
	        </div>
	      </div>
	      <?php if(get_sub_field('image_alignment') != 'left'): ?>
		      <div class="column small-12 large-6">
		        <img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>" class="width-100" />
		      </div>
	      <?php endif; ?>
	    </div>
	    <?php if($button['button_url']): ?>
		    <div class="column small-12 text-center gm-bottom--4">
		      <a href="<?php echo $button['button_url']; ?>" class="button button--green" <?php if($button['open_in_new_window'] == 1): ?>target="_blank"<?php endif; ?>><?php echo $button['button_text']; ?></a>
		    </div>
	    <?php endif; ?>
	  </div>
	</div>
</div>

<?php if(get_sub_field('bottom_border') == 1): ?>
	<div class="row shrink-width">
		<div class="column small-12">
			<hr>
		</div>
	</div>
<?php endif; ?>