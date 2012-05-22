<?php

/**** 
 * 
 * Adds shortcode support to the Silverghyll Library eg [colophon]
 *
 * Version: 1.2
 *
 * Requires: silverghyll-read-plugins.php
 *
 ************************************************************
 *
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Copyright Kate Phizackerley 2012
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
 * Tests whether specified shortcode exists
 *
 * @param	$shortcode		string		name of shortcode to text
 * 
 * @return 	boolean							true if shortcode exists, else false
 */
 
if ( !function_exists( 'shortcode_exists' ) ) :
	function shortcode_exists( $shortcode = false ) {
		global $shortcode_tags;
		if( !$shortcode ) 	return false;
		if( array_key_exists( $shortcode, $shortcode_tags ) ) 	return true;
		return false;
	}
endif;


/******************************************************************************************************************************************

Each shortcode uses a set of functions with some common:

		[colophon-plugins]									BOTH									[colophon_themes]	
		*****************										****									*****************
					V												 V													V	
															scolophon_shortcode
												<=							 					=>
	scolophon_plugins_shortcode																scolophon_themes_shortcode	
												=>												<=					
														scolophon_master_shortcode
												<=							 					=>														
	scolophon_plugins_shortcode_inner														scolophon_themes_shortcode_inner

******************************************************************************************************************************************/

/***
 * Outer function for the [colophon] shortcode
 *
 * @param	$atts		array of strings		standard shortcode function array may contain values for follow, voice, text and preamble
 * 
 * @return 	string								the output of the shortcode
 */
 
function scolophon_shortcode($atts) { 

	$results = scolophon_themes_shortcode($atts);
	if( function_exists( 'scolophon_fonts_shortcode' ) ) $results .= scolophon_fonts_shortcode($atts); // Implememted in the Colophon plugin
	$results .= scolophon_plugins_shortcode($atts);

	return  $results;
}


/******************************************************************************************************************************************/


/***
 * Outer function for the colophin-plugins shortcode
 *
 * @param	$atts		array of strings		standard shortcode function array may contain values for follow, preamble, voice
 * 
 * @return 	string								the output of the shortcode
 */
 
function scolophon_plugins_shortcode($atts) { 

	if( !array_key_exists( 'preamble', $atts ) ) $atts['preamble'] = "(We) (am) grateful to the following author(s) for their <a href='http://www.wordpress.org' target='_blank'>WordPress</a><sup>&reg;</sup> plugin(s) which add features to the richness of (title):"; // Default the preamble
			
	return scolophon_master_shortcode( 'plugins', $atts);

}

/***
 * Builds the text for the [colophon-plugins] shortcode
 *
 * @param	&$plugin_count		integer		BY REF: passed so that the plugin count can be returned, @param itself is ignored.
 * @param	&text					string		Text for between the heading and the table of plugins
 * @param	&voice				string		2 signifies first person plural nouns, 1 else 
 * 
 * @return $string		the output of the shortcode
 */
function scolophon_plugins_shortcode_inner(&$plugin_count, $preamble, $voice) {

	require_once( silverghyll_include_best_library( 'silverghyll-read-plugins.php' ) );  // We need silvergyll_read_plugins()

	$plugin_dir = dirname( dirname( dirname( __FILE__ ) ) );
	$plugins = silverghyll_read_plugins( $plugin_dir ); // Cannot use get_plugins in the front end		
	$mu_plugins = silverghyll_read_plugins( !defined(WPMU_PLUGIN_DIR) ?  WPMU_PLUGIN_DIR : dirname( $plugin_dir) . '/mu-plugins/' ); // Look in MU plugins as well
	$plugins = array_merge( (array) $plugins, (array) $mu_plugins ); // Consolidate the list of plugins
	
	uasort( $plugins, create_function('$a,$b','return strcmp( $a[\'author\'], $b[\'author\'] );') ); // Sort by author

	$plugin_count = 0;

	// Build headers for table
	$results = '<style type="text/css">.colophon-plugins td{padding:1em} .colophon-plugins h4 {font-weight:bold;}</style>';
	$results .= "<table id='colophon-plugins' class='colophon-plugins'><thead><tr>";
	$results .= "<td><h4>" . __('Author' ) . "</h4></td>";
	$results .= "<td><h4>" . __( 'Contributors' ) . "</h4></td>";
	$results .= "<td><h4>" . __( 'Plugin' ) . "</h4></td>";
	$results .= "</tr></thead><tbody>";
		
	foreach($plugins as $name => $p): // Iterate through the array of all plugins and build up a table of results
	
		if( !empty( $name ) ):
		
			$uri = $p['plugin uri'];
			
			$author = $p['author'];
			$author_uri = $p['author uri'];

			if( empty( $author ) ):
				$author_linked = 'N/K';
			elseif( empty( $author_uri ) ):
				$author_linked = "$author";
			else:
				$author_linked = "<a href='$author_uri' target='_blank' title='Author of $name WordPress plugin'  class='author'>$author</a>";
			endif;
			
			$contributors = $p['contributors'];
			
			$results .= "<tr>";
			$results .= "<td>$author_linked</td><td>$contributors</td>";
			
			if( empty( $uri ) ):
				$results .= "<td>$name</td>";
			else:
				$results .= "<td><a href='$uri' target='_blank' title='$name WordPress plugin'  class='name'>$name</a></td>";
			endif;
			
			$results .= "</tr>";
			$plugin_count++;
					
		endif;
	endforeach;
	$results .= "</tbody></table>";
	
	return "<p>" . scolophon_pluralise( $preamble, $plugin_count, $voice ) . "</p>". $results;
}

/******************************************************************************************************************************************/

/***
 * Outer function for the [colophin-themes] shortcode
 *
 * @param	$atts		array of strings		standard shortcode function array may contain values for follow, text, voice
 * 
 * @return 	string								the output of the shortcode
 */
 
function scolophon_themes_shortcode($atts) { 
			
	if( !array_key_exists( 'text', $atts ) ) $atts['text'] = "(My) site is powered by <a href='http://www.wordpress.org' target='_blank'>WordPress</a><sup>&reg;</sup>.  The theme(s) used define(^s) the overall and detailed design of (title). (My) design is the (theme) theme by (author).~~~ This is based upon (theme) by (author)."; // Default the text
				
	return scolophon_master_shortcode( 'themes', $atts);

}

/***
 * Builds the text for the [colophon-themes] shortcode
 *
 * @param	&$plugin_count		integer		BY REF: passed so that the theme count can be returned, @param itself is ignored.
 * @param	$text					string		
 * 
 * @return $string		the output of the shortcode
 */
function scolophon_themes_shortcode_inner( &$theme_count, $text, $voice) {

	$theme_count = 1;

	$theme_vars = silverghyll_versioneer_read_vars( get_stylesheet_directory() . '/style.css', false);
	
	if( !empty( $theme_vars['template'] ) ):
		$parent_theme_vars = silverghyll_versioneer_read_vars( get_template_directory() . '/style.css', false);
	endif;
	
	$main_text = strtok( $text, '~~~' );
	$parent_text = strtok( '~~~' );

	$searches = array( '(theme)', '(author)' );
	$replacements = array( 
									'<a href="' . $theme_vars['theme uri'] . '" target="_blank" class="name">' . $theme_vars['theme name'] . '</a>' ,
									'<a href="' . $theme_vars['author uri'] . '" target="_blank" class="author">' . $theme_vars['author'] . '</a>' );
	$main_text = str_replace( $searches, $replacements, $main_text);
	
	if( $theme_count = 2 ):
		$replacements = array( 
									'<a href="' . $parent_theme_vars['theme uri']. ' target="_blank" class="name">' . $parent_theme_vars['theme name'] . '</a>' ,
									'<a href="' . $parent_theme_vars['author uri']. ' target="_blank" class="author">' . $parent_theme_vars['author'] . '</a>' );
		$parent_text = str_replace( $searches, $replacements, $parent_text);
		return scolophon_pluralise( $main_text . ' '. $parent_text, 2, $voice);
	else:
		return scolophon_pluralise( $main_text, 1, $voice );
	endif;						
}


/******************************************************************************************************************************************/

		
/***
 * Unpacks the attributes of follow and text for the colophin-plugins and colophon-themes shortcodes
 *
 * @param	$type					string					plugin or theme - specifies the stem of the function used to build the shortcode 
 * @param	$default_text		string					default text to put between the heading and the table
 * @param	$atts					array of strings		standard shortcode function array may contain values for follow, text
 * 
 * @return 	string								the output of the shortcode
 */
 
function scolophon_master_shortcode( $type, $atts) { 

 	extract( shortcode_atts( array('text' => "###", 'preamble' => '###', 'follow' => 'yes', 'voice' => '2'), $atts) );
 	$follow = ( substr( strtolower($follow), 0, 2 ) == 'no' ) ? " rel='nofollow'" : "";
 	
	$inner_fn = "scolophon_{$type}_shortcode_inner";
	
	$arguments = array( 'plugins' => 'preamble', 'themes' => 'text', 'fonts' => 'font-text' ); 
	$argument = $atts[ $arguments[ $type ] ];
	$results = $inner_fn($item_count, $argument, $voice ); // Build the meat of the colophon for plugins/themes/fonts

	$results = preg_replace('~http://[^>]+~i','$0' . $follow, $results ); // Add nofollow links if required
	
	switch( $type):  // Needs to be done after inner function call which returns $item_count by ref
	case 'plugins':
		$title = _n( 'Plugin', 'Plugins', $item_count, 'colophon' ); // Headinng for plugins
		break;
	case 'themes':
		$title = _n( 'Theme', 'Themes', $item_count, 'colophon' ); // Heading for themes
		break;
	case 'fonts':
		$title = _n( 'Font', 'Fonts', $item_count, 'colophon' ); // Heading for fonts
		break;
	default:
		$title = __( 'Miscellaneous', 'colophon' ); // Default heading should not be used
	endswitch;
						
	$results = "<div id='colophon-$type' class='colophon'><h3 class='colophon-title'>$title</h3><div class='colophon-results'>$results</div></div>"; // Add a title 
	$results = wp_kses( $results, silverghyll_allowed_html( true ) );	// Sanitise
	
	return apply_filters( "scolophon-$type", $results );  // Apply filter and return
}

/***
 * Pluralises or singularies text marked with () e.g theme(s) may become theme (singular) or themes (plural)
 *
 * $item_count affects (s), (^s), (is), (are) e.g. wrote Theme(s) (are)
 * $voice affects (We), (we), (am) e.g. write (We) (am) pleased
 * (title) always replaced with 
 *
 * @param	$prose				string					the text needing parsing
 * @param	$item_count			integer					if > 1 then nouns and verbs will need to be plural
 * @param	$voice				integer					if 2 then use We, we rather than I
 * 
 * @return 	string								the output of the shortcode
 */
 
	function scolophon_pluralise( $prose, $item_count, $voice ) {
	
		$item_patterns = array('(s)', '(^s)', '(is)', '(are)' );
		$item_replacements = ( $item_count > 1) ? array('s','','are', 'are') : array('', 's', 'is', 'am');
	
		$voice_patterns = array('(we)','(We)','(am)', '(us)', '(me)', '(my)', '(our)', '(My)', '(Our)' );
		$voice_replacements = ( $voice == '2') ? 
				array( 'we', 'We', 'are', 'us', 'us', 'our', 'our', 'Our', 'Our' ) : 
				array( 'I', 'I', 'am', 'me', 'me', 'my', 'my', 'My', 'My');
	
		$patterns = array_merge( $item_patterns, $voice_patterns );
		$replacements = array_merge( $item_replacements, $voice_replacements );
			
		$patterns[] = '(title)';
		$replacements[] = get_bloginfo('name');
			
		return str_replace( $patterns, $replacements, $prose); // Tidy up the preamble
	}


// Determine whether the shortcodes are wanted and then hook them in
// add_shortcode( 'colophon-plugins', 'scolophon_plugins_shortcode');
$silverghyll_plugins = get_silverghyll_plugins(); // Get the details of Silvergyll plugins
if( !empty($tpicker_options['colophon']) or ($silverghyll_plugins['Installed'] === true) ): // $tpicker_options is in global scope if this include called by Taxonomy Picker
	foreach( array('colophon-plugins','colophon-themes','colophon')  as $silverghyll_shortcode_type): // Three possible shortcodes [colophon], [colophon-plugins], [colophon-themes]
		if( !shortcode_exists($silverghyll_shortcode_type) ) add_shortcode($silverghyll_shortcode_type, "s" . str_replace( "-", "_", $silverghyll_shortcode_type ) . "_shortcode");
	endforeach;
endif;	

unset( $silverghyll_plugins, $silverghyll_shortcode_type );

?>