<?php
	$path_to_this_theme_dir = str_replace(basename(__FILE__), '', __FILE__);
	$path_to_this_theme_dir = str_replace($_SERVER['DOCUMENT_ROOT'],'', $path_to_this_theme_dir);
	$path_to_this_theme_dir = str_replace(str_replace('index.php', '', $_SERVER['PHP_SELF']), '', $path_to_this_theme_dir);
	$path_to_this_theme_dir = get_option('siteurl').'/'.$path_to_this_theme_dir;	

	if (!$config['HAS_PANEL_WIDTH'])
	{
		$config['PANEL_WIDTH'] = '240px';
	}
	if (!$config['HAS_PANEL_HEIGHT'])
	{
		$config['PANEL_HEIGHT'] = '162px';
	}
	if (!isset($config['CAROUSEL_WIDTH']))
	{
		$config['CAROUSEL_WIDTH'] = '';
	}
	if (!isset($config['CAROUSEL_HEIGHT']))
	{
		$config['CAROUSEL_HEIGHT'] = '200px';
	}	
	
	$temp_arrows_margin = ( (int) ((str_replace('px', '', $config['CAROUSEL_HEIGHT'])) / 2) - 30);
	
	if ($temp_arrows_margin == '-30')
	{
		$temp_arrows_margin = '70';
	}
	
	if (!isset($config['THEME_SETTINGS']))
	{
		$config['THEME_SETTINGS'] = array();
	}
	
	if (!isset($config['THEME_SETTINGS']['REPEAT_MODE']))
	{
		$config['THEME_SETTINGS']['REPEAT_MODE'] = 0;
	}
	
	$repeat_mode = '';
	switch ($config['THEME_SETTINGS']['REPEAT_MODE'])
	{
		case 0:
		default:
			break;
		case 1:
			$repeat_mode = 'repeat-x';
			break;
		case 2:
			$repeat_mode = 'repeat-y';
			break;
		case 3:
			$repeat_mode = 'no-repeat';
			break;
	}
	
?>

	<div class="theme-clear-slider" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>; height:<?php echo $config['CAROUSEL_HEIGHT']; ?>;">
	
		<?php if ($config['ARROWS'] && ($config['VERTICAL_MODE'] == 0)): ?>
		<div class="arrow-left" style="margin:<?php echo $temp_arrows_margin; ?>px 0 0 0;">
			<a href="javascript:stepcarousel.stepBy('carousel_<?php echo $c_id; ?>', -<?php echo $config['SLIDE_POSTS']; ?>)">
				<span class="hide"><?php printf(__('Back %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
			</a>
		</div>
		<div class="arrow-right" style="margin:<?php echo $temp_arrows_margin; ?>px 0 0 0;">
			<a href="javascript:stepcarousel.stepBy('carousel_<?php echo $c_id; ?>', <?php echo $config['SLIDE_POSTS']; ?>)">
				<span class="hide"><?php printf(__('Forward %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
			</a>
		</div>
		<?php endif; ?>
		
		<div id="carousel_<?php echo $c_id; ?>" class="stepcarousel theme-clear-slider-carousel" style="height:<?php echo $config['CAROUSEL_HEIGHT']; ?>;">
	
			<div class="belt">
				<?php foreach ($items as $i_id => $item): ?>
				<div class="panel" style="width:<?php echo $config['PANEL_WIDTH']; ?>; height:<?php echo $config['PANEL_HEIGHT']; ?>;">
				
					<?php
						$there_is_image = false;
						if ($item['IMAGE_URL'] != '')
						{
							$there_is_image = true;
						}
						
						$there_is_video = false;
						if ($item['VIDEO'] != '')
						{
							$there_is_video = true;
						}
 
						if (WP_CAROUSEL_SHOW_VIDEOS_FIRST)
							{
								if ($there_is_video)
								{
									echo do_shortcode('<div class="video_margin">[embed width="'.str_replace('px', '', $config['PANEL_WIDTH']).'"]'.$item['VIDEO'].'[/embed]</div>');
								}
								else
								{
									?>
									<div class="panel_image" style="background:url(<?php echo $item['IMAGE_URL']; ?>) 0px 0px <?php echo $repeat_mode; ?>; width:<?php echo $config['PANEL_WIDTH']; ?>; height:<?php echo $config['PANEL_HEIGHT']; ?>;">
										<a href="<?php echo $item['LINK_URL']; ?>" title="<?php echo $item['TITLE']; ?>">
											<span class="hide"><?php echo $item['DESC']; ?></span>
										</a>
									</div>
									<?php
								}
							}
							else
							{
								if ($there_is_image)
								{
									?>
									<div class="panel_image" style="background:url(<?php echo $item['IMAGE_URL']; ?>) 0px 0px <?php echo $repeat_mode; ?>; width:<?php echo $config['PANEL_WIDTH']; ?>; height:<?php echo $config['PANEL_HEIGHT']; ?>;">
										<a href="<?php echo $item['LINK_URL']; ?>" title="<?php echo $item['TITLE']; ?>">
											<span class="hide"><?php echo $item['DESC']; ?></span>
										</a>
									</div>
									<?php
								}
								else
								{
									echo do_shortcode('<div class="video_margin">[embed width="'.str_replace('px', '', $config['PANEL_WIDTH']).'"]'.$item['VIDEO'].'[/embed]</div>');
								}
							}
					 ?>
							
				</div>	
				
				<?php endforeach; ?>
				
			</div>	
			
		</div>
		
		<div class="clear"></div>
					
	</div>

	<?php if ($config['ENABLE_PAGINATION']): ?>
	
	<div id="carousel_<?php echo $c_id; ?>-paginate" class="wp_carousel_clear-slider_pagination">
		<div id="carousel_<?php echo $c_id; ?>-paginate" class="wp_carousel_clear_slider_pagination">
			<img src="<?php echo $path_to_this_theme_dir; ?>img/opencircle.png" data-over="<?php echo $path_to_this_theme_dir; ?>img/graycircle.png" data-select="<?php echo $path_to_this_theme_dir; ?>img/closedcircle.png" data-moveby="1" />
		</div>
	</div>
	
	<?php endif; ?>