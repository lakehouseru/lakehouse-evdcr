<?php

/**
 * WP Carousel's Sample config page
 *
 * Rename this file to wp-carousel-config.php to use its values instead of default ones.
 */
 
 	/*
		Change this value to TRUE to enable the WP Carousel's External Integration option
	*/
	
	define('WP_CAROUSEL_EI', false);
	$wp_carousel_external_integration_line = __LINE__ - 1;
	
	/*
		Change this value to FALSE to show images instead of videos
	*/
	
	define('WP_CAROUSEL_SHOW_VIDEOS_FIRST', true);
	$wp_carousel_videos_first_line = __LINE__ - 1;
	
	/*
		Change this value to FALSE to disable auto-backups
	*/
	
	define('WP_CAROUSEL_AUTOSAVE_BACKUPS', true);
	$wp_carousel_autosave_backups_line = __LINE__ - 1;
	
	/*
		Here are define the name of the WordPress's options where WP Carousel stores its config
	*/
	
	define('WP_CAROUSEL_ITEMS_TABLE', 'wp_carousel');			// Tabla para los elementos del carrusel
	define('WP_CAROUSEL_CONFIG_TABLE', 'wp_carousel_config');	// Tabla para las configuraciones del carrusel
	define('WP_CAROUSEL_BACKUP_TABLE', 'wp_carousel_backup');	// Tabla para almacenar las copias de seguridad
	
	/*
		Custom fields used by WP Carousel to get the content of each panel of the carousels
	*/
	
	define('WP_CAROUSEL_CAROUSEL_TEXT', 'wp_carousel_carousel_text');	// Texto que usará el carrusel como descripción del contenido
	define('WP_CAROUSEL_IMAGE_URL', 'wp_carousel_image_url');			// URL de la imagen
	define('WP_CAROUSEL_LINK_URL', 'wp_carousel_link_url');				// URL del enlace
	define('WP_CAROUSEL_VIDEO_URL', 'wp_carousel_video_url');			// URL del vídeo
	
	/*
		Definimos los elementos que se cargarán y estarán disponibles en la página de opciones del carrusel
		
		Si por algún motivo no termina de cargar la página de opciones del carrusel, es posible que haya demasiado contenido para iterar sobre él en el tiempo de ejecución límite establecido en la configuración de PHP. Para evitar estos errores he añadido la posibilidad de eliminar las cajas con contenido demasiado grande como para mostrarse.
		
		- - - - -
		
		Are you having issues like page doesn't loading? Try disabling some of the "addable items boxes" of the Carousel's Edit Page (change 1 to 0)
		
	*/
	
	define('WP_CAROUSEL_SHOW_CATEGORIES_IN_CAROUSEL_OPTIONS', 1); // Muestra o no la caja para añadir categorías al carrusel
	define('WP_CAROUSEL_SHOW_TAGS_IN_CAROUSEL_OPTIONS', 1); // Muestra o no la caja para añadir tags al carrusel
	define('WP_CAROUSEL_SHOW_POSTS_IN_CAROUSEL_OPTIONS', 1); // Muestra o no la caja para añadir artículos al carrusel
	define('WP_CAROUSEL_SHOW_PAGES_IN_CAROUSEL_OPTIONS', 1); // Muestra o no la caja para añadir páginas al carrusel
	define('WP_CAROUSEL_SHOW_AUTHORS_IN_CAROUSEL_OPTIONS', 1); // Muestra o no la caja para añadir autores al carrusel
	
	/*
		Definimos el máximo de artículos a partir del cual dejará de mostrarse un menú desplegable en la lista de elementos individuales para solicitarse la ID.
		
		- - - - - - - -
		
		We define the maximum number of posts to load for the list of posts. If you are having issues like dissapearing part of the carousel's option page, try to use a lower value.
	*/
	
	define('WP_CAROUSEL_ITEMS_COUNT_LIMIT', 10); // 1000? 10000? I need more stats to set a better limit!
	$wp_carousel_posts_limit_line = __LINE__ - 1;
	
	/*
		Si hay más artículos que el límite, ¿se debe mostrar los últimos N (N = WP_CAROUSEL_ITEMS_COUNT_LIMIT) en lugar del cuadro de texto para indicar la ID?
		
		- - - - - - - -
		
		Where there are more posts than WP_CAROUSEL_ITEMS_COUNT_LIMIT, it can be shown the last WP_CAROUSEL_ITEMS_COUNT_LIMIT posts (value true) or the text input for IDs (value false)
	*/
	
	define('WP_CAROUSEL_SHOW_LAST_POSTS_INSTEAD_OF_ID_INPUT', false);
	$wp_carousel_force_last_posts_instead_of_id_input_line = __LINE__ - 1;
	
	/*
		Definimos el tipo de miniatura que se cargará por defecto. Se aceptan los siguientes valores:
			
			- thumbnail
			- medium
			- large
			
	*/
	
	define('WP_CAROUSEL_DEFAULT_THUMBNAIL_SIZE', 'thumbnail');	// Size of the carousel's thumbnail
	$wp_carousel_thumbnail_size_line = __LINE__ - 1;
	
	// Do not modify the following line
	$wp_carousel_config_file = __FILE__;

?>