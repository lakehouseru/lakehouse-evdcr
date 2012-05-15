<?php defined('ABSPATH') OR die();

if (!extension_loaded('openssl')) {
	print '<div class="error"><p><strong>' . __('OpenSSL does not appear to be installed. This plugin relies on OpenSSL and will not work until it has been installed.', $this->text_domain) . '</strong></p></div>';
}
else if (!function_exists('openssl_private_decrypt')) {
	print '<div class="error"><p><strong>' . __('The <code>openssl_private_decrypt</code> function appears to be disabled. This plugin will not work until it has been enabled. Check the <code>disable_functions</code> setting in your php.ini file.', $this->text_domain) . '</strong></p></div>';
}
else if (!SemisecureLoginReimagined::is_rsa_key_ok()) {
	print '<div class="error"><p><strong>' .__('The RSA keypair could not be found! Please remedy the situation by having a Super Admin generate a new keypair.', $this->text_domain)  . '</strong></p></div>';
}

$blog_charset = get_option('blog_charset');
$blog_charset = trim(strtoupper($blog_charset));
if ($blog_charset != 'UTF-8') {
	print '<div class="error"><p><strong>' . sprintf(__('This plugin is tested to work with UTF-8. Your blog settings are currently using the following character encoding: %s', $this->text_domain), htmlspecialchars($blog_charset)) . '</strong></p></div>';
}
?>
<div class="wrap">
<div class="icon32" id="icon-options-general"><br/></div>
<h2><?php _e('Semisecure Login Reimagined', $this->text_domain); ?></h2>
<p>
	<?php _e('This plugin uses a combination of public-key (RSA) and secret-key (MARC4, Rabbit, or AES) encryption to encrypt your password on the client-side before transmission. A nonce is used to help prevent replay attacks. This provides a &quot;semisecure&quot; login environment. For full security you should use an SSL certificate.', $this->text_domain); ?>
</p>
<ul class="subsubsub">
  <li><a class="current" href="?page=<?php echo $_GET['page']; ?>"><?php _e('Integration', $this->text_domain); ?></a></li>
</ul>
<div style="clear:both;"></div>

<?php require(dirname(__FILE__) . '/integration_text.inc.php'); ?>

</div>