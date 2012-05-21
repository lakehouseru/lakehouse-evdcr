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

		$pagename = 'wpsc-product';
		add_meta_box( 'wpsc_cf_meta_box', $wpsc_cf['menu'], 'wpsc_cf_meta_box', $pagename, 'normal', 'high' );

	}
	add_action( 'admin_menu', 'wpsc_cf_init_meta_box' );

	function wpsc_cf_add_to_product_form( $order ) {

		if( array_search( 'wpsc_cf_meta_box', (array)$order ) === false )
			$order[] = 'wpsc_cf_meta_box';
		return $order;

	}
	add_filter( 'wpsc_products_page_forms', 'wpsc_cf_add_to_product_form' );

	function wpsc_cf_meta_box() {

		global $post, $wpdb, $wpsc_cf, $closed_postboxes;

		$product_data = get_post_custom( $post->ID );
		$product_data['meta'] = maybe_unserialize( $product_data );
		foreach( $product_data['meta'] as $meta_key => $meta_value )
			$product_data['meta'][$meta_key] = $meta_value[0];
		$product_meta = maybe_unserialize( $product_data['_wpsc_product_metadata'][0] );

		$data = unserialize( get_option( $wpsc_cf['prefix'] . '_data' ) );
		if( $data ) {
			$data = wpsc_cf_custom_field_sort( $data, 'order' );
			$i = 0;
			foreach( $data as $field ) { ?>
<label for="wpsc_cf_product_<?php echo $i; ?>"><?php echo $field['name']; ?>:</label><br />
<?php
				switch( $field['type'] ) {

					case 'input':
						$output = '
<input type="text" id="wpsc_cf_product_' . $i . '" name="meta[' . WPSC_META_PREFIX . $field['slug'] . ']" value="' . get_product_meta( $post->ID, $field['slug'], true ) . '" size="32" />
<span class="howto">' . $field['description'] . '</span>';
						break;

					case 'textarea':
						$output = '
<textarea id="wpsc_cf_product_' . $i . '" name="meta[' . WPSC_META_PREFIX . $field['slug'] . ']" rows="3" cols="30">' . get_product_meta( $post->ID, $field['slug'], true ) . '</textarea>
<span class="howto">' . $field['description'] . '</span>';
						break;

					case 'dropdown':
						$output = '
<select id="wpsc_cf_product_' . $i . '" name="meta[' . WPSC_META_PREFIX . $field['slug'] . ']">
	<option></option>';
						if( $field['options'] ) {
							$options = explode( '|', $field['options'] );
							foreach( $options as $option )
								$output .= '
	<option value="' . $option . '"' . selected( $option, get_product_meta( $post->ID, $field['slug'], true ), false ) . '>' . $option . '&nbsp;</option>' . "\n";
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
							$options = explode( '|', $field['options'] );
							foreach( $options as $option )
								$output .= '
	<label><input type="' . $field['type'] . '" id="wpsc_cf_product_' . $i . '" name="meta[' . WPSC_META_PREFIX . $field['slug'] . ']" value="' . $option . '"' . checked( $option, get_product_meta( $post->ID, $field['slug'], true ), false ) . ' />&nbsp;' . $option . '</label><br />' . "\n";
						} else {
							$output .= '-';
						}
							$output .= '
</fieldset>
<span class="howto">' . $field['description'] . '</span>';
						break;

					case 'wysiwyg':
						$output = '
<script type="text/javascript">
	jQuery(document).ready( function () {
	jQuery("#wpsc_cf_product_' . $i . '").addClass("mceEditor");
	if ( typeof( tinyMCE ) == "object" && typeof( tinyMCE.execCommand ) == "function" ) {
	jQuery("#wpsc_cf_product_' . $i . '").wrap( "" );
	tinyMCE.execCommand("mceAddControl", false, "wpsc_cf_product_' . $i . '");
	}
	});
</script>
<div style="background-color:#fff;">
	<textarea id="wpsc_cf_product_' . $i . '" name="meta[' . WPSC_META_PREFIX . $field['slug'] . ']" rows="3" cols="30">' . wpautop( get_product_meta( $post->ID, $field['slug'], true ) ) . '</textarea>
</div>
<span class="howto">' . $field['description'] . '</span>';
					break;

				}
				echo $output; ?>
<br />
<?php
				$i++;
			}
		}
	}

	function wpsc_cf_update_product_meta( $product_id = null ) {

		$custom_fields = maybe_unserialize( get_option( 'wpsc_cf_data' ) );
		if( $custom_fields ) {
			$checkbox_fields = array();
			foreach( $custom_fields as $key => $custom_field ) {
				if( $custom_field['type'] == 'checkbox' )
					$checkbox_fields[] = $key;
			}
		}

		if( $checkbox_fields ) {
			foreach( $checkbox_fields as $checkbox_field ) {
/*
				if( $product_meta['_wpsc_' . $custom_fields[$checkbox_field]['slug']] ) {
					print_r( $product_meta['_wpsc_' . $custom_fields[$checkbox_field]['slug']] );
				}
*/
			}
		}

	}
	//add_action( 'wpsc_edit_product', 'wpsc_cf_update_product_meta', $product_id );

	/* Product Importer Deluxe integration */
	function wpsc_cf_pd_options_addons( $options ) {

		global $wpsc_cf;

		$custom_options = maybe_unserialize( get_option( $wpsc_cf['prefix'] . '_data' ) );
		if( $custom_options ) {
			foreach( $custom_options as $custom_option )
				$options[] = array( 'attribute_' . $custom_option['slug'], __( 'Attribute', 'wpsc_pd' ) . ' - ' . $custom_option['name'] );
		}
		return $options;

	}
	add_filter( 'wpsc_pd_options_addons', 'wpsc_cf_pd_options_addons', null, 1 );

	function wpsc_cf_pd_import_addons( $import, $csv_data ) {

		$import->custom_options = unserialize( get_option( $wpsc_cf['prefix'] . '_data' ) );
		if( $import->custom_options ) {
			foreach( $import->custom_options as $custom_option ) {
				if( isset( $csv_data['attribute_' . $custom_option['slug']] ) ) {
					$import->csv_custom[$custom_option['slug']] = array_filter( $csv_data['attribute_' . $custom_option['slug']] );
					$import->log .= "<br />>>> " . __( 'Attribute: ', 'wpsc_pd' ) . __( $custom_option['name'] . ' has been detected and grouped', 'wpsc_pd' );
				}
			}
		}
		return $import;

	}
	add_filter( 'wpsc_pd_import_addons', 'wpsc_cf_pd_import_addons', null, 2 );

	function wpsc_cf_pd_product_addons( $product, $import, $count ) {

		/* Attribute integration */
		if( $import->custom_options ) {
			foreach( $import->custom_options as $custom_option )
				if( isset( $import->csv_custom[$custom_option['slug']][$count] ) )
					$product->custom_fields[$custom_option['slug']] = $import->csv_custom[$custom_option['slug']][$count];
		}
		return $product;

	}
	add_filter( 'wpsc_pd_product_addons', 'wpsc_cf_pd_product_addons', null, 3 );

	function wpsc_cf_pd_create_product_log_addons( $import, $product ) {

		if( $import->custom_options ) {
			foreach( $import->custom_options as $custom_option ) {
				if( $product->custom_fields[$custom_option['slug']] )
					$import->log .= "<br />>>>>>> " . __( 'Setting ' . $custom_option['name'], 'wpsc_pd' );
			}
		}
		return $import;

	}
	add_filter( 'wpsc_pd_create_product_log_addons', 'wpsc_cf_pd_create_product_log_addons', null, 2 );

	/* End of: WordPress Administration */

} else {

	/* Start of: Storefront */

	function wpsc_cf_init() {

		global $wp_query, $wpsc_cf;

		$position = get_option( $wpsc_cf['prefix'] . '_position' );

		if( $wp_query->is_single ) {
			if( $position <> 'manual' )
				wpsc_cf_html_product();
		}

	}

	function wpsc_cf_html_product( $args = null ) {

		global $wpsc_cf;

		$data = unserialize( get_option( $wpsc_cf['prefix'] . '_data' ) );
		if( $data ) {
			$data = wpsc_cf_custom_field_sort( $data, 'order' );
			$custom_fields = $data;
			$layout = get_option( $wpsc_cf['prefix'] . '_layout' );
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

		global $post;

		$check = get_post_meta( $post->ID, '_wpsc_' . $custom_field['slug'], true );
		if( $check )
			return true;

	}

	function wpsc_cf_value( $custom_field ) {

		global $post;

		$output = '';
		switch( $custom_field['type'] ) {

			case 'input':
			case 'dropdown':
			case 'checkbox':
			case 'radio':
				$output = stripcslashes( $custom_field['prefix'] ) . get_post_meta( $post->ID, '_wpsc_' . $custom_field['slug'], true ) . stripslashes( $custom_field['suffix'] );
				break;

			case 'textarea':
			case 'wysiwyg':
				$output = stripcslashes( $custom_field['prefix'] ) . get_post_meta( $post->ID, '_wpsc_' . $custom_field['slug'], true ) . stripslashes( $custom_field['suffix'] );
				$output = str_replace( "\n", '<br />', $output );
				break;

		}
		echo $output;

	}

	/* End of: Storefront */

}
?>