<?php

	/* Calculamos la ruta al archivo wp-blog-header.php */
	
	require_once('wp-carousel-blog-header-loader.php');
	
	if (!is_readable($folder_path . 'wp-blog-header.php'))
	{
		echo 'ERROR:WP_CAROUSEL_EI:FALSE';
		exit;
	}
		
	/* Cargamos el archivo */
	
	require_once($folder_path . WP_CAROUSEL_WP_BLOG_HEADER_FILE);
	
	if(WP_CAROUSEL_EI)
	{
		if (isset($_GET['carousel_id']))
		{
			$wp_carousel_content = maybe_unserialize(get_option('wp_carousel'));
			
			if (isset($wp_carousel_content[$_GET['carousel_id']]))
			{
				$carousel_content = wp_carousel($_GET['carousel_id'], 'carousel_ei');
				echo base64_encode(serialize($carousel_content['ITEMS']));
			}
			else
			{
				echo 'ERROR:$_GET["carousel_id"]:IS-NOT-A-CAROUSEL';
			}
			
		}
		else
		{
			echo 'ERROR:$_GET["carousel_id"]:NOT-SET';
		}
	}
	else
	{
		echo 'ERROR:WP_CAROUSEL_EI:FALSE';
	}
	
?>