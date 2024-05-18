<?php
/**
 * Alpha WooCommerce Product Loop Functions
 *
 * Functions used to display product loop.
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */

defined( 'ABSPATH' ) || die;

// Product Loop Media
add_action( 'woocommerce_before_shop_loop_item', 'alpha_product_loop_figure_open', 5 );

// Compatiblilty with elementor editor
if ( ( ! empty( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] && is_admin() ) || ( ! empty( $_REQUEST['legacy-widget-preview'] ) ) ) {
	add_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
}

// Product Loop Media - Anchor Tag
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
add_action( 'woocommerce_before_shop_loop_item_title', 'alpha_product_loop_hover_thumbnail' );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 15 );
add_filter( 'single_product_archive_thumbnail_size', 'alpha_single_product_archive_thumbnail_size' );

// Product Loop Media - Labels and Actions
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 20 ); // Label
add_action( 'woocommerce_before_shop_loop_item_title', 'alpha_product_loop_vertical_action', 20 ); // Vertical action
add_action( 'woocommerce_before_shop_loop_item_title', 'alpha_product_loop_media_action', 20 ); // Media Action
add_action( 'woocommerce_before_shop_loop_item_title', 'alpha_product_loop_count_deal', 30 ); // Vertical action
add_action( 'woocommerce_before_shop_loop_item_title', 'alpha_product_loop_figure_close', 40 );

// Product Loop Details
add_action( 'woocommerce_before_shop_loop_item_title', 'alpha_product_loop_details_open', 50 );
add_action( 'alpha_shop_loop_item_categories', 'alpha_shop_loop_item_categories' );
add_action( 'alpha_shop_loop_item_categories', 'alpha_product_loop_default_wishlist_action', 15 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
add_action( 'woocommerce_shop_loop_item_title', 'alpha_wc_template_loop_product_title' );
add_action( 'woocommerce_after_shop_loop_item_title', 'alpha_product_loop_action', 30 );
// add_action( 'woocommerce_after_shop_loop_item_title', 'alpha_product_loop_count', 40 );
add_action( 'woocommerce_after_shop_loop_item', 'alpha_product_loop_details_close', 15 );
add_filter( 'woocommerce_product_get_rating_html', 'alpha_get_rating_html', 10, 3 );

// show extra info
add_action( 'woocommerce_after_shop_loop_item_title', 'alpha_get_extra_info_html', 15 );

// Remove default AddToCart
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

// Change order of del and ins tag
add_filter( 'woocommerce_format_sale_price', 'alpha_wc_format_sale_price', 10, 3 );

// Remove default YITH loop positions
if ( defined( 'YITH_WCWL' ) ) {
	add_filter( 'yith_wcwl_loop_positions', 'alpha_yith_wcwl_loop_positions' );
	add_filter( 'yith_wcwl_add_to_wishlist_params', 'alpha_yith_wcwl_add_btn_product_icon_params' );
}

// Add post
add_filter( 'shortcode_atts_products', 'alpha_wc_shortcode_product_add_exclude_attribute', 10, 3 );
add_filter( 'woocommerce_shortcode_products_query', 'alpha_wc_shortcode_product_add_exclude_arg', 10, 3 );

/**
 * Alpha Product Loop Media Functions
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_product_loop_figure_open' ) ) {
	function alpha_product_loop_figure_open() {
		echo '<figure class="product-media">';
	}
}

/**
 * Product loop figure close
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_product_loop_figure_close' ) ) {
	function alpha_product_loop_figure_close() {
		echo '</figure>';
	}
}

/**
 * Product loop hover thumbnail
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_product_loop_hover_thumbnail' ) ) {
	function alpha_product_loop_hover_thumbnail() {
		if ( alpha_get_option( 'hover_change' ) ) {
			$gallery = get_post_meta( get_the_ID(), '_product_image_gallery', true );
			if ( ! empty( $gallery ) ) {
				$gallery = explode( ',', $gallery );
				if ( ! empty( $gallery[0] ) ) {
					$attachment_image = wp_get_attachment_image(
						$gallery[0],
						alpha_wc_get_loop_prop( 'hover_thumbnail_size' ),
						false
					);
					/**
					 * Filters the html of product hover image.
					 *
					 * @since 1.0
					 */
					echo apply_filters( 'alpha_product_hover_image_html', $attachment_image );
				}
			}
		}
	}
}

/**
 * The single product archive thumbnail size.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_single_product_archive_thumbnail_size' ) ) {
	function alpha_single_product_archive_thumbnail_size( $size ) {
		$new_size = $size;
		if ( isset( $GLOBALS['alpha_current_product_img_size'] ) ) {
			$new_size = $GLOBALS['alpha_current_product_img_size'];
			unset( $GLOBALS['alpha_current_product_img_size'] );
		} else {
			$new_size = alpha_wc_get_loop_prop( 'thumbnail_size', $size );
		}
		if ( 'custom' != $new_size ) {
			$size = $new_size;
		}
		wc_set_loop_prop( 'hover_thumbnail_size', $size );
		return $size;
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

			if ( alpha_get_option( 'compare_available' ) ) {
				ob_start();
				alpha_product_compare( ' btn-product-icon' );
				$html .= ob_get_clean();
			}
			if ( '' == alpha_wc_get_loop_prop( 'quickview_pos' ) ) {
				global $product;

				$html .= '<button class="btn-product-icon btn-quickview" data-mfp-src="' . alpha_get_product_featured_image_src( $product ) . '" data-product="' . $product->get_id() . '" title="' . esc_attr__( 'Quick View', 'alpha' ) . '">' . esc_html__( 'Quick View', 'alpha' ) . '</button>';
			}

			if ( $html ) {
				echo '<div class="product-action-vertical">' . alpha_escaped( $html ) . '</div>';
			}
		}
	}
}

/**
 * The product loop media action
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_product_loop_media_action' ) ) {
	function alpha_product_loop_media_action() {

		global $product;

		if ( 'bottom' == alpha_wc_get_loop_prop( 'addtocart_pos' ) ) {
			if ( 'bottom' == alpha_wc_get_loop_prop( 'quickview_pos' ) ) {
				echo '<div class="product-action action-panel">';
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

				if ( defined( 'YITH_WCWL' ) ) {
					echo do_shortcode( '[yith_wcwl_add_to_wishlist container_classes="btn-product-icon"]' );
				}

				if ( alpha_get_option( 'compare_available' ) ) {
					echo alpha_product_compare( ' btn-product-icon' );
				}

				global $product;
				echo '<button class="btn-product-icon btn-quickview" data-mfp-src="' . alpha_get_product_featured_image_src( $product ) . '" data-product="' . $product->get_id() . '" title="' . esc_attr__( 'Quick View', 'alpha' ) . '">' . esc_html__( 'Quick View', 'alpha' ) . '</button>';

			} else {
				echo '<div class="product-action">';
				woocommerce_template_loop_add_to_cart(
					array(
						'class' => implode(
							' ',
							array_filter(
								array(
									'btn-product',
									'product_type_' . $product->get_type(),
									$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
									$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
								)
							)
						),
					)
				);
			}
			echo '</div>';
		} elseif ( 'bottom' == alpha_wc_get_loop_prop( 'quickview_pos' ) ) {
			global $product;
			echo '<div class="product-action"><button class="btn-product btn-quickview" data-mfp-src="' . alpha_get_product_featured_image_src( $product ) . '" data-product="' . $product->get_id() . '" title="' . esc_attr__( 'Quick View', 'alpha' ) . '">' . esc_html__( 'Quick View', 'alpha' ) . '</button></div>';
		}
	}
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
					wp_enqueue_style( 'alpha-countdown', alpha_core_framework_uri( '/widgets/countdown/countdown' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_CORE_VERSION );
					wp_enqueue_script( 'alpha-countdown', alpha_core_framework_uri( '/widgets/countdown/countdown' . ALPHA_JS_SUFFIX ), array( 'jquery-countdown' ), ALPHA_CORE_VERSION, true );
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
 * Alpha Product Loop Details Functions
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_product_loop_details_open' ) ) {
	function alpha_product_loop_details_open() {
		echo '<div class="product-details">';
	}
}

/**
 * The product loop details close
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_product_loop_details_close' ) ) {
	function alpha_product_loop_details_close() {
		echo '</div>';
	}
}

/**
 * The wc template loop product title
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_template_loop_product_title' ) ) {
	function alpha_wc_template_loop_product_title() {
		echo '<h3 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title', 'product-title' ) ) . '">';
		echo '<a href="' . esc_url( get_the_permalink() ) . '">' . alpha_strip_script_tags( get_the_title() ) . '</a>';
		echo '</h3>';
	}
}

/**
 * The shop loop item categories.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_shop_loop_item_categories' ) ) {
	function alpha_shop_loop_item_categories() {
		$name = alpha_wc_get_loop_prop( 'name' );

		if ( 'related' != $name ) {
			global $product;
			echo '<div class="product-cat">' . wc_get_product_category_list( $product->get_id(), ', ', '' ) . '</div>';
		}
	}
}

/**
 * The product loop default wishlist action
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_product_loop_default_wishlist_action' ) ) {
	function alpha_product_loop_default_wishlist_action() {
		if ( defined( 'YITH_WCWL' ) && 'with_title' == alpha_wc_get_loop_prop( 'wishlist_pos' ) ) {
			echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
		}
	}
}

/**
 * The product loop action
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_product_loop_action' ) ) {
	function alpha_product_loop_action( $details = '' ) {
		global $product;
		$product_type = alpha_wc_get_loop_prop( 'product_type' );
		/**
		 * Filters the action of product.
		 *
		 * @since 1.0
		 */
		$exclude_type = apply_filters( 'alpha_product_loop_action', array( 'widget' ) );
		if ( ! in_array( $product_type, $exclude_type ) ) {
			if ( 'detail_bottom' == alpha_wc_get_loop_prop( 'addtocart_pos' ) ) {
				echo '<div class="product-action">';
				woocommerce_template_loop_add_to_cart(
					array(
						'class' => implode(
							' ',
							array_filter(
								array(
									'btn-product',
									'product_type_' . $product->get_type(),
									$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
									$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
								)
							)
						),
					)
				);
				echo '</div>';
			} elseif ( 'with_qty' == alpha_wc_get_loop_prop( 'addtocart_pos' ) ) {
				echo '<div class="product-action">';

				if ( 'simple' == $product->get_type() ) {
					woocommerce_quantity_input(
						array(
							'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
							'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
							'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( sanitize_text_field( wp_unslash( $_POST['quantity'] ) ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
						)
					);
				}

				woocommerce_template_loop_add_to_cart(
					array(
						'class' => implode(
							' ',
							array_filter(
								array(
									'btn-product',
									'product_type_' . $product->get_type(),
									$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
									$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
								)
							)
						),
					)
				);
				echo '</div>';
			}
		}
	}
}

/**
 * The product loop count
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_product_loop_count' ) ) {
	function alpha_product_loop_count() {
		$html          = '';
		$show_progress = alpha_wc_get_loop_prop( 'show_progress', '' );
		$count_text    = alpha_wc_get_loop_prop( 'count_text', '' );

		if ( $show_progress && $count_text ) {
			global $product;
			$sales = $product->get_total_sales();
			$stock = $product->get_stock_quantity();
			$total = $sales + $stock;

			if ( $total && $show_progress ) {
				?>
				<div class="count-progress"><div class="count-now" style="width:<?php echo intval( $sales * 100 / $total ); ?>%;"></div></div>
				<?php
			}

			if ( $count_text ) {
				?>
				<div class="count-text">
					<?php
					echo alpha_strip_script_tags(
						apply_filters(
							'alpha_product_loop_quantity_text',
							$stock ? sprintf( $count_text, $sales, $total ) : rtrim( sprintf( $count_text, $sales, '', $total ), '/' ),
							$product,
							$sales,
							$stock
						)
					);
					?>
				</div>
				<?php
			}
		}
	}
}

/**
 * Get the rating html.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_rating_html' ) ) {
	function alpha_get_rating_html( $html, $rating, $count ) {
		if ( 0 == $rating ) {
			/* translators: %s: rating */
			$label = sprintf( esc_html__( 'Rated %s out of 5', 'alpha' ), $rating );
			$html  = '<div class="star-rating" role="img" aria-label="' . esc_attr( $label ) . '">' . wc_get_star_rating_html( $rating, $count ) . '</div>';
		}
		return $html;
	}
}

/**
 * Get the rating link html.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_rating_link_html' ) ) {
	function alpha_get_rating_link_html( $product ) {
		return '<a href="' . esc_url( get_the_permalink( $product->get_id() ) ) . '#reviews" class="woocommerce-review-link scroll-to" rel="nofollow">(' . $product->get_review_count() . ' ' . esc_html__( 'reviews', 'alpha' ) . ')</a>';
	}
}


/**
 * Change order of del and ins tag.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_format_sale_price' ) ) {
	function alpha_wc_format_sale_price( $price, $regular_price, $sale_price ) {
		return '<ins>' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins> <del>' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</del>';
	}
}

/**
 * Remove default YITH loop positions
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_yith_wcwl_loop_positions' ) ) {
	function alpha_yith_wcwl_loop_positions( $positions ) {
		$positions['before_image']['hook']     = '';
		$positions['before_image']['priority'] = 10;
		return $positions;
	}
}

/**
 * Add the wishlist icon class name.
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_yith_wcwl_add_btn_product_icon_params' ) ) {
	function alpha_yith_wcwl_add_btn_product_icon_params( $args ) {
		$args['container_classes'] .= ' btn-product-icon';
		return $args;
	}
}

/**
 * Alpha product compare function
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_product_compare' ) ) {
	function alpha_product_compare( $extra_class = '' ) {
		if ( ! class_exists( 'Alpha_Product_Compare' ) ) {
			return;
		}

		global $product;

		$css_class  = 'compare' . ( $extra_class ? ' ' . trim( $extra_class ) : '' );
		$product_id = $product->get_id();
		$url        = '#';

		if ( Alpha_Product_Compare::get_instance()->is_compared_product( $product_id ) ) {
			$url        = get_permalink( wc_get_page_id( 'compare' ) );
			$css_class .= ' added';
			/**
			 * Filters the added label of woocompare.
			 *
			 * @since 1.0
			 */
			$button_text = apply_filters( 'alpha_woocompare_added_label', esc_html__( 'Added', 'alpha' ) );
		} else {
			/**
			 * Filters the add label of woocompare.
			 *
			 * @since 1.0
			 */
			$button_text = apply_filters( 'alpha_woocompare_add_label', esc_html__( 'Compare', 'alpha' ) );
		}

		printf( '<a href="%s" class="%s" title="%s" data-product_id="%d" data-added-text="%s"></a>', esc_url( $url ), esc_attr( $css_class ), esc_html( $button_text ), $product_id, esc_html( apply_filters( 'alpha_woocompare_added_label', esc_html__( 'Added', 'alpha' ) ) ) );
	}
}

/**
 * Get product extra info html
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_get_extra_info_html' ) ) {
	function alpha_get_extra_info_html() {
		$extra_info = get_post_meta( get_the_ID(), 'alpha_extra_info', true );
		if ( ! empty( $extra_info ) ) {
			printf( '<div class="product-extra-info">(%s)</div>', $extra_info );
		}
	}
}

/**
 * Add exclude attribute to products shortcode.
 *
 * @since 1.0
 *
 * @param array  $out       The output array of shortcode attributes.
 * @param array  $pairs     The supported attributes and their defaults.
 * @param array  $atts      The user defined shortcode attributes.
 */
function alpha_wc_shortcode_product_add_exclude_attribute( $out, $pairs, $atts ) {
	if ( isset( $atts['exclude'] ) ) {
		$out['exclude'] = $atts['exclude'];
	}
	return $out;
}

/**
 * Add exclude arg to woocommerce shortcode product.
 *
 * @since 1.0
 * @param array $query_args
 * @param array $attributes
 * @param string $type
 * @return array $query_args
 */
function alpha_wc_shortcode_product_add_exclude_arg( $query_args, $attributes, $type ) {

	if ( ! empty( $attributes['exclude'] ) ) {
		$query_args['post__not_in'] = array_map( 'trim', explode( ',', $attributes['exclude'] ) );
	}
	return $query_args;
}
foreach ( apply_filters(
	'alpha_product_loop_types',
	array(
		'default' => true,
	),
	'hooks'
) as $key => $value ) {
	if ( $key && $value ) {
		require_once alpha_framework_path( ALPHA_FRAMEWORK_PLUGINS . "/woocommerce/product-loop/product-loop-{$key}.php" );
	}
}

/**
 * Fires after setting product single actions and filters.
 *
 * Here you can remove and add more actions and filters.
 *
 * @since 1.0
 */
do_action( 'alpha_after_product_loop_hooks' );
