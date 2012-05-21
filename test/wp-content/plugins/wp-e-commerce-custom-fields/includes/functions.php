<?php
if( is_admin() ) {

	/* Start of: WordPress Administration */

	function wpsc_cf_template_header() {

		global $wpsc_cf; ?>
<div class="wrap">
	<div id="icon-tools" class="icon32"><br /></div>
	<h2><?php echo $wpsc_cf['menu']; ?>
		<a href="admin.php?page=wpsc_cf&action=new" class="button add-new-h2"><?php _e( 'Add New', 'wpsc_cf' ); ?></a>
	</h2>
<?php
	}

	function wpsc_cf_template_footer() { ?>
</div>
<?php
	}

	function wpsc_cf_check_options_exist() {

		$prefix = 'vl_wpsccf';
		$sample = get_option( $prefix . '_data' );
		if( $sample )
			return true;

	}

	function wpsc_cf_is_serialized( $str ) {

		return( $str == serialize( false ) || @unserialize( $str ) !== false );

	}

	function wpsc_cf_return_type_label( $type ) {

		$options = wpsc_cf_custom_field_types();

		foreach( $options as $option ) {
			if( $option['name'] == $type ) {
				$label = $option['label'];
				break;
			}
		}
		return $label;

	}

	function wpsc_cf_custom_field_types() {

		$options = array();
		$options[] = array( 'name' => 'input', 'label' => 'Input' );
		$options[] = array( 'name' => 'textarea', 'label' => 'Textarea' );
		$options[] = array( 'name' => 'dropdown', 'label' => 'Dropdown' );
		$options[] = array( 'name' => 'wysiwyg', 'label' => 'Textarea (with Editor)' );
		$options[] = array( 'name' => 'checkbox', 'label' => 'Checkbox List' );
		$options[] = array( 'name' => 'radio', 'label' => 'Radio List' );

		return $options;

	}

	function wpsc_cf_pd_create_product_addons( $product, $import ) {

		if( $import->custom_options ) {
			foreach( $import->custom_options as $custom_option ) {
				if( $product->custom_fields[$custom_option['slug']] ) {
					switch( wpsc_get_major_version() ) {

						case '3.7':
							$wpdb->insert( $wpdb->prefix . 'wpsc_productmeta', array( 
								'product_id' => $product->ID,
								'meta_key' => $custom_option['slug'],
								'meta_value' => $product->custom_fields[$custom_option['slug']]
							) );
							break;

						case '3.8':
							update_product_meta( $product->ID, $custom_option['slug'], $product->custom_fields[$custom_option['slug']] );
							break;

					}
				}
			}
		}
		return $product;

	}
	add_filter( 'wpsc_pd_create_product_addons', 'wpsc_cf_pd_create_product_addons', null, 2 );

	function wpsc_cf_pd_merge_product_data_addons( $product_data, $product, $import ) {

		if( $product->ID ) {
			if( $import->custom_options ) {
				$custom_fields = array();
				foreach( $import->custom_options as $custom_option )
					$custom_fields[$custom_option['slug']] = get_product_meta( $product->ID, $custom_option['slug'], true );
				$product_data->custom_fields = $custom_fields;
			}
		}
		return $product_data;

	}
	add_filter( 'wpsc_pd_merge_product_data_addons', 'wpsc_cf_pd_merge_product_data_addons', null, 3 );

	function wpsc_cf_pd_merge_product_addons( $product, $import, $product_data ) {

		if( isset( $product->custom_fields ) && $product->custom_fields ) {
			foreach( $import->custom_options as $custom_option ) {
				if( $product->custom_fields[$custom_option['slug']] <> $product_data->custom_fields[$custom_option['slug']] ) {
					update_product_meta( $product->ID, $custom_option['slug'], $product->custom_fields[$custom_option['slug']] );
					$product->updated = true;
				}
			}
		}
		return $product;

	}
	add_filter( 'wpsc_pd_merge_product_addons', 'wpsc_cf_pd_merge_product_addons', null, 3 );

	function wpsc_cf_pd_merge_product_log_addons( $import, $product, $product_data ) {

		if( isset( $product->custom_fields ) && $product->custom_fields ) {
			foreach( $import->custom_options as $custom_option ) {
				if( $product->custom_fields[$custom_option['slug']] <> $product_data->custom_fields[$custom_option['slug']] )
					$import->log .= "<br />>>>>>> " . __( "Updating Custom Field: ", 'wpsc_pd' ) . $custom_option['name'];
			}
		}
		return $import;

	}
	add_filter( 'wpsc_pd_merge_product_log_addons', 'wpsc_cf_pd_merge_product_log_addons', null, 3 );

	/* End of: WordPress Administration */

} else {

	/* Start of: Storefront */

	function wpsc_cf_show_name( $show_name = null ) {

		if( $show_name )
			return $show_name;
		else
			return true;

	}

	/* End of: Storefront */

}

function wpsc_cf_custom_field_sort( $array, $key ) {

	$sort = array();
	$ret = array();
	reset( $array );
	foreach( $array as $ii => $va )
		$sort[$ii] = $va[$key];
	asort( $sort );
	foreach( $sort as $ii => $va )
		$ret[$ii] = $array[$ii];
	$array = $ret;

	return $array;

}
?>