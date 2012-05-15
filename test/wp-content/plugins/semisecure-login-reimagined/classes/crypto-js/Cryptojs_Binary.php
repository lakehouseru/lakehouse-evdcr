<?php
if (!class_exists('Cryptojs_Binary')) :
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
 *   $helloString = Cryptojs_Binary::bytesToString($helloBytes);
 *
 * Original JavaScript version:
 *   Crypto-JS v2.0.0
 *   http://code.google.com/p/crypto-js/
 *   Copyright (c) 2009, Jeff Mott. All rights reserved.
 *   http://code.google.com/p/crypto-js/wiki/License
 */
class Cryptojs_Binary {
	// These functions mimic the way JavaScript's charCodeAt and fromCharCode work
	// In PHP 6 we'll probably be able to use chr() and ord() directly

	// Convert a string to a byte array
	function stringToBytes($string) {
		$bytes = array();
		while (strlen($string) > 0) {
			// This would most likely need to be modified for PHP 6
			$bytes[] = Cryptojs_Binary::_ordUTF8($string, 0, &$bytes_used);
			$string = substr($string, $bytes_used);
		}
		return $bytes;
	}
	
	// Convert a byte array to a string
	function bytesToString($bytes) {
		for ($string = '', $i = 0; $i < count($bytes); $i++)
			$string .= Cryptojs_Binary::_unichr($bytes[$i]);
		return $string;
	}
	
	// taken from: http://us.php.net/manual/en/function.chr.php
	function _unichr($c) {
		if ($c <= 0x7F) {
			return chr($c);
		} else if ($c <= 0x7FF) {
			return chr(0xC0 | $c >> 6) . chr(0x80 | $c & 0x3F);
		} else if ($c <= 0xFFFF) {
			return chr(0xE0 | $c >> 12) . chr(0x80 | $c >> 6 & 0x3F)
			                            . chr(0x80 | $c & 0x3F);
		} else if ($c <= 0x10FFFF) {
			return chr(0xF0 | $c >> 18) . chr(0x80 | $c >> 12 & 0x3F)
			                            . chr(0x80 | $c >> 6 & 0x3F)
			                            . chr(0x80 | $c & 0x3F);
		} else {
			return false;
		}
	}
	
	// taken from: http://us.php.net/manual/en/function.ord.php
	function _ordUTF8($c, $index = 0, &$bytes = null) {
		$len = strlen($c);
		$bytes = 0;

		if ($index >= $len)
			return false;

		$h = ord($c[$index]);

		if ($h <= 0x7F) {
			$bytes = 1;
			return $h;
		}
		else if ($h < 0xC2)
			return false;
		else if ($h <= 0xDF && $index < $len - 1) {
			$bytes = 2;
			return ($h & 0x1F) <<  6 | (ord($c[$index + 1]) & 0x3F);
		}
		else if ($h <= 0xEF && $index < $len - 2) {
			$bytes = 3;
			return ($h & 0x0F) << 12 | (ord($c[$index + 1]) & 0x3F) << 6
			                         | (ord($c[$index + 2]) & 0x3F);
		}
		else if ($h <= 0xF4 && $index < $len - 3) {
			$bytes = 4;
			return ($h & 0x0F) << 18 | (ord($c[$index + 1]) & 0x3F) << 12
			                         | (ord($c[$index + 2]) & 0x3F) << 6
			                         | (ord($c[$index + 3]) & 0x3F);
		}
		else
			return false;
	}
}
endif;
?>