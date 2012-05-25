<?php

	/* Calculamos la ruta al archivo wp-blog-header.php */
			
	$folder = str_replace('/update_db.php', '', $_SERVER['PHP_SELF']);
	$folder_exploded = explode('/', $folder);
	$folder_count = count($folder_exploded);
	krsort($folder_exploded);
	$folder_count--;
	unset ($folder_exploded[$folder_count]);
	$folder_count -= 3;
	$folder_path = "";
	$folder_temp = 0;
	for ($folder_temp = 0; $folder_temp < $folder_count; $folder_temp++)
	{
		$folder_path .= '../';
	}
		
	if (!is_readable($folder_path . 'wp-blog-header.php')) $folder_path = "../../../../";
		
	/* Comprobamos si podemos cargar el archivo */
	
	if (!is_readable($folder_path . 'wp-blog-header.php')) 
	{
		exit;
	}
	else
	{
		require($folder_path . 'wp-blog-header.php');
		
		if (!current_user_can('manage_options'))
		{
			?>
			<div class="error">
				<p>
					<?php _e('WP Carousel can\'t confirm that you have an admin account. You are not allowed to perform modifications in the Database for security reasons.', 'wp_carousel'); ?>
				</p>
			</div>
			<?php
			exit;
		}
?>
	<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml" lang="es-ES">
	
		<head>
			<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
			<title><?php _e('Add a carousel', 'wp_carousel'); ?></title>
			<style type="text/css">
			body {
				font-family:Verdana, Arial, Helvetica, sans-serif; font-size:11px;
				background:#f1f1f1;
				padding:0;
				margin:8px 8px 0 8px;
			}			
			
			h1 {
				font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
				font-style: normal;
				font-variant: normal;		
				font-weight: normal;
				font-size: 22px;
				line-height: normal;
				font-size-adjust: none;
				font-stretch: normal;
				-x-system-font: none;
				padding-top: 10px 8px 5px 8px;
				margin:0;
				float: left;
				color:#555;
			}

			</style>			
			
		</head>
		<body>

			<h1><?php _e('Select a carousel', 'wp_carousel'); ?></h1>
			
			<br /> <br /> <br /> <br />
			
<?php
	
		// Obtenemos la lista de carruseles
		$carousel_config = maybe_unserialize(get_option('wp_carousel_config'));
		
?>
		<form name="carousels_form" id="carousels_form">
			<select name="carousels" id="carousels">
				<?php foreach($carousel_config as $carousel_id => $carousel_individual_config): ?>
					<?php
						if (isset($carousel_individual_config['CAROUSEL_NAME']))
						{
							$carousel_name = $carousel_individual_config['CAROUSEL_NAME'];
						}
						else
						{
							$carousel_name = __('Carousel ', 'wp_carousel').$carousel_id;
						}
					?>
					<option id="<?php echo $carousel_id; ?>" value="<?php echo $carousel_id; ?>"><?php echo $carousel_name; ?></option>
				<?php endforeach; ?>
			</select>
			
			<br /> <br /> <br /> <br />
			
			<input type="submit" value="Añadir" id="submit" name="submit" />
		</form>
<?php
		
	}
	
?>
		</body>
	</html>
<?php
	
?>