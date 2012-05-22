<?php

/**** 
 * 
 * Routine for removing Silverghyll from the database when last Silvergyll library is removed from an install
 *
 * Version: 2.0
 *
 * Requires: N/A
 *
 ************************************************************
 *
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 *
 * Copyright Kate Phizackerley 2011, 2012
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

function silvergyll_uninstall_plugin($database_option) {
	if( !defined('WP_UNINSTALL_PLUGIN') ):
		exit();  //Ensure only run when WP requests an uninstall
	else:
		switch($database_option):
		case 'silverghyll': // We only delete this when the last Silverghyll item is uninstalled
		
			$plugin_count = count( silverghyll_plugin_library_dirs() ); // Count the number of installed Silverghyll plugins
	
			if( (count( $plugin_count ) <= 1) and !defined( KANDIE_THEME_DIR ) ):  // Only uninstall Colophon if this is the lasy Silverghyll item
			 	delete_option('silverghyll'); // Delete the options stored in the database.  
			 	if( get_option( 'silverghyll' ) ): 
			 		delete_option('silverghyll'); 
			 	endif;		 	
			endif;
			return;
					
		endswitch;
	endif;
}

?>