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
				<div id="banners" class="slider-code">
					
    <div  class="viewport" >
					<ul class="overview">
					<?  
	$page = get_page_by_title('Коллекции внизу' );

	$args = array(
'post_type' => 'attachment',
'numberposts' => 6,
'post_status' => null,
'post_parent' => $page->ID,
'orderby' => 'menu_order',
'order' => 'DESC'
);

$attachments = get_posts( $args );
if ( $attachments ) {
foreach ( $attachments as $attachment ) {

	
			?>
		
								<li><a href="<?=$attachment->post_excerpt;?>" title="<?=$attachment->post_title;?>"><? echo wp_get_attachment_image($attachment -> ID,  'medium');?> </a></li>
								<li><a href="<?=$attachment->post_excerpt;?>" title="<?=$attachment->post_title;?>"><? echo wp_get_attachment_image($attachment -> ID,  'medium');?> </a></li>
							
		

			<?  }}?>
					</ul></div>
				</div>
			</div>
<?php get_footer();?>