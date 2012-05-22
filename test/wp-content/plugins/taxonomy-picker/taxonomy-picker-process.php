<?php

// Tidy up the POST output of the Taxonomy Widget form to pass to the blog homepage via URI to drive normal searching behaviour
// Version: 1.12.4

function taxonomy_picker_process() {  // Build a URI form the data POSTed by the widget form

	if( !function_exists('taxonomy_picker_decode') ): // Ensure libray is available
		if( array_key_exists('premium-widget', $tpicker_options) ): // Are we using the premium version?
			require_once( silverghyll_theme_preferred( TPICKER_DIR . 'tpicker-library.php' ) ); // Use required library version
		else:
			require_once( silverghyll_theme_preferred( TPICKER_DIR . 'taxonomy-picker-library.php' ) ); // Use required library version
		endif;
	endif;

	if( count($_POST)>0 ):
	
		$post_vars = $_POST;
		if( taxonomy_picker_decode( $post_vars['kate-phizackerley'] ) <> 'taxonomy-picker'):	
			return; // POSTED data wasn't for Taxonomy Picker
		endif;
		$custom_query=''; 
		
		foreach($post_vars as $item => $data):
		
			$clean_data = taxonomy_picker_decode($data);  // Sanitise inputs
			$clean_item = taxonomy_picker_decode($item);
			
			if($clean_item <> 'set_categories' and $clean_item <> 's' and $clean_item <> 'kate-phizackerley'): // We have a result from a combo box						
				if(strpos($clean_data,'=tp-all') === false):  // Specific taxonomy picked

					$custom_query .= ( ($custom_query) ? '&' : '' ) . strtok( $clean_data, '=' ) . '=' ;
					
					$rest = strtok( "=" );
					$rest = str_replace( ',', 'xxx123comma321xxx', $rest ); // Preserve commas
					$rest = sanitize_title( $rest ); // eg add &writer=Kate-Phizackerley
					$rest = str_replace( 'xxx123comma321xxx', ',', $rest ); // Preserve commas
					
					$custom_query .= $rest;

				elseif($clean_item == 'category'): // For All categories we need to restrict search to the specified in the dashbaord
					$custom_query .= ( ($custom_query) ? '&' : '' ) . 
							taxonomy_picker_decode( $_POST['set_categories'] );  // Already prepared for use as comma delim set of cat ids before POSTing
				endif;
			endif;
		endforeach;
		if($post_vars['s'] <> ''):
			$search_text = urlencode( $post_vars['s'] );
			$custom_query = 's='. $search_text . (($custom_query) ? '&' : ''). $custom_query;  // Add text search option into URI
		endif;
		
		//Read the Taxonomy Picker options
		$tpicker_options = get_option('taxonomy-picker-options');

		if($custom_query):  // We have a search string
		
			if( array_key_exists( 'remember' , $tpicker_options ) ) 
			
				$custom_query .= '&silverghyll_tpicker=' . taxonomy_picker_encode($custom_query);  // Save our query for defaulting widget
					
				$blog_url = get_bloginfo('url');
				$blog_url = (($blog_url[-1] == '/') ? $blog_url : $blog_url . '/').'?'.$custom_query;  // Our composite URL for searching
				
			elseif( array_key_exists( 'miss-url', $tpicker_options ) ):
			
				$blog_url = $tpicker_options['miss-url']; // Default to the main blog
				
			else:
			
				$blog_url = get_bloginfo('url');			
				
			endif;
		
		$blog_url = apply_filters('tpicker_redirect', $blog_url);
	
		if( array_key_exists( 'redirect', $tpicker_options ) ): // Enter debug mode to show the redirection
			echo "<h3>Built URL: $blog_url</h3>";
			sleep(3); // Actually usually stops entireley
		endif;	
					
		wp_redirect($blog_url, 302 );  // Redirect to the built URI
		die();
		
	endif;
return;
}
?>