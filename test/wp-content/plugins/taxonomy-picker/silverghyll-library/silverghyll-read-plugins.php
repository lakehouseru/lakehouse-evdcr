<?php

/**** 
 * 
 * Routines for reading WordPress readme files
 *
 * Version: 1.1.2
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
 * Reads and parses a standard WordPress plugin readme.txt.  Returns as an array.  Does not parse the markdown - wrap with an appropriate function if needed
 *
 * @param 	$filename		String									The filename to be read
 * @param	$title			String	 				BY REF		Value passed ignored.  Returns the title of the plugin.		
 * @param	$params			Arrays of String	 	BY REF		Value passed ignored.  Returns array key=>name data=>value of the params like Author: at the top of file - without colon		
 *
 * @return	array		Array of sections in the file, key is section name, contents are a) string of the text in that section or b) a nested array of same structiure for sub-sections
 */

 
 function silverghyll_read_readme( $filename, &$title = '', &$params = '' ) {
 
 	$file = nl2br( wp_kses_post( file_get_contents( $filename ) ) ); // Open the file as a string
 	
 	$matches = preg_split ( '/\s*==+([^=<]+)==+\s*(?:<br>)*/' , $file, -1, PREG_SPLIT_DELIM_CAPTURE ); // Sections are delimined with == Text ==
 	 	
 	$contents = array();
 	$contents['title'] = $title = trim( $matches[1] );
 	
 	array_shift( $matches ); // Get rid of blank element before title (usually only the file start character match)
 	
 	while( !empty( $matches ) ): // Loop through sections
 		$key = trim( array_shift( $matches ) ); // Output alternates key = section title ...
 		$data = ( !empty( $matches ) ) ? array_shift( $matches ) : ''; /// ... and date = section contents
 		
 		if( $key == $title ): // The first section
			
			preg_match_all( '#(.*?)<br\s*>?#', $data, $lines );
			foreach( $lines[1] as $line ): // Loop through the line (first matching patter in the regex)
				$i = strpos( $line, ':' );
				if( $i !== false):
					$var_name = trim( strtolower( substr ($line, 0, $i ) ) );
					$var_value = trim( substr( $line, $i + 1 ) );
					$contents[ $var_name ] = $var_value ;
				endif;
			endforeach;
			

 		endif;
 		
 		$inner_matches = preg_split ( '/\s*=([^=<]+)=\s*(?:<br>)*/' , $data, -1, PREG_SPLIT_DELIM_CAPTURE ); // Try to break it into sub-sections = some = etc
 		
		if( count( $inner_matches ) <= 1 ): // No sub-sections
 			$contents[ $key ] = $data;
 		else:
 		
 			$sub_sections[$key] = array_shift( $inner_matches );
 		
 			// Loop through sub-sections
 			while( !empty( $inner_matches ) ):
		  		$key2 = trim( array_shift( $inner_matches ) );
		 		$data = ( !empty( $$inner_matches ) ) ? array_shift( $inner_matches ) : '';
				$sub_sections[ $key2 ] = $data;					
 			endwhile;	
			
 			$contents[ $key ] = $sub_sections; // Insert as a sub-array
 			
 		endif;
 	endwhile;
 	
 	return $contents;
 }
 
// Front end function which returns and array of selected items from plugins readme.txt as an array in the specified folder

function silverghyll_read_plugins($folder) {
	$folder = trailingslashit( $folder );
	if( !is_dir($folder) ) return null; // Cannot deal with something which is not a folder

	foreach(new DirectoryIterator($folder) as $file):
		if ( (!$file->isDot()) && ($file->getFilename() != basename($_SERVER['PHP_SELF'])) ):
   		if($file->isDir()):
   			$readme = $folder . $file->getFilename() . "/readme.txt";
   			if( file_exists( $readme ) ):
   			
   				$title = '';
   				$plugin_vars = silverghyll_read_readme( $readme, $title );
   			
   				$installed_plugins[ $title ] = Array();
   				foreach( array('author','author uri', 'contributors', 'contributors uri', 'plugin name', 'plugin uri', 'tags', 'title', 'version') as $item ):
      				$installed_plugins[ $title ][$item] = $plugin_vars[$item];
					endforeach;

     			endif;
   		endif;
   	endif;
   endforeach;
	return $installed_plugins;
}
?>