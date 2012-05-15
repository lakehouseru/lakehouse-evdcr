<?php
/*
Plugin Name: Russify Comments Number
Plugin URI: http://ulizko.com/russify_comments_number
Description: Нормальное отображение окончания слова "комментарий" - то есть, "2 комментария", "5 комментариев" и так далее.
Version: 0.1
Author: Alexander Ulizko
Author URI: http://ulizko.com
*/

function russify_comments_number($zero = false, $one = false, $more = false, $deprecated = '') {
	global $id;
	$number = get_comments_number($id);
	
	if ($number == 0) {
		$output = 'Комментариев нет';
	} elseif ($number == 1) {
		$output = 'Один комментарий';
	} elseif (($number > 20) && (($number % 10) == 1)) {
		$output = str_replace('%', $number, '% комментарий');
	} elseif ((($number >= 2) && ($number <= 4)) || ((($number % 10) >= 2) && (($number % 10) <= 4)) && ($number > 20)) {
		$output = str_replace('%', $number, '% комментария');
	} else {
		$output = str_replace('%', $number, '% комментариев');
	}
	echo apply_filters('russify_comments_number', $output, $number);
}

add_filter('comments_number', 'russify_comments_number');
	
?>
