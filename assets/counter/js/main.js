
jQuery(document).ready(function() {	
	
    /*
        Background slideshow
    */
	const asset = 'https://goldenvision.co.id';
	$('.banner-area').backstretch([
	                     asset+"/assets/counter/images/backgrounds/1.jpg"
	                   , asset+"/assets/counter/images/backgrounds/2.jpg"
	                   , asset+"/assets/counter/images/backgrounds/3.jpg"
	                  ], {duration: 3000, fade: 750});
	

	$("#typed").typed({
		stringsElement: $('#typed-strings'),
		typeSpeed: 50,
		backDelay: 1000,
		loop: true,
		contentType: 'html', // or text
		// defaults to false for infinite loop
		loopCount: false,
		callback: function(){ foo(); },
		resetCallback: function() { newTyped(); }
	});

	$(".reset").click(function(){
		$("#typed").typed('reset');
	});
 
 
    function newTyped(){ /* A new typed object */ }

    function foo(){ console.log("Callback"+asset); }

	
});

// cowntdown function. Set the date below (December 1, 2016 00:00:00):
var austDay = new Date("Mar 7, 2024 00:00:00");
	$('#countdown').countdown({until: austDay, layout: '<div class="item"><p>{dn}</p> {dl}</div> <div class="item"><p>{hn}</p> {hl}</div> <div class="item"><p>{mn}</p> {ml}</div> <div class="item"><p>{sn}</p> {sl}</div>'});
	$('#year').text(austDay.getFullYear());
	
// smooth scrolling	
	$(function() {
  $('a[href*=#]:not([href=#])').click(function() {
	if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {

	  var target = $(this.hash);
	  target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
	  if (target.length) {
		$('html,body').animate({
		  scrollTop: target.offset().top
		}, 1000);
		return false;
	  }
	}
  });
});
