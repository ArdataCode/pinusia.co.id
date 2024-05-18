<?php
/**
 * Show messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/success.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.9.0
 */

defined( 'ABSPATH' ) || die;

if ( ! $notices ) {
	return;
}

?>

<?php foreach ( $notices as $notice ) : ?>
	<div class="woocommerce-message<?php echo apply_filters( 'alpha_wc_notice_class', '', $notice, 'success' ); ?>"<?php echo wc_get_notice_data_attr( $notice ); ?> role="alert">
		<?php
		/**
		 * Fires before print woocommerce notice.
		 *
		 * @since 1.0
		 */
		do_action( 'alpha_wc_before_notice', $notice, 'success' );
		echo wc_kses_notice( $notice['notice'] );
		/**
		 * Fires after print woocommerce notice.
		 *
		 * @since 1.0
		 */
		do_action( 'alpha_wc_after_notice', $notice, 'success' );
		?>
	</div>
<?php endforeach; ?>
