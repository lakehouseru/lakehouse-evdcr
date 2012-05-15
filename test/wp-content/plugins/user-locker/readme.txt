=== Plugin Name ===
Contributors: sirzooro
Tags: access, admin, administration, locking, login, security, user, users, account, accounts, disabling, lock, disable
Requires at least: 2.7
Tested up to: 3.2.9
Stable tag: 1.2

This plugin locks user account after given number of incorrect login attempts. This makes brute force and dictionary attacks nearly impossible.

== Description ==

Default Wordpress installation is vulnerable to brute force and dictionary attacks, because there is no limit how many times user can use invalid password before finding the correct one. This plugin closes this security hole by introducing maximum number of invalid login attempts. When someone exceeds this number, his/her account becomes locked, and can be unlocked only by requesting new password (using Lost Password option) or asking Admin for help (he/she can do it too). This makes brute force and dictionary attacks nearly impossible.

You can also disable selected user accounts, so users will not be able to log in even if they will know password. You can use this feature to ban selected users.

You can also enter lock/disable reason for your further reference. When account is automatically blocked, plugin can automatically add lock reason (configurable). By default reason text is displayed on User List only; you can also display it for user after blocked login attempt. You have also option to keep some of them private - just start the reason text with '@' (AT sign).

Plugin also provides few public functions and actions for simpler integration with other plugins - see FAQ for more details.

Available translations:

* English
* Polish (pl_PL) - done by me
* German (de_DE) - thanks [GhostLyrics](http://firefly.menkisys.de/blog)
* French (fr_FR) - thanks Gilles
* Russian (ru_RU) - thanks Fat Cow
* Belorussian (be_BY) - thanks [ilyuha](http://antsar.info/)
* Italian (it_IT) - thanks [Alessandro](http://alessandrostella.it/)
* Dutch (nl_NL) - thanks [Rene](http://wpwebshop.com/)
* Arabic (ar_AR) - thanks [zaher kadour](http://linuxawi.com/)
* Turkish (tr_TR) - thanks [Dincer YAMANLAR](http://www.osmandincer.net/)

[Changelog](http://wordpress.org/extend/plugins/user-locker/changelog/)

== Installation ==

1. Upload `user-locker` directory to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Configure and enjoy :)

== Frequently Asked Questions ==

= How can I integrate my plugin with User Locker? =

User Locker provides special functions (placed in "Public functions" section in code). I recommend to use them instead of calling methods of `UserLocker` class directly - the latter ones may change without any notice. There are also few hooks which you can use too.

There are following functions and hooks available:

* `function user_locker_lock_user( $user_id, $reason = '' )` - Lock user account (user may unlock it by requesting new password). Parameters: User ID; New lock reason (may be empty string) or False to do not update lock reason. This function calls the `user_locker_lock_user` after locking user account with one parameter - User ID;
* `function user_locker_unlock_user( $user_id, $reason = false )` - Unlock user account. Parameters: User ID; New lock reason (may be empty string) or False to do not update lock reason. This function calls the `user_locker_unlock_user` after unlocking user account with one parameter - User ID;
* `function user_locker_disable_user( $user_id, $reason = '' )` - Disable user account (user cannot enable it, only admin can do this). Parameters: User ID; New disable reason (may be empty string) or False to do not update disable reason. This function calls the `user_locker_disable_user` after disabling user account with one parameter - User ID;
* `function user_locker_enable_user( $user_id, $reason = false )` - Enable user account. Parameters: User ID; New disable reason (may be empty string) or False to do not update disable reason. This function calls the `user_locker_enable_user` after enabling user account with one parameter - User ID;

== Screenshots ==

1. Error message when User Account becomes locked after too many invalid login attempts.
2. Error message when User Account is disabled.
3. Edit User page - options which allows to lock and disable User Account.
4. Column with User Account status on User List (requires WP 2.8+)

== Changelog ==

= 1.2 =
* Allow to enter reason why user was locked or disabled;
* Lock/Disable status can be displayed in single column as before (reason text is put in tooltip) or in two columns;
* Added public functions and actions for simpler integration with other plugins (see plugin FAQ for more details);
* Fix: plugin may not work if custom authentication method is user;
* Marked as compatible with WP 3.2.x

= 1.1.13 =
* Added Turkish translation (thanks Dincer YAMANLAR)

= 1.1.12 =
* Added Arabic translation (thanks zaher kadour)

= 1.1.11 =
* Added Dutch translation (thanks Alessandro)

= 1.1.10 =
* Added icon to settings page

= 1.1.9 =
* Added Italian translation (thanks Alessandro);
* Code cleanup

= 1.1.8 =
* Code cleanup

= 1.1.7 =
* Marked as compatible with WP 2.9.x

= 1.1.6 =
* Marked as compatible with WP 2.8.5

= 1.1.5 =
* Added Belorussian translation (thanks ilyuha)

= 1.1.4 =
* Added Russian translation (thanks Fat Cow)

= 1.1.3 =
* Added French translation (thanks Gilles)

= 1.1.2 =
* Added German translation (thanks GhostLyrics)
* Marked plugin as tested with WP 2.8.1

= 1.1.1 =
* Marked plugin as tested with WP 2.8

= 1.1 =
* Added option to disable User Accounts (such users cannot login even if they know valid password);
* Administrator can change Locked and Disabled statuses of User Account by editing user profile;
* Display User Account status on User list (requires WP 2.8+).

= 1.0 =
* Initial version
