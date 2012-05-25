<?php
/*
 * Template name: Страница - список товаров
 */
?>
<?php get_header();?>
<? get_sidebar();?>






<div class="fltrt" style="width: 735px;">
	<?php if(wpsc_is_single_product()) :
include(TEMPLATEPATH . '/wpsc-single_product.php');
	?>
	
	
	<? else:
	if ( is_category_level('0') ) {
	include(TEMPLATEPATH . '/wpsc-root-category.php');
	} elseif ( is_category_level('1') ) {
	include(TEMPLATEPATH . '/wpsc-level1-category.php');
	} else {
	include(TEMPLATEPATH . '/wpsc-products_page.php');
	}
	?>

	<?php endif;?>

	


<? include(TEMPLATEPATH . '/product-filter.php');?></div>
<?php get_footer();?>