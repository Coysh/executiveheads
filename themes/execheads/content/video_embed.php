<div class="row align-center shrink-width">
	<div class="column small-12 medium-10 large-8 text-center">
	  <div class="responsive-embed widescreen">
	    <?php echo get_sub_field('video_embed'); ?>
	  </div>
	  <?php $button = get_sub_field('button'); ?>
	  <?php if($button['button_url']): ?>
	  	<a href="<?php echo $button['button_url']; ?>" class="button button--green gm-top--2 <?php if(get_sub_field('bottom_border') != 1): ?>gm-bottom--4<?php endif; ?>" <?php if($button['open_in_new_window'] == 1): ?>target="_blank"<?php endif; ?>><?php echo $button['button_text']; ?></a>
  	  <?php endif; ?>
	</div>
</div>

<?php if(get_sub_field('bottom_border') == 1): ?>
	<div class="row shrink-width gm-top--2">
		<div class="column small-12">
			<hr>
		</div>
	</div>
<?php endif; ?>

<?php if(get_sub_field('bottom_border') == 1): ?>
	<div class="row shrink-width">
		<div class="column small-12">
			<hr>
		</div>
	</div>
<?php endif; ?>