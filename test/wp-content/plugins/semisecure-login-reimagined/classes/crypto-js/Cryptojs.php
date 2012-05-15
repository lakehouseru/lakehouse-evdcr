<?php
if (!class_exists('Cryptojs')) :
/**
 * Crypto-JS v2.0.0 conversion to PHP
 * http://moggy.laceous.com/
 * http://wordpress.org/extend/plugins/semisecure-login-reimagined/
 * Works with PHP 4 (>= 4.3) and 5
 *   Note: Rabbit currently requires 64-bit PHP (does not work correctly with 32-bit PHP)
 *   Note: SHA-256 and HMAC-SHA256 require 'hash', 'mhash', suhosin, or 3rd-party support
 *
 * Usage:
 *   $digestBytes = Cryptojs::MD5('message', array('asBytes' => true));
 *   $digestBytes = Cryptojs::SHA1('message', array('asBytes' => true));
 *   $digestBytes = Crytpojs::SHA256('message', array('asBytes' => true));
 *   $digestBytes = Cryptojs::HMAC('md5', 'message', 'key', array('asBytes' => true));
 *   $key512bit1000 = Cryptojs::PBKDF2('secret passphrase', Cryptojs_util::randomBytes(16), 64, array('iterations' => 1000));
 *
 * Original JavaScript version:
 *   Crypto-JS v2.0.0
 *   http://code.google.com/p/crypto-js/
 *   Copyright (c) 2009, Jeff Mott. All rights reserved.
 *   http://code.google.com/p/crypto-js/wiki/License
 */
class Cryptojs {

	function MD5($message, $options=null) {
		$asBytes  = (is_array($options) && isset($options['asBytes'])) ? $options['asBytes'] : false;
		$asString = (is_array($options) && isset($options['asString'])) ? $options['asString'] : false;
		
		// if a UTF-8 byte array was passed directly
		if (is_array($message)) $message = Cryptojs_UTF8::bytesToString($message);
		
		// use built-in support
		$hex = md5($message);
		if ($asBytes || $asString) {
			$bytes = Cryptojs_util::hexToBytes($hex);
			if ($asBytes)
				return $bytes;
			return Cryptojs_Binary::bytesToString($bytes);
		}
		return $hex;
	}
	
	function SHA1($message, $options=null) {
		$asBytes  = (is_array($options) && isset($options['asBytes'])) ? $options['asBytes'] : false;
		$asString = (is_array($options) && isset($options['asString'])) ? $options['asString'] : false;
		
		// if a UTF-8 byte array was passed directly
		if (is_array($message)) $message = Cryptojs_UTF8::bytesToString($message);
		
		// use built-in support (since PHP 4.3)
		$hex = sha1($message);
		if ($asBytes || $asString) {
			$bytes = Cryptojs_util::hexToBytes($hex);
			if ($asBytes)
				return $bytes;
			return Cryptojs_Binary::bytesToString($bytes);
		}
		return $hex;
	}
	
	function SHA256($message, $options=null) {
		$asBytes  = (is_array($options) && isset($options['asBytes'])) ? $options['asBytes'] : false;
		$asString = (is_array($options) && isset($options['asString'])) ? $options['asString'] : false;
		
		// if a UTF-8 byte array was passed directly
		if (is_array($message)) $message = Cryptojs_UTF8::bytesToString($message);
		
		// first choice: use built-in 'hash' support (since PHP 5.1.2)
		if (function_exists('hash') && function_exists('hash_algos')) {
			$algos = hash_algos();
			if (in_array('sha256', $algos)) {
				$hex = hash('sha256', $message);
			}
		}
		
		// 2nd choice: use 'mhash' (obsoleted by 'hash')
		if (function_exists('mhash') && defined('MHASH_SHA256') && !isset($hex)) {
			$hex = bin2hex(mhash(MHASH_SHA256, $message));
		}
		
		// 3rd choice: use sha256 function
		// provided by: suhosin (http://www.hardened-php.net/suhosin/) or 3rd-party (http://nanolink.ca/pub/sha256/)
		if (function_exists('sha256') && !isset($hex)) {
			$hex = sha256($message);
		}
		
		if (isset($hex)) {
			if ($asBytes || $asString) {
				$bytes = Cryptojs_util::hexToBytes($hex);
				if ($asBytes)
					return $bytes;
				return Cryptojs_Binary::bytesToString($bytes);
			}
			return $hex;
		}
	}
	
	function HMAC($algo, $message, $key, $options=null) {
		$algo = strtolower($algo);
		if (!in_array($algo, array('md5', 'sha1', 'sha256'))) return;

		$asBytes  = (is_array($options) && isset($options['asBytes'])) ? $options['asBytes'] : false;
		$asString = (is_array($options) && isset($options['asString'])) ? $options['asString'] : false;

		// if a UTF-8 byte array was passed directly
		if (is_array($message)) $message = Cryptojs_UTF8::bytesToString($message);
		if (is_array($key)) $key = Cryptojs_UTF8::bytesToString($key);

		// first choice: use built-in 'hash' support (since PHP 5.1.2)
		if (function_exists('hash_hmac') && function_exists('hash_algos')) {
			$algos = hash_algos();
			if (in_array($algo, $algos)) {
				$hex = hash_hmac($algo, $message, $key);
			}
		}
		
		$algo = strtoupper($algo);
		
		// 2nd choice: use 'mhash' (obsoleted by 'hash')
		$mhash_constant = 'MHASH_' . $algo;
		if (function_exists('mhash') && defined( $mhash_constant ) && !isset($hex)) {
			$hex = bin2hex(mhash(constant($mhash_constant), $message, $key));
		}
		
		if (isset($hex)) {
			if ($asBytes || $asString) {
				$bytes = Cryptojs_util::hexToBytes($hex);
				if ($asBytes)
					return $bytes;
				return Cryptojs_Binary::bytesToString($bytes);
			}
			return $hex;
		}
		
		// 3rd choice: use the algorithm from crypto-js
		$message = Cryptojs_UTF8::stringToBytes($message);
		$key = Cryptojs_UTF8::stringToBytes($key);
		
		// Allow arbitrary length keys
		$blocksize = Cryptojs::_blocksize($algo);
		if (count($key) > $blocksize * 4)
			$key = Cryptojs::$algo($key, array('asBytes' => true));
		
		// XOR keys with pad constants
		$okey = array_slice($key, 0);
		$ikey = array_slice($key, 0);
		for ($i = 0; $i < $blocksize * 4; $i++) {
			// throws an error for some reason
			//$okey[$i] ^= 0x5C;
			//$ikey[$i] ^= 0x36;
			$okey[$i] = $okey[$i] ^ 0x5C;
			$ikey[$i] = $ikey[$i] ^ 0x36;
		}
		
		$bytes = Cryptojs::$algo(Cryptojs_util::concat($okey, Cryptojs::$algo(Cryptojs_util::concat($ikey, $message), array('asBytes' => true))), array('asBytes' => true));
		if ($asBytes)
			return $bytes;
		else if($asString)
			return Cryptojs_Binary::bytesToString($bytes);
		return Cryptojs_util::bytesToHex($bytes);
	}
	
	function PBKDF2($password, $salt, $keylen, $options=null) {
		// Defaults
		$hasher     = (is_array($options) && isset($options['hasher'])) ? $options['hasher'] : 'sha1';
		$iterations = (is_array($options) && isset($options['iterations'])) ? $options['iterations'] : 1;
		$asBytes    = (is_array($options) && isset($options['asBytes'])) ? $options['asBytes'] : false;
		$asString   = (is_array($options) && isset($options['asString'])) ? $options['asString'] : false;
		
		// allow strings or UTF8 byte arrays
		if (!is_array($password)) $password = Cryptojs_UTF8::stringToBytes($password);
		if (!is_array($salt)) $salt = Cryptojs_UTF8::stringToBytes($salt);

		// Generate key
		$derivedKeyBytes = array();
		$blockindex = 1;
		while (count($derivedKeyBytes) < $keylen) {
			$block = Cryptojs::_PRF($hasher, $password, Cryptojs_util::concat($salt, Cryptojs_util::wordsToBytes(array($blockindex))));

			for ($u = $block, $i = 1; $i < $iterations; $i++) {
				$u = Cryptojs::_PRF($hasher, $password, $u);
				for ($j = 0; $j < count($block); $j++) $block[$j] ^= $u[$j];
			}

			$derivedKeyBytes = Cryptojs_util::concat($derivedKeyBytes, $block);
			$blockindex++;
		}
		
		// Truncate excess bytes
		$derivedKeyBytes = array_slice($derivedKeyBytes, 0, $keylen);
		
		if ($asBytes)
			return $derivedKeyBytes;
		else if($asString)
			return Cryptojs_Binary::bytesToString($derivedKeyBytes);
		return Cryptojs_util::bytesToHex($derivedKeyBytes);
	}
	
	// Pseudo-random function
	function _PRF($algo, $password, $salt) {
		return Cryptojs::HMAC($algo, $salt, $password, array('asBytes' => true));
	}
	
	function _blocksize($algo) {
		$algo = strtolower($algo);
		switch($algo) {
			case 'md5':
				return 16;
			case 'sha1':
				return 16;
			case 'sha256':
				return 16;
		}
	}
}
endif;
?>