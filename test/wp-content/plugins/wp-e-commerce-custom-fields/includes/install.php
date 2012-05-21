<?php
function wpsc_cf_install() {

	wpsc_cf_create_options();

}

// Trigger the creation of Admin options for this Plugin
function wpsc_cf_create_options() {

	if( !get_option( 'wpsc_cf_position' ) )
		add_option( 'wpsc_cf_position', 'wpsc_product_addon_after_descr' );

	if( !get_option( 'wpsc_cf_layout' ) )
		add_option( 'wpsc_cf_layout', 'table.php' );

	if( !get_option( 'wpsc_cf_display_title' ) )
		add_option( 'wpsc_cf_display_title', 1 );

	if( !get_option( 'wpsc_cf_title_text' ) )
		add_option( 'wpsc_cf_title_text', __( 'Additional Details', 'wpsc_cf' ) );

}
?>