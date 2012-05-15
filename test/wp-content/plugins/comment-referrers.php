<?php
/*
Plugin Name: Comment Referrers
Plugin URI: http://ocaoimh.ie/comment-referrers/
Description: Add referrer information to comment notifications
Author: Donncha O Caoimh
Version: 0.1
Author URI: http://ocaoimh.ie/
*/ 

function add_referrers( $post_id ) {
	global $current_user;
	?>
	<script type='text/javascript'>
	<!--
	ref = escape( document[ 'referrer' ] );
	document.write("<input type='hidden' name='ref' value='"+ref+"'>");
	// -->
	</script>
	<?php
}
add_action( 'comment_form', 'add_referrers' );

function add_referrer_to_notification( $text, $comment_id ) {
	if( $_POST[ 'ref' ] && $_POST[ 'ref' ] != '' ) {
		$ref = addslashes( urldecode( $_POST[ 'ref' ] ) );
		$ref = str_replace( '%3A', ':', $ref );
		$text .= "\r\nReferrer: $ref\r\n";
	}
	return $text;
}
add_filter( 'comment_notification_text', 'add_referrer_to_notification', 10, 2 );
add_filter( 'comment_moderation_text', 'add_referrer_to_notification', 10, 2 );
?>
