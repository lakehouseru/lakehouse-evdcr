<?php
if (!class_exists('SemisecureLoginReimagined_RsaKeys_Base')) {
	/**
	* This is a base class for generating RSA keys
	* It includes the main class level variables, and a function to return those variables as an array
	*/
	class SemisecureLoginReimagined_RsaKeys_Base {

		// private_key is needed on the back-end to decrypt
		// modulus and public_exponent are needed on the client-side to encrypt
		var $private_key;      // PEM format
		var $modulus;          // hex format
		var $public_exponent;  // 10001 or 3
		var $num_bits;         // int
		var $public_key;       // PEM format

		/**
		 * @return array Returns the class-level variables in an array
		 */
		function get_keys_array() {
			return array(
				'numbits'        => (int)    $this->num_bits,
				'publicexponent' => (int)    $this->public_exponent,
				'modulus'        => (string) strtoupper($this->modulus),
				'privatekey'     => (string) $this->private_key,
				'publickey'      => (string) $this->public_key
			);
		}

	}
}
?>