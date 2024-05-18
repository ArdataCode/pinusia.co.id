<?php
/**
 * Alpha Product Category Default Functions
 *
 * Functions used to display default type.
 *
 * @author     D-THEMES
 * @package    WP Alpha
 * @subpackage Theme
 * @since      1.0
 */

// Category Thumbnail
add_action( 'woocommerce_before_subcategory_title', 'alpha_pc_default_before_subcategory_thumbnail', 5 );
add_action( 'woocommerce_before_subcategory_title', 'alpha_pc_default_after_subcategory_thumbnail', 15 );
add_action( 'woocommerce_before_subcategory_title', 'alpha_pc_default_subcategory_thumbnail' );

// Category Content
add_action( 'woocommerce_after_subcategory_title', 'alpha_pc_default_after_subcategory_title' );
add_action( 'woocommerce_shop_loop_subcategory_title', 'alpha_pc_default_template_loop_category_title' );

/**
 * pc_default_before_subcategory_thumbnail
 *
 * Render html after subcategory thumbnail.
 *
 * @param string $category
 * @since 1.0
 */
if ( ! function_exists( 'alpha_pc_default_before_subcategory_thumbnail' ) ) {
	function alpha_pc_default_before_subcategory_thumbnail( $category ) {
		echo '<figure>';
		echo '<a href="' . esc_url( get_term_link( $category, 'product_cat' ) ) . '">';
	}
}

/**
 * pc_default_subcategory_thumbnail
 *
 * Render subcategory thumbnail.
 *
 * @param string $category
 * @since 1.0
 */
if ( ! function_exists( 'alpha_pc_default_subcategory_thumbnail' ) ) {
	function alpha_pc_default_subcategory_thumbnail( $category ) {
		if ( alpha_wc_get_loop_prop( 'show_icon', false ) ) {
			$icon_class = get_term_meta( $category->term_id, 'product_cat_icon', true );
			$icon_class = $icon_class ? $icon_class : 'far fa-heart';
			echo '<i class="' . $icon_class . '"></i>';
		} else {

			$html           = '';
			$thumbnail_size = apply_filters( 'subcategory_archive_thumbnail_size', 'woocommerce_thumbnail' );
			$thumbnail_id   = get_term_meta( $category->term_id, 'thumbnail_id', true );
			$dimensions     = false;

			if ( ! in_array( str_replace( 'woocommerce_', '', $thumbnail_size ), array( 'shop_single', 'single', 'shop_catalog', 'thumbnail', 'shop_thumbnail', 'gallery_thumbnail' ) ) ) {
				if ( 'full' == $thumbnail_size ) {
					$dimensions = wp_get_attachment_metadata( $thumbnail_id );
				} else {
					$dimensions = image_get_intermediate_size( $thumbnail_id, $thumbnail_size );
				}
			}
			if ( ! $dimensions ) {
				$dimensions = wc_get_image_size( $thumbnail_size );
			}

			if ( $thumbnail_id ) {
				if ( isset( $dimensions['url'] ) && $dimensions['url'] ) {
					$image = $dimensions['url'];
				} else {
					$image = isset( wp_get_attachment_image_src( $thumbnail_id, $thumbnail_size )[0] ) ? wp_get_attachment_image_src( $thumbnail_id, $thumbnail_size )[0] : '';
				}
				$image_srcset = wp_get_attachment_image_srcset( $thumbnail_id, $thumbnail_size );
				$image_meta   = wp_get_attachment_metadata( $thumbnail_id );
				$image_sizes  = wp_get_attachment_image_sizes( $thumbnail_id, $thumbnail_size, $image_meta );

				if ( 0 == $dimensions['height'] ) {
					$full_image_size = wp_get_attachment_image_src( $thumbnail_id, 'full' );
					if ( isset( $full_image_size[1] ) && $full_image_size[1] ) {
						$dimensions['height'] = intval( $dimensions['width'] / absint( $full_image_size[1] ) * absint( $full_image_size[2] ) );
					}
				}

				// If image's width is smaller than thumbnail size, use real image's size.
				if ( is_array( $dimensions ) && is_array( $image_meta ) && $dimensions['width'] > $image_meta['width'] ) {
					$dimensions['width']  = $image_meta['width'];
					$dimensions['height'] = $image_meta['height'];
				}
			} else {
				$image        = wc_placeholder_img_src();
				$image_srcset = false;
				$image_sizes  = false;
			}

			if ( $image ) {
				// Prevent esc_url from breaking spaces in urls for image embeds.
				// Ref: https://core.trac.wordpress.org/ticket/23605.
				$image = str_replace( ' ', '%20', $image );

				// Add responsive image markup if available.
				if ( $image_srcset && $image_sizes ) {
					$html = '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" srcset="' . esc_attr( $image_srcset ) . '" sizes="' . esc_attr( $image_sizes ) . '" />';
				} else {
					$html = '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />';
				}
			}

			/**
			 * Filters the html of subcategory thumbnail.
			 *
			 * @since 1.0
			 */
			echo apply_filters( 'alpha_wc_subcategory_thumbnail_html', $html );
		}
	}
}

/**
 * pc_default_after_subcategory_thumbnail
 *
 * Render html after subcategory thumbnail.
 *
 * @param string $category
 * @since 1.0
 */
if ( ! function_exists( 'alpha_pc_default_after_subcategory_thumbnail' ) ) {
	function alpha_pc_default_after_subcategory_thumbnail( $category ) {
		$content_origin = alpha_wc_get_loop_prop( 'content_origin' );
		echo '</a>';
		echo '</figure>';
		if ( $content_origin ) {
			echo '<div class="category-content ' . $content_origin . '">';
		} else {
			echo '<div class="category-content">';
		}
	}
}

/**
 * pc_default_template_loop_category_title
 *
 * Render product category title.
 *
 * @param array $category
 * @since 1.0
 */
if ( ! function_exists( 'alpha_pc_default_template_loop_category_title' ) ) {
	function alpha_pc_default_template_loop_category_title( $category ) {

		// Title
		echo '<h3 class="woocommerce-loop-category__title">';
			echo '<a href="' . esc_url( get_term_link( $category, 'product_cat' ) ) . '">';
			echo esc_html( $category->name );
			echo '</a>';
		echo '</h3>';

		// Count
		if ( alpha_wc_get_loop_prop( 'show_count', true ) ) {
			echo apply_filters( 'woocommerce_subcategory_count_html', '<mark>' . esc_html( $category->count ) . ' ' . esc_html__( 'Products', 'alpha' ) . '</mark>', $category );
		}
		// Link
		if ( alpha_wc_get_loop_prop( 'show_link', true ) ) {
			$link_text  = alpha_wc_get_loop_prop( 'link_text' );
			$link_class = 'btn btn-underline btn-link';
			echo '<a class="' . esc_html( $link_class ) . '"' .
			' href="' . esc_url( get_term_link( $category, 'product_cat' ) ) . '">' .
			( $link_text ? esc_html( $link_text ) : esc_html__( 'Shop Now', 'alpha' ) ) .
			'</a>';
		}
	}
}

/**
 * pc_default_after_subcategory_title
 *
 * Render html after subcategory title.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_pc_default_after_subcategory_title' ) ) {
	function alpha_pc_default_after_subcategory_title() {
		echo '</div>';
	}
}
