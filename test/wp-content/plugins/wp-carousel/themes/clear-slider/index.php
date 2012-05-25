<?php

	// THEME INFORMATION
	
	$theme['author'] = "Sumolari - based on Jonno Riekwel\'s design";
	$theme['author_url']= "http://sumolari.com";
	$theme['name'] = "Clear Slider";
	$theme['url'] = "http://sumolari.com/wp-carousel";
	$theme['desc'] = sprintf(__('A theme based on <a href="%s">Jonno Riekwel\'s design</a>', 'wp_carousel'), 'http://365psd.com/day/230/');
	$theme['version'] = '1.0.1';
	
	$theme['css'] = array();
	$theme['css'][] = 'style.css';
	$theme['css'][] = 'style-jcarousel.css';
	
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
	
	$theme['custom_settings'] = array(
		'a_group' => array(
			'type' => 'group',
			'title' => __('This theme does not support videos.', 'wp_carousel').' '
		),
		'repeat_mode' => array(
			'type' => 'select',
			'default_value' => '0',
			'values' => array(
				0 => __('Repeat', 'wp_carousel'),
				1 => __('Repeat only on x-axis', 'wp_carousel'),
				2 => __('Repeat only on y-axis', 'wp_carousel'),
				3 => __('Don\'t repeat', 'wp_carousel')
			),
			'title' => 'When the image is smaller than the panel, should it be repeated?'
		)
	);
		
?>