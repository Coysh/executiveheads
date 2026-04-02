<?php $columns = get_sub_field('text_column'); ?>
<?php $button = get_sub_field('button'); ?>

<div class="row shrink-width">
	<div class="column small-12 <?php if(get_sub_field('bottom_border') != 1): ?>gm-bottom--2<?php endif; ?>">
	  <div class="row">
	  	<?php foreach ($columns as $column): ?>
		    <div class="column small-12 large-<?php echo (12/count($columns)); ?>">
		      <h5><strong><?php echo $column['title']; ?></strong></h5>
		      <p><?php echo $column['text']; ?></p>
		    </div>
	    <?php endforeach; ?>
		<div class="column small-12 text-center <?php if(get_sub_field('bottom_border') != 1): ?>gm-bottom--2<?php endif; ?> gm-top--2">
	    	<?php if($button['button_url'] != ''): ?>
		      <a href="<?php echo $button['button_url']; ?>" class="button button--green no-margin-bottom" <?php if($button['open_in_new_window'] == 1): ?>target="_blank"<?php endif; ?>><?php echo $button['button_text']; ?></a>
			<?php endif; ?>
		</div>
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