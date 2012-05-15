<?php defined('ABSPATH') OR die();

if (is_multisite()) {
	if ($this->get_option('allow_overrides') != 'no') {
		if ($this->get_option('allow_overrides') == 'network-admins' && !current_user_can(SEMISECURELOGIN_REIMAGINED__MANAGE_NETWORK_CAP)) {
			wp_die(__('Ooops... looks like you do not have the correct privileges to access this page.', $this->text_domain));
		}
		else if ($this->get_option('allow_overrides') == 'site-admins' && !current_user_can(SEMISECURELOGIN_REIMAGINED__MANAGE_OPTIONS_CAP)) {
			wp_die(__('Ooops... looks like you do not have the correct privileges to access this page.', $this->text_domain));
		}
	}
	else {
		wp_die(__('Ooops... looks like you do not have the correct privileges to access this page.', $this->text_domain));
	}
}
else {
	wp_die(__('Ooops... looks like you do not have the correct privileges to access this page.', $this->text_domain));
}

$sub = $_GET['sub'];
if ($sub == 'integration')
	$class_integration = 'class="current"';
else {
	$class_misc = 'class="current"';
	$sub = 'misc';
}

// WP forces GPC to be escaped, but nothing here should fall into that scenario
// miscellaneous settings
if ($_POST['where_to_encrypt'] == 'postback') {
	check_admin_referer('misc_semisecure-login-reimagined');
	$this->update_option('use_overrides', $_POST['use_overrides'], true);
	$do_admin_encrypt = (isset($_POST['do_admin_encrypt'])) ? 'yes' : 'no';
	$do_login_encrypt = (isset($_POST['do_login_encrypt'])) ? 'yes' : 'no';
	$this->update_option('encrypt_admin', $do_admin_encrypt, true);
	$this->update_option('encrypt_login', $do_login_encrypt, true);
	$this->update_option('secretkey_algo', $_POST['secret_key_algo'], true);
	$this->update_option('nonce_method', $_POST['nonce_method'], true);
	print '<div id="message" class="updated fade"><p><strong>' . __('The settings were successfully updated.', $this->text_domain) . '</strong></p></div>';
}

if (!extension_loaded('openssl')) {
	print '<div class="error"><p><strong>' . __('OpenSSL does not appear to be installed. This plugin relies on OpenSSL and will not work until it has been installed.', $this->text_domain) . '</strong></p></div>';
}
else if (!function_exists('openssl_private_decrypt')) {
	print '<div class="error"><p><strong>' . __('The <code>openssl_private_decrypt</code> function appears to be disabled. This plugin will not work until it has been enabled. Check the <code>disable_functions</code> setting in your php.ini file.', $this->text_domain) . '</strong></p></div>';
}
else if (!SemisecureLoginReimagined::is_rsa_key_ok()) {
	print '<div class="error"><p><strong>' . __('The RSA keypair could not be found! Please remedy the situation by having a Super Admin generate a new keypair.', $this->text_domain)  . '</strong></p></div>';
}

$blog_charset = get_option('blog_charset');
$blog_charset = trim(strtoupper($blog_charset));
if ($blog_charset != 'UTF-8') {
	print '<div class="error"><p><strong>' . sprintf(__('This plugin is tested to work with UTF-8. Your blog settings are currently using the following character encoding: %s', $this->text_domain), htmlspecialchars($blog_charset)) . '</strong></p></div>';
}

$use_overrides = $this->get_option('use_overrides', true);

$do_encrypt_admin = $this->get_option('encrypt_admin', true);
$do_encrypt_login = $this->get_option('encrypt_login', true);
$secret_key_algo = $this->get_option('secretkey_algo', true);
$nonce_method = $this->get_option('nonce_method', true);

$do_encrypt_admin_global = $this->get_option('encrypt_admin');
$do_encrypt_login_global = $this->get_option('encrypt_login');
$secret_key_algo_global = $this->get_option('secretkey_algo');
$nonce_method_global = $this->get_option('nonce_method');

$is_64bit_int = $this->is_64bit_int();
?>
<div class="wrap">
<div class="icon32" id="icon-options-general"><br/></div>
<h2><?php _e('Semisecure Login Reimagined', $this->text_domain); ?></h2>
<p>
	<?php _e('This plugin uses a combination of public-key (RSA) and secret-key (MARC4, Rabbit, or AES) encryption to encrypt your password on the client-side before transmission. A nonce is used to help prevent replay attacks. This provides a &quot;semisecure&quot; login environment. For full security you should use an SSL certificate.', $this->text_domain); ?>
</p>
<ul class="subsubsub">
  <li><a <?php echo $class_misc; ?> href="?page=<?php echo $_GET['page']; ?>"><?php _e('Misc Settings', $this->text_domain); ?></a> | </li>
  <li><a <?php echo $class_integration; ?> href="?page=<?php echo $_GET['page']; ?>&amp;sub=integration"><?php _e('Integration', $this->text_domain); ?></a></li>
</ul>
<div style="clear:both;"></div>

<?php if ($sub == 'misc') : ?>

<form method="post" action="">
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><?php _e('Override settings', $this->text_domain); ?></th>
			<td>
				<label><input type="radio" name="use_overrides" value="yes" <?php if ($use_overrides == 'yes') echo 'checked="checked"'; ?> /> <?php _e('Yes', $this->text_domain); ?> </label>
				<label><input type="radio" name="use_overrides" value="no" <?php if ($use_overrides == 'no') echo 'checked="checked"'; ?> /> <?php _e('No', $this->text_domain); ?> </label>
				<br/><?php _e('Do you want to override the following settings on this site? If not, then the global network-wide settings will be used.', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<td colspan="2">
				<hr style="text-align:center;width:50%;margin-right:auto;margin-left:auto;" />
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('User login', $this->text_domain); ?></th>
			<td>
				<label><input type="checkbox" name="do_login_encrypt" value="yes" <?php if ($do_encrypt_login == 'yes') { echo 'checked="checked"'; } ?> /> <?php _e('Encrypt passwords when logging in?', $this->text_domain); ?> </label> <strong><?php _e(sprintf('[Global setting: %s]', ($do_encrypt_login_global == 'yes') ? 'Checked' : 'Unchecked'), $this->text_domain) ?></strong>
				<br/><?php _e('This includes the wp-login.php page as well as any plugins that implement the login_head and login_form hooks.', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('User administration', $this->text_domain); ?></th>
			<td>
				<label><input type="checkbox" name="do_admin_encrypt" value="yes" <?php if ($do_encrypt_admin == 'yes') { echo 'checked="checked"'; } ?> /> <?php _e('Encrypt passwords when managing users?', $this->text_domain); ?> </label> <strong><?php _e(sprintf('[Global setting: %s]', ($do_encrypt_admin_global == 'yes') ? 'Checked' : 'Unchecked'), $this->text_domain) ?></strong>
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
				<?php
					if ($secret_key_algo_global == 'marc4')
						$secret_key_algo_global_txt = __('MARC4', $this->text_domain);
					else if ($secret_key_algo_global == 'rabbit')
						$secret_key_algo_global_txt = __('Rabbit', $this->text_domain);
					else if ($secret_key_algo_global == 'aes-cbc')
						$secret_key_algo_global_txt = __('AES (CBC)', $this->text_domain);
					else //if ($secret_key_algo_global == 'aes-ofb')
						$secret_key_algo_global_txt = __('AES (OFB)', $this->text_domain);
				?>
				<strong><?php _e(sprintf('[Global setting: %s]', $secret_key_algo_global_txt), $this->text_domain) ?></strong>
				<br/><?php _e('MARC4 (Modified Allegedly RC4) is based on RC4, a widely-used stream cipher. This modified version corrects certain weaknesses found in RC4. Rabbit is a high-performance stream cipher and a finalist in the eSTREAM portfolio. The Advanced Encryption Standard (AES) is a block cipher, and an encryption standard adopted by the U.S. government. CBC (cipher-block chaining) is one of the most common block cipher modes. OFB (output feedback) is a mode that turns a block cipher into a synchronous stream cipher.<br /><em>Note: Rabbit currently requires 64-bit PHP, and will not be selectable if you are running 32-bit PHP.</em>', $this->text_domain); ?>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e('Nonce', $this->text_domain); ?></th>
			<td>
				<label><input type="radio" name="nonce_method" value="direct" <?php if ($nonce_method == 'direct') echo 'checked="checked"'; ?> /> <?php _e('Print directly', $this->text_domain); ?> </label>
				<label><input type="radio" name="nonce_method" value="async" <?php if ($nonce_method == 'async') echo 'checked="checked"'; ?> /> <?php _e('Async (Ajax)', $this->text_domain); ?> </label>
				<label><input type="radio" name="nonce_method" value="disable" <?php if ($nonce_method == 'disable') echo 'checked="checked"'; ?> /> <?php _e('Disable', $this->text_domain); ?> </label>
				<?php
					if ($nonce_method_global == 'direct')
						$nonce_method_global_txt = __('Print directly', $this->text_domain);
					else if ($nonce_method_global == 'async')
						$nonce_method_global_txt = __('Async (Ajax)', $this->text_domain);
					else //if ($nonce_method_global == 'disable')
						$nonce_method_global_txt = __('Disable', $this->text_domain);
				?>
				<strong><?php _e(sprintf('[Global setting: %s]', $nonce_method_global_txt), $this->text_domain) ?></strong>
				<br/><?php _e('<em>Print directly</em> means that the nonce is passed directly from PHP to JavaScript. The <em>asynchronous</em> option will use Ajax to dynamically retrieve the current nonce value. This might be necessary if you are using a caching plugin. You can also choose to <em>disable</em> nonce support. Nonces are used to protect against login replays. This plugin does not protect against session hijacking, so nonces are not strictly needed.', $this->text_domain); ?>
			</td>
		</tr>
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