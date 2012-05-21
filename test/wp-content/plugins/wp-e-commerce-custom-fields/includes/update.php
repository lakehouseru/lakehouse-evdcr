<?php
function wpsc_cf_admin_notice() {

	if( wpsc_cf_check_options_exist() )
		wpsc_cf_update_options();

}
add_action( 'admin_notices', 'wpsc_cf_admin_notice' );

function wpsc_cf_update_options() {

	$options = array();
	$options[] = array( 'old_name' => 'data' );

	$old_prefix = 'vl_wpsccf';
	$new_prefix = 'wpsc_cf';

	wpsc_vl_migrate_prefix_options( $options, $old_prefix, $new_prefix );

}
?>