<!-- Uninstall the Taonomy Picker Plugin -->

<?php

if( !defined('WP_UNINSTALL_PLUGIN') ):
	exit();  //Ensure only run when WP requests an uninstall
else:
 	delete_option('taxonomy-picker-options'); // Delete the options stored in the database.  Ignore transients which will expire naturally over a few hours.
 	if( get_option( 'taxonomy-picker-taxonomies' ) ): 
 		delete_option('taxonomy-picker-taxonomies'); 
 	endif;
endif;

?>