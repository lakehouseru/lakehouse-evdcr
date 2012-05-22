<?php

/*****
 * This is a foundation for all Silverghyll developments adding versioning, best-library and debugging support
 *
 * Version: 3.4.1
 * Requires: silverghyll-transients.php, silverghyll-common.php
 *
 * NB - Best library support is NOT available for silverghyll-foundation or sikvergyll-transients because they are needed to establish the best library function
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
 * Sets SILVERGHYLL_READY - called when all plugins loaded- set to name of the plugin/theme hosting the silverghyll-foundation.php in use
 *
 * @return N/A
 */
 
function silverghyll_set_ready() {

	$parent_dir = dirname( dirname ( __FILE__ ) );
	$key_file = ( file_exists( $parent_dir . '/readme.txt' ) ) ? $parent_dir . '/readme.txt' : $parent_dir . '/styles.css'; // Readme for plugins; stylesheet for themes
	$key_file_vars = silverghyll_versioneer_read_vars( $key_file); // Parse the markdown
	
	if( array_key_exists( 'plugin name', $key_file_vars ) ):
		$package_name = $key_file_vars['plugin name'];
	elseif( array_key_exists( 'theme name', $key_file_vars ) ):
		$package_name = $key_file_vars['theme name'];
	else:
		$package_name = $key_file_vars[ 'title' ]; // Fallback should never be needed
	endif;
		
	define( 'SILVERGHYLL_READY', $package_name );  
}
add_action( 'plugins_loaded', 'silverghyll_set_ready');


/***
 * Get / set Silverghyll debug status
 *
 * @param 1st  false or '' to turn off; 'echo' or 'true' to default echo stream debugging; 'log' for PHP error log ; simple for echo without extended error handling
 *							or 'trace-echo' or 'trace-log' which turns extended error handling on, plus trace reporting!
 *							or 'squawk' to display with silverghyll_debug_status() is subsequently set on!
 *
 * @return mixed	false as logical or debug stream as string
 */

require_once( 'silverghyll-transients.php'); // Add transients support

function silverghyll_debug_status() {
	if( !defined( KANDIE_THEME_DIR ) ) return; // Ensure it doesn't run onlive systems

	static $silverghyll_debug_status_saved = false;	
	
	if( func_num_args() > 0 ):
		if($silverghyll_debug_status_saved == 'squawk'):
			debug_print_backtrace(); // Use to find where debug_status is being set by setting to squawk' early on
		endif;
		$silverghyll_debug_status_saved = func_get_arg(0);
		
		// Restrict the valid options
		if( ($silverghyll_debug_status_saved != 'echo') 
			and ($silverghyll_debug_status_saved !='log') 
			and ($silverghyll_debug_status_saved !='simple') 
			and ($silverghyll_debug_status_saved !='squawk')
			and ($silverghyll_debug_status_saved !='trace-echo')
			and ($silverghyll_debug_status_saved !='trace-log')
			and ($silverghyll_debug_status_saved !== false) )
				 $silverghyll_debug_status_saved = 'echo' ; // Restrict to backtrace, false, echo or log as only permitted values, defaulting to echo (e.g default true)

		// Turn extended error handling on or off
		if( ($silverghyll_debug_status_saved == 'log') or ($silverghyll_debug_status_saved == 'echo') ):
			require_once( 'silverghyll-debug.php' ); // Load up the debug functions
			set_error_handler("silverghyll_error_handler");  // set_error_handler
		elseif( ($silverghyll_debug_status_saved == 'trace-echo')  or ($silverghyll_debug_status_saved == 'trace-log') ):
			require_once( 'silverghyll-debug.php' ); // Load up the debug functions
			set_error_handler("silverghyll_trace_handler");  // set_error_handler
			$silverghyll_debug_status_saved = substr( $silverghyll_debug_status_saved, 6);
		elseif( ($silverghyll_debug_status_saved == 'echo-trace')  or ($silverghyll_debug_status_saved == 'log-trace') ): 
			require_once( 'silverghyll-debug.php' ); // Load up the debug functions
			set_error_handler("silverghyll_trace_handler");  // set_error_handler
			$i = strpos( $silverghyll_debug_status_saved , '-' ); 
			$silverghyll_debug_status_saved = substr( $silverghyll_debug_status_saved, 0, $i);
		elseif( function_exists('silverghyll_unset_error_handler') and ($silverghyll_debug_status_saved === false) ):
			silverghyll_unset_error_handler(); // Turn off extended error handling
		endif;
					 
	endif;
	
	return $silverghyll_debug_status_saved;
}

/***
 * Prints a neat backtrace - no return
 *
 * @param $stream		String		'echo', 'log' or 'mixed' - as used by silverghyll_debug_log()
 * @param $drop 		Integer		Number of calls to drop (so error handler isn't shown)
 */

function silverghyll_backtrace( $stream = 'echo', $drop = 0) {

	if( !defined( KANDIE_THEME_DIR ) ) return; // Ensure it doesn't run onlive systems

	require_once( 'silverghyll-debug.php' ); // Load up the debug functions
	$styles = "<style type='text/css'>.trace-indent {margin-left:18px}</style>";
	$dropped = false;
	
		
	if( $stream == 'mixed' ) $stream = silverghyll_debug_status();
	if( $stream != 'echo' and $stream != 'log' ) $stream = 'echo'; // Default to echo 

	$backtrace = debug_backtrace();
	
	
	while($drop):
		array_shift($backtrace);
		$drop--;
		$dropped = true;
	endwhile;
	if($dropped):
		$trace = reset($backtrace); 
		$fn_args = "<strong style='color:green;'>Args:<br>$styles<p class='trace-indent'>" . implode("<br/>,", $trace) . "</p></strong>";
	endif;



	if( $stream == 'echo' ):
		echo $fn_args;
		echo "<table><tbody>";
		array_walk( $backtrace, "silverghyll_echo_backtrace" ); // Output the backtrace
		echo "</tbody></table><br/<";
	else:
		array_walk( $backtrace, "silverghyll_log_backtrace" ); // Log the backtrace	
	endif;
}

/***
 * Return the version stored in comments within the specified file
 *
 * $param	$filename	string	(optional) filename to read - defaults to last file read 
 											NB pass "__FILE__" to get version of active silverghyll-foundation.php
 *
 * @return	string	version info (if available), blank otherwise
 */

function silverghyll_versioneer( $filename = '!last!', $comments_only = true ) {  // extracts the version 

	static $last_filename = '';
	static $file_vars = NULL;

	if( ($filename <> $last_filename) and ($filename <> '!last!') ):  // For efficiency, only read a file once if we are using it successively
		$file_vars = silverghyll_versioneer_read_vars($filename, $comments_only);
		$last_filename = $filename;
	endif;
	
	return $file_vars['version'];
}

// Read the vars in a file and return as an array of with the key based on the variable name in the comment 

function silverghyll_versioneer_read_vars($filename, $comments_only = '?'){ 

	if( strtolower($filename) == '__file__' ) $filename = __FILE__; // Allow for interrogation of the active silverghyll-foundation.php by passing '__FILE__' as an argument
	if( $comments_only == '?') $comments_only = ( strtolower( pathinfo($filename, PATHINFO_EXTENSION) ) == 'php' ); // For PHP file default to reading only to the first function

	if(!file_exists($filename)):
		if( function_exists('silverghyll_debug_log') )
			if( function_exists('silverghyll_debug)_log') )
				silverghyll_debug_log("silverghyll_versioneer_read_vars cannot open $filename. \n\r" ); // Report failure to open file when in debug mode
		return; // Nothing to do
	endif;
				
	$lines = file($filename, FILE_SKIP_EMPTY_LINES);
	$line1 = reset( $lines ); // Get the first line
	if( preg_match( '#===([^=]+)===#', $line1, $matches  ) ): // === Title ===?
		$variables['title'] = trim( $matches[1] );
		array_shift( $lines ); // Line processed
	endif;
	
	$comment_block = false;
	foreach($lines as $line):
	
		$line = trim( $line ); // So we can test easily
			
		if( substr( $line, 0, 8) == 'function' ) return $variables; // Assume no comments when we reach our first function
	
		$block_start = (strpos($line, '/*') !== FALSE);
		$comment_block = ( ($comment_block && !$block_end)  || $block_start);
		$block_end = (strpos($line, '*/') !== FALSE);

		if($comment_block or ( strpos($line, '//') !== false ) or !$comments_only ):
			if(preg_match('#(?P<var>[A-Za-z0-9\-_ ]+):\s*?(?P<value>[^\r\n]+)#',$line, $matches)):
				$variables[ trim( strtolower($matches['var'] ) ) ] = trim($matches['value']);
			endif;
		endif;
	endforeach;
	return $variables;	
}

/***
 * Checks that we are using the latest version of silverghyll-foundation.php
 *
 * Because silverghyll-foundation.php and silverghyll-transients.php must be included before we call silverghyll_best_library() we cannot use that function to ensure that the most up to
 * date version of those two files is used as we can with other parts of the library.  They are intended to change infrequently for this reason.  This function us used as a check
 * and (optionally) to display a visual warning if they if silverghyll-foundation.php is out of date.
 *
 * @param	$echo		mixed		true: 	display default warning
 *										string:	display the supplied string, replacing [name] with the name of the problem plugin / theme
 *										false:	no display
 * 
 * @return  Boolean		true if silverghyll-foundation in use is best available, else false
 */
function silverghyll_check_foundation($echo = true) {

	// Check we are using lastest version of silverghyll-foundation.php which is not loaded using best-library
	$best_ver = silverghyll_include_best_library( 'silverghyll-foundation.php', 'version' ); // Best version
	$num_ver = silverghyll_numver( silverghyll_versioneer( '__FILE__') ); // Version in use
	
	if( $best_ver > $num_ver ): // We are not using the most recent silverghyll-foundation.php - check failed
		if( defined( SILVERGHYLL_READY ) ): // If defined we have the name of the host package for silverghyll-foundation.php
			$problem_name = SILVERGHYLL_READY;
		else:
			$parent_dir = dirname( dirname ( __FILE__ ) );
			$key_file = ( file_exists( $parent_dir . '/readme.txt' ) ) ? $parent_dir . '/readme.txt' : $parent_dir . '/styles.css'; // Readme for plugins; stylesheet for themes
			
			$key_file_vars = silverghyll_versioneer_read_vars( $key_file); // Parse the markdown
			$problem_name = $key_file_vars['plugin name'] . $key_file_vars['theme name'];
		endif;
		if( $echo !== false ):
			echo ( is_string( $echo) ) ?
					str_replace( '[name]', $problem_name, $echo) :
					"<p class='silverghyll-warning'><strong>Outdated Silverghyll Foundation Library:</strong>  $problem_name needs updating</p>";
		endif;
		return false;
	else:
		$problem_name = '_N/A_'; // No problem, check passed
		return true;
	endif;
}


/***
 * Return path to latest version available or specified silverghyll file which must reside in silverghyll-library
 *
 * Uses the get_plugin_data to get details of all plugins and returns those attributed to Author = Kate Phizackerley
 * See http://phpdoc.wordpress.org/trunk/WordPress/Administration/_wp-admin---includes---plugin.php.html#functionget_plugin_data
 *
 * @param	$filename		string		name of the file we are looking for e.g. silverghyll-admin.php (default)
 * @param	$path_type		string		d, dir => return a directory (folder) path (default)
 *													u, url => return URL path
 *													v, version -> return the version in silverghyll_number() format
 * 
 * @return full path (dir or URL) to best version of the file we can find
 */
function silverghyll_include_best_library( $filename = 'silverghyll-admin-menu.php', $path_type = 'dir' ) {

	$transient_name = '!SILVER-BEST!' . $filename . '-' . $path_type;
	
	global $silverghyll_transients;	
	if( $silverghyll_transients->get( $transient_name ) ):
		return $silverghyll_transients->get( $transient_name ); // Avoid parsing the files if we can!
	endif;
	
	$path_type = strtolower( trim( $path_type ) );
	$silverghyll_plugins = silverghyll_plugin_library_dirs();
		
	// Test whether we have a theme version to test as well
	$silverghyll_options = get_option( 'kandie-girls-theme' );
	if( $silverghyll_options['theme_name'] == get_current_theme() ) 
			$silverghyll_plugins[ $silverghyll_options['theme_uri'] . 'silverghyll-library/' ] = $silverghyll_options['theme_dir'] . 'silverghyll-library/';

	$max_ver = 0; // The best version found
	$best_path = '';  // The path of the best version found
	$best_date = 0; // The date of the best version found as ymdHi format

	foreach($silverghyll_plugins as $plugin => $path):

		$file_path = $path . $filename;

		if( !file_exists($file_path) ):
			if( function_exists('silverghyll_debug_log') and silverghyll_debug_status() ) 
				silverghyll_debug_log( "Missing library $filename in $path<br/>" ); // If debugging, we need to know
		 	continue; //  Skip any old libraries which don't contain a version of the file we want
		endif;

		$ver = silverghyll_versioneer($file_path);
		if( (!$ver) and	function_exists('silverghyll_debug_log') ) silverghyll_debug_log('No version found in ' . $file_path . ' while finding best library');
		$num_ver = silverghyll_numver( $ver );
		$item_date = date( 'ymdHi', filemtime( $file_path ) );
	
		// Debugging aid - aomment out for live use
/*		
		echo "<h3>File: $filename</h3>";
		echo "<p>Bagged: ver=$max_ver @{$best_date} & path=$best_path</p>";
		echo "<p>Testing: ver=$num_ver @{$item_date} & path=$path<br/></p>"; 
*/		
		// Best is highest version or, if version is identical, the newest modified
		if( ($max_ver < $num_ver) or ( ($max_ver == $num_ver) and ($item_date > $best_date) ) ):
			if( ( $path_type[0] == 'd') ): // Update best_path which may be a folder, URL or version
				$best_path = $path . $filename;
			elseif( ( $path_type[0] == 'v') ):
				$best_path = $num_ver;
			else:
				$best_path = $plugin . $filename;
			endif;
			$best_date = $item_date;
			$max_ver = $num_ver;
		endif;
	endforeach;

	// Store in a transient to avoid iterating when not needed but only once all plugins have loaded
	if( defined( 'SILVERGHYLL_READY' ) ) $silverghyll_transients->set( $transient_name, $best_path );
	
	return $best_path;
}


/***
 * Standardised function for turning a version string into a number for comparison
 *
 *
 * @param $ver	String	version in form 3.12.3a etc
 * @return integer  
 */

function silverghyll_numver( $ver ) {
	$ver=trim($ver); // Make sure it is tidy

	$major_tok = strtok($ver, ".");  // Major release

	$minor_tok = ($major_tok) ? strtok(".") : '';  // Minor release
	$minor_tok_lastchar = substr($minor_tok, -1 , 1); // last character of patch tok
	
	if( ctype_alpha($minor_tok_lastchar) ):
		$minor_tok = substr( $minor_tok, 0, strlen( $minor_tok ) - 1 ); // Remove the letter patch
		$letter_patch = ord( $minor_tok_lastchar ) - 64;
		$patch_tok = 0;
	else:

		// Third element of version
		$patch_tok = ($minor_tok) ? strtok(".") : '';  // Patch release
		$patch_tok_lastchar = substr($patch_tok, -1 , 1); // last character of patch tok
		if( ctype_alpha($patch_tok_lastchar) ):
			$path_tok = substr( $patch_tok, 0, strlen( $patch_tok ) - 1 ); // Remove the letter patch
			$letter_patch = ord( $patch_tok_lastchar ) - 64;
		else:
			$letter_patch = 0;
		endif;

	endif;
	
	$num_ver = 1000000 * $major_tok + 10000 * $minor_tok + 100 * $patch_tok + $letter_patch; // Build into a number
return $num_ver;
}

/***
 * Return array of all silverghyll plugins on the system or available
 *
 * Uses the get_plugin_data to get details of all plugins and returns those attributed to Author = Kate Phizackerley
 * See http://phpdoc.wordpress.org/trunk/WordPress/Administration/_wp-admin---includes---plugin.php.html#functionget_plugin_data
 *
 * @param $name	String	optional - only use when setting information about a plugins directory - the name of the plugin
 * @param $dir	String	optional - only use when setting information about a plugins directory - full folder (basename) of the main plugin file
 * @param $url	String	optional - only use when setting information about a plugins directory - url to the main plugin folder
 *
 *
 * @return array array of plugins details as strings in form used by get_plugins()
 */

function get_silverghyll_plugins($name = '', $dir = '', $url = '') {

	static $silverghyll_plugins, $parsed;

	if( !$parsed and function_exists( 'get_plugins' ) ): // Only parse plugin details once but wait until get_plugins() becomes available - also protects non-WP installs

		// Add some standard text to advertise any plugins which are not installed
		$silverghylls = array( 
			'Colophon' => 'Adds a [colophon] shortcode to build a colophon (an option within other Silverghyll plugins)',  
			'Taxonomy Picker' => 'Interactive search builder widget for your custom taxonomies',  
			'Phiz Feeds' => 'FORTHCOMIMG - Include newsfeeds in your posts and pages by using a flexible shortcode',
			'Egyptological Hieroglyphs' => 'FORTHCOMIMG - Adds a shortcode which displays Egyptian Hieroglyphs by parsing basic Manuel de Codage syntax', 
			'Egyptological New Gardiner Hieroglyphs' => 'FORTHCOMING - Adds a shortcode which displays Egyptian Hieroglyphs based on Dr Mark-Jan Nederhof\'s New Gardiner font' );
		foreach($silverghylls as $name => $description):
			$silverghyll_plugins[ $name ][ 'Description' ] = $description;
			$silverghyll_plugins[ $name ][ 'Name' ] = $name;			
			$silverghyll_plugins[ $name ][ 'Installed' ] = false;			
		endforeach;

		$plugins = get_plugins();

		foreach($plugins as $plugin): 
			$name = $plugin['Name'];
			$author = trim($plugin['Author']);
			if( ( substr( $author, 0 , 17 ) == 'Kate Phizackerley') or (substr( $author, 0 , 11 ) == 'Silverghyll') ):
				$silverghyll_plugins[ $name ] = array_merge( (array)$silverghyll_plugins[ $name ] , $plugin ); // Store the plugin detail
				$silverghyll_plugins[ $name ][ 'Installed' ] = true;			
			endif;
		endforeach;
		$parsed = true; // Flag we have built the array
	endif;
	
	if( func_num_args() > 1 ):
		$silverghyll_plugins[ $name ][ 'dir' ] = $dir; // Add info on the dir into our array.
		$silverghyll_plugins[ $name ][ 'url' ] = $url; // Add info on the url into our array.
	endif;
		
	return $silverghyll_plugins;

}

/***
 * Return array of all paths to silverghyll plugin library on the system
 *
 * @return array  string 	key => URL to library stylesheet => full DIR path to silverghyll-library with trailing slash
 */


function silverghyll_plugin_library_dirs() {

	static $installed_plugins; //Expensive in time so only run once
	if( !empty($installed_plugins) ) return $installed_plugins;
	
	$folder = WP_PLUGIN_DIR .'/';
	foreach (new DirectoryIterator($folder) as $file):
   		if ( (!$file->isDot()) && ($file->getFilename() != basename($_SERVER['PHP_SELF'])) ):
      		if($file->isDir()):
      			if( file_exists( $folder . $file->getFilename() . "/silverghyll-library/silverghyll-admin-menu.php" ) ):
      				$installed_plugins[ trailingslashit(plugins_url()). $file->getFilename() . "/silverghyll-library/" ] = $folder . $file->getFilename() . "/silverghyll-library/";
     			endif;
      		endif;
      	endif;
    endforeach;		
	
	return $installed_plugins;

}


?>