<?php
/*
 * Template name: Координаты
 */
?>
<?php get_header();?>
<div id="content">
	<div id="post">
		<div id="breadcrumbs">
			<a href="<? bloginfo('url');?>">Главная</a> 
		</div>
		 <?php while(have_posts()) : the_post(); ?>
		<h1> <? the_title();?></h1>
		<div class="gallery fancy">
			
				<?  $args = array(
   'post_type' => 'attachment',
   'numberposts' => -1,
   'post_status' => null,
   'post_parent' => $post->ID,
   'orderby' => 'menu_order',
   'order' => 'DESC'
  );

  $attachments = get_posts( $args );
     if ( $attachments ) {
        foreach ( $attachments as $attachment ) {
	?>
	 <? echo wp_get_attachment_link($attachment -> ID, 'thumbnail');?> </dt>
        


	<?  }}?>
					</div>
		<? the_content();?>
		
		<p>
			<iframe width="640" height="400" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.com/maps/ms?msa=0&amp;msid=203690253234636239338.0004b86257b181eaa750e&amp;ie=UTF8&amp;t=m&amp;ll=55.739953,37.614334&amp;spn=0.002416,0.00751&amp;z=17&amp;iwloc=0004b86278dfd5156659a&amp;output=embed"></iframe>
					</p>
		<p>
			<a href="javascript:history.back(1)" class="back"><< Назад</a>
		</p>
	</div>
	 <?php endwhile; ?>
	<? get_sidebar();?>
</div>
<hr />
<?php get_footer();?>