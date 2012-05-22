

	<div id="breadcrumbs">
		<? wpsc_output_breadcrumbs();?>
	</div>
	<h1 class="pageheader"><?php echo wpsc_category_name();?> <a href="#"> Фото в интерьере</a></h1>
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
</div>