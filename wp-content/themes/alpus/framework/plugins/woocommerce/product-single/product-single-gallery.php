<?php
/**
 * Alpha WooCommerce Gallery Single Product Functions
 *
 * Functions used to display Gallery single product type.
 *
 * @author     D-THEMES
 * @package    WP Alpha
 * @subpackage Theme
 * @since      1.0
 */

add_action( 'alpha_woocommerce_product_images', 'alpha_sp_gallery_images' );
add_filter( 'alpha_single_product_summary_class', 'alpha_sp_gallery_summary_extend_class' );


/**
 * sp_gallery_images
 *
 * Render single product gallery images.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_sp_gallery_images' ) ) {
	function alpha_sp_gallery_images() {
		if ( 'gallery' == alpha_get_single_product_layout() ) {
			global $product;
			global $alpha_layout;

			$post_thumbnail_id = $product->get_image_id();
			$attachment_ids    = $product->get_gallery_image_ids();

			if ( $post_thumbnail_id ) {
				$html = apply_filters( 'woocommerce_single_product_image_thumbnail_html', alpha_wc_get_gallery_image_html( $post_thumbnail_id, true, true ), $post_thumbnail_id );
			} else {
				$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
				$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image">', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'alpha' ) );
				$html .= '</div>';
			}

			if ( $attachment_ids && $post_thumbnail_id ) {
				foreach ( $attachment_ids as $attachment_id ) {
					$html .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', alpha_wc_get_gallery_image_html( $attachment_id, true ), $attachment_id );
				}
			}
			/**
			 * Filters the class(attr) of single product gallery.
			 *
			 * @since 1.0
			 */
			$html = '<div class="product-gallery-carousel slider-wrapper' . apply_filters( 'alpha_single_product_gallery_class', ' row cols-1 cols-md-2 cols-lg-3' )
				. '" data-slider-status="slider-same-height slider-nav-inner slider-nav-fade"' . apply_filters( 'alpha_single_product_gallery_attr', '' ) . '>' . $html . '</div>';
			echo alpha_escaped( $html );
		}
	}
}

/**
 * sp_gallery_summary_extend_class
 *
 * Render single product gallery images.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_sp_gallery_summary_extend_class' ) ) {
	function alpha_sp_gallery_summary_extend_class( $class ) {
		if ( 'gallery' == alpha_get_single_product_layout() ) {
			$class .= ' row';
		}
		return $class;
	}
}
