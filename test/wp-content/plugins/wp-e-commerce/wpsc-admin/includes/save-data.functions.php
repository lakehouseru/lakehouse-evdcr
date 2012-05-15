<?php
/**
 * This file used for adding fields to the products category taxonomy page and saving those values correctly :)
 *
 * @package wp-e-commerce
 * @since 3.8
 * @todo UI needs a lot of loving - lots of padding issues, if we have these boxes, they should be sortable, closable, hidable, etc.
 */
function wpsc_ajax_set_variation_order(){
	global $wpdb;
	$sort_order = $_POST['sort_order'];
	$parent_id  = $_POST['parent_id'];

	$result = true;
	foreach( $sort_order as $key=>$value ){
		if ( empty( $value ) )
			continue;

		$value = preg_replace( '/[^0-9]/', '', $value );

		if( !wpsc_update_meta( $value, 'sort_order', $key, 'wpsc_variation' ) )
			$result = false;
	} 
}

/**
 * WP eCommerce edit and add product category page functions
 *
 * These are the main WPSC Admin functions
 *
 * @package wp-e-commerce
 * @since 3.7
 */

function wpsc_ajax_set_category_order(){
	global $wpdb;
	$sort_order = $_POST['sort_order'];
	$parent_id  = $_POST['parent_id'];

	$result = true;
	foreach( $sort_order as $key=>$value ){
		if ( empty( $value ) )
			continue;

		$value = preg_replace( '/[^0-9]/', '', $value );

		if( !wpsc_update_meta( $value, 'sort_order', $key, 'wpsc_category' ) )
			$result = false;
	} 
}

add_filter( 'manage_edit-wpsc_product_category_columns', 'wpsc_custom_category_columns' );
add_filter( 'manage_wpsc_product_category_custom_column', 'wpsc_custom_category_column_data', 10, 3);
add_action( 'wpsc_product_category_add_form_fields', 'wpsc_admin_category_forms_add' ); // After left-col
add_action( 'wpsc_product_category_edit_form_fields', 'wpsc_admin_category_forms_edit' ); // After left-col
add_action( 'created_wpsc_product_category', 'wpsc_save_category_set', 10 , 2 ); //After created
add_action( 'edited_wpsc_product_category', 'wpsc_save_category_set', 10 , 2 ); //After saved

/**
 * wpsc_custom_category_columns
 * Adds images column to category column.
 * @internal Don't feel handle column is necessary, but you would add it here if you wanted to
 * @param (array) columns | Array of columns assigned to this taxonomy
 * @return (array) columns | Modified array of columns
 */

function wpsc_custom_category_columns( $columns ) {
    // Doing it this funny way to ensure that image stays in far left, even if other items are added via plugin.
    unset( $columns["cb"] );
    
    $custom_array = array(
        'cb' => '<input type="checkbox" />',
        'image' => __( 'Image', 'wpsc' )
    );
    
    $columns = array_merge( $custom_array, $columns );
    
    return $columns;
}
/**
 * wpsc_custom_category_column_data
 * Adds images to the custom category column
 * @param (array) column_name | column name
 * @return nada
 */

function wpsc_custom_category_column_data( $string, $column_name, $term_id ) {
   global $wpdb;

  $image = wpsc_get_categorymeta( $term_id, 'image' );
  $name = get_term_by( 'id', $term_id, 'wpsc_product_category' );
  $name = $name->name;

  if( !empty( $image ) )
      $image = "<img src=\"".WPSC_CATEGORY_URL.stripslashes( $image )."\" title='".$name."' alt='".$name."' width='30' height='30' />";
   else
      $image = "<img src='".WPSC_CORE_IMAGES_URL."/no-image-uploaded.gif' title='".$name."' alt='".$name."' width='30' height='30' />";
   

    return $image;

}

/**
 * wpsc_admin_get_category_array
 * Recursively step through the categories and return it in a clean multi demensional array
 * for use in other list functions
 * @param int $parent_id
 */
function wpsc_admin_get_category_array($parent_id = null){
  global $wpdb;
  $orderedList = array();
  if(!isset($parent_id)) $parent_id = 0;
  $category_list = get_terms('wpsc_product_category','hide_empty=0&parent='.$parent_id);
  if(!is_array($category_list)){
    return false;
  }
  foreach($category_list as $category){
    $category_order = wpsc_get_categorymeta($category->term_id, 'order');
    $category_image = wpsc_get_categorymeta($category->term_id, 'image');
    if(!isset($category_order) || $category_order == 0) $category_order = (count($orderedList) +1);
    print "<!-- setting category Order number to ".$category_order."-->";
    $orderedList[$category_order]['id'] = $category->term_id;
    $orderedList[$category_order]['name'] = $category->name;
    $orderedList[$category_order]['image'] = $category_image;
    $orderedList[$category_order]['parent_id'] = $parent_id;
    $orderedList[$category_order]['children'] = wpsc_admin_get_category_array($category->term_id);
  }

  ksort($orderedList);
  return($orderedList);
}

/**
 * wpsc_admin_category_group_list, prints the left hand side of the add categories page
 * nothing returned
 */
function wpsc_admin_category_forms_add() {
	global $wpdb;
	$category_value_count = 0;
	?>

	<h3><?php _e('Advanced Settings', 'wpsc'); ?></h3>

	<div id="poststuff" class="postbox">
		<h3 class="hndle"><?php _e('Presentation Settings', 'wpsc'); ?></h3>

		<div class="inside">
			<tr>
				<td>
			<label for='image'><?php _e( 'Category Image' , 'wpsc' ); ?></label>					
				</td>
				<td>
			<input type='file' name='image' value='' /><br /><br />
				</td>
</tr>
				<tr>
					<td>
						<?php _e('Catalog View', 'wpsc'); ?>
					</td>
					<td>
						<?php
					if (!isset($category['display_type'])) $category['display_type'] = '';

						if ($category['display_type'] == 'grid') {
							$display_type1="selected='selected'";
						} else if ($category['display_type'] == 'default') {
							$display_type2="selected='selected'";
						}

						switch($category['display_type']) {
							case "default":
								$category_view1 = "selected ='selected'";
							break;

							case "grid":
							if(function_exists('product_display_grid')) {
								$category_view3 = "selected ='selected'";
								break;
							}

							case "list":
							if(function_exists('product_display_list')) {
								$category_view2 = "selected ='selected'";
								break;
							}

							default:
								$category_view0 = "selected ='selected'";
							break;
						}?>
							

						<select name='display_type'>
							<option value=''<?php echo $category_view0; ?> ><?php _e('Please select', 'wpsc'); ?></option>
							<option value='default' <?php if (isset($category_view1)) echo $category_view1; ?> ><?php _e('Default View', 'wpsc'); ?></option>

							<?php	if(function_exists('product_display_list')) {?>
										<option value='list' <?php echo  $category_view2; ?>><?php _e('List View', 'wpsc'); ?></option>
							<?php	} else { ?>
										<option value='list' disabled='disabled' <?php if (isset($category_view2)) echo $category_view2; ?>><?php _e('List View', 'wpsc'); ?></option>
							<?php	} ?>
							<?php if(function_exists('product_display_grid')) { ?>
										<option value='grid' <?php if (isset($category_view3)) echo  $category_view3; ?>><?php _e('Grid View', 'wpsc'); ?></option>
							<?php	} else { ?>
										<option value='grid' disabled='disabled' <?php if (isset($category_view3)) echo  $category_view3; ?>><?php  _e('Grid View', 'wpsc'); ?></option>
							<?php	} ?>
						</select><br /><br />
					</td>
				</tr>
			<tr>
				<td>
				<span class='small'><?php _e('To over-ride the presentation settings for this group you can enter in your prefered settings here', 'wpsc'); ?></span><br /><br />
				</td>
			</tr>

			<?php	if(function_exists("getimagesize")) { ?>
			<tr>
				<td>
					<?php _e('Thumbnail&nbsp;Size', 'wpsc'); ?>
				</td>
				<td>
					<?php _e('Width', 'wpsc'); ?> <input type='text' value='<?php if (isset($category['image_width'])) echo $category['image_width']; ?>' name='image_width' size='6'/>
                                        <?php _e('Height', 'wpsc'); ?> <input type='text' value='<?php if (isset($category['image_height'])) echo $category['image_height']; ?>' name='image_height' size='6'/><br/>
                                </td>
			</tr>
			<?php	
                            }
                        ?>
		</div>
	</div>

<!-- START OF TARGET MARKET SELECTION -->
<div id="poststuff" class="postbox">
	<h3 class="hndle"><?php _e( 'Target Market Restrictions', 'wpsc' ); ?></h3>
	<div class="inside"><?php
		$category_id = '';
		if (isset($_GET["tag_ID"])) $category_id = $_GET["tag_ID"];
		$countrylist = $wpdb->get_results("SELECT id,country,visible FROM `".WPSC_TABLE_CURRENCY_LIST."` ORDER BY country ASC ",ARRAY_A);
		$selectedCountries = wpsc_get_meta($category_id,'target_market','wpsc_category');
		$output = '';
		$output .= " <tr>\n\r";
		$output .= " 	<td>\n\r";
		$output .= __('Target Markets', 'wpsc').":\n\r";
		$output .= " 	</td>\n\r";
		$output .= " 	<td>\n\r";

		if(@extension_loaded('suhosin')) {
			$output .= "<em>".__("The Target Markets feature has been disabled because you have the Suhosin PHP extension installed on this server. If you need to use the Target Markets feature then disable the suhosin extension, if you can not do this, you will need to contact your hosting provider.
			",'wpsc')."</em>";

		} else {
			$output .= "<span>Select: <a href='' class='wpsc_select_all'>All</a>&nbsp; <a href='' class='wpsc_select_none'>None</a></span><br />";
			$output .= " 	<div id='resizeable' class='ui-widget-content multiple-select'>\n\r";
			foreach($countrylist as $country){
				if(in_array($country['id'], (array)$selectedCountries)){
					$output .= " <input type='checkbox' name='countrylist2[]' value='".$country['id']."'  checked='".$country['visible']."' />".$country['country']."<br />\n\r";
				} else {
					$output .= " <input type='checkbox' name='countrylist2[]' value='".$country['id']."'  />".$country['country']."<br />\n\r";
				}
			}
			$output .= " </div><br /><br />";
			$output .= " <span class='wpscsmall description'>Select the markets you are selling this category to.<span>\n\r";
		}
		$output .= "   </td>\n\r";
		$output .= " </tr>\n\r";

		echo $output;
		?>
	</div>
</div>

<!-- Checkout settings -->
<div id="poststuff" class="postbox">
	<h3 class="hndle"><?php _e('Checkout Settings', 'wpsc'); ?></h3>
	<div class="inside">
		<table class='category_forms'>
			<?php
			if (!isset($category['term_id'])) $category['term_id'] = '';
				$used_additonal_form_set = wpsc_get_categorymeta($category['term_id'], 'use_additional_form_set'); ?>
				<tr>
					<td>
						<?php _e("This category requires additional checkout form fields",'wpsc'); ?>
					</td>
					<td>
						<select name='use_additional_form_set'>
							<option value=''><?php _e("None",'wpsc'); ?></option>
							<?php
							$checkout_sets = get_option('wpsc_checkout_form_sets');
							unset($checkout_sets[0]);

							foreach((array)$checkout_sets as $key => $value) {
								$selected_state = "";
							if($used_additonal_form_set == $key)
								$selected_state = "selected='selected'";
							 ?>
								<option <?php echo $selected_state; ?> value='<?php echo $key; ?>'><?php echo stripslashes($value); ?></option>
							<?php
							}
							?>
						</select>
					</td>
				</tr>
			<?php $uses_billing_address = (bool)wpsc_get_categorymeta($category['term_id'], 'uses_billing_address'); ?>
				<tr>
					<td>
						<?php _e("Products in this category use the billing address to calculate shipping",'wpsc'); ?>
					</td>
					<td>
						<input type='radio' value='1' name='uses_billing_address' <?php echo (($uses_billing_address == true) ? "checked='checked'" : ""); ?> /><?php _e("Yes",'wpsc'); ?>
						<input type='radio' value='0' name='uses_billing_address' <?php echo (($uses_billing_address != true) ? "checked='checked'" : ""); ?> /><?php _e("No",'wpsc'); ?>
					</td>
				</tr>
		</table>
	</div>
</div>

<table class="category_forms">
	<tr>

	</tr>
</table>
  <?php
}

function wpsc_admin_category_forms_edit() {
	global $wpdb;

	$category_value_count = 0;
	$category_name = '';
	$category = array();
           
        $category_id = absint( $_REQUEST["tag_ID"] );
        $category = get_term($category_id, 'wpsc_product_category', ARRAY_A);
        $category['nice-name'] = wpsc_get_categorymeta($category['term_id'], 'nice-name');
        $category['description'] = wpsc_get_categorymeta($category['term_id'], 'description');
        $category['image'] = wpsc_get_categorymeta($category['term_id'], 'image');
        $category['fee'] = wpsc_get_categorymeta($category['term_id'], 'fee');
        $category['active'] = wpsc_get_categorymeta($category['term_id'], 'active');
        $category['order'] = wpsc_get_categorymeta($category['term_id'], 'order');
        $category['display_type'] = wpsc_get_categorymeta($category['term_id'], 'display_type');
        $category['image_height'] = wpsc_get_categorymeta($category['term_id'], 'image_height');
        $category['image_width'] = wpsc_get_categorymeta($category['term_id'], 'image_width');
        $category['use_additional_form_set'] = wpsc_get_categorymeta($category['term_id'], 'use_additional_form_set');
	

	?>

        <tr>
            <td colspan="2">
                <h3><?php _e( 'Advanced Settings', 'wpsc' ); ?></h3>
              
            </td>
        </tr>

	<tr class="form-field">
            <th scope="row" valign="top">
		<label for="display_type"><?php _e( 'Catalog View', 'wpsc' ); ?></label>
            </th>
            <td>
		<?php
                    //Could probably be *heavily* refactored later just to use do_action here and in GoldCart.  Baby steps.
					if (!isset($category['display_type'])) $category['display_type'] = '';

						if ($category['display_type'] == 'grid') {
							$display_type1="selected='selected'";
						} else if ($category['display_type'] == 'default') {
							$display_type2="selected='selected'";
						}

						switch($category['display_type']) {
							case "default":
								$category_view1 = "selected ='selected'";
							break;

							case "grid":
							if(function_exists('product_display_grid')) {
								$category_view3 = "selected ='selected'";
								break;
							}

							case "list":
							if(function_exists('product_display_list')) {
								$category_view2 = "selected ='selected'";
								break;
							}

							default:
								$category_view0 = "selected ='selected'";
							break;
						}
                                                ?>
                        <select name='display_type'>
                                <option value=''<?php echo $category_view0; ?> ><?php _e('Please select', 'wpsc'); ?></option>
                                <option value='default' <?php if (isset($category_view1)) echo $category_view1; ?> ><?php _e( 'Default View', 'wpsc' ); ?></option>

                                <?php	if(function_exists('product_display_list')) {?>
                                                        <option value='list' <?php echo  $category_view2; ?>><?php _e('List View', 'wpsc'); ?></option>
                                <?php	} else { ?>
                                                        <option value='list' disabled='disabled' <?php if (isset($category_view2)) echo $category_view2; ?>><?php _e( 'List View', 'wpsc' ); ?></option>
                                <?php	} ?>
                                <?php if(function_exists('product_display_grid')) { ?>
                                                        <option value='grid' <?php if (isset($category_view3)) echo  $category_view3; ?>><?php _e( 'Grid View', 'wpsc' ); ?></option>
                                <?php	} else { ?>
                                                        <option value='grid' disabled='disabled' <?php if (isset($category_view3)) echo  $category_view3; ?>><?php  _e( 'Grid View', 'wpsc' ); ?></option>
                                <?php	} ?>
                        </select><br />
		<span class="description"><?php _e( 'To over-ride the presentation settings for this group you can enter in your prefered settings here', 'wpsc' ); ?></span>
            </td>
	</tr>
        <tr class="form-field">
            <th scope="row" valign="top">
		<label for="image"><?php _e( 'Category Image', 'wpsc' ); ?></label>
            </th>
            <td>
		<input type='file' name='image' value='' /><br />
                <label><input type='checkbox' name='deleteimage' class="wpsc_cat_box" value='1' /><?php _e( 'Delete Image', 'wpsc' ); ?></label><br/>
		<span class="description"><?php _e( 'You can set an image for the category here.  If one exists, check the box to delete.', 'wpsc' ); ?></span>
            </td>
	</tr>
        <?php if( function_exists( "getimagesize" ) ) : ?>
        <tr class="form-field">
            <th scope="row" valign="top">
		<label for="image"><?php _e( 'Thumbnail Size', 'wpsc' ); ?></label>
            </th>
            <td>
                <?php _e( 'Width', 'wpsc' ); ?> <input type='text' class="wpsc_cat_image_size" value='<?php if (isset($category['image_width'])) echo $category['image_width']; ?>' name='image_width' size='6' />
                <?php _e( 'Height', 'wpsc' ); ?> <input type='text' class="wpsc_cat_image_size" value='<?php if (isset($category['image_height'])) echo $category['image_height']; ?>' name='image_height' size='6' /><br/>
           </td>
	</tr>
        <?php endif; // 'getimagesize' condition ?>
	<tr>
            <td colspan="2"><h3><?php _e( 'Shortcodes and Template Tags', 'wpsc' ); ?></h3></td>
        </tr>

        <tr class="form-field">
            <th scope="row" valign="top">
		<label for="image"><?php _e( 'Display Category Shortcode', 'wpsc' ); ?>:</label>
            </th>
            <td>
                <span>[wpsc_products category_url_name='<?php echo $category["slug"]; ?>']</span><br />
		<span class="description"><?php _e( 'Shortcodes are used to display a particular category or group within any WordPress page or post.', 'wpsc' ); ?></span>
            </td>
	</tr>
        <tr class="form-field">
            <th scope="row" valign="top">
		<label for="image"><?php _e( 'Display Category Template Tag', 'wpsc' ); ?>:</label>
            </th>
            <td>
                <span>&lt;?php echo wpsc_display_products_page( array( 'category_url_name'=>'<?php echo $category["slug"]; ?>' ) ); ?&gt;</span><br />
		<span class="description"><?php _e( 'Template tags are used to display a particular category or group within your theme / template.', 'wpsc' ); ?></span>
            </td>
	</tr>

<!-- START OF TARGET MARKET SELECTION -->

        <tr>
            <td colspan="2">
                <h3><?php _e( 'Target Market Restrictions', 'wpsc' ); ?></h3>
            </td>
        </tr>
        <?php
            $countrylist = $wpdb->get_results( "SELECT id,country,visible FROM `".WPSC_TABLE_CURRENCY_LIST."` ORDER BY country ASC ",ARRAY_A );
            $selectedCountries = wpsc_get_meta( $category_id,'target_market','wpsc_category' );
        ?>
        <tr class="form-field">
            <th scope="row" valign="top">
		<label for="image"><?php _e( 'Target Markets', 'wpsc' ); ?>:</label>
            </th>
            <td>
                <?php
                    if ( @extension_loaded( 'suhosin' ) ) :
                 ?>
                <em><?php _e( 'The Target Markets feature has been disabled because you have the Suhosin PHP extension installed on this server. If you need to use the Target Markets feature, then disable the suhosin extension. If you can not do this, you will need to contact your hosting provider.','wpsc'); ?></em>

                <?php
                    else :
                ?>
		<span><?php _e( 'Select', 'wpsc' ); ?>: <a href='' class='wpsc_select_all'><?php _e( 'All', 'wpsc' ); ?></a>&nbsp; <a href='' class='wpsc_select_none'><?php _e( 'None', 'wpsc' ); ?></a></span><br />
		<div id='resizeable' class='ui-widget-content multiple-select'>
                    <?php
			foreach( $countrylist as $country ) {
                            if( in_array( $country['id'], (array)$selectedCountries ) )
                                echo " <input type='checkbox' class='wpsc_cat_box' name='countrylist2[]' value='".$country['id']."'  checked='".$country['visible']."' />".$country['country']."<br />";
                            else
                                echo " <input type='checkbox' class='wpsc_cat_box' name='countrylist2[]' value='".$country['id']."'  />".$country['country']."<br />";
                        }
                    ?>
		</div>
                <?php
                    endif;
                ?><br />
		<span class="description"><?php _e( 'Select the markets you are selling this category to.', 'wpsc' ); ?></span>
            </td>
	</tr>
<!-- Checkout settings -->

        <tr>
            <td colspan="2">
                <h3><?php _e( 'Checkout Settings', 'wpsc' ); ?></h3>
            </td>
        </tr>
        <?php
            if ( !isset( $category['term_id'] ) )
                $category['term_id'] = '';
            $used_additonal_form_set = wpsc_get_categorymeta( $category['term_id'], 'use_additional_form_set' );
            $checkout_sets = get_option('wpsc_checkout_form_sets');
            unset($checkout_sets[0]);
            $uses_billing_address = (bool)wpsc_get_categorymeta( $category['term_id'], 'uses_billing_address' );
        ?>
        <tr class="form-field">
            <th scope="row" valign="top">
		<label for="image"><?php _e( 'Category requires additional checkout form fields', 'wpsc' ); ?></label>
            </th>
            <td>
                <select name='use_additional_form_set'>
                    <option value=''><?php _e( 'None', 'wpsc' ); ?></option>
                    <?php
                        foreach( (array) $checkout_sets as $key => $value ) {
                            $selected_state = "";
                            if( $used_additonal_form_set == $key )
                                $selected_state = "selected='selected'";
                     ?>
                    <option <?php echo $selected_state; ?> value='<?php echo $key; ?>'><?php echo stripslashes( $value ); ?></option>
                    <?php
                        }
                    ?>
                </select><br />
              </td>
	</tr>

        <tr class="form-field">
            <th scope="row" valign="top">
		<label for="image"><?php _e( 'Products in this category use the billing address to calculate shipping', 'wpsc' ); ?></label>
            </th>
            <td>
                <input type='radio' class="wpsc_cat_box" value='1' name='uses_billing_address' <?php echo ( ( $uses_billing_address == true ) ? "checked='checked'" : "" ); ?> /><?php _e( 'Yes','wpsc' ); ?>
                <input type='radio' class="wpsc_cat_box" value='0' name='uses_billing_address' <?php echo (($uses_billing_address != true) ? "checked='checked'" : ""); ?> /><?php _e( 'No','wpsc' ); ?>
                <br />
	  </td>
	</tr>

  <?php
} 

/**
 * wpsc_save_category_set, Saves the category set data
 * @param nothing
 * @return nothing
 */
function wpsc_save_category_set($category_id, $tt_id) {
	global $wpdb;
	if( !empty( $_POST ) ) {
		/* Image Processing Code*/
		if( !empty( $_FILES['image'] ) && preg_match( "/\.(gif|jp(e)*g|png){1}$/i", $_FILES['image']['name'] ) ) {
			if( function_exists( "getimagesize" ) ) {
				if( ( (int) $_POST['width'] > 10 && (int) $_POST['width'] < 512 ) && ((int)$_POST['height'] > 10 && (int)$_POST['height'] < 512) ) {
					$width = (int) $_POST['width'];
					$height = (int) $_POST['height'];
					image_processing( $_FILES['image']['tmp_name'], ( WPSC_CATEGORY_DIR.$_FILES['image']['name'] ), $width, $height );
				} else {
					image_processing( $_FILES['image']['tmp_name'], ( WPSC_CATEGORY_DIR.$_FILES['image']['name'] ) );
				}	
				$image = esc_sql( $_FILES['image']['name'] );
			} else {
				$new_image_path = ( WPSC_CATEGORY_DIR.basename($_FILES['image']['name'] ) );
				move_uploaded_file( $_FILES['image']['tmp_name'], $new_image_path );
				$stat = stat( dirname( $new_image_path ) );
				$perms = $stat['mode'] & 0000666;
				@ chmod( $new_image_path, $perms );	
				$image = esc_sql( $_FILES['image']['name'] );
			}
		} else {
			$image = '';
		}
		//Good to here		
		  
        if( isset( $_POST['tag_ID'] ) ) {
            //Editing
            $category_id= $_POST['tag_ID'];
            $category = get_term_by( 'id', $category_id, 'wpsc_product_category' );
            $url_name=$category->slug;

        }		
		if(isset($_POST['deleteimage']) && $_POST['deleteimage'] == 1) {
			wpsc_delete_categorymeta($category_id, 'image');
		} else if($image != '') {
			wpsc_update_categorymeta($category_id, 'image', $image);
		}
		
		if ( !empty( $_POST['height'] ) && is_numeric( $_POST['height'] ) && !empty( $_POST['width'] ) && is_numeric( $_POST['width'] ) && $image == null ) {
			$imagedata = wpsc_get_categorymeta($category_id, 'image');
			if($imagedata != null) {
				$height = $_POST['height'];
				$width = $_POST['width'];
				$imagepath = WPSC_CATEGORY_DIR . $imagedata;
				$image_output = WPSC_CATEGORY_DIR . $imagedata;
				image_processing($imagepath, $image_output, $width, $height);
			}
		}
		
		wpsc_update_categorymeta($category_id, 'fee', '0');
		wpsc_update_categorymeta($category_id, 'active', '1');
		wpsc_update_categorymeta($category_id, 'order', '0');
		
		if ( isset( $_POST['display_type'] ) )
			wpsc_update_categorymeta($category_id, 'display_type',esc_sql(stripslashes($_POST['display_type'])));
			
		if ( isset( $_POST['image_height'] ) )
			wpsc_update_categorymeta($category_id, 'image_height', esc_sql(stripslashes($_POST['image_height'])));
			
		if ( isset( $_POST['image_width'] ) )
			wpsc_update_categorymeta($category_id, 'image_width', esc_sql(stripslashes($_POST['image_width'])));
		
		
		if ( ! empty( $_POST['use_additional_form_set'] ) ) {
			wpsc_update_categorymeta($category_id, 'use_additional_form_set', $_POST['use_additional_form_set']);
			//exit('<pre>'.print_r($_POST,1).'</pre>');
		} else {
			wpsc_delete_categorymeta($category_id, 'use_additional_form_set');
		}

		if ( ! empty( $_POST['uses_billing_address'] ) ) {
			wpsc_update_categorymeta($category_id, 'uses_billing_address', 1);
			$uses_additional_forms = true;
		} else {
			wpsc_update_categorymeta($category_id, 'uses_billing_address', 0);
			$uses_additional_forms = false;
		}	
		
	  	if( ! empty( $_POST['countrylist2'] ) && ($category_id > 0)){
	    	$AllSelected = false;
			$countryList = $wpdb->get_col("SELECT `id` FROM  `".WPSC_TABLE_CURRENCY_LIST."`");
	    			
			if($AllSelected != true){
				$unselectedCountries = array_diff($countryList, $_POST['countrylist2']);
				//find the countries that are selected
				$selectedCountries = array_intersect($countryList, $_POST['countrylist2']);
				wpsc_update_categorymeta( $category_id,   'target_market', $selectedCountries); 
			}
			
		} elseif ( ! isset($_POST['countrylist2'] ) ){
			wpsc_update_categorymeta( $category_id,   'target_market',''); 
  			$AllSelected = true;
		}

	}
}


?>
