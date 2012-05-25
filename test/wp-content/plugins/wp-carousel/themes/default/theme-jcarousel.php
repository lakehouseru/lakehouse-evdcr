<?php
	if (!isset($config['VERTICAL_MODE']))
	{
		$config['VERTICAL_MODE'] = 0;
	}
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
		$config['PANEL_WIDTH'] = '530px';	
	}
	if (!$config['HAS_PANEL_HEIGHT'])
	{
		if ($config['VERTICAL_MODE'] != 0)
		{
			$config['PANEL_HEIGHT'] = '160px';
		}
		else
		{
			$config['PANEL_HEIGHT'] = '210px';
		}
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
	
	if (!isset($config['THEME_SETTINGS']['WHERE_TO_SHOW_DESC']))
	{
		$config['THEME_SETTINGS']['WHERE_TO_SHOW_DESC'] = 'under_image';
	}
	
	if (!isset($config['THEME_SETTINGS']['CAROUSEL_WIDTH']))
	{
		$config['THEME_SETTINGS']['CAROUSEL_WIDTH'] = '';
		$temp_paginator_width = '';
	}
	else
	{
		$temp_paginator_width = str_replace('px', '', $config['THEME_SETTINGS']['CAROUSEL_WIDTH']);
		$temp_paginator_width = (int) $temp_paginator_width;
		$temp_paginator_width  -= 60;
		$temp_paginator_width = $temp_paginator_width .'px';
	}
	
	switch (true)
	{
		case ($config['THEME_SETTINGS']['WHERE_TO_SHOW_DESC'] == 'under_image'):
		default:
			$text_css = '';
			break;
		case ($config['THEME_SETTINGS']['WHERE_TO_SHOW_DESC'] == 'left'):
			$text_css = ' at_left';
			break;
		case ($config['THEME_SETTINGS']['WHERE_TO_SHOW_DESC'] == 'right'):
			$text_css = ' at_right';
			break;
	}
		
?>
	<?php if (isset($config['THEME_SETTINGS']['CAROUSEL_TITLE'])) { echo '<h2>'.$config['THEME_SETTINGS']['CAROUSEL_TITLE'].'</h2>'; } ?>

	<div class="theme-default<?php echo ' cv-'.$config['THEME_SETTINGS']['COLOR_VARIATION']; ?>" style="width:<?php echo $config['THEME_SETTINGS']['CAROUSEL_WIDTH']; ?>">
	
		<div class="arrow-right">
			<?php if ($config['ARROWS'] && ($config['VERTICAL_MODE'] == 0)): ?>
			<p><a href="#scroll" class="carousel_<?php echo $c_id; ?>_next">
				<span class="hide"><?php printf(__('Forward %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
			</a></p>
			<?php endif; ?>
		</div>
		<div class="arrow-left">
			<?php if ($config['ARROWS'] && ($config['VERTICAL_MODE'] == 0)): ?>
			<p><a href="#scroll" class="carousel_<?php echo $c_id; ?>_prev">
				<span class="hide"><?php printf(__('Back %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
			</a></p>
			<?php endif; ?>
		</div>
		
		<div class="carousel-default-background">
		
			<?php if ($config['ARROWS'] && ($config['VERTICAL_MODE'] != 0)): ?>
			<div class="arrow-top">
				<p><a href="#scroll" class="carousel_<?php echo $c_id; ?>_next">
					<span class="hide"><?php printf(__('Forward %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
				</a></p>
			</div>
			<?php endif; ?>
			
			<ul id="carousel_<?php echo $c_id; ?>" class="jcarousel-skin-default">
			
				<?php foreach ($items as $i_id => $item): ?>
				<li class="panel" style="width:<?php echo $config['PANEL_WIDTH']; ?>; height:<?php echo $config['PANEL_HEIGHT']; ?>;">
					
					<?php if ($text_css != '') { ?>				
					<div class="panel-text<?php echo $text_css; ?>">
					<?php
						if ($config['THEME_SETTINGS']['SHOW_TITLES_IN_DESC'] == 0)
						{
							echo $item['DESC']; 
						}
						else
						{
							echo $item['TITLE'];
						}
					?>
					</div>
					<?php } ?>
					
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
					
					<?php if ($text_css == '') { ?>				
					<div class="panel-text">
					<?php
						if ($config['THEME_SETTINGS']['SHOW_TITLES_IN_DESC'] == 0)
						{
							echo $item['DESC']; 
						}
						else
						{
							echo $item['TITLE'];
						}
					?>
					</div>
					<?php } ?>
				</li>
				<?php endforeach; ?>
			
			</ul>
			
			<?php if ($config['ARROWS'] && ($config['VERTICAL_MODE'] != 0)): ?>
			<div class="arrow-bottom">
				<p><a href="#scroll" class="carousel_<?php echo $c_id; ?>_prev">
					<span class="hide"><?php printf(__('Back %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
				</a></p>
			</div>
			<?php endif; ?>
		
		</div>
		
	</div>

	<?php if ($config['ENABLE_PAGINATION']): ?>
	
	<div id="carousel_<?php echo $c_id; ?>-paginate" class="jcarousel wp_carousel_default_pagination<?php echo ' cv-'.$config['THEME_SETTINGS']['COLOR_VARIATION']; ?>" style="width:<?php echo $temp_paginator_width; ?>">
		<?php $t_id = 0; ?>
		<?php foreach ($items as $i_id => $item): ?>
			<?php $t_id++; ?>
			<a href="#"><?php echo $t_id; ?></a>
		<?php endforeach; ?>
	</div>
	
	<?php endif; ?>