</div>
		<div class="footer">
			<div class="in">
			
					<? 
					
					$queryfooter = new WP_Query('pagename=footertext');?>
			
			<td id="arrowed_content"><?php while ($queryfooter->have_posts()) :  $queryfooter->the_post();
			?>
				<h1> <? the_title();?> </h1>
				<p>
					<? the_excerpt();?>
				</p>
				<? endwhile;?>
				<div id="subfooter"><p>
					<span class="fltlft">&copy; 2012 Евродекор</span>
					<span class="fltrt">Дизайн и разработка - <a href="http://www.lakehouse.ru/lite/">lakehouse:lite</a></span></p>
				</div>
			</div>
		</div>
	<? wp_footer();?></body>
</html>
		
