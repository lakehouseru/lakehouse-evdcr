<?php defined('ABSPATH') OR die();

if (is_multisite()) {
	if (!current_user_can(SEMISECURELOGIN_REIMAGINED__MANAGE_NETWORK_CAP)) {
		wp_die(__('Ooops... looks like you do not have the correct privileges to access this page.', $this->text_domain));
	}
}
else {
	if (!current_user_can(SEMISECURELOGIN_REIMAGINED__MANAGE_OPTIONS_CAP)) {
		wp_die(__('Ooops... looks like you do not have the correct privileges to access this page.', $this->text_domain));
	}
}

$sub = $_GET['sub'];
if ($sub == 'misc')
	$class_misc = 'class="current"';
else if ($sub == 'integration')
	$class_integration = 'class="current"';
else {
	$class_keypair = 'class="current"';
	$sub = 'keypair';
}

// WP forces GPC to be escaped, but nothing here should fall into that scenario
// generate and save a new keypair
if (isset($_POST['rsa_numbits']) && !empty($_POST['rsa_numbits']) && is_numeric($_POST['rsa_numbits'])) {
	check_admin_referer('generate_semisecure-login-reimagined');
	if ($_POST['force_alt_method'] == 'yes')
		$this->force_alt_keypair_generation_method = TRUE;
	$rsa_numbits = $_POST['rsa_numbits'];
	$successful = $this->store_new_rsa_keypair($rsa_numbits);
	if ($successful) {
		print '<div onmouseover="this.childNodes[1].style.display=\'block\'" onmouseout="this.childNodes[1].style.display=\'none\'" id="message" class="updated fade"><p>' . __('<strong>The new keypair was successfully generated and saved.</strong> <span style="font-size:75%;">(hover for debug info)</span>', $this->text_domain) . '</p>';
	}
	else {
		print '<div onmouseover="this.childNodes[1].style.display=\'block\'" onmouseout="this.childNodes[1].style.display=\'none\'" id="message" class="error"><p>' . __('<strong>The keypair could not be created!</strong> <span style="font-size:75%;">(hover for debug info)</span>', $this->text_domain) . '</p>';
	}
	print '<ul style="display:none;">';
	foreach ($this->debug_info as $val) {
		print '<li>' . $val . '</li>';
	}
	print '</ul>';
	print '</div>';
}
// miscellaneous settings
else if ($_POST['where_to_encrypt'] == 'postback') {
	check_admin_referer('misc_semisecure-login-reimagined');
	$do_admin_encrypt = (isset($_POST['do_admin_encrypt'])) ? 'yes' : 'no';
	$do_login_encrypt = (isset($_POST['do_login_encrypt'])) ? 'yes' : 'no';
	$this->update_option('encrypt_admin', $do_admin_encrypt);
	$this->update_option('encrypt_login', $do_login_encrypt);
	$this->update_option('secretkey_algo', $_POST['secret_key_algo']);
	$this->update_option('nonce_method', $_POST['nonce_method']);
	if (is_multisite()) {
		$this->update_option('force_utf8', $_POST['force_utf8']);
		$this->update_option('allow_overrides', $_POST['allow_overrides']);
	}
	print '<div id="message" class="updated fade"><p><strong>' . __('The settings were successfully updated.', $this->text_domain) . '</strong></p></div>';
	
	if ($this->get_option('force_utf8') == 'yes') {
		add_filter('pre_option_blog_charset', array(&$this, 'pre_option_blog_charset'));
	}
	else {
		remove_filter('pre_option_blog_charset', array(&$this, 'pre_option_blog_charset'));
	}
}

if (!extension_loaded('openssl')) {
	print '<div class="error"><p><strong>' . __('OpenSSL does not appear to be installed. This plugin relies on OpenSSL and will not work until it has been installed.', $this->text_domain) . '</strong></p></div>';
}
else if (!function_exists('openssl_private_decrypt')) {
	print '<div class="error"><p><strong>' . __('The <code>openssl_private_decrypt</code> function appears to be disabled. This plugin will not work until it has been enabled. Check the <code>disable_functions</code> setting in your php.ini file.', $this->text_domain) . '</strong></p></div>';
}
else if (!SemisecureLoginReimagined::is_rsa_key_ok()) {
	print '<div class="error"><p><strong>' .__('The RSA keypair could not be found! Please remedy the situation by generating a new keypair.', $this->text_domain)  . '</strong></p></div>';
}

$blog_charset = get_option('blog_charset');
$blog_charset = trim(strtoupper($blog_charset));
if ($blog_charset != 'UTF-8') {
	print '<div class="error"><p><strong>' . sprintf(__('This plugin is tested to work with UTF-8. Your blog settings are currently using the following character encoding: %s', $this->text_domain), htmlspecialchars($blog_charset)) . '</strong></p></div>';
}

$do_encrypt_admin = $this->get_option('encrypt_admin');
$do_encrypt_login = $this->get_option('encrypt_login');
$secret_key_algo = $this->get_option('secretkey_algo');
$nonce_method = $this->get_option('nonce_method');
$force_utf8 = $this->get_option('force_utf8');
$allow_overrides = $this->get_option('allow_overrides');

$is_64bit_int = $this->is_64bit_int();

$keys_array = $this->get_option('rsa_keys');
?>
<div class="wrap">
<div class="icon32" id="icon-options-general"><br/></div>
<h2><?php _e('Semisecure Login Reimagined', $this->text_domain); ?></h2>
<p>
	<?php _e('This plugin uses a combination of public-key (RSA) and secret-key (MARC4, Rabbit, or AES) encryption to encrypt your password on the client-side before transmission. A nonce is used to help prevent replay attacks. This provides a &quot;semisecure&quot; login environment. For full security you should use an SSL certificate.', $this->text_domain); ?>
</p>
<ul class="subsubsub">
  <li><a <?php echo $class_keypair; ?> href="?page=<?php echo $_GET['page']; ?>"><?php _e('Keypair Settings', $this->text_domain); ?></a> | </li>
  <li><a <?php echo $class_misc; ?> href="?page=<?php echo $_GET['page']; ?>&amp;sub=misc"><?php _e('Misc Settings', $this->text_domain); ?></a> | </li>
  <li><a <?php echo $class_integration; ?> href="?page=<?php echo $_GET['page']; ?>&amp;sub=integration"><?php _e('Integration', $this->text_domain); ?></a></li>
</ul>
<div style="clear:both;"></div>

<?php if ($sub == 'keypair') : ?>

<h3><?php _e('Current RSA Keypair', $this->text_domain); ?></h3>
<p>
<?php
// check if a keypair already exists
if (SemisecureLoginReimagined::is_rsa_key_ok()) {
?>
	<?php printf(__('The current key is <strong>%d</strong> bits in length, and the public-exponent is <strong>%s</strong>', $this->text_domain), $keys_array['numbits'], '0x' . $keys_array['publicexponent']); ?>
	<table border="0">
		<tr valign="top">
			<td><?php _e('Modulus:', $this->text_domain); ?></td>
			<td><pre><?php echo wordwrap($keys_array['modulus'], 64, "\n", true) ?></pre></td>
		</tr>
	</table>
<?php
} else {
?>
	<span style="color:red;"><strong><?php _e('WARNING:', $this->text_domain); ?></strong><br /><?php _e('The RSA keypair could not be found! Please remedy the situation by generating a new keypair below.', $this->text_domain); ?></span>
<?php
}
?>
</p>
<p>
	<?php _e('<em>&quot;RSA claims that 1024-bit keys are likely to become crackable some time between 2006 and 2010 and that 2048-bit keys are sufficient until 2030. An RSA key length of 3072 bits should be used if security is required beyond 2030.&quot;</em> -<a href="http://en.wikipedia.org/wiki/Key_size#Asymmetric_algorithm_key_lengths" target="_blank">Wikipedia</a>', $this->text_domain); ?>
</p>
<h3><?php _e('Generate New Keypair', $this->text_domain); ?></h3>
<p>
	<?php _e('Use the following options to generate &amp; store a new RSA keypair.', $this->text_domain); ?>
</p>
<p>
	<?php _e('<em>A lower number of bits requires less processing power, while a higher number of bits provides better security.</em>', $this->text_domain); ?>
</p>
<form method="post" action="">
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('Number of bits', $this->text_domain); ?></th>
			<td>
				<select name="rsa_numbits">
					<option value="512"><?php _e('512 bits', $this->text_domain); ?></option>
					<option value="1024"><?php _e('1024 bits', $this->text_domain); ?></option>
					<option value="2048" selected="selected"><?php _e('2048 bits', $this->text_domain); ?></option>
					<option value="3072"><?php _e('3072 bits', $this->text_domain); ?></option>
				</select>
				<br/><?php _e('2048 bits is currently recommended based on the tradeoff between performance and security.', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<td colspan="2">
				<hr style="text-align:center;width:50%;margin-right:auto;margin-left:auto;" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Force alt method', $this->text_domain); ?></th>
			<td>
				<label><input type="checkbox" name="force_alt_method" value="yes" />  <?php _e('Yes', $this->text_domain); ?> </label>
				<br /><?php _e('There are two keypair generation methods. The default method makes a direct call against OpenSSL on the server. The alternative method uses PHP\'s built-in OpenSSL functions. The alternative method will automatically be used if: PHP\'s execution functions are disabled, safe mode is enabled, or OpenSSL is not in the location specified in the advanced override option. You can also force the alternative method using this option.', $this->text_domain); ?>
			</td>
		</tr>
	</table>
	<p class="submit">
		<?php wp_nonce_field('generate_semisecure-login-reimagined'); ?>
		<input type="submit" name="Submit" value="<?php _e('Generate Key &raquo;', $this->text_domain); ?>" />
	</p>
</form>

<?php elseif ($sub == 'misc') : ?>

<form method="post" action="">
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('User login', $this->text_domain); ?></th>
			<td>
				<label><input type="checkbox" name="do_login_encrypt" value="yes" <?php if ($do_encrypt_login == 'yes') { echo 'checked="checked"'; } ?> /> <?php _e('Encrypt passwords when logging in?', $this->text_domain); ?> </label>
				<br/><?php _e('This includes the wp-login.php page as well as any plugins that implement the login_head and login_form hooks.', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('User administration', $this->text_domain); ?></th>
			<td>
				<label><input type="checkbox" name="do_admin_encrypt" value="yes" <?php if ($do_encrypt_admin == 'yes') { echo 'checked="checked"'; } ?> /> <?php _e('Encrypt passwords when managing users?', $this->text_domain); ?> </label>
				<br/><?php _e('This includes changing or setting a password when: editing your own user (wp-admin/profile.php), editing another user (wp-admin/user-edit.php), and creating a new user (wp-admin/user-new.php).', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Secret-key algorithm', $this->text_domain); ?></th>
			<td>
				<label><input type="radio" name="secret_key_algo" value="marc4" <?php if ($secret_key_algo == 'marc4') echo 'checked="checked"'; ?> /> <?php _e('MARC4', $this->text_domain); ?> </label>
				<label><input type="radio" name="secret_key_algo" value="rabbit" <?php if ($secret_key_algo == 'rabbit') echo 'checked="checked"'; if (!$is_64bit_int) echo ' disabled="disabled"'; ?> /> <?php if(!$is_64bit_int) echo '<del>'; _e('Rabbit', $this->text_domain); if(!$is_64bit_int) echo '</del>'; ?> </label>
				<label><input type="radio" name="secret_key_algo" value="aes-cbc" <?php if ($secret_key_algo == 'aes-cbc') echo 'checked="checked"'; ?> /> <?php _e('AES (CBC)', $this->text_domain); ?> </label>
				<label><input type="radio" name="secret_key_algo" value="aes-ofb" <?php if ($secret_key_algo == 'aes-ofb') echo 'checked="checked"'; ?> /> <?php _e('AES (OFB)', $this->text_domain); ?> </label>
				<br/><?php _e('MARC4 (Modified Allegedly RC4) is based on RC4, a widely-used stream cipher. This modified version corrects certain weaknesses found in RC4. Rabbit is a high-performance stream cipher and a finalist in the eSTREAM portfolio. The Advanced Encryption Standard (AES) is a block cipher, and an encryption standard adopted by the U.S. government. CBC (cipher-block chaining) is one of the most common block cipher modes. OFB (output feedback) is a mode that turns a block cipher into a synchronous stream cipher.<br /><em>Note: Rabbit currently requires 64-bit PHP, and will not be selectable if you are running 32-bit PHP.</em>', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Nonce', $this->text_domain); ?></th>
			<td>
				<label><input type="radio" name="nonce_method" value="direct" <?php if ($nonce_method == 'direct') echo 'checked="checked"'; ?> /> <?php _e('Print directly', $this->text_domain); ?> </label>
				<label><input type="radio" name="nonce_method" value="async" <?php if ($nonce_method == 'async') echo 'checked="checked"'; ?> /> <?php _e('Async (Ajax)', $this->text_domain); ?> </label>
				<label><input type="radio" name="nonce_method" value="disable" <?php if ($nonce_method == 'disable') echo 'checked="checked"'; ?> /> <?php _e('Disable', $this->text_domain); ?> </label>
				<br/><?php _e('<em>Print directly</em> means that the nonce is passed directly from PHP to JavaScript. The <em>asynchronous</em> option will use Ajax to dynamically retrieve the current nonce value. This might be necessary if you are using a caching plugin. You can also choose to <em>disable</em> nonce support. Nonces are used to protect against login replays. This plugin does not protect against session hijacking, so nonces are not strictly needed.', $this->text_domain); ?>
			</td>
		</tr>
		<?php if (is_multisite()) : ?>
		<tr valign="top">
			<th scope="row"><?php _e('Force UTF-8', $this->text_domain); ?></th>
			<td>
				<label><input type="radio" name="force_utf8" value="yes" <?php if ($force_utf8 == 'yes') echo 'checked="checked"'; ?> /> <?php _e('Yes', $this->text_domain); ?> </label>
				<label><input type="radio" name="force_utf8" value="no" <?php if ($force_utf8 == 'no') echo 'checked="checked"'; ?> /> <?php _e('No', $this->text_domain); ?> </label>
				<br/><?php _e('This plugin is tested to work with UTF-8. You can individually edit your blog settings or, for multisite installs, globally force the option here without having to edit each and every site in your network.<br /><em>Note: This option does not change the blog_charset in the database.</em>', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Allow overrides', $this->text_domain); ?></th>
			<td>
				<label><input type="radio" name="allow_overrides" value="network-admins" <?php if ($allow_overrides == 'network-admins') echo 'checked="checked"'; ?> /> <?php _e('Yes (super admins only)', $this->text_domain); ?> </label>
				<label><input type="radio" name="allow_overrides" value="site-admins" <?php if ($allow_overrides == 'site-admins') echo 'checked="checked"'; ?> /> <?php _e('Yes (super admins + site admins)', $this->text_domain); ?> </label>
				<label><input type="radio" name="allow_overrides" value="no" <?php if ($allow_overrides == 'no') echo 'checked="checked"'; ?> /> <?php _e('No', $this->text_domain); ?> </label>
				<br/><?php _e('For multisite installs, these options are applied globally throughout your network. Do you also want to allow some of these settings to be overriden on a site by site basis? If so, do you want to restrict access to <em>super administrators</em> or do you also want to give individual <em>site administrators</em> access to these settings?', $this->text_domain); ?>
			</td>
		</tr>
		<?php endif; ?>
	</table>
	<p class="submit">
		<input type="hidden" name="where_to_encrypt" value="postback" />
		<?php wp_nonce_field('misc_semisecure-login-reimagined'); ?>
		<input type="submit" name="Submit" value="<?php _e('Update Options &raquo;', $this->text_domain); ?>" />
	</p>
</form>

<?php elseif ($sub == 'integration') : ?>

<?php require(dirname(__FILE__) . '/integration_text.inc.php'); ?>

<?php endif; ?>
</div>