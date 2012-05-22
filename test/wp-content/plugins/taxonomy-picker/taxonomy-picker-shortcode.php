<?php

/**** 
 * 
 * Adds shortcode support to the Silverghyll Library eg [colophon]
 *
 * Version: 1.1
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
 * Returns the version of the Taxonomy Picker plugin iin use
 *
 * @param	$atts		array of strings		standard shortcode function array may contain values for follow, voice, text and preamble
 * 
 * @return 	string								the version of the Taxonomy Picker plugin in use (as text)
 */
 
add_shortcode( 'tpickerversion', 'tpicker_shortcode_version') ;
function tpicker_shortcode_version($atts) { 

	include_once( silverghyll_include_best_library('silverghyll-read-plugins.php') );
	$tpicker_options = get_option('taxonomy-picker-options');
	$plugin = silverghyll_read_readme( TPICKER_DIR . 'readme.txt' ); 

	$version = $plugin['version']; 
	return $version .=  (array_key_exists( 'premium-widget', $tpicker_options) ) ? ' (Premium)' : '';
}



add_shortcode( 'tpicker', 'taxonomy_picker_shortcode') ;

function taxonomy_picker_shortcode($atts, $content = ''){ 
		
	extract(shortcode_atts(array("taxonomies" => ""), $atts));
	
	$instance['taxonomies'] = implode( ',', $taxonomies );
	
}
?>