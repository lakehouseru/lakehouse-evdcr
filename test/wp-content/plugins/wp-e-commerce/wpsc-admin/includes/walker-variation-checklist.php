<?php
/**
 * Walker Variation Checklist
 * Outputs checkboxes for variation sets
 */
class WPSC_Walker_Variation_Checklist extends Walker_Category_Checklist {
	private $highlighted = array();

	public function __construct( $highlighted = array() ) {
		$this->highlighted = $highlighted;
	}

	public function start_el(&$output, $category, $depth, $args) {
		extract($args);
		if ( empty($taxonomy) )
			$taxonomy = 'category';

		$checked = in_array( $category->term_id, $selected_cats );
		$input_class = ( $depth === 0 ) ? ' class="variation-set"' : '';
		$li_classes = array( 'wpsc-variation-checklist-item' );
		if ( $depth === 0 && defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			$li_classes[] = 'expanded';
			$li_classes[] = 'ajax';
		} elseif ( $checked && $depth === 0 ) {
			$li_classes[] = 'expanded';
		} elseif ( in_array( $category->term_id, $this->highlighted ) ) {
			$li_classes[] = 'ajax';
		}
		ob_start();
		?>
		<li id="<?php echo esc_attr( $taxonomy ); ?>-<?php echo $category->term_id; ?>" class="<?php echo implode( ' ', $li_classes ); ?>">
			<?php if ( $depth == 0 ): ?>
				<a href="#" class="expand"><?php echo esc_html( _x( 'Expand', 'product variation set', 'wpsc' ) ); ?></a>
			<?php endif ?>

			<label class="selectit">
				<input
					<?php echo $input_class; ?>
					type="checkbox" value="1"
					<?php if ( $depth !== 0 ): ?>
						name="edit_var_val[<?php echo $category->parent; ?>][<?php echo $category->term_id ?>]"
					<?php endif ?>
					id="in-<?php echo esc_attr( $taxonomy ) . '-' . $category->term_id; ?>"
					<?php checked( $checked, true ); disabled( empty( $args['disabled'] ), false ); ?>
				/>
				<?php echo esc_html( $category->name ); ?>
			</label>
		<?php
		$output .= ob_get_clean();
	}
}