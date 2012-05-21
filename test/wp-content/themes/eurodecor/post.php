<?php
/*
 * Template name: Post
 */
?>
<?php get_header(); ?>
<? get_sidebar(); ?>
<div class="fltrt" style="width: 735px;">
	<div id="breadcrumbs">
		<?php
		if (function_exists('bcn_display')) { bcn_display();
		}
	?>
	</div>
	<h1 class="pageheader"> <? the_title(); ?></h1>
<div id="content">
	<div id="post">
		<?php while(have_posts()) : the_post(); ?>
		<? the_content(); ?>
		<?php endwhile; ?>
	</div>
</div>
</div>
<?php get_footer(); ?>