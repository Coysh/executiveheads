<div class="row align-center gm-top--6">
    <div class="column small-12 medium-10 large-8 xlarge-6 testimonial__text gm-top--4 gm-bottom--4 text-center">
      <p class="blue-text"><?php echo get_sub_field('quote'); ?></p>
      <p><strong class="blue-text futura uppercase"><?php echo get_sub_field('author'); ?></strong></p>
    </div>
</div>

<?php if(get_sub_field('bottom_border') == 1): ?>
	<div class="row shrink-width">
		<div class="column small-12">
			<hr>
		</div>
	</div>
<?php endif; ?>