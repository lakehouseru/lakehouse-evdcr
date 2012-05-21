<?php
/* Start of: WP e-Commerce News - by Visser Labs */

if( !function_exists( 'wpsc_vl_dashboard_setup' ) ) {

	function wpsc_vl_dashboard_setup() {

		wp_add_dashboard_widget( 'wpsc_vl_news_widget', __( 'Plugin News - by Visser Labs', 'wpsc_vl' ), 'wpsc_vl_news_widget' );

	}
	add_action( 'wp_dashboard_setup', 'wpsc_vl_dashboard_setup' );

	function wpsc_vl_news_widget() {

		include_once( ABSPATH . WPINC . '/feed.php' );

		$rss = fetch_feed( 'http://www.visser.com.au/blog/category/e-commerce/feed/' );
		$output = '<div class="rss-widget">';
		if( !is_wp_error( $rss ) ) {
			$maxitems = $rss->get_item_quantity( 5 );
			$rss_items = $rss->get_items( 0, $maxitems );
			$output .= '<ul>';
			foreach ( $rss_items as $item ) :
				$output .= '<li>';
				$output .= '<a href="' . $item->get_permalink() . '" title="' . 'Posted ' . $item->get_date( 'j F Y | g:i a' ) . '" class="rsswidget">' . $item->get_title() . '</a>';
				$output .= '<span class="rss-date">' . $item->get_date( 'j F, Y' ) . '</span>';
				$output .= '<div class="rssSummary">' . $item->get_description() . '</div>';
				$output .= '</li>';
			endforeach;
			$output .= '</ul>';
		} else {
			$message = __( 'Connection failed. Please check your network settings.', 'wpsc_vl' );
			$output .= '<p>' . $message . '</p>';
		}
		$output .= '</div>';

		echo $output;

	}

}

/* End of: WP e-Commerce News - by Visser Labs */

/* Start of: WP e-Commerce Plugins - by Visser Labs */

if( !function_exists( 'wpsc_vm_dashboard_setup' ) ) {

	function wpsc_vm_dashboard_setup() {

		global $wpsc_cf;

		$plugin_slug = $wpsc_cf['dirname'];

		if( current_user_can( 'manage_options' ) ) {
			wp_add_dashboard_widget( 'wpsc_vm_status_widget', __( 'WP e-Commerce Plugins - by Visser Labs', 'wpsc_vm' ), 'wpsc_vm_status_widget' );
			if( file_exists( STYLESHEETPATH . '/wpsc-admin_dashboard_vm-plugins.css' ) )
				wp_enqueue_style( 'wpsc_vm_styles', get_bloginfo( 'stylesheet_directory' ) . '/wpsc-admin_dashboard_vm-plugins.css', false );
			else
				wp_enqueue_style( 'wpsc_vm_styles', plugins_url( $plugin_slug . '/templates/admin/wpsc-admin_dashboard_vm-plugins.css' ) );
		}

	}
	add_action( 'wp_dashboard_setup', 'wpsc_vm_dashboard_setup' );

	function wpsc_vm_status_widget() {

		global $wpsc_cf;

		$plugin_path = $wpsc_cf['abspath'];

		$check = wp_remote_fopen( 'http://www.visser.com.au/?wpsc_vm_data' );
		if( $check ) {
			$raw_plugins = explode( '<br />', $check );
			$vl_plugins = array();
			foreach( $raw_plugins as $raw_plugin ) {
				$raw_plugin = explode( '@', $raw_plugin );
				$vl_plugins[] = array(
					'name' => $raw_plugin[1],
					'version' => $raw_plugin[3],
					'url' => $raw_plugin[5]
				);
			}
		}

		$wp_plugins = get_plugins();
		foreach( $wp_plugins as $wp_plugin ) {
			if( $wp_plugin['Author'] == 'Visser Labs' ) {
				if( $vl_plugins ) {
					for( $i = 0; $i < count( $vl_plugins ); $i++ ) {
						if( $vl_plugins[$i]['name'] == $wp_plugin['Name'] ) {
							$vl_plugins[$i]['name'] = str_replace( 'WP e-Commerce - ', '', $vl_plugins[$i]['name'] );
							$vl_plugins[$i]['installed'] = true;
							if( ( version_compare( strval( $vl_plugins[$i]['version'] ), strval( $wp_plugin['Version'] ), '>' ) == 1 ) ) {
								$wp_plugins_update = true;
								$vl_plugins[$i]['version_existing'] = $wp_plugin['Version'];
							}
							if( strval( $wp_plugin['Version'] ) > strval( $vl_plugins[$i]['version'] ) )
								$vl_plugins[$i]['version_beta'] = $wp_plugin['Version'];
						}
					}
				}
			}
		}

		include_once( $plugin_path . '/templates/admin/wpsc-admin_dashboard_vm-plugins.php' );

	}

}

/* End of: WP e-Commerce Plugins - by Visser Labs */
?>