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
	
	// Return the ITEM ARRAY
	
	function wpc_akopt_taxonomy_item($extra)
	{
		$extra = unserialize(base64_decode($extra));
		$id = $extra['ID'];
		$taxonomy = $extra['TAXONOMY'];
		$slug = get_term_by('id', $id, $taxonomy);
		
		if (is_object($slug)) // We check if that term exists
		{
			$slug = $slug->slug;
		}
		else
		{
			$slug = false;
		}
		
		if ($slug != false):
		
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
			query_posts(array($taxonomy => $slug, 'posts_per_page' => $number));
			
			$final_items = array();
			
			//The Loop
			if ( have_posts() ) : while ( have_posts() ) : the_post();
	
				$this_item_id = get_the_ID();
				
				$item_serialized = base64_encode(serialize(array('ID' => $this_item_id))); 
			
				$final_items[] = array(
					'ID' => $this_item_id,
					'TITLE' => wpc_tcp_title($item_serialized),
					'DESC' => wpc_tcp_desc($item_serialized),
					'IMAGE_URL' => wpc_tcp_image_url($item_serialized),
					'LINK_URL' => wpc_tcp_link_url($item_serialized),
					'VIDEO' => wpc_tcp_video_url($item_serialized),
					'TYPE' => 'akopt-singleitemwithcustomtaxonomy' // YOU HAVE TO INDICATE WHICH KIND OF ITEM IT IS
				);
			
			endwhile; endif;
			
			//Reset Query
			wp_reset_query();
			
			return $final_items;
		
		else:
		
			return array();
		
		endif;	
		
	}
	
?>