<?php get_header();?>
<div class="content">
				<div id="slides">
					<div class="slides_container">
						
					<?  $args = array(
'post_type' => 'attachment',
'numberposts' => -1,
'post_status' => null,
'post_parent' => 2,
'orderby' => 'menu_order',
'order' => 'DESC'
);

$attachments = get_posts( $args );
if ( $attachments ) {
foreach ( $attachments as $attachment ) {
			?>
			
			<div class="slide">
								<? echo wp_get_attachment_link($attachment -> ID, 'full');?> 
								
							<div class="caption" >
								<h2> <?=$attachment -> post_title;?> </h2>
								<p><?=$attachment -> post_content;?>
										</p>
							</div>
						</div>
		

			<?  }}?>
			
			
					</div>
				</div>
				<div id="banners">
					<a href="#" title="Название коллекции"><img src="<?php bloginfo('template_url'); ?>/images/banner_low.jpg" /></a>
					<a href="#" title="Название коллекции"><img src="<?php bloginfo('template_url'); ?>/images/banner_low.jpg" /></a>
					<a href="#" title="Название коллекции"><img src="<?php bloginfo('template_url'); ?>/images/banner_low.jpg" /></a>
					<a href="#" title="Название коллекции"><img src="<?php bloginfo('template_url'); ?>/images/banner_low.jpg" /></a>
					<a href="#" title="Название коллекции" class="last"><img src="<?php bloginfo('template_url'); ?>/images/banner_low.jpg" /></a>
				</div>
			</div>
<?php get_footer();?>