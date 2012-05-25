<?php

	// Returns the image URL

	if (!function_exists('wpc_tcp_image_url')):

	function wpc_tcp_image_url($extra)
	{
		$extra = unserialize(base64_decode($extra));
		$id = $extra['ID'];	
		
		return wp_carousel_item_value($id, '2', 'image_url', array(), 'get');
	}
	
	endif;
	
	// Returns the link to the item
	
	if (!function_exists('wpc_tcp_link_url')):
	
	function wpc_tcp_link_url($extra)
	{	
		$extra = unserialize(base64_decode($extra));
		$id = $extra['ID'];	
		
		return wp_carousel_item_value($id, '2', 'link_url', array(), 'get');
	}
	
	endif;
	
	// Returns the title
	
	if (!function_exists('wpc_tcp_title')):
	
	function wpc_tcp_title($extra)
	{
		$extra = unserialize(base64_decode($extra));
		$id = $extra['ID'];	
		
		return wp_carousel_item_value($id, '2', 'name', array(), 'get');
	}
	
	endif;
	
	// Returns the desc
	
	if (!function_exists('wpc_tcp_desc')):
	
	function wpc_tcp_desc($extra)
	{
		$extra = unserialize(base64_decode($extra));
		$id = $extra['ID'];	
		
		return wp_carousel_item_value($id, '2', 'desc', array(), 'get');
	}
	
	endif;
	
	// Returns the video URL
	
	if (!function_exists('wpc_tcp_video_url')):
	
	function wpc_tcp_video_url($extra)
	{
		$extra = unserialize(base64_decode($extra));
		$id = $extra['ID'];	
		
		return wp_carousel_get_video_embed_code($id, 'get');
	}
	
	endif;
	
?>