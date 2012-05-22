<?php
// Silverghyll Debug functions should never be loaded in a live environment
// Version: 3.0

/***
 * Debug function which logs error messages
 *
 * @param message				string	Text to log / echo
 * @param stream				string	'echo'  or 'log' (for PHP error log) or 'mixed' (default) which defaults to echo if silverghyll_debug = true, 'log' otherwise
 *										of 'simple' which is echo without extended error handling
 * @param temp_debug_status		logic	if set to true, forces debug message to appear even if debug set off
 *
 * @return 	none
 */
 
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

 
function silverghyll_debug_log($message, $stream = 'mixed', $temp_debug_status = false) {  // Can be turned on by either WP_DEBUG or by using silverghyll_deug( true )

	$stream = strtolower($stream);
	if($stream == 'mixed' ) $stream = silverghyll_debug_status();  // Variable default

	if( ( silverghyll_debug_status() === false ) and !$temp_debug_status ) return;  // Break out if no debug option is set on
	
	if( is_array($message) || is_object($message)) $message = print_r( $message, true ); // Convert to something printable
    if( headers_sent() and ($stream == 'echo' or $stream == 'backtrace'  or $stream == 'simple') )
    			echo $message ; else error_log($message); // Print or log it as demanded
}

/***
 * Extended error handling to making tracing errors quicker
 *
 */

function silverghyll_error_handler($errno, $errstr, $errfile, $errline ) {
	silverghyll_error_trace_handler($errno, $errstr, $errfile, $errline, false );  // Without backtrace
}

function silverghyll_trace_handler($errno, $errstr, $errfile, $errline ) {
	silverghyll_error_trace_handler($errno, $errstr, $errfile, $errline, true ); // With backtrce
}

function silverghyll_error_trace_handler($errno, $errstr, $errfile, $errline, $trace = false ) {

	if(!(error_reporting() & $errno)) return;  // This error code is not included in error_reporting

	$tidy_errfile = basename( dirname ( $errfile ) ) . '/'. basename( $errfile ); // Just the nice trailing bit!

    switch ($errno) {
    case E_USER_ERROR:
    	silverghyll_debug_log("<b>silverghyll PHP ERROR</b> [$errno] $errstr<br />\n");
        silverghyll_debug_log("  Fatal error on line $errline in file $tidy_errfile \n");
        if($trace) silverghyll_backtrace( silverghyll_debug_status(), 3 );
        silverghyll_debug_log("Aborting...<br />\n");
        exit(1);
        break;

    case E_USER_WARNING:
        silverghyll_debug_log("<b>silverghyll PHP WARNING</b> [$errno] $errstr on line $errline in file $tidy_errfile <br />\n");
        break;

    case E_USER_NOTICE:
        silverghyll_debug_log("<b>silverghyll NOTICE</b> [$errno] $errstr<br />\n");
        break;

    default:
        silverghyll_debug_log("silverghyll unknown error type: [$errno] $errstr on line $errline in file $tidy_errfile<br />\n");
        break;
    }

    if($trace) silverghyll_backtrace( silverghyll_debug_status() , 3 );

    /* Don't execute PHP internal error handler */    
    return true;
}

// Unwind the error handler stack until we're back at the built-in error handler.
function silverghyll_unset_error_handler()
{
    while (set_error_handler(create_function('$errno,$errstr', 'return false;'))) {
        // Unset the error handler we just set.
        restore_error_handler();
        // Unset the previous error handler.
        restore_error_handler();
    }
    // Restore the built-in error handler.
    restore_error_handler();
}


function silverghyll_echo_backtrace($item, $key){
    $func = $item['function'];
    $line = $item['line'];
    $file = $item['file'];
    $tidy = trim(basename(dirname($file)) . '/' . basename($file));
    if($tidy == '/') $tidy = ''; else $tidy = " in <b style='color:blue;'>" . $tidy."[".$line."]</b>";
    
	echo  "<tr>&nbsp;<td><b style='color:red'>$func</b></td><td>{$tidy}<br/></tr>";
}

function silverghyll_log_backtrace($item, $key){
    $func = $item['function'];
    $line = $item['line'];
    $file = $item['file'];
    $tidy = trim(basename(dirname($file)) . '/' . basename($file));
    if($tidy == '/') $tidy = ''; else $tidy = $tidy."[".$line."]";
    
	silverghyll_debug_log( "$tidy - $func", "log");
}


?>