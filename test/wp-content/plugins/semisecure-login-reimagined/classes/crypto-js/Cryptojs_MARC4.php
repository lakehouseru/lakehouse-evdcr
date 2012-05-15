<?php
if (!class_exists('Cryptojs_MARC4')) :
/**
 * Crypto-JS v2.0.0 conversion to PHP
 * http://moggy.laceous.com/
 * http://wordpress.org/extend/plugins/semisecure-login-reimagined/
 * Works with PHP 4 (>= 4.3) and 5
 *   Note: Rabbit currently requires 64-bit PHP (does not work correctly with 32-bit PHP)
 *   Note: SHA-256 and HMAC-SHA256 require 'hash', 'mhash', suhosin, or 3rd-party support
 *
 * Usage:
 *   $crypted = Cryptojs_MARC4::encrypt('message', 'secret passphrase');
 *   $plain   = Cryptojs_MARC4::decrypt($crypted,  'secret passphrase');
 *
 * Original JavaScript version:
 *   Crypto-JS v2.0.0
 *   http://code.google.com/p/crypto-js/
 *   Copyright (c) 2009, Jeff Mott. All rights reserved.
 *   http://code.google.com/p/crypto-js/wiki/License
 */
class Cryptojs_MARC4 {

	function encrypt($message, $password) {
		// Convert to bytes
		$m = Cryptojs_UTF8::stringToBytes($message);
		
		// Generate random IV
		$iv = Cryptojs_util::randomBytes(16);
		
		// Generate key
		$k = (!is_array($password)) ?
		     // Derive key from passphrase
		     Cryptojs::PBKDF2($password, $iv, 32, array('asBytes' => true)) :
		     // else, assume byte array representing cryptographic key
		     $password;
		
		// Encrypt
		Cryptojs_MARC4::_marc4(&$m, $k, 1536);
		
		// Return ciphertext
		return Cryptojs_util::bytesToBase64(Cryptojs_util::concat($iv, $m));
	}
	
	function decrypt($ciphertext, $password) {
		// Convert to bytes
		$c = Cryptojs_util::base64ToBytes($ciphertext);
		
		// Separate IV and message
		$iv = array_splice(&$c, 0, 16);
		
		// Generate key
		$k = (!is_array($password)) ?
		     // Derive key from passphrase
		     Cryptojs::PBKDF2($password, $iv, 32, array('asBytes' => true)) :
		     // else, assume byte array representing cryptographic key
		     $password;
		
		// Decrypt
		Cryptojs_MARC4::_marc4(&$c, $k, 1536);
		
		// Return plaintext
		return Cryptojs_UTF8::bytesToString($c);
	}
	
	function _marc4(&$m, $k, $drop) {
		// State variables
		$i; $j; $s; $temp;
		
		// Key Setup
		for ($i = 0, $s = array(); $i < 256; $i++) $s[$i] = $i;
		for ($i = 0, $j = 0; $i < 256; $i++) {
			$j = ($j + $s[$i] + $k[$i % count($k)]) % 256;
			
			// Swap
			$temp = $s[$i];
			$s[$i] = $s[$j];
			$s[$j] = $temp;
		}
		
		// Clear counters
		$i = $j = 0;
		
		// Encryption
		for ($k = 0 - $drop; $k < count($m); $k++) {
			$i = ($i + 1) % 256;
			$j = ($j + $s[$i]) % 256;
			
			// Swap
			$temp = $s[$i];
			$s[$i] = $s[$j];
			$s[$j] = $temp;
			
			// Stop here if we're still dropping keystream
			if ($k < 0) continue;
			
			// Encrypt
			$m[$k] ^= $s[($s[$i] + $s[$j]) % 256];
		}
	}
}
endif;
?>