<?php

register_nav_menus(array('top' => 'верхнее меню', ));

add_theme_support('post-thumbnails');
add_image_size('home_thumb', 951, 381, true);

/*Function get single page photo*/
function get_single_page_photo() {
	global $wpdb, $post;

	$sql = "SELECT ID, post_title, post_excerpt
            FROM wp_posts p
            WHERE p.post_parent = $post->ID
            AND p.post_type = 'attachment'
            AND p.post_mime_type LIKE 'image%'
            ORDER BY menu_order
            ";

	$result = $wpdb -> get_results($sql);
	$images = array();

	foreach ($result as $top_images) {
		$image = wp_get_attachment_image_src($top_images -> ID, 'thumbnail');
		$link = '<a rel="' . $top_images -> post_title . '" class="post-foto" href="' . wp_get_attachment_url($top_images -> ID) . '"><img src="' . $image[0] . '" /></a>';
		$text = '<span>' . $top_images -> post_excerpt . '</span>';
		$images[] = array($link, $text);
	}

	return $images;
}


/*Remove Default Gallery Styles*/
function remove_gallery_css() {
	return "<ul class=\"gallery\">";
}

//add_filter('gallery_style', 'remove_gallery_css');

function fix_gallery_output($output) {
	$output = preg_replace("%</div>%", "</ul>", $output);
	return $output;
}

//add_filter('the_content', 'fix_gallery_output',11, 1);

/*Display second taxonomy depend on first taxonomy*/
function get_my_second_taxonomy() {
	global $post;

	$pagetitle = $post -> post_title;
	$parent_term = get_term_by('name', $pagetitle, 'portfolio-type');
	$parent_slug = $parent_term -> slug;

	$second_taxonomy = get_terms('portfolio-country', 'hide_empty=0&parent=0&orderby=id');

	echo "<ul id='country'>";
	foreach ($second_taxonomy as $taxonomy) {
		$children = get_term_children($taxonomy -> term_taxonomy_id, 'portfolio-country');
		echo "<li>";
		echo "<a href='/?portfolio-type=$parent_slug&portfolio-country=$taxonomy->slug'>$taxonomy->name</a>";
		if ($children) {
			echo "<ul>";
			foreach ($children as $child) {
				$term = get_term_by('id', $child, 'portfolio-country');
				echo "<li><a href='/?portfolio-type=$parent_slug&portfolio-country=$term->slug'>$term->name</a></li>";
			}
			echo "</ul>";
		}
		echo "</li>";
	}
	echo "</ul>";
}




// get taxonomies terms links
function custom_taxonomies_terms_links() {
	global $post, $post_id;
	// get post by post id
	$post = &get_post($post -> ID);
	// get post type by post
	$post_type = $post -> post_type;
	// get post type taxonomies
	$taxonomies = get_object_taxonomies($post_type);
	foreach ($taxonomies as $taxonomy) {
		// get the terms related to post
		$terms = get_the_terms($post -> ID, $taxonomy);
		if (!empty($terms)) {
			$out = array();
			foreach ($terms as $term)
				$out[] = '<a href="' . get_term_link($term -> slug, $taxonomy) . '">' . $term -> name . '</a>';
			$return = join(', ', $out);
		}
		return $return;
	}
}


	function is_category_level($depth) {
		$current_category = wpsc_category_id();
		$my_category = get_categories('include=' . $current_category.'&taxonomy=wpsc_product_category&hide_empty=0');
		$cat_depth = 0;

		if ($my_category[0] -> category_parent == 0) {
			$cat_depth = 0;
		} else {

			while ($my_category[0] -> category_parent != 0) {
				$my_category = get_categories('include=' . $my_category[0] -> category_parent.'&taxonomy=wpsc_product_category&hide_empty=0');
				$cat_depth++;
			}
		}
		if ($cat_depth == intval($depth)) {
			return true;
		}
		return null;
	}
	
	function wpsc_display_child_cats($parent_category_id){
	
	
	$category_data = get_terms('wpsc_product_category','hide_empty=0&parent='.$parent_category_id);

	foreach((array)$category_data as $category_row) {
	
		
		// Sticks the category description in
		$category_description = '';
		if($category_row->description != '') {
			$start_element = $query['description_container']['start_element'];
			$end_element = $query['description_container']['end_element'];
			$category_description =  $start_element.wpautop(wptexturize( wp_kses(stripslashes($category_row->description), $allowedtags ))).$end_element;
		}
		
		
	
		
		// get the category images
		$category_image = wpsc_place_category_image($category_row->term_id, $modified_query);

		$width = (isset($query['image_size']['width'])) ? ($query['image_size']['width']) : get_option('category_image_width');
		$height = (isset($query['image_size']['height'])) ? ($query['image_size']['height']) : get_option('category_image_height');
		$category_image = wpsc_get_categorymeta($category_row->term_id, 'image');
		$category_image_html = "<img src='".WPSC_CATEGORY_URL."$category_image' alt='{$category_row->name}' style='width: {$width}px; height: {$height}px;' class='wpsc_category_image' />";
		
		
		?>
		
		<a href="<?=get_term_link($category_row->slug, 'wpsc_product_category');?>" title="<?=$category_description;?>"><?=$category_image_html;?>
			<h3><?=$category_row->name;?></h3>
			
		</a>
						

		<?
	}
}
