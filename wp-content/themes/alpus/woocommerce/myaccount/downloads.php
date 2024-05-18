<?php
/**
 * Downloads
 *
 * Shows downloads on the account page.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/downloads.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.2.0
 */

defined( 'ABSPATH' ) || die;

$downloads     = WC()->customer->get_downloadable_products();
$has_downloads = (bool) $downloads;

do_action( 'woocommerce_before_account_downloads', $has_downloads ); ?>


<?php if ( $has_downloads ) : ?>

	<?php do_action( 'woocommerce_before_available_downloads' ); ?>

	<?php do_action( 'woocommerce_available_downloads', $downloads ); ?>

	<?php do_action( 'woocommerce_after_available_downloads' ); ?>

<?php else : ?>
	<div class="woocommerce-Message woocommerce-Message--info woocommerce-info">
		<div class="d-flex align-items-center flex-wrap">
			<div class="icon-box icon-box-side woocommerce-MyAccount-content-caption me-2">
				<span class="icon-box-icon">
					<i class="far fa-check-circle"></i>
				</span>
				<div class="icon-box-content">
					<h4 class="icon-box-title text-normal"><?php echo esc_html_e( 'Browse Products : ', 'alpha' ); ?></h4>
				</div>
			</div>
			<p class="mb-0"><?php esc_html_e( 'No downloads available yet.', 'alpha' ); ?></p>
		</div>
		<a class="woocommerce-Button btn btn-dark btn-rounded btn-icon-right" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
			<?php esc_html_e( 'Go To Shop', 'alpha' ); ?>
		</a>
	</div>
<?php endif; ?>

<?php do_action( 'woocommerce_after_account_downloads', $has_downloads ); ?>
