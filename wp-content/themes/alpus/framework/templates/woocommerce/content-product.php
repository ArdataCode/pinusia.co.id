<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || die;

global $product;
// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$wrap_class   = 'product-wrap'; // classes for product wrap
$product_type = empty( alpha_wc_get_loop_prop( 'product_type' ) ) ? 'product-default' : 'product-' . alpha_wc_get_loop_prop( 'product_type' );

// Classes for product
$product_classes = array( 'product-loop', $product_type );
?>

<?php if ( empty( alpha_wc_get_loop_prop( 'alpha_print_content_only' ) ) ) : ?>
<li class="<?php echo esc_attr( apply_filters( 'alpha_product_wrap_class', $wrap_class ) ); ?>">
<?php endif; ?>

	<?php
	/**
	 * Fires before rendering product loop item.
	 *
	 * @since 1.0
	 */
	do_action( 'alpha_product_loop_before_item', $product_type );
	?>

	<div 
	<?php
		/**
		 * Filters the class of product.
		 *
		 * @since 1.0
		 */
		wc_product_class( apply_filters( 'alpha_product_class', $product_classes, $product_type ), $product );
	?>
		>
		<?php
		/**
		 * Hook: woocommerce_before_shop_loop_item.
		 *
		 * @hooked alpha_product_loop_figure_open - 5
		 * @hooked woocommerce_template_loop_product_link_open - 10
		 */
		do_action( 'woocommerce_before_shop_loop_item' );

		/**
		 * Hook: woocommerce_before_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_product_thumbnail - 10
		 * @hooked alpha_product_loop_hover_thumbnail - 10
		 * @hooked woocommerce_show_product_loop_sale_flash - 20
		 * @hooked alpha_product_loop_vertical_action - 20
		 * @hooked alpha_product_loop_media_action - 20
		 * @hooked alpha_product_loop_figure_close - 40
		 * @hooked alpha_product_loop_details_open - 50
		 */
		do_action( 'woocommerce_before_shop_loop_item_title' );
		/**
		 * Hook: alpha_shop_loop_item_categories.
		 *
		 * @hooked alpha_shop_loop_item_categories - 10
		 * @hooked alpha_product_loop_default_wishlist_action - 15
		 */
		do_action( 'alpha_shop_loop_item_categories' );

		/**
		 * Hook: woocommerce_shop_loop_item_title.
		 *
		 * @removed woocommerce_template_loop_product_title - 10
		 * @hooked alpha_wc_template_loop_product_title - 10
		 */
		do_action( 'woocommerce_shop_loop_item_title' );

		/**
		 * Hook: woocommerce_after_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 * @hooked alpha_get_extra_info_html - 15
		 * @hooked alpha_product_loop_action - 30
		 * @hooked alpha_product_loop_count - 40
		 */
		do_action( 'woocommerce_after_shop_loop_item_title' );

		/**
		 * Hook: woocommerce_after_shop_loop_item.
		 *
		 * @removed woocommerce_template_loop_add_to_cart - 10
		 * @hooked alpha_product_loop_details_close - 15
		 */
		do_action( 'woocommerce_after_shop_loop_item' );
		?>
	</div>
	<?php
	/**
	 * Fires after rendering product loop item.
	 *
	 * @since 1.0
	 */
	do_action( 'alpha_product_loop_after_item', $product_type );
	?>

<?php if ( empty( alpha_wc_get_loop_prop( 'alpha_print_content_only' ) ) ) : ?>
</li>
	<?php
endif;
