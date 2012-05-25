<?php

	// Returns the image URL

	if (!function_exists('wpc_internal_carousel_image_url')):

	function wpc_internal_carousel_image_url($extra) { return ''; }
	
	endif;
	
	// Returns the link to the item
	
	if (!function_exists('wpc_internal_carousel_link_url')):
	
	function wpc_internal_carousel_link_url($extra) { return ''; }
	
	endif;
	
	// Returns the title
	
	if (!function_exists('wpc_internal_carousel_title')):
	
	function wpc_internal_carousel_title($extra) { return ''; }
	
	endif;
	
	// Returns the desc
	
	if (!function_exists('wpc_internal_carousel_desc')):
	
	function wpc_internal_carousel_desc($extra) { return ''; }
	
	endif;
	
	// Returns the desc
	
	if (!function_exists('wpc_internal_carousel_video_url')):
	
	function wpc_internal_carousel_video_url($extra) { return ''; }
	
	endif;
	
	// Return the ITEM ARRAY
	
	function wpc_internal_carousel_item_function($extra)
	{
		$extra = unserialize(base64_decode($extra));
		$id = $extra['ID'];
		
		$items_in_carousel = wp_carousel($id, 'internal_carousel_extra');
		$items_array = array();

		return $items_in_carousel['ITEMS'];		
	}
	
?>