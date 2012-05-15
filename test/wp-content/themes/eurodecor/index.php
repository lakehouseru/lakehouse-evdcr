<?php get_header();?>
<div id="content">
	<div id="post">
		<div id="breadcrumbs">
			<a href="<? bloginfo('url');?>">Главная</a> / <a href="<? bloginfo('url');?>?portfolio-type=portfolio">Портфолио</a> 
			
		</div>
		<?php while(have_posts()) : the_post();
		?>
		<h1><? the_title();?></h1>
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
			<a href="javascript:history.back(1)" class="back"><< Назад</a>
		</p>
	</div>
	<?php endwhile;?>
	<? get_sidebar();?>
</div>
<hr />
<?php get_footer();?>