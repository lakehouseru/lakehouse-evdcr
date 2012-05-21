<?php if( $custom_fields ) { ?>
<div id="custom_fields" class="wpsc_cf">
	<?php if( wpsc_cf_show_title() ) { ?>
	<h3><?php wpsc_cf_title(); ?></h3>
	<?php } ?>
	<ul class="wpsc_cf-list wpsc_cf-list_unordered">
	<?php foreach( $custom_fields as $custom_field_id => $custom_field ) { ?>
		<?php if( wpsc_cf_has_value( $custom_field ) ) { ?>
		<li id="wpsc_cf-list-<?php echo $custom_field_id; ?>">
			<?php if( wpsc_cf_show_name( $custom_field['show_name'] ) ) { ?><strong><?php wpsc_cf_label( $custom_field ); ?></strong>:<?php } ?>
			<span class="wpsc_cf_field-item"><?php wpsc_cf_value( $custom_field ); ?></span>
		</li>
		<?php } ?>
	<?php } ?>
	</ul>
</div>
<?php } ?>