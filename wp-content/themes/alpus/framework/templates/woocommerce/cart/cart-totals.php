<?php
/**
 * Cart totals
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-totals.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 2.3.6
 */

defined( 'ABSPATH' ) || die;

global $post;
$is_cart = is_cart() || ( $post && ALPHA_NAME . '_template' == $post->post_type && 'cart' == get_post_meta( $post->ID, ALPHA_NAME . '_template_type', true ) );

?>
<div class="cart_totals <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">
	<div class="cart-information">

		<?php do_action( 'woocommerce_before_cart_totals' ); ?>

		<h3 class="cart-title"><?php esc_html_e( 'Cart Totals', 'alpha' ); ?></h3>

		<table cellspacing="0" class="shop_table">

			<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
				<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
					<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
					<td data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
				</tr>
			<?php endforeach; ?>

			<tr class="cart-subtotal">
				<th><?php esc_html_e( 'Subtotal', 'alpha' ); ?></th>
				<td data-title="<?php esc_attr_e( 'Subtotal', 'alpha' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></td>
			</tr>

			<?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

				<?php do_action( 'woocommerce_cart_totals_before_shipping' ); ?>

				<?php
				if ( alpha_is_elementor_preview() ) {

					$packages = WC()->shipping()->get_packages();
					$first    = true;

					foreach ( $packages as $i => $package ) {
						$chosen_method = isset( WC()->session->chosen_shipping_methods[ $i ] ) ? WC()->session->chosen_shipping_methods[ $i ] : '';
						$product_names = array();

						if ( count( $packages ) > 1 ) {
							foreach ( $package['contents'] as $item_id => $values ) {
								$product_names[ $item_id ] = $values['data']->get_name() . ' &times;' . $values['quantity'];
							}
							$product_names = apply_filters( 'woocommerce_shipping_package_details_array', $product_names, $package );
						}

						wc_get_template(
							'cart/cart-shipping.php',
							array(
								'package'                  => $package,
								'available_methods'        => $package['rates'],
								'show_package_details'     => count( $packages ) > 1,
								'show_shipping_calculator' => $is_cart && apply_filters( 'woocommerce_shipping_show_shipping_calculator', $first, $i, $package ),
								'package_details'          => implode( ', ', $product_names ),
								/* translators: %d: shipping package number */
								'package_name'             => apply_filters( 'woocommerce_shipping_package_name', ( ( $i + 1 ) > 1 ) ? sprintf( _x( 'Shipping %d', 'shipping packages', 'alpha' ), ( $i + 1 ) ) : _x( 'Shipping', 'shipping packages', 'alpha' ), $i, $package ),
								'index'                    => $i,
								'chosen_method'            => $chosen_method,
								'formatted_destination'    => WC()->countries->get_formatted_address( $package['destination'], ', ' ),
								'has_calculated_shipping'  => WC()->customer->has_calculated_shipping(),
							)
						);

						$first = false;
					}
				} else {
					wc_cart_totals_shipping_html();
				}
				?>

				<?php do_action( 'woocommerce_cart_totals_after_shipping' ); ?>


			<?php elseif ( WC()->cart->needs_shipping() && 'yes' == get_option( 'woocommerce_enable_shipping_calc' ) && ! empty( $show_shipping_calculator ) ) : ?>

				<tr class="shipping">
					<th><?php esc_html_e( 'Shipping', 'alpha' ); ?></th>
					<td data-title="<?php esc_attr_e( 'Shipping', 'alpha' ); ?>"><?php woocommerce_shipping_calculator(); ?></td>
				</tr>

			<?php endif; ?>

			<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
				<tr class="fee">
					<th><?php echo esc_html( $fee->name ); ?></th>
					<td data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></td>
				</tr>
			<?php endforeach; ?>

			<?php
			if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
				$taxable_address = WC()->customer->get_taxable_address();
				$estimated_text  = '';

				if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
					/* translators: %s location. */
					$estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'alpha' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
				}

				if ( 'itemized' == get_option( 'woocommerce_tax_total_display' ) ) {
					foreach ( WC()->cart->get_tax_totals() as $code => $tax ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited
						?>
						<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
							<th><?php echo esc_html( $tax->label ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
							<td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo alpha_strip_script_tags( $tax->formatted_amount ); ?></td>
						</tr>
						<?php
					}
				} else {
					?>
					<tr class="tax-total">
						<th><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
						<td data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
					</tr>
					<?php
				}
			}
			?>

			<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

			<tr class="order-total">
				<th><?php esc_html_e( 'Total', 'alpha' ); ?></th>
				<td data-title="<?php esc_attr_e( 'Total', 'alpha' ); ?>"><?php wc_cart_totals_order_total_html(); ?></td>
			</tr>

			<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

		</table>

	<div class="wc-proceed-to-checkout">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
