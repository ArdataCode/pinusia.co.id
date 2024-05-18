<?php
/**
 * Alpha WooCommerce Sticky Thumbs Single Product Functions
 *
 * Functions used to display sticky thumbs single product type.
 *
 * @author     D-THEMES
 * @package    WP Alpha
 * @subpackage Theme
 * @since      1.0
 */

add_action( 'alpha_woocommerce_product_images', 'alpha_sp_sticky_thumbs_images' );
add_action( 'woocommerce_product_thumbnails', 'alpha_wc_show_sp_sticky_thumbs_thumbnails', 20 );

/**
 * sp_sticky_thumbs_images
 *
 * Render sticky thumbs single product images.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_sp_sticky_thumbs_images' ) ) {
	function alpha_sp_sticky_thumbs_images() {
		if ( 'sticky-thumbs' == alpha_get_single_product_layout() ) {
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
			$html = '<div class="product-sticky-images' . apply_filters( 'alpha_single_product_thumbs_wrap_class', 'gutter-md' ) . '">' . $html . '</div>';
			echo alpha_escaped( $html );
		}
	}
}


/**
 * wc_show_sp_sticky_thumbs_thumbnails
 *
 * Render single product sticky thumbnails.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_show_sp_sticky_thumbs_thumbnails' ) ) {
	function alpha_wc_show_sp_sticky_thumbs_thumbnails() {
		if ( 'sticky-thumbs' == alpha_get_single_product_layout() ) {
			wp_enqueue_script( 'alpha-sticky-lib' );
			?>
			<div class="product-sticky-thumbs<?php echo apply_filters( 'alpha_single_product_thumbs_wrap_class', 'gutter-md' ); ?>">
				<div class="product-sticky-thumbs-inner sticky-sidebar" data-sticky-options="{'minWidth': 319}">
					<?php woocommerce_show_product_thumbnails(); ?>
				</div>
			</div>
			<?php
		}
	}
}
