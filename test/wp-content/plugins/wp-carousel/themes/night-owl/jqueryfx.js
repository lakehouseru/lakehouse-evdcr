jQuery(document).ready(function($) {
	$(".theme-night-owl .panel").hover(function(){
		$(this).animate({marginTop: "10px"}, 500);
	}, function() {
		$(this).animate({marginTop: "50px"}, 300);
	});
});