<?php
/**
 * Checkout coupon form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-coupon.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.4
 */

defined( 'ABSPATH' ) || die;

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
	return;
}

?>
<div class="woocommerce-form-coupon-toggle">
	<?php echo ( esc_html__( 'Have a coupon?', 'alpha' ) . ' <a href="#" class="showcoupon">' . esc_html__( 'Enter your code', 'alpha' ) . '</a>' ); ?>
</div>

<form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none">

	<p><?php esc_html_e( 'If you have a coupon code, please apply it below.', 'alpha' ); ?></p>

	<div class="form-row input-wrapper-inline form-coupon">
		<input type="text" name="coupon_code" class="input-text form-control" placeholder="<?php esc_attr_e( 'Coupon code', 'alpha' ); ?>" id="coupon_code" value="" />
		<button type="submit" class="btn btn-rounded" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'alpha' ); ?>"><?php esc_html_e( 'Apply coupon', 'alpha' ); ?></button>
	</div>

	<div class="clear"></div>
</form>
