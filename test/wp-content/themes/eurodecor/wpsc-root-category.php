<div id="breadcrumbs">
	<? wpsc_output_breadcrumbs();?>
</div>
<h1 class="pageheader"><?php echo wpsc_category_name();?></h1>
<div id="post">
	<div id="brands">
		
		<? wpsc_display_child_cats(wpsc_category_id());?>
	</div>
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
