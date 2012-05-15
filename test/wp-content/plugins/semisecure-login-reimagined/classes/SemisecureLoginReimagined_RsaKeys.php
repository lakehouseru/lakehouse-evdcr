<?php
if (!class_exists('SemisecureLoginReimagined_RsaKeys')) {
	require_once(dirname(__FILE__).'/SemisecureLoginReimagined_RsaKeys_Base.php');
	/**
	* This class generates new public/private keys using openssl
	* It makes a couple calls directly against the openssl executable on your server
	*/
	class SemisecureLoginReimagined_RsaKeys extends SemisecureLoginReimagined_RsaKeys_Base {

		/**
		 * Constructor
		 *
		 * @param int $numbits Should be either 512, 1024, 2048, or 3072
		 * @param int $publicexponent Should be either 10001 (F4 = 0x10001 = 65537) or 3 (0x3)
		 */
		function SemisecureLoginReimagined_RsaKeys($numbits = 2048, $publicexponent = 10001) {
			list($this->private_key, $this->public_exponent, $this->num_bits) = $this->generate_new_private_key($numbits, $publicexponent);
			list($this->modulus, $this->public_key) = $this->get_modulus_and_public_key_from_private_key($this->private_key);
		}

		/**
		 * Get modulus and public key from privatekey via openssl
		 *
		 * @param string $privkey RSA private-key from openssl
		 * @return array Returns the modulus and public-key
		 */
		function get_modulus_and_public_key_from_private_key($privkey) {
			if (preg_match('/^-----BEGIN RSA PRIVATE KEY-----[^-]+-----END RSA PRIVATE KEY-----$/s', $privkey)) {
				$descriptorspec = array(
					0 => array("pipe", "r"),  // stdin is a pipe that the child will read from
					1 => array("pipe", "w")   // stdout is a pipe that the child will write to
					//2 => array("file", "/tmp/error-output.txt", "a") // stderr is a file to write to
				);
				$process = @proc_open(SEMISECURELOGIN_REIMAGINED__OPENSSL_LOCATION . ' rsa -pubout -modulus', $descriptorspec, $pipes);
				if (is_resource($process)) {
					fwrite($pipes[0], $privkey);
					fclose($pipes[0]);
					//$output = stream_get_contents($pipes[1]); // PHP5 only :(
					while (!feof($pipes[1]))
						$output .= fgets($pipes[1]);
					$output = trim($output);
					fclose($pipes[1]);
					@proc_close($process);
					if (preg_match('/^Modulus=([^-]+)(-----BEGIN PUBLIC KEY-----[^-]+-----END PUBLIC KEY-----)$/s', $output, $matches)) {
						return array(
							trim($matches[1]),
							trim($matches[2])
						);
					}
				}
			}
			return array('', '');
		}

		/**
		 * Generates the private key via openssl
		 *
		 * @param int $numbits Should be either 512, 1024, 2048, or 3072
		 * @param int $publicexponent Should be either 10001 (F4 = 0x10001 = 65537) or 3 (0x3)
		 * @return array Returns the private-key, public-exponent, and number-of-bits
		 */
		function generate_new_private_key($numbits = 2048, $publicexponent = 10001) {
			if ($publicexponent == 10001) {
				$e = '-f4';
				$pubExp = 10001;
			}
			else {
				$e = '-3';
				$pubExp = 3;
			}

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

			$privkey = @shell_exec(SEMISECURELOGIN_REIMAGINED__OPENSSL_LOCATION . " genrsa $e $nbits");
			return array(
				trim($privkey),
				$pubExp,
				$nbits
			);
		}
	}
}
?>