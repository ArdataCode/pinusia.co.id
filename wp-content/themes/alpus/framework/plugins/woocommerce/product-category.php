<?php
/**
 * Alpha WooCommerce Product Category Functions
 *
 * Functions used to display product category.
 *
 * @author     D-THEMES
 * @package    WP Alpha
 * @subpackage Theme
 * @since      1.0
 */

defined( 'ABSPATH' ) || die;

remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open' );
remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close' );

// Category Thumbnail
add_filter( 'subcategory_archive_thumbnail_size', 'alpha_wc_category_thumbnail_size' );
remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail' );

// Category Content
remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title' );

/**
 * Alpha Category Thumbnail Functions
 */
/**
 * wc_category_thumbnail_size
 *
 * Get category thumbnail size.
 *
 * @param string $size
 * @return string
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_category_thumbnail_size' ) ) {
	function alpha_wc_category_thumbnail_size( $size ) {
		$size = alpha_wc_get_loop_prop( 'thumbnail_size', $size );
		if ( 'custom' == $size ) {
			return alpha_wc_get_loop_prop( 'thumbnail_custom_size', 'woocommerce_thumbnail' );
		}
		return $size;
	}
}

/**
 * wc_category_show_info
 *
 * Get cateogry show info from category type.
 *
 * @param string $type
 * @return array
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_category_show_info' ) ) {
	function alpha_wc_category_show_info( $type = 'default' ) {
		$cat_options = apply_filters(
			'alpha_wc_category_show_infos',
			array(
				'default' => array(
					'link'  => '',
					'count' => '',
				),
			)
		);
		return $cat_options[ $type ];
	}
}

/**
 * get_category_classes
 *
 * Get category classes from category type.
 *
 * @return array
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_category_classes' ) ) {
	function alpha_get_category_classes() {

		$category_type  = 'default';
		$category_class = 'cat-type-default cat-type-absolute';

		/**
		 * Filters the category classes.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'alpha_get_category_classes', $category_class, $category_type );
	}
}

/* Product Category Types */
foreach ( apply_filters(
	'alpha_pc_types',
	array(
		'default' => true,
	),
	'hooks'
) as $key => $value ) {
	if ( $key && $value ) {
		require_once alpha_framework_path( ALPHA_FRAMEWORK_PLUGINS . "/woocommerce/product-category/product-category-{$key}.php" );
	}
}

/**
 * Fires after setting product category actions and filters.
 *
 * Here you can remove and add more actions and filters.
 *
 * @since 1.0
 */
do_action( 'alpha_after_pc_hooks' );
