<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 2.6.0
 */

defined( 'ABSPATH' ) || die;

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation col-md-3">
	<ul>
		<?php
		foreach ( wc_get_account_menu_items() as $endpoint => $label ) :
			if ( 'wishlist' == $endpoint ) {
				$url = defined( 'YITH_WCWL' ) ? YITH_WCWL()->get_wishlist_url() : get_home_url();
			} elseif ( 'vendor_dashboard' == $endpoint ) {
				/**
				 * Filters the url to link account dashboard.
				 *
				 * @since 1.0
				 */
				$url = apply_filters( 'alpha_account_dashboard_link', '' );
			} else {
				$url = wc_get_account_endpoint_url( $endpoint );
			}
			if ( ! $url ) {
				continue;
			}
			?>
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $label ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
