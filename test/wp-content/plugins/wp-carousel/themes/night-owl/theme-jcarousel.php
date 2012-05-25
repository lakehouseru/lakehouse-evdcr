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
		$config['CAROUSEL_HEIGHT'] = '250px';
	}	
	
	$temp_video_width = ( (int) str_replace('px', '', $config['PANEL_WIDTH']) - 10);
	$temp_image_height = ( (int) str_replace('px', '', $config['PANEL_HEIGHT']) - 9).'px';
	
	$temp_arrows_margin = ( (int) ((str_replace('px', '', $config['CAROUSEL_HEIGHT']) - 50) / 2) + 50 );
	
?>
	<div class="theme-night-owl" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>; height:<?php echo $config['CAROUSEL_HEIGHT']; ?>;">
	
		<?php if ($config['ARROWS'] && ($config['VERTICAL_MODE'] == 0)): ?>
		<div class="arrow-left" style="margin:<?php echo $temp_arrows_margin; ?>;">
			<a href="#scroll" class="carousel_<?php echo $c_id; ?>_next">
				<span class="hide"><?php printf(__('Forward %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
			</a>
		</div>
		<div class="arrow-right" style="margin:<?php echo $temp_arrows_margin; ?>;">
			<a href="#scroll" class="carousel_<?php echo $c_id; ?>_prev">
				<span class="hide"><?php printf(__('Back %s panel', 'wp_carousel'), $config['SLIDE_POSTS']); ?></span>
			</a>
		</div>
		<?php endif; ?>
				
		<ul id="carousel_<?php echo $c_id; ?>" class="jcarousel-skin-night-owl">
			
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
				?>
			
				<div class="panel-in">
					
					<?php 
						if (WP_CAROUSEL_SHOW_VIDEOS_FIRST)
							{
								if ($there_is_video)
								{
									echo do_shortcode('<div class="video_margin">[embed width="'.$temp_video_width.'"]'.$item['VIDEO'].'[/embed]</div>');
								}
								else
								{
									?>
									<div class="panel_image" style="background:url(<?php echo $item['IMAGE_URL']; ?>) 0px 0px; width:<?php echo $temp_video_width.'px'; ?>; height:<?php echo $temp_image_height; ?>;">
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
									<div class="panel_image" style="background:url(<?php echo $item['IMAGE_URL']; ?>) 0px 0px; width:<?php echo $temp_video_width.'px'; ?>; height:<?php echo $temp_image_height; ?>;">
										<a href="<?php echo $item['LINK_URL']; ?>" title="<?php echo $item['TITLE']; ?>">
											<span class="hide"><?php echo $item['DESC']; ?></span>
										</a>
									</div>
									<?php
								}
								else
								{
									echo do_shortcode('<div class="video_margin">[embed width="'.$temp_video_width.'"]'.$item['VIDEO'].'[/embed]</div>');
								}
							}
					 ?>
					
				</div>

			</li>
			<?php endforeach; ?>
			
		</ul>
		
		<div class="clear"></div>
					
	</div>

	<?php if ($config['ENABLE_PAGINATION']): ?>
	
	<div id="carousel_<?php echo $c_id; ?>-paginate" class="jcarousel wp_carousel_night-owl_pagination" style="width:<?php echo $config['CAROUSEL_WIDTH']; ?>;">
		<?php $t_id = 0; ?>
		<?php foreach ($items as $i_id => $item): ?>
			<?php $t_id++; ?>
			<a href="#"><?php echo $t_id; ?></a>
		<?php endforeach; ?>
		<div class="theme-night-owl-clear"></div>
	</div>
	
	<?php endif; ?>