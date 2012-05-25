<?php

	// THEME INFORMATION
	
	$theme['author'] = "Gilbert Pellegrom & Pascal Gartner (Adapted to WP Carousel by Sumolari)";
	$theme['author_url']= "http://nivo.dev7studios.com";
	$theme['name'] = __('Nivo Slider - Pascal', 'wp_carousel');
	$theme['url'] = "http://sumolari.com/wp-carousel";
	$theme['desc'] = __('Use Nivo Slider with the Pascal theme', 'wp_carousel');
	$theme['version'] = '1.0';
	
	$theme['css'] = array();
	$theme['css'][] = 'style.css';
		
	$theme['supports']['jcarousel'] = false; // NO Soporta jCarousel
	$theme['supports']['vertical_mode'] = false; // NO Soporta el modo vertical (sólo compatible con jCarousel)
	$theme['supports']['carousel_size'] = false; // No soporta el tamaño del carrusel personalizado
	$theme['supports']['nivo'] = true; // Soporta Nivo Slider
	$theme['supports']['panel_size'] = false;
	$theme['supports']['image_size'] = false;
	
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
		'carousel_title' => array(
			'type' => 'text',
			'default_value' => '',
			'title' => 'Carousel\'s title'
		),
		'show_badge' => array(
			'type' => 'select',
			'default_value' => '0',
			'title' => 'Display "Featured" badge',
			'values' => array(
				1 => 'Display badge',
				0 => 'Don\'t display badge'
			)
		),
	);
	
	// Ejemplo de opciones personalizadas
	
	$theme['custom_settings_demo'] = array(
		'option_identifier' => array(
			'type' => 'textarea | text | password | select | checkbox (do NOT work well) | group',
			'default_value' => 'string for textarea, text and password; the key of the default value in the options array for select and 0 or 1 for checkbox (0 = false = unchecked, 1 = true = checked), group is a special value, see at bottom',
			'values' => array(
				0 => 'This array',
				1 => 'Is only required',
				2 => 'When you use select',
				3 => 'As TYPE'
			),
			'title' => 'Optional, title which is shown at the left'
		)
	);
	
	// About group value:
	// The group type allows you to group content. This type is NOT a field and only requires a title, the others value will be ignored
		
?>