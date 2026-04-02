<?php $testimonials = get_sub_field('testimonial'); ?>

<div class="bg--blue-gradient" style="position: relative;">
	<div class="slick" data-slick='{"slidesToShow": 1, "arrows": false, "dots": true, "appendDots": "#arrows"}'>
	  <?php foreach ($testimonials as $testimonial): ?>
	  	  <?php $image = $testimonial['image']; ?>
		  <div class="slick__slide">
		    <div class="row">
		      <div class="column small-12 large-6 gm-top--4 gm-bottom--4">
		        <div class="row align-center">
		          <div class="column small-12 medium-10 large-8 testimonial__text">
		            <p class="white-text"><?php echo $testimonial['testimonial']; ?></p>
		            <p><strong class="white-text futura uppercase"><?php echo $testimonial['author']; ?></strong></p>
		          </div>
		        </div>
		      </div>
		      <div class="column small-12 large-6 testimonial__image" style="background-image: url('<?php echo $image['sizes']['large']; ?>')">
		        <img src="<?php echo $image['sizes']['large']; ?>" alt="<?php echo $image['alt']; ?>" class="width-100 hide-for-large" />
		      </div>
		    </div>
		  </div>
	  <?php endforeach; ?>
	  

	</div>
	<div class="row"><div class="column small-12 large-6" id="arrows"></div></div>
</div>