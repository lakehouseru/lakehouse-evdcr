<?php
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
	
?>
	<div class="theme-clear-slider" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>; height:<?php echo $config['CAROUSEL_HEIGHT']; ?>;">
	
		<?php if ($config['ARROWS'] && ($config['VERTICAL_MODE'] == 0)): ?>
		<div class="arrow-left" style="margin:<?php echo $temp_arrows_margin; ?>px 0 0 0;">
			<a href="#scroll" class="carousel_<?php echo $c_id; ?>_next">
				<span class="hide"><?php printf(__('Forward %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
			</a>
		</div>
		<div class="arrow-right" style="margin:<?php echo $temp_arrows_margin; ?>px 0 0 0;">
			<a href="#scroll" class="carousel_<?php echo $c_id; ?>_prev">
				<span class="hide"><?php printf(__('Back %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
			</a>
		</div>
		<?php endif; ?>
				
		<ul id="carousel_<?php echo $c_id; ?>" class="jcarousel-skin-clear-slider" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>; height:<?php echo $config['CAROUSEL_HEIGHT']; ?>;">
			
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
								echo apply_filters('the_content', '<div class="video_margin">[embed width="'.str_replace('px', '', $config['PANEL_WIDTH']).'"]'.$item['VIDEO'].'[/embed]</div>');
							}
							else
							{
								?>
								<div class="panel_image" style="background:url(<?php echo $item['IMAGE_URL']; ?>) 0px 0px; width:<?php echo $config['PANEL_WIDTH']; ?>; height:<?php echo $config['PANEL_HEIGHT']; ?>;">
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
								<div class="panel_image" style="background:url(<?php echo $item['IMAGE_URL']; ?>) 0px 0px; width:<?php echo $config['PANEL_WIDTH']; ?>; height:<?php echo $config['PANEL_HEIGHT']; ?>;">
									<a href="<?php echo $item['LINK_URL']; ?>" title="<?php echo $item['TITLE']; ?>">
										<span class="hide"><?php echo $item['DESC']; ?></span>
									</a>
								</div>
								<?php
							}
							else
							{
								echo apply_filters('the_content', '<div class="video_margin">[embed width="'.str_replace('px', '', $config['PANEL_WIDTH']).'"]'.$item['VIDEO'].'[/embed]</div>');
							}
						}
				 ?>
				
			</li>
			<?php endforeach; ?>
			
		</ul>
		
		<div class="clear"></div>
					
	</div>

	<?php if ($config['ENABLE_PAGINATION']): ?>
	
	<div id="carousel_<?php echo $c_id; ?>-paginate" class="jcarousel wp_carousel_clear-slider_pagination">
		<?php $t_id = 0; ?>
		<?php foreach ($items as $i_id => $item): ?>
			<?php $t_id++; ?>
			<a href="#"><?php echo $t_id; ?></a>
		<?php endforeach; ?>
		<div class="theme-clear-slider-clear"></div>
	</div>
	
	<?php endif; ?>