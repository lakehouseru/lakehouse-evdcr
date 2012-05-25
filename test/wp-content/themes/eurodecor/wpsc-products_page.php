<div id="breadcrumbs">
	<? wpsc_output_breadcrumbs();?>
</div>
<h1 class="pageheader"><?php echo wpsc_category_name();?> <a href="#" id="gointfoto"> Фото в интерьере</a></h1>
<table id="arrowed" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td id="arr_left"><a href="#"><img src="<?php bloginfo('template_url');?>/images/arr_l.png" /> </a></td>
		<td id="arrowed_content"><?php while (wpsc_have_products()) :  wpsc_the_product();
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

<div id="intfoto">
	<?  
	$page = get_page_by_title(wpsc_category_name().' ФИ','OBJECT', 'wpsc-product' );

	$args = array(
'post_type' => 'attachment',
'numberposts' => -1,
'post_status' => null,
'post_parent' => $page->ID,
'orderby' => 'menu_order',
'order' => 'DESC'
);

$attachments = get_posts( $args );
if ( $attachments ) {
foreach ( $attachments as $attachment ) {
			?>
		
								<? echo wp_get_attachment_link($attachment -> ID, 'thumb');?> 
								
							
		

			<?  }}?>
</div>
</div>