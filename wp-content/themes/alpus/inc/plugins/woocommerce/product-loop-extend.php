<?php
/**
 * Alpha WooCommerce Product Loop Extension
 *
 * Functions used to display product loop.
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */

remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_price', 7 );
add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 15 );
if ( ( ! empty( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] && is_admin() ) || ( ! empty( $_REQUEST['legacy-widget-preview'] ) ) ) {
	add_action(
		'alpha_after_product_loop_hooks',
		function() {
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
			remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
		}
	);
}


/**
 * The product loop count deal
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_product_loop_count_deal' ) ) {
	function alpha_product_loop_count_deal() {
		global $product;
		if ( $product->is_on_sale() ) {
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
					$date_diff = date( 'Y/m/d H:i:s', (int) $sale_date );
				}
			} else {
				$date_diff = $product->get_date_on_sale_to();
				if ( $date_diff ) {
					$date_diff = $date_diff->date( 'Y/m/d H:i:s' );
				}
			}
			if ( $date_diff ) :
				if ( defined( 'ALPHA_CORE_VERSION' ) ) {
					wp_enqueue_script( 'jquery-countdown' );
					wp_enqueue_style( 'alpha-countdown', ALPHA_ASSETS . '/vendor/countdown/countdown' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), '6.7.0' );
					wp_enqueue_script( 'alpha-countdown', ALPHA_ASSETS . '/vendor/countdown/countdown' . ALPHA_JS_SUFFIX, '6.7.0', true );
				}
				?>
				<div class="countdown-container block-type">
					<div class="countdown" data-until="<?php echo esc_attr( strtotime( $date_diff ) - strtotime( 'now' ) ); ?>" data-relative="true" data-labels-short="true"></div>
				</div>
				<?php
			endif;
		}
	}
}

/**
  * Alpha product compare function
  *
  * @since 1.0
  */
if ( ! function_exists( 'alpha_product_compare' ) ) {
	function alpha_product_compare( $extra_class = '' ) {
		if ( ! class_exists( 'Alpus_Product_Compare' ) ) {
			return;
		}

		global $product;

		$css_class  = 'compare' . $extra_class;
		$product_id = $product->get_id();
		$url        = '#';

		if ( Alpus_Product_Compare::get_instance()->is_compared_product( $product_id ) ) {
			$url         = get_permalink( wc_get_page_id( 'compare' ) );
			$css_class  .= ' added';
			$button_text = apply_filters( 'alpha_woocompare_added_label', esc_html__( 'Added', 'alpha' ) );
		} else {
			$button_text = apply_filters( 'alpha_woocompare_add_label', esc_html__( 'Compare', 'alpha' ) );
		}

		printf( '<a href="%s" class="%s" title="%s" data-product_id="%d" data-added-text="%s">%s</a>', esc_url( $url ), esc_attr( $css_class ), esc_html( $button_text ), $product_id, esc_html( apply_filters( 'alpha_woocompare_added_label', esc_html__( 'Added', 'alpha' ) ) ), esc_html( $button_text ) );
	}
}

/**
 * The product loop vertical action
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_product_loop_vertical_action' ) ) {
	function alpha_product_loop_vertical_action() {
		// if product type is not default, do not print vertical action buttons.
		global $product;
		$product_type = alpha_wc_get_loop_prop( 'product_type' );
		/**
		 * Filters the vertical actions of product.
		 *
		 * @since 1.0
		 */
		$exclude_type = apply_filters( 'alpha_product_loop_vertical_action', array( 'widget' ) );
		if ( ! in_array( $product_type, $exclude_type ) ) {
			$html = '';
			if ( '' == alpha_wc_get_loop_prop( 'addtocart_pos' ) ) {
				ob_start();
				woocommerce_template_loop_add_to_cart(
					array(
						'class' => implode(
							' ',
							array_filter(
								array(
									'btn-product-icon',
									'product_type_' . $product->get_type(),
									$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
									$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
								)
							)
						),
					)
				);

				$html .= ob_get_clean();
			}

			if ( '' == alpha_wc_get_loop_prop( 'wishlist_pos' ) && defined( 'YITH_WCWL' ) ) {
				$html .= do_shortcode( '[yith_wcwl_add_to_wishlist container_classes="btn-product-icon"]' );
			}

			ob_start();
			alpha_product_compare( ' btn-product-icon' );
			$html .= ob_get_clean();

			if ( '' == alpha_wc_get_loop_prop( 'quickview_pos' ) ) {
				global $product;

				$html .= '<button class="btn-product-icon btn-quickview" data-mfp-src="' . alpha_get_product_featured_image_src( $product ) . '" data-product="' . $product->get_id() . '" title="' . esc_attr__( 'Quick View', 'alpha' ) . '">' . esc_html__( 'Quick View', 'alpha' ) . '</button>';
			}

			ob_start();

			do_action( 'alpha_product_loop_gallery_buttons' );

			$html .= ob_get_clean();

			if ( $html ) {
				echo '<div class="product-action-vertical">' . alpha_escaped( $html ) . '</div>';
			}
		}
	}
}
