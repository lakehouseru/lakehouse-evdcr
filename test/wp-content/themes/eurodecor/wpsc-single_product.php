<div id="breadcrumbs">
	<? wpsc_output_breadcrumbs();?>
</div>
<h1 class="pageheader"><?php echo wpsc_category_name();?> - <? echo wpsc_the_product_title();?></h1>
<div id="post">
	<div id="previewblock1">
		<div id="bigimageblock">
			<a href="<?php echo wpsc_the_product_image();?>"><img src="<?php echo wpsc_the_product_thumbnail(330,210);?>"/></a>
		
			<div id="price">

						Цена:
						<span class="redlarge">
							<? echo wpsc_the_product_price();?>
						</span>

			</div>
		</div>
		<div id="chart">
			<?php if( function_exists( 'wpsc_the_custom_fields' ) ) wpsc_the_custom_fields(); ?>
		<a class="tack1" href="javascript: history.go(-1)">Назад в каталог
	</a>
		</div>
	</div>
	
	<div id="previewblock2">
		<p>
			Сделать заказ:
		</p>
		<p class="graysmall">
			Количество метров:&#160;&#160;
			<input class="input" size=4>
			м &#160;=
		</p>
		<p class="redlarge">
			4,5
		</p>
		<p class="redsmall">
			рулона
		</p>
		<button class="tack2">
			Заказать
		</button>
		<button class="tack3">
			Отложить в корзину
		</button>
	</div>
	<div id="previewblock3">
		<p class="graylarge">
			Рассчитать количество метров:
		</p>
		<div id="meters">
			<p class="redextralarge">
				43,75
			</p>
			<p class="graymedium">
				метров
			</p>
		</div>
		<p class="graysmall">
			Ширина комнаты:
			<input class="input" size=3>
			м
		</p>
		<p class="graysmall">
			Высота комнаты:&#160;
			<input class="input" size=3>
			м
		</p>
		<p class="graysmall">
			Длина комнаты:&#160;&#160;&#160;
			<input class="input" size=3>
			м
		</p>
		<button class="tack4">
			Рассчитать
		</button>
	</div>
</div>

<script language="JavaScript" type="text/javascript">
	$("body").css("background-image","url(<?php bloginfo('template_url'); ?>/images/inner_bg.jpg)");
	$("body").css("background-repeat","repeat");
	
</script>