<?php
if (!class_exists('Cryptojs_Rabbit')) :
/**
 * Crypto-JS v2.0.0 conversion to PHP
 * http://moggy.laceous.com/
 * http://wordpress.org/extend/plugins/semisecure-login-reimagined/
 * Works with PHP 4 (>= 4.3) and 5
 *   Note: Rabbit currently requires 64-bit PHP (does not work correctly with 32-bit PHP)
 *   Note: SHA-256 and HMAC-SHA256 require 'hash', 'mhash', suhosin, or 3rd-party support
 *
 * Usage:
 *   $crypted = Cryptojs_Rabbit::encrypt('message', 'secret passphrase');
 *   $plain   = Cryptojs_Rabbit::decrypt($crypted,  'secret passphrase');
 *
 * Original JavaScript version:
 *   Crypto-JS v2.0.0
 *   http://code.google.com/p/crypto-js/
 *   Copyright (c) 2009, Jeff Mott. All rights reserved.
 *   http://code.google.com/p/crypto-js/wiki/License
 */
class Cryptojs_Rabbit {

	function encrypt($message, $password) {
		// Convert to bytes
		$m = Cryptojs_UTF8::stringToBytes($message);
		
		// Generate a random IV
		$iv = Cryptojs_util::randomBytes(8);
		
		// Generate key
		$k = (!is_array($password)) ?
		     // Derive key from passphrase
		     Cryptojs::PBKDF2($password, $iv, 32, array('asBytes' => true)) :
		     // else, assume byte array representing cryptographic key
		     $password;
		
		// Encrypt
		Cryptojs_Rabbit::_rabbit(&$m, $k, Cryptojs_util::bytesToWords($iv));
		
		// Return ciphertext
		return Cryptojs_util::bytesToBase64(Cryptojs_util::concat($iv, $m));
	}
	
	function decrypt($ciphertext, $password) {
		// Convert to bytes
		$c = Cryptojs_util::base64ToBytes($ciphertext);
		
		// Separate IV and message
		$iv = array_splice(&$c, 0, 8);
		
		// Generate key
		$k = (!is_array($password)) ?
		     // Derive key from passphrase
		     Cryptojs::PBKDF2($password, $iv, 32, array('asBytes' => true)) :
		     // else, assume byte array representing cryptographic key
		     $password;
		
		// Decrypt
		Cryptojs_Rabbit::_rabbit(&$c, $k, Cryptojs_util::bytesToWords($iv));
		
		// Return plaintext
		return Cryptojs_UTF8::bytesToString($c);
	}
	
	// Encryptin/decryption scheme
	function _rabbit(&$m, $k, $iv) {
		// Inner State
		static $x = array();
		static $c = array();
		static $b;
		
		Cryptojs_Rabbit::_keysetup(&$x, &$c, &$b, $k);
		if ($iv) Cryptojs_Rabbit::_ivsetup(&$x, &$c, &$b, $iv);
		
		for ($s = array(), $i = 0; $i < count($m); $i++) {
			if ($i % 16 == 0) {
				// Iterate the system
				Cryptojs_Rabbit::_nextstate(&$x, &$c, &$b);
				
				// Generate 16 bytes of pseudo-random data
				$s[0] = $x[0] ^ Cryptojs_util::urs($x[5], 16) ^ ($x[3] << 16);
				$s[1] = $x[2] ^ Cryptojs_util::urs($x[7], 16) ^ ($x[5] << 16);
				$s[2] = $x[4] ^ Cryptojs_util::urs($x[1], 16) ^ ($x[7] << 16);
				$s[3] = $x[6] ^ Cryptojs_util::urs($x[3], 16) ^ ($x[1] << 16);
				
				// Swap endian
				for ($j = 0; $j < 4; $j++) {
					$s[$j] = (($s[$j] <<  8) | Cryptojs_util::urs($s[$j], 24)) & 0x00FF00FF | (($s[$j] << 24) | Cryptojs_util::urs($s[$j], 8)) & 0xFF00FF00;
				}
				
				// Convert words to bytes
				for ($bb = 120; $bb >= 0; $bb -= 8) {
					$s[$bb / 8] = Cryptojs_util::urs($s[Cryptojs_util::urs($bb, 5)], (24 - $bb % 32)) & 0xFF;
				}
			}
			
			$m[$i] ^= $s[$i % 16];
		}
	}
	
	// Key setup scheme
	function _keysetup(&$x, &$c, &$b, $k) {
		// Generate initial state values
		$x[0] = $k[0];
		$x[2] = $k[1];
		$x[4] = $k[2];
		$x[6] = $k[3];
		$x[1] = ($k[3] << 16) | Cryptojs_util::urs($k[2], 16);
		$x[3] = ($k[0] << 16) | Cryptojs_util::urs($k[3], 16);
		$x[5] = ($k[1] << 16) | Cryptojs_util::urs($k[0], 16);
		$x[7] = ($k[2] << 16) | Cryptojs_util::urs($k[1], 16);
		
		// Generate initial counter values
		$c[0] = Cryptojs_util::rotl($k[2], 16);
		$c[2] = Cryptojs_util::rotl($k[3], 16);
		$c[4] = Cryptojs_util::rotl($k[0], 16);
		$c[6] = Cryptojs_util::rotl($k[1], 16);
		$c[1] = ($k[0] & 0xFFFF0000) | ($k[1] & 0xFFFF);
		$c[3] = ($k[1] & 0xFFFF0000) | ($k[2] & 0xFFFF);
		$c[5] = ($k[2] & 0xFFFF0000) | ($k[3] & 0xFFFF);
		$c[7] = ($k[3] & 0xFFFF0000) | ($k[0] & 0xFFFF);
		
		// Clear carry bit
		$b = 0;
		
		// Iterate the system four times
		for ($i = 0; $i < 4; $i++) Cryptojs_Rabbit::_nextstate(&$x, &$c, &$b);
		
		// Modify the counters
		for ($i = 0; $i < 8; $i++) $c[$i] ^= $x[($i + 4) & 7];
	}
	
	// IV setup scheme
	function _ivsetup(&$x, &$c, &$b, $iv) {
		// Generate four subvectors
		$i0 = Cryptojs_util::endian($iv[0]);
		$i2 = Cryptojs_util::endian($iv[1]);
		$i1 = Cryptojs_util::urs($i0, 16) | ($i2 & 0xFFFF0000);
		$i3 = ($i2 <<  16) | ($i0 & 0x0000FFFF);
		
		// Modify counter values
		$c[0] ^= $i0;
		$c[1] ^= $i1;
		$c[2] ^= $i2;
		$c[3] ^= $i3;
		$c[4] ^= $i0;
		$c[5] ^= $i1;
		$c[6] ^= $i2;
		$c[7] ^= $i3;
		
		for ($i = 0; $i < 4; $i++) Cryptojs_Rabbit::_nextstate(&$x, &$c, &$b);
	}
	
	// Next-state function
	function _nextstate(&$x, &$c, &$b) {
		// Save old counter values
		for ($c_old = array(), $i = 0; $i < 8; $i++) $c_old[$i] = $c[$i];
		
		// Calculate new counter values
		$c[0] = Cryptojs_util::urs(($c[0] + 0x4D34D34D + $b), 0);
		$c[1] = Cryptojs_util::urs(($c[1] + 0xD34D34D3 + (Cryptojs_util::urs($c[0], 0) < Cryptojs_util::urs($c_old[0], 0) ? 1 : 0)), 0);
		$c[2] = Cryptojs_util::urs(($c[2] + 0x34D34D34 + (Cryptojs_util::urs($c[1], 0) < Cryptojs_util::urs($c_old[1], 0) ? 1 : 0)), 0);
		$c[3] = Cryptojs_util::urs(($c[3] + 0x4D34D34D + (Cryptojs_util::urs($c[2], 0) < Cryptojs_util::urs($c_old[2], 0) ? 1 : 0)), 0);
		$c[4] = Cryptojs_util::urs(($c[4] + 0xD34D34D3 + (Cryptojs_util::urs($c[3], 0) < Cryptojs_util::urs($c_old[3], 0) ? 1 : 0)), 0);
		$c[5] = Cryptojs_util::urs(($c[5] + 0x34D34D34 + (Cryptojs_util::urs($c[4], 0) < Cryptojs_util::urs($c_old[4], 0) ? 1 : 0)), 0);
		$c[6] = Cryptojs_util::urs(($c[6] + 0x4D34D34D + (Cryptojs_util::urs($c[5], 0) < Cryptojs_util::urs($c_old[5], 0) ? 1 : 0)), 0);
		$c[7] = Cryptojs_util::urs(($c[7] + 0xD34D34D3 + (Cryptojs_util::urs($c[6], 0) < Cryptojs_util::urs($c_old[6], 0) ? 1 : 0)), 0);
		$b = Cryptojs_util::urs($c[7], 0) < Cryptojs_util::urs($c_old[7], 0) ? 1 : 0;
		
		// Calculate the g-values
		for ($g = array(), $i = 0; $i < 8; $i++) {
			$gx = Cryptojs_util::urs(($x[$i] + $c[$i]), 0);
			
			// Construct high and low argument for squaring
			$ga = $gx & 0xFFFF;
			$gb = Cryptojs_util::urs($gx, 16);
			
			// Calculate high and low result of squaring
			$gh = Cryptojs_util::urs((Cryptojs_util::urs(($ga * $ga), 17) + $ga * $gb), 15) + $gb * $gb;
			$gl = Cryptojs_util::urs((($gx & 0xFFFF0000) * $gx), 0) + Cryptojs_util::urs(Cryptojs_util::urs((($gx & 0x0000FFFF) * $gx), 0), 0);
			
			// High XOR low
			$g[$i] = $gh ^ $gl;
		}
		
		// Calculate new state values
		$x[0] = $g[0] + (($g[7] << 16) | Cryptojs_util::urs($g[7], 16)) + (($g[6] << 16) | Cryptojs_util::urs($g[6], 16));
		$x[1] = $g[1] + (($g[0] <<  8) | Cryptojs_util::urs($g[0], 24)) + $g[7];
		$x[2] = $g[2] + (($g[1] << 16) | Cryptojs_util::urs($g[1], 16)) + (($g[0] << 16) | Cryptojs_util::urs($g[0], 16));
		$x[3] = $g[3] + (($g[2] <<  8) | Cryptojs_util::urs($g[2], 24)) + $g[1];
		$x[4] = $g[4] + (($g[3] << 16) | Cryptojs_util::urs($g[3], 16)) + (($g[2] << 16) | Cryptojs_util::urs($g[2], 16));
		$x[5] = $g[5] + (($g[4] <<  8) | Cryptojs_util::urs($g[4], 24)) + $g[3];
		$x[6] = $g[6] + (($g[5] << 16) | Cryptojs_util::urs($g[5], 16)) + (($g[4] << 16) | Cryptojs_util::urs($g[4], 16));
		$x[7] = $g[7] + (($g[6] <<  8) | Cryptojs_util::urs($g[6], 24)) + $g[5];
	}
}
endif;
?>