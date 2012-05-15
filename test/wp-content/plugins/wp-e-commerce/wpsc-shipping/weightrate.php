<?php
/**
 * shipping/weightrate.php
 *
 * @package WP e-Commerce
 */


class weightrate {
	var $internal_name, $name;

	/**
	 *
	 *
	 * @return unknown
	 */
	function weightrate() {
		$this->internal_name = "weightrate";
		$this->name="Weight Rate";
		$this->is_external=false;
		return true;
	}

	/**
	 *
	 *
	 * @return unknown
	 */
	function getName() {
		return $this->name;
	}

	/**
	 *
	 *
	 * @return unknown
	 */
	function getInternalName() {
		return $this->internal_name;
	}

	private function output_row( $key = '', $shipping = '' ) {
		$currency = wpsc_get_currency_symbol();
		$class = ( $this->alt ) ? ' class="alternate"' : '';
		$this->alt = ! $this->alt;
		?>
			<tr>
				<td<?php echo $class; ?>>
					<div class="cell-wrapper">
						<input type="text" name="wpsc_shipping_weightrate_layer[]" value="<?php echo esc_attr( $key ); ?>" size="4" />
						<small><?php _e( ' lbs and above', 'wpsc' ); ?></small>
					</div>
				</td>
				<td<?php echo $class; ?>>
					<div class="cell-wrapper">
						<small><?php echo esc_html( $currency ); ?></small>
						<input type="text" name="wpsc_shipping_weightrate_shipping[]" value="<?php echo esc_attr( $shipping ); ?>" size="4" />
						<div class="actions">
							<a tabindex="-1" title="<?php _e( 'Add Layer', 'wpsc' ); ?>" class="action add" href="#">Add</a>
							<a tabindex="-1" title="<?php _e( 'Delete Layer', 'wpsc' ); ?>" class="action delete" href="#">Delete</a>
						</div>
					</div>
				</td>
			</tr>
		<?php
	}


	/**
	 *
	 *
	 * @return unknown
	 */
	function getForm() {
		$this->alt = false;
		$layers = get_option( 'weight_rate_layers', array() );
		ob_start();
		?>
			<thead>
				<tr>
					<th class="total-weight" title="<?php _e( 'You must enter the weight here in pounds, regardless of what you used on your products', 'wpsc' ); ?>">
						<?php _e( 'Total Weight', 'wpsc' ); ?><br /><small><?php _e( 'in pounds' ); ?></small>
					</th>
					<th class="shipping"><?php _e( 'Shipping Price', 'wpsc' ); ?></th>
				</tr>
			</thead>
			<tbody class="table-rate">
				<tr class="js-warning">
					<td colspan="2">
						<small><?php echo sprintf( __( 'To remove a rate layer, simply leave the values on that row blank. By the way, <a href="%s">enable JavaScript</a> for a better user experience.'), 'http://www.google.com/support/bin/answer.py?answer=23852' ); ?></small>
					</td>
				</tr>
				<?php if ( ! empty( $layers ) ): ?>
					<?php
						foreach( $layers as $key => $shipping ){
							$this->output_row( $key, $shipping );
						}
					?>
				<?php else: ?>
					<?php $this->output_row(); ?>
				<?php endif ?>
			</tbody>
		<?php
		return ob_get_clean();
	}

	/**
	 *
	 *
	 * @return unknown
	 */
	function submit_form() {
		if ( empty( $_POST['wpsc_shipping_weightrate_shipping'] ) || empty( $_POST['wpsc_shipping_weightrate_layer'] ) )
			return false;

		$new_layers = array();
		$layers = (array)$_POST['wpsc_shipping_weightrate_layer'];
		$shippings = (array)$_POST['wpsc_shipping_weightrate_shipping'];

		if ( !empty($shippings) ) {

			foreach ($shippings as $key => $price) {
				if ( empty( $price ) || empty( $layers[$key] ) )
					continue;

				$new_layers[$layers[$key]] = $price;

			}

		}

		krsort( $new_layers );
		update_option( 'weight_rate_layers', $new_layers );
		return true;
	}

	/**
	 *
	 *
	 * @return unknown
	 */
	function getQuote() {

		global $wpdb, $wpsc_cart;

		$weight = wpsc_cart_weight_total();
		if (is_object($wpsc_cart)) {
			$cart_total = $wpsc_cart->calculate_subtotal(true);
		}

		$layers = get_option('weight_rate_layers');

		if ($layers != '') {

			krsort($layers);

			foreach ($layers as $key => $shipping) {

				if ($weight >= (float)$key) {

					if (stristr($shipping, '%')) {

						// Shipping should be a % of the cart total
						$shipping = str_replace('%', '', $shipping);
						$shipping_amount = $cart_total * ( $shipping / 100 );
						return array("Weight Rate"=>(float)$shipping_amount);

					} else {

						return array("Weight Rate"=>$shipping);

					}

				}

			}

			$shipping = array_shift($layers);

			if (stristr($shipping, '%')) {
				$shipping = str_replace('%', '', $shipping);
				$shipping_amount = $price * ( $shipping / 100 );
			} else {
				$shipping_amount = $shipping;
			}

			return array("Weight Rate"=>(float)$shipping_amount);
		}

	}

	/**
	 *
	 *
	 * @param unknown $cart_item (reference)
	 * @return unknown
	 */
	function get_item_shipping(&$cart_item) {

		global $wpdb, $wpsc_cart;

		$unit_price = $cart_item->unit_price;
		$quantity = $cart_item->quantity;
		$weight = $cart_item->weight;
		$product_id = $cart_item->product_id;

		$uses_billing_address = false;
		foreach ($cart_item->category_id_list as $category_id) {
			$uses_billing_address = (bool)wpsc_get_categorymeta($category_id, 'uses_billing_address');
			if ($uses_billing_address === true) {
				break; /// just one true value is sufficient
			}
		}

		if (is_numeric($product_id) && (get_option('do_not_use_shipping') != 1)) {
			if ($uses_billing_address == true) {
				$country_code = $wpsc_cart->selected_country;
			} else {
				$country_code = $wpsc_cart->delivery_country;
			}

			if ($cart_item->uses_shipping == true) {
				//if the item has shipping
				$additional_shipping = '';
				if (isset($cart_item->meta[0]['shipping'])) {
					$shipping_values = $cart_item->meta[0]['shipping'];
				}
				if (isset($shipping_values['local']) && $country_code == get_option('base_country')) {
					$additional_shipping = $shipping_values['local'];
				} else {
					if (isset($shipping_values['international'])) {
						$additional_shipping = $shipping_values['international'];
					}
				}
				$shipping = $quantity * $additional_shipping;
			} else {
				//if the item does not have shipping
				$shipping = 0;
			}
		} else {
			//if the item is invalid or all items do not have shipping
			$shipping = 0;
		}
		return $shipping;
	}
}


$weightrate = new weightrate();
$wpsc_shipping_modules[$weightrate->getInternalName()] = $weightrate;
