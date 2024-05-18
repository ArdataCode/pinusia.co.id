<?php
/**
 * Order Customer Details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details-customer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.4
 */

defined( 'ABSPATH' ) || die;

$show_shipping = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>
<section class="woocommerce-customer-details">

	<?php if ( $show_shipping ) : ?>

	<section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses row addresses">
		<div class="col-md-6">
			<div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address">

	<?php endif; ?>

	<h2 class="woocommerce-column__title"><?php esc_html_e( 'Billing address', 'alpha' ); ?></h2>

	<address>
		<?php echo alpha_strip_script_tags( $order->get_formatted_billing_address( esc_html__( 'N/A', 'alpha' ) ) ); ?>

		<?php if ( $order->get_billing_phone() ) : ?>
			<p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
		<?php endif; ?>

		<?php if ( $order->get_billing_email() ) : ?>
			<p class="woocommerce-customer-details--email"><?php echo esc_html( $order->get_billing_email() ); ?></p>
		<?php endif; ?>
	</address>

	<?php if ( $show_shipping ) : ?>

			</div>
		</div>

		<div class="col-md-6">
			<div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address">
				<h2 class="woocommerce-column__title"><?php esc_html_e( 'Shipping address', 'alpha' ); ?></h2>
				<address>
					<?php echo alpha_strip_script_tags( $order->get_formatted_shipping_address( esc_html__( 'N/A', 'alpha' ) ) ); ?>
				</address>
			</div>
		</div>

	</section>

	<?php endif; ?>

	<?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>

	<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="woocommerce-button wc-action-btn back-to-list btn btn-md btn-dark btn-rounded btn-icon-left"><i class="<?php echo ALPHA_ICON_PREFIX; ?>-icon-long-arrow-left"></i> <?php esc_html_e( 'Back To List', 'alpha' ); ?></a>

</section>
