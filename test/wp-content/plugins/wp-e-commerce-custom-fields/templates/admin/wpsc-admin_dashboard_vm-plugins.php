<?php
$output = '';
if( $wp_plugins && $vl_plugins ) {
	$output .= '<div class="table table_content">';

	if( isset( $wp_plugins_update ) ) {
		$message = __( 'A new version of a Visser Labs Plugin for WP e-Commerce is available for download.', 'wpsc_vm' );
		$output .= '<p class="message">' . $message . '</p>';
	}

	$output .= '<table class="wpsc_vm_version_table">';
	$output .= '<tr><th style="text-align:left;">' . __( 'Plugin', 'wpsc_vm' ) . '</th><th style="text-align:left;">' . __( 'Version', 'wpsc_vm' ) . '</th><th style="text-align:left;">' . __( 'Status', 'wpsc_vm' ) . '</th></tr>';

	foreach( $vl_plugins as $vl_plugin ) {
		if( $vl_plugin['version'] ) {

			if( isset( $vl_plugin['installed'] ) ) {
				$output .= '<tr>';
				$output .= '<td><a href="' . $vl_plugin['url'] . '#toc-news" target="_blank">' . str_replace( ' for WP e-Commerce', '', $vl_plugin['name'] ) . '</a></td>';
				if( isset( $vl_plugin['version_existing'] ) ) {
					$output .= '<td class="version">' . $vl_plugin['version_existing'] . ' to <span>' . $vl_plugin['version'] . '</span></td>';
					if( $vl_plugin['url'] )
						$output .= '<td class="status"><a href="' . $vl_plugin['url'] . '#toc-update" target="_blank"><span class="red" title="Plugin update available for ' . $wp_plugin['Name'] . '.">Update</span></a></td>';
					else
						$output .= '<td class="status"><span class="red" title="Plugin update available for ' . $wp_plugin['Name'] . '.">Update</span></td>';
				} elseif( isset( $vl_plugin['version_beta'] ) ) {
					$output .= '<td class="version">' . $vl_plugin['version_beta'] . '</td>';
					$output .= '<td class="status"><span class="yellow" title="' . str_replace( ' for WP e-Commerce', '', $vl_plugin['name'] ) . ' is from the future.">Beta</span></td>';
				} else {
					$output .= '<td class="version">' . $vl_plugin['version'] . '</td>';
					$output .= '<td class="status"><span class="green" title="' . str_replace( ' for WP e-Commerce', '', $vl_plugin['name'] ) . ' is up to date.">OK</span></td>';
				}
				$output .= '</tr>';
			}
		}
		unset( $vl_plugin );
	}

	$output .= '</table>';

	$message = __( 'Looking for more WP e-Commerce Plugins?', 'wpsc_vm' );
	$output .= '<p class="link"><a href="http://www.visser.com.au/wp-ecommerce/" target="_blank">' . $message . '</a></p>';

	$output .= '</div>';

} else {

	$message = __( 'Connection failed. Please check your network settings.', 'wpsc_wm' );
	$output .= '<p>' . $message . '</p>';

}

echo $output;
?>