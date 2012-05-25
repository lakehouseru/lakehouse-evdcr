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
	
	// Return the ITEM ARRAY
	
	function wpc_tcp_category_item($extra)
	{
		$extra = unserialize(base64_decode($extra));
		$id = $extra['ID'];
		$slug = get_term_by('id', $id, 'tcp_product_category');
		$slug = $slug->slug;
		
		if (!isset($extra['NUMBER']))
		{
			$extra['NUMBER'] = -1;
		}
		
		$number = $extra['NUMBER'];
		if ($number == '')
		{
			$number = -1;
		}
		
		//The Query
		query_posts(array('tcp_product_category' => $slug, 'posts_per_page' => $number));
		
		$products = array();
		
		//The Loop
		if ( have_posts() ) : while ( have_posts() ) : the_post();

			$this_product_id = get_the_ID();
			
			$product_serialized = base64_encode(serialize(array('ID' => $this_product_id))); 
		
			$products[] = array(
				'ID' => $this_product_id,
				'TITLE' => wpc_tcp_title($product_serialized),
				'DESC' => wpc_tcp_desc($product_serialized),
				'IMAGE_URL' => wpc_tcp_image_url($product_serialized),
				'LINK_URL' => wpc_tcp_link_url($product_serialized),
				'VIDEO' => wpc_tcp_video_url($product_serialized),
				'TYPE' => 'tcp-singleproduct' // YOU HAVE TO INDICATE WHICH KIND OF ITEM IT IS
			);
		
		endwhile; endif;
		
		//Reset Query
		wp_reset_query();
		
		return $products;	
		
	}
	
?>