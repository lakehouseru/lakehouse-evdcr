<?php
if (!class_exists('SemisecureLoginReimagined_RsaKeys_Alt')) {
	require_once(dirname(__FILE__).'/SemisecureLoginReimagined_RsaKeys_Base.php');
	/**
	* An alternative class to generate RSA keys
	* This class doesn't rely on direct calls against openssl (it uses only built-in PHP functions)
	* It's not possible to set the public exponent with this class
	*/
	class SemisecureLoginReimagined_RsaKeys_Alt extends SemisecureLoginReimagined_RsaKeys_Base {

		/**
		 * Constructor
		 *
		 * @param int $numbits Should be either 512, 1024, 2048, or 3072
		 */
		function SemisecureLoginReimagined_RsaKeys_Alt($numbits = 2048) {
			list($this->private_key, $this->public_key, $this->num_bits) = $this->generate_new_keypair($numbits);
			if ( empty($this->public_key) ) {
				list($this->public_exponent, $this->modulus) = $this->get_modulus_and_public_exponent_from_private_key($this->private_key);
				$this->public_key = "-----BEGIN PUBLIC KEY-----\n".wordwrap(base64_encode('NOT A REAL PEM ENCODED PUBLIC KEY'), 64, "\n", true)."\n-----END PUBLIC KEY-----"; // this could be made into a real PEM-encoded public-key (not needed at this point)
			}
			else {
				list($this->public_exponent, $this->modulus) = $this->get_modulus_and_public_exponent_from_public_key($this->public_key);
			}
		}

		/**
		 * This function has been tested with 512/1024/2048/3072 bits and f4/3 public-exponent
		 *
		 * @param string $priv_key RSA public-key from openssl
		 * @return array Returns the public exponent and modulus
		 */
		function get_modulus_and_public_exponent_from_private_key($priv_key) {
			$e = 0;
			$n = '';

			if (preg_match('/^-----BEGIN RSA PRIVATE KEY-----([^-]+)-----END RSA PRIVATE KEY-----$/s', $priv_key, $matches)) {
				$key = str_replace("\n", '', $matches[1]); // LF
				$key = str_replace("\r", '', $key);        // CR
				if ( @preg_match('/\p{L}/u', 'a') ) { // replace extra unicode newline characters with empty strings
					$key = preg_replace('/\x{0085}/u', '', $key); // NEL
					$key = preg_replace('/\x{000C}/u', '', $key); // FF
					$key = preg_replace('/\x{2028}/u', '', $key); // LS
					$key = preg_replace('/\x{2029}/u', '', $key); // PS
				}
				$key = bin2hex(base64_decode($key));

				list($bytes, $key) = $this->read_bytes($key, 2);
				if ($bytes == '3081') {
					list($bytes, $key) = $this->read_bytes($key, 1);
				}
				else if ($bytes == '3082') {
					list($bytes, $key) = $this->read_bytes($key, 2);
				}
				else {
					// when dealing with some smaller values, we run into this 'else' situation
					if (substr($bytes, 0, 2) == '30') {
						list($bytes, $key) = $this->read_bytes($key, 1);
					}
					else {
						return array($e, trim($n));
					}
				}

				list($bytes, $key) = $this->read_bytes($key, 4);
				if ($bytes != '02010002') {
					return array($e, trim($n));
				}

				list($bytes, $key) = $this->read_bytes($key, 1);
				if ($bytes == '82') {
					list($bytes, $key) = $this->read_bytes($key, 2);
					$modulus_length = hexdec($bytes);
				}
				else if ($bytes == '81') {
					list($bytes, $key) = $this->read_bytes($key, 1);
					$modulus_length = hexdec($bytes);
				}
				else {
					$modulus_length = hexdec($bytes);
				}

				list($bytes, $key) = $this->read_bytes($key, $modulus_length);
				$n = ltrim($bytes, '0');

				list($bytes, $key) = $this->read_bytes($key, 1);
				if ($bytes != '02') {
					return array($e, '');
				}

				list($bytes, $key) = $this->read_bytes($key, 1);
				$exponent_length = hexdec($bytes);
				list($bytes, $key) = $this->read_bytes($key, $exponent_length);
				$e = (int)$bytes; //ltrim($bytes, '0'); //hexdec($bytes);

				// no need to continue with: privExp, prime1, prime2, exp1, exp2, coeff
			}
			return array($e, trim($n));
		}

		/**
		 * New function that parses through the public key and returns the modulus and public exponent
		 * Doesn't work with every irregular RSA key size, but should work 100% of the time for this plugin
		 *
		 * Another example of parsing a PEM public-key can be found here (C#):
		 *   http://www.jensign.com/JavaScience/dotnet/pempublic/
		 *
		 * @param string $pubkey RSA public-key from openssl
		 * @return array Returns the public exponent and modulus
		 */
		function get_modulus_and_public_exponent_from_public_key($pubkey) {
			$e = 0;
			$n = '';

			if (preg_match('/^-----BEGIN PUBLIC KEY-----([^-]+)-----END PUBLIC KEY-----$/s', $pubkey, $matches)) {
				$key = str_replace("\n", '', $matches[1]); // LF
				$key = str_replace("\r", '', $key);        // CR
				if ( @preg_match('/\p{L}/u', 'a') ) { // replace extra unicode newline characters with empty strings
					$key = preg_replace('/\x{0085}/u', '', $key); // NEL
					$key = preg_replace('/\x{000C}/u', '', $key); // FF
					$key = preg_replace('/\x{2028}/u', '', $key); // LS
					$key = preg_replace('/\x{2029}/u', '', $key); // PS
				}
				$key = bin2hex(base64_decode($key));

				list($bytes, $key) = $this->read_bytes($key, 2);
				if ($bytes == '3081') {
					list($bytes, $key) = $this->read_bytes($key, 1);
				}
				else if ($bytes == '3082') {
					list($bytes, $key) = $this->read_bytes($key, 2);
				}
				else {
					// when dealing with some smaller values, we run into this 'else' situation
					if (substr($bytes, 0, 2) == '30') {
						// nothing to do
					}
					else {
						return array($e, trim($n));
					}
				}

				list($bytes, $key) = $this->read_bytes($key, 15);
				if (strtolower($bytes) != '300d06092a864886f70d0101010500') {
					return array($e, trim($n));
				}

				list($bytes, $key) = $this->read_bytes($key, 2);
				if ($bytes == '0381') {
					list($bytes, $key) = $this->read_bytes($key, 1);
				}
				else if ($bytes == '0382') {
					list($bytes, $key) = $this->read_bytes($key, 2);
				}
				else {
					// when dealing with some smaller values, we run into this 'else' situation
					if (substr($bytes, 0, 2) == '03') {
						// nothing to do
					}
					else {
						return array($e, trim($n));
					}
				}

				list($bytes, $key) = $this->read_bytes($key, 1);
				if ($bytes != '00') {
					return array($e, trim($n));
				}

				list($bytes, $key) = $this->read_bytes($key, 2);
				if ($bytes == '3081') {
					list($bytes, $key) = $this->read_bytes($key, 1);
				}
				else if ($bytes == '3082') {
					list($bytes, $key) = $this->read_bytes($key, 2);
				}
				else {
					// when dealing with some smaller values, we run into this 'else' situation
					if (substr($bytes, 0, 2) == '30') {
						// nothing to do
					}
					else {
						return array($e, trim($n));
					}
				}

				list($bytes, $key) = $this->read_bytes($key, 2);
				if ($bytes == '0281') {
					list($bytes, $key) = $this->read_bytes($key, 1);
					$modulus_length = hexdec($bytes);
				}
				else if ($bytes == '0282') {
					list($bytes, $key) = $this->read_bytes($key, 2);
					$modulus_length = hexdec($bytes);
				}
				else {
					// when dealing with some smaller values, we run into this 'else' situation
					if (substr($bytes, 0, 2) == '02') {
						$modulus_length = hexdec(substr($bytes, 2));
					}
					else {
						return array($e, trim($n));
					}
				}

				list($bytes, $key) = $this->read_bytes($key, 1, true);
				if ($bytes == '00') {
					$modulus_length -= 1;
					list($bytes, $key) = $this->read_bytes($key, 1);
				}

				list($bytes, $key) = $this->read_bytes($key, $modulus_length);
				$n = ltrim($bytes, '0');

				list($bytes, $key) = $this->read_bytes($key, 1);
				if ($bytes != '02') {
					return array($e, '');
				}

				list($bytes, $key) = $this->read_bytes($key, 1);
				$exponent_length = hexdec($bytes);
				list($bytes, $key) = $this->read_bytes($key, $exponent_length);
				$e = (int)$bytes; //ltrim($bytes, '0'); //hexdec($bytes);
			}

			return array($e, trim($n));
		}

		/**
		 * @param string $string
		 * @param int $num The number of bytes (every 2 characters) to read from the string
		 * @param bool $peak Optionally peak at the number of bytes without modifying the returned string
		 * @return array Returns the byte string and the rest of the passed in string
		 */
		function read_bytes($string, $num = 1, $peak = false) {
			$num *= 2;

			$bytes = substr($string, 0, $num);
			if ($peak)
				$new_string = $string;
			else
				$new_string = substr($string, $num);

			return array($bytes, $new_string);
		}

		/**
		 * Generate new public/private PEM keys via built-in PHP functions
		 *
		 * @param int $numbits Should be either 512, 1024, 2048, or 3072
		 * @return array Returns the private-key, public-key, and then number of bits
		 */
		function generate_new_keypair($numbits = 2048) {
			$numbits = (int)$numbits;
			switch($numbits) {
				case 512:
				case 1024:
				case 2048:
				case 3072:
					$nbits = $numbits;
					break;
				default:
					$nbits = 2048;
					break;
			}

			// not possible to set the public-exponent here (PHP seems to default to F4)
			$configargs = array(
				'private_key_bits' => $nbits,
				'private_key_type' => OPENSSL_KEYTYPE_RSA
			);
			$res = openssl_pkey_new($configargs);
			if (is_resource($res)) {
				openssl_pkey_export($res, $priv_key);
				$pub_key = '';
				if (function_exists('openssl_pkey_get_details')) { // version_compare(PHP_VERSION, '5.2', '>=')
					$pub_key_array = openssl_pkey_get_details($res);
					$pub_key = $pub_key_array['key'];
					//$pub_key_array['bits'] == $nbits
				}
				openssl_pkey_free($res);
				return array(
					trim($priv_key),
					trim($pub_key),
					$nbits
				);
			}
			return array('', '', '');
		}
	}
}
?>