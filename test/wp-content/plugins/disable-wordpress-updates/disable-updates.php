<?php
/*
Plugin Name: Disable WordPress Updates
Description: Disables the theme, plugin and core update checking, the related cronjobs and notification system.
Plugin URI:  http://www.officetrend.de/1793/plugin-disable-wordpress-updates-theme-plugin-und-core/
Version:     1.2
Author:      Tanja Preu&szlig;e 
Author URI:  http://www.officetrend.de/

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

*/

//Disable Theme Updates
# 2.8 to 3.0:
remove_action( 'load-themes.php', 'wp_update_themes' );
remove_action( 'load-update.php', 'wp_update_themes' );
remove_action( 'admin_init', '_maybe_update_themes' );
remove_action( 'wp_update_themes', 'wp_update_themes' );
add_filter( 'pre_transient_update_themes', create_function( '$a', "return null;" ) );
wp_clear_scheduled_hook( 'wp_update_themes' );

# 3.0:
remove_action( 'load-update-core.php', 'wp_update_themes' );
add_filter( 'pre_site_transient_update_themes', create_function( '$a', "return null;" ) );
wp_clear_scheduled_hook( 'wp_update_themes' );


//Disable Plugin Updates
# 2.8 to 3.0:
remove_action( 'load-plugins.php', 'wp_update_plugins' );
remove_action( 'load-update.php', 'wp_update_plugins' );
remove_action( 'admin_init', '_maybe_update_plugins' );
remove_action( 'wp_update_plugins', 'wp_update_plugins' );
add_filter( 'pre_transient_update_plugins', create_function( '$a', "return null;" ) );
wp_clear_scheduled_hook( 'wp_update_plugins' );

# 3.0:
remove_action( 'load-update-core.php', 'wp_update_plugins' );
add_filter( 'pre_site_transient_update_plugins', create_function( '$a', "return null;" ) );
wp_clear_scheduled_hook( 'wp_update_plugins' );


//Diasable Core Updates
# 2.8 to 3.0:
remove_action( 'wp_version_check', 'wp_version_check' );
remove_action( 'admin_init', '_maybe_update_core' );
add_filter( 'pre_transient_update_core', create_function( '$a', "return null;" ) );
wp_clear_scheduled_hook( 'wp_version_check' );

# 3.0:
add_filter( 'pre_site_transient_update_core', create_function( '$a', "return null;" ) );
wp_clear_scheduled_hook( 'wp_version_check' );
?>