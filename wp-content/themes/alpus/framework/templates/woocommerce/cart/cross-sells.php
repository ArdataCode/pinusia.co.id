<?php
/**
 * Cross-sells
 *
 * @version 4.4.0
 */

defined( 'ABSPATH' ) || die;

global $alpha_layout;


if ( $cross_sells ) :

	// If cart page builder is set, don't display products
	if ( ! empty( $alpha_layout['alpha_panel_map'] ) && ! empty( $alpha_layout['cart_block'] ) ) {
		return;
	}

	wc_set_loop_prop( 'linked_products', true );
	?>

	<div class="cross-sells">
		<?php
		$heading = apply_filters( 'woocommerce_product_cross_sells_products_heading', __( 'You may be interested in&hellip;', 'alpha' ) );

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php woocommerce_product_loop_start(); ?>

			<?php foreach ( $cross_sells as $cross_sell ) : ?>

				<?php
					$post_object = get_post( $cross_sell->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					wc_get_template_part( 'content', 'product' );
				?>

			<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</div>
	<?php
endif;

wp_reset_postdata();
