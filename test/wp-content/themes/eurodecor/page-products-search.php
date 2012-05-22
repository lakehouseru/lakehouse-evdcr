<?php
/*
 * Template name: Результаты поиска по товарам
 */
?>
<?php get_header();?>
<? get_sidebar();?>

<div class="fltrt" style="width: 735px;">
	<div id="breadcrumbs">
		<div class="wpsc-breadcrumbs">
			<a class="wpsc-crumb" id="wpsc-crumb-home" href="<? bloginfo('url');?>">Евродекор</a> » <a class="wpsc-crumb" id="wpsc-crumb-4" href="<? bloginfo('url');?>/products-page/">Каталог</a> 
		</div>
	</div>
	<h1 class="pageheader">Результаты поиска</h1>
	<table id="arrowed" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td id="arr_left"><a href="#"><img src="<?php bloginfo('template_url');?>/images/arr_l.png" /> </a></td>
			
			<? 
			
			$args = array(
					'tax_query' => array(
						'relation' => 'OR',
						array(
							'taxonomy' => 'wpsc_product_category',
							'field' => 'ID',
							'terms' => array( $_REQUEST['cat'] )
						)
					),
					'meta_query' => array(
						array(
							'key' => '_wpsc_width',
							'value' => array(  $_REQUEST['width_from'], $_REQUEST['width_to'] ),
							'type' => 'numeric',
							'compare' => 'BETWEEN'
						)
					),'meta_query' => array(
						array(
							'key' => '_wpsc_price',
							'value' => array(  $_REQUEST['width_price'], $_REQUEST['price_to'] ),
							'type' => 'numeric',
							'compare' => 'BETWEEN'
						)
					),
					'post_type' => 'wpsc-product'
			);
			
			$wpsc_query = new WP_Query($args);?>
			
			<td id="arrowed_content"><?php while ($wpsc_query->have_posts()) :  $wpsc_query->the_post();
			?>

			<div class="shop_item">
				<a href="<?php echo wpsc_the_product_image();?>"><img src="<?php echo wpsc_the_product_thumbnail();?>" /> <h3><? echo wpsc_the_product_title();?></h3> </a>
				<p class="item_desc">
					<?php echo wpsc_the_product_description();?>
				</p>
				<a class="button" href="<? echo wpsc_the_product_permalink();?>">О товаре/Заказать</a>
			</div><? endwhile;?>

			<div style="display:none; " id="hpagination">
				<?php wpsc_pagination();?>
			</div></td>
			<td id="arr_right"><a href="#"> <img src="<?php bloginfo('template_url');?>/images/arr_r.png" /> </a></td>
	</table>
</div>

<? include(TEMPLATEPATH . '/product-filter.php');?>
<?php get_footer();?>