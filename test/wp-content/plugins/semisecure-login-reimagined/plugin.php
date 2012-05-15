<?php
/*
Plugin Name: Semisecure Login Reimagined
Plugin URI: http://wordpress.org/extend/plugins/semisecure-login-reimagined/
Description: This plugin increases the security of the login process by encrypting the password on the client-side. JavaScript is required to enable encryption. It is most useful for situations where SSL is not available, but the administrator wishes to have some additional security measures in place without sacrificing convenience.
Version: 3.2.0
Author: moggy
Author URI: http://moggy.laceous.com/
Network: false
*/

/*
    Based on Semisecure Login (http://wordpress.org/extend/plugins/semisecure-login/)

    Thanks to:
      - BigIntegers and RSA in JavaScript (http://www-cs-students.stanford.edu/~tjw/jsbn/)
      - crypto-js (http://code.google.com/p/crypto-js/)
      - famfamfam (http://www.famfamfam.com/lab/icons/silk/)

    This plugin requires at least WP 3.1 and PHP 4.3

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
    To override any of the following advanced options, you should define them in your wp-config.php file
    Please note that these options could change as this plugin is updated
*/

/**
 * If you're using the default keypair generation method, and for some reason the path to openssl isn't included in your server's system path
 *   (which means that you can't call out directly to openssl)
 *   then you can include the full path to openssl here
 * For example: '/usr/bin/openssl' or 'C:\\OpenSSL\\bin\\openssl'
 * This is a string (the default value is 'openssl')
 */
if (!defined('SEMISECURELOGIN_REIMAGINED__OPENSSL_LOCATION'))
	define('SEMISECURELOGIN_REIMAGINED__OPENSSL_LOCATION', 'openssl');

/**
 * If the POST key is already set, do we want to overwrite the value with the decrypted RSA value?
 * For example:
 *   $_POST['pwd'] is already set
 *   $_POST['pwd__uuid'] is also set
 *     Should we overwrite 'pwd' with the decrypted value of 'pwd__uuid'?
 *     The one exception is if 'pwd' contains all asterisks (*)
 *       In this case, we'll always overwrite 'pwd' with 'pwd__uuid'
 * Allowed values: TRUE or FALSE (the default value is FALSE)
 */
if (!defined('SEMISECURELOGIN_REIMAGINED__OVERWRITE_POST_KEY_VALUE'))
	define('SEMISECURELOGIN_REIMAGINED__OVERWRITE_POST_KEY_VALUE', FALSE);

/**
 * This option is helpful if you're using a 2nd (or 3rd, etc.) plugin that also outputs "something" below the login form
 * For example, if you're using the WP-OpenID plugin, then the login form will display both the "Semisecure" message and the OpenID textbox below the login form
 *   This option will explicitly let you choose if the "Semisecure" message is displayed before or after the OpenID textbox
 * A smaller number will cause the "Semisecure" message to be displayed earlier (before "something else"), and a larger number will cause the message to be displayed later (after "something else")
 *   This number is relative to that "something else"
 * This is an integer, and the default value is 10 (which corresponds to WordPress' default value for plugin hooks)
 */
if (!defined('SEMISECURELOGIN_REIMAGINED__LOGIN_FORM_MESSAGE_POSITION'))
	define('SEMISECURELOGIN_REIMAGINED__LOGIN_FORM_MESSAGE_POSITION', 10);

/**
 * For multisite installs, manage_network_cap is used to check to see if the user is a network admin (aka super admin)
 * For single and multisite installs, manage_options_cap is used to check to see if the user is an individual site admin
 * Most likely, you will not want to change these settings
 * Both of these are strings, and the default values are 'manage_network' and 'manage_options'
 */
if (!defined('SEMISECURELOGIN_REIMAGINED__MANAGE_NETWORK_CAP'))
	define('SEMISECURELOGIN_REIMAGINED__MANAGE_NETWORK_CAP', 'manage_network');
if (!defined('SEMISECURELOGIN_REIMAGINED__MANAGE_OPTIONS_CAP'))
	define('SEMISECURELOGIN_REIMAGINED__MANAGE_OPTIONS_CAP', 'manage_options');



require_once(dirname(__FILE__).'/classes/SemisecureLoginReimagined.php');
new SemisecureLoginReimagined; // create an instance of the main class
?>