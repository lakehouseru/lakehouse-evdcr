<?php
	$path_to_this_theme_dir = str_replace(basename(__FILE__), '', __FILE__);
	$path_to_this_theme_dir = str_replace($_SERVER['DOCUMENT_ROOT'],'', $path_to_this_theme_dir);
	$path_to_this_theme_dir = str_replace(str_replace('index.php', '', $_SERVER['PHP_SELF']), '', $path_to_this_theme_dir);
	$path_to_this_theme_dir = get_option('siteurl').'/'.$path_to_this_theme_dir;
	
	if (!$config['HAS_IMG_WIDTH'])
	{
	 	$config['IMG_WIDTH'] = '100px';
	}
	if (!$config['HAS_IMG_HEIGHT'])
	{
		$config['IMG_HEIGHT'] = '100px';
	}
	if (!$config['HAS_PANEL_WIDTH'])
	{
		$config['PANEL_WIDTH'] = '158px';
	}
	if (!$config['HAS_PANEL_HEIGHT'])
	{
		$config['PANEL_HEIGHT'] = '180px';
	}
	if (!isset($config['CAROUSEL_WIDTH']))
	{
		$config['CAROUSEL_WIDTH'] = '';
	}
	if (!isset($config['CAROUSEL_HEIGHT']))
	{
		$config['CAROUSEL_HEIGHT'] = '';
	}
	
	if (!isset($config['THEME_SETTINGS']))
	{
		$config['THEME_SETTINGS'] = array();
	}
	
	if (!isset($config['THEME_SETTINGS']['SHOW_TITLES_IN_DESC']))
	{
		$config['THEME_SETTINGS']['SHOW_TITLES_IN_DESC'] = 0;
	}
	
	if (!isset($config['THEME_SETTINGS']['COLOR_VARIATION']))
	{
		$config['THEME_SETTINGS']['COLOR_VARIATION'] = 'default';
	}
	
	if (!isset($config['THEME_SETTINGS']['SHOW_TITLES_IN_DESC']))
	{
		$config['THEME_SETTINGS']['SHOW_TITLES_IN_DESC'] = 0;
	}
		
?>

	<div class="theme-default-sidebar<?php echo ' cv-'.$config['THEME_SETTINGS']['COLOR_VARIATION']; ?>" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>">
		<div class="top-div">
			<?php if ($config['ARROWS']): ?>
			<div class="arrow-right">
				<p class="arrow">
					<a href="javascript:stepcarousel.stepBy('carousel_<?php echo $c_id; ?>', -<?php echo $config['SLIDE_POSTS']; ?>)">
						<span class="hide"><?php printf(__('Back %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
					</a>
				</p>
			</div>
			<div class="arrow-left">
				<p class="arrow">
					<a href="javascript:stepcarousel.stepBy('carousel_<?php echo $c_id; ?>', <?php echo $config['SLIDE_POSTS']; ?>)">
						<span class="hide"><?php printf(__('Forward %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
					</a>
				</p>
			</div>
			<div class="clear"></div>
			<?php endif; ?>
		</div>
	</div>
	<div id="carousel_<?php echo $c_id; ?>" class="stepcarousel theme-default-sidebar-carousel<?php echo ' cv-'.$config['THEME_SETTINGS']['COLOR_VARIATION']; ?>" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>; height:<?php echo $config['CAROUSEL_HEIGHT']; ?>">
	
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
							echo do_shortcode('[embed width="'.str_replace('px', '', str_replace('%', '', str_replace('em', '', $config['IMG_WIDTH']))).'" height="'.str_replace('px', '', str_replace('%', '', str_replace('em', '', $config['IMG_HEIGHT']))).'"]'.$item['VIDEO'].'[/embed]');
						}
						else
						{
							?>
						<a href="<?php echo $item['LINK_URL']; ?>" title="<?php echo $item['TITLE']; ?>">
							<img src="<?php echo $item['IMAGE_URL']; ?>" alt="<?php echo $item['TITLE']; ?>" title="<?php echo $item['TITLE']; ?>" style="width:<?php echo $config['IMG_WIDTH']; ?>; height:<?php echo $config['IMG_HEIGHT']; ?>;" />
						</a>
							<?php
						}
					}
					else
					{
						if ($there_is_image)
						{
							?>
						<a href="<?php echo $item['LINK_URL']; ?>" title="<?php echo $item['TITLE']; ?>">
							<img src="<?php echo $item['IMAGE_URL']; ?>" alt="<?php echo $item['TITLE']; ?>" title="<?php echo $item['TITLE']; ?>" style="width:<?php echo $config['IMG_WIDTH']; ?>; height:<?php echo $config['IMG_HEIGHT']; ?>;" />
						</a>
							<?php
						}
						else
						{
							echo do_shortcode('[embed width="'.str_replace('px', '', str_replace('%', '', str_replace('em', '', $config['IMG_WIDTH']))).'" height="'.str_replace('px', '', str_replace('%', '', str_replace('em', '', $config['IMG_HEIGHT']))).'"]'.$item['VIDEO'].'[/embed]');
						}
					}
				
				?>	
				
				<div class="panel-text">
				<?php echo $item['DESC']; ?>
				</div>
			</div>
			<?php endforeach; ?>
		</div>
	
	</div>
	<div class="theme-default-sidebar<?php echo ' cv-'.$config['THEME_SETTINGS']['COLOR_VARIATION']; ?>" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>">
		<div class="bottom-div">
			<?php if ($config['ENABLE_PAGINATION']): ?>
			<div id="carousel_<?php echo $c_id; ?>-paginate" class="wp_carousel_default_sidebar_pagination">
				<img src="<?php echo $path_to_this_theme_dir; ?>img/opencircle.png" data-over="<?php echo $path_to_this_theme_dir; ?>img/graycircle.png" data-select="<?php echo $path_to_this_theme_dir; ?>img/closedcircle.png" data-moveby="1" />
			</div>
			<?php endif; ?>
		</div>
	</div>
