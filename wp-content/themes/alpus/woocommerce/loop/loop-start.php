<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     3.3.0
 */

defined( 'ABSPATH' ) || die;

/**
 * Enqueue styles and scripts for woocommerce.
 *
 * @since 1.0
 */

global $alpha_layout;
$col_cnt = array();
// Set product type as theme option
if ( ! alpha_wc_get_loop_prop( 'widget' ) ) {
	alpha_wc_set_loop_prop();
}

if ( ! wc_get_loop_prop( 'col_cnt' ) ) {
	$col_cnt = apply_filters( 'woocommerce_catalog_columns', get_option( 'woocommerce_catalog_columns', ! empty( $alpha_layout['products_column'] ) ? $alpha_layout['products_column'] : 4 ) );
	$col_cnt = alpha_get_responsive_cols(
		array(
			'xlg' => $col_cnt,
			'xl'  => 3,
			'lg'  => 2,
		)
	);
	wc_set_loop_prop( 'col_cnt', $col_cnt );
}

$col_cnt = wc_get_loop_prop( 'col_cnt' );

$wrapper_class   = alpha_wc_get_loop_prop( 'wrapper_class', array() );
$wrapper_attrs   = alpha_wc_get_loop_prop( 'wrapper_attrs', '' );
$wrapper_class[] = 'products';

// Add classes to wrapper class
$gap = $alpha_layout['products_gap'];
if ( $gap ) {
	$wrapper_class[] = 'gutter-' . $gap;
}

/**
 * Filters the show info for alpha shop
 *
 * @since 1.0
 */
if ( alpha_is_shop() ) {

	/**
	 * Product Archive (Shop)
	 */
	$wrapper_attrs .= ' data-col="' . esc_attr( alpha_get_col_class( $col_cnt ) ) . '"';

	wc_set_loop_prop( 'loadmore_type', 'page' );
	wc_set_loop_prop( 'loadmore_label', esc_html__( 'Load More', 'alpha' ) );
	wc_set_loop_prop( 'loadmore_args', array( 'shop' => true ) );

	echo '<div class="product-archive">';
}

if ( alpha_wc_get_loop_prop( 'linked_products', false ) ) {
	/**
	 * Related, Up-Sell, Cross-Sell Products
	 *
	 * @since 1.0
	 */
	$col_cnt         = alpha_get_responsive_cols( array( 'lg' => 4 ) );
	$wrapper_class[] = alpha_get_slider_class();
	$slider_attrs    = alpha_get_slider_attrs(
		array(
			'show_dots'    => false,
			'col_sp'       => isset( $alpha_layout['products_gap'] ) ? $alpha_layout['products_gap'] : '',
			'status_class' => 'slider-shadow',
		),
		$col_cnt
	);
	if ( empty( $slider_attrs['breakpoints'][0] ) ) {
		$slider_attrs['breakpoints'][0] = array( 'spaceBetween' => 20 );
	} else {
		$slider_attrs['breakpoints'][0]['spaceBetween'] = 20;
	}
	$wrapper_attrs .= ' data-slider-options="' . esc_attr(
		json_encode( $slider_attrs )
	) . '"';
}

if (  function_exists( 'alpha_get_category_classes' ) && function_exists( 'alpha_wc_category_show_info' ) ) {
	$category_class = array( alpha_get_category_classes() );
	$show_info      = alpha_wc_category_show_info( alpha_wc_get_loop_prop( 'category_type' ) );
	wc_set_loop_prop( 'show_link', 'yes' == $show_info['link'] );
	wc_set_loop_prop( 'show_count', 'yes' == $show_info['count'] );
	wc_set_loop_prop( 'category_class', $category_class );
}


// If loadmore or ajax category filter, add only pages count.
if ( alpha_wc_get_loop_prop( 'alpha_ajax_load' ) ) {
	$wrapper_attrs .= ' data-load-max="' . alpha_wc_get_loop_prop( 'total_pages' ) . '"';
} else {

	// Load more
	$loadmore_type = alpha_wc_get_loop_prop( 'loadmore_type' );

	if ( $loadmore_type ) {
		$wrapper_attrs .= ' ' . alpha_loadmore_attributes(
			'product',
			alpha_wc_get_loop_prop( 'loadmore_props' ), // Props
			alpha_wc_get_loop_prop( 'loadmore_args' ),  // Args
			$loadmore_type,                             // Type
			alpha_wc_get_loop_prop( 'total_pages' ),    // Total Pages
		);
	}
}

$wrapper_class[] = alpha_get_col_class( $col_cnt );

/**
 * Filters the classes of product loop wrapper.
 *
 * @since 1.0
 */
$wrapper_class = apply_filters( 'alpha_product_loop_wrapper_classes', $wrapper_class );
/**
 * Hook: alpha_before_shop_loop_start.
 *
 * @hooked alpha_before_shop_loop_start - 10
 */
do_action( 'alpha_before_shop_loop_start' );

echo '<ul class="' . esc_attr( implode( ' ', $wrapper_class ) ) . '"' . alpha_escaped( $wrapper_attrs ) . '>';
