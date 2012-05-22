<?php

// Standard module to initialise the Silverghyll Menu on the dashboard of nothing else has done it.  Copy to all plugins

// Version: 3.4

/*
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


add_action( 'admin_menu', 'silverghyll_girls_top_menu', 10);  // Add silverghyll admin menu support
add_action( 'admin_init', 'silverghyll_girls_admin_menu_init' );  // Initialise silverghyll admin menu

/* Add silverghyll Girls top menu */
function silverghyll_girls_top_menu() {
	$page = add_menu_page('Silverghyll', 'Silverghyll', 'administrator',  basename(__FILE__), 'silverghyll_create_admin_menu'  );
	add_action( 'admin_print_styles-' . $page, 'silverghyll_admin_styles' );
}

/* Register our stylesheet. */
function silverghyll_girls_admin_menu_init() { 
	
	// Find the best silverghyll admin stylesheet (highest version)
	$silverghyll_admin_stylesheet_path = silverghyll_include_best_library('silverghyll-admin-style.css'); 
	$silverghyll_admin_stylesheet_url  = silverghyll_include_best_library('silverghyll-admin-style.css','url');
	if( $silverghyll_admin_stylesheet_path == '' and function_exists('silverghyll_debug_log') )
		silverghyll_debug_log('Failed to locate silverghyll-admin-style.css<b/>');  // In debug mode, report failure, otherwise silent

	// Register it, adding in filedate as a modifier
	$last_modified = date( 'ymdHi', filemtime( $silverghyll_admin_stylesheet_path ) );
	wp_register_style( 'silverghyll_admin_stylesheet', $silverghyll_admin_stylesheet_url, false, $last_modified);
}
/* Enque stylesheet */
function silverghyll_admin_styles() { // It will be called only on your plugin admin page, enqueue our stylesheet here
	wp_enqueue_style( 'silverghyll_admin_stylesheet');
}

function silverghyll_create_admin_menu(){
	
	$silverghyll_plugins = get_silverghyll_plugins();
	ksort($silverghyll_plugins);
		
	$plugin_data = get_plugin_data( plugin_dir_path(__FILE__) . '../readme.txt' );  // Path to this plugins readme 
	$plugin_name = $plugin_data['Name'];
	if(!$plugin_name) $plugin_name =  'Kandie Girls'; // If not a plugin, then it is the Kandie Girls theme!
	
	silverghyll_check_foundation( true ); // Check silvergyll-foundation.php is up to date and display a warning if not
	
	?>
	<h3>Silverghyll Packages</h3>
	<p>This sub-menu is used for packages (plugins and themes) from <a href="http://www.silverghyll.com" target="_blank">Silverghyll</a></p>
	<table id="silverghyll-inventory" class="widefat">
	<thead>
		<tr>
			<td>Package</td>
			<td>Type</td>
			<td>Installed?</td>
			<td>Description</td>
		</tr>
	</thead>
	<tbody>
	
	<?php foreach( $silverghyll_plugins as $kp ): ?>
		<tr>
			<?php if( isset($kp['PluginURI']) ): ?>
				<td><a href="<?php echo $kp['PluginURI'];?>" title="<?php echo $kp['Name'];?>"><?php echo $kp['Name'];?></a></td>
			<?php else: ?>
				<td><?php echo $kp['Name'];?></td>
			<?php endif; ?>			
			<td>Plugin</td>
			<td>
				<?php 
				$item_installed = ($kp['Installed'] === true )  ? 'Yes' : 'No';
				_e($item_installed); 
				if($item_installed == 'Yes') echo ' (v' . $kp['Version'] . ')';
				?>
			</td>
			<td><?php echo $kp['Description'];?></td>
		</tr>
	<?php endforeach; ?> 

	<?php
		if( defined('KANDIE_THEME_DIR') ):
			echo "<tr><td>Kandie Girls</td><td>Theme</td><td>Yes (v". silverghyll_versioneer( trailingslashit( silverghyll_THEME_DIR) . 'style.css' ); 
			echo ")</td><td>Kandie Girls theme developed for Egyptological</td></tr>";
		endif; 
		?>
	</tbody>
	</table><br style="clear:both;">
	
	Packages &copy; Kate Phizackerley and/or Silverghyll 2009-2012.  Please refer to each package for copyright and licence details. <br/>
	
	<?php 
	silverghyll_debug_status('echo-trace');
	echo "<br/><h3>Silverghyll Library Versions and Paths</h3><style type='text/css'>.widefat thead td {font-weight:bold;font-size;120%;}</style>";
	echo "<table class='widefat'><thead><b><tr><td>Library Item</td><td>Path</td><td>Version</td><td>Date</td></tr></b></thead><tbody>";
	$lib_contents = silverghyll_admin_library_versions();
	foreach($lib_contents as $item => $path):
		$tidy_path = dirname( str_replace( $_SERVER['DOCUMENT_ROOT'], '', $path ) ); // Strip out the leading stuff and the item name
		echo "<tr><td>$item</td><td>$tidy_path</td><td>" . silverghyll_versioneer($path)."</td><td>".date( 'd/m/Y', filemtime( $path ) )."</td></tr>";
	endforeach;
	$gd_inf = gd_info();
	$gd_ver = " &amp; GD {$gd_inf['GD Version']}" . ( ($gd_inf['Freetype Support']) ? " with Freetype support" : "" );
	echo "</tbody></table><br/><p>Printed by $plugin_name using PHP v" . phpversion() .
			"{$gd_ver} with Silverghyll Foundation Library from " . SILVERGHYLL_READY ."</p>";
		
}

/**
 * Return list of the best version of all files in the silverghyll Admin library, with path
 * 
 * @return array key => string, DIR of file 	data => version
 */

function silverghyll_admin_library_versions() {
	$lib = trailingslashit( dirname(__FILE__) );
	foreach (new DirectoryIterator($lib) as $file):
   		if ( (!$file->isDot()) && ($file->getFilename() != basename($_SERVER['PHP_SELF'])) ):
      		if( !($file->isDir()) and ( ($fname = $file->getFilename()) != 'bare.php' ) ):
      			$library_versions[$fname] = silverghyll_include_best_library($fname, 'dir');
      		endif;
      	endif;
    endforeach;
	return $library_versions;
}

/**
 * Settings API support function - display a checkbox
 * 
 * @param 	$option_name	string	Name of option in the database
 * @param	$item_name		string	Name of the item within the $option array
 */

function silverghyll_admin_checkbox($option_name, $item_name) {
	$options = get_option( $option_name );	
	if( isset($options[$item_name])) { $checked = ' checked="checked" '; } else $checked = '';
	echo "<input ".$checked." id='$item_name' name='{$option_name}[{$item_name}]' type='checkbox' />";
}

/**
 * Settings API support function - display a combobox
 * 
 * @param 	$option_name	string		Name of option in the database
 * @param	$item_name		string		Name of the item within the $option array
 * @param	$valid			mixed		Array of strings of the valid items, or a comma separated list of items to arrayify
 *												use id=>Description if you need nice labels not in the database
 */

function silverghyll_admin_combobox($option_name, $item_name, $valid) {
	$options = get_option( $option_name );	//Read in the options		
	echo "<select id='$item_name' name='{$option_name}[{$item_name}]'/>";
	if( is_string($valid) ) $valid = explode(',',$valid); // Arrayify
	foreach($valid as $item):
		$i = strpos($item, "=>" );
		if( $i ):
			$nice_label = trim( substr( $item, $i+2) );
			$item = trim( substr( $item, 0, $i) );
		else:
			$nice_label = $item;
		endif; 
		
		$selected = ($options[$item_name]==$item) ? 'selected="selected"' : '';
		echo "<option value='$item' $selected>$nice_label</option>";
	endforeach;
	echo "</select>";
}


/**
 * Settings API support function - display a textbox
 * 
 * @param 	$option_name	string		Name of option in the database
 * @param	$item_name		string		Name of the item within the $option array
 * @param	$valid			mixed		Array of strings of the valid items, or a comma separated list of items to arrayify
 */


function silverghyll_admin_textbox( $option_name, $item_name, $size = 40 ) {
	$options = get_option($option_name);
	$value = $options[$item_name];
	echo "<input id='$item_name' name='{$option_name}[{$item_name}]' size='$size' type='text' value='{$value}' />";
}


/*** 
 * Echo  or return Combo box of categories OR taxonomies
 *
 * @param	Mixed	 WP format array or string of arguments:
 *						first = String - additonal text item to show at top of list, optional, default = don't display anything (null)
 *						echo = Boolean - if TRUE (default), echo the combobox
 *						option_name = sting, name of option in database (required)
 *						item_name = sting, name of item within the optionname, optional, default = 'category'
 *						... plus any arguments take by get_categories
 *
 */

function silverghyll_category_dropdown( $args = null ) {
	$defaults = array('first' => null, 'echo' => true, 'option_name' => '', 'item_name' => 'category' );
	$r = wp_parse_args( $args, $defaults );
	
	// Split out our arguments
	foreach( $defaults as $key => $value):
		$$key = $r[$key]; // Set our variable
		unset( $r[$key] );
	endforeach;
	
	if( is_string( $first) ) $valid = $first;
	$categories = get_categories( $r ); // Get the categories specified as an array of objects
	
	// Now turn the categories into a comma separated list of names
	foreach( $categories as $cat ):
		$valid .= ',' . $cat->category_nicename;
	endforeach;
	if( $valid[0] == ',' ) $valid = substr($valid, 1);
	
	$result = silverghyll_admin_combobox( $option_name, $item_name, $valid, false );
	if( $echo ) echo $result; else return $result;
}

/**
 * Return script for auto opening of admin help text - just echo somewhere 
 *
 * @return String	the script
 */

function silverghyll_auto_open_help() {
	return "<script type='text/javascript'>jQuery(document).bind('ready',function() {jQuery('a#contextual-help-link').trigger('click');});</script>";
}

function silverghyll_nothing() {};

?>