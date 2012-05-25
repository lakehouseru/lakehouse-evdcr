<?php

	// Returns the image URL

	if (!function_exists('wpc_akopt_image_url')):

	function wpc_akopt_image_url($extra)
	{
		$extra = unserialize(base64_decode($extra));
		$id = $extra['ID'];	
		
		return wp_carousel_item_value($id, '2', 'image_url', array(), 'get');
	}
	
	endif;
	
	// Returns the link to the item
	
	if (!function_exists('wpc_akopt_link_url')):
	
	function wpc_akopt_link_url($extra)
	{	
		$extra = unserialize(base64_decode($extra));
		$id = $extra['ID'];	
		
		return wp_carousel_item_value($id, '2', 'link_url', array(), 'get');
	}
	
	endif;
	
	// Returns the title
	
	if (!function_exists('wpc_akopt_title')):
	
	function wpc_akopt_title($extra)
	{
		$extra = unserialize(base64_decode($extra));
		$id = $extra['ID'];	
		
		return wp_carousel_item_value($id, '2', 'name', array(), 'get');
	}
	
	endif;
	
	// Returns the desc
	
	if (!function_exists('wpc_akopt_desc')):
	
	function wpc_akopt_desc($extra)
	{
		$extra = unserialize(base64_decode($extra));
		$id = $extra['ID'];	
		
		return wp_carousel_item_value($id, '2', 'desc', array(), 'get');
	}
	
	endif;
	
	// Returns the video URL

	if (!function_exists('wpc_akopt_video_url')):

	function wpc_akopt_video_url($extra)
	{
		$extra = unserialize(base64_decode($extra));
		$id = $extra['ID'];	
		
		return wp_carousel_get_video_embed_code($id, 'get');
	}
	
	endif;
	
?>