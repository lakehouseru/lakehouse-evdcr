<?php

/**
 * WP eCommerce AJAX and Init functions
 *
 * These are the WPSC AJAX and Init functions
 *
 * @package wp-e-commerce
 * @since 3.7
 */
function wpsc_special_widget() {
	wpsc_add_to_cart();
}

if ( isset( $_REQUEST['wpsc_ajax_action'] ) && ($_REQUEST['wpsc_ajax_action'] == 'special_widget' || $_REQUEST['wpsc_ajax_action'] == 'donations_widget') ) {
	add_action( 'init', 'wpsc_special_widget' );
}

/**
 * add_to_cart function, used through ajax and in normal page loading.
 * No parameters, returns nothing
 */
function wpsc_add_to_cart() {
	global $wpsc_cart;
	/// default values
	$default_parameters['variation_values'] = null;
	$default_parameters['quantity'] = 1;
	$default_parameters['provided_price'] = null;
	$default_parameters['comment'] = null;
	$default_parameters['time_requested'] = null;
	$default_parameters['custom_message'] = null;
	$default_parameters['file_data'] = null;
	$default_parameters['is_customisable'] = false;
	$default_parameters['meta'] = null;

	$provided_parameters = array();

	/// sanitise submitted values
	$product_id = apply_filters( 'wpsc_add_to_cart_product_id', (int)$_POST['product_id'] );

	// compatibility with older themes
	if ( isset( $_POST['wpsc_quantity_update'] ) && is_array( $_POST['wpsc_quantity_update'] ) ) {
		$_POST['wpsc_quantity_update'] = $_POST['wpsc_quantity_update'][$product_id];
	}

	if(isset($_POST['variation'])){
		foreach ( (array)$_POST['variation'] as $key => $variation )
			$provided_parameters['variation_values'][(int)$key] = (int)$variation;

		if ( count( $provided_parameters['variation_values'] ) > 0 ) {
			$variation_product_id = wpsc_get_child_object_in_terms( $product_id, $provided_parameters['variation_values'], 'wpsc-variation' );
			if ( $variation_product_id > 0 )
				$product_id = $variation_product_id;
		}

	}

	if ((isset($_POST['quantity']) && $_POST['quantity'] > 0) && (!isset( $_POST['wpsc_quantity_update'] )) ) {
		$provided_parameters['quantity'] = (int)$_POST['quantity'];
	} else if ( isset( $_POST['wpsc_quantity_update'] ) ) {
		$wpsc_cart->remove_item( $_POST['key'] );
		$provided_parameters['quantity'] = (int)$_POST['wpsc_quantity_update'];
	}

	if (isset( $_POST['is_customisable']) &&  $_POST['is_customisable'] == 'true' ) {
		$provided_parameters['is_customisable'] = true;

		if ( isset( $_POST['custom_text'] ) ) {
			$provided_parameters['custom_message'] = $_POST['custom_text'];
		}
		if ( isset( $_FILES['custom_file'] ) ) {
			$provided_parameters['file_data'] = $_FILES['custom_file'];
		}
	}
	if ( isset($_POST['donation_price']) && ((float)$_POST['donation_price'] > 0 ) ) {
		$provided_parameters['provided_price'] = (float)$_POST['donation_price'];
	}
	$parameters = array_merge( $default_parameters, (array)$provided_parameters );

	$state = $wpsc_cart->set_item( $product_id, $parameters );

	$product = get_post( $product_id );

	if ( $state == true ) {
		$cart_messages[] = str_replace( "[product_name]", stripslashes( $product->post_title ), __( 'You just added "[product_name]" to your cart.', 'wpsc' ) );
	} else {
		if ( $parameters['quantity'] <= 0 ) {
			$cart_messages[] = __( 'Sorry, but you cannot add zero items to your cart', 'wpsc' );
		} else if ( $wpsc_cart->get_remaining_quantity( $product_id, $parameters['variation_values'], $parameters['quantity'] ) > 0 ) {
			$quantity = $wpsc_cart->get_remaining_quantity( $product_id, $parameters['variation_values'], $parameters['quantity'] );
			$cart_messages[] = sprintf( _n( 'Sorry, but there is only %s of this item in stock.', 'Sorry, but there are only %s of this item in stock.', $quantity, 'wpsc' ), $quantity );
		} else {
			$cart_messages[] = sprintf( __( 'Sorry, but the item "%s" is out of stock.', 'wpsc' ), $product->post_title	);
		}
	}

	if ( isset($_GET['ajax']) && $_GET['ajax'] == 'true' ) {
		if ( ($product_id != null) && (get_option( 'fancy_notifications' ) == 1) ) {
			echo "if(jQuery('#fancy_notification_content')) {\n\r";
			echo "   jQuery('#fancy_notification_content').html(\"" . str_replace( array( "\n", "\r" ), array( '\n', '\r' ), addslashes( fancy_notification_content( $cart_messages ) ) ) . "\");\n\r";
			echo "   jQuery('#loading_animation').css('display', 'none');\n\r";
			echo "   jQuery('#fancy_notification_content').css('display', 'block');\n\r";
			echo "}\n\r";
			$error_messages = array( );
		}

		ob_start();

		include_once( wpsc_get_template_file_path( 'wpsc-cart_widget.php' ) );

		$output = ob_get_contents();
		ob_end_clean();
		$output = str_replace( Array( "\n", "\r" ), Array( "\\n", "\\r" ), addslashes( $output ) );
		echo "jQuery('div.shopping-cart-wrapper').html('$output');\n";


		if ( get_option( 'show_sliding_cart' ) == 1 ) {
			if ( (wpsc_cart_item_count() > 0) || (count( $cart_messages ) > 0) ) {
				$_SESSION['slider_state'] = 1;
				echo "
               jQuery('#sliding_cart').slideDown('fast',function(){
                  jQuery('#fancy_collapser').attr('src', ('".WPSC_CORE_IMAGES_URL."/minus.png'));
               });
         ";
			} else {
				$_SESSION['slider_state'] = 0;
				echo "
               jQuery('#sliding_cart').slideUp('fast',function(){
                  jQuery('#fancy_collapser').attr('src', ('".WPSC_CORE_IMAGES_URL."/plus.png'));
               });
         ";
			}
		}

		echo "jQuery('.cart_message').delay(3000).slideUp(500);";

		do_action( 'wpsc_alternate_cart_html', $cart_messages );
		exit();
	}
}

// execute on POST and GET
if ( isset( $_REQUEST['wpsc_ajax_action'] ) && ($_REQUEST['wpsc_ajax_action'] == 'add_to_cart') ) {
	add_action( 'init', 'wpsc_add_to_cart' );
}

function wpsc_get_cart() {
	global $wpsc_cart;

	ob_start();

	include_once( wpsc_get_template_file_path( 'wpsc-cart_widget.php' ) );
	$output = ob_get_contents();

	ob_end_clean();

	$output = str_replace( Array( "\n", "\r" ), Array( "\\n", "\\r" ), addslashes( $output ) );
	echo "jQuery('div.shopping-cart-wrapper').html('$output');\n";


	if ( get_option( 'show_sliding_cart' ) == 1 ) {
		if ( (wpsc_cart_item_count() > 0) || (count( $cart_messages ) > 0) ) {
			$_SESSION['slider_state'] = 1;
			echo "
            jQuery('#sliding_cart').slideDown('fast',function(){
               jQuery('#fancy_collapser').attr('src', (WPSC_CORE_IMAGES_URL+'/minus.png'));
            });
      ";
		} else {
			$_SESSION['slider_state'] = 0;
			echo "
            jQuery('#sliding_cart').slideUp('fast',function(){
               jQuery('#fancy_collapser').attr('src', (WPSC_CORE_IMAGES_URL+'/plus.png'));
            });
      ";
		}
	}


	do_action( 'wpsc_alternate_cart_html', '' );
	exit();
}

if ( isset( $_REQUEST['wpsc_ajax_action'] ) && ($_REQUEST['wpsc_ajax_action'] == 'get_cart') ) {
	add_action( 'init', 'wpsc_get_cart' );
}

/**
 * empty cart function, used through ajax and in normal page loading.
 * No parameters, returns nothing
 */
function wpsc_empty_cart() {
	global $wpsc_cart;
	$wpsc_cart->empty_cart( false );

	if ( $_REQUEST['ajax'] == 'true' ) {
		ob_start();

		include_once( wpsc_get_template_file_path( 'wpsc-cart_widget.php' ) );
		$output = ob_get_contents();

		ob_end_clean();
		$output = str_replace( Array( "\n", "\r" ), Array( "\\n", "\\r" ), addslashes( $output ) );
		echo "jQuery('div.shopping-cart-wrapper').html('$output');";
		do_action( 'wpsc_alternate_cart_html' );

		if ( get_option( 'show_sliding_cart' ) == 1 ) {
			$_SESSION['slider_state'] = 0;
			echo "
            jQuery('#sliding_cart').slideUp('fast',function(){
               jQuery('#fancy_collapser').attr('src', (WPSC_CORE_IMAGES_URL+'/plus.png'));
            });
      ";
		}
		exit();
	}

	// this if statement is needed, as this function also runs on returning from the gateway
	if ( $_REQUEST['wpsc_ajax_action'] == 'empty_cart' ) {
		wp_redirect( remove_query_arg( array( 'wpsc_ajax_action', 'ajax' ) ) );
		exit();
	}
}

// execute on POST and GET
if ( isset( $_REQUEST['wpsc_ajax_action'] ) && (($_REQUEST['wpsc_ajax_action'] == 'empty_cart') || (isset($_GET['sessionid'])  && ($_GET['sessionid'] > 0))) ) {
	add_action( 'init', 'wpsc_empty_cart' );
}

/**
 * coupons price, used through ajax and in normal page loading.
 * No parameters, returns nothing
 */
function wpsc_coupon_price( $currCoupon = '' ) {
	global $wpsc_cart, $wpsc_coupons;
	if ( isset( $_POST['coupon_num'] ) && $_POST['coupon_num'] != '' ) {
		$coupon = esc_sql( $_POST['coupon_num'] );
		$_SESSION['coupon_numbers'] = $coupon;
		$wpsc_coupons = new wpsc_coupons( $coupon );

		if ( $wpsc_coupons->validate_coupon() ) {
			$discountAmount = $wpsc_coupons->calculate_discount();
			$wpsc_cart->apply_coupons( $discountAmount, $coupon );
			$wpsc_coupons->errormsg = false;
		} else {
			$wpsc_coupons->errormsg = true;
			$wpsc_cart->coupons_amount = 0;
			$wpsc_cart->coupons_name = '';
		}
	} else if ( (!isset( $_POST['coupon_num'] ) || $_POST['coupon_num'] == '') && $currCoupon == '' ) {
		$wpsc_cart->coupons_amount = 0;
		$wpsc_cart->coupons_name = '';
	} else if ( $currCoupon != '' ) {
		$coupon = esc_sql( $currCoupon );
		$_SESSION['coupon_numbers'] = $coupon;
		$wpsc_coupons = new wpsc_coupons( $coupon );

		if ( $wpsc_coupons->validate_coupon() ) {

			$discountAmount = $wpsc_coupons->calculate_discount();
			$wpsc_cart->apply_coupons( $discountAmount, $coupon );
			$wpsc_coupons->errormsg = false;
		}
	}
}

// execute on POST and GET
if ( isset( $_POST['coupon_num'] ) ) {
	add_action( 'init', 'wpsc_coupon_price' );
}

/**
 * update quantity function, used through ajax and in normal page loading.
 * No parameters, returns nothing
 */
function wpsc_update_item_quantity() {
	global $wpsc_cart;

	if ( is_numeric( $_POST['key'] ) ) {
		$key = (int)$_POST['key'];
		if ( $_POST['quantity'] > 0 ) {
			// if the quantity is greater than 0, update the item;
			$parameters['quantity'] = (int)$_POST['quantity'];
			$wpsc_cart->edit_item( $key, $parameters );
		} else {
			// if the quantity is 0, remove the item.
			$wpsc_cart->remove_item( $key );
		}
		if ( isset( $_SESSION['coupon_numbers'] ) ) {
			wpsc_coupon_price( $_SESSION['coupon_numbers'] );
		}
	}

	if ( isset( $_REQUEST['ajax'] ) && $_REQUEST['ajax'] == 'true' ) {
		ob_start();

		include_once( wpsc_get_template_file_path( 'wpsc-cart_widget.php' ) );
		$output = ob_get_contents();

		ob_end_clean();

		$output = str_replace( Array( "\n", "\r" ), Array( "\\n", "\\r" ), addslashes( $output ) );

		echo "jQuery('div.shopping-cart-wrapper').html('$output');\n";
		do_action( 'wpsc_alternate_cart_html' );


		exit();
	}
}

// execute on POST and GET
if ( isset( $_REQUEST['wpsc_update_quantity'] ) && ($_REQUEST['wpsc_update_quantity'] == 'true') ) {
	add_action( 'init', 'wpsc_update_item_quantity' );
}

function wpsc_update_product_rating() {
	global $wpdb;
	$nowtime = time();
	$product_id = absint( $_POST['product_id'] );
	$ip_number = $_SERVER['REMOTE_ADDR'];
	$rating = absint( $_POST['product_rating'] );

	$cookie_data = explode( ",", $_COOKIE['voting_cookie'][$product_id] );

	if ( is_numeric( $cookie_data[0] ) && ($cookie_data[0] > 0) ) {
		$vote_id = absint( $cookie_data[0] );
		$wpdb->update( WPSC_TABLE_PRODUCT_RATING, array(
		'rated' => $rating
		), array( 'id' => $vote_id ) );
	} else {
		$wpdb->insert( WPSC_TABLE_PRODUCT_RATING, array(
		'ipnum' => $ip_number,
		'productid' => $product_id,
		'rated' => $rating,
		'time' => $nowtime
		) );
		$data = $wpdb->get_results( "SELECT `id`,`rated` FROM `" . WPSC_TABLE_PRODUCT_RATING . "` WHERE `ipnum`='" . $ip_number . "' AND `productid` = '" . $product_id . "'  AND `rated` = '" . $rating . "' AND `time` = '" . $nowtime . "' ORDER BY `id` DESC LIMIT 1", ARRAY_A );
		$vote_id = $data[0]['id'];
		setcookie( "voting_cookie[$prodid]", ($vote_id . "," . $rating ), time() + (60 * 60 * 24 * 360) );
	}
	if ( $_POST['ajax'] == 'true' ) {

		exit();
	}
}

// execute on POST and GET
if ( isset( $_REQUEST['wpsc_ajax_action'] ) && ($_REQUEST['wpsc_ajax_action'] == 'rate_product') ) {
	add_action( 'init', 'wpsc_update_product_rating' );
}

/**
 * update_shipping_price function, used through ajax and in normal page loading.
 * No parameters, returns nothing
 */
function wpsc_update_shipping_price() {
	global $wpsc_cart;
	$quote_shipping_method = $_POST['key1'];
	$quote_shipping_option = $_POST['key'];
	if(!empty($quote_shipping_option) && !empty($quote_shipping_method)){
		$wpsc_cart->update_shipping( $quote_shipping_method, $quote_shipping_option );
		echo "
		if(jQuery('.pricedisplay.checkout-shipping .pricedisplay')){
			jQuery('.pricedisplay.checkout-shipping > .pricedisplay:first').html(\"" . wpsc_cart_shipping() . "\");
			jQuery('.shoppingcart .pricedisplay.checkout-shipping > .pricedisplay:first').html(\"" . wpsc_cart_shipping() . "\");
		} else {
			jQuery('.pricedisplay.checkout-shipping').html(\"" . wpsc_cart_shipping() . "\");}";
		echo "jQuery('.pricedisplay.checkout-total').html(\"" . wpsc_cart_total() . "\");\n\r";
	}
	exit();
}
// execute on POST and GET
if ( isset( $_REQUEST['wpsc_ajax_action'] ) && ($_REQUEST['wpsc_ajax_action'] == 'update_shipping_price') ) {
	add_action( 'init', 'wpsc_update_shipping_price' );
}

/**
 * update_shipping_price function, used through ajax and in normal page loading.
 * No parameters, returns nothing
 */
function wpsc_get_rating_count() {
	global $wpdb, $wpsc_cart;
	$prodid = $_POST['product_id'];
	$count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) AS `count` FROM `" . WPSC_TABLE_PRODUCT_RATING . "` WHERE `productid` = %d", $prodid ) );
	echo $count . "," . $prodid;
	exit();
}

// execute on POST and GET
if ( isset( $_REQUEST['get_rating_count'] ) && ($_REQUEST['get_rating_count'] == 'true') && is_numeric( $_POST['product_id'] ) ) {
	add_action( 'init', 'wpsc_get_rating_count' );
}

/**
 * update_product_page_price function, used through ajax with variations
 * No parameters, returns nothing
 */
function wpsc_update_product_price() {

	$from = '';
	$change_price = true;
	$product_id = (int) $_POST['product_id'];
	$variations = array();
	$response = array(
		'product_id' => $product_id,
		'variation_found' => false,
	);
	if ( ! empty( $_POST['variation'] ) ) {
		foreach ( $_POST['variation'] as $variation ) {
			if ( is_numeric( $variation ) ) {
				$variations[] = (int)$variation;
			}
		}

		do_action( 'wpsc_update_variation_product', $product_id, $variations );

		$stock = wpsc_check_variation_stock_availability( $product_id, $variations );

		if ( $stock !== false ) {
			$response['variation_found'] = true;
			if ( $stock === 0 ) {
				$response += array(
					'product_msg'     =>  __( 'Sorry, but this variation is out of stock.', 'wpsc' ),
					'variation_msg'   => __( 'Variation not in stock', 'wpsc' ),
					'stock_available' => false,
				);
			} else {
				$response += array(
					'variation_msg'   => __( 'Product in stock', 'wpsc' ),
					'stock_available' => true,
				);
			}

			if ( $change_price ) {
				$old_price = wpsc_calculate_price( $product_id, $variations, false );
				$you_save_amount = wpsc_you_save( array( 'product_id' => $product_id, 'type' => 'amount', 'variations' => $variations ) );
				$you_save_percentage = wpsc_you_save( array( 'product_id' => $product_id, 'variations' => $variations ) );
				$price = wpsc_calculate_price( $product_id, $variations, true );
				$response += array(
					'old_price'         => wpsc_currency_display( $old_price, array( 'display_as_html' => false ) ),
					'numeric_old_price' => (float) number_format( $old_price ),
					'you_save'          => wpsc_currency_display( $you_save_amount, array( 'display_as_html' => false ) ) . "! (" . $you_save_percentage . "%)",
					'price'             => $from . wpsc_currency_display( $price, array( 'display_as_html' => false ) ),
					'numeric_price'     => (float) number_format( $price ),
				);
			}
		}
	}

	echo json_encode( $response );
	exit();
}

// execute on POST and GET
if ( isset( $_REQUEST['update_product_price'] ) && ($_REQUEST['update_product_price'] == 'true') && ! empty( $_POST['product_id'] ) && is_numeric( $_POST['product_id'] ) ) {
	add_action( 'init', 'wpsc_update_product_price' );
}

/**
 * update location function, used through ajax and in normal page loading.
 * No parameters, returns nothing
 */
function wpsc_update_location() {
	global $wpdb, $wpsc_cart;

	if ( $_POST['country'] != null ) {
		$_SESSION['wpsc_delivery_country'] = $_POST['country'];
		if ( $_SESSION['wpsc_selected_country'] == null ) {
			$_SESSION['wpsc_selected_country'] = $_POST['country'];
		}
		if ( ! empty( $_POST['region'] ) ) {
			$_SESSION['wpsc_delivery_region'] = $_POST['region'];
			if ( $_SESSION['wpsc_selected_region'] == null ) {
				$_SESSION['wpsc_selected_region'] = $_POST['region'];
			}
		} else if ( $_SESSION['wpsc_selected_region'] == '' ) {
			$_SESSION['wpsc_delivery_region'] = get_option( 'base_region' );
			$_SESSION['wpsc_selected_region'] = get_option( 'base_region' );
		}

		if ( $_SESSION['wpsc_delivery_region'] == '' ) {
			$_SESSION['wpsc_delivery_region'] = $_SESSION['wpsc_selected_region'];
		}
	}

	if ( ! empty( $_POST['zipcode'] ) ) {
		$_SESSION['wpsc_zipcode'] = $_POST['zipcode'];
	}

	$delivery_region_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(`regions`.`id`) FROM `" . WPSC_TABLE_REGION_TAX . "` AS `regions` INNER JOIN `" . WPSC_TABLE_CURRENCY_LIST . "` AS `country` ON `country`.`id` = `regions`.`country_id` WHERE `country`.`isocode` IN('%s')",  $_SESSION['wpsc_delivery_country'] ) );
	if ( $delivery_region_count < 1 ) {
		$_SESSION['wpsc_delivery_region'] = null;
	}

	$selected_region_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(`regions`.`id`) FROM `" . WPSC_TABLE_REGION_TAX . "` AS `regions` INNER JOIN `" . WPSC_TABLE_CURRENCY_LIST . "` AS `country` ON `country`.`id` = `regions`.`country_id` WHERE `country`.`isocode` IN('%s')", $_SESSION['wpsc_selected_country'] ) );
	if ( $selected_region_count < 1 ) {
		$_SESSION['wpsc_selected_region'] = null;
	}

	$wpsc_cart->update_location();
	$wpsc_cart->get_shipping_method();
	$wpsc_cart->get_shipping_option();
	if ( $wpsc_cart->selected_shipping_method != '' ) {
		$wpsc_cart->update_shipping( $wpsc_cart->selected_shipping_method, $wpsc_cart->selected_shipping_option );
	}

	if ( isset( $_GET['ajax'] ) && $_GET['ajax'] == 'true' )
		exit;
}

// execute on POST and GET
if ( isset( $_REQUEST['wpsc_ajax_actions'] ) && ($_REQUEST['wpsc_ajax_actions'] == 'update_location') ) {
	add_action( 'init', 'wpsc_update_location' );
}

function wpsc_cart_html_page() {
	require_once(WPSC_FILE_PATH . "/wpsc-includes/shopping_cart_container.php");
	exit();
}

// execute on POST and GET
if ( isset( $_REQUEST['wpsc_action'] ) && ($_REQUEST['wpsc_action'] == 'cart_html_page') ) {
	add_action( 'init', 'wpsc_cart_html_page', 110 );
}

/**
 * submit checkout function, used through ajax and in normal page loading.
 * No parameters, returns nothing
 */
function wpsc_submit_checkout() {
	global $wpdb, $wpsc_cart, $user_ID, $nzshpcrt_gateways, $wpsc_shipping_modules, $wpsc_gateways;

	$num_items = 0;
	$use_shipping = 0;
	$disregard_shipping = 0;

	do_action( 'wpsc_before_submit_checkout' );

	$_SESSION['wpsc_checkout_misc_error_messages'] = array( );
	$wpsc_checkout = new wpsc_checkout();
	$selected_gateways = get_option( 'custom_gateway_options' );
	$submitted_gateway = $_POST['custom_gateway'];
	$options = get_option( 'custom_shipping_options' );
	$form_validity = $wpsc_checkout->validate_forms();
	extract( $form_validity ); // extracts $is_valid and $error_messages

	if ( $_POST['agree'] != 'yes' ) {
		$_SESSION['wpsc_checkout_misc_error_messages'][] = __( 'Please agree to the terms and conditions, otherwise we cannot process your order.', 'wpsc' );
		$is_valid = false;
	}
	$selectedCountry = $wpdb->get_results( $wpdb->prepare( "SELECT id, country FROM `" . WPSC_TABLE_CURRENCY_LIST . "` WHERE isocode = '%s' ", $_SESSION['wpsc_delivery_country'] ), ARRAY_A );
	foreach ( $wpsc_cart->cart_items as $cartitem ) {
		if( ! empty( $cartitem->meta[0]['no_shipping'] ) ) continue;
		$categoriesIDs = $cartitem->category_id_list;
		foreach ( (array)$categoriesIDs as $catid ) {
			if ( is_array( $catid ) )
				$countries = wpsc_get_meta( $catid[0], 'target_market', 'wpsc_category' );
			else
				$countries = wpsc_get_meta( $catid, 'target_market', 'wpsc_category' );

			if ( !empty($countries) && !in_array( $selectedCountry[0]['id'], (array)$countries ) ) {
				$errormessage = sprintf( __( '%s cannot be shipped to %s. To continue with your transaction please remove this product from the list below.', 'wpsc' ), $cartitem->product_name, $selectedCountry[0]['country'] );
				$_SESSION['categoryAndShippingCountryConflict'] = $errormessage;
				$is_valid = false;
			}
		}
		//count number of items, and number of items using shipping
		$num_items++;
		if ( $cartitem->uses_shipping != 1 )
			$disregard_shipping++;
		else
			$use_shipping++;

	}
	if ( array_search( $submitted_gateway, $selected_gateways ) !== false )
		$_SESSION['wpsc_previous_selected_gateway'] = $submitted_gateway;
	else
		$is_valid = false;

	if ( get_option( 'do_not_use_shipping' ) == 0 && ($wpsc_cart->selected_shipping_method == null || $wpsc_cart->selected_shipping_option == null) && ( $num_items != $disregard_shipping ) ) {
		$_SESSION['wpsc_checkout_misc_error_messages'][] = __( 'You must select a shipping method, otherwise we cannot process your order.', 'wpsc' );
		$is_valid = false;
	}
	if ( (get_option( 'do_not_use_shipping' ) != 1) && (in_array( 'ups', (array)$options )) && $_SESSION['wpsc_zipcode'] == '' && ( $num_items != $disregard_shipping ) ) {
			$_SESSION['categoryAndShippingCountryConflict'] = __( 'Please enter a Zipcode and click calculate to proceed', 'wpsc' );
			$is_valid = false;
	}
	if ( $is_valid == true ) {
		$_SESSION['categoryAndShippingCountryConflict'] = '';
		// check that the submitted gateway is in the list of selected ones
		$sessionid = (mt_rand( 100, 999 ) . time());
		$_SESSION['wpsc_sessionid'] = $sessionid;
		$subtotal = $wpsc_cart->calculate_subtotal();
		if ( $wpsc_cart->has_total_shipping_discount() == false )
			$base_shipping = $wpsc_cart->calculate_base_shipping();
		else
			$base_shipping = 0;

		$delivery_country = $wpsc_cart->delivery_country;
		$delivery_region = $wpsc_cart->delivery_region;

		if ( wpsc_uses_shipping ( ) ) {
			$shipping_method = $wpsc_cart->selected_shipping_method;
			$shipping_option = $wpsc_cart->selected_shipping_option;
		} else {
			$shipping_method = '';
			$shipping_option = '';
		}
		if ( isset( $_POST['how_find_us'] ) )
			$find_us = $_POST['how_find_us'];
		else
			$find_us = '';

		//keep track of tax if taxes are exclusive
		$wpec_taxes_controller = new wpec_taxes_controller();
		if ( !$wpec_taxes_controller->wpec_taxes_isincluded() ) {
			$tax = $wpsc_cart->calculate_total_tax();
			$tax_percentage = $wpsc_cart->tax_percentage;
		} else {
			$tax = 0.00;
			$tax_percentage = 0.00;
		}
		$total = $wpsc_cart->calculate_total_price();
		$wpdb->insert( WPSC_TABLE_PURCHASE_LOGS, array(
			'totalprice' => $total,
			'statusno' => '0',
			'sessionid' => $sessionid,
			'user_ID' => (int)$user_ID,
			'date' => time(),
			'gateway' => $submitted_gateway,
			'billing_country' => $wpsc_cart->selected_country,
			'shipping_country' => $delivery_country,
			'billing_region' => $wpsc_cart->selected_region,
			'shipping_region' => $delivery_region,
			'base_shipping' => $base_shipping,
			'shipping_method' => $shipping_method,
			'shipping_option' => $shipping_option,
			'plugin_version' => WPSC_VERSION,
			'discount_value' => $wpsc_cart->coupons_amount,
			'discount_data' => $wpsc_cart->coupons_name,
			'find_us' => $find_us,
			'wpec_taxes_total' => $tax,
			'wpec_taxes_rate' => $tax_percentage
		) );
		$purchase_log_id = $wpdb->insert_id;
		$wpsc_checkout->save_forms_to_db( $purchase_log_id );
		$wpsc_cart->save_to_db( $purchase_log_id );
		$wpsc_cart->submit_stock_claims( $purchase_log_id );
		if ( get_option( 'wpsc_also_bought' ) == 1 )
			wpsc_populate_also_bought_list();
		if( !isset( $our_user_id ) && isset( $user_ID ))
			$our_user_id = $user_ID;
		$wpsc_cart->log_id = $purchase_log_id;
		do_action( 'wpsc_submit_checkout', array( "purchase_log_id" => $purchase_log_id, "our_user_id" => $our_user_id ) );
		if ( get_option( 'permalink_structure' ) != '' )
			$separator = "?";
		else
			$separator = "&";

		// submit to gateway
		$current_gateway_data = &$wpsc_gateways[$submitted_gateway];
		if ( isset( $current_gateway_data['api_version'] ) && $current_gateway_data['api_version'] >= 2.0 ) {
			$merchant_instance = new $current_gateway_data['class_name']( $purchase_log_id );
			$merchant_instance->construct_value_array();
			do_action_ref_array( 'wpsc_pre_submit_gateway', array( &$merchant_instance ) );
			$merchant_instance->submit();
		} elseif ( ($current_gateway_data['internalname'] == $submitted_gateway) && ($current_gateway_data['internalname'] != 'google') ) {
			$gateway_used = $current_gateway_data['internalname'];
			$wpdb->update( WPSC_TABLE_PURCHASE_LOGS, array(
			'gateway' => $gateway_used
			), array( 'id' => $purchase_log_id ) );
			$current_gateway_data['function']( $separator, $sessionid );
		} elseif ( ($current_gateway_data['internalname'] == 'google') && ($current_gateway_data['internalname'] == $submitted_gateway) ) {
			$gateway_used = $current_gateway_data['internalname'];
			$wpdb->update( WPSC_TABLE_PURCHASE_LOGS, array(
			'gateway' => $gateway_used
			), array( 'id' => $purchase_log_id ) );
			$_SESSION['gateway'] = 'google';
			wp_redirect(get_option( 'shopping_cart_url' ));
			exit;
		}
	}
}

// execute on POST and GET
if ( isset( $_REQUEST['wpsc_action'] ) && ($_REQUEST['wpsc_action'] == 'submit_checkout') ) {
	add_action( 'init', 'wpsc_submit_checkout' );
}

function wpsc_product_rss() {
	global $wp_query, $wpsc_query;
	list($wp_query, $wpsc_query) = array( $wpsc_query, $wp_query ); // swap the wpsc_query object
	header( "Content-Type: application/xml; charset=UTF-8" );
	header( 'Content-Disposition: inline; filename="E-Commerce_Product_List.rss"' );
	require_once(WPSC_FILE_PATH . '/wpsc-includes/rss_template.php');
	list($wp_query, $wpsc_query) = array( $wpsc_query, $wp_query ); // swap the wpsc_query object
	exit();
}

if ( isset( $_REQUEST['wpsc_action'] ) && ($_REQUEST['wpsc_action'] == "rss") ) {
	add_action( 'template_redirect', 'wpsc_product_rss', 80 );
}

function wpsc_gateway_notification() {
	global $wpsc_gateways;
	$gateway_name = $_GET['gateway'];
	// work out what gateway we are getting the request from, run the appropriate code.
	if ( ($gateway_name != null) && isset( $wpsc_gateways[$gateway_name]['class_name'] ) ) {
		$merchant_class = $wpsc_gateways[$gateway_name]['class_name'];
		$merchant_instance = new $merchant_class( null, true );
		$merchant_instance->process_gateway_notification();
	}
	exit();
}

// execute on POST and GET
if ( isset( $_REQUEST['wpsc_action'] ) && ($_REQUEST['wpsc_action'] == 'gateway_notification') ) {
	add_action( 'init', 'wpsc_gateway_notification' );
}

if ( isset( $_GET['termsandconds'] ) && ($_GET['termsandconds'] === 'true') ) {
	echo wpautop( stripslashes( get_option( 'terms_and_conditions' ) ) );
	exit();
}

/**
 * wpsc_change_tax function, used through ajax and in normal page loading.
 * No parameters, returns nothing
 */
function wpsc_change_tax() {
	global $wpdb, $wpsc_cart;

	$form_id = absint( $_POST['form_id'] );

	$wpsc_selected_country = $wpsc_cart->selected_country;
	$wpsc_selected_region = $wpsc_cart->selected_region;

	$wpsc_delivery_country = $wpsc_cart->delivery_country;
	$wpsc_delivery_region = $wpsc_cart->delivery_region;


	$previous_country = $_SESSION['wpsc_selected_country'];
	if ( isset( $_POST['billing_country'] ) ) {
		$wpsc_selected_country = $_POST['billing_country'];
		$_SESSION['wpsc_selected_country'] = $wpsc_selected_country;
	}

	if ( isset( $_POST['billing_region'] ) ) {
		$wpsc_selected_region = absint( $_POST['billing_region'] );
		$_SESSION['wpsc_selected_region'] = $wpsc_selected_region;
	}

	$check_country_code = $wpdb->get_var( $wpdb->prepare( "SELECT `country`.`isocode` FROM `" . WPSC_TABLE_REGION_TAX . "` AS `region` INNER JOIN `" . WPSC_TABLE_CURRENCY_LIST . "` AS `country` ON `region`.`country_id` = `country`.`id` WHERE `region`.`id` = %d LIMIT 1", $_SESSION['wpsc_selected_region'] ) );

	if ( $_SESSION['wpsc_selected_country'] != $check_country_code ) {
		$wpsc_selected_region = null;
	}

	if ( isset( $_POST['shipping_country'] ) ) {
		$wpsc_delivery_country = $_POST['shipping_country'];
		$_SESSION['wpsc_delivery_country'] = $wpsc_delivery_country;
	}
	if ( isset( $_POST['shipping_region'] ) ) {
		$wpsc_delivery_region = absint( $_POST['shipping_region'] );
		$_SESSION['wpsc_delivery_region'] = $wpsc_delivery_region;
	}

	$check_country_code = $wpdb->get_var( $wpdb->prepare( "SELECT `country`.`isocode` FROM `" . WPSC_TABLE_REGION_TAX . "` AS `region` INNER JOIN `" . WPSC_TABLE_CURRENCY_LIST . "` AS `country` ON `region`.`country_id` = `country`.`id` WHERE `region`.`id` = %d LIMIT 1", $wpsc_delivery_region ) );

	if ( $wpsc_delivery_country != $check_country_code ) {
		$wpsc_delivery_region = null;
	}


	$wpsc_cart->update_location();
	$wpsc_cart->get_shipping_method();
	$wpsc_cart->get_shipping_option();
	if ( $wpsc_cart->selected_shipping_method != '' ) {
		$wpsc_cart->update_shipping( $wpsc_cart->selected_shipping_method, $wpsc_cart->selected_shipping_option );
	}

	$tax = $wpsc_cart->calculate_total_tax();
	$total = wpsc_cart_total();
	$total_input = wpsc_cart_total(false);
	if($wpsc_cart->coupons_amount >= wpsc_cart_total(false) && !empty($wpsc_cart->coupons_amount)){
		$total = 0;
	}
	if ( $wpsc_cart->total_price < 0 ) {
		$wpsc_cart->coupons_amount += $wpsc_cart->total_price;
		$wpsc_cart->total_price = null;
		$wpsc_cart->calculate_total_price();
	}
	ob_start();

	include_once( wpsc_get_template_file_path( 'wpsc-cart_widget.php' ) );
	$output = ob_get_contents();

	ob_end_clean();

	$output = str_replace( Array( "\n", "\r" ), Array( "\\n", "\\r" ), addslashes( $output ) );
	if ( get_option( 'lock_tax' ) == 1 ) {
		echo "jQuery('#current_country').val('" . esc_js( $_SESSION['wpsc_delivery_country'] ) . "'); \n";
		if ( $_SESSION['wpsc_delivery_country'] == 'US' && get_option( 'lock_tax' ) == 1 ) {
			$output = wpsc_shipping_region_list( $_SESSION['wpsc_delivery_country'], $_SESSION['wpsc_delivery_region'] );
			$output = str_replace( Array( "\n", "\r" ), Array( "\\n", "\\r" ), addslashes( $output ) );
			echo "jQuery('#region').remove();\n\r";
			echo "jQuery('#change_country').append(\"" . $output . "\");\n\r";
		}
	}


	foreach ( $wpsc_cart->cart_items as $key => $cart_item ) {
		echo "jQuery('#shipping_$key').html(\"" . wpsc_currency_display( $cart_item->shipping ) . "\");\n\r";
	}

	echo "jQuery('#checkout_shipping').html(\"" . wpsc_cart_shipping() . "\");\n\r";

	echo "jQuery('div.shopping-cart-wrapper').html('$output');\n";
	if ( get_option( 'lock_tax' ) == 1 ) {
		echo "jQuery('.shipping_country').val('" . esc_js( $_SESSION['wpsc_delivery_country'] ) . "') \n";
		$sql = $wpdb->prepare( "SELECT `country` FROM `" . WPSC_TABLE_CURRENCY_LIST . "` WHERE `isocode`= '%s'", $_SESSION['wpsc_selected_country'] );
		$country_name = $wpdb->get_var( $sql );
		echo "jQuery('.shipping_country_name').html('" . $country_name . "') \n";
	}


	$form_selected_country = null;
	$form_selected_region = null;
	$onchange_function = null;

	if ( ! empty( $_POST['billing_country'] ) && $_POST['billing_country'] != 'undefined' && !isset( $_POST['shipping_country'] ) ) {
		$form_selected_country = $wpsc_selected_country;
		$form_selected_region = $wpsc_selected_region;
		$onchange_function = 'set_billing_country';
	} else if ( ! empty( $_POST['shipping_country'] ) && $_POST['shipping_country'] != 'undefined' && !isset( $_POST['billing_country'] ) ) {
		$form_selected_country = $wpsc_delivery_country;
		$form_selected_region = $wpsc_delivery_region;
		$onchange_function = 'set_shipping_country';
	}

	if ( ($form_selected_country != null) && ($onchange_function != null) ) {
		$region_list = $wpdb->get_results( $wpdb->prepare( "SELECT `" . WPSC_TABLE_REGION_TAX . "`.* FROM `" . WPSC_TABLE_REGION_TAX . "`, `" . WPSC_TABLE_CURRENCY_LIST . "`  WHERE `" . WPSC_TABLE_CURRENCY_LIST . "`.`isocode` IN('%s') AND `" . WPSC_TABLE_CURRENCY_LIST . "`.`id` = `" . WPSC_TABLE_REGION_TAX . "`.`country_id`", $form_selected_country ), ARRAY_A );
		if ( $region_list != null ) {
			$title = (empty($_POST['billing_country']))?'shippingstate':'billingstate';
			$output = "<select name='collected_data[" . $form_id . "][1]' class='current_region' onchange='$onchange_function(\"region_country_form_$form_id\", \"$form_id\");' title='" . $title . "'>\n\r";

			foreach ( $region_list as $region ) {
				if ( $form_selected_region == $region['id'] ) {
					$selected = "selected='selected'";
				} else {
					$selected = "";
				}
				$output .= "   <option value='" . $region['id'] . "' $selected>" . htmlspecialchars( $region['name'] ) . "</option>\n\r";
			}
			$output .= "</select>\n\r";

			$output = str_replace( Array( "\n", "\r" ), Array( "\\n", "\\r" ), addslashes( $output ) );
			echo "jQuery('#region_select_$form_id').html(\"" . $output . "\");\n\r";
			echo "
				var wpsc_checkout_table_selector = jQuery('#region_select_$form_id').parents('.wpsc_checkout_table').attr('class');
				wpsc_checkout_table_selector = wpsc_checkout_table_selector.replace(' ','.');
				wpsc_checkout_table_selector = '.'+wpsc_checkout_table_selector;
				jQuery(wpsc_checkout_table_selector + ' input.billing_region').attr('disabled', 'disabled');
				jQuery(wpsc_checkout_table_selector + ' input.shipping_region').attr('disabled', 'disabled');
				jQuery(wpsc_checkout_table_selector + ' .billing_region').parent().parent().hide();
				jQuery(wpsc_checkout_table_selector + ' .shipping_region').parent().parent().hide();
			";
		} else {
			if ( get_option( 'lock_tax' ) == 1 ) {
				echo "jQuery('#region').hide();";
			}
			echo "jQuery('#region_select_$form_id').html('');\n\r";
			echo "
				var wpsc_checkout_table_selector = jQuery('#region_select_$form_id').parents('.wpsc_checkout_table').attr('class');
				wpsc_checkout_table_selector = wpsc_checkout_table_selector.replace(' ','.');
				wpsc_checkout_table_selector = '.'+wpsc_checkout_table_selector;
				jQuery(wpsc_checkout_table_selector + ' input.billing_region').removeAttr('disabled');
				jQuery(wpsc_checkout_table_selector + ' input.shipping_region').removeAttr('disabled');
				jQuery(wpsc_checkout_table_selector + ' .billing_region').parent().parent().show();
				jQuery(wpsc_checkout_table_selector + ' .shipping_region').parent().parent().show();
			";
		}
	}

	if ( $tax > 0 ) {
		echo "jQuery(\"tr.total_tax\").show();\n\r";
	} else {
		echo "jQuery(\"tr.total_tax\").hide();\n\r";
	}
	echo "jQuery('#checkout_tax').html(\"<span class='pricedisplay'>" . wpsc_cart_tax() . "</span>\");\n\r";
	echo "jQuery('#checkout_total').html(\"{$total}<input id='shopping_cart_total_price' type='hidden' value='{$total_input}' />\");\n\r";
	exit();
}

// execute on POST and GET
if ( isset( $_REQUEST['wpsc_ajax_action'] ) && ($_REQUEST['wpsc_ajax_action'] == 'change_tax') ) {
	add_action( 'init', 'wpsc_change_tax' );
}

/**
 * wpsc scale image function, dynamically resizes an image oif no image already exists of that size.
 */
function wpsc_scale_image() {
	global $wpdb;

	if ( !isset( $_REQUEST['wpsc_action'] ) || !isset( $_REQUEST['attachment_id'] ) || ( 'scale_image' != $_REQUEST['wpsc_action'] ) || !is_numeric( $_REQUEST['attachment_id'] ) )
		return false;

	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$attachment_id = absint( $_REQUEST['attachment_id'] );
	$width = absint( $_REQUEST['width'] );
	$height = absint( $_REQUEST['height'] );
	$intermediate_size = '';

	if ( (($width >= 10) && ($height >= 10)) && (($width <= 1024) && ($height <= 1024)) ) {
		$intermediate_size = "wpsc-{$width}x{$height}";
		$generate_thumbnail = true;
	} else {
		if ( isset( $_REQUEST['intermediate_size'] ) )
		$intermediate_size = esc_attr( $_REQUEST['intermediate_size'] );
		$generate_thumbnail = false;
	}

	// If the attachment ID is greater than 0, and the width and height is greater than or equal to 10, and less than or equal to 1024
	if ( ($attachment_id > 0) && ($intermediate_size != '') ) {
		// Get all the required information about the attachment
		$uploads = wp_upload_dir();

		$image_meta = get_post_meta( $attachment_id, '' );
		$file_path = get_attached_file( $attachment_id );
		foreach ( $image_meta as $meta_name => $meta_value ) { // clean up the meta array
			$image_meta[$meta_name] = maybe_unserialize( array_pop( $meta_value ) );
		}
		if ( !isset( $image_meta['_wp_attachment_metadata'] ) )
			$image_meta['_wp_attachment_metadata'] = '';
		$attachment_metadata = $image_meta['_wp_attachment_metadata'];

		if ( !isset( $attachment_metadata['sizes'] ) )
			$attachment_metadata['sizes'] = '';
		if ( !isset( $attachment_metadata['sizes'][$intermediate_size] ) )
			$attachment_metadata['sizes'][$intermediate_size] = '';

		// determine if we already have an image of this size
		if ( (count( $attachment_metadata['sizes'] ) > 0) && ($attachment_metadata['sizes'][$intermediate_size]) ) {
			$intermediate_image_data = image_get_intermediate_size( $attachment_id, $intermediate_size );
			if ( file_exists( $file_path ) ) {
				$original_modification_time = filemtime( $file_path );
				$cache_modification_time = filemtime( $uploads['basedir'] . "/" . $intermediate_image_data['path'] );
				if ( $original_modification_time < $cache_modification_time ) {
					$generate_thumbnail = false;
				}
			}
		}

		if ( $generate_thumbnail == true ) {
			//JS - 7.1.2010 - Added true parameter to function to not crop - causing issues on WPShop
			$intermediate_size_data = image_make_intermediate_size( $file_path, $width, $height, true );
			$attachment_metadata['sizes'][$intermediate_size] = $intermediate_size_data;
			wp_update_attachment_metadata( $attachment_id, $attachment_metadata );
			$intermediate_image_data = image_get_intermediate_size( $attachment_id, $intermediate_size );
		}

		/// if we are serving the page using SSL, we have to use for the image too.
		if ( is_ssl ( ) ) {
			$output_url = str_replace( "http://", "https://", $intermediate_image_data['url'] );
		} else {
			$output_url = $intermediate_image_data['url'];
		}
		wp_redirect( $output_url );
	} else {
		_e( "Invalid Image parameters", 'wpsc' );
	}
	exit();
}
add_action( 'init', 'wpsc_scale_image' );

function wpsc_download_file() {
	global $wpdb;

	if ( isset( $_GET['downloadid'] ) ) {
		// strip out anything that isnt 'a' to 'z' or '0' to '9'
		ini_set('max_execution_time',10800);
		$downloadid = preg_replace( "/[^a-z0-9]+/i", '', strtolower( $_GET['downloadid'] ) );
		$download_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `" . WPSC_TABLE_DOWNLOAD_STATUS . "` WHERE `uniqueid` = '%s' AND `downloads` > '0' AND `active`='1' LIMIT 1", $downloadid ), ARRAY_A );

		if ( is_null( $download_data ) && is_numeric( $downloadid ) )
		    $download_data = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `" . WPSC_TABLE_DOWNLOAD_STATUS . "` WHERE `id` = %d AND `downloads` > '0' AND `active`='1' AND `uniqueid` IS NULL LIMIT 1", $downloadid ), ARRAY_A );


		if ( (get_option( 'wpsc_ip_lock_downloads' ) == 1) && ($_SERVER['REMOTE_ADDR'] != null) ) {
			$ip_number = $_SERVER['REMOTE_ADDR'];
			if ( $download_data['ip_number'] == '' ) {
				// if the IP number is not set, set it
				$wpdb->update( WPSC_TABLE_DOWNLOAD_STATUS, array(
				'ip_number' => $ip_number
				), array( 'id' => $download_data['id'] ) );
			} else if ( $ip_number != $download_data['ip_number'] ) {
				// if the IP number is set but does not match, fail here.
				exit( _e( 'This download is no longer valid, Please contact the site administrator for more information.', 'wpsc' ) );
			}
		}

		$file_id = $download_data['fileid'];
		$file_data = wpsc_get_downloadable_file($file_id);

		if ( $file_data == null ) {
			exit( _e( 'This download is no longer valid, Please contact the site administrator for more information.', 'wpsc' ) );
		}

		if ( $download_data != null ) {

			if ( (int)$download_data['downloads'] >= 1 ) {
				$download_count = (int)$download_data['downloads'] - 1;
			} else {
				$download_count = 0;
			}


			$wpdb->update( WPSC_TABLE_DOWNLOAD_STATUS, array(
			'downloads' => $download_count
			), array( 'id' => $download_data['id'] ) );

			$cart_contents = $wpdb->get_results( $wpdb->prepare( "SELECT `" . WPSC_TABLE_CART_CONTENTS . "`.*, $wpdb->posts.`guid` FROM `" . WPSC_TABLE_CART_CONTENTS . "` LEFT JOIN $wpdb->posts ON `" . WPSC_TABLE_CART_CONTENTS . "`.`prodid`= $wpdb->posts.`post_parent` WHERE $wpdb->posts.`post_type` = 'wpsc-product-file' AND `purchaseid` = %d", $download_data['purchid'] ), ARRAY_A );
			$dl = 0;

			foreach ( $cart_contents as $cart_content ) {
				if ( $cart_content['guid'] == 1 ) {
					$dl++;
				}
			}
			if ( count( $cart_contents ) == $dl ) {
				$wpdb->update( WPSC_TABLE_PURCHASE_LOGS, array(
				'processed' => '4'
				), array( 'id' => $download_data['purchid'] ) );
			}



			do_action( 'wpsc_alter_download_action', $file_id );


			$file_path = WPSC_FILE_DIR . basename( $file_data->post_title );
			$file_name = basename( $file_data->post_title );

			if ( is_file( $file_path ) ) {
				if( !ini_get('safe_mode') ) set_time_limit(0);
				header( 'Content-Type: ' . $file_data->post_mime_type );
				header( 'Content-Length: ' . filesize( $file_path ) );
				header( 'Content-Transfer-Encoding: binary' );
				header( 'Content-Disposition: attachment; filename="' . stripslashes( $file_name ) . '"' );
				if ( isset( $_SERVER["HTTPS"] ) && ($_SERVER["HTTPS"] != '') ) {
					/*
					  There is a bug in how IE handles downloads from servers using HTTPS, this is part of the fix, you may also need:
					  session_cache_limiter('public');
					  session_cache_expire(30);
					  At the start of your index.php file or before the session is started
					 */
					header( "Pragma: public" );
					header( "Expires: 0" );
					header( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
					header( "Cache-Control: public" );
				} else {
					header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
				}
				header( "Pragma: public" );
                                header( "Expires: 0" );

				// destroy the session to allow the file to be downloaded on some buggy browsers and webservers
				session_destroy();
				wpsc_readfile_chunked( $file_path );
				exit();
			}else{
				wp_die(__('Sorry something has gone wrong with your download!', 'wpsc'));
			}
		} else {
			exit( _e( 'This download is no longer valid, Please contact the site administrator for more information.', 'wpsc' ) );
		}
	}
}

add_action( 'init', 'wpsc_download_file' );

function wpsc_shipping_same_as_billing(){
	$_SESSION['shippingSameBilling'] = $_POST['wpsc_shipping_same_as_billing'];
}

add_action('wp_ajax_wpsc_shipping_same_as_billing', 'wpsc_shipping_same_as_billing');
?>
