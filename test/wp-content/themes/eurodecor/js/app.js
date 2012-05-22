// JavaScript Document

$(document).ready(function() {

	$("#slides").slides({
		preload : false, // boolean, Set true to preload images in an image based slideshow
		preloadImage : '/img/loading.gif', // string, Name and location of loading image for preloader. Default is "/img/loading.gif"
		container : 'slides_container', // string, Class name for slides container. Default is "slides_container"
		generateNextPrev : true, // boolean, Auto generate next/prev buttons
		next : 'next', // string, Class name for next button
		prev : 'prev', // string, Class name for previous button
		pagination : true, // boolean, If you're not using pagination you can set to false, but don't have to
		generatePagination : false, // boolean, Auto generate pagination
		prependPagination : false, // boolean, prepend pagination
		paginationClass : 'pagination', // string, Class name for pagination
		currentClass : 'current', // string, Class name for current class
		fadeSpeed : 1350, // number, Set the speed of the fading animation in milliseconds
		fadeEasing : '', // string, must load jQuery's easing plugin before http://gsgd.co.uk/sandbox/jquery/easing/
		slideSpeed : 1350, // number, Set the speed of the sliding animation in milliseconds
		slideEasing : '', // string, must load jQuery's easing plugin before http://gsgd.co.uk/sandbox/jquery/easing/
		start : 1, // number, Set the speed of the sliding animation in milliseconds
		effect : 'slide', // string, '[next/prev], [pagination]', e.g. 'slide, fade' or simply 'fade' for both
		crossfade : false, // boolean, Crossfade images in a image based slideshow
		randomize : false, // boolean, Set to true to randomize slides
		play : 4000, // number, Autoplay slideshow, a positive number will set to true and be the time between slide animation in milliseconds
		pause : 0, // number, Pause slideshow on click of next/prev or pagination. A positive number will set to true and be the time of pause in milliseconds
		hoverPause : true, // boolean, Set to true and hovering over slideshow will pause it
		autoHeight : false, // boolean, Set to true to auto adjust height
		autoHeightSpeed : 1350, // number, Set auto height animation time in milliseconds
		bigTarget : false, // boolean, Set to true and the whole slide will link to next slide on click
		animationStart : function() {
		}, // Function called at the start of animation
		animationComplete : function() {
		}, // Function called at the completion of animation
		slidesLoaded : function() {

		} // Function is called when slides is fully loaded
	});

	$('.next').empty();
	$('.prev').empty()

	$('#slides').mouseenter(function() {
		$('.caption').fadeIn(1000)
	})

	$('#slides').mouseleave(function() {
		$('.caption').fadeOut(200)
	})

	$('#menu li.ani').mouseenter(function() {
		$(this).toggleClass('red');
		$(this).children('a').css('color', '#FFF');
		$(this).animate({
			bottom : "+=6px"
		}, 200, function() {
			// Animation complete.
		})
	})

	$('#menu li.ani').mouseleave(function() {
		$(this).toggleClass('red');
		$(this).children('a').css('color', '#4c3a2b');
		$(this).animate({
			bottom : "-=6px"
		}, 200, function() {
			// Animation complete.
		})
	})
	// Всплывающие надписи в списке артикулов
	$('.shop_item').hover(function() {
		$(this).find('a h3').fadeIn();
	}, function() {
		$(this).find('a h3').fadeOut();
	})
	// Тултипы
	$(function() {
		$("#banners a").tipTip();
		$("#brands a").tipTip();
		$("#collection a").tipTip();
	});

	//Управление фильтрами

	$('#fhook').hover(function() {
		$(this).parent().css('background-color', '#ddb65f');
	}, function() {
		$(this).parent().css('background-color', '#efdcb2');
	})
	$('#fhook').click(function() {
		$(this).parent().toggleClass('open');
		$(this).parent().find('form').toggle(500);

	})
	// Коллекции

	$('.descriptionimg').hover(function() {
		$(this).find('a h3').fadeIn();
	}, function() {
		$(this).find('a h3').fadeOut();
	})
	//Возврат на главную на Flash

	$('#home').flash({
		swf : 'images/home.swf',
		height : 60,
		width : 160,
		allowFullScreen : true,
		wmode : 'transparent'
	});

	//Отметим в сайдбаре текущий раздел

	$('.current-cat a:first').attr('class', 'green');
	// Костыль - ищем скрытый блок пагинации, забераем линки и подставляем в большие стрелки.
	$('#arr_right a').attr('href', $('#hpagination a[title="Следующая страница"]').attr('href'));
	$('#arr_left a').attr('href', $('#hpagination a[title="Предыдущая страница"]').attr('href'));

	//Поиск
	$('#ssearch').click(function() {
		$('#simpleSearch').submit();
	})
	//Show-Hide
	$('#post h2').click(function() {
		$(this).next("p").toggle(250)
	})
	
});
