<?php
/***
 * Common library routines
 *
 * Version: 1.0.3a
 * Requires: silverghyll-foundation.php
 *
 ************************************************************
 *
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Copyright Kate Phizackerley 2011,2012
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */
 
  /***
 * Array of allowed HTML
 *
 * @return 	array					Array of allowed HTML for passing to wp_kses
 */
 function silverghyll_allowed_html( $links_allowed = false ) {

	$allowed = array(
					'br' => array(),
					'sup' => array(),
					'em' => array(), 'i' => array(),
					'strong' => array(), 'b' => array(),
					'h1' => array( 'id' => array(), 'class' => array() ),
					'h2' => array( 'id' => array(), 'class' => array() ),
					'h3' => array( 'id' => array(), 'class' => array() ),
					'h4' => array( 'id' => array(), 'class' => array() ),
					'p' => array( 'id' => array(), 'class' => array() ),
					'div' => array( 'id' => array(), 'class' => array() ),
					'table' => array( 'id' => array(), 'class' => array() ),
					'tbody' => array( 'id' => array(), 'class' => array() ),
					'tr' => array( 'id' => array(), 'class' => array() ),
					'td' => array( 'id' => array(), 'class' => array() ),
					'style' => array( 'type' => array() )					
					);
	if( $links_allowed ) $allowed['a'] = array('href' => array(),'title' => array());
	
	return $allowed;
 }
 
 /***
 * Returns the theme version of the specified file if exists, if not then the specified file
 *
 * @param	$file				string		File path to test (full path)
 * @param	$test_parent	boolean		(Optional) true if parent theme folder to be tried first (default = false)
 * 
 * @return 	string						File path - theme in preference to that passed as argument
 */
 
function silverghyll_theme_preferred( $file, $test_parent = false ) {

	$paths = pathinfo( $file );
	
	if( $test_parent ):
		$result = trailingslashit( get_bloginfo( 'template_drectory' ) ) . $paths['basename'];
		if( file_exists( $result ) ):
			return $result;
		endif;
	endif;
	
	$result = trailingslashit( get_bloginfo( 'stylesheet_drectory' ) ) . $paths['basename'];
	if( file_exists( $result ) ) return $result;
	
	return $file;
		
}
 
 // Identical to get_term() but sorted in tree order
 
function silverghyll_get_terms_tree($taxonomies, $args) {
	
	if( is_array( $taxonomies ) ):
		foreach( $taxonomies as $taxonomy ) $terms[] = silverghyll_get_terms_tree( $taxonomy, $args ); // Recurse
		return $terms;
	endif;
	
	$args['parent'] = 0; // Get top level only
	$args['orderby'] = 'name'; // Want alphabetically within our tree

	$terms = get_terms($taxonomies, $args ); //Get top level terms
	
	$result = array();
	if( $terms ) foreach( $terms as $term ) $result = array_merge( $result, silverghyll_get_term_subtree($taxonomies, $term, $args) );  // Recurse sub-trees
	return $result;
	
}
// Inner function for the recursion
function silverghyll_get_term_subtree($taxonomy, $term, $args) {

		static $depth = 0;

		$args['parent'] = $term->term_id;
		$kids = get_terms( $taxonomy, $args);
			
		$result[] = $term; // Seed the array			

		$depth++ ;
		if( 5 >= $depth ) if( !empty($kids) ) foreach($kids as $kid ) $result = array_merge( $result, silverghyll_get_term_subtree($taxonomy, $kid, $args) ); // Recurse
		$depth-- ;
		
		return $result; // Will always return an array with at least one item
}
 
?>