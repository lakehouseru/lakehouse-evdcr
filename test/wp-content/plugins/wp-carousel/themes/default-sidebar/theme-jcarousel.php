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
		if ($config['VERTICAL_MODE'] != 0)
		{
			$config['PANEL_WIDTH'] = '150px';
		}
		else
		{
			$config['PANEL_WIDTH'] = '530px';	
		}
	}
	if (!$config['HAS_PANEL_HEIGHT'])
	{
		if ($config['VERTICAL_MODE'] != 0)
		{
			$config['PANEL_HEIGHT'] = '180px';
		}
		else
		{
			$config['PANEL_HEIGHT'] = '210px';
		}
	}
	if (!isset($config['CAROUSEL_WIDTH']))
	{
		$config['CAROUSEL_WIDTH'] = '';
	}
	if (!isset($config['CAROUSEL_HEIGHT']))
	{
		if ($config['VERTICAL_MODE'] != 0)
		{
			$config['CAROUSEL_HEIGHT'] = '';
		}
		else
		{
			$config['CAROUSEL_HEIGHT'] = '210px';
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
	
	if (!isset($config['THEME_SETTINGS']['SHOW_TITLES_IN_DESC']))
	{
		$config['THEME_SETTINGS']['SHOW_TITLES_IN_DESC'] = 0;
	}
	
	if ($config['CAROUSEL_WIDTH'] != '')
	{
		$temp_paginator_width = '';
	}
	else
	{
		$temp_paginator_width = str_replace('px', '', $config['CAROUSEL_WIDTH']);
		$temp_paginator_width = (int) $temp_paginator_width;
		$temp_paginator_width  -= 60;
		$temp_paginator_width = $temp_paginator_width .'px';
	}
		
?>
	<div class="theme-default-sidebar<?php echo ' cv-'.$config['THEME_SETTINGS']['COLOR_VARIATION']; ?>" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>">
		<div class="top-div">
			<?php if ($config['ARROWS']): ?>
				<?php if ($config['VERTICAL_MODE'] == 0): ?>
				<div class="arrow-right">
					<p class="arrow"><a href="#scroll" class="carousel_<?php echo $c_id; ?>_next">
						<span class="hide"><?php printf(__('Forward %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
					</a></p>
				</div>
				<div class="arrow-left">
					<p class="arrow"><a href="#scroll" class="carousel_<?php echo $c_id; ?>_prev">
						<span class="hide"><?php printf(__('Back %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
					</a></p>
				</div>
				<div class="clear"></div>
				<?php else: ?>
				<div class="arrow-top">
					<p><a href="#scroll" class="carousel_<?php echo $c_id; ?>_next">
						<span class="hide"><?php printf(__('Forward %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
					</a></p>
				</div>
				<div class="arrow-bottom">
					<p><a href="#scroll" class="carousel_<?php echo $c_id; ?>_prev">
						<span class="hide"><?php printf(__('Back %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
					</a></p>
				</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
	</div>

	<div class="carousel-default-sidebar-background<?php echo ' cv-'.$config['THEME_SETTINGS']['COLOR_VARIATION']; ?>" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>; height:<?php echo $config['CAROUSEL_HEIGHT']; ?>;">
		
			<ul id="carousel_<?php echo $c_id; ?>" class="jcarousel-skin-default-sidebar">
			
				<?php foreach ($items as $i_id => $item): ?>
				<li class="panel" style="width:<?php echo $config['PANEL_WIDTH']; ?>; height:<?php echo $config['PANEL_HEIGHT']; ?>;">
					
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
									echo apply_filters('the_content', '[embed width="'.str_replace('px', '', str_replace('%', '', str_replace('em', '', $config['IMG_WIDTH']))).'" height="'.str_replace('px', '', str_replace('%', '', str_replace('em', '', $config['IMG_HEIGHT']))).'"]'.$item['VIDEO'].'[/embed]');
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
									echo apply_filters('the_content', '[embed width="'.str_replace('px', '', str_replace('%', '', str_replace('em', '', $config['IMG_WIDTH']))).'" height="'.str_replace('px', '', str_replace('%', '', str_replace('em', '', $config['IMG_HEIGHT']))).'"]'.$item['VIDEO'].'[/embed]');
								}
							}
					?>
					
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
				</li>
				<?php endforeach; ?>
			
			</ul>
		
	</div>
	
	<div id="carousel_<?php echo $c_id; ?>-paginate" class="jcarousel wp_carousel_default_sidebar_pagination<?php echo ' cv-'.$config['THEME_SETTINGS']['COLOR_VARIATION']; ?>" style="width:<?php echo $temp_paginator_width; ?>">
		<?php
			if ($config['ENABLE_PAGINATION']):
				$t_id = 0;
				foreach ($items as $i_id => $item):
					$t_id++;
		?>
				<a href="#"><?php echo $t_id; ?></a>
		<?php
				endforeach;
			endif;
		?>
	</div>