<?php get_header(); ?>
<? get_sidebar(); ?>
<div class="fltrt" style="width: 735px;">
	<div id="breadcrumbs">
		<?php
		if (function_exists('bcn_display')) { bcn_display();
		}
		?>
	</div>
	<div id="content">
		<div id="post">
			<?php while(have_posts()) : the_post(); ?>
			<div class="fullpost">
				<div class="date">
					<? echo get_the_date(); ?>
				</div>
				<h3><a href="<? the_permalink(); ?>"><? the_title(); ?></a></h3>
				<? the_content('', true); ?>
			</div>
			<?php endwhile; ?>
		</div>
	</div>
</div>
<?php get_footer(); ?>