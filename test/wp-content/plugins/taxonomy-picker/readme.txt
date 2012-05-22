=== Taxonomy Picker ===

Contributors: Kate Phizackerley, katephiz
Plugin Name: Taxonomy Picker
Plugin URI: http://www.squidoo.com/taxonomy-picker-wordpress-plugin
Tags: wp, query, taxonomy, category, categories, widget, plugin, sidebar, search
Author URI: http://katephizackerley.wordpress.com
Author: Kate Phizackerley
Requires at least: 3.1
Tested up to: 3.3.2
Stable tag: trunk
Version: 1.13.4

Add a widget to help your readers build custom queries using drop downs of your categories and custom taxonomies.  The latest version comes with a fully optional set of pre-packed custom taxonomies for anybody who wants additional "cartegories" or sets of "tags" without coding - the names are customisable.

== Description ==

Taxonomy Picker is a widget which you can include in your sidebar to help visitors build complex queries using categories and your custom taxonomies by chosing terms from drop down boxes.  The widget also includes a text search making it easy to search for text only within certain categories or taxonomies.  The latest version of the enhanced widget includes support multi-select combo boxes and Post Type.  

Results will be displayed using your theme's standard search form so the results need no additonal styling - but your permalinks must handle standard WordPress queries in the URL and some prettylink settings may be incompatible.

For example on my site I use it to allow users to:

+ Find all posts containing the word Egypt within the Books category
+ Find all posts within the Magazine category which match "Valley of the Kings" in my "Where?" custom taxonomy which also include the word Tutankhamun

If you don't know how to create custom taxonomies, don't worry.  For those who want it, there is an option to turn on support for a pre-pack set of custom taxonmies.  These have slugs like product, size, colour, who, what etc but you can change the labels on them so that they are whatever you need them to be on your site.  Of course some people prefer to regsiter their own custom taxonomies in code and that is fine.  

There is everything you need to set up a database site for a collection of photographs, a directory of local businesses, a real estate site or a site showcasing products, all examples I have seen.

You can display the query the reader selected from within code by using echo tpicker_query_string();


*Plugin home:* http://www.squidoo.com/taxonomy-picker-wordpress-plugin

== Installation ==

Download the and activate the plugin the usual way.  The new widget will then be immediately availble to use.

+ Upload files to the /wp-content/plugins/ directory
+ Activate the Taxonomy Picker plugin through the 'Plugins' menu in WordPress


== Upgrade Notice ==
= 1.13.3 =
Fix missing count and remove various PHP notices
= 1.13.1 =
Extends multi select option to basic, legacy widget
= 1.13.0 =
Adds experimental multi select option to beta widget (and to basic widget via filters).  Fixes notice warning.
= 1.12.0 =
Restructure Silverghll library code in preparation for release of Colophon plugin

== Screenshots ==

See  http://www.squidoo.com/taxonomy-picker-wordpress-plugin

== Changelog ==
1.13.3 Bug fixes
1.13.0 Adds experimental multi-select option to beta widget
1.10.9 Fixes bug with tags; adds tpicker_query_string() function
1.5  Admin screen.  Post count.  Remember query.  Better handling of "all" option.
1.4  Bug fixes
1.3  Sorted multiple words in plain text search
1.2  Fixed accents in taxonomies
1.12 Incompatibility with WP3.1 addressed
1.01 PHP warning removed

== Frequently Asked Questions ==

See  http://www.squidoo.com/taxonomy-picker-wordpress-plugin

= How does it work? =

Taxonomy Picker uses an HTML form to build a standard WordPress URL.  This is then passed to WordPress and your theme which should then display the correct results but that is out of the hands of the plugin. 

== Premium Plugin ==

= Introduction =

The plugin allows access to beta features of a forthcomimg premium version.  See the Explanation below for more details.  Beta in this case has the same sort of meaning given to it by Google: I use the beta version now on my live sites but I test upgrades on a development site first because the code itself is still evolving.  At a future date, the premium version is likely to require subscription.

= Premium Features =

At present premium features include:

- Option to use radio buttons instead of combo boxes
- Ability to sort the results
- Option to enable selection on post_date (alpha functionality only - it just works with limitation and needs better styling)

The tree view for taxonomies works but needs to be re-written as there are severe performance issues.  There are plans to add styling options using JQuery libraries and a date picker for instance.  One of the most requested features is an ability to change the order in which taxonomies are shown.  This is possible now using the tpicker_taxonomies filter but adding it to the UI would be neater.

`
add_filter( 'tpicker_taxonomies', 'my_tpicker_taxonomies');
function my_tpicker_taxonomies( $taxonomies_array ) {

	// Sort $taxonies_array into your desired order

	return $taxonomies_array;
}
`

Other filters are available and will be documented when the next support site is in place.

= Explanation =

The intention is to fork the plugin and release a premium version in late 2012 or early 2013.  This is based on new code for the core of the plugin.  The intention is that most enhancements are made in the premium version, although some trickle down into the standard/legacy version.  There are commercial considerations in moving towards a premium version, the plugin is complex and many enhancements now take considerable time, but the bigger reason is that the WordPress Repository requires that a plugin is compatible with the GPL v2 licence.  A commercial set up will also allow me to spend time on documenation and improve the support site.

