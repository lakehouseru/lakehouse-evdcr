<?php

	// THEME INFORMATION
	
	$theme['author'] = "Sumolari - based on Farzad Ban\'s design";
	$theme['author_url']= "http://sumolari.com";
	$theme['name'] = "Night Owl";
	$theme['url'] = "http://sumolari.com/wp-carousel";
	$theme['desc'] = sprintf(__('A theme based on <a href="%s">Farzad Ban\'s design</a>', 'wp_carousel'), 'http://365psd.com/day/289/');
	$theme['version'] = '1.0.1';
	
	$theme['css'] = array();
	$theme['css'][] = 'style.css';
	$theme['css'][] = 'style-jcarousel.css';
	
	$theme['js'] = array();
	$theme['js'][] = 'jqueryfx.js';
	
	/* Especificamos el soporte de jCarousel y el modo vertical y el no soporte del tamaño del carrusel personalizado */
		
	$theme['supports']['jcarousel'] = true; // Soporta jCarousel
	$theme['supports']['vertical_mode'] = false; // NO soporta el modo vertical (sólo compatible con jCarousel)
	$theme['supports']['carousel_size'] =  true; // Soporta el tamaño personalizado
	$theme['supports']['image_size'] = false; // NO soporta el tamaño personalizado de las imágenes
	
	/*
	
		Valores por defecto - Values by default
	
		$theme['supports']['stepcarousel'] = true;
		$theme['supports']['arrows'] = true;
		$theme['supports']['panel_size'] = true;
		$theme['supports']['image_size'] = true;
		$theme['supports']['pagination'] = true;
		
		$theme['supports']['jcarousel'] = false;
		$theme['supports']['carousel_size'] = false;
		$theme['supports']['vertical_mode'] = false;
	
	*/
		
?>