<?php
/**
 * shipping/tablerate.php
 *
 * @package WP e-Commerce
 */


class tablerate {

	var $internal_name, $name;

	/**
	 *
	 *
	 * @return unknown
	 */
	function tablerate() {
		$this->internal_name = "tablerate";
		$this->name="Table Rate";
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
						<small><?php echo esc_html( $currency ); ?></small>
						<input type="text" name="wpsc_shipping_tablerate_layer[]" value="<?php echo esc_attr( $key ); ?>" size="4" />
						<small><?php _e( ' and above', 'wpsc' ); ?></small>
					</div>
				</td>
				<td<?php echo $class; ?>>
					<div class="cell-wrapper">
						<small><?php echo esc_html( $currency ); ?></small>
						<input type="text" name="wpsc_shipping_tablerate_shipping[]" value="<?php echo esc_attr( $shipping ); ?>" size="4" />
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
		$layers = get_option( 'table_rate_layers', array() );
		$this->alt = false;
		ob_start();
		?>
			<thead>
				<tr>
					<th class="total"><?php _e('Total Price', 'wpsc' ); ?></th>
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
		if ( ! isset( $_POST['wpsc_shipping_tablerate_layer'] ) || ! isset( $_POST['wpsc_shipping_tablerate_shipping'] ) )
			return false;

		$layers = (array) $_POST['wpsc_shipping_tablerate_layer'];
		$shippings = (array) $_POST['wpsc_shipping_tablerate_shipping'];
		$new_layer = array();
		if ($shippings != '') {
			foreach ($shippings as $key => $price) {
				if ( empty( $price ) || empty( $layers[$key] ) )
					continue;

				$new_layer[$layers[$key]] = $price;
			}
		}
		// Sort the data before it goes into the database. Makes the UI make more sense
		krsort( $new_layer );
		update_option('table_rate_layers', $new_layer);
		return true;
	}

	/**
	 *
	 *
	 * @return unknown
	 */
	function getQuote() {

		global $wpdb, $wpsc_cart;
		if (isset($_SESSION['nzshpcrt_cart'])) {
			$shopping_cart = $_SESSION['nzshpcrt_cart'];
		}
		if (is_object($wpsc_cart)) {
			$price = $wpsc_cart->calculate_subtotal(true);
		}

		$layers = get_option('table_rate_layers');

		if ($layers != '') {

			// At some point we should probably remove this as the sorting should be
			// done when we save the data to the database. But need to leave it here
			// for people who have non-sorted settings in their database
			krsort($layers);

			foreach ($layers as $key => $shipping) {

				if ($price >= (float)$key) {

					if (stristr($shipping, '%')) {

						// Shipping should be a % of the cart total
						$shipping = str_replace('%', '', $shipping);
						$shipping_amount = $price * ( $shipping / 100 );

					} else {

						// Shipping is an absolute value
						$shipping_amount = $shipping;

					}

					return array("Table Rate"=>$shipping_amount);

				}

			}

			$shipping = array_shift($layers);

			if (stristr($shipping, '%')) {
				$shipping = str_replace('%', '', $shipping);
				$shipping_amount = $price * ( $shipping / 100 );
			} else {
				$shipping_amount = $shipping;
			}

			return array("Table Rate"=>$shipping_amount);

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


$tablerate = new tablerate();
$wpsc_shipping_modules[$tablerate->getInternalName()] = $tablerate;
?>
