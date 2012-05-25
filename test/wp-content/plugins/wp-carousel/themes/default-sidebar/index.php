<?php

	// THEME INFORMATION
	
	$theme['author'] = "Sumolari";
	$theme['author_url']= "http://sumolari.com";
	$theme['name'] = __('WP Carousel Default - Sidebar', 'wp_carousel');
	$theme['url'] = "http://sumolari.com/wp-carousel";
	$theme['desc'] = __('The default theme adapted to sidebars', 'wp_carousel');
	$theme['version'] = '2.0';
	
	$theme['css'] = array();
	$theme['css'][] = 'style.css';
	$theme['css'][] = 'style-jcarousel.css';
	
	/* Especificamos el soporte de jCarousel y el modo vertical y el no soporte del tamaño del carrusel personalizado */
	
	$theme['supports']['jcarousel'] = true; // Soporta jCarousel
	$theme['supports']['vertical_mode'] = true; // Soporta el modo vertical (sólo compatible con jCarousel)
	$theme['supports']['carousel_size'] = true; // Soporta el ancho personalizado
	
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
	
	/* Añadimos las opciones del Panel de Opciones */
	
	$theme['custom_settings'] = array(
		'group_exceprt' => array(
			'type' => 'group',
			'title' => __('Item\'s excerpt', 'wp_carousel').' '
		),
		'show_titles_in_desc' => array(
			'type' => 'select',
			'default_value' => 0,
			'values' => array (
				0 => __('Show excerpt', 'wp_carousel').' ',
				1 => __('Show title', 'wp_carousel').' '
			),
			'title' => 'Show post\'s excerpt or post\'s title?'
		),
		'group_variations' => array(
			'type' => 'group',
			'title' => __('Variations', 'wp_carousel').' '
		),
		'color_variation' => array(
			'type' => 'select',
			'default_value' => 'default',
			'values' => array (
				'default' => __('Default', 'wp_carousel').' ',
				'kubrick' => __('Kubrick', 'wp_carousel').' ',
				'red' => __('Red', 'wp_carousel').' ',
				'green' => __('Green', 'wp_carousel').' ',
				'orange' => __('Orange', 'wp_carousel').' ',
				'violet' => __('Violet', 'wp_carousel').' '
			),
			'title' => 'Color variation'
		),
	);
		
?>