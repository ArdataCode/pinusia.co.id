<?php
/**
 * My Account Dashboard
 *
 * Shows the first intro screen on the account dashboard.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/dashboard.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$allowed_html = array(
	'a' => array(
		'href' => array(),
	),
);

$woo      = defined( 'WOOCOMMERCE_VERSION' );
$wishlist = defined( 'YITH_WCWL' );

$account_arr = array(
	'orders'       => array( 'Orders', 'orders' ),
	'downloads'    => array( 'downloads', 'downloads' ),
	'edit-address' => array( 'addresses', 'edit-address' ),
	'edit-account' => array( 'Account details', 'edit-account' ),
	'wishlist'     => array( 'Wishlist', 'wishlist' ),
);

/**
 * Filters the items of account dashboard.
 *
 * @since 1.0
 */
$account_arr = apply_filters( 'alpha_account_dashboard_items', $account_arr );

if ( ! isset( $account_arr['vendor_dashboard'] ) ) { // if vendor plugin is not activated yet
	$account_arr['customer-logout'] = array( 'Logout', 'customer-logout' );
}

?>

<div class="myaccount-content">
	<p class="greeting mb-0">
		<?php
		printf(
			/* translators: 1: user display name 2: logout url */
			wp_kses( __( 'Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'alpha' ), $allowed_html ),
			'<strong>' . esc_html( $current_user->display_name ) . '</strong>',
			esc_url( wc_logout_url() )
		);
		?>
	</p>

	<p class="mb-4 mb-md-8">
		<?php
		/* translators: 1: Orders URL 2: Address URL 3: Account URL. */
		$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">billing address</a>, and <a href="%3$s">edit your password and account details</a>.', 'alpha' );
		if ( wc_shipping_enabled() ) {
			/* translators: 1: Orders URL 2: Addresses URL 3: Account URL. */
			$dashboard_desc = __( 'From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">shipping and billing addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'alpha' );
		}
		printf(
			wp_kses( $dashboard_desc, $allowed_html ),
			esc_url( wc_get_endpoint_url( 'orders' ) ),
			esc_url( wc_get_endpoint_url( 'edit-address' ) ),
			esc_url( wc_get_endpoint_url( 'edit-account' ) )
		);
		?>
	</p>

	<div class="icon-boxes-wrapper">
		<div class="row gutter-lg">
			<?php foreach ( $account_arr as $key => $value ) : ?>
				<div class="col-md-4 col-sm-6 col-12 icon-box-wrap">
					<?php
					if ( 'wishlist' == $key ) {
						if ( $wishlist & $woo ) {
							$url = YITH_WCWL()->get_wishlist_url();
						} else {
							$url = get_home_url();
						}
					} elseif ( 'request_support' == $key ) {
						$url = $value[1];
					} else {
						$url = wc_get_account_endpoint_url( $value[1] );
					}
					?>
					<div class="icon-box text-center">
						<a href="<?php echo esc_url( $url ); ?>">
							<span class="icon-box-icon mb-2 icon-<?php echo esc_attr( strtolower( $value[0] ) ); ?>"></span>
							<p class="text-capitalize text-center mb-0"><?php echo esc_html( $value[0] ); ?></p>
						</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>

	<?php
		/**
		 * My Account dashboard.
		 *
		 * @since 2.6.0
		 */
		do_action( 'woocommerce_account_dashboard' );

		/**
		 * Deprecated woocommerce_before_my_account action.
		 *
		 * @deprecated 2.6.0
		 */
		do_action( 'woocommerce_before_my_account' );

		/**
		 * Deprecated woocommerce_after_my_account action.
		 *
		 * @deprecated 2.6.0
		 */
		do_action( 'woocommerce_after_my_account' );
	?>
</div>

