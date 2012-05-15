=== Semisecure Login Reimagined ===
Contributors: laceous
Tags: admin, login, rsa, encryption, security, password, public-key, secret-key, aes, marc4, rc4, rabbit
Requires at least: 3.1
Tested up to: 3.1
Stable tag: trunk

"Re-imagined" version of Semisecure Login that uses public and secret-key encryption to encrypt passwords when logging in.

== Description ==

Semisecure Login Reimagined increases the security of the login process by using a combination of public and secret-key encryption to encrypt the password on the client-side when a user logs in. JavaScript is required to enable encryption. It is most useful for situations where SSL is not available, but the administrator wishes to have some additional security measures in place without sacrificing convenience.

This plugin is a "re-imagining" of the original [Semisecure Login](http://wordpress.org/extend/plugins/semisecure-login/ "Semisecure Login") (which used one-way MD5 hashing). This version works with the new phpass hashed passwords that WordPress uses, as well as maintaining backwards compatibility with the older (pre WordPress 2.5) MD5 hashed passwords. Theoretically, it will also work with any other hashing algorithm (because this plugin simply adds an extra layer in the process rather than trying to authenticate anything itself).

This plugin requires PHP to be compiled with openssl support, which is a pretty standard option for most hosts.

== Screenshots ==

1. The login form displaying "Semisecure Login is enabled."

== Installation ==

1. Upload the entire `semisecure-login-reimagined/` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Optionally, navigate to the settings page to re-generate the RSA keypair (a keypair will automatically be generated when you first activate the plugin), choose a secret-key algorithm, uninstall the plugin, or check to see if there were any errors

* If upgrading manually, make sure to disable and then re-enable the plugin (upgrading through the admin interface will do this automatically)
* For best results, make sure your blog's character encoding is set to UTF-8
* If using a caching plugin, it's a good idea to clear the cache anytime you upgrade or change the plugin's settings

== Frequently Asked Questions ==

= How does this work? =

A user attempts to log in via the login page. If JavaScript is enabled, a secret-key is generated and used to encrypt the password along with a nonce, the public-key encrypts the secret-key, and the original (unencrypted) password is not sent. The server decrypts the secret-key with the private-key which is used to decrypt the password+nonce. The nonce is verified before handing the password over to WordPress for verification.

If JavaScript is not enabled, the password is sent in cleartext just like normal. This is inherently insecure over plaintext channels, but it is the default behavior of WordPress.

= How do I know this plugin is working? =

When the login form is displayed, the message "Semisecure Login is enabled" will appear underneath the Username and Password fields. If for some reason it isn't working (i.e., if JavaScript is not enabled, or you're running a browser that doesn't support certain necessary JavaScript functions), the message will read, "Semisecure Login is not enabled! Please enable JavaScript and use a modern browser to ensure your password is encrypted."

Note: v2.0 adds support for encrypting passwords on the user administration pages. In this case, the message "Semisecure Login is enabled" will only appear if the option has been activated (and JavaScript is enabled). If not, then nothing will be displayed.

= Is this really secure? =

Short answer: No, but it's better than nothing.

Without SSL, you're going to be susceptible to replay attacks/session hijacking no matter what. What this means is that if someone is able to guess or learn the session ID of a logged-in user (which would be trivial to do in an unprotected wireless network), then essentially they could do anything to your WordPress site by masquerading as that user.

= So what's the point? =

The point of this is to prevent your password from being transmitted in the "clear." If someone is in a position where they can learn your session ID, under normal circumstances, they'd also be able to learn your password. The proper use of this plugin removes that possibility.

= How can I make my site REALLY secure? =

Use SSL. This means you'll have to have a dedicated IP (which usually costs additional money) and an SSL certificate (which is expensive for a "real" one, but if you're just using this for your own administration purposes, a "self-signed" certificate would probably suffice). Any more detail on these two things is beyond the scope of this document.

== Additional Info ==

In general...

* Semisecure Login Reimagined 3.2.x is tested to work with:
 * WP 3.1.x
 * PHP 4.3.x - 5.3.x
* Semisecure Login Reimagined 3.1.x was tested to work with:
 * WP 2.8.x - 3.0.x
 * PHP 4.3.x - 5.3.x
* Semisecure Login Reimagined 3.0.x was tested to work with:
 * WP 2.7.x - 2.9.x
 * PHP 4.3.x - 5.3.x
* Semisecure Login Reimagined 2.x was tested to work with:
 * WP 2.2.x - 2.8.x
 * PHP 4.2.x - 5.2.x
* Semisecure Login Reimagined 1.x was tested to work with:
 * WP 2.1.x - 2.7.x
 * PHP 4.3.x - 5.2.x

** See the readme.txt file for each version for more specific information

== Changelog ==

= 3.2.0 =
* Updates to deal with the new network admin area for multisite installs
* Bump required WP version to 3.1
= 3.1.0.4 =
* Try to decrypt the password(s) as early as possible during the init hook
= 3.1.0.3 =
* Re-hash of 3.1.0.2
= 3.1.0.2 =
* Update $_REQUEST as well as $_POST
= 3.1.0.1 =
* Modify super admin menu slug
= 3.1.0 =
* Add WP 3.0 multisite integration. For a multisite install:
 * the settings are applied to all of the sites in your network (as long as the plugin is activated on the site in question)
 * only super-admins can modify these global settings
 * optionally, you can also enable a few override settings on each individual site in your network
* Remove JavaScript keypair generation
* Password encryption on the user administration pages is now turned on by default
* Remove uninstall tab on the settings page (the WP uninstall.php file is still available)
* Change default RSA keypair size to 2048 bits
* Prefix generic variable/function names in the jsbn library with "jsbn_" (hopefully all the names should be unique and not conflict with anything in the global scope anymore)
* Add a new option to globally force UTF-8 (multisite only)
* Remove public exponent option when generating new keypair
* AdvancedOptions file removed, advanced options have been moved to "defines"
* You can now display the custom integration instructions in a public page with this shortcode: [semisecurelogin_reimagined_integration]
* Bump required WP version to 2.8
= 3.0.8.4 =
* Fix typo in admin_head JavaScript
* Verify WP 2.9 works
= 3.0.8.3 =
* JavaScript update (simplify integration)
= 3.0.8.2 =
* Keypair generation debug info available via JavaScript
= 3.0.8.1 =
* Few small updates
= 3.0.8 =
* Update crypto-js to 2.0.0
* Update crypto-js PHP implementation
* Update jsbn library to 1.1
* Other small updates
= 3.0.7 =
* Add icon for Ozh Admin Drop Down Menu (thanks famfamfam)
= 3.0.6.3 =
* Revert earlier non-fix
= 3.0.6.2 =
* Fix small bug
= 3.0.6.1 =
* Remove legacy functions
= 3.0.6 =
* Don't start the session if nonces are disabled
= 3.0.5 =
* Fix version method bug
* New nonce admin option
= 3.0.4 =
* Simplify JavaScript
= 3.0.3 =
* Update crypto-js PHP implementation
* Add Rabbit cipher (requires 64-bit PHP)
= 3.0.2 = 
* Added more debugging
= 3.0.1 =
* Couple small updates
= 3.0 =
* Now using a combination of public and secret-key encryption (any password length is now supported)
* Now supports UTF-8 passwords (for best results, your blog's charset should be set to UTF-8)
* Updated the settings page
* Debugging info can be displayed if the keypair is not generating correctly
* Changed/simplified the integration API
* WP2.7 is now required
= 2.1.2 =
* Update options on the keypair generation form
= 2.1.1 =
* Updated the settings link on the plugins page
* Updated the JavaScript keypair generation page
= 2.1.0 =
* The alternative keypair generation method no longer requires PHP 5.2
= 2.0.1 =
* The nonce is now loaded in dynamically via JavaScript. This should make this plugin compatible with caching plugins.
= 2.0 =
* jQuery is now being utilized
* Split the plugin into multiple parts
 * RSA key creation and decryption (enabled by activating the plugin)
 * Password encryption on the login page (enabled on the settings page, requires WP2.2 or higher, enabled by default)
 * Password encryption on the user administration pages (new to v2.0, enabled on the settings page, requires WP2.7 or higher, only displays the "Semisecure" message if JavaScript is enabled)
* Added integration support for other plugin authors (see the included help file)
* Added JavaScript keypair generation
* WP2.2 is now required (dropping support for WP2.1)
= 1.4.0 =
* Added support for I18n (send me your translated po/mo files if you want them included with this plugin by default)
= 1.3.1 =
* Updated how the nonce is generated
= 1.3.0 =
* Added a few advanced options which can be set directly in the new AdvancedOptions file, added new WP2.7 uninstall.php file, code restructuring, other smallish updates
= 1.2.4 =
* Added 3072-bit option, other smallish updates
= 1.2.3 =
* Only call session_start for the login page
= 1.2.2 =
* The settings page is now available to users with a capability of 'manage_options' rather than a user-level of 8
= 1.2.1 =
* No longer sends the password length when logging in
= 1.2.0 =
* WP2.6 updates, code restructuring
= 1.1.0 =
* Additional keypair generation method that works with PHP safe mode enabled and/or the PHP execution functions disabled (this new method requires at least PHP 5.2.0)
= 1.0.3 =
* JavaScript updates
= 1.0 =
* Initial Release of this "re-imagined" version