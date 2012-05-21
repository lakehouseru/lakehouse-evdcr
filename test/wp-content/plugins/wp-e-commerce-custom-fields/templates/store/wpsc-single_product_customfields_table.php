<?php if( $custom_fields ) { ?>
<div id="custom_fields" class="wpsc_cf">
	<?php if( wpsc_cf_show_title() ) { ?>
	<h3><?php wpsc_cf_title(); ?></h3>
	<?php } ?>
	<table class="wpsc_cf-table" border="0" cellpadding="0" cellspacing="0">
	<?php foreach( $custom_fields as $custom_field_id => $custom_field ) { ?>
		<?php if( wpsc_cf_has_value( $custom_field ) ) { ?>
		<tr id="wpsc_cf-table-<?php echo $custom_field_id; ?>">
			<td class="wpsc_cf_field-name">
				<?php if( wpsc_cf_show_name( $custom_field['show_name'] ) ) { ?><strong><?php wpsc_cf_label( $custom_field ); ?></strong>:<?php } ?>
			</td>
			<td class="wpsc_cf_field-value">
				<span class="wpsc_cf_field-item"><?php wpsc_cf_value( $custom_field ); ?></span>
			</td>
		</tr>
		<?php } ?>
	<?php } ?>
	</table>
</div>
<?php } ?>