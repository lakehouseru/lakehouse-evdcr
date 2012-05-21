<?php
if( !is_admin() ) {

	/* Start of: Storefront */

	function wpsc_the_custom_fields( $args = null ) {

		global $wpsc_cf;

		$position = get_option( $wpsc_cf['prefix'] . '_position' );
		if( $args )
			wpsc_cf_get_value( $args );
		else if( $position == 'manual' )
			wpsc_cf_html_product();

	}

	function wpsc_cf_show_title() {

		global $wpsc_cf;

		$display_title = get_option( $wpsc_cf['prefix'] . '_display_title' );
		return $display_title;

	}

	function wpsc_cf_title() {

		global $wpsc_cf;

		$output = '';
		$output = get_option( $wpsc_cf['prefix'] . '_title_text' );
		if( $output )
			echo $output;

	}

	function wpsc_cf_get_title() {

		global $wpsc_cf;

		$output = '';
		$output = get_option( $wpsc_cf['prefix'] . '_title_text' );
		if( $output )
			return $output;

	}

	function wpsc_cf_label( $custom_field ) {

		$output = '';
		$output = $custom_field['name'];
		if( $output )
			echo $output;

	}

	function wpsc_cf_get_label( $custom_field ) {

		$output = '';
		$output = $custom_field['name'];
		if( $output )
			return $output;

	}

	function wpsc_cf_get_value( $args = null ) {

		global $wpsc_cf;

		if( $args ) {
			$defaults = array(
				'slug' => '',
			);
			$args = wp_parse_args( $args, $defaults );
			extract( $args, EXTR_SKIP );
			foreach( $args as $key => $arg ) {
				switch( $key ) {

					case 'slug':
						$data = unserialize( get_option( $wpsc_cf['prefix'] . '_data' ) );
						if( $data ) {
							foreach( $data as $key => $item ) {
								if( $item['slug'] == $args['slug'] ) {
									echo wpsc_cf_value( $item );
									break;
								}
							}
						}
						break;

				}
			}
		}

	}

	/* End of: Storefront */

}
?>