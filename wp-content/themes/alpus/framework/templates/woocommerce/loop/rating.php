<?php
/**
 * Loop Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/rating.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || die;

if (
	! wc_review_ratings_enabled() ||
	(
		! isset( $is_hide_details ) &&
		'popup' == alpha_wc_get_loop_prop( 'classic_hover' ) &&
		'list' !== alpha_wc_get_loop_prop( 'product_type' )
	)
) {
	return;
}

global $product;
?>
<div class="woocommerce-product-rating">
	<?php
	/**
	 * Filters the rating number of single product.
	 *
	 * @since 1.0
	 */
	if ( apply_filters( 'alpha_single_product_rating_show_number', false ) ) {
		echo esc_html( $product->get_average_rating() );
	} else {
		echo wc_get_rating_html( $product->get_average_rating() );
	}

	/**
	 * Filters the review of single product.
	 */
	if ( apply_filters( 'alpha_single_product_show_review', comments_open() && 'widget' != alpha_wc_get_loop_prop( 'product_type' ) ) ) {
		echo alpha_get_rating_link_html( $product );
	}
	?>
</div>
