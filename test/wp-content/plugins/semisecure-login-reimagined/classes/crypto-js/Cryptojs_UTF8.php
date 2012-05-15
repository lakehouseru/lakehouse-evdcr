<?php
if (!class_exists('Cryptojs_UTF8')) :
/**
 * Crypto-JS v2.0.0 conversion to PHP
 * http://moggy.laceous.com/
 * http://wordpress.org/extend/plugins/semisecure-login-reimagined/
 * Works with PHP 4 (>= 4.3) and 5
 *   Note: Rabbit currently requires 64-bit PHP (does not work correctly with 32-bit PHP)
 *   Note: SHA-256 and HMAC-SHA256 require 'hash', 'mhash', suhosin, or 3rd-party support
 *
 * Usage:
 *   $utf8Bytes     = Cryptojs_UTF8::stringToBytes('България');
 *   $unicodeString = Cryptojs_UTF8::bytesToString($utf8Bytes);
 *
 * Original JavaScript version:
 *   Crypto-JS v2.0.0
 *   http://code.google.com/p/crypto-js/
 *   Copyright (c) 2009, Jeff Mott. All rights reserved.
 *   http://code.google.com/p/crypto-js/wiki/License
 */
class Cryptojs_UTF8 {
	// Convert a string to a byte array
	function stringToBytes($string) {
		$bytes = array();
		
		// This relies on PHP 4+5 inability to deal with multi-byte characters
		//for ($i = 0; $i < strlen($string); $i++) {
		//	$bytes[] = ord($string[$i]);
		//}
		
		// this should _hopefully_ also work in PHP 6
		$encoded = rawurlencode($string);
		while (strlen($encoded) > 0) {
			if ($encoded[0] == '%') {
				$bytes[] = hexdec(substr($encoded, 1, 2));
				$encoded = substr($encoded, 3);
			}
			else {
				$bytes[] = ord($encoded[0]);
				$encoded = substr($encoded, 1);
			}
		}
		
		return $bytes;
	}
	
	// Convert a byte array to a string
	function bytesToString($bytes) {
		$string = '';
		foreach ($bytes as $byte) {
			$temp = dechex($byte);
			if (strlen($temp) == 1) $temp = '0'.$temp;
			$string .= '%'.$temp;
		}
		return rawurldecode($string);
	}
}
endif;
?>