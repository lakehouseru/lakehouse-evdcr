<?php

/**
 * WP eCommerce display functions
 *
 * These are functions for the wp-eCommerce themngine, template tags and shortcodes
 *
 * @package wp-e-commerce
 * @since 3.7
 */

/**
 * wpsc buy now button code products function
 * Sorry about the ugly code, this is just to get the functionality back, buy now will soon be overhauled, and this function will then be completely different
 * @return string - html displaying one or more products
 */
function wpsc_buy_now_button( $product_id, $replaced_shortcode = false ) {
	$product = get_post( $product_id );
	$supported_gateways = array('wpsc_merchant_paypal_standard','paypal_multiple');
	$selected_gateways = get_option( 'custom_gateway_options' );
	if ( in_array( 'wpsc_merchant_paypal_standard', (array)$selected_gateways ) ) {
		if ( $product_id > 0 ) {
			$post_meta = get_post_meta( $product_id, '_wpsc_product_metadata', true );
			$shipping = $post_meta['shipping']['local'];
			$price = get_post_meta( $product_id, '_wpsc_price', true );
			$special_price = get_post_meta( $product_id, '_wpsc_special_price', true );
			if ( $special_price )
				$price = $special_price;
			if ( wpsc_uses_shipping ( ) ) {
				$handling = get_option( 'base_local_shipping' );
			} else {
				$handling = $shipping;
			}
			$output .= "<form onsubmit='log_paypal_buynow(this)' target='paypal' action='" . get_option( 'paypal_multiple_url' ) . "' method='post' />
				<input type='hidden' name='business' value='" . get_option( 'paypal_multiple_business' ) . "' />
				<input type='hidden' name='cmd' value='_xclick' />
				<input type='hidden' name='item_name' value='" . $product->post_title . "' />
				<input type='hidden' id='item_number' name='item_number' value='" . $product_id . "' />
				<input type='hidden' id='amount' name='amount' value='" . ($price) . "' />
				<input type='hidden' id='unit' name='unit' value='" . $price . "' />
				<input type='hidden' id='shipping' name='ship11' value='" . $shipping . "' />
				<input type='hidden' name='handling' value='" . $handling . "' />
				<input type='hidden' name='currency_code' value='" . get_option( 'paypal_curcode' ) . "' />";
			if ( get_option( 'multi_add' ) == 1 ) {
				$output .="<label for='quantity'>" . __( 'Quantity', 'wpsc' ) . "</label>";
				$output .="<input type='text' size='4' id='quantity' name='quantity' value='' /><br />";
			} else {
				$output .="<input type='hidden' name='undefined_quantity' value='0' />";
			}
			$output .="<input type='image' name='submit' border='0' src='https://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif' alt='PayPal - The safer, easier way to pay online' />
				<img alt='' border='0' width='1' height='1' src='https://www.paypal.com/en_US/i/scr/pixel.gif' />
			</form>\n\r";
		}
	}
	if ( $replaced_shortcode == true ) {
		return $output;
	} else {
		echo $output;
	}
}

function wpsc_also_bought( $product_id ) {
	/*
	 * Displays products that were bought aling with the product defined by $product_id
	 * most of it scarcely needs describing
	 */
	global $wpdb;

	if ( get_option( 'wpsc_also_bought' ) == 0 ) {
		//returns nothing if this is off
		return '';
	}


	// to be made customiseable in a future release
	$also_bought_limit = 3;
	$element_widths = 96;
	$image_display_height = 96;
	$image_display_width = 96;

	$output = '';
	$also_bought = $wpdb->get_results( $wpdb->prepare( "SELECT `" . $wpdb->posts . "`.* FROM `" . WPSC_TABLE_ALSO_BOUGHT . "`, `" . $wpdb->posts . "` WHERE `selected_product`= %d AND `" . WPSC_TABLE_ALSO_BOUGHT . "`.`associated_product` = `" . $wpdb->posts . "`.`id` AND `" . $wpdb->posts . "`.`post_status` IN('publish','protected') ORDER BY `" . WPSC_TABLE_ALSO_BOUGHT . "`.`quantity` DESC LIMIT $also_bought_limit", $product_id ), ARRAY_A );
	if ( count( $also_bought ) > 0 ) {
		$output .= "<h2 class='prodtitles wpsc_also_bought' >" . __( 'People who bought this item also bought', 'wpsc' ) . "</h2>";
		$output .= "<div class='wpsc_also_bought'>";
		foreach ( (array)$also_bought as $also_bought_data ) {
			$output .= "<div class='wpsc_also_bought_item' style='width: " . $element_widths . "px;'>";
			if ( get_option( 'show_thumbnails' ) == 1 ) {
				if ( $also_bought_data['image'] != null ) {
					$output .= "<a href='" . get_permalink($also_bought_data['ID']) . "' class='preview_link'  rel='" . str_replace( " ", "_", get_the_title($also_bought_data['ID']) ) . "'>";
					$image_path = "index.php?productid=" . $also_bought_data['ID'] . "&amp;width=" . $image_display_width . "&amp;height=" . $image_display_height . "";

					$output .= "<img src='$image_path' id='product_image_" . $also_bought_data['ID'] . "' class='product_image' style='margin-top: " . $margin_top . "px'/>";
					$output .= "</a>";
				} else {
					if ( get_option( 'product_image_width' ) != '' ) {
						$output .= "<img src='" . WPSC_CORE_IMAGES_URL . "/no-image-uploaded.gif' title='" . get_the_title($also_bought_data['ID']) . "' alt='" . $also_bought_data['name'] . "' width='$image_display_height' height='$image_display_height' id='product_image_" . $also_bought_data['ID'] . "' class='product_image' />";
					} else {
						$output .= "<img src='" . WPSC_CORE_IMAGES_URL . "/no-image-uploaded.gif' title='" . get_the_title($also_bought_data['ID']) . "' alt='" . htmlentities( stripslashes( get_the_title($also_bought_data['ID']) ), ENT_QUOTES, 'UTF-8' ) . "' id='product_image_" . $also_bought_data['ID'] . "' class='product_image' />";
					}
				}
			}

			$output .= "<a class='wpsc_product_name' href='" . get_permalink($also_bought_data['ID']) . "'>" . get_the_title($also_bought_data['ID']) . "</a>";
			$price = get_product_meta($also_bought_data['ID'], 'price', true);
			$special_price = get_product_meta($also_bought_data['ID'], 'special_price', true);
			if(!empty($special_price)){
				$output .= '<span style="text-decoration: line-through;">' . wpsc_currency_display( $price ) . '</span>';
				$output .= wpsc_currency_display( $special_price );
			} else {
				$output .= wpsc_currency_display( $price );
			}
			$output .= "</div>";
		}
		$output .= "</div>";
		$output .= "<br clear='all' />";
	}
	return $output;
}

/**
 * Get the URL of the loading animation image.
 * Can be filtered using the wpsc_loading_animation_url filter.
 */
function wpsc_loading_animation_url() {
	return apply_filters( 'wpsc_loading_animation_url', WPSC_CORE_THEME_URL . 'wpsc-images/indicator.gif' );
}

function fancy_notifications() {	
	return wpsc_fancy_notifications( true );
}
function wpsc_fancy_notifications( $return = false ) {
	static $already_output = false;
	
	if ( $already_output )
		return '';
	
	$output = "";
	if ( get_option( 'fancy_notifications' ) == 1 ) {
		$output = "";
		$output .= "<div id='fancy_notification'>\n\r";
		$output .= "  <div id='loading_animation'>\n\r";
		$output .= '<img id="fancy_notificationimage" title="Loading" alt="Loading" src="' . wpsc_loading_animation_url() . '" />' . __( 'Updating', 'wpsc' ) . "...\n\r";
		$output .= "  </div>\n\r";
		$output .= "  <div id='fancy_notification_content'>\n\r";
		$output .= "  </div>\n\r";
		$output .= "</div>\n\r";
	}
	
	$already_output = true;
	
	if ( $return )
		return $output;
	else
		echo $output;
}
add_action( 'wpsc_theme_footer', 'wpsc_fancy_notifications' );

function fancy_notification_content( $cart_messages ) {
	$siteurl = get_option( 'siteurl' );
	$output = '';
	foreach ( (array)$cart_messages as $cart_message ) {
		$output .= "<span>" . $cart_message . "</span><br />";
	}
	$output .= "<a href='" . get_option( 'shopping_cart_url' ) . "' class='go_to_checkout'>" . __( 'Go to Checkout', 'wpsc' ) . "</a>";
	$output .= "<a href='#' onclick='jQuery(\"#fancy_notification\").css(\"display\", \"none\"); return false;' class='continue_shopping'>" . __( 'Continue Shopping', 'wpsc' ) . "</a>";
	return $output;
}

/*
 * wpsc product url function, gets the URL of a product,
 * Deprecated, all parameters past the first unused. use get_permalink
 */

function wpsc_product_url( $product_id, $category_id = null, $escape = true ) {
	$post = get_post($product_id);
	if ( isset($post->post_parent) && $post->post_parent > 0) {
		return get_permalink($post->post_parent);
	} else {
		return get_permalink($product_id);
	}
}

function external_link( $product_id ) {
	$link = get_product_meta( $product_id, 'external_link', true );
	if ( !stristr( $link, 'http://' ) ) {
		$link = 'http://' . $link;
	}
	$target = wpsc_product_external_link_target( $product_id );
	$output .= "<input class='wpsc_buy_button' type='button' value='" . wpsc_product_external_link_text( $product_id, __( 'Buy Now', 'wpsc' ) ) . "' onclick='return gotoexternallink(\"$link\", \"$target\")'>";
	return $output;
}

/* 19-02-09
 * add cart button function used for php template tags and shortcodes
 */

function wpsc_add_to_cart_button( $product_id, $return = false ) {
	global $wpdb,$wpsc_variations;
	$output = '';
	if ( $product_id > 0 ) {
		// grab the variation form fields here
		$wpsc_variations = new wpsc_variations( $product_id );
		if ( $return )
			ob_start();
		?>
			<div class='wpsc-add-to-cart-button'>
				<form class='wpsc-add-to-cart-button-form' id='product_<?php echo esc_attr( $product_id ) ?>' action='' method='post'>
					<?php do_action( 'wpsc_add_to_cart_button_form_begin' ); ?>
					<div class='wpsc_variation_forms'>
						<?php while ( wpsc_have_variation_groups() ) : wpsc_the_variation_group(); ?>
							<p>
								<label for='<?php echo wpsc_vargrp_form_id(); ?>'><?php echo esc_html( wpsc_the_vargrp_name() ) ?>:</label>
								<select class='wpsc_select_variation' name='variation[<?php echo wpsc_vargrp_id(); ?>]' id='<?php echo wpsc_vargrp_form_id(); ?>'>
									<?php while ( wpsc_have_variations() ): wpsc_the_variation(); ?>
										<option value='<?php echo wpsc_the_variation_id(); ?>' <?php echo wpsc_the_variation_out_of_stock(); ?>><?php echo esc_html( wpsc_the_variation_name() ); ?></option>
									<?php endwhile; ?>
								</select>
							</p>
						<?php endwhile; ?>
					</div>
					<input type='hidden' name='wpsc_ajax_action' value='add_to_cart' />
					<input type='hidden' name='product_id' value='<?php echo $product_id; ?>' />
					<input type='submit' id='product_<?php echo $product_id; ?>_submit_button' class='wpsc_buy_button' name='Buy' value='<?php echo __( 'Add To Cart', 'wpsc' ); ?>'  />
					<?php do_action( 'wpsc_add_to_cart_button_form_end' ); ?>
				</form>
			</div>
		<?php
		
		if ( $return )
			return ob_get_clean();
	}
}

/**
 * wpsc_refresh_page_urls( $content )
 *
 * Refresh page urls when permalinks are turned on or altered
 *
 * @global object $wpdb
 * @param string $content
 * @return string
 */
function wpsc_refresh_page_urls( $content ) {
	global $wpdb;

	$wpsc_pageurl_option['product_list_url'] = '[productspage]';
	$wpsc_pageurl_option['shopping_cart_url'] = '[shoppingcart]';
	$check_chekout = $wpdb->get_var( "SELECT `guid` FROM `{$wpdb->posts}` WHERE `post_content` LIKE '%[checkout]%' AND `post_type` NOT IN('revision') LIMIT 1" );

	if ( $check_chekout != null )
		$wpsc_pageurl_option['checkout_url'] = '[checkout]';
	else
		$wpsc_pageurl_option['checkout_url'] = '[checkout]';

	$wpsc_pageurl_option['transact_url'] = '[transactionresults]';
	$wpsc_pageurl_option['user_account_url'] = '[userlog]';
	$changes_made = false;
	foreach ( $wpsc_pageurl_option as $option_key => $page_string ) {
		$post_id = $wpdb->get_var( "SELECT `ID` FROM `{$wpdb->posts}` WHERE `post_type` IN('page','post') AND `post_content` LIKE '%$page_string%' AND `post_type` NOT IN('revision') LIMIT 1" );
		$the_new_link = _get_page_link( $post_id );

		if ( stristr( get_option( $option_key ), "https://" ) )
			$the_new_link = str_replace( 'http://', "https://", $the_new_link );

		update_option( $option_key, $the_new_link );
	}
	return $content;
}

add_filter( 'mod_rewrite_rules', 'wpsc_refresh_page_urls' );


/**
 * wpsc_obtain_the_title function, for replaacing the page title with the category or product
 * @return string - the new page title
 */
function wpsc_obtain_the_title() {
	global $wpdb, $wp_query, $wpsc_title_data;
	$output = null;
	$category_id = null;
	if( !isset( $wp_query->query_vars['wpsc_product_category']) &&  !isset( $wp_query->query_vars['wpsc-product']))
		return;

	if ( !isset( $wp_query->query_vars['wpsc_product_category'] ) && isset($wp_query->query_vars['wpsc-product']) )
		$wp_query->query_vars['wpsc_product_category'] = 0;


	if ( isset( $wp_query->query_vars['taxonomy'] ) && 'wpsc_product_category' ==  $wp_query->query_vars['taxonomy'] || isset($wp_query->query_vars['wpsc_product_category']))
		$category_id = wpsc_get_the_category_id($wp_query->query_vars['wpsc_product_category'],'slug');

	if ( $category_id > 0 ) {

		if ( isset( $wpsc_title_data['category'][$category_id] ) ) {
			$output = $wpsc_title_data['category'][$category_id];
		} else {
			$term = get_term($category_id, 'wpsc_product_category');
			$output = $term->name;
			$wpsc_title_data['category'][$category_id] = $output;
		}
	}

	if ( !isset( $_GET['wpsc-product'] ) )
		$_GET['wpsc-product'] = 0;

	if ( !isset( $wp_query->query_vars['wpsc-product'] ) )
		$wp_query->query_vars['wpsc-product'] = '';

	if ( isset( $wp_query->query_vars['wpsc-product'] ) || is_string( $_GET['wpsc-product'] ) ) {
		$product_name = $wp_query->query_vars['wpsc-product'];
		if ( isset( $wpsc_title_data['product'][$product_name] ) ) {
			$product_list = array( );
			$full_product_name = $wpsc_title_data['product'][$product_name];
		} else if ( $product_name != '' ) {
			$product_id = $wp_query->post->ID;
			$full_product_name = $wpdb->get_var( $wpdb->prepare( "SELECT `post_title` FROM `$wpdb->posts` WHERE `ID`= %d LIMIT 1", $product_id ) );
			$wpsc_title_data['product'][$product_name] = $full_product_name;
		} else {
			if(isset($_REQUEST['product_id'])){
				$product_id = absint( $_REQUEST['product_id'] );
				$product_name = $wpdb->get_var( $wpdb->prepare( "SELECT `post_title` FROM `$wpdb->posts` WHERE `ID`= %d LIMIT 1", $product_id ) );
				$full_product_name = $wpdb->get_var( $wpdb->prepare( "SELECT `post_title` FROM `$wpdb->posts` WHERE `ID`= %d LIMIT 1", $product_id ) );
				$wpsc_title_data['product'][$product_name] = $full_product_name;
			}else{
				//This has to exist, otherwise we would have bailed earlier.
				$category = $wp_query->query_vars['wpsc_product_category'];
				$cat_term = get_term_by('slug',$wp_query->query_vars['wpsc_product_category'], 'wpsc_product_category');
				$full_product_name = $cat_term->name;
			}
		}
		$output = $full_product_name;
	}

	if ( isset( $full_product_name ) && ($full_product_name != null) )
		$output = htmlentities( stripslashes( $full_product_name ), ENT_QUOTES, 'UTF-8' );
	$seperator = ' | ';
	$seperator = apply_filters('wpsc_the_wp_title_seperator' , $seperator);
	return $output.$seperator;
}

function wpsc_obtain_the_description() {
	global $wpdb, $wp_query, $wpsc_title_data;
	$output = null;

	if ( is_numeric( $wp_query->query_vars['category_id'] ) ) {
		$category_id = $wp_query->query_vars['category_id'];
	} else if ( $_GET['category'] ) {
		$category_id = absint( $_GET['category'] );
	}

	if ( is_numeric( $category_id ) ) {
		$output = wpsc_get_categorymeta( $category_id, 'description' );
	}


	if ( is_numeric( $_GET['product_id'] ) ) {
		$product_id = absint( $_GET['product_id'] );
		$output = $wpdb->get_var( $wpdb->prepare( "SELECT `post_content` FROM `" . $wpdb->posts . "` WHERE `id` = %d LIMIT 1", $product_id ) );
	}
	return $output;
}

function wpsc_replace_wp_title( $input ) {
	global $wpdb, $wp_query;
	$output = wpsc_obtain_the_title();
	if ( $output != null ) {
		return $output;
	}
	return $input;
}

function wpsc_replace_bloginfo_title( $input, $show ) {
	global $wpdb, $wp_query;
	if ( $show == 'description' ) {
		$output = wpsc_obtain_the_title();
		if ( $output != null ) {
			return $output;
		}
	}
	return $input;
}

if ( get_option( 'wpsc_replace_page_title' ) == 1 ) {
	add_filter( 'wp_title', 'wpsc_replace_wp_title', 10, 2 );
}
?>