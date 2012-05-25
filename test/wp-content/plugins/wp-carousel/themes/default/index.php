<?php

	// THEME INFORMATION
	
	$theme['author'] = "Sumolari";
	$theme['author_url']= "http://sumolari.com";
	$theme['name'] = __('WP Carousel 0.3 Look', 'wp_carousel');
	$theme['url'] = "http://sumolari.com/wp-carousel";
	$theme['desc'] = __('Use WP Carousel 0.3\'s theme', 'wp_carousel');
	$theme['version'] = '2.0';
	
	$theme['css'] = array();
	$theme['css'][] = 'style.css';
	$theme['css'][] = 'style-jcarousel.css';
	
	/* Especificamos el soporte de jCarousel y el modo vertical y el no soporte del tamaño del carrusel personalizado */
	
	$theme['supports']['jcarousel'] = true; // Soporta jCarousel
	$theme['supports']['vertical_mode'] = true; // Soporta el modo vertical (sólo compatible con jCarousel)
	$theme['supports']['carousel_size'] = false; // No soporta el tamaño del carrusel personalizado porque debería ajustar las imágenes que componen el theme y el código que centra verticalmente las flechas del desplazamiento manual. Quizás en un futuro añada esta opción a este theme.
	
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
		'where_to_show_desc' => array(
			'type' => 'select',
			'default_value' => 'under_image',
			'values' => array(
				'under_image' => __('Under image', 'wp_carousel').' ',
				'left' => __('Left', 'wp_carousel').' ',
				'right' => __('Right', 'wp_carousel').' '
			),
			'title' => 'Where should be shown item\'s excerpt?'
		),
		'group_width' => array(
			'type' => 'group',
			'title' => __('Width', 'wp_carousel').' '
		),
		'carousel_width' => array(
			'type' => 'text',
			'default_value' => '',
			'title' => 'Carousel\'s width (like 200px - please, use only px as unit)'
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
		'carousel_title' => array(
			'type' => 'text',
			'default_value' => '',
			'title' => 'Carousel\'s title'
		)
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