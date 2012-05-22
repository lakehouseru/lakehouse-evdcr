<?php

/**** 
 * 
 * Silverghyll transients class is used to combine our common, semi-permanent transients to reduce database calls by getting them just once in each session
 *
 * Version: 3.1
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

 
 
 
 
global $silverghyll_transients; 
$silverghyll_transients = new silverghyll_transient_class();
	 
class silverghyll_transient_class {

	private $transients;  // $this is what we are storing
	private $duration; // How long we will save transients for
	
	public function __construct() {
		$this->duration = 60 * 60 * 24;  // Default to one day
		if( is_admin() ) $this->flush(); else $this->transients = get_transient( 'silverghyll-transients' ); // Flush in admin() 			
	}
	
	// Return time transients were last saved in time() format
	public function timestamp() {
		return $this->transients['timestamp'];
	}
	
	// Clear all transients and reset timestamp
	public function flush() {
		unset( $this->transients );
		$this->set( 'timestamp', time() );
	}
	
	// Get a transient
	public function get($name) {
		if( is_array( $this->transients ) and array_key_exists( $name, $this->transients ) ) return $this->transients[$name]; else return null;
	}
	
	// Set a transient
	public function set($name, $value) {
		$this->transients[$name] = $value;
		set_transient( 'silverghyll-transients', $this->transients, $this->duration );		
		return $value;
	}
	
	// Magic to string - return the transients array
	public function __toString() {
		return var_export( $this->transients , true );
    }
	
} // End of class definition
	

?>