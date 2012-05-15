<?php get_header();?>
<div id="content">
	<div id="post">
		<div id="breadcrumbs">
			<? $type = $wp_query -> query_vars["portfolio-type"];
			
			$type_name = get_term_by('slug', $type, 'portfolio-type');
			print_r(	$type_name);
			?>
			<a href="<? bloginfo('url');?>">Главная</a> / <a href="<? bloginfo('url');?>?portfolio-type=portfolio">Портфолио</a>
		</div>
		<h1><a href="#"><?php echo $type_name->name
		?></a></h1>
		<div class="gallery tiporise" >
			<?php while(have_posts()) : the_post();
			?>
			<a href="<? the_permalink();?>" title="<? the_title();?>"><?php the_post_thumbnail('thumbnail');?></a><?php endwhile;?>
		</div>
		<p>
			<a href="javascript:history.back(1)" class="back"><< Назад</a>
		</p>
	</div>
	<? get_sidebar();?>
</div>
<hr />
<?php get_footer();?>