<?php
/**
 * Alpha WooCommerce Single Product Functions
 *
 * Functions used to display single product.
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */

defined( 'ABSPATH' ) || die;

// Compatiblilty with elementor editor
if ( ! empty( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] && is_admin() ) {
	if ( class_exists( 'WC_Template_Loader' ) ) {
		add_filter( 'woocommerce_product_tabs', array( 'WC_Template_Loader', 'unsupported_theme_remove_review_tab' ) );
		add_filter( 'woocommerce_product_tabs', 'woocommerce_default_product_tabs' );
		add_filter( 'woocommerce_product_tabs', 'woocommerce_sort_product_tabs', 99 );
	}
}

// Alpha Single Product Navigation
add_filter( 'alpha_breadcrumb_args', 'alpha_single_prev_next_product' );

// Single Product Class
add_filter( 'alpha_single_product_classes', 'alpha_single_product_extend_class' );

// Single Product - Label, Sale Countdown
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'alpha_before_wc_gallery_figure', 'woocommerce_show_product_sale_flash' );
add_action( 'woocommerce_available_variation', 'alpha_variation_add_sale_ends', 100, 3 );

// Single Product - default template
add_action( 'woocommerce_before_single_product_summary', 'alpha_single_product_wrap_first_start', 5 );
add_action( 'woocommerce_before_single_product_summary', 'alpha_single_product_wrap_first_end', 30 );
add_action( 'woocommerce_before_single_product_summary', 'alpha_single_product_wrap_second_start', 30 );
add_action( 'alpha_after_product_summary_wrap', 'alpha_single_product_wrap_second_end', 20 );

// Remove default rendering of wishlist button by YITH
// Change default yith wishlist button position hooks
if ( class_exists( 'YITH_WCWL' ) || class_exists( 'YITH_WCWL_Frontend' ) ) {
	add_filter( 'yith_wcwl_show_add_to_wishlist', 'alpha_print_wishlist_button', 20 );
	add_filter( 'yith_wcwl_positions', 'alpha_yith_wcwl_positions' );
}

// Single Product Media
add_filter( 'alpha_wc_thumbnail_image_size', 'alpha_single_product_thumbnail_image_size' );
add_action( 'alpha_woocommerce_product_images', 'alpha_single_product_images' );
add_filter( 'alpha_single_product_gallery_main_classes', 'alpha_single_product_gallery_classes' );
remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
add_filter( 'alpha_single_product_gallery_attr', 'alpha_single_product_gallery_slider_attrs' );
add_filter( 'alpha_single_product_thumbs_class', 'alpha_single_product_thumbs_slider_classes' );
add_filter( 'alpha_single_product_thumbs_attr', 'alpha_single_product_thumbs_slider_attrs' );
add_filter( 'alpha_single_product_gallery_main_classes', 'alpha_single_product_main_class', 40 );

// Single Product Summary
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 7 );
add_filter( 'alpha_single_product_summary_class', 'alpha_single_product_summary_extend_class' );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' );
add_action( 'woocommerce_single_product_summary', 'alpha_single_product_divider', 8 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 9 );
add_action( 'woocommerce_single_product_summary', 'alpha_single_product_sale_countdown', 9 );
add_action( 'woocommerce_single_product_summary', 'alpha_single_product_links_wrap_start', 45 );
add_action( 'woocommerce_single_product_summary', 'alpha_single_product_compare', 54 );
add_action( 'woocommerce_single_product_summary', 'alpha_single_product_links_wrap_end', 55 );

// Show extra info
// add_action( 'woocommerce_single_product_summary', 'alpha_get_extra_info_html', 15 );

// Single Product Form
add_action( 'woocommerce_before_add_to_cart_quantity', 'alpha_single_product_divider', 10 );
add_action( 'woocommerce_before_add_to_cart_button', 'alpha_single_product_sticky_cart_wrap_start', 15 );
add_action( 'woocommerce_after_add_to_cart_button', 'alpha_single_product_sticky_cart_wrap_end', 20 );

// Single Product Data Tab
add_filter( 'alpha_single_product_data_tab_type', 'alpha_single_product_get_data_tab_type' );
add_filter( 'woocommerce_product_tabs', 'alpha_wc_product_custom_tabs', 99 );

// Single Product Reviews Tab
add_action( 'woocommerce_review_before', 'alpha_wc_review_before_avatar', 5 );
add_action( 'woocommerce_review_before', 'alpha_wc_review_after_avatar', 15 );
remove_action( 'woocommerce_review_before_comment_meta', 'woocommerce_review_display_rating' );
add_action( 'woocommerce_review_meta', 'woocommerce_review_display_rating', 15 );

// Single Product - Related Products
add_action( 'woocommerce_output_related_products_args', 'alpha_related_products_args' );

// Single Product - Up-Sells Products
add_filter( 'woocommerce_upsell_display_args', 'alpha_upsells_products_args' );

// Woocommerce Comment Form
add_filter( 'woocommerce_product_review_comment_form_args', 'alpha_comment_form_args' );

/**
 * doing_quickview
 *
 * Check if doing ajax product quickview popup.
 *
 * @return bool
 * @since 1.0
 */
if ( ! function_exists( 'alpha_doing_quickview' ) ) {
	function alpha_doing_quickview() {
		/**
		 * Filters if the quickview is ajax popup.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'alpha_doing_quickview', alpha_doing_ajax() && isset( $_REQUEST['action'] ) && 'alpha_quickview' == $_REQUEST['action'] && isset( $_POST['product_id'] ) );
	}
}
// Quickview ajax actions & enqueue scripts for quickview
if ( alpha_doing_quickview() ) {
	add_action( 'wp_ajax_alpha_quickview', 'alpha_wc_quickview' );
	add_action( 'wp_ajax_nopriv_alpha_quickview', 'alpha_wc_quickview' );
} elseif ( 'disable' != alpha_get_option( 'quickview_thumbs' ) || ( function_exists( 'alpha_is_elementor_preview' ) && alpha_is_elementor_preview() ) ) {
	add_action( 'alpha_before_shop_loop_start', 'alpha_quickview_add_scripts' );
}


/**
 * Alpha Single Product Class & Layout
 */

/**
 * single_product_extend_class
 *
 * Get single product extend classes.
 *
 * @param array $classes
 * @return array
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_extend_class' ) ) {
	function alpha_single_product_extend_class( $classes ) {
		$single_product_layout = alpha_get_single_product_layout();

		if ( 'gallery' != $single_product_layout ) {
			if ( 'sticky-thumbs' == $single_product_layout ) {
				$classes[] = 'sticky-thumbs';
			}
			if ( ! alpha_doing_ajax() ) {
				$classes[] = 'row';
			}
		}
		/**
		 * Filters the extended class in single product.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'alpha_single_product_extend_class', $classes, $single_product_layout );
	}
}

/**
 * get_single_product_layout
 *
 * Get single product layout type
 *
 * @return string
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_single_product_layout' ) ) {
	function alpha_get_single_product_layout() {
		global $alpha_layout;

		if ( alpha_doing_ajax() ) {
			$layout = '';
			if ( 'offcanvas' != alpha_get_option( 'quickview_type' ) ) {
				$layout = alpha_get_option( 'quickview_thumbs' );
			}
			if ( ! $layout ) {
				$layout = 'horizontal';
			}
		} else {
			$layout = 'horizontal';
		}
		/**
		 * Filters the single product layout.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'alpha_single_product_layout', $layout );
	}
}

/**
 * Alpha Single Product - Gallery Image Functions
 */
if ( ! function_exists( 'alpha_wc_get_gallery_image_html' ) ) {
	/**
	 * Get html of single product gallery image
	 *
	 * @since 1.0
	 * @param int $attachment_id        Image ID
	 * @param boolean $main_image       True if large image is needed
	 * @param boolean $featured_image   True if attachment is featured image
	 * @param boolean $is_thumbnail     True if thumb wrapper is needed
	 * @return string image html
	 */
	function alpha_wc_get_gallery_image_html( $attachment_id, $main_image = false, $featured_image = false, $is_thumbnail = true ) {

		if ( $main_image ) {
			// Get large image

			$image_size    = apply_filters( 'woocommerce_gallery_image_size', doing_action( 'woocommerce_after_shop_loop_item_title' ) ? 'woocommerce_thumbnail' : 'woocommerce_single' );
			$full_size     = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
			$thumbnail_src = wp_get_attachment_image_src( $attachment_id, 'woocommerce_single' );
			$full_src      = wp_get_attachment_image_src( $attachment_id, $full_size );
			$alt_text      = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
			$image         = wp_get_attachment_image(
				$attachment_id,
				$image_size,
				false,
				apply_filters(
					'woocommerce_gallery_image_html_attachment_image_params',
					array(
						'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
						'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
						'data-src'                => esc_url( ! empty( $full_src ) ? $full_src[0] : '' ),
						'data-large_image'        => esc_url( ! empty( $full_src[0] ) ? $full_src[0] : '' ),
						'data-large_image_width'  => ! empty( $full_src[1] ) ? $full_src[1] : '',
						'data-large_image_height' => ! empty( $full_src[2] ) ? $full_src[2] : '',
						'class'                   => $featured_image ? 'wp-post-image' : '',
					),
					$attachment_id,
					$image_size,
					$main_image
				)
			);

			if ( $is_thumbnail ) {
				$image = '<div data-thumb="' . esc_url( ! empty( $thumbnail_src[0] ) ? $thumbnail_src[0] : '' ) . ( $alt_text ? '" data-thumb-alt="' . esc_attr( $alt_text ) : '' ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( ! empty( $full_src[0] ) ? $full_src[0] : '' ) . '">' . $image . '</a></div>';
			}
		} else {
			/**
			 * Get small image
			 *
			 * Filters the image size of woocommerce thumbnail.
			 *
			 * @since 1.0
			 */
			$thumbnail_size = apply_filters( 'alpha_wc_thumbnail_image_size', 'woocommerce_thumbnail' );

			if ( $attachment_id ) {
				// If default or horizontal layout, print simple image tag
				$gallery_thumbnail = false;
				if ( 'alpha-product-thumbnail' == $thumbnail_size ) {
					$image_sizes = wp_get_additional_image_sizes();
					if ( isset( $image_sizes[ $thumbnail_size ] ) ) {
						$gallery_thumbnail = $image_sizes[ $thumbnail_size ];
					}
				}
				if ( ! $gallery_thumbnail ) {
					$gallery_thumbnail = wc_get_image_size( $thumbnail_size );
				}

				if ( 0 == $gallery_thumbnail['height'] ) {
					$full_image_size = wp_get_attachment_image_src( $attachment_id, 'full' );
					if ( isset( $full_image_size[1] ) && $full_image_size[1] ) {
						$gallery_thumbnail['height'] = intval( $gallery_thumbnail['width'] / absint( $full_image_size[1] ) * absint( $full_image_size[2] ) );
					}
				}
				$thumbnail_size = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
				$image_src      = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
				$image          = '<img alt="' . esc_attr( _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ) ) . '" src="' . esc_url( ! empty( $image_src[0] ) ? $image_src[0] : '' ) . '" width="' . (int) ( ! empty( $thumbnail_size[0] ) ? $thumbnail_size[0] : '' ) . '" height="' . (int) ( ! empty( $thumbnail_size[1] ) ? $thumbnail_size[1] : '' ) . '">';

			} else {
				$image = '';
			}

			if ( $is_thumbnail && $image ) {
				$image = '<div class="product-thumb' . ( $featured_image ? ' active' : '' ) . '"><div class="product-thumb-inner">' . $image . '</div></div>';
			}
		}
		/**
		 * Filters the html of gallery image.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'alpha_wc_get_gallery_image_html', $image );
	}
}

/**
 * single_product_thumbnail_image_size
 *
 * Get single product thumbnail image size.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_thumbnail_image_size' ) ) {
	function alpha_single_product_thumbnail_image_size( $image ) {
		if ( alpha_is_product() ) {
			return 'alpha-product-thumbnail';
		}
	}
}

/**
 * alpha_single_product_gallery_slider_attrs
 *
 * Get single product gallery slider attrs.
 *
 * @since 1.2.0
 */
if ( ! function_exists( 'alpha_single_product_gallery_slider_attrs' ) ) {
	function alpha_single_product_gallery_slider_attrs( $attr = '' ) {
		$options = array(
			'pagination' => false,
			'navigation' => true,
			'autoHeight' => true,
			'thumbs'     => array(
				'slideThumbActiveClass' => 'active',
			),
		);
		/**
		 * Filters the slider attrs of single product gallery.
		 *
		 * @since 1.0
		 */
		$options = apply_filters( 'alpha_single_product_gallery_slider_attrs', $options );
		return $attr . ' data-slider-options="' . esc_attr( json_encode( $options ) ) . '"';
	}
}

/**
 * alpha_single_product_thumbs_slider_classes
 *
 * Get single product gallery slider classes.
 *
 * @since 1.2.0
 */
if ( ! function_exists( 'alpha_single_product_thumbs_slider_classes' ) ) {
	function alpha_single_product_thumbs_slider_classes( $class = '' ) {
		if ( 'vertical' == alpha_get_single_product_layout() ) {
			$col_cnt       = alpha_get_responsive_cols(
				array(
					'xlg' => 5,
					'lg'  => 5,
				)
			);
			$col_cnt['xlg'] = $col_cnt['xl'] = $col_cnt['lg'] = 1;
			$add_class     = alpha_get_col_class( $col_cnt );

		} else {
			$col_cnt   = alpha_get_responsive_cols(
				array(
					'xlg' => 5,
					'lg'  => 4,
				)
			);
			$add_class = alpha_get_col_class( $col_cnt );
		}

		/**
		 * Filters the slider classes of single product thumbs.
		 *
		 * @since 1.0
		 */
		$add_class = apply_filters( 'alpha_single_product_thumbs_slider_classes', $add_class );
		return $class . ' ' . $add_class;
	}
}
/**
 * alpha_single_product_main_class
 *
 * Add gutter-sm on default single product image.
 *
 * @since 1.2.0
 */
if ( ! function_exists( 'alpha_single_product_main_class' ) ) {
	function alpha_single_product_main_class( $class ) {
		if ( ! strpos( implode( ' ', $class ), 'gutter-' ) ) {
			$class[] = 'gutter-sm';
		}
		return $class;
	}
}
/**
 * alpha_single_product_thumbs_slider_attrs
 *
 * Get single product gallery slider attrs.
 *
 * @since 1.2.0
 */
if ( ! function_exists( 'alpha_single_product_thumbs_slider_attrs' ) ) {
	function alpha_single_product_thumbs_slider_attrs( $attr = '' ) {
		$options         = array(
			'navigation'            => true,
			'pagination'            => false,
			'spaceBetween'          => 10,
			'normalizeSlideIndex'   => false,
			'freeMode'              => true,
			'watchSlidesVisibility' => true,
			'watchSlidesProgress'   => true,
		);
		$max_breakpoints = alpha_get_breakpoints();
		if ( 'vertical' == alpha_get_single_product_layout() ) {
			$col_cnt = alpha_get_responsive_cols( array( 'xlg' => 5 ) );
			foreach ( $col_cnt as $w => $c ) {
				if ( 'xlg' == $w || 'xl' == $w || 'lg' == $w ) {
					continue;
				}
				$options['breakpoints'][ $max_breakpoints[ $w ] ] = array(
					'slidesPerView' => $c,
					'direction'     => 'horizontal',
				);
			}
			$options['direction']                         = 'vertical';
			$options['breakpoints'][992]['slidesPerView'] = 'auto';
			$options['breakpoints'][992]['direction']     = 'vertical';
		} else {
			$col_cnt = alpha_get_responsive_cols(
				array(
					'xlg' => 4,
					'lg' => 4,
					'sm' => 3,
				)
			);
			foreach ( $col_cnt as $w => $c ) {
				$options['breakpoints'][ $max_breakpoints[ $w ] ] = array(
					'slidesPerView' => $c,
				);
			}
		}
		/**
		 * Filters the slider attrs of single product thumbs.
		 *
		 * @since 1.0
		 */
		$options = apply_filters( 'alpha_single_product_thumbs_slider_attrs', $options );
		return $attr . ' data-slider-options="' . esc_attr( json_encode( $options ) ) . '"';
	}
}

/**
 * Alpha Single Product Navigation
 */

/**
 * single_product_navigation
 *
 * Render single product navigation.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_navigation' ) ) {
	function alpha_single_product_navigation() {
		/**
		 * Filters post products in single product builder
		 *
		 * @since 1.0
		 */
		if ( ! class_exists( 'Alpha_Single_Product_Builder' ) || ( class_exists( 'Alpha_Single_Product_Builder' ) && apply_filters( 'alpha_single_product_builder_set_preview', false ) ) ) {
			global $post;
			$prev_post = get_previous_post( true, '', 'product_cat' );
			$next_post = get_next_post( true, '', 'product_cat' );
			$html      = '';

			if ( is_a( $prev_post, 'WP_Post' ) || is_a( $next_post, 'WP_Post' ) ) {
				$html .= '<ul class="product-nav">';

				if ( is_a( $prev_post, 'WP_Post' ) ) {
					$html             .= '<li class="product-nav-prev">';
						$html         .= '<a href="' . esc_url( get_the_permalink( $prev_post->ID ) ) . '" aria-label="' . esc_html__( 'Prev', 'alpha' ) . '" rel="prev"><i class="' . apply_filters( 'alpha_single_product_nav_prev_icon', ALPHA_ICON_PREFIX . '-icon-angle-left' ) . '"></i>';
							$html     .= '<span class="product-nav-popup">';
								$html .= alpha_strip_script_tags( get_the_post_thumbnail( $prev_post->ID, apply_filters( 'woocommerce_gallery_thumbnail_size', 'woocommerce_gallery_thumbnail' ) ) );
								$html .= '<span>' . esc_attr( get_the_title( $prev_post->ID ) ) . '</span>';
					$html             .= '</span></a></li>';
				}
				if ( is_a( $next_post, 'WP_Post' ) ) {
					$html             .= '<li class="product-nav-next">';
						$html         .= '<a href="' . esc_url( get_the_permalink( $next_post->ID ) ) . '" aria-label="' . esc_html__( 'Next', 'alpha' ) . '" rel="next"><i class="' . apply_filters( 'alpha_single_product_nav_next_icon', ALPHA_ICON_PREFIX . '-icon-angle-right' ) . '"></i>';
							$html     .= ' <span class="product-nav-popup">';
								$html .= alpha_strip_script_tags( get_the_post_thumbnail( $next_post->ID, apply_filters( 'woocommerce_gallery_thumbnail_size', 'woocommerce_gallery_thumbnail' ) ) );
								$html .= '<span>' . esc_attr( get_the_title( $next_post->ID ) ) . '</span>';
					$html             .= '</span></a></li>';
				}

				$html .= '</ul>';
			}
			do_action( 'alpha_single_product_builder_unset_preview' );
			/**
			 * Filters the navigation of single product.
			 *
			 * @since 1.0
			 */
			return apply_filters( 'alpha_single_product_navigation', $html );
		}
	}
}

/**
 * single_prev_next_product
 *
 * Render single product navigation.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_prev_next_product' ) ) {
	function alpha_single_prev_next_product( $args ) {
		global $post, $alpha_layout;
		if ( empty( $alpha_layout['single_product_block'] ) && 'single_product' == alpha_get_page_layout() ) {
			$args['wrap_before'] = '<div class="product-navigation">' . $args['wrap_before'];
			$args['wrap_after'] .= alpha_single_product_navigation() . '</div>';
		}
		/**
		 * Filters the product which placed in prev or next.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'alpha_filter_single_prev_next_product', $args );
	}
}

/**
 * single_product_wrap_first_start
 *
 * Render start part of single product wrap.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_wrap_first_start' ) ) {
	function alpha_single_product_wrap_first_start() {
		if ( ! alpha_doing_ajax() || 'offcanvas' != alpha_get_option( 'quickview_type' ) ) {
			echo '<div class="col-md-6">';
		}
	}
}

/**
 * single_product_wrap_first_end
 *
 * Render end part of single product wrap.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_wrap_first_end' ) ) {
	function alpha_single_product_wrap_first_end() {
		if ( ! alpha_doing_ajax() || 'offcanvas' != alpha_get_option( 'quickview_type' ) ) {
			echo '</div>';
		}
	}
}

/**
 * single_product_wrap_second_start
 *
 * Render start part of single product wrap.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_wrap_second_start' ) ) {
	function alpha_single_product_wrap_second_start() {
		if ( ! alpha_doing_ajax() || 'offcanvas' != alpha_get_option( 'quickview_type' ) ) {
			echo '<div class="col-md-6">';
		}
	}
}

/**
 * single_product_wrap_second_end
 *
 * Render end part of single product wrap.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_wrap_second_end' ) ) {
	function alpha_single_product_wrap_second_end() {
		if ( ! alpha_doing_ajax() || 'offcanvas' != alpha_get_option( 'quickview_type' ) ) {
			echo '</div>';
		}
	}
}


/**
 * single_product_sticky_cart_wrap_start
 *
 * Render start part of single product sticky cart wrap.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_sticky_cart_wrap_start' ) ) {
	function alpha_single_product_sticky_cart_wrap_start() {
		global $alpha_layout, $product;
		/**
		 * Filters if single product sticy cart is enabled.
		 *
		 * @since 1.0
		 */
		if ( apply_filters( 'alpha_single_product_sticky_cart_enabled', ! empty( $alpha_layout['single_product_sticky'] ) ) ) {
			echo '<div class="sticky-content product-sticky-content" data-sticky-options="{\'minWidth\':' . ( empty( $alpha_layout['single_product_sticky_mobile'] ) ? '768' : '1' ) . ', \'scrollMode\': true}"><div class="container"><div class="sticky-product-details">';
			$product_image_id = $product->get_image_id();

			if ( $product_image_id && wp_get_attachment_url( $product_image_id ) ) {
				$image_html = wc_get_gallery_image_html( $product_image_id, true );
			} else {
				$image_html  = '<div class="woocommerce-product-gallery__image--placeholder">';
				$image_html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'alpha' ) );
				$image_html .= '</div>';
			}
				echo  alpha_strip_script_tags( $image_html );
				echo '<div>';
					echo '<h3 class="product-title entry-title">' . esc_html( get_the_title() ) . '</h3>';
					echo '<p class="price price-sticky">' . alpha_strip_script_tags( $product->get_price_html() ) . '</p>';
				echo '</div>';
			echo '</div>';
		}
	}
}

/**
 * single_product_sticky_cart_wrap_end
 *
 * Render end part of single product sticky cart wrap.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_sticky_cart_wrap_end' ) ) {
	function alpha_single_product_sticky_cart_wrap_end() {
		global $alpha_layout;
		/**
		 * Filters if single product sticy cart is enabled.
		 *
		 * @since 1.0
		 */
		if ( apply_filters( 'alpha_single_product_sticky_cart_enabled', ! empty( $alpha_layout['single_product_sticky'] ) ) ) {
			echo '</div></div>';
		}
	}
}

/**
 * Alpha Single Product Media Functions
 */

/**
 * single_product_images
 *
 * Render single product images
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_images' ) ) {
	function alpha_single_product_images() {
		$single_product_layout = alpha_get_single_product_layout();

		/**
		 * Filters the types of gallery.
		 *
		 * @since 1.0
		 */
		if ( in_array( $single_product_layout, apply_filters( 'alpha_special_gallery_types', array( 'horizontal', 'vertical', 'gallery', 'sticky-thumbs' ) ) ) ) {
			return;
		}

		global $product;
		global $alpha_layout;

		$single_product_layout = alpha_get_single_product_layout();
		$post_thumbnail_id     = $product->get_image_id();
		$attachment_ids        = $product->get_gallery_image_ids();

		if ( $post_thumbnail_id ) {
			$html = apply_filters( 'woocommerce_single_product_image_thumbnail_html', alpha_wc_get_gallery_image_html( $post_thumbnail_id, true, true ), $post_thumbnail_id );
		} else {
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image">', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'alpha' ) );
			$html .= '</div>';
		}

		if ( $single_product_layout ) {
			if ( $attachment_ids && $post_thumbnail_id ) {
				foreach ( $attachment_ids as $attachment_id ) {
					$html .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', alpha_wc_get_gallery_image_html( $attachment_id, true ), $attachment_id );
				}
			}
		}

		echo alpha_escaped( $html );
	}
}

/**
 * single_product_gallery_classes
 *
 * Return single product gallery classes.
 *
 * @param array $classes
 * @return array
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_gallery_classes' ) ) {
	function alpha_single_product_gallery_classes( $classes ) {
		$single_product_layout = alpha_get_single_product_layout();
		$classes[]             = 'product-gallery';
		if ( 'vertical' == $single_product_layout ) {
			wp_enqueue_script( 'swiper' );
			$classes[] = 'pg-vertical';
		} elseif ( 'horizontal' == $single_product_layout ) {
			wp_enqueue_script( 'swiper' );
		} elseif ( 'gallery' == $single_product_layout ) {
			wp_enqueue_script( 'swiper' );
			$classes[] = 'pg-gallery';
		} elseif ( 'grid' == $single_product_layout ) {
			$classes[] = 'row';
			$classes[] = 'cols-sm-2';
		} elseif ( 'masonry' == $single_product_layout ) {
			$classes[] = 'row';
			$classes[] = 'cols-sm-2';
			$classes[] = 'product-masonry-type';
		} elseif ( '' == $single_product_layout ) {
			$classes[] = 'pg-default';
		}
		/**
		 * Filters the classes of single product gallery.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'alpha_single_product_gallery_classes', $classes, $single_product_layout );
	}
}

/**
 * Alpha Single Product Meta - Social Sharing Wrapper Functions
 */

/**
 * single_product_ms_wrap_start
 *
 * Return start part of single product meta wrap.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_ms_wrap_start' ) ) {
	function alpha_single_product_ms_wrap_start() {
		echo '<div class="product-ms-wrapper">';
	}
}

/**
 * single_product_ms_wrap_end
 *
 * Return end part of single product meta wrap.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_ms_wrap_end' ) ) {
	function alpha_single_product_ms_wrap_end() {
		echo '</div>';
	}
}

/**
 * single_product_brands
 *
 * Render single product brands.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_brands' ) ) {
	function alpha_single_product_brands( $label = true ) {
		global $product;

		$has_brand_image = false;
		$brands          = wp_get_post_terms( get_the_ID(), 'product_brand', array( 'fields' => 'id=>name' ) );
		$brand_html      = '';

		if ( is_array( $brands ) && count( $brands ) ) {
			foreach ( $brands as $brand_id => $brand_name ) {
				$brand_image = get_term_meta( $brand_id, 'brand_thumbnail_id', true );
				if ( $brand_image ) {
					$has_brand_image = true;
					$brand_html     .= '<a class="brand d-inline-block" href="' . esc_url( get_term_link( $brand_id, 'product_brand' ) ) . '" title="' . esc_attr( $brand_name ) . '">';
					$brand_html     .= wp_get_attachment_image( $brand_image, 'full' );
					$brand_html     .= '</a>';
				} else {
					if ( array_key_last( $brands ) == $brand_id ) {
						$comma = '';
					} else {
						$comma = ', ';
					}
					$brand_html .= ( $label ? ( '<span>' . esc_html__( 'Brand: ', 'alpha' ) ) : '' ) . '<a href="' . esc_url( get_term_link( $brand_id, 'product_brand' ) ) . '" title="' . esc_attr( $brand_name ) . '">' . esc_html( $brand_name ) . '</a>' . ( $label ? '</span>' : '' ) . $comma;
				}
			}
		}
		return array(
			'html'      => $brand_html,
			'has_image' => $has_brand_image,
		);
	}
}

/**
 * Alpha Single Product Summary Functions
 */

/**
 * Display sale countdown for simple & variable product in single product page.
 *
 * @since 1.0
 * @param string $ends_label
 * @return void
 */
if ( ! function_exists( 'alpha_single_product_sale_countdown' ) ) {
	function alpha_single_product_sale_countdown( $ends_label = '' ) {

		global $product;

		if ( $product->is_on_sale() ) {

			$extra_class = '';

			if ( $product->is_type( 'variable' ) ) {
				$variations = $product->get_available_variations( 'object' );
				$date_diff  = '';
				$sale_date  = '';
				foreach ( $variations as $variation ) {
					if ( $variation->is_on_sale() ) {
						$new_date = get_post_meta( $variation->get_id(), '_sale_price_dates_to', true );
						if ( ! $new_date || ( $date_diff && $date_diff != $new_date ) ) {
							$date_diff = false;
						} elseif ( $new_date ) {
							if ( false !== $date_diff ) {
								$date_diff = $new_date;
							}
							$sale_date = $new_date;
						}
						if ( false === $date_diff && $sale_date ) {
							break;
						}
					}
				}
				if ( $date_diff ) {
					$date_diff = date( 'Y/m/d H:i:s', (int) $date_diff );
				} elseif ( $sale_date ) {
					$extra_class .= ' countdown-variations';
					$date_diff    = date( 'Y/m/d H:i:s', (int) $sale_date );
				}
			} else {
				$date_diff = $product->get_date_on_sale_to();
				if ( $date_diff ) {
					$date_diff = $date_diff->date( 'Y/m/d H:i:s' );
				}
			}

			if ( $date_diff && defined( 'ALPHA_CORE_VERSION' ) ) {
				wp_enqueue_script( 'jquery-countdown' );
				wp_enqueue_style( 'alpha-countdown', alpha_core_framework_uri( '/widgets/countdown/countdown' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_CORE_VERSION );
				wp_enqueue_script( 'alpha-countdown', alpha_core_framework_uri( '/widgets/countdown/countdown' . ALPHA_JS_SUFFIX ), array( 'jquery-countdown' ), ALPHA_CORE_VERSION, true );
				?>
				<div class="product-countdown-container<?php echo esc_attr( $extra_class ); ?>">
					<?php echo empty( $ends_label ) ? esc_html__( 'Offer Ends In:', 'alpha' ) : esc_html( $ends_label ); ?>
					<div class="countdown product-countdown countdown-compact" data-until="<?php echo esc_attr( $date_diff ); ?>" data-compact="true">0<?php esc_html_e( 'days', 'alpha' ); ?>, 00 : 00 : 00</div>
				</div>
				<?php
			}
		}
	}
}

/**
 * Single Product Sale Countdown for variable product.
 *
 * @since 1.0
 * @param array $vars
 * @param array $product
 * @param array $variation
 * @return array $vars
 */
if ( ! function_exists( 'alpha_variation_add_sale_ends' ) ) {
	function alpha_variation_add_sale_ends( $vars, $product, $variation ) {

		if ( $variation->is_on_sale() ) {
			$date_diff = $variation->get_date_on_sale_to();
			if ( $date_diff ) {
				$vars['alpha_date_on_sale_to'] = $date_diff->date( 'Y/m/d H:i:s' );
			}
		}
		return $vars;
	}
}

/**
 * single_product_summary_extend_class
 *
 * Return single product summary extend class.
 *
 * @param string $class
 * @return string
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_summary_extend_class' ) ) {
	function alpha_single_product_summary_extend_class( $class ) {
		if ( alpha_doing_ajax() ) {
			$class .= ' scrollable';
		}
		return $class;
	}
}

/**
 * single_product_divider
 *
 * Render single product divider.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_divider' ) ) {
	function alpha_single_product_divider() {
		global $alpha_layout;
		if ( ! alpha_is_elementor_preview() ) {
			/**
			 * Filters the single product divider.
			 *
			 * @since 1.0
			 */
			echo apply_filters( 'alpha_single_product_divider', '<hr class="product-divider">' );
		}
	}
}

/**
 * Alpha Single Product Data Tab Functions
 */

/**
 * single_product_get_data_tab_type
 *
 * Return single product data tab type.
 *
 * @param string $tabs
 * @return string
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_get_data_tab_type' ) ) {
	function alpha_single_product_get_data_tab_type( $tabs ) {
		global $alpha_layout;
		if ( isset( $alpha_layout['product_data_type'] ) ) {
			if ( 'accordion' == $alpha_layout['product_data_type'] ) {
				return 'accordion';
			} elseif ( 'section' == $alpha_layout['product_data_type'] ) {
				return 'section';
			}
		}
		return 'tab';
	}
}

/**
 * wc_product_custom_tabs
 *
 * @param string $tabs
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_product_custom_tabs' ) ) {
	function alpha_wc_product_custom_tabs( $tabs ) {

		// Show reviews at last
		if ( isset( $tabs['reviews'] ) ) {
			$tabs['reviews']['priority'] = 999;
		}

		// Change default titles
		if ( isset( $tabs['description'] ) && isset( $tabs['description']['title'] ) ) {
			$tabs['description']['title'] = alpha_get_option( 'product_description_title' );
		}

		if ( isset( $tabs['additional_information'] ) && isset( $tabs['additional_information']['title'] ) ) {
			$tabs['additional_information']['title'] = alpha_get_option( 'product_specification_title' );
		}
		if ( isset( $tabs['reviews'] ) && isset( $tabs['reviews']['title'] ) ) {
			$tabs['reviews']['title'] = alpha_get_option( 'product_reviews_title' ) . ' <span>(' . $GLOBALS['product']->get_review_count() . ')</span>';
		}

		// Global tab
		$title = alpha_get_option( 'product_tab_title' );
		if ( $title ) {
			$tabs['alpha_product_tab'] = array(
				'title'    => sanitize_text_field( $title ),
				'priority' => 24,
				'callback' => 'alpha_wc_product_custom_tab',
			);
		}

		// Custom tab for current product
		$title = get_post_meta( get_the_ID(), 'alpha_custom_tab_title_1st', true );
		if ( $title ) {
			$tabs['alpha_custom_tab_1st'] = array(
				'title'    => sanitize_text_field( $title ),
				'priority' => 26,
				'callback' => 'alpha_wc_product_custom_tab',
			);
		}
		$title = get_post_meta( get_the_ID(), 'alpha_custom_tab_title_2nd', true );
		if ( $title ) {
			$tabs['alpha_custom_tab_2nd'] = array(
				'title'    => sanitize_text_field( $title ),
				'priority' => 26,
				'callback' => 'alpha_wc_product_custom_tab',
			);
		}
		return $tabs;
	}
}


/**
 * wc_product_custom_tab
 *
 * Render Woocommerce Custom Tab.
 *
 * @param string $key
 * @param string product_tab
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_product_custom_tab' ) ) {
	function alpha_wc_product_custom_tab( $key, $product_tab ) {
		wc_get_template(
			'single-product/tabs/custom_tab.php',
			array(
				'tab_name' => $key,
				'tab_data' => $product_tab,
			)
		);
	}
}

/**
 * yith_wcwl_positions
 *
 * Change default YITH positions
 *
 * @param array $position
 * @return array
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_yith_wcwl_positions' ) ) {
	function alpha_yith_wcwl_positions( $position ) {
		$position['summary']['hook'] = 'alpha_after_product_summary';
		return $position;
	}
}

/**
 * Single Product Reviews Tab
 */

/**
 * wc_review_before_avatar
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_review_before_avatar' ) ) {
	function alpha_wc_review_before_avatar() {
		echo '<figure class="comment-avatar">';
	}
}

/**
 * wc_review_after_avatar
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_review_after_avatar' ) ) {
	function alpha_wc_review_after_avatar() {
		echo '</figure>';
	}
}

/**
 * Alpha Single Product - Related Products Functions
 *
 * @since 1.0
 * @param array $args
 * @return array $args
 */
if ( ! function_exists( 'alpha_related_products_args' ) ) {
	function alpha_related_products_args( $args = array() ) {
		$count    = 4;
		$orderby  = '';
		$orderway = 'asc';
		if ( $count ) {
			$args['posts_per_page'] = $count;
		}
		if ( $orderby ) {
			$args['orderby'] = $orderby;
		}
		if ( $orderway ) {
			$args['orderway'] = $orderway;
		}
		return $args;
	}
}

/**
 * Alpha Single Product - Up-Sells Products Functions
 *
 * @since 1.0
 * @param array $args
 * @return array $args
 */
if ( ! function_exists( 'alpha_upsells_products_args' ) ) {
	function alpha_upsells_products_args( $args = array() ) {
		$count    = 4;
		$orderby  = '';
		$orderway = 'asc';
		if ( $count ) {
			$args['posts_per_page'] = $count;
		}
		if ( $orderby ) {
			$args['orderby'] = $orderby;
		}
		if ( $orderway ) {
			$args['orderway'] = $orderway;
		}
		return $args;
	}
}

/**
 * Alpha Quickview Ajax Actions
 */

/**
 * wc_quickview
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_quickview' ) ) {
	function alpha_wc_quickview() {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		if ( ! has_action( 'woocommerce_single_product_summary', 'alpha_single_product_compare', 58 ) ) {
			add_action( 'woocommerce_single_product_summary', 'alpha_single_product_compare', 58 );
		}

		global $product, $post;
		$product_id = intval( $_POST['product_id'] );
		$post       = get_post( $product_id );
		$product    = wc_get_product( $product_id );

		if ( $product->is_type( 'variation' ) ) {
			$attrs = wc_get_product_variation_attributes( $post->ID );
			if ( ! empty( $attrs ) ) {
				foreach ( $attrs as $key => $val ) {
					$_REQUEST[ $key ] = $val;
				}
			}
			$parent_id = wp_get_post_parent_id( $post );
			if ( $parent_id ) {
				$post    = get_post( (int) $parent_id );
				$product = wc_get_product( $post->ID );
			}
		}

		wc_get_template_part( 'content', 'single-product' );
		// phpcs:enable
		die;
	}
}

/**
 * quickview_add_scripts
 *
 * Enqueue script files for quickview.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_quickview_add_scripts' ) ) {
	function alpha_quickview_add_scripts() {
		wp_enqueue_style( 'alpha-magnific-popup' );

		wp_enqueue_script( 'swiper' );
		wp_enqueue_script( 'alpha-magnific-popup' );
		wp_enqueue_script( 'jquery-countdown' );

		wp_enqueue_script( 'wc-single-product' );
		wp_enqueue_script( 'wc-add-to-cart-variation' );
		wp_enqueue_script( 'zoom' );

		if ( alpha_get_option( 'advanced_swatch' ) ) {
			wp_enqueue_script( 'alpha-advanced-swatch' );
		}
	}
}

/**
 * single_product_compare
 *
 * Render Single Product Compare.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_compare' ) ) {
	function alpha_single_product_compare() {
		echo '<div class="add-to-compare">';
		alpha_product_compare( ' btn-product-icon' );
		echo '</div>';
	}
}

/**
 * print_wishlist_button
 *
 * Render YITH wishlist button
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_print_wishlist_button' ) ) {
	function alpha_print_wishlist_button() {
		echo do_shortcode( '[yith_wcwl_add_to_wishlist container_classes="btn-product-icon"]' );
		return false;
	}
}

/**
 * single_product_links_wrap_start
 *
 * Render start part of single product links.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_links_wrap_start' ) ) {
	function alpha_single_product_links_wrap_start() {
		echo '<div class="product-links-wrapper">';
	}
}

/**
 * single_product_links_wrap_end
 *
 * Render end part of single product links.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_links_wrap_end' ) ) {
	function alpha_single_product_links_wrap_end() {
		echo '</div>';
	}
}

/* Single Product Gallery Types */
foreach ( apply_filters(
	'alpha_sp_types',
	array(
		'vertical'      => true,
		'horizontal'    => true,
		'grid'          => true,
		'masonry'       => true,
		'gallery'       => true,
		'sticky-thumbs' => true,
	),
	'hooks'
) as $key => $value ) {
	if ( $key && $value ) {
		require_once alpha_framework_path( ALPHA_FRAMEWORK_PLUGINS . "/woocommerce/product-single/product-single-{$key}.php" );
	}
}

/**
 * Fires after setting product single actions and filters.
 *
 * Here you can remove and add more actions and filters.
 *
 * @since 1.0
 */
do_action( 'alpha_after_ps_hooks' );
