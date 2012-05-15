<?php
/*
Plugin Name: MaxSite Russian Date
Plugin URI: http://maxsite.org/
Description: Русские даты 
Version: 1.01
Author: MAX
Author URI: http://maxsite.org/russian-date
*/

function maxsite_the_russian_time($tdate = '') {
	if ( substr_count($tdate , '---') > 0 ) return str_replace('---', '', $tdate);

	$treplace = array (
	"Январь" => "января",
	"Февраль" => "февраля",
	"Март" => "марта",
	"Апрель" => "апреля",
	"Май" => "мая",
	"Июнь" => "июня",
	"Июль" => "июля",
	"Август" => "августа",
	"Сентябрь" => "сентября",
	"Октябрь" => "октября",
	"Ноябрь" => "ноября",
	"Декабрь" => "декабря",
	
	"January" => "января",
	"February" => "февраля",
	"March" => "марта",
	"April" => "апреля",
	"May" => "мая",
	"June" => "июня",
	"July" => "июля",
	"August" => "августа",
	"September" => "сентября",
	"October" => "октября",
	"November" => "ноября",
	"December" => "декабря",	
	
	"Sunday" => "воскресенье",
	"Monday" => "понедельник",
	"Tuesday" => "вторник",
	"Wednesday" => "среда",
	"Thursday" => "четверг",
	"Friday" => "пятница",
	"Saturday" => "суббота",
	
	"Sun" => "воскресенье",
	"Mon" => "понедельник",
	"Tue" => "вторник",
	"Wed" => "среда",
	"Thu" => "четверг",
	"Fri" => "пятница",
	"Sat" => "суббота",
	
	"th" => "",
	"st" => "",
	"nd" => "",
	"rd" => ""

	);
   	return strtr($tdate, $treplace);
}

add_filter('the_time', 'maxsite_the_russian_time');
add_filter('get_comment_date', 'maxsite_the_russian_time');
add_filter('the_modified_time', 'maxsite_the_russian_time');

?>