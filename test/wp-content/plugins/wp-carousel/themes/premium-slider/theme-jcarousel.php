<?php
	if (!isset($config['CAROUSEL_WIDTH']))
	{
		$config['CAROUSEL_WIDTH'] = '100%';
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
	
	$temp_panel_width = '';
	if ($config['CAROUSEL_WIDTH'] != '' && $config['CAROUSEL_WIDTH'] != '100%')
	{
		$temp_panel_width = ( (int) (str_replace('px', '', $config['CAROUSEL_WIDTH']) - 90)).'px';
	}
	
	$temp_panel_height = '';
	if ($config['CAROUSEL_HEIGHT'] != '')
	{
		$temp_panel_height = ( (int) (str_replace('px', '', $config['CAROUSEL_HEIGHT']) - 30)).'px';
	}
	
?>
	<div class="theme-premium-slider" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>; height:<?php echo $config['CAROUSEL_HEIGHT']; ?>;">
	
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

		<ul id="carousel_<?php echo $c_id; ?>" class="jcarousel-skin-premium-slider" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>; height:<?php echo $config['CAROUSEL_HEIGHT']; ?>;">
			
			<?php foreach ($items as $i_id => $item): ?>
			<li class="panel" style="width:<?php echo $temp_panel_width; ?>; height:<?php echo $temp_panel_height; ?>;">
			
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
								echo do_shortcode('<div class="video_margin">[embed width="'.str_replace('px', '', $temp_panel_width).'"]'.$item['VIDEO'].'[/embed]</div>');
							}
							else
							{
								?>
								<div class="panel_image" style="background:url(<?php echo $item['IMAGE_URL']; ?>) 0px 0px; width:<?php echo $temp_panel_width; ?>; height:<?php echo $temp_panel_height; ?>;">
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
								<div class="panel_image" style="background:url(<?php echo $item['IMAGE_URL']; ?>) 0px 0px; width:<?php echo $temp_panel_width; ?>; height:<?php echo $temp_panel_height; ?>;">
									<a href="<?php echo $item['LINK_URL']; ?>" title="<?php echo $item['TITLE']; ?>">
										<span class="hide"><?php echo $item['DESC']; ?></span>
									</a>
								</div>
								<?php
							}
							else
							{
								echo do_shortcode('<div class="video_margin">[embed width="'.str_replace('px', '', $temp_panel_width).'"]'.$item['VIDEO'].'[/embed]</div>');
							}
						}
				 ?>
				
			</li>
			<?php endforeach; ?>
			
		</ul>
		
		<div class="clear"></div>
					
	</div>

	<?php if ($config['ENABLE_PAGINATION']): ?>
	
	<div id="carousel_<?php echo $c_id; ?>-paginate" class="jcarousel wp_carousel_premium-slider_pagination" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>; margin:30px auto;">
		<?php $t_id = 0; ?>
		<?php foreach ($items as $i_id => $item): ?>
			<?php $t_id++; ?>
			<a href="#"><span class="hide"><?php echo $t_id; ?></span></a>
		<?php endforeach; ?>
		<div class="theme-premium-slider-clear"></div>
	</div>
	
	<?php endif; ?>