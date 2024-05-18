<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || die;

if ( $max_value && $min_value === $max_value ) {
	?>
	<div class="quantity hidden">
		<input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="qty" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $min_value ); ?>" />
	</div>
	<?php
} else {
	/* translators: %s: Quantity. */
	$label = ! empty( $args['product_name'] ) ? sprintf( esc_html__( '%s quantity', 'alpha' ), wp_strip_all_tags( $args['product_name'] ) ) : esc_html__( 'Quantity', 'alpha' );
	?>
	<div class="quantity-wrapper">
		<?php do_action( 'woocommerce_before_quantity_input_field' ); ?>

		<label><?php esc_html_e( 'Quantity:', 'alpha' ); ?></label>
		<div class="quantity">
			<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $label ); ?></label>
			<input
				type="number"
				id="<?php echo esc_attr( $input_id ); ?>"
				class="<?php echo esc_attr( join( ' ', (array) $classes ) ); ?>"
				step="<?php echo esc_attr( $step ); ?>"
				min="<?php echo esc_attr( $min_value ); ?>"
				<?php echo 0 < $max_value ? 'max="' . esc_attr( $max_value ) . '"' : ''; ?>
				name="<?php echo esc_attr( $input_name ); ?>"
				value="<?php echo esc_attr( $input_value ); ?>"
				title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'alpha' ); ?>"
				placeholder="<?php echo esc_attr( $placeholder ); ?>"/>			
			<button class="quantity-minus <?php echo ALPHA_ICON_PREFIX; ?>-icon-minus" title="<?php esc_attr_e( 'Minus', 'alpha' ); ?>"></button>
			<button class="quantity-plus <?php echo ALPHA_ICON_PREFIX; ?>-icon-plus" title="<?php esc_attr_e( 'Plus', 'alpha' ); ?>"></button>
			<?php do_action( 'woocommerce_after_quantity_input_field' ); ?>
		</div>
	</div>
	<?php
}
