<?php
/*
Plugin Name: Unique Comments
Version: 0.2
Plugin URI: http://uplift.ru/projects/
Description: Checks via Google Search if the comment being left is a common spam comment. Inspired by Delink Comment Author plugin from Alex King.
Author: Sergey Biryukov
Author URI: http://sergeybiryukov.ru/
*/

function uc_get_url($comment_id) {
	$comment = get_comment($comment_id);

	preg_match('/^.{0,256}([\s]|$)/is', $comment->comment_content, $match);
	$query = '"' . urlencode(strip_tags(trim($match[0]))) . '"';

	return "http://www.google.ru/search?q={$query}&oe=utf-8";
}

function uc_check_comment($comment_id) {
	$ch = curl_init(uc_get_url($comment_id));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	$html = curl_exec($ch);
	curl_close ($ch);

	preg_match('/<h2 class=r>(.*?)<\/h2>/', $html, $matches);

	if ( count($matches) > 0 && false === strpos($html, 'yellow_warning.gif') ) {
		if ( 'spam' == get_option('unique_comments') ) {
			wp_set_comment_status($comment_id, 'spam');
		} else {
			wp_update_comment(array( comment_approved => 0 ));
		}
	}
}
add_action('comment_post', 'uc_check_comment');

if ( !empty($_GET['uc_action']) && !empty($_GET['comment_id']) ) {
	if ( 'check_comment' == $_GET['uc_action'] ) {
		header('Location: ' . uc_get_url(intval($_GET['comment_id'])));
		exit();
	}
}

function uc_row($actions, $comment) {
	$check_url = admin_url("index.php?uc_action=check_comment&comment_id=$comment->comment_ID");
	$actions['check'] = "<a href='$check_url' target='_blank'>" . __('Check&nbsp;Uniqueness', 'unique-comments') . '</a>';;	
	return $actions;
}
add_filter('comment_row_actions', 'uc_row', 10, 2); 

function uc_email($text, $comment_id) {
	$check_url = admin_url("index.php?uc_action=check_comment&comment_id=$comment_id");
	return $text .= __('Check Uniqueness: ', 'unique-comments') . "$check_url\r\n";
}
add_filter('comment_notification_text', 'uc_email', 10, 2);
add_filter('comment_moderation_text', 'uc_email', 10, 2);

function uc_options_page() {
?>
<div class="wrap">
<h2><?php _e('Unique Comments Options', 'unique-comments'); ?></h2>
<?php
if ( $_POST['uc_options'] ) {
	update_option('unique_comments', $_POST['uc_options']);
	echo '<div class="updated fade"><p>' . __('Options saved.', 'unique-comments') . '</p></div>';
}
?>
<form method="post">

<p><?php _e('Treat non-unique comments as: ', 'unique-comments'); ?>
<select name="uc_options" style="vertical-align: middle">
<?php
$current_setup = get_option('unique_comments');
$options = array(
	array( 'moderated', __('Moderated', 'unique-comments') ),
	array( 'spam', __('Spam', 'unique-comments') )
);
foreach ( $options as $option ) :
	$selected = ($option[0] == $current_setup ? ' selected="selected"' : '');
	echo "<option value='" . strtolower($option[0]) . "'$selected>{$option[1]}</option>";
endforeach;
?>
</select></p>

<p class="submit">
<input type="submit" value="<?php _e('Save Changes', 'unique-comments'); ?>" />
</p>

</form>
</div>
<?
}

function uc_add_menu() {
	add_options_page(__('Unique Comments', 'unique-comments'), __('Unique Comments', 'unique-comments'), 8, __FILE__, 'uc_options_page');
}
add_action('admin_menu', 'uc_add_menu');

load_plugin_textdomain('unique-comments', PLUGINDIR . '/unique-comments');
?>