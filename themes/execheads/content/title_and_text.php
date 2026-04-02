<?php $link = get_sub_field('link'); ?>

<div class="bg--<?php echo get_sub_field('background_colour'); ?>">
	<div class="row align-center shrink-width">
		<div class="column small-12 large-10 xlarge-8 text-<?php echo get_sub_field('text_alignment'); ?> gm-top--3 gm-bottom--3">
			<h4 class="gm-bottom--2 regular"><?php echo get_sub_field('title'); ?></h4>
			<div class="rte-content">
				<p><?php echo get_sub_field('text'); ?></p>
			</div>
			<?php if($link['link_url']): ?>
				<a href="<?php echo $link['link_url']; ?>" class="boldlink"><?php echo $link['link_text']; ?></a>
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