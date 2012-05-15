<?php
function coupon_edit_form($coupon) {

$conditions = maybe_unserialize($coupon['condition']);

  $start_timestamp = strtotime($coupon['start']);
  $end_timestamp = strtotime($coupon['expiry']);
  $id = $coupon['id'];
  $output = '';
  $output .= "<form name='edit_coupon' id='".$coupon['coupon_code']."' method='post' action=''>\n\r";
  $output .= "<table class='add-coupon'>\n\r";
  $output .= " <tr>\n\r";
  $output .= "   <th>".__('Coupon Code', 'wpsc')."</th>\n\r";
  $output .= "   <th>".__('Discount', 'wpsc')."</th>\n\r";
  $output .= "   <th>".__('Start', 'wpsc')."</th>\n\r";
  $output .= "   <th>".__('Expiry', 'wpsc')."</th>\n\r";
  $output .= "   <th>".__('Use Once', 'wpsc')."</th>\n\r";
  $output .= "   <th>".__('Active', 'wpsc')."</th>\n\r";
	$output .= "   <th>".__('Apply On All Products', 'wpsc')."</th>\n\r";
  $output .= "   <th></th>\n\r";
  $output .= " </tr>\n\r";
  $output .= " <tr>\n\r";
  $output .= "  <td>\n\r";
  $output .= "   <input type='hidden' value='true' name='is_edit_coupon' />\n\r";
  $output .= "   <input type='text' size='11' value='".$coupon['coupon_code']."' name='edit_coupon[".$id."][coupon_code]' />\n\r";
  $output .= "  </td>\n\r";
  $output .= "  <td>\n\r";

  $output .= "   <input type='text' size='3' value='".$coupon['value']."'  name=edit_coupon[".$id."][value]' />";
  $output .= "   <select name='edit_coupon[".$id."][is-percentage]'>";
  $output .= "     <option value='0' ".(($coupon['is-percentage'] == 0) ? "selected='true'" : '')." >$</option>\n\r";//
  $output .= "     <option value='1' ".(($coupon['is-percentage'] == 1) ? "selected='true'" : '')." >%</option>\n\r";
  $output .= "     <option value='2' ".(($coupon['is-percentage'] == 2) ? "selected='true'" : '')." >" . __( 'Free shipping', 'wpsc' ) . "'</option>\n\r";
  $output .= "   </select>\n\r";
  $output .= "  </td>\n\r";
  $output .= "  <td>\n\r";
  $coupon_start = explode(" ",$coupon['start']);
  $output .= "<input type='text' class='pickdate' size='11' name='edit_coupon[".$id."][start]' value='{$coupon_start[0]}'>";
  $output .= "  </td>\n\r";
  $output .= "  <td>\n\r";
  $coupon_expiry = explode(" ",$coupon['expiry']);
  $output .= "<input type='text' class='pickdate' size='11' name='edit_coupon[".$id."][expiry]' value='{$coupon_expiry[0]}'>";
  $output .= "  </td>\n\r";
  $output .= "  <td>\n\r";
  $output .= "   <input type='hidden' value='0' name='edit_coupon[".$id."][use-once]' />\n\r";
  $output .= "   <input type='checkbox' value='1' ".(($coupon['use-once'] == 1) ? "checked='checked'" : '')." name='edit_coupon[".$id."][use-once]' />\n\r";
  $output .= "  </td>\n\r";
  $output .= "  <td>\n\r";
  $output .= "   <input type='hidden' value='0' name='edit_coupon[".$id."][active]' />\n\r";
  $output .= "   <input type='checkbox' value='1' ".(($coupon['active'] == 1) ? "checked='checked'" : '')." name='edit_coupon[".$id."][active]' />\n\r";
  $output .= "  </td>\n\r";
  $output .= "  <td>\n\r";
  $output .= "   <input type='hidden' value='0' name='edit_coupon[".$id."][add_every_product]' />\n\r";
  $output .= "   <input type='checkbox' value='1' ".(($coupon['every_product'] == 1) ? "checked='checked'" : '')." name='edit_coupon[".$id."][add_every_product]' />\n\r";
  $output .= "  </td>\n\r";
  $output .= "  <td>\n\r";
  $output .= "   <input type='hidden' value='".$id."' name='edit_coupon[".$id."][id]' />\n\r";
  $output .= "  </td>\n\r";
  $output .= " </tr>\n\r";

  if($conditions != null){
	  $output .= "<tr>";
	  $output .= "<th>";
	  $output .= __("Conditions", 'wpsc');
	  $output .= "</th>";
	  $output .= "</tr>";
	  $output .= "<th>";
	  $output .= __("Delete", 'wpsc');
	  $output .= "</th>";
	  $output .= "<th>";
	  $output .= __("Property", 'wpsc');
	  $output .= "</th>";
	  $output .= "<th>";
	  $output .= __("Logic", 'wpsc');
	  $output .= "</th>";
	  $output .= "<th>";
	  $output .= __("Value", 'wpsc');
	  $output .= "</th>";
	  $output .= " </tr>\n\r";
	  $i=0;
	  foreach ($conditions as $condition){
		  $output .= "<tr>";
		  $output .= "<td>";
		  $output .= "<input type='hidden' name='coupon_id' value='".$id."' />";
		  $output .= "<input type='submit' id='delete_condition".$i."' style='display:none;' value='".$i."' name='delete_condition' />";
		  $output .= "<span style='cursor:pointer;' class='delete_button' onclick='jQuery(\"#delete_condition".$i."\").click()'>" . __('Delete', 'wpsc' ) . "</span>";
		  $output .= "</td>";
		  $output .= "<td>";
		  $output .= $condition['property'];
		  $output .= "</td>";
		  $output .= "<td>";
		  $output .= $condition['logic'];
		  $output .= "</td>";
		  $output .= "<td>";
		  $output .= $condition['value'];
		  $output .= "</td>";
		  $output .= "</tr>";
		  $i++;
	  }
	  $output .=	wpsc_coupons_conditions( $id);
  }elseif($conditions == null){
  	$output .=	wpsc_coupons_conditions( $id);

  }
  $output .= "</table>\n\r";
  $output .= "</form>\n\r";
  echo $output;
  return $output;
  }
function wpsc_coupons_conditions($id){
?>

<?php

$output ='
<input type="hidden" name="coupon_id" value="'.$id.'" />
<tr><td colspan="3"><b>' . __( 'Add Conditions', 'wpsc') . '</b></td></tr>
<tr><td colspan="6">
	<div class="coupon_condition">
		<div>
			<select class="ruleprops" name="rules[property][]">
				<option value="item_name" rel="order">' . __( 'Item name', 'wpsc') . '</option>
				<option value="item_quantity" rel="order">' . __( 'Item quantity', 'wpsc') . '</option>
				<option value="total_quantity" rel="order">' . __( 'Total quantity', 'wpsc') . '</option>
				<option value="subtotal_amount" rel="order">' . __( 'Subtotal amount', 'wpsc') . '</option>
				' . apply_filters( 'wpsc_coupon_rule_property_options', '' ) . '
			</select>
			<select name="rules[logic][]">
				<option value="equal">' . __( 'Is equal to', 'wpsc') . '</option>
				<option value="greater">' . __( 'Is greater than', 'wpsc') . '</option>
				<option value="less">' . __( 'Is less than', 'wpsc') . '</option>
				<option value="contains">' . __( 'Contains', 'wpsc') . '</option>
				<option value="not_contain">' . __( 'Does not contain', 'wpsc') . '</option>
				<option value="begins">' . __( 'Begins with', 'wpsc') . '</option>
				<option value="ends">' . __( 'Ends with', 'wpsc') . '</option>
			</select>
			<span>
				<input type="text" name="rules[value][]"/>
			</span>
			

		</div>
	</div>
	</td>
	<td>
	</td>
	<td colspan="3">
	
		<input type="submit" value="'.__("Update Coupon", "wpsc").'" class="button-primary" name="edit_coupon['.$id.'][submit_coupon]" />';
 		
 		$nonced_url = wp_nonce_url("admin.php?wpsc_admin_action=wpsc-delete-coupon&amp;delete_id=$id", 'delete-coupon');
		
		$output.= "<a class='delete_button' style='text-decoration:none;' href=" .$nonced_url."> Delete</a>";	

 	$output.='	
 	</td>
</tr>
';
return $output;

}  
function setting_button(){
	$next_url	= 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']."?page=wpsc-edit-products";
	
// 	$output.="<div><img src='".get_option('siteurl')."/wp-content/plugins/".WPSC_DIR_NAME."/images/settings_button.jpg' onclick='display_settings_button()'>";
	$output.="<div style='float: right; margin-top: 0px; position: relative;'> | <a href='#' onclick='display_settings_button(); return false;' style='text-decoration: underline;'>".__('Settings', 'wpsc')." &raquo;</a>";
	$output.="<span id='settings_button' style='width:180px;background-color:#f1f1f1;position:absolute; right: 10px; border:1px solid black; display:none;'>";
	$output.="<ul class='settings_button'>";
	
	$output.="<li><a href='admin.php?page=wpsc-settings'>".__('Shop Settings', 'wpsc')."</a></li>";
	$output.="<li><a href='admin.php?page=wpsc-settings&amp;tab=gateway'>".__('Money and Payment', 'wpsc')."</a></li>";
	$output.="<li><a href='admin.php?page=wpsc-settings&amp;tab=checkout'>".__('Checkout Page Settings', 'wpsc')."</a></li>";
	$output.="</ul>";
	$output.="</span>&emsp;&emsp;</div>";
	
	return $output;
}

function wpsc_right_now() {
	global $wpdb;
	$year = date("Y");
	$month = date("m");
	$start_timestamp = mktime(0, 0, 0, $month, 1, $year);
	$end_timestamp = mktime(0, 0, 0, ($month+1), 0, $year);
	$product_count = $wpdb->get_var("SELECT COUNT(*)
		FROM `".$wpdb->posts."` 
		WHERE `post_status` = 'publish'
		AND `post_type` = 'wpsc-product'"
	);
	$group_count = count(get_terms("wpsc_product_category"));
	$sales_count = $wpdb->get_var("SELECT COUNT(*) FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `date` BETWEEN '".$start_timestamp."' AND '".$end_timestamp."'");
	$monthtotal = wpsc_currency_display( admin_display_total_price( $start_timestamp,$end_timestamp ) );
	$overaltotal = wpsc_currency_display( admin_display_total_price() );
	$variation_count = count(get_terms("wpsc-variation", array('parent' => 0)));
	$pending_sales = $wpdb->get_var("SELECT COUNT(*) FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `processed` IN ('1','2')");
	$accept_sales = $wpdb->get_var("SELECT COUNT(*) FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `processed` IN ('3' ,'4', '5')");
	$theme = get_option('wpsc_selected_theme');
	?>
	<div class='table'>
		<p class='sub'><?php _e('At a Glance', 'wpsc'); ?></p>
		<table style='border-top:1px solid #ececec;'>
			<tr class='first'>
				<td class='first b'>
					<?php echo $product_count; ?>
				</td>
				<td class='t'>
					<?php echo _nx( 'Product', 'Products', $product_count, 'dashboard widget', 'wpsc' ); ?>
				</td>
				<td class='b'>
					<?php echo $sales_count; ?>
				</td>
				<td class='last'>
					<?php echo _nx('Sale', 'Sales', $sales_count, 'dashboard widget', 'wpsc'); ?>
				</td>
			</tr>
			<tr>
				<td class='first b'>
					<?php echo $group_count; ?>
				</td>
				<td class='t'>
					<?php echo _nx('Category', 'Categories', $group_count, 'dashboard widget', 'wpsc'); ?>
				</td>
				<td class='b'>
					<?php echo $pending_sales; ?>
				</td>
				<td class='last t waiting'>
					<?php echo _n('Pending sale', 'Pending sales', $pending_sales, 'wpsc'); ?>
				</td>
			</tr>
			<tr>
				<td class='first b'>
					<?php echo $variation_count; ?>
				</td>
				<td class='t'>
					<?php echo _nx('Variation', 'Variations', $variation_count, 'dashboard widget', 'wpsc'); ?>
				</td>
				<td class='b'>
					<?php echo $accept_sales; ?>
				</td>
				<td class='last t approved'>
					<?php echo _n('Closed sale', 'Closed sales', $accept_sales, 'wpsc'); ?>
				</td>
			</tr>
		</table>
	</div>
	<?php
}


function wpsc_packing_slip( $purchase_id ) {
	echo "<!DOCTYPE html><html><head><title>" . __( 'Packing Slip', 'wpsc' ) . "</title></head><body id='wpsc-packing-slip'>";
	global $wpdb;
	$purch_sql = $wpdb->prepare( "SELECT * FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `id`=%d", $purchase_id );
	$purch_data = $wpdb->get_row( $purch_sql, ARRAY_A ) ;

	$cartsql = $wpdb->prepare( "SELECT * FROM `".WPSC_TABLE_CART_CONTENTS."` WHERE `purchaseid`=%d", $purchase_id );
	$cart_log = $wpdb->get_results($cartsql,ARRAY_A) ; 
	$j = 0;

	if($cart_log != null) {
		echo "<div class='packing_slip'>\n\r";
		echo apply_filters( 'wpsc_packing_slip_header', '<h2>' . __( 'Packing Slip', 'wpsc' ) . "</h2>\n\r" );
		echo "<strong>".__('Order', 'wpsc')." #</strong> ".$purchase_id."<br /><br />\n\r";
		
		echo "<table>\n\r";
		
		$form_sql = $wpdb->prepare( "SELECT * FROM `".WPSC_TABLE_SUBMITED_FORM_DATA."` WHERE `log_id` = %d", $purchase_id );
		$input_data = $wpdb->get_results($form_sql,ARRAY_A);
		
		foreach($input_data as $input_row) {
			$rekeyed_input[$input_row['form_id']] = $input_row;
		}
		
		
		if($input_data != null) {
			$form_data = $wpdb->get_results("SELECT * FROM `".WPSC_TABLE_CHECKOUT_FORMS."` WHERE `active` = '1'",ARRAY_A);
			
			foreach($form_data as $form_field) {

				switch($form_field['type']) {
					case 'country':
						$region_count_sql = $wpdb->prepare( "SELECT COUNT(`regions`.`id`) FROM `".WPSC_TABLE_REGION_TAX."` AS `regions` INNER JOIN `".WPSC_TABLE_CURRENCY_LIST."` AS `country` ON `country`.`id` = `regions`.`country_id` WHERE `country`.`isocode` IN('%s')", $purch_data['billing_country'] );
						$delivery_region_count = $wpdb->get_var( $region_count_sql );
			
						if(is_numeric($purch_data['billing_region']) && ($delivery_region_count > 0)) 
							echo "	<tr><td>".__('State', 'wpsc').":</td><td>".wpsc_get_region($purch_data['billing_region'])."</td></tr>\n\r";
						
						 echo "	<tr><td>".wp_kses($form_field['name'], array() ).":</td><td>".htmlentities(stripslashes($rekeyed_input[$form_field['id']]['value']), ENT_QUOTES, 'UTF-8')."</td></tr>\n\r";
					break;
							
					case 'delivery_country':
					
						if(is_numeric($purch_data['shipping_region']) && ($delivery_region_count > 0)) 
							echo "	<tr><td>".__('State', 'wpsc').":</td><td>".wpsc_get_region($purch_data['shipping_region'])."</td></tr>\n\r";
						
						 echo "	<tr><td>".wp_kses($form_field['name'], array() ).":</td><td>".htmlentities(stripslashes($rekeyed_input[$form_field['id']]['value']), ENT_QUOTES, 'UTF-8')."</td></tr>\n\r";
					break;

					case 'heading':
                        
                        if($form_field['name'] == "Hidden Fields")
                          continue;
                        else
                          echo "	<tr class='heading'><td colspan='2'><strong>".wp_kses($form_field['name'], array()).":</strong></td></tr>\n\r";
					break;

					default:				
						if( $form_field['name'] == "Cupcakes") {
							parse_str($rekeyed_input[$form_field['id']]['value'], $cupcakes );
							
							foreach( $cupcakes as $product_id => $quantity ) {
								$product = get_post($product_id);
								$string .= "(".$quantity.") ".$product->post_title.", ";
							}
							
							$string = rtrim($string, ", ");
							echo "	<tr><td>".wp_kses($form_field['name'], array() ).":</td><td>".htmlentities(stripslashes($string), ENT_QUOTES, 'UTF-8')."</td></tr>\n\r";
						} else {
							if ($form_field['name']=="State" && !empty($purch_data['billing_region']) || $form_field['name']=="State" && !empty($purch_data['billing_region']))
								echo "";
							else
								echo "	<tr><td>".wp_kses($form_field['name'], array() ).":</td><td>".
									( isset( $rekeyed_input[$form_field['id']] ) ? htmlentities(stripslashes($rekeyed_input[$form_field['id']]['value']), ENT_QUOTES, 'UTF-8') : '' ).
									"</td></tr>\n\r";
						}
					break;
				}

			}
		} else {
			echo "	<tr><td>".__('Name', 'wpsc').":</td><td>".$purch_data['firstname']." ".$purch_data['lastname']."</td></tr>\n\r";
			echo "	<tr><td>".__('Address', 'wpsc').":</td><td>".$purch_data['address']."</td></tr>\n\r";
			echo "	<tr><td>".__('Phone', 'wpsc').":</td><td>".$purch_data['phone']."</td></tr>\n\r";
			echo "	<tr><td>".__('Email', 'wpsc').":</td><td>".$purch_data['email']."</td></tr>\n\r";
		}
		
		if ( 2 == get_option( 'payment_method' ) ) {
			$gateway_name = '';
			$nzshpcrt_gateways = nzshpcrt_get_gateways();

			foreach( $nzshpcrt_gateways as $gateway ) {
				if ( $purch_data['gateway'] != 'testmode' ) {
					if ( $gateway['internalname'] == $purch_data['gateway'] ) {
						$gateway_name = $gateway['name'];
					}
				} else {
					$gateway_name = __('Manual Payment', 'wpsc');
				}
			}
		}

		echo "</table>\n\r";
		
		
		do_action ('wpsc_packing_slip_extra_info',$purchase_id);

		
		echo "<table class='packing_slip'>";
			
		echo "<tr>";
		echo " <th>".__('Quantity', 'wpsc')." </th>";
		
		echo " <th>".__('Name', 'wpsc')."</th>";
		
		
		echo " <th>".__('Price', 'wpsc')." </th>";
		
		echo " <th>".__('Shipping', 'wpsc')." </th>";
		echo '<th>' . __('Tax', 'wpsc') . '</th>';
		echo '</tr>';
		$endtotal = 0;
		$all_donations = true;
		$all_no_shipping = true;
		$file_link_list = array();
		$total_shipping = 0;
		foreach($cart_log as $cart_row) {
			$alternate = "";
			$j++;
			if(($j % 2) != 0) {
				$alternate = "class='alt'";
			}
			// product ID will be $cart_row['prodid']. need to fetch name and stuff 
			
			$variation_list = '';
			
			if($cart_row['donation'] != 1) {
				$all_donations = false;
			}
			
			if($cart_row['no_shipping'] != 1) {
				$shipping = $cart_row['pnp'];
				$total_shipping += $shipping;						
				$all_no_shipping = false;
			} else {
				$shipping = 0;
			}
		
			$price = $cart_row['price'] * $cart_row['quantity'];
			$gst = $price - ($price	/ (1+($cart_row['gst'] / 100)));
			
			if($gst > 0) {
				$tax_per_item = $gst / $cart_row['quantity'];
			}


			echo "<tr $alternate>";
	
	
			echo " <td>";
			echo $cart_row['quantity'];
			echo " </td>";
			
			echo " <td>";
			echo $cart_row['name'];
			echo stripslashes($variation_list);
			echo " </td>";
			
			
			echo " <td>";
			echo wpsc_currency_display( $price );
			echo " </td>";
			
			echo " <td>";
			echo wpsc_currency_display($shipping );
			echo " </td>";
						


			echo '<td>';
			echo wpsc_currency_display( $cart_row['tax_charged'] );
			echo '</td>';
			echo '</tr>';
		}
		
		echo "</table>";
		echo '<table class="packing-slip-totals">';
		if ( floatval( $purch_data['discount_value'] ) )
			echo '<tr><th>'.__('Discount', 'wpsc').'</th><td>(' . wpsc_currency_display( $purch_data['discount_value'] ) . ')</td></tr>';
		
		echo '<tr><th>'.__('Base Shipping','wpsc').'</th><td>' . wpsc_currency_display( $purch_data['base_shipping'] ) . '</td></tr>';
		echo '<tr><th>'.__('Total Shipping','wpsc').'</th><td>' . wpsc_currency_display( $purch_data['base_shipping'] + $total_shipping ) . '</td></tr>';
        //wpec_taxes
        if($purch_data['wpec_taxes_total'] != 0.00)
        {
           echo '<tr><th>'.__('Taxes','wpsc').'</th><td>' . wpsc_currency_display( $purch_data['wpec_taxes_total'] ) . '</td></tr>';
        }
		echo '<tr><th>'.__('Total Price','wpsc').'</th><td>' . wpsc_currency_display( $purch_data['totalprice'] ) . '</td></tr>';
		echo '</table>';
		
		echo "</div>\n\r";
	} else {
		echo "<br />".__('This users cart was empty', 'wpsc');
	}

}


		


function wpsc_product_item_row() {
}

?>
