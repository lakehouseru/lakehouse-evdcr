<div id="col-container">
	<div id="col-left">
		<div class="col-wrap">
			<div class="form-wrap">
				<h3><?php echo $title; ?></h3>
				<form method="post" action="admin.php?page=wpsc_cf" class="validate" >

					<div class="form-field form-required">
						<label><?php _e( 'Name', 'wpsc_cf' ); ?><span class="description"> (<?php _e( 'required', 'wpsc_cf' ); ?>)</span></label>
						<input type="text" name="custom-field-name" value="<?php echo stripslashes( $field['name'] ); ?>" />
						<p class="description"><?php _e( 'The name is how it appears on your site (if &quot;Show Name&quot; is checked)', 'wpsc_cf' ); ?>.</p>
					</div>

					<div class="form-field form-required">
						<label><?php _e( 'Slug', 'wpsc_cf' ); ?></label>
						<input type="text" name="custom-field-slug" value="<?php echo $field['slug']; ?>" />
						<p class="description"><?php _e( 'The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens', 'wpsc_cf' ); ?>.</p>
					</div>

					<div class="form-field form-required">
						<label><?php _e( 'Field Type', 'wpsc_cf' ); ?><span class="description"> (<?php _e( 'required', 'wpsc_cf' ); ?>)</span></label>
						<select name="custom-field-type">
<?php foreach( $options as $option ) { ?>
							<option value="<?php echo $option['name']; ?>"<?php selected( $option['name'], $field['type'] ); ?>><?php echo $option['label']; ?>&nbsp;</option>
<?php } ?>
						</select>
					</div>
<?php if( $field['type'] == 'dropdown' || $field['type'] == 'checkbox' || $field['type'] == 'radio' ) { ?>

					<div class="form-field">
						<label><?php _e( 'Options', 'wpsc_cf' ); ?></label>
						<input type="text" name="custom-field-options" value="<?php echo $field['options']; ?>" />
						<p class="description"><?php _e( 'Separate each option with a \'|\' (pipe) character. For instance, \'Option 1|Option 2\' will display 2 options on the Add/Edit Product page.', 'wpsc_cf' ); ?></p>
					</div>
<?php } ?>

					<div class="form-field">
						<label><?php _e( 'Order', 'wpsc_cf' ); ?></label>
						<input type="text" name="custom-field-order" value="<?php if( $field['order'] ) echo $field['order']; else echo '0'; ?>" size="4" />
					</div>

					<div class="form-field">
						<label><?php _e( 'Prefix', 'wpsc_cf' ); ?></label>
						<input type="text" name="custom-field-prefix" value="<?php echo ( $field['prefix'] ) ? stripslashes( $field['prefix'] ) : '' ?>" size="4" class="text" />
						<p class="description"><?php _e( 'A prefix to add when shown on your site', 'wpsc_cf' ); ?>.</p>
					</div>

					<div class="form-field">
						<label><?php _e( 'Suffix', 'wpsc_cf' ); ?></label>
						<input type="text" name="custom-field-suffix" value="<?php echo ( $field['suffix'] ) ? stripslashes( $field['suffix'] ) : '' ?>" size="4" />
						<p class="description"><?php _e( 'A suffix to add when shown on your site', 'wpsc_cf' ); ?>.</p>
					</div>
					
					<div class="form-field">
						<label>
							<input type="checkbox" style="width:13px;margin-top:-5px;" name="custom-field-show-name"<?php if( $action == 'edit' ) { checked( $field['show_name'], 'on' ); } else { checked( false, false ); } ?> />
							<?php _e( 'Show name', 'wpsc_cf' ); ?>
						</label>
						<p class="description"><?php _e( 'Should the name of field be shown in your site?', 'wpsc_cf' ); ?></p>
					</div>

					<div class="form-field">
						<label><?php _e( 'Description', 'wpsc_cf' ); ?></label>
						<textarea name="custom-field-description" rows="5" cols="40"><?php echo stripslashes( $field['description'] ); ?></textarea>
						<p class="description"><?php _e( 'The description is not prominent by default; however, some themes may show it', 'wpsc_cf' ); ?>.</p>
					</div>

<?php if( $action == 'new' ) { ?>
					<p class="submit">
						<input type="submit" id="submit" value="<?php _e( 'Add New Attribute', 'wpsc_cf' ); ?>" class="button" />
					</p>
					<input type="hidden" name="action" value="new-confirm" />
<?php } else { ?>
					<p class="submit">
						<input type="submit" id="submit" value="<?php _e( 'Update', 'wpsc_cf' ); ?>" class="button-primary" />
					</p>
					<input type="hidden" name="action" value="edit-confirm" />
					<input type="hidden" name="custom-field-id" value="<?php echo $id; ?>" />
<?php } ?>

				</form>
			</div>
		</div>
	</div>
</div>