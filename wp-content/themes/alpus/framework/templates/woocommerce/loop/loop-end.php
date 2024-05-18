<?php
/**
 * Product Loop End
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-end.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     2.0.0
 */

defined( 'ABSPATH' ) || die;

global $alpha_layout;

if ( isset( $GLOBALS['alpha_current_product_id'] ) ) {

	// Print single product in products
	$sp_insert     = alpha_wc_get_loop_prop( 'sp_insert' );
	$banner_insert = alpha_wc_get_loop_prop( 'banner_insert' );
	$current_id    = $GLOBALS['alpha_current_product_id'];
	$repeater_ids  = alpha_wc_get_loop_prop( 'repeater_ids' );

	// Print single product in products
	if ( 'last' == $sp_insert || ( (int) $sp_insert >= $current_id ) ) { // at last or after max
		$html = alpha_wc_get_loop_prop( 'single_in_products', '' );
		if ( $html ) {
			$wrap_class = 'product-wrap product-single-wrap';
			if ( isset( $repeater_ids[ $current_id + 1 ] ) ) {
				$wrap_class .= ' ' . $repeater_ids[ $current_id + 1 ];
			}

			echo '<li class="' . esc_attr( $wrap_class ) . '">' . alpha_escaped( $html ) . '</li>';

			wc_set_loop_prop( 'single_in_products', '' );
		}
	}

	// Print banner in products
	if ( 'last' == $banner_insert || ( (int) $sp_insert >= $current_id ) ) { // at last or after max
		$html = alpha_wc_get_loop_prop( 'product_banner', '' );
		if ( $html ) {
			wc_set_loop_prop( 'product_banner', '' );
			echo alpha_escaped( $html );
		}
	}

	// Close multiple slider
	$row_cnt = alpha_wc_get_loop_prop( 'row_cnt' );
	if ( $row_cnt && 1 != $row_cnt ) {
		if ( 0 != $current_id % $row_cnt ) {
			echo '</ul></li>';
		}
	}
}

echo '</ul>';

// Load More
$loadmore_type      = alpha_wc_get_loop_prop( 'loadmore_type' );
$loadmore_btn_style = alpha_wc_get_loop_prop( 'loadmore_btn_style' );

if ( $loadmore_type ) {
	$page        = absint( empty( $_GET['product-page'] ) ? alpha_wc_get_loop_prop( 'current_page', 1 ) : $_GET['product-page'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$total_pages = alpha_wc_get_loop_prop( 'total_pages' );

	if ( $total_pages > 1 ) {
		if ( 'page' === $loadmore_type ) {
			if ( alpha_wc_get_loop_prop( 'widget', false ) && empty( $alpha_layout['is_shop_builder_rendering'] ) ) {
				echo alpha_get_pagination_html( $page, $total_pages, 'pagination-load' );
			}
		} else {
			alpha_loadmore_html( '', $loadmore_type, alpha_wc_get_loop_prop( 'loadmore_label' ), $loadmore_btn_style );
		}
	}
}

if ( alpha_is_shop() && ! alpha_wc_get_loop_prop( 'widget' ) ) {
	echo '</div>'; // end of div.product-archive
}


/**
 * Hook: alpha_after_shop_loop_end.
 *
 * @hooked vendor_store_tab_end - 10
 */
do_action( 'alpha_after_shop_loop_end' );
