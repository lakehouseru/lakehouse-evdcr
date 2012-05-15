<?php 

class AC_Settings extends scbBoxesPage {

	function setup() {
		$this->textdomain = 'admin-customization';
		
		$this->args = array(
			'page_title' => __( 'Admin Customization Settings', $this->textdomain ),
			'menu_title' => __( 'Admin Customization' , $this->textdomain ),
			'page_slug' => 'admin-customization',
		);
		
		$this->boxes = array(
			array( 'style_preferences', __( 'Style preferences', $this->textdomain ), 'normal' ),
			array( 'dashboard_settings', __( 'Dashboard widgets', $this->textdomain ), 'side' ),
			array( 'general_settings', __( 'General settings', $this->textdomain ), 'normal' ),
		);
	}

    /**
     *  Helper method that verifies if a file exists
     *
     *  @param $file - path to file relative to wp-content
     *  @param $echo_error - if true, echoes a wordpress error if the file is 
     *  missing
     *
     *  @returns true - if file exists, false - if it does not, nothing if file 
     *  path is empty
     */
     private function verify_file_existence( $file, $echo_error=false ) {
        // If favicon file does not exist, alert the user
        if ( !empty( $file ) ) {
            if ( !file_exists( ABSPATH . 'wp-content/' . $file ) ) {
                if ( $echo_error ) {
                $file_url = site_url( 'wp-content/' . $file );
?>
<div class="error"><p><?php printf( __( 'Cannot find any file at <a href="%1$s">%1$s</a>', $this->textdomain ), $file_url ); ?></p></div>
<?php
                }
                return false;
            } else {
                return true;
            }
        }      
    }   

    function page_header() {
        echo "<div class='wrap'>\n";
        screen_icon();
        echo "<h2>" . $this->args['page_title'] . "</h2>\n";
        echo '<p class="note">' . sprintf( __( 'Note: If you don\'t see any changes immediately after you saved, just <a href="%1$s">refresh</a> or visit a different admin page.', $this->textdomain ), admin_url('options-general.php?page=admin-customization') ) . "<p>"; 

        $this->verify_file_existence( $this->options->favicon, true );  
        $this->verify_file_existence( $this->options->login_logo, true );  
        $this->verify_file_existence( $this->options->admin_logo, true );  
    }

	function general_settings_box() {
		$output = '';
		$checkboxes = array(
			__( 'Hide update notices', $this->textdomain ) => array(
				'value' => 'hide_update_notices',
			),
			__( 'Hide plugin update count', $this->textdomain ) => array(
				'value' => 'hide_plugin_count',
			),
			__( 'Redirect to home page on logout', $this->textdomain ) => array(
				'value' => 'redirect_on_logout',
			),
		);
		foreach ( $checkboxes as $name => $args )
		{
		$output .= html( 'tr',
				html( 'td',
					$this->input( array(
						'type' => 'checkbox',
						'name' => 'general[]',
						'value' => $args['value'],
						'desc' => $name,
						'checked' => in_array( $args['value'], (array) $this->options->general_settings ),
					) )
				)
				);
				
		}
		
		echo $this->form_wrap( html( 'table class="checklist widefat notitle"', $output ), array('action' => 'general_preferences_button'));
	}
	
	function general_settings_handler() {
		if ( !isset( $_POST['general_preferences_button'] ) )
			return;

		$this->admin_msg( sprintf( __( 'General settings saved. Please <a href="%1$s">refresh</a> to see the changes.', $this->textdomain ), admin_url('options-general.php?page=admin-customization') ) );	
		// $this->admin_msg( __( 'General settings saved.', $this->textdomain ) );
		$this->options->general_settings = (array) @$_POST['general'];

        // Refresh page
        wp_redirect( admin_url('options-general.php?page=admin-customization') );
	}

	function style_preferences_box() {
        $favicon_exists = $this->verify_file_existence($this->options->favicon);
        $login_logo_exists = $this->verify_file_existence($this->options->login_logo);
        $admin_logo_exists = $this->verify_file_existence($this->options->admin_logo);

		$output = $this->table( array(
			array(
				'title' => __( 'Favicon', $this->textdomain ),
				'desc' => __( '(favicon path realative to wp-content)', $this->textdomain ),
				'type' => 'text',
				'name' => 'favicon',
                'extra' => empty( $favicon_exists ) ? array( 'class' => 'regular-text' ) : array( 'class' => 'ac_found regular-text' ), 
				'value' => implode( ', ', (array) $this->options->favicon )
			),

			array(
				'title' => __( 'Login logo', $this->textdomain ),
				'desc' => __( '(login logo path relative to wp-content)', $this->textdomain ),
				'type' => 'text',
				'name' => 'login_logo',
                'extra' => empty( $login_logo_exists ) ? array( 'class' => 'regular-text' ) : array( 'class' => 'ac_found regular-text' ),        
				'value' => implode( ', ', (array) $this->options->login_logo )
			),
			array(
				'title' => __( 'Admin logo ', $this->textdomain ),
				'desc' => __( '(admin logo path relative to wp-content.)<br />e.g.: "themes/mytheme/img/admin_logo.png"', $this->textdomain ),
				'type' => 'text',
				'name' => 'admin_logo',
                'extra' => empty( $admin_logo_exists ) ? array( 'class' => 'regular-text' ) : array( 'class' => 'ac_found regular-text' ),        
				'value' => implode( ', ', (array) $this->options->admin_logo )
			),
			array(
				'title' => false,
				'type' => 'checkbox',
				'name' => 'style_settings[]',
				'value' => 'hide_logo',
				'desc' => __( 'Hide admin logo', $this->textdomain ),
				'checked' => in_array( 'hide_logo', (array) $this->options->style_settings )
			),
			array(
				'title' => __( 'Logo text font size', $this->textdomain ),
				'desc' => __( 'The font size in pixels. Non-numeric characters will be stripped.', $this->textdomain ),
				'type' => 'text',
				'name' => 'admin_logo_font_size',
				'value' => implode( ', ', (array) $this->options->admin_logo_font_size )
			),
			array(
				'title' => false,
				'type' => 'checkbox',
				'name' => 'style_settings[]',
				'value' => 'hide_logo_name',
				'desc' => __( 'Hide admin logo text', $this->textdomain ),
				'checked' => in_array( 'hide_logo_name', (array) $this->options->style_settings )
			),
			array(
				'title' => __( 'Admin footer text left', $this->textdomain ),
				'desc' => __( 'Replacement for the left footer text. Accepts HTML.', $this->textdomain ),
				'type' => 'text',
				'name' => 'admin_footer_left',
				'value' => implode( ', ', (array)$this->options->admin_footer_left )
			),
			array(
				'title' => __( 'Admin footer text right', $this->textdomain ),
				'desc' => __( 'Replacement for the Wordpress version footer text on the right. Accepts HTML.', $this->textdomain ),
				'type' => 'text',
				'name' => 'admin_footer_right',
				'value' => implode( ', ', (array) $this->options->admin_footer_right ) 
			),
		) ); 
				
		// same as $this->form_wrap( $output, '', 'style_preferences_button');
		echo $this->form_wrap( $output, array('action' => 'style_preferences_button'));
	}
	
	function style_preferences_handler() {
		if ( !isset( $_POST['style_preferences_button'] ) )
			return;
		
		// $this->admin_msg( __( 'Style preferences changes saved.', $this->textdomain ) );
        $this->admin_msg( sprintf( __( 'Style preferences changes saved. Please <a href="%1$s">refresh</a> to see the changes.', $this->textdomain ), admin_url('options-general.php?page=admin-customization') ) );

        foreach ( array( 'favicon', 'login_logo', 'admin_logo', 'style_settings' ) as $key ) {
			$this->options->$key = @$_POST[$key];
        }

        foreach ( array( 'admin_footer_left', 'admin_footer_right' ) as $key ) {
			$this->options->$key = htmlspecialchars ( stripslashes( @$_POST[$key] ) );
        }
        // Strip non-numeric characters from the font size
        $font_size = ereg_replace( "[^0-9]", "", @$_POST['admin_logo_font_size'] );
        $this->options->admin_logo_font_size = empty( $font_size ) ? 16 : $font_size;

        // Refresh page
        // header( 'Location: ' . admin_url('options-general.php?page=admin-customization') );
	}

	function dashboard_settings_box() {
		$output = '<p class="updated">' . __( 'To update this list of widgets you must first visit the <a href="/wp-admin" target="_blank">Dashboard</a>.', $this->textdomain ) . '</p>' .
			$this->_widget_table(__( 'Disable All Widgets', $this->textdomain ), $this->options->widgets);

		// same as $this->form_wrap( $output, '', 'dashboard_settings_button');
		echo $this->form_wrap( $output, array('action' => 'dashboard_settings_button'));
	}
	
	function dashboard_settings_handler() {
		if ( !isset ( $_POST['dashboard_settings_button'] ) )
			return;
			
		$this->admin_msg( __( 'Dashboard widgets settings saved.', $this->textdomain ) );
		$this->options->disabled_widgets = (array) @$_POST['widgets'];
	}
	
	function default_css() {
?>
<style type="text/css">
img.checked {
    background: none repeat scroll 0 0 #EEEEEE;
    border: 1px solid #F9F9F9;
    border-radius: 20px 20px 20px 20px;
    float: right;
    margin-top: 1px;
    padding: 4px !important;
    position: relative;
    right: -5px;
}
.settings_page_admin-customization .widefat {
    background-color: transparent;
}
.settings_page_admin-customization .checklist thead th {
    font-family: Arial,"Bitstream Vera Sans",Helvetica,Verdana,sans-serif;
    font-size: 12px;
}
.postbox-container + .postbox-container {
	margin-left: 18px;
}
.postbox-container {
	padding-right: 0;
}
#wpbody-content .metabox-holder {
    padding-top:0;
    margin-top:10px;
}
.wrap div.updated, .wrap div.error {
margin: 10px 0;
}
.widefat td, .widefat th {
    border-top: 0;
}
.inside {
	clear: both;
	overflow: hidden;
	padding: 10px 10px 0 !important;
}
.inside table {
	margin: 0 !important;
	padding: 0 !important;
}
.inside table td {
	vertical-align: middle !important;
}
.inside table .regular-text {
	width: 100% !important;
}
.inside .form-table th {
	width: 20%;
	max-width: 200px;
	padding: 10px 0 !important;
    position: relative;
	font-size: 12px;
}
.inside .widefat .check-column {
	padding-bottom: 7px !important;
}
.inside p,
.inside table {
	margin: 0 0 10px !important;
}
.inside p.submit {
	float: right !important;
	padding: 0 !important;
}
.inside table.checklist {
	clear: both;
	margin-right: 1em !important;
	line-height: 1.6em;
}

.inside .checklist th input {
	margin: 0 0 0 4px !important;
}

.inside .checklist thead th {
	padding-top: 5px !important;
	padding-bottom: 5px !important;
}

.checklist thead th {
	background: none #F1F1F1 !important;
	padding: 5px 8px 8px;
	line-height: 1;
	font-size: 11px;
}

.checklist .check-column, .checklist th, .checklist td {
	padding-left: 0 !important
}
table.notitle {
	border: none !important;
}
.widefat tbody tr:last-child th, .widefat tbody tr:last-child td {
	border-bottom: 0;
}
.widefat thead th {
	border-top: 1px solid #ddd;
	border-bottom: 1px solid #ddd;
} 
.widefat {
	border: 0;
}
p.updated {
	padding: 0.6em;
	background-color: #FFFFE0;
    border-color: #E6DB55;
    border-style: solid;
    border-width: 1px;
    float: left;
    width: 98%;
}
#style_preferences table tbody > :last-child * {
	padding-top: 3px !important;
}
.note {
	font-size: 1.2em;
	font-style: italic;
	color: #777;
	font-family: Georgia,"Times New Roman","Bitstream Charter",Times,serif;
	margin-bottom: 0;
}
.note a {
    color: #444;
}
</style>
<script type="text/javascript"> 
jQuery(document).ready(function() {
    jQuery('#disable-all-widgets').change(function() {
        $checkboxes = jQuery(this).closest('table').find('.check-column input[type="checkbox"]');
        if ( jQuery(this).is(':checked') ) {
            $checkboxes.attr('checked', 'checked');
        } else {
            $checkboxes.removeAttr('checked');
        }
    });
    $check_img = jQuery('<img src="<?php echo $this->plugin_url ?>/img/check.png" class="checked" />');
    jQuery('.ac_found').closest('tr').find('th').append($check_img);

});
</script>
<?php 
	}
	private function _checklist_wrap( $title, $tbody ) {
		$thead =
		html( 'tr',
			 html( 'th scope="col" class="check-column"', '<input id="' . sanitize_title_with_dashes( $title ) . '" type="checkbox" />' )
			.html( 'th scope="col"', '<label for=' . sanitize_title_with_dashes( $title ) . '>' . $title . '</label>' )
		);
	 
		$table =
		html( 'table class="checklist widefat"',
			 html( 'thead', $thead )
			.html( 'tbody', $tbody )
		);

		return $table;
	}
	
	private function _widget_table( $title, $widgets ) {
		$tbody = '';
		foreach ( $widgets as $widget ) {
			if ( empty( $widget['title'] ) )
				continue;
			$tbody .=
			html( 'tr',
				html( 'th scope="row" class="check-column"',
					$this->input( array(
						'type' => 'checkbox',
						'name' => 'widgets[]',
						'value' => $widget['id'],
						'desc' => false,
						'extra' => array( 'id' => $widget['id'] ),
						'checked' => in_array( $widget['id'], (array) $this->options->disabled_widgets ),
					) )
				)
				.html( 'td', '<label for=' . $widget['id'] . '>' . $widget['title'] . '</label>')
			);
		}
		return $this->_checklist_wrap( $title, $tbody );
	}	
}
