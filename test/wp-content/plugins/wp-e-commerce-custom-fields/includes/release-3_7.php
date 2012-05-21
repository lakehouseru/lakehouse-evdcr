<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	/* WordPress Adminstration Menu */
	function wpsc_cf_add_modules_admin_pages( $page_hooks, $base_page ) {

		global $wpsc_cf;

		$page_hooks[] = add_submenu_page( $base_page, $wpsc_cf['name'], $wpsc_cf['menu'], 7, 'wpsc_cf', 'wpsc_cf_html_page' );
		return $page_hooks;

	}
	add_filter( 'wpsc_additional_pages', 'wpsc_cf_add_modules_admin_pages', 10, 2 );

	function wpsc_cf_init_meta_box() {

		global $wpsc_cf;

		$pagename = 'store_page_wpsc-edit-products';
		add_meta_box( 'wpsc_cf_meta_box', $wpsc_cf['name'], 'wpsc_cf_meta_box', $pagename, 'normal', 'high' );

	}
	add_action( 'admin_menu', 'wpsc_cf_init_meta_box' );

	function wpsc_cf_add_to_product_form( $order ) {

		if( array_search( 'wpsc_cf_meta_box', (array)$order ) === false )
			$order[] = 'wpsc_cf_meta_box';
		return $order;

	}
	add_filter( 'wpsc_products_page_forms', 'wpsc_cf_add_to_product_form' );

	function wpsc_cf_meta_box( $product_data = array() ) {

		global $wpdb, $wpsc_cf, $closed_postboxes;

		$data = unserialize( get_option( $wpsc_cf['prefix'] . '_data' ) ); ?>
<div id="wpsc_product_custom_fields" class="postbox <?php echo( ( array_search( 'wpsc_cf_meta_box', (array)$product_data['closed_postboxes'] ) !== false) ? 'closed"' : '' ); ?>" <?php echo( ( array_search( 'wpsc_cf_meta_box', (array)$product_data['hidden_postboxes'] ) !== false ) ? ' style="display: none;"' : '' ); ?>>
	<h3 class="hndle"><?php echo $wpsc_cf['name']; ?></h3>
	<div class="inside">
		<div>
			<p><span class="howto"><?php echo $wpsc_cf['name']; ?></span></p>
<?php
		if( $data ) {
			$data = wpsc_cf_custom_field_sort( $data, 'order' );
			$i = 0;
			foreach( $data as $field ) { ?>
			<label><?php echo $field['name']; ?>:</label><br />
<?php
				switch( $field['type'] ) {

					case 'input':
						$output = '
						<input type="text" id="wpsc_cf_product_' . $i . '" name="productmeta_values[' . $field['slug'] . ']" value="' . get_product_meta( $product_data['id'], $field['slug'], true ) . '" size="32" />
						<span class="howto">' . $field['description'] . '</span>';
						break;

					case 'textarea':
						$output = '
<textarea id="wpsc_cf_product_' . $i . '" name="productmeta_values[' . $field['slug'] . ']" rows="3" cols="30">' . get_product_meta( $product_data['id'], $field['slug'], true ) . '</textarea>
<span class="howto">' . $field['description'] . '</span>';
						break;

					case 'dropdown':
						$output = '
<select id="wpsc_cf_product_' . $i . '" name="productmeta_values[' . $field['slug'] . ']">
	<option></option>';
						if( $field['options'] ) {
							$options = explode( '|', $field['options'] );
							foreach( $options as $option )
								$output .= '
	<option value="' . $option . '"' . selected( $option, get_product_meta( $product_data['id'], $field['slug'], true ), false ) . '>' . $option . '&nbsp;</option>' . "\n";
						}
							$output .= '
</select>
<span class="howto">' . $field['description'] . '</span>';
						break;

					case 'checkbox':
					case 'radio':
						$output = '
<fieldset id="wpsc_cf_product_fieldset_' . $i . '">';
						if( $field['options'] ) {
							$output .= print_r( get_product_meta( $product_data['id'], $field['slug'] ) );
							$options = explode( '|', $field['options'] );
							foreach( $options as $option )
								$output .= '
	<label><input type="' . $field['type'] . '" id="wpsc_cf_product_' . $i . '" name="productmeta_values[' . $field['slug'] . ']" value="' . $option . '"' . checked( $option, get_product_meta( $product_data['id'], $field['slug'], true ), false ) . ' />&nbsp;' . $option . '</label><br />' . "\n";
						}
							$output .= '
</fieldset>
<span class="howto">' . $field['description'] . '</span>';
						break;

					case 'radio':
						break;

				}
				echo $output; ?>
			<br />
<?php
				$i++;
			}
		} ?>
		</div>
	</div>
</div>
<?php
	}

	/* End of: WordPress Administration */

} else {

	/* Start of: Storefront */

	function wpsc_cf_init() {

		global $wpsc_query, $wpsc_cf;

		$position = get_option( $wpsc_cf['prefix'] . '_position' );

		if( $wpsc_query->is_single ) {
			if( $position <> 'manual' )
				wpsc_cf_html_product();
		}

	}

	function wpsc_cf_html_product( $args = null ) {

		global $wpsc_cf, $wpsc_query;

		$data = unserialize( get_option( $wpsc_cf['prefix'] . '_data' ) );
		if( $data ) {
			$data = wpsc_cf_custom_field_sort( $data, 'order' );

			if( $args ) {
				$args_data = explode( '&', $args );
				$args_filter_data = array();
				for( $i = 0; $i <= ( count( $args_data ) - 1 ); $i++ ) {
					$args_filter_data[$i] = explode( '=', $args_data[$i] );
					if( in_array( 'slug', $args_filter_data[$i] ) ) {
						$args_filter_value = $args_filter_data[$i][1];
						foreach( $data as $field_id => $field ) {
							if( $args_filter_value == $field['slug'] ) {
								$data = array();
								$data[] = $field;
							}
						}
					}
				}
			}

			$layout = get_option( $wpsc_cf['prefix'] . '_layout' );
			$custom_fields = $data;

			if( $layout ) {
				if( file_exists( STYLESHEETPATH . '/wpsc-single_product_customfields_' . $layout ) )
					include( STYLESHEETPATH . '/wpsc-single_product_customfields_' . $layout );
				else
					include( $wpsc_cf['abspath'] . '/templates/store/wpsc-single_product_customfields_' . $layout );
			} else {
				include( $wpsc_cf['abspath'] . '/templates/store/wpsc-single_product_customfields_table.php' );
			}

		}

	}

	function wpsc_cf_has_value( $custom_field ) {

		$check = get_product_meta( wpsc_the_product_id(), $custom_field['slug'], true );
		if( $check )
			return true;

	}

	function wpsc_cf_value( $custom_field ) {

		$output = '';
		switch( $custom_field['type'] ) {

			case 'input':
			case 'dropdown':
			case 'checkbox':
			case 'radio':
				$output = stripcslashes( $custom_field['prefix'] ) . get_product_meta( wpsc_the_product_id(), $custom_field['slug'], true ) . stripslashes( $custom_field['suffix'] );
				break;

			case 'textarea':
				$output = stripcslashes( $custom_field['prefix'] ) . get_product_meta( wpsc_the_product_id(), $custom_field['slug'], true ) . stripslashes( $custom_field['suffix'] );
				$output = str_replace( "\n", '<br />', $output );
				break;

		}
		echo $output;

	}

	/* End of: Storefront */

}
?>