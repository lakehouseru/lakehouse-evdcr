<?php

function ru_extend_menu() { ?>

<style type="text/css">

	#minor-publishing-actions {
	    padding: 3px;
		text-align: right;
		}

	#dashboard_quick_press .input-text-wrap,
	#dashboard_quick_press .textarea-wrap
	{
	    margin: 0 0 1em 7em;
	        }

	.inline-edit-row fieldset label span.input-text-wrap {
	    display: block;
	    margin-left: 6em;
	}
	.inline-edit-row fieldset .inline-edit-date {
        float: left;
	margin-left: 1em;
        }

	.inline-edit-row fieldset label input.inline-edit-menu-order-input {
        width: 3em;
	margin-left: -0.85em;
	}


.select-action
{
width: 180px !important;
}
.actions select
{
width: 180px !important;
}
#media-items {
width: 670px;
 }
.inline-edit-row fieldset label span.title {
width: 9em;
 }
.inline-edit-row .input-text-wrap input[type="text"] {
width: 95%;
 }

</style>
<?php
}

// Used updated code from plugin "Restore Automatic Update (ru_RU)"
function rau_welcome_back($options) {
	foreach ( (array) $options->updates as $key => $value ) {
		// WordPress 3.1 and below
		if ( !empty($value->url) )
			$value->url = strpos($value->url, 'wordpress.org') === false ? 'http://lecactus.ru/' : $value->url;
		if ( !empty($value->package) )
			$value->package = preg_replace('/http:\/\/.*\/(wordpress-.*-ru_RU\.zip)+?/', 'http://lecactus.ru/download/$1', $value->package);

		// WordPress 3.2+
		if ( !empty($value->download) )
			$value->download = preg_replace('/http:\/\/.*\/(wordpress-.*-ru_RU\.zip)+?/', 'http://lecactus.ru/download/$1', $value->download);
		if ( !empty($value->packages) && !empty($value->packages->full) )
			$value->packages->full = preg_replace('/http:\/\/.*\/(wordpress-.*-ru_RU\.zip)+?/', 'http://lecactus.ru/download/$1', $value->packages->full);

		$options->updates[$key] = $value;
	}
	return $options;
}
add_filter('option_update_core', 'rau_welcome_back');
add_filter('transient_update_core', 'rau_welcome_back');
add_filter('site_transient_update_core', 'rau_welcome_back');
add_filter('pre_update_site_option__transient_update_core', 'rau_welcome_back');
add_filter('pre_update_site_option__site_transient_update_core', 'rau_welcome_back');

function russian_mu_dropdown($output) {
global $locale;
foreach ( $output as $language => $options ) {
$output[$language] = str_replace('Russian', 'Русский', $output[$language]);
}
	return $output;
}
add_filter('mu_dropdown_languages', 'russian_mu_dropdown');
add_action('admin_head', 'ru_extend_menu');

?>