<?php

/**
 * WP eCommerce User Account class
 *
 * This class is responsible for theming the User Account page.
 *
 * @package wp-e-commerce
 * @since 3.8
 */
global $wpdb, $user_ID, $wpsc_purchlog_statuses, $separator;

if ( get_option( 'permalink_structure' ) != '' )
	$separator = "?";
else
	$separator = "&amp;";

$siteurl = site_url();

function is_wpsc_profile_page() {
	return !empty($_REQUEST['edit_profile']) && ( $_REQUEST['edit_profile'] == 'true' );
}

function is_wpsc_downloads_page() {
	return !empty($_REQUEST['downloads']) && ( $_REQUEST['downloads'] == 'true' );
}

function validate_form_data() {

	global $wpdb, $user_ID, $wpsc_purchlog_statuses;

	$any_bad_inputs = false;
	$changes_saved = false;
	$bad_input_message = '';
	$_SESSION['collected_data'] = null;

	if ( ! empty($_POST['collected_data']) ) {

		if( ! wp_verify_nonce( $_POST['_wpsc_user_profile'], 'wpsc_user_profile') )
			die( __( 'It would appear either you are trying to hack into this account, or your session has expired.  Hoping for the latter.', 'wpsc' ) );

		foreach ( (array)$_POST['collected_data'] as $value_id => $value ) {
			$form_sql = $wpdb->prepare( "SELECT * FROM `" . WPSC_TABLE_CHECKOUT_FORMS . "` WHERE `id` = %d LIMIT 1", $value_id );
			$form_data = $wpdb->get_row( $form_sql, ARRAY_A );
			$bad_input = false;
			if ( $form_data['mandatory'] == 1 ) {
				switch ( $form_data['type'] ) {
					case "email":
						if ( !preg_match( "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9-.]+\.[a-zA-Z]{2,5}$/", $value ) ) {
							$any_bad_inputs = true;
							$bad_input = true;
						}
						break;

					case "delivery_country":
						if ( ($value != null ) ) {
							$_SESSION['delivery_country'] == $value;
						}
						break;

					default:
						if ( empty( $value ) )
							$bad_input = true;
						break;
				}
				if ( $bad_input === true ) {

					switch ( $form_data['name'] ) {
						case __( 'First Name', 'wpsc' ):
							$bad_input_message .= __( 'Please enter a valid name', 'wpsc' ) . "";
							break;

						case __( 'Last Name', 'wpsc' ):
							$bad_input_message .= __( 'Please enter a valid surname', 'wpsc' ) . "";
							break;

						case __( 'Email', 'wpsc' ):
							$bad_input_message .= __( 'Please enter a valid email address', 'wpsc' ) . "";
							break;

						case __( 'Address 1', 'wpsc' ):
						case __( 'Address 2', 'wpsc' ):
							$bad_input_message .= __( 'Please enter a valid address', 'wpsc' ) . "";
							break;

						case __( 'City', 'wpsc' ):
							$bad_input_message .= __( 'Please enter your town or city.', 'wpsc' ) . "";
							break;

						case __( 'Phone', 'wpsc' ):
							$bad_input_message .= __( 'Please enter a valid phone number', 'wpsc' ) . "";
							break;

						case __( 'Country', 'wpsc' ):
							$bad_input_message .= __( 'Please select your country from the list.', 'wpsc' ) . "";
							break;

						default:
							$bad_input_message .= sprintf(__( 'Please enter a valid <span class="wpsc_error_msg_field_name">%s</span>.', 'wpsc' ), esc_html($form_data['name']) );
							break;
					}
					$bad_input_message .= "<br />";
				} else {
					$meta_data[$value_id] = $value;
				}
			} else {
				$meta_data[$value_id] = $value;
			}
		}
		$meta_data = apply_filters( 'wpsc_user_log_update', $meta_data, $user_ID );
		update_user_meta( $user_ID, 'wpshpcrt_usr_profile', $meta_data );
	}
	if ( $changes_saved ) {
		$message = __( 'Thanks, your changes have been saved.', 'wpsc' );
	} else {
		$message = $bad_input_message;
	}
	return apply_filters( 'wpsc_profile_message', $message );
}

/**
 * wpsc_display_form_fields()
 *
 * This function displays each of the form fields.  Each of them are filterable via 'wpsc_account_form_field_$tag' where tag is permalink-styled name or uniquename.
 * i.e. First Name under Shipping would be 'wpsc_account_form_field_shippingfirstname' - while Your Billing Details would be filtered
 * via 'wpsc_account_form_field_your-billing-details'.
 *
 * @global <type> $wpdb
 * @global <type> $user_ID
 * @global <type> $wpsc_purchlog_statuses
 * @global <type> $gateway_checkout_form_fields
 */
function wpsc_display_form_fields() {
// Field display and Data saving function

	global $wpdb, $user_ID, $wpsc_purchlog_statuses, $gateway_checkout_form_fields, $wpsc_checkout;

	if ( empty( $wpsc_checkout ) )
		$wpsc_checkout = new WPSC_Checkout();

	$meta_data = null;
	$saved_data_sql = "SELECT * FROM `" . $wpdb->usermeta . "` WHERE `user_id` = '" . $user_ID . "' AND `meta_key` = 'wpshpcrt_usr_profile';";
	$saved_data = $wpdb->get_row( $saved_data_sql, ARRAY_A );

	$meta_data = get_user_meta( $user_ID, 'wpshpcrt_usr_profile', 1 );
	$meta_data = apply_filters( 'wpsc_user_log_get', $meta_data, $user_ID );

	$form_sql = "SELECT * FROM `" . WPSC_TABLE_CHECKOUT_FORMS . "` WHERE `active` = '1' ORDER BY `checkout_order`;";
	$form_data = $wpdb->get_results( $form_sql, ARRAY_A );
	foreach ( $form_data as $form_field ) {
		if ( !empty( $form_field['unique_name'] ) ) {
			$ff_tag = $form_field['unique_name'];
		} else {
			$ff_tag = htmlentities( stripslashes( strtolower( str_replace( ' ', '-', $form_field['name'] ) ) ), ENT_QUOTES, 'UTF-8' );
		}

		if(!empty($meta_data[$form_field['id']]) && !is_array($meta_data[$form_field['id']]))
			$meta_data[$form_field['id']] = htmlentities( stripslashes( $meta_data[$form_field['id']] ), ENT_QUOTES, 'UTF-8' );

		if ( $form_field['type'] == 'heading' ) {
			echo "
    <tr>
      <td colspan='2'>\n\r";
			echo "<strong>" . apply_filters( 'wpsc_account_form_field_' . $ff_tag, $form_field['name'] ) . "</strong>";
			echo "
      </td>
    </tr>\n\r";
		} else {
			$continue = true;
			if( $form_field['unique_name'] == 'billingstate'){
				$selected_country_id = wpsc_get_country_form_id_by_type('country');
				if(is_array($meta_data[$selected_country_id]) && isset($meta_data[$selected_country_id][1])){
					$continue = false;
				}else{
					$continue = true;
				}

			}

			if( $form_field['unique_name'] == 'shippingstate'){
				$delivery_country_id = wpsc_get_country_form_id_by_type('delivery_country');

				if((is_array($meta_data[$delivery_country_id]) && (isset($meta_data[$delivery_country_id][1]) ))|| is_numeric($meta_data[$form_field['id']])){
					$shipping_form_field = $form_field;
					$continue = false;
				}else{
					$continue = true;
				}
			}

			if($continue){
				echo "
			      <tr>
	    		    <td align='left'>\n\r";
						echo apply_filters( 'wpsc_account_form_field_' . $ff_tag, $form_field['name'] );
						if ( $form_field['mandatory'] == 1 )
						echo " *";
						echo "
	        		</td>\n\r
	        		<td  align='left'>\n\r";
        	}
			switch ( $form_field['type'] ) {
				case "city":
				case "delivery_city":
				echo "<input type='text' value='" . $meta_data[$form_field['id']] . "' name='collected_data[" . $form_field['id'] . "]' />";
					break;

				case "address":
				case "delivery_address":
				case "textarea":
					echo "<textarea name='collected_data[" . $form_field['id'] . "]'>" . $meta_data[$form_field['id']] . "</textarea>";
					break;

				case "text":
					$value = isset( $meta_data[$form_field['id']] ) ? $meta_data[$form_field['id']] : '';
					if($continue){
						echo "<input type='text' value='" . $value . "' name='collected_data[" . $form_field['id'] . "]' />";

					}elseif('shippingstate' == $form_field['unique_name'] && is_numeric( $value )){

					}

					break;

				case "region":
				case "delivery_region":
					echo "<select name='collected_data[" . $form_field['id'] . "]'>" . nzshpcrt_region_list( $_SESSION['collected_data'][$form_field['id']] ) . "</select>";
					break;


				case "country":
					if (is_array($meta_data[$form_field['id']]))
						$country_code = ($meta_data[$form_field['id']][0]);
					else
						$country_code = ($meta_data[$form_field['id']]);
					echo "<select name='collected_data[" . $form_field['id'] . "][0]' >" . nzshpcrt_country_list( $country_code ) . "</select>";
					if ( isset($meta_data[$form_field['id']][1]) )
						echo "<br /><select name='collected_data[" . $form_field['id'] . "][1]'>" . nzshpcrt_region_list( $country_code, $meta_data[$form_field['id']][1] ) . "</select>";

					break;

				case "delivery_country":

					if (is_array($meta_data[$form_field['id']]))
						$country_code = ($meta_data[$form_field['id']][0]);
					else
						$country_code = ($meta_data[$form_field['id']]);
					echo "<select name='collected_data[" . $form_field['id'] . "][0]' >" . nzshpcrt_country_list( $country_code ) . "</select>";
					if( is_array($meta_data[$form_field['id']]))
						echo "<br /><select name='collected_data[" . $form_field['id'] . "][1]'>" . nzshpcrt_region_list( $country_code, $meta_data[$form_field['id']][1] ) . "</select>";
					elseif( isset($shipping_form_field) )
						echo "<br /><select name='collected_data[" . $shipping_form_field['id'] . "][1]'>" . nzshpcrt_region_list( $country_code, $meta_data[$shipping_form_field['id']] ) . "</select>";
					break;
				case "email":
					echo "<input type='text' value='" . $meta_data[$form_field['id']] . "' name='collected_data[" . $form_field['id'] . "]' />";
					break;

				case "select":
					$options = $wpsc_checkout->get_checkout_options( $form_field['id'] );
					$selected = isset( $meta_data[$form_field['id']] ) ? $meta_data[$form_field['id']] : null;

					?>
						<select name='collected_data[<?php echo esc_attr( $form_field['id'] ); ?>]'>
							<option value="-1"><?php _ex( 'Select an Option', 'Dropdown default on user log page', 'wpsc' ); ?></option>
							<?php foreach ( $options as $label => $value ): ?>
								<option <?php selected( $value, $selected ); ?> value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
							<?php endforeach ?>
						</select>
					<?php
					break;

				case 'checkbox':
				case 'radio':
					$checked_values = isset( $meta_data[$form_field['id']] ) ? (array) $meta_data[$form_field['id']] : array();
					$options = $wpsc_checkout->get_checkout_options( $form_field['id'] );
					$field_name = "collected_data[{$form_field['id']}]";
					if ( $form_field['type'] == 'checkbox' )
						$field_name .= '[]';
					foreach ( $options as $label => $value ) {
						?>
							<label>
								<input <?php checked( in_array( $value, $checked_values ) ); ?> type="<?php echo $form_field['type']; ?>" id="" name="collected_data[<?php echo esc_attr( $form_field['id'] ); ?>][]" value="<?php echo esc_attr( $value ); ?>"  />
								<?php echo esc_html( $label ); ?>
							</label><br />
						<?php
					}
					break;

				default:
					$value = isset( $meta_data[$form_field['id']] ) ? $meta_data[$form_field['id']] : '';
					echo "<input type='text' value='" . $value . "' name='collected_data[" . $form_field['id'] . "]' />";
					break;
			}
			echo wp_nonce_field( 'wpsc_user_profile', '_wpsc_user_profile' );
			echo "
        </td>
      </tr>\n\r";

		}
	}
	/* Returns an empty array at this point, empty in regards to fields, does show the internalname though.  Needs to be reconsidered, even if it did work, need to check
	 * functionality and PCI_DSS compliance

	  if ( isset( $gateway_checkout_form_fields ) )
	  {
	  echo $gateway_checkout_form_fields;
	  }
	 */
}

function wpsc_has_downloads() {
	global $wpdb, $user_ID, $files, $links, $products;

	$purchases = $wpdb->get_results( "SELECT `id`, `processed` FROM `" . WPSC_TABLE_PURCHASE_LOGS . "` WHERE user_ID = " . (int)$user_ID . "" );
	$rowcount = count( $purchases );

	if ( $rowcount >= 1 ) {
		$perchidstr = "(";
		$perchids = array();
		foreach( (array)$purchases as $purchase ) {
			$is_transaction = wpsc_check_purchase_processed( $purchase->processed );
			if( $is_transaction ) {
				$perchids[] = $purchase->id;
			}
		}
		if(!empty($perchids)){
			$perchidstr .= implode( ',', $perchids );
			$perchidstr .= ")";
			$sql = "SELECT * FROM `" . WPSC_TABLE_DOWNLOAD_STATUS . "` WHERE `purchid` IN " . $perchidstr . " AND `active` IN ('1') ORDER BY `datetime` DESC";
			$products = $wpdb->get_results( $sql, ARRAY_A );
			$products = apply_filters( 'wpsc_has_downloads_products', $products );
		}
	}

	foreach ( (array)$products as $key => $product ) {
	if( !empty( $product['uniqueid'] ) ) { // if the uniqueid is not equal to null, its "valid", regardless of what it is
			$links[] = site_url() . "/?downloadid=" . $product['id'];
		} else {
			$links[] = site_url() . "/?downloadid=" . $product['uniqueid'];
 		}
		$sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE id = %d", $product['fileid'] );
		$file = $wpdb->get_results( $sql, ARRAY_A );
		$files[] = $file[0];
	}
	if ( count( $files ) > 0 ) {
		return true;
	} else {
		return false;
	}
}

function wpsc_has_purchases() {

	global $wpdb, $user_ID, $wpsc_purchlog_statuses, $gateway_checkout_form_fields, $purchase_log, $col_count;

	/*
	 * this finds the earliest timedit-profile in the shopping cart and sorts out the timestamp system for the month by month display
	 */

	$earliest_record_sql = "SELECT MIN(`date`) AS `date` FROM `" . WPSC_TABLE_PURCHASE_LOGS . "` WHERE `date`!=''";
	$earliest_record = $wpdb->get_results( $earliest_record_sql, ARRAY_A );

	if ( $earliest_record[0]['date'] != null ) {
		$form_sql = "SELECT * FROM `" . WPSC_TABLE_CHECKOUT_FORMS . "` WHERE `active` = '1' AND `display_log` = '1';";
		$col_count = 4; //+ count( $form_data );
		$sql = "SELECT * FROM `" . WPSC_TABLE_PURCHASE_LOGS . "` WHERE `user_ID` IN ('" . $user_ID . "') ORDER BY `date` DESC";
		$purchase_log = $wpdb->get_results( $sql, ARRAY_A );

		return true;
	} else {

		return false;
	}
}

function wpsc_has_purchases_this_month() {
	global $wpdb, $user_ID, $wpsc_purchlog_statuses, $gateway_checkout_form_fields, $purchase_log, $col_count;

	$i = 0;
	$subtotal = 0;

	if ( $purchase_log != null )
		return true;
	else
		return false;
}

function wpsc_user_details() {
	global $wpdb, $user_ID, $wpsc_purchlog_statuses, $gateway_checkout_form_fields, $purchase_log, $col_count;

	$nzshpcrt_gateways = nzshpcrt_get_gateways();
	$i = 0;
	$subtotal = 0;

        do_action( 'wpsc_pre_purchase_logs' );

	foreach ( (array)$purchase_log as $purchase ) {
		$status_state = "expand";
		$status_style = "display:none;";
		$alternate = "";
		$i++;

		if ( ($i % 2) != 0 )
			$alternate = "class='alt'";

		echo "<tr $alternate>\n\r";
		echo " <td class='processed'>";
		echo "<a href='#' onclick='return show_details_box(\"status_box_" . $purchase['id'] . "\",\"log_expander_icon_" . $purchase['id'] . "\");'>";

		if ( !empty($_GET['id']) && $_GET['id'] == $purchase['id'] ) {
			$status_state = "collapse";
			$status_style = "style='display: block;'";
		}

		echo "<img class='log_expander_icon' id='log_expander_icon_" . $purchase['id'] . "' src='" . WPSC_CORE_IMAGES_URL . "/icon_window_$status_state.gif' alt='' title='' />";

		echo "<span id='form_group_" . $purchase['id'] . "_text'>" . __( 'Details', 'wpsc' ) . "</span>";
		echo "</a>";
		echo " </td>\n\r";

		echo " <td>";
		echo date( "jS M Y", $purchase['date'] );
		echo " </td>\n\r";

		echo " <td>";
		$country = get_option( 'country_form_field' );
		if ( $purchase['shipping_country'] != '' ) {
			$billing_country = $purchase['billing_country'];
			$shipping_country = $purchase['shipping_country'];
		} elseif ( !empty($country)) {
			$country_sql = $wpdb->prepare( "SELECT * FROM `" . WPSC_TABLE_SUBMITED_FORM_DATA . "` WHERE `log_id` = %d AND `form_id` = %d LIMIT 1", $purchase['id'] ,get_option( 'country_form_field' ) );
			$country_data = $wpdb->get_results( $country_sql, ARRAY_A );
			$billing_country = $country_data[0]['value'];
			$shipping_country = $country_data[0]['value'];
		}
		echo wpsc_currency_display( $purchase['totalprice'], array('display_as_html' => false) );
		$subtotal += $purchase['totalprice'];
		echo " </td>\n\r";


		if ( get_option( 'payment_method' ) == 2 ) {
			echo " <td>";
			$gateway_name = '';
			foreach ( (array)$nzshpcrt_gateways as $gateway ) {
				if ( $purchase['gateway'] != 'testmode' ) {
					if ( $gateway['internalname'] == $purchase['gateway'] ) {
						$gateway_name = $gateway['name'];
					}
				} else {
					$gateway_name = "Manual Payment";
				}
			}
			echo $gateway_name;
			echo " </td>\n\r";
		}

		echo "</tr>\n\r";
		echo "<tr>\n\r";
		echo " <td colspan='$col_count' class='details'>\n\r";
		echo "  <div id='status_box_" . $purchase['id'] . "' class='order_status' style=\"$status_style\">\n\r";
		echo "  <div>\n\r";

		//order status code lies here
		//check what $purchase['processed'] reflects in the $wpsc_purchlog_statuses array
		$status_name = wpsc_find_purchlog_status_name( $purchase['processed'] );
		echo "  <strong class='form_group'>" . __( 'Order Status', 'wpsc' ) . ":</strong>\n\r";
		echo $status_name . "<br /><br />";

                do_action( 'wpsc_user_log_after_order_status', $purchase );

		//written by allen
		$usps_id = get_option( 'usps_user_id' );
		if ( $usps_id != null ) {
			$XML1 = "<TrackFieldRequest USERID=\"$usps_id\"><TrackID ID=\"" . $purchase['track_id'] . "\"></TrackID></TrackFieldRequest>";
			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, "http://secure.shippingapis.com/ShippingAPITest.dll?" );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			$postdata = "API=TrackV2&XML=" . $XML1;
			curl_setopt( $ch, CURLOPT_POSTFIELDS, $postdata );

			$parser = new xml2array;
			$parsed = $parser->parse( $result );
			$parsed = $parsed[0]['children'][0]['children'];
			if ( $purchase['track_id'] != null ) {
				echo "<br /><br />";
				echo " <strong class='form_group'>" . __( 'Shipping Address', 'wpsc' ) . "</strong>\n\r";
				echo "<table>";
				foreach ( (array)$parsed as $parse ) {
					if ( $parse['name'] == "TRACKSUMMARY" )
						foreach ( (array)$parse['children'] as $attrs ) {
							if ( $attrs['name'] != "EVENT" )
								$attrs['name'] = str_replace( "EVENT", "", $attrs['name'] );
							$bar = ucfirst( strtolower( $attrs['name'] ) );
							echo "<tr><td>" . $bar . "</td><td>" . $attrs['tagData'] . "</td></tr>";
						}
				}
				echo "</table>";
			}
			echo "<br /><br />";
		}
		//end of written by allen
		//cart contents display starts here;
		echo "  <strong class='form_group'>" . __( 'Order Details', 'wpsc' ) . ":</strong>\n\r";
		$cartsql = $wpdb->prepare( "SELECT * FROM `" . WPSC_TABLE_CART_CONTENTS . "` WHERE `purchaseid`= %d", $purchase['id'] );
		$cart_log = $wpdb->get_results( $cartsql, ARRAY_A );
		$j = 0;
		// /*
		if ( $cart_log != null ) {
			echo "<table class='logdisplay'>";
			echo "<tr class='toprow2'>";

			echo " <td>";
			_e( 'Name', 'wpsc' );
			echo " </td>";

			echo " <td>";
			_e( 'Quantity', 'wpsc' );
			echo " </td>";

			echo " <td>";
			_e( 'Price', 'wpsc' );
			echo " </td>";

			echo " <td>";
			_e( 'GST', 'wpsc' );
			echo " </td>";

			echo " <td>";
			_e( 'Shipping', 'wpsc' );
			echo " </td>";

			echo " <td>";
			_e( 'Total', 'wpsc' );
			echo " </td>";

			echo "</tr>";

			$gsttotal = false;
			$endtotal = $total_shipping = 0;
			foreach ( (array)$cart_log as $cart_row ) {
				$alternate = "";
				$j++;

				if ( ($j % 2) != 0 )
					$alternate = "class='alt'";

				$variation_list = '';

				$billing_country = !empty($country_data[0]['value']) ? $country_data[0]['value'] : '';
				$shipping_country = !empty($country_data[0]['value']) ? $country_data[0]['value'] : '';

				$shipping = $cart_row['pnp'];
				$total_shipping += $shipping;
				echo "<tr $alternate>";

				echo " <td>";
				echo $cart_row['name'];
				echo $variation_list;
				echo " </td>";

				echo " <td>";
				echo $cart_row['quantity'];
				echo " </td>";

				echo " <td>";
				$price = $cart_row['price'] * $cart_row['quantity'];
				echo wpsc_currency_display( $price );
				echo " </td>";

				echo " <td>";
				$gst = $cart_row['tax_charged'];
				if( $gst > 0)
					$gsttotal += $gst;
				echo wpsc_currency_display( $gst , array('display_as_html' => false) );
				echo " </td>";

				echo " <td>";
				echo wpsc_currency_display( $shipping , array('display_as_html' => false) );
				echo " </td>";

				echo " <td>";
				$endtotal += $price;
				echo wpsc_currency_display( ( $shipping + $price ), array('display_as_html' => false)  );
				echo " </td>";

				echo '</tr>';
			}
			echo "<tr >";

			echo " <td>";
			echo " </td>";

			echo " <td>";
			echo " </td>";

			echo " <td>";
			echo " <td>";
			echo " </td>";
			echo " </td>";

			echo " <td>";
			echo "<strong>" . __( 'Total Shipping', 'wpsc' ) . ":</strong><br />";
			echo "<strong>" . __( 'Total Tax', 'wpsc' ) . ":</strong><br />";
			echo "<strong>" . __( 'Final Total', 'wpsc' ) . ":</strong>";
			echo " </td>";

			echo " <td>";
			$total_shipping += $purchase['base_shipping'];
			$endtotal += $total_shipping;
			$endtotal += $purchase['wpec_taxes_total'];
			echo wpsc_currency_display( $total_shipping, array('display_as_html' => false)  ) . "<br />";
			if ( $gsttotal ){ //if false then must be exclusive.. doesnt seem too reliable needs more testing
				echo wpsc_currency_display( $gsttotal , array('display_as_html' => false) ). "<br />";
			} else {
				echo wpsc_currency_display( $purchase['wpec_taxes_total'] , array('display_as_html' => false) ). "<br />";
			}
			echo wpsc_currency_display( $endtotal , array('display_as_html' => false) );
			echo " </td>";

			echo '</tr>';

			echo "</table>";
			echo "<br />";

			echo "<strong>" . __( 'Customer Details', 'wpsc' ) . ":</strong>";
			echo "<table class='customer_details'>";


			$usersql = $wpdb->prepare( "SELECT `".WPSC_TABLE_SUBMITED_FORM_DATA."`.value, `".WPSC_TABLE_CHECKOUT_FORMS."`.* FROM `".WPSC_TABLE_CHECKOUT_FORMS."` LEFT JOIN `".WPSC_TABLE_SUBMITED_FORM_DATA."` ON `".WPSC_TABLE_CHECKOUT_FORMS."`.id = `".WPSC_TABLE_SUBMITED_FORM_DATA."`.`form_id` WHERE `".WPSC_TABLE_SUBMITED_FORM_DATA."`.log_id = %d OR `".WPSC_TABLE_CHECKOUT_FORMS."`.type = 'heading' ORDER BY `".WPSC_TABLE_CHECKOUT_FORMS."`.`checkout_order`", $purchase['id'] );
			$formfields = $wpdb->get_results($usersql, ARRAY_A);
			if ( !empty($formfields) ) {

				foreach ( (array)$formfields as $form_field ) {
					// If its a heading display the Name otherwise continue on
					if( 'heading' == $form_field['type'] ){
						echo "  <tr><td colspan='2'>" . $form_field['name'] . ":</td></tr>";
						continue;
					}

					switch ($form_field['unique_name']){
						case 'shippingcountry':
						case 'billingcountry':
						$country = unserialize($form_field['value']);
							 if(is_array($country))
							 	$country = $country[0];
							 else
							 	$country = $form_field['value'];

							 echo "  <tr><td>" . $form_field['name'] . ":</td><td>".$country ."</td></tr>";
							break;

						case 'billingstate':
						case 'shippingstate':
							if(is_numeric($form_field['value']))
								$state = wpsc_get_state_by_id($form_field['value'],'name');
 	   						else
 	            				$state = $form_field['value'];

 	            			 echo "  <tr><td>" . $form_field['name'] . ":</td><td>".$state ."</td></tr>";
							break;

						default:
							echo "  <tr><td>" . $form_field['name'] . ":</td><td>" . esc_html( $form_field['value'] ) . "</td></tr>";

					}
				}
			}

			$payment_gateway_names = '';
			$payment_gateway_names = get_option('payment_gateway_names');

			foreach ( (array)$payment_gateway_names as $gatewayname ) {
				//if the gateway has a custom name
				if (!empty ($gatewayname) )
					$display_name = $payment_gateway_names[$purchase_log[0]['gateway']];
				else{
				//if not fall back on default name
					foreach ( (array)$nzshpcrt_gateways as $gateway ){
						if ( $gateway['internalname'] == $purchase['gateway'])
							$display_name = $gateway['name'];
					}
				}
			}

			echo "  <tr><td>" . __( 'Payment Method', 'wpsc' ) . ":</td><td>" . $display_name . "</td></tr>";
			echo "  <tr><td>" . __( 'Purchase #', 'wpsc' ) . ":</td><td>" . $purchase['id'] . "</td></tr>";
			if ( $purchase['transactid'] != '' ) {
				echo "  <tr><td>" . __( 'Transaction Id', 'wpsc' ) . ":</td><td>" . $purchase['transactid'] . "</td></tr>";
			}
			echo "</table>";
		}
		echo "  </div>\n\r";
		echo "  </div>\n\r";
		echo " </td>\n\r";
		echo "</tr>\n\r";
	}
}

?>