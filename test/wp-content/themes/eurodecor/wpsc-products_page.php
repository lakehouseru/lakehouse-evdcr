

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
<div id="filter" class="closed">
	<a href="#filter" id="fhook"><h4>Фильтрация</h4></a>
	<form>
		<p>
			<label class="description" for="element_1">Тип обоев </label>
			<span>
				<input id="element_1_1" name="element_1_1" class="element checkbox" type="checkbox" value="1" />
				<label class="choice" for="element_1_1">Виниловые</label>
				<input id="element_1_2" name="element_1_2" class="element checkbox" type="checkbox" value="1" />
				<label class="choice" for="element_1_2">Флизелиновые</label>
				<input id="element_1_3" name="element_1_3" class="element checkbox" type="checkbox" value="1" />
				<label class="choice" for="element_1_3">Натуральные</label>
				<input id="element_1_4" name="element_1_4" class="element checkbox" type="checkbox" value="1" />
				<label class="choice" for="element_1_4">Тканевые</label> </span>
		</p>
		<p>
			<label class="description" for="element_2">Размер </label>
			<span>
				<input id="element_2_1" name="element_2_1" class="element checkbox" type="checkbox" value="1" />
				<label class="choice" for="element_2_1">0,67 м</label>
				<input id="element_2_2" name="element_2_2" class="element checkbox" type="checkbox" value="1" />
				<label class="choice" for="element_2_2">0,93 м</label>
				<input id="element_2_3" name="element_2_3" class="element checkbox" type="checkbox" value="1" />
				<label class="choice" for="element_2_3">1,05 м</label>
				<input id="element_2_4" name="element_2_4" class="element checkbox" type="checkbox" value="1" />
				<label class="choice" for="element_2_4">1,25 м</label> </span>
		</p>
		<p>
			<label class="description" for="element_3">Цена за рулон </label>
			<span>
				<input id="element_3_1" name="element_3_1" class="element checkbox" type="checkbox" value="1" />
				<label class="choice" for="element_3_1">до 300 руб.</label>
				<input id="element_3_2" name="element_3_2" class="element checkbox" type="checkbox" value="1" />
				<label class="choice" for="element_3_2">300 - 900 руб.</label>
				<input id="element_3_3" name="element_3_3" class="element checkbox" type="checkbox" value="1" />
				<label class="choice" for="element_3_3">900 - 1500 руб.</label>
				<input id="element_3_4" name="element_3_4" class="element checkbox" type="checkbox" value="1" />
				<label class="choice" for="element_3_4">более 1500 руб.</label>
		</p>
		<button>
			Искать
		</button>
	</form>