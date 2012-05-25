<?php

	/* Esta variable almacenara si se ha producido un error o no */
	
	$wp_carousel_error = false;

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
	
	if (!isset($_POST['backup_time']))
	{
		$wp_carousel_error = true;
		?>
			<div class="error">
				<p>
					<?php printf(__('There was an error, please, report it in the forum and attach this error message:', 'wp_carousel'), $folder_path . 'wp-blog-header.php'); ?>
				</p>
				<p>
					<?php echo base64_encode(serialize($_POST)).' (file: restore_db.php - '.__LINE__.')'; ?>
				</p>
			</div>
		<?php
		exit;
	}
	
	//$carousel_content = $wp_carousel_content[$_POST['carousel_id']];

	if (isset($_POST['action']))
	{
		if ($_POST['action'] == 'restoreBackup')
		{
									
			$backups = maybe_unserialize(get_option(WP_CAROUSEL_BACKUP_TABLE));
									
			$message_exploded = explode('_', $_POST['backup_time']);
			
			$backup_time = $message_exploded[0];
			$carousel_id = $message_exploded[1];
			
			$selected_backup = $backups[$carousel_id][$backup_time];
			$selected_backup['items'] = maybe_unserialize($selected_backup['items']);
			$selected_backup['config'] = maybe_unserialize($selected_backup['config']);
			
			$items = maybe_unserialize(get_option(WP_CAROUSEL_ITEMS_TABLE));
			$items[$carousel_id] = $selected_backup['items'];
			$items = serialize($items);
			
			$config = maybe_unserialize(get_option(WP_CAROUSEL_CONFIG_TABLE));
			$config[$carousel_id] = $selected_backup['config'];
			$config = serialize($config);
			
			update_option(WP_CAROUSEL_ITEMS_TABLE, $items);
			update_option(WP_CAROUSEL_CONFIG_TABLE, $config);
						
		}
		else
		{
			$wp_carousel_error = true;
		}
	}
	else
	{
		$wp_carousel_error = true;
	}
	
	if (!$wp_carousel_error)
	{
		?><div class="updated changes_saved"><p><?php _e('Backup restored', 'wp_carousel'); ?></p></div><?php
	}
	else
	{
		?><div class="error"><p><?php _e('There was an error!', 'wp_carousel'); ?></p></div><?php
	}
?>