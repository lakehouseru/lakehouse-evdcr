<?php
	$path_to_this_theme_dir = str_replace(basename(__FILE__), '', __FILE__);
	$path_to_this_theme_dir = str_replace($_SERVER['DOCUMENT_ROOT'],'', $path_to_this_theme_dir);
	$path_to_this_theme_dir = str_replace(str_replace('index.php', '', $_SERVER['PHP_SELF']), '', $path_to_this_theme_dir);
	$path_to_this_theme_dir = get_option('siteurl').'/'.$path_to_this_theme_dir;
	
	if (!isset($config['THEME_SETTINGS']))
	{
		$config['THEME_SETTINGS'] = array();
	}
		
?>

	<?php if (isset($config['THEME_SETTINGS']['CAROUSEL_TITLE'])) { echo '<h2>'.$config['THEME_SETTINGS']['CAROUSEL_TITLE'].'</h2>'; } ?>

	<div class="slider-wrapper theme-nivo">
		
		<div class="ribbon"></div>

		<div class="theme-nivo-nivo" id="slider_<?php echo $c_id; ?>" class="nivoSlider">
			<?php foreach ($items as $i_id => $item): ?>
				<a href="<?php echo $item['LINK_URL']; ?>">
					<img src="<?php echo $item['IMAGE_URL']; ?>" alt="" title=<?php echo $item['TITLE']; ?>" />
				</a>
			<?php endforeach; ?>
		</div>
		
	</div>