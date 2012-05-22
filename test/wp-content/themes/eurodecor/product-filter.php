<div id="filter" class="closed">
	<a href="#filter" id="fhook"><h4>Фильтрация</h4></a>
	<form method="post" action="<? bloginfo('url');?>/searchresults" >
		<p>
			<label class="description" for="element_1">Производитель</label>
			 <? 
			 $args = array('show_option_all'=> 'Все','taxonomy' => 'wpsc_product_category', 'orderby' => 'ID', 'order' => 'ASC', 'hide_empty' => 0, 'title_li' => '', 'use_desc_for_title' => 0 , 'child_of' => 4 );
			
			 wp_dropdown_categories( $args )?> 
		</p>
		<p>
			<label class="description" for="element_2">Ширина (м.) </label>
			<span>
				<label class="choice" for="element_2_1">От</label>
				<input id="element_2_1" name="width_from" class="element checkbox"  value="0" maxlength="3" style="width:50px;"  />
				<label class="choice" for="element_2_2">До</label>
				<input id="element_2_2" name="width_to" class="element checkbox"  value="10"  maxlength="3" style="width:50px;"/>
				 </span>
		</p>
		<p>
			<label class="description" for="element_3">Цена за рулон (руб.)</label>
	
			<span>
				<label class="choice" for="price_from">От</label>
				<input id="price_from" name="price_from" class="element checkbox"  value="0" maxlength="6" style="width:50px;"  />
				<label class="choice" for="price_to">До</label>
				<input id="price_to" name="price_to" class="element checkbox"  value="2000"  maxlength="6" style="width:50px;"/>
				 </span>
		</p>
		<button>
			Искать
		</button>
	</form>
</div>