<?php defined('ABSPATH') OR die(); ?>

<?php
if (SemisecureLoginReimagined::is_rsa_key_ok() && SemisecureLoginReimagined::is_openssl_avail())
	$skipEncrypt = 'false';
else
	$skipEncrypt = 'true';

if (! SemisecureLoginReimagined::is_rsa_key_ok())
	$message = __('Semisecure Login is not enabled!<br />The public key was not found!<br />Your password will not be encrypted!', $this->text_domain);
else if(! SemisecureLoginReimagined::is_openssl_avail())
	$message = __('Semisecure Login is not enabled due to a misconfiguration on the server!', $this->text_domain);
else
	$message = __('Semisecure Login is enabled.', $this->text_domain);

// SemisecureLoginReimagined::enqueue_js() is called in the admin_print_scripts hook
?>
<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function($) {
	$('p.submit:last').prepend('<span id="semisecure-message"><?php echo addslashes($message); ?></span><br /><br />');

	$('form#your-profile, form#adduser').submit(function() {
		if ( $('table.form-table:last tr.form-invalid').length <= 0 && $('table.form-table:last tr.form-required').filter(function(){return $('input:visible', this).val() == '';}).length <= 0) {
			var password1 = $('#pass1').val();
			var password2 = $('#pass2').val();
			var passwords = [];
			passwords[0] = password1;
			passwords[1] = password2;

			var names = [];
			names[0] = $('#pass1').attr('name');
			names[1] = $('#pass2').attr('name');

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
				$('#semisecure-message').html('<?php _e('Encrypting password &amp; submitting...', $this->text_domain); ?>');

				var arr = SemisecureLoginReimagined.encrypt(passwords, names, nonce_js, public_n, public_e, uuid, secret_key_algo, rand_chars, max_rand_chars);

				if (arr) {
					for (var i = 0; i < arr.length; i++) {
						$('form#your-profile, form#adduser').append(arr[i]);
					}

					var temp1 = '';
					var temp2 = '';
					for (var i = 0; i < password1.length; i++) { temp1 += '*'; }
					for (var i = 0; i < password2.length; i++) { temp2 += '*'; }
					$('#pass1').val(temp1);
					$('#pass2').val(temp2);
					$('#pass1').attr('disabled', 'true');
					$('#pass2').attr('disabled', 'true');

					return true;
				}
			}
			else {
				return true;
			}

			$('#semisecure-message').html('<?php _e('Problem encrypting passwords!<br />Please try again or disable JavaScript to submit without encryption.', $this->text_domain); ?>');
			return false;
		}
	});
})
//]]>
</script>
