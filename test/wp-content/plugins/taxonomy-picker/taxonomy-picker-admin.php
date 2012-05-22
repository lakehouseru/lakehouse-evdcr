<?php

// PHP for the Taxonomy Picker Admin menu
// Version: 1.13.4

add_action( 'admin_menu', 'silverghyll_tpicker_menu_initialisation', 20); // silverghyll Menu added as 10
add_action( 'admin_init', 'taxonomy_picker_admin_init', 20 ); 
add_filter('plugin_action_links', 'taxonomy_picker_plugin_action_links', 10, 2);  // Filter to add action settings

/** Taxonomy Action Settings  **
**********************************/

function taxonomy_picker_plugin_action_links($links, $file) { // Add 'Settings" to action links

    static $this_plugin;
    if( !isset($this_plugin) ) $this_plugin = 'taxonomy-picker/taxonomy-picker.php';

    if ($file == $this_plugin):
        $settings_link = '<a href="' . admin_url() . 'admin.php?page=' . basename(dirname(__FILE__)) . '/'. basename(__FILE__) . '">Settings</a>';
        array_unshift($links, $settings_link);
    endif;
    return $links;
}


/** Taxonomy Picker Admin Menu  **
**********************************/

// Adds the Taxonomy Picker admin menu in the silverghyll section
function silverghyll_tpicker_menu_initialisation() {
		$tpicker_admin_page = add_submenu_page( 'silverghyll-admin-menu.php', 'Taxonomy Picker', 'Taxonomy Picker', 'administrator', __FILE__, 'silverghyll_create_tpicker_menu'  );
	add_action( 'admin_print_styles-' . $tpicker_admin_page, 'silverghyll_admin_styles' ); // Add our admin style sheet
}

// Register and define settings
function taxonomy_picker_admin_init() {

	$tpicker_options = get_option('taxonomy-picker-options');

	register_setting( 'taxonomy-picker-options', 'taxonomy-picker-options','taxonomy_picker_options_validate'); // Register settings
	
	add_settings_section( "tpicker-processing", 'Query Processing', 'tpicker_nothing', "tpicker-processing-sec");
				
	$fn_txt = "silverghyll_admin_combobox('taxonomy-picker-options',
			'all-format',array('** All **','All Items','Blank','Everything','Every {name}','All {name}', 'All {name}s', 'N/A'));";

/*	$fn_txt = str_replace( $fn_txt, 'All', __('All','tpicker') );	
	$fn_txt = str_replace( $fn_txt, 'Items', __('Items','tpicker') );	
	$fn_txt = str_replace( $fn_txt, 'Everything', __('Everything','tpicker') );	
	$fn_txt = str_replace( $fn_txt, 'Every', __('Every','tpicker') );	*/


	$fn_txt = str_replace( array('All','Blank','Items','Every','Everything'),
				array( __('All','tpicker'), __('Blank','tpicker'), __('Items','tpicker'),__('Every','tpicker'),__('Everything','tpicker') ), $fn_txt );			
	
	add_settings_field( "all-format", 'Text for \'all\' option', create_function('',$fn_txt),	"tpicker-processing-sec","tpicker-processing");

	add_settings_field('all-override','Override text for ** all ** (optional)', 'taxonomy_picker_tpfn2', "tpicker-processing-sec","tpicker-processing");				

	add_settings_field('search-text','Alternative text for "search" (optional)', 'taxonomy_picker_tpfn3', "tpicker-processing-sec","tpicker-processing");				

	add_settings_field( "show-count", 'Show item count', 
				create_function('',"silverghyll_admin_checkbox('taxonomy-picker-options','show-count');"), "tpicker-processing-sec", "tpicker-processing");

	$fn_txt = "silverghyll_admin_combobox('taxonomy-picker-options','empty-terms',array('always=>Always','never=>Never','sometimes=>If Only Empty Descendents'));";
	add_settings_field( "empty-terms", 'Include empty terms?', create_function('',$fn_txt),	"tpicker-processing-sec","tpicker-processing");


	add_settings_field( "remember", 'Remember the user query?', 
				create_function('',"silverghyll_admin_checkbox('taxonomy-picker-options','remember');"), "tpicker-processing-sec", "tpicker-processing");

	add_settings_field('miss-url','Redirect to URL on null search', 'taxonomy_picker_tpfn', "tpicker-processing-sec","tpicker-processing");				

	/* Housekeeping options */
	
	add_settings_field( "labels_after", 'Labels after fields?', 
				create_function('',"silverghyll_admin_checkbox('taxonomy-picker-options','labels_after');"), "tpicker-housekeeping-sec", "tpicker-housekeeping");

	add_settings_section( "tpicker-housekeeping", 'Housekeeping Options', 'tpicker_nothing', "tpicker-housekeeping-sec" );

	add_settings_field( "taxonomies", 'Add pre-pack taxonomy support?', 
				create_function('',"silverghyll_admin_checkbox('taxonomy-picker-options','taxonomies');"), "tpicker-housekeeping-sec", "tpicker-housekeeping");

	add_settings_field( "no-stylesheet", 'Disable stylesheet?', 
				create_function('',"silverghyll_admin_checkbox('taxonomy-picker-options','no-stylesheet');"), "tpicker-housekeeping-sec", "tpicker-housekeeping");

	$fn_txt = "silverghyll_admin_combobox('taxonomy-picker-options','punctuation',array(' ',':','?','-'));";
	add_settings_field( "punctuation", 'List punctuation?', create_function('',$fn_txt), "tpicker-housekeeping-sec", "tpicker-housekeeping");
	
	add_settings_field( "premium-widget", "Use beta/premium widget (see notes)", 
					create_function('',"silverghyll_admin_checkbox('taxonomy-picker-options','premium-widget');"), "tpicker-housekeeping-sec", "tpicker-housekeeping");

	add_settings_field( "colophon", 'Enable colophon shortcode?', 
				create_function('',"silverghyll_admin_checkbox('taxonomy-picker-options','colophon');"), "tpicker-housekeeping-sec", "tpicker-housekeeping");
				
	add_settings_field( "redirect", 'Enable redirect debug mode?', 
				create_function('',"silverghyll_admin_checkbox('taxonomy-picker-options','redirect');"), "tpicker-housekeeping-sec", "tpicker-housekeeping"); 

	/* Results Options */
	if( $tpicker_options['premium-widget'] ):


		// Enable post formats and post types
		add_settings_field( "post_format", 'Enable post format (NB search results not guaranteed)?', 
						create_function('',"silverghyll_admin_checkbox('taxonomy-picker-options','post_format');"), "tpicker-processing-sec", "tpicker-processing"); 

		add_settings_field( "post_type", 'Enable post type?', 
					create_function('',"silverghyll_admin_checkbox('taxonomy-picker-options','post_type');"), "tpicker-processing-sec", "tpicker-processing");

		add_settings_section( "tpicker-results", 'Results Handling', 'tpicker_nothing', "tpicker-results-sec" );

/*		add_settings_field( "taxonomies", 'Sort taxonomies using Priority field?', 
					create_function('',"silverghyll_admin_checkbox('taxonomy-picker-options','sort-priority');"), "tpicker-housekeeping-sec", "tpicker-housekeeping");
*/
		foreach( array('author', 'comment_count', 'date','ID', 'modified', 'title') as $item):
				add_settings_field( "results_sort_$item", "Allow sort by $item?", 
							create_function('',"silverghyll_admin_checkbox('taxonomy-picker-options','results_sort_$item');"), "tpicker-results-sec", "tpicker-results") ; 
		endforeach;
	endif;

}

function tpicker_nothing(){
	// Nothing to do!
} 

function taxonomy_picker_tpfn() {silverghyll_admin_textbox( 'taxonomy-picker-options', 'miss-url', 40); }
function taxonomy_picker_tpfn2() {silverghyll_admin_textbox( 'taxonomy-picker-options', 'all-override', 20); }
function taxonomy_picker_tpfn3() {silverghyll_admin_textbox( 'taxonomy-picker-options', 'search-text', 20); }

function taxonomy_picker_options_validate($input) {
	$newinput = $input;
	
	$newinput['miss-url'] = esc_url( $input['miss-url'] ); // Sanitize URL
	$newinput['all-override'] = strip_tags( $input['all-override'] ); // Sanitize URL
	$newinput['search-text'] = strip_tags( $input['search-text'] ); // Sanitize URL

	// Save the current version of the plugin in our options so that we can test for updates
	$silverghyll_plugins = get_silverghyll_plugins();
	$taxonomy_plugin = $silverghyll_plugins['Taxonomy Picker'];
	$newinput['version'] = $taxonomy_plugin['Version'];

	// Default the result sort options to on when Premium widget first selected
	if( ($newinput['beta-widget'] == 'on') and !$newinput['premium-widget'] ):
		 foreach( array('author', 'comment_count', 'date','ID', 'modified', 'title') as $item) $newinput["results_sort_$item"] = 'on';
		 $newinput['premium-widget'] = $newinput['beta-widget'];
	endif;
	
	return $newinput;
}


function silverghyll_create_tpicker_menu(){
	$silverghyll_plugins = get_silverghyll_plugins();
	$taxonomy_plugin = $silverghyll_plugins['Taxonomy Picker']; // The readme.txt details for Taxonomy Picker
	$tp = 'taxonomy-picker'; // Just a convenient shorthand
	$tpicker_options = get_option('taxonomy-picker-options');
	?>
	<div class="wrap">	
		<div class="icon32" id="icon-options-general"><br></div>

		<h2>Taxonomy Picker (v<?php echo $taxonomy_plugin['Version'] . ')' . ( ( $tpicker_options['premium-widget'] ) ? ' Premium Widget &beta;': ''); ?></h2>
		
		<?php silverghyll_check_foundation( true ); // Check silvergyll-foundation.php is up to date and display a warning if not ?>

		<p><strong>For help and support please vist <a href="http://www.squidoo.com/taxonomy-picker-wordpress-plugin" target="_blank">http://www.squidoo.com/taxonomy-picker-wordpress-plugin</a></strong></p>
		
		<form action="options.php" method="post"><table><tbody><tr>
		
			<?php  settings_fields( "$tp-options" ); ?>
 			
 			<td style="vertical-align:top;"><?php do_settings_sections("tpicker-processing-sec"); ?></td>
 			<td style="vertical-align:top;"> <?php do_settings_sections("tpicker-housekeeping-sec"); ?></td><?php 
 			if( $tpicker_options['premium-widget'] ):
 				echo '<td style="vertical-align:top;">';
 				do_settings_sections("tpicker-results-sec"); 
 				echo "<p style='background:#fffcb6;font-weight:bold;padding:12px;border 3px solid black;max-width:350px;'>Concentration has been on adding features.  The beta version still needs a lot of code consolidation, debugging and testing.  You have been warned.</p>";
 				echo '</td>'; 
 			endif; ?> 
					
 		</tr></tbody></table><p>&nbsp;</p>
		<input name="Submit" type="submit" value="Save Changes" />
		</form>
		<p><strong>NB</strong> The beta widget has additional options but is still undergoing final testing. The additional options include radio buttons as an alternative to combo boxes and the ability to control the order of results. When this is complete (forthcoming release 2.0.0 of the plugin) it may become a paid-for, premium widget.  The basic, legacy widget will always remain free and it is suggested that you use that unless you need any of the advanaced features.</p>
		<p><strong>&copy; <a href="http://www.egyptological.com/about/founders" target="_blank">Kate Phizackerley</a>, 2011-2012</strong></p>
	</div> <!-- Wrap -->
	<?php
}

?>