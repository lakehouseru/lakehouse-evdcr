<?php
/*
Plugin Name: Admin Customization
Version: 2.0.1
Description: Allows you to customize basic aspects of your Wordpress backend
Author: Alex Ciobica
Author URI: http://ciobi.ca/
Plugin URI: https://github.com/c10b10/wp-admin-customization/
Text Domain: admin-customization
Domain Path: /lang


Using the scbFramework (http://scribu.net/wordpress/scb-framework/) by Cristi Burca (http://scribu.net/)
Copyright (C) 2011 Alex Ciobica (alex.ciobica@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
( at your option ) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

// load scb
require dirname(__FILE__) . '/scb/load.php';

//= debug
//require dirname(__FILE__) . '/scb-debug.php';
//require dirname(__FILE__) . '/FirePHP.class.php';
//require dirname(__FILE__) . '/fb.php';

function _ac_init() {
	$textdomain = 'admin-customization';
	load_plugin_textdomain( $textdomain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	
	$dir = dirname(__FILE__);
	
	$options = new scbOptions( 'admin-customization', __FILE__, array(
		'favicon' => false,
		'login_logo' => false,
		'admin_logo' => false,
		'admin_footer_left' => false,
		'admin_footer_right' => false,
		'admin_logo_font_size' => 16,
		'widgets' => array(),
		'disabled_widgets' => '',
		'general_settings' => '',
		'style_settings' => array(),
	) );
	
	require_once $dir . '/core.php';
	// initialize the core
	AC_Core::init($options);

	// add admin page
	if ( is_admin() ) {
		require_once $dir . '/admin/admin.php';
		scbAdminPage::register( 'AC_Settings', __FILE__, $options );
	}
	
}
scb_init('_ac_init');


