<?php 
class AC_Core {
	static $options;
	
	static function init( $options ) {
		
		self::$options = $options;
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'ac_dashboard_setup' ), 99);
		add_action( 'admin_head', array( __CLASS__, 'ac_admin_head_setup' ) );
		add_action( 'admin_init', array( __CLASS__, 'ac_remove_update_notices' ) );
		add_action( 'admin_menu', array( __CLASS__, 'ac_remove_plugin_update_count' ) );
		add_filter( 'admin_user_info_links', array( __CLASS__, 'ac_redirect_on_logout' ) );
		add_action( 'login_head', array( __CLASS__, 'ac_login_head_setup' ) );
		add_filter( 'login_headerurl', array( __CLASS__, 'ac_login_url' ) );
		add_filter( 'login_headertitle', array( __CLASS__, 'ac_login_title' ) );
		add_filter( 'admin_footer_text', array( __CLASS__, 'ac_footer_left' ) );
		add_filter( 'update_footer', array( __CLASS__, 'ac_footer_right' ), 11 );
	}
	
	function ac_remove_update_notices()	{
//		global $current_user;	
//		get_currentuserinfo();
//		if ($current_user->user_login != 'admin')
		if ( in_array( 'hide_update_notices', (array) self::$options->general_settings ) )
			remove_action('admin_notices', 'update_nag', 3);	
	}
	
	function ac_remove_plugin_update_count() {
		if ( in_array( 'hide_plugin_count', (array) self::$options->general_settings ) )
		{
			global $menu, $submenu;
		 
		    $menu[65][0] = 'Plugins';  
		    $submenu['index.php'][10][0] = 'Updates';  
		}
	}
	
	function ac_redirect_on_logout($links) {
		if ( in_array( 'redirect_on_logout', (array) self::$options->general_settings ) ) {
			$links[15] = '| <a href="' . wp_logout_url( home_url() ) . '" title="Log Out">Log Out</a>';
		}
			return $links;
	}
	
	
    function ac_admin_head_setup() {
        // Make logo mark into a link
?>
<script type="text/javascript">
        jQuery(document).ready(function() {
            $logo_mark = jQuery('#header-logo');
            $site_url = jQuery('<a href="<?php echo site_url(); ?>"></a>');
            $site_url.append($logo_mark);
            jQuery('#site-heading').before($site_url);
        });
</script>
<?php
		// Favicon
		if ( !empty( self::$options->favicon ) )
			echo '<link rel="shortcut icon" href="' . site_url() . '/wp-content/' . self::$options->favicon . '" />';
		
		$styles = array();
		
        // Fix wp 3.2 user info dropdown width bug
        $styles[] = '
            #user_info > div {
                min-width: 95px !important;
            }
            ';
		// Backend logo name
        $is_logo_text_hidden = false;
        if ( in_array( 'hide_logo_name', (array) self::$options->style_settings ) ) {
            $styles[] = '#site-heading { display: none !important }';	
            $is_logo_text_hidden = true;
        }

        // Backend logo
        $is_logo_hidden = false;
		if ( in_array( 'hide_logo', (array) self::$options->style_settings ) ) {
	        $styles[] = '#header-logo { display: none !important }';	
            $is_logo_hidden = true;
        }

        // Wordpress's default top, left margins
        $margins = array( 7, 7 );
        // Wordpress's default logo width and height
        $logo_size = array( 16, 16 );

        $font_size = $is_logo_text_hidden ? 16 : self::$options->admin_logo_font_size;
        // if logo mark and name aren't both hidden
        if ( ( count( (array)self::$options->style_settings ) < 2 ) ) {
            // If admin logo needs to be set
            if ( !empty( self::$options->admin_logo ) && file_exists( ABSPATH . 'wp-content/' . self::$options->admin_logo ) && !$is_logo_hidden ) {
                // Get logo information
                $logo_path = site_url( 'wp-content/' . self::$options->admin_logo );
                $logo_size = getimagesize( $logo_path );

                $styles[] ='
                    #header-logo {
                        background:url(' . $logo_path . ') left center no-repeat !important;
                        height: ' . $logo_size[1] . 'px !important;
                        width: ' . $logo_size[0] . 'px !important;
                        margin-top: ' . max( ( $font_size / 2 - $logo_size[1] / 2 + $margins[1] ), $margins[1] ) . 'px;
                    }';

                $adjusted_head_height = max( $logo_size[1] + $margins[1] * 2, $font_size + $margins[1] * 2, 32 );

            } else if ( !$is_logo_text_hidden ){
                $styles[] ='
                    #header-logo {
                        margin-top: ' . max( ( $font_size / 2 - $logo_size[1] / 2 + $margins[1] ), $margins[1] ) . 'px;
                    }';
                // Calculate the header height
                $adjusted_head_height = max( 16 + $margins[1] * 2, $font_size + $margins[1] * 2, 32 );
            }
            if ( !empty( $adjusted_head_height ) ) {
                $styles[] ='
                    #wphead {
                    height: ' . $adjusted_head_height . 'px;
                }'; 
            }
			$styles[] ='
                #wphead h1 {
                    margin-top:' . max( ( $logo_size[1] / 2 + $margins[1] - $font_size / 2 ), $margins[0] ) . 'px;
                    margin-left:' . ( $is_logo_hidden ? '0' : $margins[0] ) . 'px;
                    padding: 0;
                    font-size: '. $font_size . 'px;
                    line-height: '. $font_size . 'px;
                }'; 
        }

        // Echo style modifications, if any
        if ( !empty( $styles ) ) {
            echo '<style type="text/css">';

            foreach ( $styles as $rule ){
                echo $rule;
            }
            echo '</style>';

        }
		
			
	}
	
	function ac_login_head_setup() {
		if ( !empty( self::$options->login_logo ) ) {
		  $logo_path = site_url() . '/wp-content/' . self::$options->login_logo;
		  $logo_size = getimagesize( $logo_path );
          $wp_default_width = 320;
		  echo '<style type="text/css">
          h1 a
          {
          	background:url(' . $logo_path . ') center top no-repeat !important;
          	height: '. $logo_size[1] . 'px;
            width: auto !important;
        }
        #login {
            width: ' . max( $logo_size[0], $wp_default_width ).  'px;
        }
        form {
            margin-left: 0;
        }
        </style>';
		}
	}

	function ac_login_url() {
		echo home_url();
	}
	
	function ac_login_title() {
		echo get_option( 'blogname' );
	}

    function ac_footer_left( $footer_text ) {
        if ( !empty( self::$options->admin_footer_left ) ) {
            return htmlspecialchars_decode ( self::$options->admin_footer_left );
        } else {
            return $footer_text;
        }        
    }

    function ac_footer_right( $upgrade ) {
        if ( !empty( self::$options->admin_footer_right ) ) {
            return htmlspecialchars_decode ( self::$options->admin_footer_right );
        } else {
            return $upgrade;
        }        
    }
			
	function ac_dashboard_setup() {
	 	global $wp_meta_boxes;
		self::$options->widgets = self::_get_unset_dashboard_widgets(self::$options->disabled_widgets);
	}
	
	private function _get_unset_dashboard_widgets($disabled_widgets = array()) {
		global $wp_meta_boxes;
		
		if ( isset($wp_meta_boxes['dashboard']) ) {
			foreach ( $wp_meta_boxes['dashboard'] as $context => $data ) {
				foreach ( $data as $priority=>$data ) {
					foreach( $data as $widget=>$data ) {
						$widgets[$widget] = array('id' => $widget,
										   'title' => strip_tags( preg_replace('/( |)<span.*span>/im', '', $data['title']) ),
										   'context' => $context,
										   'priority' => $priority
										   );
						// unset the required widgets
						if ( in_array( $widget, (array) $disabled_widgets ) ) 
							unset($wp_meta_boxes['dashboard'][$context][$priority][$widget]);
					}
				}
			}
		}
		return $widgets;
	}
}
