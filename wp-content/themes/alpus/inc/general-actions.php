<?php
/**
 * Theme Actions & Filters
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */

// Social Icons
add_filter( 'alpha_social_links', 'alpha_social_links' );

// Posts
add_filter( 'navigation_markup_template', 'alpha_pager_posts' );

// Change Account Orders Column
add_filter( 'woocommerce_account_orders_columns', 'alpha_change_orders_column' );

// Post Loop Default Args
add_filter( 'alpha_post_loop_default_args', 'alpha_post_loop_args' );

// Woocommerce Shop
add_filter( 'alpha_layout_default_args', 'alpha_get_layout_default_args', 20, 2 );

if ( ! function_exists( 'alpha_social_links' ) ) {
	function alpha_social_links( $social_links ) {
		$social_links['pinterest']['icon'] = 'fab fa-pinterest';
		return $social_links;
	}
}

if ( ! function_exists( 'alpha_add_body_class' ) ) {
	/**
	 * Add classes to body
	 *
	 * @since 1.0
	 *
	 * @param array[string] $classes
	 *
	 * @return array[string] $classes
	 */
	function alpha_add_body_class( $classes ) {
		global $alpha_layout;

		// Site Layout
		if ( 'full' != alpha_get_option( 'site_type' ) ) { // Boxed or Framed
			$classes[] = 'site-boxed';
		}

		// Page Type
		$layout = alpha_get_page_layout();
		if ( false !== strpos( $layout, 'archive_' . ALPHA_NAME . '_portfolio' ) || false !== strpos( $layout, 'archive_' . ALPHA_NAME . '_member' ) ) {
			$classes[] = 'alpha-archive-post-layout';
		}
		$classes[] = 'alpha-' . str_replace( '_', '-', $layout ) . '-layout';

		// Disable Mobile Slider
		if ( alpha_get_option( 'mobile_disable_slider' ) ) {
			$classes[] = 'alpha-disable-mobile-slider';
		}

		// Disable Mobile Animation
		if ( alpha_get_option( 'mobile_disable_animation' ) ) {
			$classes[] = 'alpha-disable-mobile-animation';
		}

		if ( is_customize_preview() ) {
			$classes[] = 'alpha-disable-animation';
		}

		// Add single-product-page or shop-page to body class
		if ( alpha_is_product() ) {
			$classes[] = 'single-product-page';
		} elseif ( alpha_is_shop() ) {
			$classes[] = 'product-archive-page';
		}

		// @start feature: fs_plugin_woocommerce
		if ( class_exists( 'WooCommerce' ) && wc_get_page_id( 'compare' ) == get_the_ID() ) {
			$classes[] = 'compare-page';
		}
		// @end feature: fs_plugin_woocommerce

		// Category Filter
		if ( is_archive() && 'post' == get_post_type() && alpha_get_option( 'posts_filter' ) ) {
			$classes[] = 'breadcrumb-divider-active';
		}

		// Rounded Skin
		// $classes[] = 'alpha-rounded-skin';

		if ( is_admin_bar_showing() ) {
			$classes[] = 'alpha-adminbar';
		}
		if ( defined( 'ALPHA_FRAMEWORK_VENDORS' ) ) {
			$classes[] = 'alpha-use-vendor-plugin';
		}
		return $classes;
	}
}

if ( ! function_exists( 'alpha_pager_posts' ) ) {
	function alpha_pager_posts() {

		$post_type = get_post_type();

		$post_type_object = get_post_type_object( $post_type );

		$template = '
		<nav class="navigation %1$s" aria-label="%4$s">
			<h2 class="screen-reader-text">%2$s</h2>
			<div class="nav-links">%3$s</div>' .
		'<a class="post-nav-blog ' . ALPHA_ICON_PREFIX . '-icon-grid-empty" href="' . esc_url( get_post_type_archive_link( $post_type ) ) . '" title="' . $post_type_object->labels->all_items . '"></a>' .
		'</nav>';

		return $template;
	}
}

	/**
	 * Change Account Orders Column
	 *
	 * @since 1.0
	 */

if ( ! function_exists( 'alpha_change_orders_column' ) ) {
	function alpha_change_orders_column( $columns ) {
		$columns['order-actions'] = 'Action';

		return $columns;
	}
}

/**
 * Get layout theme options map
 *
 * @since 1.0
 */
function alpha_get_layout_default_args( $res, $layout_name ) {

	if ( 'archive_product' == $layout_name ) {
		$res = array(
			'products_column' => 3,
			'products_gap'    => 'lg',
		);
	} elseif ( 'single_product' == $layout_name ) {
		$res = array(
			'single_product_type'          => 'horizontal',
			'single_product_sticky'        => true,
			'single_product_sticky_mobile' => true,
			'products_load'                => '',
			'product_data_type'            => 'tab',
			'products_gap'                 => 'lg',
		);
	} elseif ( 'archive_' == substr( $layout_name, 0, 8 ) ) {
		$res = array(
			'posts_filter' => false,
			'posts_column' => 1,
		);
	} elseif ( 'single_' == substr( $layout_name, 0, 7 ) ) {
		$res = array(
			'single_image_size' => 'full', // for single post featured image.
		);
	}
	/**
	 * Filters layout theme option map.
	 *
	 * @since 1.0
	 */
	return $res;
}

/**
 * Set default post args
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_post_loop_args' ) ) {
	function alpha_post_loop_args( $args ) {
		if ( empty( $args ) ) {
			$args = array();
		}
		$args = array_merge(
			$args,
			array(
				'excerpt_length' => 43,
				'overlay'        => '',
			)
		);
		return $args;
	}
}
