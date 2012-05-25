<?php

	define('WP_CAROUSEL_WP_BLOG_HEADER_FILE', 'wp-blog-header.php');

	/* Esta variable almacenara si se ha producido un error o no */
	
	$wp_carousel_error = false;
	
	$had_backup = false; // Y esta si se ha realizado una copia de seguridad o no
	
	/* Primero creamos la nueva martriz a guardar */	
		
	if (!isset($_POST['carousel_id']))	// Los cambios deben aplicarse a algún carrusel
	{
		$wp_carousel_error = true;
		?>
			<div class="error">
				<p>
					<?php echo 'There was an error, please, report it in the forum and attach this error message:'; ?>
				</p>
				<p>
					<?php printf('%s', base64_encode(serialize($_POST)).' (file: update_db.php - '.__LINE__.' - '. $folder_path . WP_CAROUSEL_WP_BLOG_HEADER_FILE .' )'); ?>
				</p>
			</div>
		<?php
		exit;
	}
	
	$carousel_id = $_POST['carousel_id'];
	$action_to_perform = $_POST['action'];
	$should_save_a_backup = false;
	
	switch (true):	// Vamos a ver la acción que debemos realizar
	
		case ($_POST['action'] == 'updateSortableContent'): // Actualizar los elementos del carrusel
		
			if (!isset($_POST['must_backup']))	// ¿Debemos hacer una copia de seguridad? Por defecto sólo la haremos si se ha guardado manualmente el contenido, es decir, haciendo clic en el botón "Guardar" en lugar de arrastrando y soltando elementos. Para evitar excesivas copias de seguridad, más que nada
			{
				$_POST['must_backup'] = 'no';
				$should_save_a_backup = false;
			}
						
			foreach ($_POST as $key => $value) // Iteramos sobre el contenido recibido
			{
				if (($key != 'action' && $key != 'internal_type' && $key != 'carousel_id' && $key != 'must_backup') && $_POST['internal_type'] == 'serialized') // No es el campo de accion y el indicador es de serializado
				{
					$temp_printable = base64_decode($_POST[$key]); // Primer paso de la decodificación de los parámetros enviados
					$temp_printable = explode('&', $temp_printable); // Segundo paso, ahora tenemos todos los nuevos elementos en una matriz
					foreach ($temp_printable as $temp_key => $temp_value) // Iteramos sobre el nuevo contenido del carrusel
					{
						$temp_value = explode('=', $temp_value); // Separamos campos y valores (originalmente CAMPO=VALOR, ahora matriz[0]=CAMPO, matriz[1]=VALOR
						$array_keys = array('category_id', 'posts_order', 'posts_number', 'show_in_loop', 'order', 'type', 'post_title', 'desc', 'url_image', 'url_video', 'url_link', 'wp_carousel_ei_url', 'wp_carousel_ei_id');  // Los nombres de los campos del formulario enviado, es decir, los valores de CAMPO
						$array_names = array('ID', 'POSTS_ORDER', 'POSTS_NUMBER', 'SHOW', 'ORDER', 'TYPE', 'TITLE', 'DESC', 'IMAGE_URL', 'VIDEO_URL', 'LINK_URL', 'WP_CAROUSEL_EI_URL', 'WP_CAROUSEL_EI_ID'); // Los nombres de los identificadores que se guardarán en la Base de Datos y que utiliza WP Carousel para crear los carruseles
						$temp_value[0] = str_replace($array_keys, $array_names, $temp_value[0]); // Cambiamos los nombres originales de los formularios por los identificadores correctos de WP Carousel
						$temp_printable[$temp_value[0]] = urldecode($temp_value[1]); // Establecemos los valores de los campos
						unset($temp_printable[$temp_key]); // Eliminamos de la matriz contenido sobrante
					}
					if (!isset($temp_printable['POSTS_NUMBER'])) $temp_printable['POSTS_NUMBER'] = 0; // Establecemos valor por defecto
					if (!isset($temp_printable['SHOW'])) $temp_printable['SHOW'] = 0; // Idem que la línea anterior
					unset($_POST[$key]); // Eliminamos contenido innecesario
					$key_exploded = explode('_', $key); // Analizamos el identificador interno del contenido del carrusel
					$key = $key_exploded[1].'_'.$temp_printable['ID'].'_'.$temp_printable['TYPE']; // Lo modificamos para adaptarlo al sistema de identificación que utiliza WP Carousel.
					$_POST[$key] = $temp_printable; // Reemplazamos el valor anterior (sin adaptar) por el valor adaptado
				}
			}
			
			$new_content = $_POST; // Creamos la matriz que contiene el nuevo contenido del carrusel
			
			unset($_POST); // Eliminamos contenido innecesario
			unset($new_content['action']); // Eliminamos contenido innecesario
			unset($new_content['internal_type']); // Eliminamos contenido innecesario
			unset($new_content['carousel_id']); // Eliminamos contenido innecesario
			unset($new_content['must_backup']); // Eliminamos contenido innecesario
			
			$updated_content = $new_content; // Guardamos el nuevo contenido en la matriz general de nuevos contenidos
		
			break;
			
			// FIN DEL CASO updateSortableContent
			
		case ($_POST['action'] == 'updateStandardOptions'): // Actualizar las opciones estándar del carrusel (es decir, las dependientes directamente de WP Carousel y no del theme)
		
			$_POST['must_backup'] = 'yes'; // Por defecto guardamos SIEMPRE una copia de seguridad al guardar cambios en las opciones, ya que el guardado manual es el único modo de guardado
			$should_save_a_backup = true;
			
			$config = array();
						
			$new_config = base64_decode($_POST['content']); // Primera fase de la descodificar los nuevos valores
			$new_config_exploded = explode('&', $new_config); // Segunda fase, ahora tenemos una matriz
						
			if (array_search('use_jcarousel=yes', $new_config_exploded) === false)
			{
				$new_config_exploded[] = 'use_jcarousel=no'; // Establecemos un valor por defecto
			}
			
			if (array_search('vertical_mode=yes', $new_config_exploded) === false)
			{
				$new_config_exploded[] = 'vertical_mode=no'; // Establecemos un valor por defecto
			}
						
			if (array_search('show_arrows=yes', $new_config_exploded) === false)
			{
				$new_config_exploded[] = 'show_arrows=no'; // Establecemos un valor por defecto
			}
						
			if (array_search('loop_mode=yes', $new_config_exploded) === false)
			{
				$new_config_exploded[] = 'loop_mode=no'; // Establecemos un valor por defecto
			}
						
			if (array_search('enable_pagination=yes', $new_config_exploded) === false)
			{
				$new_config_exploded[] = 'enable_pagination=no';  // Establecemos un valor por defecto
			}
		
			foreach ($new_config_exploded as $key => $value) // Iteramos sobre las nuevas opciones
			{
				$new_option_exploded = explode('=', $value); // Separamos valor y campo
				
				if ($new_option_exploded[1] == 'yes')
				{
					$new_option_exploded[1] = 1; // Un 'yes' se almacena como un 1
				}
				
				if ($new_option_exploded[1] == 'no')
				{
					$new_option_exploded[1] = 0; // Un 'no' se almacena como un 0
				}
				
				$config[strtoupper($new_option_exploded[0])] = urldecode($new_option_exploded[1]); // Guardamos los nuevos valores en la matriz
			}
													
			$new_config = $config; // Guardamos la nueva configuración en una nueva matriz
			$updated_content = $new_config; // Guardamos el nuevo contenido en la matriz general de nuevos contenidos
			
			unset($config); // Eliminamos contenido innecesario
		
			break;
			
			// FIN DEL CASO updateStandardOptions
			
		case ($_POST['action'] == 'updateThemeOptions'): // Actualizamos las opciones dependientes de cada theme
			
			$_POST['must_backup'] = 'yes'; // Por defecto crearemos una copia de seguridad
			$should_save_a_backup = true;
						
			$config = array();
						
			$new_config = base64_decode($_POST['content']); // Primera fase de la descodificación
			$new_config_exploded = explode('&', $new_config); // Segunda fase de la descodificación, ahora tenemos una matriz
		
			foreach ($new_config_exploded as $key => $value) // Iteramos sobre los nuevos valores
			{
				$new_option_exploded = explode('=', $value); // Separamos valor y campo
				
				if (isset($new_option_exploded[0]) && isset($new_option_exploded[1]))
				{
								
					if ($new_option_exploded[1] == 'yes')
					{
						$new_option_exploded[1] = 1; // Un 'yes' equivale a 1
					}
					
					$config[strtoupper($new_option_exploded[0])] = urldecode($new_option_exploded[1]); // Guardamos los nuevos valores en la matriz
				
				}
			}
						
			$new_theme_config = $config; // Guardamos las nuevas opciones en la matriz correspondiente
			$updated_content = $new_theme_config; // Guardamos el nuevo contenido en la matriz general de nuevos contenidos
			
			unset($config); // Eliminamos contenido innecesario
			unset($new_config); // Eliminamos la variable $new_config, que ya hemos utilizado, para evitar conflictos con el guardado de opciones estándar
		
			break;
			
			// FIN DEL CASO updateThemeOptions
			
		default: // ERROR, CASO NO RECONOCIDO
			
			?>
			
				<div class="error">
					<p><?php printf('Action can\'t be performed because a correct action indicator has not been sent: update_db.php:%s', __LINE__); ?></p>
				</div>
			
			<?php
			
			exit;
			
			break;
	
	endswitch;
	
	/* Calculamos la ruta al archivo wp-blog-header.php */
	
	require_once('wp-carousel-blog-header-loader.php');
		
	/* Comprobamos si podemos cargar el archivo */
	
	if (!is_readable($folder_path . WP_CAROUSEL_WP_BLOG_HEADER_FILE)) 
	{
		$wp_carousel_error = true;
		?>
			<div class="error">
				<p><?php printf('File <code>%s</code> can\'t be read!', $folder_path . WP_CAROUSEL_WP_BLOG_HEADER_FILE); ?></p>
			</div>
		<?php
			$action_sended = 'SAVE-NO-AJAX:'.base64_encode(serialize($updated_content)).':'.$carousel_id;
		?>
			<div class="updated fade blue-message">
				<p>Click <a href="admin.php?page=edit-carousel-<?php echo $carousel_id; ?>&action=<?php echo $action_sended; ?>&in_mode=<?php echo $action_to_perform; ?>">here</a> to save changes.</p>
			</div>
		<?php
		exit;
	}	
	
	/* Cargamos el archivo */
	
	echo '<p style="display:none;">';	
	require_once($folder_path . WP_CAROUSEL_WP_BLOG_HEADER_FILE);
	echo '</p>';
	
	/*
		Cargamos los archivos del idioma correspondiente
	*/
		
	$currentLocale = get_locale();
	if(!empty($currentLocale)) 
	{
		$moFile = dirname(__FILE__) . "/language/" . $currentLocale . ".mo";
		if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('wp_carousel', $moFile);
	}
	
	/*
		Comprobamos que el usuario tiene permisos suficientes para guardar los cambios
	*/

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
	
	/*
		Comprobamos que existe una ID del carrusel y alertamos de problemas en caso contrario
	*/
	
	if (!isset($carousel_id) && isset($_POST['carousel_id']))
	{
		$carousel_id = $_POST['carousel_id'];
	}
	
	if (!isset($carousel_id))
	{
		$wp_carousel_error = true;
		?>
			<div class="error">
				<p>
					<?php printf(__('There was an error, please, report it in the forum and attach this error message:', 'wp_carousel'), $folder_path . WP_CAROUSEL_WP_BLOG_HEADER_FILE); ?>
				</p>
				<p>
					<?php printf('%s', base64_encode(serialize($_POST)).' (file: update_db.php - '.__LINE__.')'); ?>
				</p>
			</div>
		<?php
		exit;
	}
	
	/*
		Cargamos el contenido original del carrusel
	*/
	
	$wp_carousel_content = maybe_unserialize(get_option(WP_CAROUSEL_ITEMS_TABLE)); // Obtenemos el contenido de todos los carruseles
	$carousel_content = $wp_carousel_content[$carousel_id]; // Centramos el contenido en el del carrusel actual
	
	$wp_carousel_config = maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE)); // Obtenemos la configuración de todos los carruseles
	$carousel_config = $wp_carousel_config[$carousel_id]; // Centramos la configuración en la del carrusel actual
	
	/*
		Actualizamos las matrices para que reemplacen el contenido antiguo por el nuevo y mantengan el viejo contenido no actualizado intacto
	*/
	
	switch (true):
		
		case (isset($new_config)): // Se han actualizado las opciones estándar
			
			$final_config = $carousel_config; // Guardamos las opciones previas en una nueva matriz que manipularemos y convertiremos en las opciones antiguas actualizadas
			
			foreach ($final_config as $fc_key => $fc_value) // Iteramos sobre todos los índices de las opciones antiguas
			{
				if (isset($new_config[$fc_key]))
				{
					$final_config[$fc_key] = $new_config[$fc_key];
				}
			}
									
			foreach ($new_config as $nc_key => $nc_value) // Hacemos lo mismo que antes, sólo que con las opciones nuevas, por si hemos dejado algo por modificar
			{
				if (!isset($final_config[$nc_key]))
				{
					$final_config[$nc_key] = $nc_value;
				}
			}
		
			break;
		
		case (isset($new_theme_config)): // En el caso de las opciones del theme, éstas siempre reemplazan a las anteriores
		
			$final_config = $carousel_config;
			
			$final_config['THEME_SETTINGS'] = $new_theme_config;
		
			break;
	
		default: // No se ha hecho nada de nada, es decir, ha habido un error
		case (isset($new_content)):	// Se ha actualizado el contenido, no es necesario hacer nada en este caso
			break;
	
	endswitch;
				
	/*
		Generamos el backup, si es necesario
	*/
			
	if ($should_save_a_backup && WP_CAROUSEL_AUTOSAVE_BACKUPS) // Comprobamos que tenemos que hacer una copia de seguridad
	{
		
		$backup_structure = maybe_unserialize(get_option(WP_CAROUSEL_BACKUP_TABLE));  // Obtenemos la matriz de copias de seguridad
		
		if (!isset($backup_structure[$carousel_id])) // Si no hay ningún backup anterior del carrusel actual, creamos la matriz correspondiente a este carrusel
		{
			$backup_structure[$carousel_id] = array();
		}
		
		$backup_structure_current_carousel = $backup_structure[$carousel_id]; // Nos centramos en el carrusel actual
		// Creamos una nueva copia de seguridad
		
		switch (true):
	
			case (isset($new_content)):	// Se ha actualizado el contenido, respaldamos sólo eso
				
				$backup_structure_current_carousel[time()] = array(
					'items' => serialize($new_content),
					'config' => serialize($carousel_config)
				);
			
				break;
				
			case (isset($new_config) && isset($final_config)): // Se han actualizado las opciones estándar, respaldamos sólo eso
			case (isset($new_theme_config) && isset($final_config)): // Se han actualizado las opciones del theme, respaldamos sólo eso
							
				$backup_structure_current_carousel[time()] = array(
					'items' => serialize($carousel_content),
					'config' => serialize($final_config)
				);
							
				break;
		
		endswitch;
		
		$backup_structure[$carousel_id] = $backup_structure_current_carousel;
		
		update_option(WP_CAROUSEL_BACKUP_TABLE, serialize($backup_structure));
		
		$had_backup = true;
	
	}
	
	/*
		Ahora guardamos los cambios
	*/
	
	switch (true):
	
		case (isset($new_content)):	// Se ha actualizado el contenido, guardamos sólo eso
			
			$wp_carousel_content[$carousel_id] = $new_content; // Reemplazamos el contenido anterior por el nuevo contenido
			
			update_option(WP_CAROUSEL_ITEMS_TABLE, serialize($wp_carousel_content));
		
			break;
			
		case (isset($new_config) && isset($final_config)): // Se han actualizado las opciones estándar, guardamos sólo eso
		case (isset($new_theme_config) && isset($final_config)): // Se han actualizado las opciones del theme, guardamos sólo eso
						
			$wp_carousel_config[$carousel_id] = $final_config;

			update_option(WP_CAROUSEL_CONFIG_TABLE, serialize($wp_carousel_config));
						
			break;
	
	endswitch;
	
	/*
		Comprobamos errores y mostramos mensajes finales
	*/
	
	if (!$wp_carousel_error)
	{
		?><div class="updated changes_saved green-message"><p><?php _e('Changes saved', 'wp_carousel'); ?></p></div><?php
		if ($had_backup)
		{
			?><div class="updated changes_saved green-message"><p><?php _e('A Backup has been saved', 'wp_carousel'); ?></p></div><?php
		}
	}
	else
	{
		?><div class="error"><p><?php _e('There was an error!', 'wp_carousel'); ?></p></div><?php
	}
	
	exit;

?>