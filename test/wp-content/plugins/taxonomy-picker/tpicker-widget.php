<?php

// Version: 1.13.4
// Builds the Premium TPicker widget

add_action('widgets_init','register_phiz_find_helper');

function register_phiz_find_helper() {
	register_widget('FindHelperWidget');
}

class FindHelperWidget extends WP_Widget {

	function FindHelperWidget(){
		
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'taxonomy-picker', 'description' => __('Presents taxonomies as drop-downs so reader can pick query', 'taxonomy_picker') );

		/* Widget control settings. */
		$control_ops = array( 'width' => 525,  'id_base' => 'taxonomy_picker' );

		/* Create the widget. */
		$this->WP_Widget( 'taxonomy_picker', __('Taxonomy Picker', 'taxonomy_picker'), $widget_ops, $control_ops );
	}

// Display the widget on the front of the site
	function widget($args, $instance ){ 

		// Check whether to disaply on this page or not
		global $post;
		
		$pgs = explode(',',$instance['set_pages']);	
		if($instance['choose_pages']=='I'):  // Only allow specified pages
			$allowed = false;
			foreach($pgs as $pg):
				if($pg == $post->ID): // Page is allowed 
					$allowed = true;
					break;
				endif;
			endforeach;
			if(!$allowed):  // Not matched to exit function without displaying page
				return;
			endif;
		elseif($instance['choose_pages']=='E'): // Reject specified pages
			foreach($pgs as $pg):
				if($pg == $post->ID): // page matches so disallowed - break out of function
					return; 
				endif;
			endforeach;							
			// No category match so allow to proceed
		endif;

		echo taxonomy_picker_display_widget( $instance, $args ); // Display the widget
	}

	/**
	 * Updates the $instance of the widget in the database on Save
	 *
	 * @param $new_instance		array	New instance proposed for save
	 * @param $old_instance		array	Old version of the instance
	 *
	 * return array 	Cleansed $instance with pre-processed taxonomies field added to save processing when displayed
	 */
	function update($new_instance, $old_instance) {
	
		// Tidy up inputs
		$instance = $new_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['category_title'] = strip_tags( $new_instance['category_title'] );
		$instance['set_categories'] = str_replace(' ','', strip_tags( $new_instance['set_categories'] ) );
		$instance['set_pages'] = strip_tags( $new_instance['set_pages'] );
		$instance['id'] = preg_replace( '~\W+~', '', $instance['id'] );

		return taxonomy_picker_taxonomies_array( $instance ); // Pre-process the instance for efficiency
		
	}
	
	function form ($instance) { // Form for configuring the widget instance
	
		// Set up some defaults
		$defaults = array( 'title' => __('Example', 'example'), 'choose_categories' => 'A', 'choose_pages' => 'A');
		$instance = wp_parse_args( (array) $instance, $defaults ); 
		$options = get_option('taxonomy-picker-options');

		// ID
		$instanceid = $this->get_field_id( 'id' );
		$instance_name = $this->get_field_name( 'id' );
		if( empty( $instance['id'] ) ) $instance['id'] = substr(md5(uniqid(rand(), true)),0,6);

		// Widget title
	   $titleid = $this->get_field_id( 'title' );
	   $title_name = $this->get_field_name( 'title' );
		$title_value = $instance['title'] . '&ensp;<span class="instance-ID">(ID = ' .  $instance['id'] . ')</span>';

		//Show search?
	 	$searchid = $this->get_field_id( 'hidesearch' );
	   $search_name = $this->get_field_name( 'hidesearch' );
		$radio_checked = ( array_key_exists( 'hidesearch', $instance ) and $instance['hidesearch'] ) ? 'checked ' : '';
	    
	   echo '<p class="tpicker-title"><fieldset id="taxonomy-picker-title">';
			echo "<td><label for='$titleid' title='title'>Title:</label>
							<input id='$titleid' class='textbox' type='textbox' name='$title_name' value='" . $instance['title'] . "' /></td>";
			echo "<td><label for='$instanceid' title='id'>Unique ID:</label>
							<input id='$instanceid' class='textbox' type='textbox' name='$instance_name' value = '" . $instance['id'] . "' /></td>";
			echo "<td><label for='$searchid' title='showsearch'>Hide text search?</label>
							<input id='$searchid' class='checkbox' type='checkbox' name='$search_name' $radio_checked /></td>";
		echo "</tr></tbody></table></fieldset></p><hr>";


		/*********** Premium options *********/
		
		// Radio buttons alternative
		?><p><fieldset id="tpicker_premium"><h3>Premium Options</h3><table><tbody><tr><td><?php

		// Date Match option
		echo "Post date:</td><td><select name='" . $this->get_field_name('date_match') . "'>";
		foreach( array('N/A','Y','YM','YMD','M','D') as $item ):
			$selected = ( $instance['date_match'] == $item ) ? 'selected=selected' : '' ;
			echo "<option value='$item' $selected>{$item}</option>";
		endforeach;
		?></select></td><?php

		
		// Code for sort results options
		$selected = ( array_key_exists( 'results_orderby', $instance ) and ($instance['results_orderby'] == '_default' ) ) ? 'selected=selected' : '' ;
		echo "<td>Sort results by:</td><td><select name='" . $this->get_field_name('results_orderby') . "'>
					<option value='_default' $selected>** Default **</option>";
		$user_choice = false;
		foreach( array('author', 'comment_count', 'date','ID', 'modified', 'title') as $item):
			if( $options["results_sort_$item"] ):
				$item_text = str_replace( '_', ' ', ucfirst( $item) );
				$selected = ( array_key_exists( 'results_orderby', $instance ) and ( $instance['results_orderby'] == $item ) ) ? 'selected=selected' : '' ;
				echo "<option value='$item' $selected>$item_text</option>";
				$user_choice = true; // We have at least one option to allow a user choice
			endif;
		endforeach;
	
		if( $user_choice ): // There are enough options for user_choice to be sensible
			$selected = ( array_key_exists( 'results_orderby', $instance ) and ( $instance['results_orderby'] == '_choice' ) ) ? 'selected=selected' : '' ;
			echo "<option value='_choice' $selected>** Visitors' Choice **</option>";
		endif;
			 
		echo "</select></td><td><select name='" . $this->get_field_name('results_order') . "'>
						<option value='ASC'>Ascending</option><option value='DSC'>Descending</option></select></td></tr>";

		echo "<tr><td>Combobox type:</td><td><select name='" . $this->get_field_name('combo') . "'>";
		foreach( array('flat','multi', 'radio') as $combo ):
			$selected = ( array_key_exists( 'combo', $instance ) and ( $instance['combo'] == $combo ) ) ? 'selected=selected' : '' ;
			echo "<option value='$combo' $selected>" .  ucwords($combo) . "</option>";
		endforeach;
				
		?></select></td><?php

		echo "<p>(<b>NB - Release Notes</b> - Post Date is alpha trial functionality in this release)<p>";
		
		?></tr></tbody></table></fieldset></p><hr><?php

	 	unset($titleid, $title_name, $title_value,$searchid,$search_value,$combo, $selected,$item,$item_text,$combo,$user_choice);
	 	
	 	/************ Taxonomies Section ************/

		// Build taxonomy selection boxes	 	
		$taxes = get_taxonomies('','names');

		if( isset($options['post_type'] ) ) $taxes['post_type'] = "post_type"; // Option to add post_type

		if(count($taxes)>0): 
			
			$priority_name = ( isset($options['sort-priority'] ) ) ? '<td><strong>Priority</strong></td>' : '';

			?>
			<fieldset id="taxonomy-picker-taxonomoies"><h3>Taxonomies</h3><div>
			<table  style="width:400px;"><thead><tr>
				<td><strong>Taxonomy</strong></td>
				<td><strong>Fix/Initial</strong></td>
				<td><strong>Order By</strong></td>
				<td><strong>Sort</strong>
				</td><?php echo $priority_name; ?>
			</tr></thead><tbody><?php
			
			foreach($taxes as $tax):
			
				if(	($tax=='link_category') or 
						($tax=='nav_menu') or 
						( ($tax=='post_format') and !isset($options['post_format']) ) or
						( ($tax=='post_type') and !isset($options['post_type']) ) ) 
						continue;  // Skip any un-supported or de-selected taxonomy
						
				$tax_stem = 'taxonomy_'.$tax;		
				if( $tax == 'post_type'):
					$tax_label = 'Post Type';				
				else:
					$taxonomy = get_taxonomy($tax);
					$tax_label = $taxonomy->label;
				endif;
					
				$taxid = $this->get_field_id($tax_stem);
				$tax_name = $this->get_field_name($tax_stem);
				$radio_checked = ( array_key_exists( $tax_stem, $instance ) and ( $instance[$tax_stem]=='on') ) ? 'checked ' : '';
				
				if($tax <> 'category'): // Custom taxonomy or post_type or post_format - build fix/initial value combobox
				
					if( $tax == 'post_type' ):
						$taxonomy_name = 'post_type';
						$args = array( '_builtin' => true );
						$terms = get_post_types( $args, 'names' );
					else:
						$taxonomy_name = $taxonomy->name;
						$terms = get_terms($taxonomy->name, array('orderby'=>'name'));
					endif;

					if( !empty( $terms ) ):  // Check we have some terms to process ...
					
						$select_name = $this->get_field_name( "fix_{$tax}" ); // The name of the Fix field

						
						$tax_select  = "<select name='$select_name' style='width:90%;font-size:85%;'>";
						$tax_select .= "<option value='{$taxonomy_name}=tp-all'>".taxonomy_picker_all_text($tax_label)."</option>";
						foreach( (array) $terms as $term ): // Loop through the terms to build the options
							$option_name = $taxonomy_name . '=' . ( (is_string( $term ) ? $term : $term->slug ) );
							$selected = (array_key_exists( 'fix_'.$tax, $instance ) and ($instance['fix_'.$tax] == $option_name) ) ? 'selected="selected"' : '';
							$tax_select .= "<option value='$option_name' $selected>" . ( (is_string( $term ) ? $term : $term->name ) ) . '</option>';
						endforeach;
						$tax_select .= "</select>";
					endif; // !empty()
					
					// Orderby comboboxes
					$select_name = $this->get_field_name("orderby_".$tax);
					$order_select  = "<select name='$select_name' style='width:90%;font-size:90%;'>";
										
					if( $tax=='post_type' or $tax=='post_format' ) $orders = 'name'; else $orders =array('name','slug','id','count','tree');  // Add 'pruned-tree' when ready
					
					foreach( (array) $orders as $order):
						$selected = ( array_key_exists( "orderby_{$tax}", $instance ) and ( $instance["orderby_{$tax}"] == $order ) ) ? 'selected="selected"' : '';
						$select_label = ($order=='name') ? 'Label' : ucwords( str_replace('_',' ',$order) );
						$order_select .= "<option value='$order' $selected>$select_label</option>";
					endforeach;
					unset($orders, $order);

					// Sort order comboboxes
					$select_name = $this->get_field_name("sort_".$tax);
					$sort_select  = "<select name='$select_name' style='width:90%;font-size:90%;'>";
					foreach( array('Asc','Desc') as $term):
						$selected = ( array_key_exists( 'sort_'.$tax, $instance ) and ($instance['sort_'.$tax] == $term ) )? 'selected="selected"' : '';
						$sort_select .= "<option value='$term' $selected>$term</option>";
					endforeach;

					// Priority inputs
					if( @isset($options['sort-priority'] ) ): // Only show priority field if required
						$select_name = $this->get_field_name("priority_".$tax);
						$priority_input  = "<input name='$select_name' style='width:60px;font-size:90%;'>";
					else:
						$priority_input = '';
					endif;						
				endif;
				
				echo "<tr><td><input id='$taxid' class='checkbox' type='checkbox' name='$tax_name' $radio_checked />";
				echo "&nbsp;<label for='$taxid' title='$tax_stem'>$tax_label</span></label></td>";
				
				// Output any cells we have build
				foreach( array( 'tax_select', 'order_select', 'sort_select', 'priority_input' ) as $i ):
					 if( isset( $$i ) ) echo '<td>' . $$i . '</td>'; else echo '<td>&nbsp;</td>';
				endforeach;
				
				echo "</tr>";
			endforeach;
			echo '</tbody></table><i style="font-size:75%">If on, the value is the initial one; if off, value is fixed to restrict search</i></div></fieldset><hr>';
		endif;
		
		// Select Categories		
		$titleid = $this->get_field_id( 'category_title' );
	   $title_name = $this->get_field_name('category_title');
	   $title_value = ( array_key_exists( 'category_title', $instance ) ) ? $instance['category_title'] : '';

		echo '<fieldset id="taxonomy-picker-categories"<p><h3>Categories</h3></p>';
		echo '<p style="float:left;"><label for="$cat_titleid"><b>Title:</b></label></p>';
		echo '<p style="float:right;width:75%;">';
			echo "<input id='$titleid' name='$title_name' value='$title_value' style='width:90%;' />";
		echo '</p>';
		echo '<br style="clear:both;"/><label><b>Select:&nbsp;&nbsp;</b></label>';

		// Build radio buttons for All, Incl , Excl for categories	
		$radioid = $this->get_field_id('choose_categories');
		$radio_name = $this->get_field_name('choose_categories');
		$radio_value = $instance['choose_categories'];
		$radio_checked = ($instance['choose_categories']=='A')?'checked':'';
		echo "All:&nbsp;<input type='radio' name='$radio_name' value='A' $radio_checked />&nbsp;|&nbsp;"; 
		$radio_checked = ($instance['choose_categories']=='I')?'checked':'';
		echo "Incl:&nbsp;<input type='radio' name='$radio_name' value='I' $radio_checked />&nbsp;|&nbsp;"; 
		$radio_checked = ($instance['choose_categories']=='E')?'checked':'';
		echo "Excl:&nbsp;<input type='radio' name='$radio_name' value='E' $radio_checked /><br/>"; 
		$inputid = $this->get_field_id('set_categories');
		$input_name = $this->get_field_name('set_categories');
		$input_value = ( array_key_exists( 'set_categories', $instance ) ) ? $instance['set_categories'] : '';
		echo "<input id='$inputid' name='$input_name'  value='$input_value' style='width:100%;margin-top:2px;'/>";
		echo '<i style="font-size:75%">Enter category IDs separated by commas</i>';
		echo '</fieldset><hr>';

		echo '<fieldset id="taxonomy-picker-pages">';
		echo '<p><h3>Pages</h3></p><label><b>Select:&nbsp;&nbsp;</b></label>';
		$radioid = $this->get_field_id('choose_pages');
		$radio_name = $this->get_field_name('choose_pages');
		$radio_value = $instance['choose_pages'];
		$radio_checked = ($instance['choose_pages']=='A')?'checked':'';
		echo "All:&nbsp;<input type='radio' name='$radio_name' value='A' $radio_checked />&nbsp;|&nbsp;"; 
		$radio_checked = ($instance['choose_pages']=='I')?'checked':'';
		echo "Incl:&nbsp;<input type='radio' name='$radio_name' value='I' $radio_checked />&nbsp;|&nbsp;"; 
		$radio_checked = ($instance['choose_pages']=='E')?'checked':'';
		echo "Excl:&nbsp;<input type='radio' name='$radio_name' value='E' $radio_checked /><br/>"; 

		$inputid = $this->get_field_id('set_pages');
		$input_name = $this->get_field_name('set_pages');
		$input_value = ( array_key_exists( 'set_pages', $instance ) ) ? $instance['set_pages'] : '';
		echo "<input id='$inputid' name='$input_name' value='$input_value' style='width:100%;margin-top:2px;'/>";
		echo '<i style="font-size:75%">Enter page IDs separated by commas</i>';
	echo '</fieldset>';
		
	}
	
} // End class
?>