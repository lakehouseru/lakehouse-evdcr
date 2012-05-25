<?php
	if (!$config['HAS_IMG_WIDTH'])
	{
	 	$config['IMG_WIDTH'] = '';
	}
	if (!$config['HAS_IMG_HEIGHT'])
	{
		$config['IMG_HEIGHT'] = '';
	}
	if (!$config['HAS_PANEL_WIDTH'])
	{
		$config['PANEL_WIDTH'] = '';
	}
	if (!$config['HAS_PANEL_HEIGHT'])
	{
		$config['PANEL_HEIGHT'] = '';
	}
	if (!isset($config['CAROUSEL_WIDTH']))
	{
		$config['CAROUSEL_WIDTH'] = '';
	}
	if (!isset($config['CAROUSEL_HEIGHT']))
	{
		$config['CAROUSEL_HEIGHT'] = '';
	}	
?>

	<div class="theme-skinless" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>">
	
		<?php if ($config['ARROWS'] && ($config['VERTICAL_MODE'] == 0)): ?>
		<div class="arrow-left"><a href="#scroll" class="carousel_<?php echo $c_id; ?>_next"><?php printf(__('Forward %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></a></div>
		<div class="arrow-right"><a href="#scroll" class="carousel_<?php echo $c_id; ?>_prev"><?php printf(__('Back %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></a></div>
		<?php elseif ($config['ARROWS'] && ($config['VERTICAL_MODE'] == 1)): ?>
		<div class="arrow-top"><a href="#scroll" class="carousel_<?php echo $c_id; ?>_next"><?php printf(__('Forward %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></a></div>
		<div class="arrow-bottom"><a href="#scroll" class="carousel_<?php echo $c_id; ?>_prev"><?php printf(__('Back %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></a></div>
		<?php endif; ?>
		
		<div class="clear"></div>
		
		<ul id="carousel_<?php echo $c_id; ?>" class="jcarousel-skin-skinless">
			
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

			</li>
			<?php endforeach; ?>
			
		</ul>
					
	</div>

	<?php if ($config['ENABLE_PAGINATION']): ?>
	
	<div id="carousel_<?php echo $c_id; ?>-paginate" class="jcarousel wp_carousel_skinless_pagination" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>">
		<?php $t_id = 0; ?>
		<?php foreach ($items as $i_id => $item): ?>
			<?php $t_id++; ?>
			<a href="#"><?php echo $t_id; ?></a>
		<?php endforeach; ?>
	</div>
	
	<?php endif; ?>