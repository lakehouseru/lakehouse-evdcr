<div id="breadcrumbs">
	<? wpsc_output_breadcrumbs();?>
</div>
<h1 class="pageheader"><?php echo wpsc_category_name();?></h1>
<div id="post">
					<p class="description">
						<img class="rightimg" src="<?php echo wpsc_category_image(); ?>" width=186 height=161 align=right/>
						<?php echo wpsc_category_description(); ?>
					</p>
					<h2 class="collectionheader">Коллекции:</h2>
					<div id="collections">
				
							<? wpsc_display_child_cats(wpsc_category_id());?>
					
					</div>
				</div>