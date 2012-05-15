<?php
if (!class_exists('Cryptojs_util')) :
/**
 * Crypto-JS v2.0.0 conversion to PHP
 * http://moggy.laceous.com/
 * http://wordpress.org/extend/plugins/semisecure-login-reimagined/
 * Works with PHP 4 (>= 4.3) and 5
 *   Note: Rabbit currently requires 64-bit PHP (does not work correctly with 32-bit PHP)
 *   Note: SHA-256 and HMAC-SHA256 require 'hash', 'mhash', suhosin, or 3rd-party support
 *
 * Usage:
 *   $helloBytes  = Cryptojs_Binary::stringToBytes('Hello, World!');
 *   $helloHex    = Cryptojs_util::bytesToHex($helloBytes);
 *   $helloBytes  = Cryptojs_util::hexToBytes($helloHex);
 *   $helloBase64 = Cryptojs_util::bytesToBase64($helloBytes);
 *   $helloBytes  = Cryptojs_util::base64ToBytes($helloBase64);
 *
 * Original JavaScript version:
 *   Crypto-JS v2.0.0
 *   http://code.google.com/p/crypto-js/
 *   Copyright (c) 2009, Jeff Mott. All rights reserved.
 *   http://code.google.com/p/crypto-js/wiki/License
 */
class Cryptojs_util {
	/**
	 * http://www.movable-type.co.uk/scripts/aes-php.html (LGPL)
	 *
	 * Unsigned right shift function, since PHP has neither >>> operator nor unsigned ints
	 *
	 * @param a  number to be shifted (32-bit integer)
	 * @param b  number of bits to shift a to the right (0..31)
	 * @return   a right-shifted and zero-filled by b bits
	 */
	function urs($a, $b) {
		$a &= 0xffffffff; $b &= 0x1f;  // (bounds check)
		if ($a&0x80000000 && $b>0) {   // if left-most bit set
			$a = ($a>>1) & 0x7fffffff;   //   right-shift one bit & clear left-most bit
			$a = $a >> ($b-1);           //   remaining right-shifts
		} else {                       // otherwise
			$a = ($a>>$b);               //   use normal right-shift
		}
		return $a;
	}
	// Other similar functions can be found @
	//  http://pear.php.net/package/Crypt_Xtea/docs/latest/__filesource/fsource_Crypt_Xtea__Crypt_Xtea-1.1.0Xtea.php.html
	//  http://www.phpbuilder.com/board/showthread.php?threadid=10366408
	//  http://www.sitepoint.com/forums/showthread.php?t=449434
	//  http://nanolink.ca/pub/sha256/sha256.inc.txt
	
	/** 
	 * Accept any number of arrays, and append one to the other in order
	 * Doesn't respect key values, just the values themselves
	 */
	function concat() {
		$array = array();
		$args = func_get_args();
		foreach($args as $arg) {
			if (is_array($arg)) {
				foreach($arg as $val)
					$array[] = $val;
			}
		}
		return $array;
	}
	
	function base64map() {
		return 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/';
	}
	
	/**
	 * Bit-wise rotate left
	 */
	function rotl($n, $b) {
		return ($n << $b) | Cryptojs_util::urs($n, (32 - $b));
	}
	
	/**
	 * Bit-wise rotate right
	 */
	function rotr($n, $b) {
		return ($n << (32 - $b)) | Cryptojs_util::urs($n, $b);
	}
	
	/**
	 * Swap big-endian to little-endian and vice versa
	 * (This function probably requires 64-bit PHP, it's only used by Rabbit internally which currently already requires 64-bit PHP)
	 */
	function endian($n) {
		// If number given, swap endian
		if (is_int($n)) {
			return Cryptojs_util::rotl($n, 8) & 0x00FF00FF | Cryptojs_util::rotl($n, 24) & 0xFF00FF00;
		}
		
		//Else, assume array and swap all items
		for ($i = 0; $i < count($n); $i++) {
			$n[$i] = Cryptojs_util::endian($n[$i]);
		}
		return $n;
	}
	
	/**
	 * Generate an array of any length of random bytes
	 */
	function randomBytes($n) {
		for ($bytes = array(); $n > 0; $n--) {
			$bytes[] = mt_rand(0, 255);
		}
		return $bytes;
	}
	
	/**
	 * Convert a byte array to big-endian 32-bits words
	 */
	function bytesToWords($bytes) {
		for ($words = array(), $i = 0, $b = 0; $i < count($bytes); $i++, $b += 8) {
			$words[Cryptojs_util::urs($b, 5)] |= $bytes[$i] << (24 - $b % 32);
		}
		return $words;
	}
	
	/**
	 * Convert big-endian 32-bit words to a byte array
	 */
	function wordsToBytes($words) {
		for ($bytes = array(), $b = 0; $b < count($words) * 32; $b += 8) {
			$bytes[] = Cryptojs_util::urs($words[Cryptojs_util::urs($b, 5)], (24 - $b % 32)) & 0xFF;
		}
		return $bytes;
	}
	
	/**
	 * Convert a byte array to a hex string
	 */
	function bytesToHex($bytes) {
		for ($hex = '', $i = 0; $i < count($bytes); $i++) {
			$temp = dechex($bytes[$i]);
			$hex .= (strlen($temp) % 2 === 0) ? $temp : '0' . $temp;
		}
		return $hex;
	}
	
	/**
	 * Convert a hex string to a byte array
	 */
	function hexToBytes($hex) {
		for ($bytes = array(), $c = 0; $c < strlen($hex); $c += 2) {
			//$bytes[] = intval(substr($hex, $c, 2), 16);
			$bytes[] = hexdec(substr($hex, $c, 2));
		}
		return $bytes;
	}
	
	/**
	 * Convert a byte array to a base-64 string
	 */
	function bytesToBase64($bytes) {
		$base64map = Cryptojs_util::base64map();
		
		for ($base64 = '', $i = 0; $i < count($bytes); $i += 3) {
			$triplet = ($bytes[$i] << 16) | ($bytes[$i + 1] << 8) | $bytes[$i + 2];
			for ($j = 0; $j < 4; $j++) {
				if ($i * 8 + $j * 6 <= count($bytes) * 8)
					$base64 .= $base64map[Cryptojs_util::urs($triplet, 6 * (3 - $j)) & 0x3F];
				else $base64 .= '=';
			}
		}
		
		return $base64;
	}
	
	/**
	 * Convert a base-64 string to a byte array
	 */
	function base64ToBytes($base64) {
		// Remove non-base-64 characters
		$base64 = preg_replace('|[^A-Z0-9+/]|i', '', $base64);		
		$base64map = Cryptojs_util::base64map();
		
		for ($bytes = array(), $i = 0, $imod4 = 0; $i < strlen($base64); $imod4 = ++$i % 4) {
			if ($imod4 == 0) continue;
			$bytes[] = ( ((strpos($base64map, $base64[$i - 1]) & (pow(2, -2 * $imod4 + 8) - 1)) << ($imod4 * 2)) |
			             Cryptojs_util::urs(strpos($base64map, $base64[$i]), (6 - $imod4 * 2)) );
		}
		
		return $bytes;
	}
}
endif;
?>