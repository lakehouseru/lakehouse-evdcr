<?php
class SimpleTags_Client_Autoterms extends SimpleTags_Client {
	/**
	 * Constructor
	 *
	 * @return void
	 * @author Amaury Balmer
	 */
	function SimpleTags_Client_Autoterms() {
		add_action( 'save_post', 				array(&$this, 'saveAutoTerms'), 12, 2 );
		add_action( 'post_syndicated_item', 	array(&$this, 'saveAutoTerms'), 12, 2 );
	}
	
	/**
	 * Check post/page content for auto terms
	 *
	 * @param integer $post_id
	 * @param object $object
	 * @return boolean
	 */
	function saveAutoTerms( $post_id = null, $object = null ) {
		// Get options
		$options = get_option( STAGS_OPTIONS_NAME_AUTO );
		
		// Auto terms for this CPT ?
		if ( !isset($options[$object->post_type]) || empty($options[$object->post_type]) )
			return false;
			
		// user preference for this post ?
		$meta_value = get_post_meta( $object->ID, '_exclude_autotags', true );
		if ( !empty($meta_value) )
			return false;
		
		// Loop option for find if autoterms is actived on any taxo
		$flag = false;
		foreach( $options[$object->post_type] as $taxo_name => $local_options ) {
			if ( !isset($local_options['use_auto_terms']) || $local_options['use_auto_terms'] != '1' )
				continue;
			
			$this->autoTermsPost( $object, $taxo_name, $local_options );
			$flag = true;
		}
		
		if ( $flag == true ) { // Clean cache ?
			if ( isset($object->post_type) && $object->post_type == 'page' ) {
				clean_page_cache($post_id);
			} else {
				clean_post_cache($post_id);
			}
		}
		
		return true;
	}
	
	/**
	 * Automatically tag a post/page from the database terms for the taxonomy specified
	 *
	 * @param object $object 
	 * @param string $taxonomy 
	 * @param array $options 
	 * @param boolean $counter 
	 * @return boolean
	 * @author Amaury Balmer
	 */
	function autoTermsPost( $object, $taxonomy = 'post_tag', $options = array(), $counter = false ) {
		global $wpdb;
		
		if ( get_the_terms($object->ID, $taxonomy) != false && $options['at_empty'] == 1 ) {
			return false; // Skip post with terms, if term only empty post option is checked
		}
		
		$terms_to_add = array();
		
		// Merge title + content + excerpt to compare with terms
		$content = $object->post_content. ' ' . $object->post_title;
		if ( isset($object->post_excerpt) )
		 	$content .= ' ' . $object->post_excerpt;
		
		$content = trim(strip_tags($content));
		if ( empty($content) ) {
			return false;
		}
		
		// Auto term with specific auto terms list
		$terms = (array) maybe_unserialize($options['auto_list']);
		foreach ( $terms as $term ) {
			if ( !is_string($term) && empty($term) )
			 	continue;
			
			$term = trim($term);
			
			// Whole word ?
			if ( (int) $options['only_full_word'] == 1 ) {
				if ( preg_match("/\b".$term."\b/i", $content) )
					$terms_to_add[] = $term;
			} elseif ( stristr($content, $term) ) {
				$terms_to_add[] = $term;
			}
		}
		unset($terms, $term);
		
		// Auto terms with all terms
		if ( $options['at_all'] == 1 ) {
			// Get all terms
			$terms = $wpdb->get_col( $wpdb->prepare("SELECT DISTINCT name
				FROM {$wpdb->terms} AS t
				INNER JOIN {$wpdb->term_taxonomy} AS tt ON t.term_id = tt.term_id
				WHERE tt.taxonomy = %s", $taxonomy) );
			
			$terms = array_unique($terms);
			
			foreach ( $terms as $term ) {
				$term = stripslashes($term);
				
				if ( !is_string($term) && empty($term) )
				 	continue;
				
				// Whole word ?
				if ( (int) $options['only_full_word'] == 1 ) {
					$term = ' '.$term.' '; // Add space before and after !
				}
				
				if ( stristr($content, $term) ) {
					$terms_to_add[] = $term;
				}
			}
			
			// Clean memory
			$terms = array();
			unset($terms, $term);
		}
		
		// Append terms if terms to add
		if ( !empty($terms_to_add) ) {
			// Remove empty and duplicate elements
			$terms_to_add = array_filter($terms_to_add, '_delete_empty_element');
			$terms_to_add = array_unique($terms_to_add);
			
			if ( $counter == true ) {
				// Increment counter
				$counter = ((int) get_option('tmp_auto_terms_st')) + count($terms_to_add);
				update_option('tmp_auto_terms_st', $counter);
			}
			
			// Add terms to posts
			wp_set_object_terms( $object->ID, $terms_to_add, $taxonomy, true );
			
			// Clean cache
			if ( isset($object->post_type) && $object->post_type = 'page' ) {
				clean_page_cache($object->ID);
			} else {
				clean_post_cache($object->ID);
			}
			
			return true;
		}
		
		return false;
	}

}
?>