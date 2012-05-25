<?php

	// Extra information

	$extra['author'] = "Sumolari";
	$extra['author_url']= "http://sumolari.com";
	$extra['name'] = __('Internal Carousel', 'wp_carousel');
	$extra['url'] = "http://sumolari.com/wp-carousel";
	$extra['desc'] = __('Show the content added to any other carousel', 'wp_carousel');
	$extra['version'] = '1.1';
	$extra['type'] = 'group'; // "single" -> the Extra will return AN ITEM || "group" -> the Extra will return AN ARRAY OF ITEMS
	
	// Functions to get item's information
	
	$extra['image_url_function'] = 'wpc_internal_carousel_image_url';
	$extra['link_url_function'] = 'wpc_internal_carousel_link_url';
	$extra['title_function'] = 'wpc_internal_carousel_title';
	$extra['desc_function'] = 'wpc_internal_carousel_desc';	
	$extra['video_url_function'] = 'wpc_internal_carousel_video_url';
	
	$extra['item_function'] = 'wpc_internal_carousel_item_function';	// Needed if it is a GROUP EXTRA
	
	// Javascript files to be loaded
	
	$extra['js'] = array();
	//$extra['js'][] = 'afile.js';
	//$extra['js'][] = 'otherone.js';
	
	// Ejemplo de opciones personalizadas
	
	// NOTE: There must be ONE option which "id" for identifier ("option_identifier"). This is because WP Carousel will use this value to order arrays and other things. Please, set the main option's option_identifier to "id".
	
	// NOTA: Es necesario que haya UNA opción con "id" como identificador ("option_identifier"). Esto es porque WP Carousel usa este valor para tareas internas de gestión de las matrices y para otras cosas. Por favor, para la opción principal utiliza como identificador "id".
	
	$extra['custom_settings_demo'] = array(
		'option_identifier' => array(
			'type' => 'textarea | text | password | select | checkbox | group',
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
	
	/* Añadimos las opciones para configurar este elemento */
		
	$carousels = maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE));
	$carousels_array = array();
	
	if (is_array($carousels)):
		foreach ($carousels as $key => $carousel)
		{
			if (isset($carousel['CAROUSEL_NAME']))
			{
				$carousel_name = $carousel['CAROUSEL_NAME'];
			}
			else
			{
				$carousel_name = __('Carousel', 'wp_carousel').' '.$key;
			}
			
			$carousels_array[$key] = $carousel_name;
		}
		
		$extra['custom_settings'] = array(
			'id' => array(
				'type' => 'select',
				'default_value' => '',
				'title' => __('Carousel', 'wp_carousel'),
				'values' => $carousels_array
			)
		);
	else:
		$extra['custom_settings'] = array();
	endif;

?>