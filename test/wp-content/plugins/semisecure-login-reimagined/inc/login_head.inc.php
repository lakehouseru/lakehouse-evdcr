<?php defined('ABSPATH') OR die();

if (SemisecureLoginReimagined::is_rsa_key_ok() && SemisecureLoginReimagined::is_openssl_avail())
	$skipEncrypt = 'false';
else
	$skipEncrypt = 'true';

if (! SemisecureLoginReimagined::is_rsa_key_ok())
	$message = __('Semisecure Login is not enabled!<br />The public key was not found!<br />Your password will not be encrypted!<br />Please login &amp; fix the issue.', $this->text_domain);
else if(! SemisecureLoginReimagined::is_openssl_avail())
	$message = __('Semisecure Login is not enabled due to a misconfiguration on the server!<br />Please login &amp; fix the issue.', $this->text_domain);
else
	$message = __('Semisecure Login is enabled.', $this->text_domain);

// make sure all the required JavaScript is present
SemisecureLoginReimagined::enqueue_js(true);
?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function($) {
	$('#semisecure-message').html('<?php echo addslashes($message); ?>');

	$('form#loginform').submit(function() {
		var password = $('#user_pass').val();
		var name = $('#user_pass').attr('name');
		
		var skipEncrypt = <?php echo $skipEncrypt; ?>;

		// pass PHP values over to the JavaScript side
		var public_n = '<?php echo SemisecureLoginReimagined::public_n(); ?>';
		var public_e = '<?php echo SemisecureLoginReimagined::public_e(); ?>';
		var uuid = '<?php echo SemisecureLoginReimagined::uuid(); ?>';
		var nonce_js = '<?php echo SemisecureLoginReimagined::nonce_js(); ?>';
		var max_rand_chars = '<?php echo SemisecureLoginReimagined::max_rand_chars(); ?>';
		var rand_chars = '<?php echo addslashes(SemisecureLoginReimagined::rand_chars()); ?>';
		var secret_key_algo = '<?php echo SemisecureLoginReimagined::secret_key_algo(); ?>';

		if (public_n != null && public_e != null && public_n.length > 0 && public_e.length > 0 && !skipEncrypt) {
			$('#semisecure-message').html('<?php _e('Encrypting password &amp; logging in...', $this->text_domain); ?>');

			var arr = SemisecureLoginReimagined.encrypt(password, name, nonce_js, public_n, public_e, uuid, secret_key_algo, rand_chars, max_rand_chars);

			if (arr) {
				for (var i = 0; i < arr.length; i++) {
					$('form#loginform').append(arr[i]);
				}

				// don't submit the plain-text password!
				var temp = '';
				for (var i = 0; i < password.length; i++) { temp += '*'; }
				$('#user_pass').val(temp);
				$('#user_pass').attr('disabled', 'true');

				return true;
			}
		}
		else {
			return true;
		}

		$('#semisecure-message').html('<?php _e('Problem encrypting password!<br />Please try again or disable JavaScript to login without encryption.', $this->text_domain); ?>');
		return false;
	});
})
//]]>
</script>
