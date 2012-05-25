<?php

	/* Calculamos la ruta al archivo wp-blog-header.php */
			
	require_once('wp-carousel-blog-header-loader.php');
		
	/* Comprobamos si podemos cargar el archivo */
	
	if (!is_readable($folder_path . WP_CAROUSEL_WP_BLOG_HEADER_FILE)) 
	{
		exit;
	}
	else
	{
		echo '<p style="display:none;">';	
		require($folder_path . WP_CAROUSEL_WP_BLOG_HEADER_FILE);
		echo '</p>';
		
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
		
		if (!isset($_POST))
		{
			exit;
		}
		else
		{
			if (isset($_POST['action']))
			{
				if ($_POST['action'] == 'getItemThumbnail')
				{
					if (isset($_POST['item_id']) && isset($_POST['item_type']))
					{
						if (in_array($_POST['item_type'], array(2, 3)) && is_numeric($_POST['item_id']))
						{
							$image = wp_carousel_item_value($_POST['item_id'], $_POST['item_type'], "image_url");
							
							if ($image != '')
							{
								echo $image;
							}
							else
							{
								echo "images/gray-grad.png";
							}
						}
						else
						{
							echo "images/gray-grad.png";
						}
					}
					else
					{
						exit;
					}
				}
				else
				{
					exit;
				}
			}
			else
			{
				exit;
			}
		}
	}
	
?>