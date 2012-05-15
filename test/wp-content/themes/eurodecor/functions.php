<?php 

register_nav_menus( array(
        'top' =>  'верхнее меню',
  
        ) );

add_theme_support( 'post-thumbnails' );
add_image_size( 'home_thumb', 951, 381, true);



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

    $result = $wpdb->get_results($sql);
    $images = array();

    foreach ($result as $top_images) {
        $image = wp_get_attachment_image_src($top_images->ID, 'thumbnail');
        $link =  '<a rel="'.$top_images->post_title.'" class="post-foto" href="'. wp_get_attachment_url($top_images->ID) .'"><img src="'.$image[0] .'" /></a>';
        $text =  '<span>'.$top_images->post_excerpt.'</span>';
        $images[] = array($link, $text);
    }

    return $images;
}



/*Register Post type*/
$args_furniture = array(
        'label' => __('Работы'),
        'singular_label' => __('portfolio'),
        'public' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => false,
        'query_var' => true,
        'supports' => array('title', 'editor','thumbnail', 'revision'),
        'menu_position' => 5
);

register_post_type( 'portfolio' , $args_furniture );


/*Register Taxonomy*/
function my_build_taxo(){
    register_taxonomy( 'portfolio-type', 'portfolio', array( 'hierarchical' => true, 'label' => 'Разделы', 'query_var' => true, 'rewrite' => true ) );
   
}

add_action( 'init', 'my_build_taxo', 0 );



/*Remove Default Gallery Styles*/
function remove_gallery_css()
{
    return "<ul class=\"gallery\">";
}

//add_filter('gallery_style', 'remove_gallery_css');

function fix_gallery_output( $output )
{
    $output = preg_replace("%</div>%", "</ul>", $output);
    return $output;
}
//add_filter('the_content', 'fix_gallery_output',11, 1);


/*Display second taxonomy depend on first taxonomy*/
function get_my_second_taxonomy(){
    global $post;
    
    $pagetitle = $post->post_title;
    $parent_term = get_term_by('name', $pagetitle, 'portfolio-type');
    $parent_slug = $parent_term->slug;
   
    $second_taxonomy = get_terms('portfolio-country', 'hide_empty=0&parent=0&orderby=id');
    
    echo "<ul id='country'>";
        foreach($second_taxonomy as $taxonomy){
            $children = get_term_children($taxonomy->term_taxonomy_id, 'portfolio-country');
            echo "<li>";
            echo "<a href='/?portfolio-type=$parent_slug&portfolio-country=$taxonomy->slug'>$taxonomy->name</a>";
            if($children){
                echo "<ul>";
                    foreach($children as $child){
                        $term = get_term_by('id', $child, 'portfolio-country');
                        echo "<li><a href='/?portfolio-type=$parent_slug&portfolio-country=$term->slug'>$term->name</a></li>";
                    }
                echo "</ul>";
            }
            echo "</li>";
        }
    echo "</ul>";
}


function get_my_second_taxonomy_2(){
    global $post;
    
    $pagetitle = $post->post_title;
    $parent_term = get_term_by('name', $pagetitle, 'portfolio-type');
    $parent_slug = $parent_term->slug;
   
    $second_taxonomy = get_terms('portfolio-country', 'hide_empty=0&parent=0&orderby=id');
    
    echo "<ul id='country'>";
        foreach($second_taxonomy as $taxonomy){  
            $posts = get_posts('post_type=portfolio&&numberposts=-1&order=ASC&orderby=title&portfolio-type='.$parent_slug.'&portfolio-country='.$taxonomy->slug);
            echo "<li>$taxonomy->name";
            if($posts){
               echo "<ul>";
               foreach($posts as $post){
                echo "<li><a href='".get_permalink($post->ID) ."'>$post->post_title</a></li>";
               }
               echo "</ul>";
            }
            echo "</li>";
        }
    echo "</ul>";
}

register_sidebar(array(
  'name' => 'Календарик',
  'id' => 'cal-sidebar',
  'description' => 'По идее тут должен быть календарик и ничего менять не надо',
  'before_title' => '<h4>',
  'after_title' => '</h4>'
));

function the_taxo($postid, $taxo)
{
	$term_list = wp_get_post_terms($postid, $taxo, array("fields" => "names"));
	echo '<a href="'.get_bloginfo('url').'?portfolio-type=houses">'.$term_list[0].'</a>';
	
	
	//print_r(wp_get_post_terms($postid, $taxo, array("fields" => "portfolio")));
}

// get taxonomies terms links
function custom_taxonomies_terms_links() {
	global $post, $post_id;
	// get post by post id
	$post = &get_post($post->ID);
	// get post type by post
	$post_type = $post->post_type;
	// get post type taxonomies
	$taxonomies = get_object_taxonomies($post_type);
	foreach ($taxonomies as $taxonomy) {
		// get the terms related to post
		$terms = get_the_terms( $post->ID, $taxonomy );
		if ( !empty( $terms ) ) {
			$out = array();
			foreach ( $terms as $term )
				$out[] = '<a href="' .get_term_link($term->slug, $taxonomy) .'">'.$term->name.'</a>';
			$return = join( ', ', $out );
		}
		return $return;
	}
} ?>