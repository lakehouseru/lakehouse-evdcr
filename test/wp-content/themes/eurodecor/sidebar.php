<div class="fltlft" style="width: 202px;">
	<div id="search">
		<label>Поиск:</label><br />
		<input id="searchtool"
		size="22"
		onblur="this.value=(this.value=='')?this.title:this.value;this.style.backgroundColor='#f5f5f5';"
		onfocus="this.value=(this.value==this.title)?'':this.value;this.style.backgroundColor='#ffffff';"
		type="text"
		>
		<div id="searchbutton">
			<a href="#about"><img src="<?php bloginfo('template_url'); ?>/images/search.jpg" width=23px height=23px></a>
		</div>
	</div>
	<div id='sidebar'>
		<ul>
			<?php
			$args = array('taxonomy' => 'wpsc_product_category', 'orderby' => 'ID', 'order' => 'ASC', 'hide_empty' => 0, 'title_li' => '', 'use_desc_for_title' => 0  );
			wp_list_categories($args);
			?>
		</ul>
	</div>
</div>