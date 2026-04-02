var offset = 6;
var pag = 3;
var apiUrl = "/wp-json/wp/v2/assignments?categories=1&order=desc&offset="+offset+"&per_page="+pag;
var lastScrollTop = 0;
var scrollPos = 0;
var scrollPos1 = 0;

function toggleNav() {
	var mobNav = document.querySelector('#mobile-nav');
	var main = document.querySelector('#main');
	
	mobNav.classList.toggle('active');
	main.classList.toggle('nav-open');
}


$(window).on('scroll', function() {
    var st = $(this).scrollTop();
    if (st > lastScrollTop){
       scrollPos = scrollPos+0.4;
       scrollPos1 = scrollPos1-0.4;
    } else {
       scrollPos = scrollPos-0.4;
       scrollPos1 = scrollPos1+0.4;
    }
    lastScrollTop = st;
    $('.home .image-grid-block .triangle-1').css({
        'transform': 'rotate('+scrollPos+'deg)'
    });
    $('.home .image-grid-block .triangle-2').css({
        'transform': 'rotate('+scrollPos1+'deg)'
    });
});

$(document).ready(function() {
	$('.slick').slick();
	$('#current-assignments').masonry({
		itemSelector: '.assignment'
	});

	$('#recent-assignments').masonry({
		itemSelector: '.assignment'
	});


	$('a[href*="#"]')
	  // Remove links that don't actually link to anything
	  .not('[href="#"]')
	  .not('[href="#0"]')
	  .click(function(event) {
	    // On-page links
	    if (
	      location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
	      && 
	      location.hostname == this.hostname
	    ) {
	      // Figure out element to scroll to
	      var target = $(this.hash);
	      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
	      // Does a scroll target exist?
	      if (target.length) {
	        // Only prevent default if animation is actually gonna happen
	        event.preventDefault();
	        $('html, body').animate({
	          scrollTop: target.offset().top
	        }, 1000, function() {
	          // Callback after animation
	          // Must change focus!
	          var $target = $(target);
	          $target.focus();
	          if ($target.is(":focus")) { // Checking if the target was focused
	            return false;
	          } else {
	            $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
	            $target.focus(); // Set focus again
	          };
	        });
	      }
	    }
	  });

	  $('.menu-item-has-children').each(function() {
	  	$(this).on('click', function () {
	  		console.log('now');
	  		$('.sub-menu', this).slideToggle();
	  	});
	  });


});


function loadCurrents() {
	$('#loadCurrent').text('Loading...');
	offset+=3;
	apiUrl = "/wp-json/wp/v2/assignments?categories=1&order=desc&offset="+offset+"&per_page="+pag;
	$.get(apiUrl, function(data){ 
	    for ( var i = 0, l = data.length; i < l; i++ ) {
	      $('#current-assignments').append(
	        '<div class="column assignment">'+
	          '<div class="assignment__content">'+
	            '<p><strong><a href="'+data[i].link+'" class="blue-text">'+data[i].title.rendered+'</a></strong></p>'+
	            '<p><small><strong>'+data[i].acf.location+' '+data[i].acf.salary+'</strong></small></p>'+
	            '<p><small>'+data[i].acf.short_description+'</small></p>'+
	            '<a href="'+data[i].link+'" class="no-margin-bottom button button--rounded-gray">View details</a>'+
	          '</div>'+
	        '</div>').css('opacity', 0).animate({opacity: 1}, 300);
	    }
	}).done(function() {
	  $('#current-assignments').masonry('destroy').masonry({
		itemSelector: '.assignment'
	  });
	  $('html, body').animate({scrollTop: $('#current-assignments-load').offset().top - $(window).height() }, 0);
	  $('#loadCurrent').text('Load more');
	  $.get(apiUrl, function(data){      
	  	console.log(data.length);
	  	if(data.length == 0) {
	  		$('#loadCurrent').hide();
	  	}
	  });
	});
}