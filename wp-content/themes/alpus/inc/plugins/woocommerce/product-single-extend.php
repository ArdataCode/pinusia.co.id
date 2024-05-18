<?php
/**
 * Alpha WooCommerce Product Single Extension
 *
 * Functions used to display product single.
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
add_action(
	'alpha_after_framework',
	function() {
		remove_action( 'woocommerce_single_product_summary', 'alpha_single_product_sale_countdown', 9 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 7 );
		remove_action( 'woocommerce_before_add_to_cart_quantity', 'alpha_single_product_divider', 10 );
		remove_action( 'woocommerce_single_product_summary', 'alpha_single_product_compare', 54 );

		add_action( 'woocommerce_single_product_summary', 'alpha_single_product_sale_countdown', 20 );
		add_action( 'woocommerce_single_product_summary', 'alpha_single_product_before_price_start', 8 );
		add_action( 'woocommerce_single_product_summary', 'alpha_single_product_after_rating_end', 15 );
		add_action( 'woocommerce_single_product_summary', 'alpha_single_product_compare', 35 );
		add_action( 'woocommerce_single_product_summary', 'alpha_single_product_divider', 35 );
		add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	}
);

if ( ! function_exists( 'alpha_single_product_before_price_start' ) ) {
	function alpha_single_product_before_price_start() {
		global $alpha_layout;
		if ( empty( $alpha_layout['single_product_block'] ) ) {
			echo '<div class="product-price-rating">';
		}
	}
}
if ( ! function_exists( 'alpha_single_product_after_rating_end' ) ) {
	function alpha_single_product_after_rating_end() {
		global $alpha_layout;
		if ( empty( $alpha_layout['single_product_block'] ) ) {
			echo '</div>';
		}
	}
}

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
				wp_enqueue_style( 'alpha-countdown', ALPHA_ASSETS . '/vendor/countdown/countdown' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), '6.7.0' );
				wp_enqueue_script( 'alpha-countdown', ALPHA_ASSETS . '/vendor/countdown/countdown' . ALPHA_JS_SUFFIX, '6.7.0', true );
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
				$classes[] = 'row gutter-lg';
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

		if ( ! defined( 'ALPHA_PRO_VERSION' ) ) {
			return $tabs;
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

		if ( ! defined( 'ALPHA_PRO_VERSION' ) ) {
			return;
		}

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
 * single_product_brands
 *
 * Render single product brands.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_brands' ) ) {
	function alpha_single_product_brands( $before_label = true, $type = 'name' ) {
		global $product;

		$has_brand_image = false;
		$brands          = wp_get_post_terms( get_the_ID(), 'product_brand', array( 'fields' => 'id=>name' ) );
		$brand_html      = '';

		if ( is_array( $brands ) && count( $brands ) ) {
			foreach ( $brands as $brand_id => $brand_name ) {
				if ( 'name' != $type ) {
					$brand_image = get_term_meta( $brand_id, 'brand_thumbnail_id', true );
				}
				if ( ! empty( $brand_image ) ) {
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
					$brand_html .= ( $before_label ? ( '<span>' . esc_html__( 'Brand: ', 'alpha' ) ) : '' ) . '<a href="' . esc_url( get_term_link( $brand_id, 'product_brand' ) ) . '" title="' . esc_attr( $brand_name ) . '">' . esc_html( $brand_name ) . '</a>' . ( $before_label ? '</span>' : '' ) . $comma;
				}
			}
		}
		return array(
			'html'      => $brand_html,
			'has_image' => $has_brand_image,
		);
	}
}