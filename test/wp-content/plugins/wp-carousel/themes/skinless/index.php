<?php

	// THEME INFORMATION
	
	$theme['author'] = "Sumolari";
	$theme['author_url']= "http://sumolari.com";
	$theme['name'] = __('Skinless', 'wp_carousel');
	$theme['url'] = "http://sumolari.com/wp-carousel";
	$theme['desc'] = __('A theme with the minimum code. Use this theme as a template to create your own theme.', 'wp_carousel');
	$theme['version'] = '1.0';
	
	$theme['css'] = array();
	$theme['css'][] = 'style.css';
	$theme['css'][] = 'style-jcarousel.css';
	
	/* Especificamos el soporte de jCarousel y el modo vertical y el no soporte del tamaño del carrusel personalizado */
	
	$theme['supports']['jcarousel'] = true;
	$theme['supports']['vertical_mode'] = true; 
	$theme['supports']['carousel_size'] = true;
	
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