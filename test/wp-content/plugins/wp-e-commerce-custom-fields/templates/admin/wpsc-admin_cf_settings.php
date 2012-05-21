<h3><?php _e( 'Manage Attributes', 'wpsc_cf' ); ?></h3>
<table class="widefat">
	<thead>
		<tr>
			<th class="manage-column column-name"><?php _e( 'Name', 'wpsc_cf' ); ?></th>
			<th class="manage-column column-name"><?php _e( 'Type', 'wpsc_cf' ); ?></th>
			<th class="manage-column column-name"><?php _e( 'Slug', 'wpsc_cf' ); ?></th>
			<th class="manage-column column-name"><?php _e( 'Description', 'wpsc_cf' ); ?></th>
			<th class="manage-column column-name"><?php _e( 'Example', 'wpsc_cf' ); ?></th>
		</tr>
	</thead>
	<tbody>
<?php
if( $data ) {
	$i = 0;
	foreach( $data as $id => $field ) { ?>
		<tr id="custom-field-<?php echo $id; ?>">
			<td class="name column-name">
				<a href="admin.php?page=wpsc_cf&action=edit&id=<?php echo $id; ?>" class="row-title"><strong><?php echo $field['name']; ?></strong></a>
				<div class="row-actions">
					<span class="edit"><a href="admin.php?page=wpsc_cf&action=edit&id=<?php echo $id; ?>"><?php _e( 'Edit', 'wpsc_cf' ); ?></a></span> | 
					<span class="submitdelete"><a href="admin.php?page=wpsc_cf&action=delete&id=<?php echo $id; ?>"><?php _e( 'Delete', 'wpsc_cf' ); ?></a></span>
				</div>
			</td>
			<td class="name column-name"><?php echo wpsc_cf_return_type_label( $field['type'] ); ?></td>
			<td class="name column-name"><?php echo $field['slug']; ?></td>
			<td class="name column-name"><?php echo $field['description']; ?></td>
			<td class="name column-name"><?php echo stripslashes( $field['prefix'] ) . '[value]' . stripslashes( $field['suffix'] ); ?></td>
		</tr>
<?php
		$i++;
	}
}
if( $i == 0 ) { ?>
		<tr>
			<td colspan="3">
				<?php _e( 'No Attributes have been created.', 'wpsc_cf' ); ?>
			</td>
		</tr>
<?php
} ?>
	</tbody>
</table>

<form method="post" action="<?php the_permalink(); ?>" id="your-profile">

	<h3><?php _e( 'General', 'wpsc_cf' ); ?></h3>
	<table class="form-table">

		<tr>
			<th scope="row"><label for="position"><?php _e( 'Position', 'wpsc_cf' ); ?>:</label></th>
			<td>
				<select name="position">
<?php
$output = '';
for( $i = 0; $i < count( $positions ); $i++ )
	$output .= '<option value="' . $positions[$i][0] . '"' . selected( $positions[$i][0], $position, false ) . '>' . $positions[$i][1] . '&nbsp;</option>' . "\n";
echo $output; ?>
				</select> <span class="description"><?php _e( 'The placement of Attributes within the Product details template.', 'wpsc_cf' ); ?></span>
			</td>
		</tr>

	</table>
	<p><?php _e( 'For manual positioning use the following PHP template tag within the Single Product template', 'wpsc_cf' ); ?>.</p>
	<p><code><?php echo htmlentities2( '<?php if( function_exists( \'wpsc_the_custom_fields\' ) ) wpsc_the_custom_fields(); ?>' ); ?></code></p>
	<p><?php _e( 'To display individual custom fields use the following PHP template tag with the \'slug\' property', 'wpsc_cf' ); ?>.</p>
	<p><code><?php echo htmlentities2( '<?php if( function_exists( \'wpsc_the_custom_fields\' ) ) wpsc_the_custom_fields( \'slug=' . $field['slug'] . '\' ); ?>' ); ?></code></p>

	<h3><?php _e( 'Presentation', 'wpsc_cf' ); ?></h3>
	<table class="form-table">

		<tr>
			<th scope="row"><label for="layout"><?php _e( 'Layout', 'wpsc_cf' ); ?>:</label></th>
			<td>
				<select name="layout">
<?php
$output = '';
foreach( $layouts as $layout )
	$output .= '<option value="' . $layout[0] . '"' . selected( $layout[0], get_option( $wpsc_cf['prefix'] . '_layout' ), false ) . '>' . $layout[1] . '&nbsp;</option>' . "\n";
echo $output; ?>
				</select>
				<span class="description"><?php _e( 'The layout of Attributes within the Product details template.', 'wpsc_cf' ); ?></span>
			</td>
		</tr>

		<tr>
			<th scope="row"><label><?php _e( 'Header Visibility', 'wpsc_cf' ); ?>:</label></th>
			<td>
				<fieldset>
					<label><input type="radio" name="display_title" value="1"<?php checked( get_option( $wpsc_cf['prefix'] . '_display_title' ), 1 ); ?> /> <?php _e( 'I would like to display the Attributes header', 'wpsc_cf' ); ?></label><br />
					<label><input type="radio" name="display_title" value="0"<?php checked( get_option( $wpsc_cf['prefix'] . '_display_title' ), 0 ); ?> /> <?php _e( 'I would like to hide the Attributes header', 'wpsc_cf' ); ?></label>
				</fieldset>
				<p class="description"><?php _e( 'Show or hide the Related Products header.', 'wpsc_cf' ); ?></p>
			</td>
		</tr>

		<tr>
			<th scope="row"><label><?php _e( 'Header Title', 'wpsc_cf' ); ?>:</label></th>
			<td>
				<input type="text" name="title_text" value="<?php echo get_option( $wpsc_cf['prefix'] . '_title_text' ); ?>" size="10" class="regular-text" />
				<span class="description"><?php _e( 'The header text for Attributes.', 'wpsc_cf' ); ?></span>
			</td>
		</tr>

	</table>

	<p class="submit">
		<input type="submit" value="<?php _e( 'Save Changes', 'wpsc_cf' ); ?>" class="button-primary" />
	</p>
	<input type="hidden" name="action" value="update" />

</form>