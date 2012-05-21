<?php
/*

Filename: common.php
Description: common.php loads commonly accessed functions across the Visser Labs suite.

- wpsc_vl_migrate_prefix_options
- wpsc_productpage_positions

- wpsc_get_action
- wpsc_get_major_version

*/

if( is_admin() ) {

	/* Start of: WordPress Administration */

	include_once( 'common-dashboard_widgets.php' );

	if( !function_exists( 'wpsc_vl_migrate_prefix_options' ) ) {

		/**
		* Migrates outdated WordPress options to their new Plugin prefix
		*
		* @param array $options List of WordPress options.
		* @param string $old_prefix Existing Plugin prefix.
		* @param string $new_prefix Existing Plugin prefix.
		* @return boolean "No! Try not. Do. Or do not. There is no try." - Yoda
		*/
		function wpsc_vl_migrate_prefix_options( $options, $old_prefix, $new_prefix ) {

			foreach( $options as $option ) {
				if( $option['old_name'] && isset( $option['new_name'] ) ) {
					add_option( $new_prefix . '_' . $option['new_name'], get_option( $old_prefix . '_' . $option['old_name'] ) );
					$success = true;
				} else if( $option['old_name'] ) {
					add_option( $new_prefix . '_' . $option['old_name'], get_option( $old_prefix . '_' . $option['old_name'] ) );
					$success = true;
				}
				delete_option( $old_prefix . '_' . $option['old_name'] );
			}
			if( $success )
				return true;

		}

	}

	if( !function_exists( 'wpsc_productpage_positions' ) ) {

		function wpsc_productpage_positions() {

			$positions = array();

			$positions[] = array( 'wpsc_product_before_description', __( 'Before Description', 'wpsc_at' ) );
			$positions[] = array( 'wpsc_product_addons', __( 'Addons', 'wpsc_at' ) );
			switch( wpsc_get_major_version() ) {

				case '3.7':
					$positions[] = array( 'wpsc_product_addon_after_descr', __( 'After Description', 'wpsc_at' ) );
					break;

			}
			$positions[] = array( 'manual', __( 'Manual', 'wpsc_at' ) );

			return $positions;

		}

	}

	/* End of: WordPress Administration */

}

if( !function_exists( 'wpsc_get_action' ) ) {

	function wpsc_get_action( $switch = false ) {

		if( $switch ) {

			if( isset( $_GET['action'] ) )
				$action = $_GET['action'];
			else if( !isset( $action ) && isset( $_POST['action'] ) )
				$action = $_POST['action'];
			else
				$action = false;

		} else {

			if( isset( $_POST['action'] ) )
				$action = $_POST['action'];
			else if( !isset( $action ) && isset( $_GET['action'] ) )
				$action = $_GET['action'];
			else
				$action = false;

		}
		return $action;

	}

}

if( !function_exists( 'wpsc_get_major_version' ) ) {

	function wpsc_get_major_version() {

		$version = get_option( 'wpsc_version' );
		return substr( $version, 0, 3 );

	}

}
?>