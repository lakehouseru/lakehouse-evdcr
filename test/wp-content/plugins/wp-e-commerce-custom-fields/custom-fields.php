<?php
/*
Plugin Name: WP e-Commerce - Custom Fields
Plugin URI: http://www.visser.com.au/wp-ecommerce/plugins/custom-fields/
Description: Add and manage custom Product meta details within WP e-Commerce.
Version: 1.4.5
Author: Visser Labs
Author URI: http://www.visser.com.au/about/
Contributor: Ryan Waggoner
Contributor URI: http://ryanwaggoner.com/
Contributor: Kleber Lopes da Silva
Contributor URI: http://gameplaceholder.blogspot.com/
License: GPL2
*/

load_plugin_textdomain( 'wpsc_cf', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

include_once( 'includes/functions.php' );

include_once( 'includes/common.php' );

switch( wpsc_get_major_version() ) {

	case '3.7':
		include_once( 'includes/release-3_7.php' );
		break;

	case '3.8':
		include_once( 'includes/release-3_8.php' );
		break;

}

$wpsc_cf = array(
	'filename' => basename( __FILE__ ),
	'dirname' => basename( dirname( __FILE__ ) ),
	'abspath' => dirname( __FILE__ ),
	'relpath' => basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ ),
	'prefix_separator' => '_'
);

$wpsc_cf['prefix'] = 'wpsc_cf';
$wpsc_cf['name'] = __( 'Custom Fields for WP e-Commerce', 'wpsc_cf' );
$wpsc_cf['menu'] = __( 'Attributes', 'wpsc_cf' );

if( is_admin() ) {

	/* Start of: WordPress Administration */

	include_once( 'includes/install.php' );
	register_activation_hook( __FILE__, 'wpsc_cf_install' );

	include_once( dirname( __FILE__ ) . '/includes/update.php' );

	function wpsc_cf_html_page() {

		global $wpdb, $wpsc_cf;

		wpsc_cf_template_header();
		$action = wpsc_get_action();
		switch( $action ) {

			case 'update':
				$position = $_POST['position'];
				$layout = $_POST['layout'];
				$display_title = $_POST['display_title'];
				$title_text = $_POST['title_text'];

				update_option( $wpsc_cf['prefix'] . '_position', $position );
				update_option( $wpsc_cf['prefix'] . '_layout', $layout );
				update_option( $wpsc_cf['prefix'] . '_display_title', $display_title );
				update_option( $wpsc_cf['prefix'] . '_title_text', $title_text );

				$message = __( 'Settings updated', 'wpsc_cf' );
				$output = '<div class="updated settings-error"><p><strong>' . $message . '.</strong></p></div>';
				echo $output;

				wpsc_cf_manage_form();
				break;

			case 'delete':
				$id = $_GET['id'];
				$data = unserialize( get_option( $wpsc_cf['prefix'] . '_data' ) );
				unset( $data[$id] );
				$data = serialize( $data );

				update_option( $wpsc_cf['prefix'] . '_data', $data );
				unset( $data );

				$message = __( 'Attribute deleted', 'wpsc_cf' );
				$output = '<div class="updated settings-error"><p><strong>' . $message . '.</strong></p></div>';
				echo $output;

				wpsc_cf_manage_form();
				break;

			case 'edit-confirm':
				$id = $_POST['custom-field-id'];
				$name = $_POST['custom-field-name'];
				$slug = $_POST['custom-field-slug'];
				$type = $_POST['custom-field-type'];
				$order = $_POST['custom-field-order'];
				$prefix = $_POST['custom-field-prefix'];
				$suffix = $_POST['custom-field-suffix'];
				$show_name = $_POST['custom-field-show-name'];
				if( isset( $id ) && $name && $slug && $type ) {
					$options = $_POST['custom-field-options'];
					$description = $_POST['custom-field-description'];
					$field = array();
					$field[] = array(
						'name' => $name, 
						'slug' => $slug, 
						'type' => $type, 
						'options' => $options, 
						'order' => $order, 
						'description' => $description,
						'prefix' => $prefix,
						'suffix' => $suffix,
						'show_name' => $show_name
					);
					$data = unserialize( get_option( $wpsc_cf['prefix'] . '_data' ) );
					$data[$id]['name'] = $name;
					$data[$id]['slug'] = $slug;
					$data[$id]['type'] = $type;
					$data[$id]['order'] = $order;
					$data[$id]['prefix'] = $prefix;
					$data[$id]['suffix'] = $suffix;
					$data[$id]['show_name'] = $show_name;
					if( $type == 'dropdown' || $type == 'checkbox' || $type == 'radio' )
						$data[$id]['options'] = $options;
					$data[$id]['description'] = $description;
					$data = serialize( $data );

					update_option( $wpsc_cf['prefix'] . '_data', $data );
					unset( $data );

					$message = __( 'Attribute updated', 'wpsc_cf' );
					$output = '<div class="updated settings-error"><p><strong>' . $message . '.</strong></p></div>';
				} else {
					$message = '<strong>' . __( 'ERROR', 'wpsc_cf' ) . '</strong>: ' . __( 'A required field was not filled. Please ensure required fields are filled.', 'wpsc_cf' );
					$output = '<div class="error settings-error"><p>' . $message . '</strong></p></div>';
				}
				echo $output;

				wpsc_cf_manage_form();
				break;

			case 'new-confirm':
				$name = $_POST['custom-field-name'];
				$slug = $_POST['custom-field-slug'];
				$type = $_POST['custom-field-type'];
				$order = $_POST['custom-field-order'];
				$prefix = $_POST['custom-field-prefix'];
				$suffix = $_POST['custom-field-suffix'];
				$show_name = $_POST['custom-field-show-name'];
				if( $name && $type ) {
					if( !$slug ) {
						$slug_filters = array( '(', ')' );
						$slug = str_replace( $slug_filters, '', $name );
						$slug = strtolower( str_replace( ' ', '-', $slug ) );
					}
					$description = $_POST['custom-field-description'];
					if( get_option( $wpsc_cf['prefix'] . '_data' ) ) {
						$data = unserialize( get_option( $wpsc_cf['prefix'] . '_data' ) );
						$field = array(
							'name' => $name, 
							'slug' => $slug, 
							'type' => $type, 
							'order' => $order, 
							'description' => $description,
							'prefix' => $prefix,
							'suffix' => $suffix,
							'show_name' => $show_name
						);
						$data[] = $field;
						$data = serialize( $data );
						update_option( $wpsc_cf['prefix'] . '_data', $data );
					} else {
						$data = array();
						$data[] = array(
							'name' => $name, 
							'slug' => $slug, 
							'type' => $type, 
							'order' => $order, 
							'description' => $description,
							'prefix' => $prefix,
							'suffix' => $suffix,
							'show_name' => $show_name
						);
						$data = serialize( $data );
						update_option( $wpsc_cf['prefix'] . '_data', $data );
					}
					unset( $data );

					if( $type == 'dropdown' || $type == 'checkbox' || $type == 'radio' )
						$message = __( 'Attribute saved, you\'ll now need to define the Options for this field', 'wpsc_cf' );
					else
						$message = __( 'Attribute saved', 'wpsc_cf' );
					$output = '<div class="updated settings-error"><p><strong>' . $message . '.</strong></p></div>';
				} else {
					$message = '<strong>' . __( 'ERROR', 'wpsc_cf' ) . '</strong>: ' . __( 'A required field was not filled. Please ensure required fields are filled.', 'wpsc_cf' ) . '.</strong>';
					$output = '<div class="error settings-error"><p>' . $message . '</p></div>';
				}
				echo $output;

				wpsc_cf_manage_form();
				break;

			case 'edit':
			case 'new':
				if( $action == 'edit' ) {
					$id = $_GET['id'];
					$data = unserialize( get_option( $wpsc_cf['prefix'] . '_data' ) );
					$field = $data[$id];
				}

				if( $action == 'edit' )
					$title = __( 'Edit Attribute', 'wpsc_cf' );
				else
					$title = __( 'Add New Attribute', 'wpsc_cf' );
				$options = wpsc_cf_custom_field_types();

				include( 'templates/admin/wpsc-admin_cf_settings_detail.php' );

				break;

			default:
				wpsc_cf_manage_form();
				break;

		}
		wpsc_cf_template_footer();

	}

	function wpsc_cf_manage_form() {

		global $wpsc_cf;

		$positions = wpsc_productpage_positions();

		$layouts = array();
		$layouts[] = array( 'table.php', __( 'Table', 'wpsc_cf' ) );
		$layouts[] = array( 'list-ordered.php', __( 'List - Ordered', 'wpsc_cf' ) );
		$layouts[] = array( 'list-unordered.php', __( 'List - Unordered', 'wpsc_cf' ) );

		$data = get_option( $wpsc_cf['prefix'] . '_data' );
		if( $data ) {
			if( wpsc_cf_is_serialized( $data ) )
				$data = unserialize( $data );
			$data = wpsc_cf_custom_field_sort( $data, 'order' );
		}

		include( 'templates/admin/wpsc-admin_cf_settings.php' );

	}

	/* End of: WordPress Administration */

} else {

	/* Start of: Storefront */

	include_once( 'includes/template.php' );
	include_once( 'includes/legacy.php' );

	$position = get_option( $wpsc_cf['prefix'] . '_position' );

	if( $position )
		add_action( $position, 'wpsc_cf_init' );
	else
		add_action( 'wpsc_product_addon_after_descr', 'wpsc_cf_init' );

	/* End of: Storefront */

}
?>