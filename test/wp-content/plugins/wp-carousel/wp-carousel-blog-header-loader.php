<?php

	/*
		Este archivo se importa (require_once) cuando es necesario acceder al archivo wp-blog-header.php, en la carpeta Raíz de WordPress. Para evitar código duplicado se almacena aquí el sistema para recorrer los directorios y acceder a la carpeta Raíz de WordPress desde la carpeta de WP Carousel.
	*/
	
	if (!defined('WP_CAROUSEL_WP_BLOG_HEADER_FILE'))
	{
		define('WP_CAROUSEL_WP_BLOG_HEADER_FILE', 'wp-blog-header.php');
	}
	
	$folder_path = '';
	
	while (!is_readable($folder_path . WP_CAROUSEL_WP_BLOG_HEADER_FILE))
	{
		$folder_path .= '../';
		
		if (!is_dir($folder_path))
		{
			break;
		}
	}
	
?>