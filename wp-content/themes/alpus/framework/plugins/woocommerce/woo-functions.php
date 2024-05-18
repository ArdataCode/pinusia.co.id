<?php
/**
 * Alpha WooCommerce Functions
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;


// Woocommerce Comment Form
// add_filter( 'woocommerce_product_review_comment_form_args', 'alpha_comment_form_args' );

// Woocommerce Mini Cart
add_filter( 'woocommerce_add_to_cart_fragments', 'alpha_wc_add_to_cart_fragment' );
add_filter( 'woocommerce_cart_item_name', 'alpha_wc_cart_item_name', 10, 4 );
add_action( 'wp_ajax_alpha_cart_item_remove', 'alpha_wc_cart_item_remove' );
add_action( 'wp_ajax_nopriv_alpha_cart_item_remove', 'alpha_wc_cart_item_remove' );
add_action( 'wp_ajax_alpha_add_to_cart', 'alpha_wc_add_to_cart' );
add_action( 'wp_ajax_nopriv_alpha_add_to_cart', 'alpha_wc_add_to_cart' );

// Alpha Ajax Add to Cart in Quickview and Single Product Widget
add_action( 'wp_ajax_alpha_ajax_add_to_cart', 'alpha_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_alpha_ajax_add_to_cart', 'alpha_ajax_add_to_cart' );

// Woocommerce Breadcrumb
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );

// Woocommerce Notice Skin
add_filter( 'wc_add_to_cart_message_html', 'alpha_wc_add_to_cart_message_html' );
add_filter( 'alpha_wc_notice_class', 'alpha_wc_notice_class', 10, 3 );
add_action( 'alpha_wc_before_notice', 'alpha_wc_notice_action', 10, 2 );
add_action( 'alpha_wc_after_notice', 'alpha_wc_notice_close', 10, 2 );

// Woocommerce Checkout Page
add_filter( 'woocommerce_default_address_fields', 'alpha_wc_address_fields_change_form_row' );
add_filter( 'woocommerce_billing_fields', 'alpha_wc_billing_fields_change_form_row' );
add_filter( 'woocommerce_form_field_args', 'alpha_wc_form_field_args' );

// Woocommerce Cart Page
// change position of cross sell product
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );
add_action( 'template_redirect', 'alpha_clear_cart_action' );

// Change columns and total of cross sell
add_filter( 'woocommerce_cross_sells_columns', 'alpha_cross_sell_columns' );
add_filter( 'woocommerce_cross_sells_total', 'alpha_cross_sell_products_count' );

add_filter( 'woocommerce_formatted_address_force_country_display', 'alpha_formatted_address_force_country_display' );
add_filter( 'woocommerce_formatted_address_replacements', 'alpha_formatted_address_replacements', 10, 2 );

// My Accont Page
add_action( 'woocommerce_save_account_details', 'alpha_wc_save_account_description' );
add_filter( 'woocommerce_account_menu_items', 'alpha_woocommerce_account_menu_items' );

// YITH Wishlist Page
add_filter( 'yith_wcwl_edit_title_icon', 'alpha_yith_wcwl_edit_title_icon' );
add_filter( 'yith_wcwl_wishlist_params', 'alpha_yith_wcwl_wishlist_params', 10, 5 );

// YITH Mini Wishlist
add_action( 'wp_ajax_alpha_update_mini_wishlist', 'alpha_yith_update_mini_wishlist' );
add_action( 'wp_ajax_nopriv_alpha_update_mini_wishlist', 'alpha_yith_update_mini_wishlist' );

// YITH Wishlist Remove Notice
if ( class_exists( 'WooCommerce' ) && defined( 'YITH_WCWL' ) ) {
	add_action( 'wp_ajax_remove_from_wishlist', 'alpha_yith_wcwl_before_remove_notice', 3 );
	add_action( 'wp_ajax_nopriv_remove_from_wishlist', 'alpha_yith_wcwl_before_remove_notice', 3 );
	add_action( 'wp', 'alpha_yith_wcwl_remove_notice' );
	add_action( 'wp_ajax_alpha_account_form', 'alpha_yith_wcwl_remove_notice', 5 );
	add_action( 'wp_ajax_nopriv_alpha_account_form', 'alpha_yith_wcwl_remove_notice', 5 );
}

// YITH ajax filter
add_filter( 'yith_wcan_list_type_empty_filter_class', 'alpha_yith_empty_filter_class' );
add_filter( 'yith_wcwl_localize_script', 'alpha_yith_wcwl_localize_script' );

// Add recently viewed products
remove_action( 'template_redirect', 'wc_track_product_view', 20 );
add_action( 'template_redirect', 'alpha_wc_track_product_view', 20 );

// Shop before Actions
add_action( 'alpha_before_shop_loop_start', 'alpha_before_shop_loop_start' );

/**
 * Alpha Woocommerce Mini Cart Functions
 */
if ( ! function_exists( 'alpha_wc_add_to_cart_fragment' ) ) {
	function alpha_wc_add_to_cart_fragment( $fragments ) {
		$_cart_total                           = WC()->cart->get_cart_subtotal();
		$fragments['.cart-toggle .cart-price'] = '<span class="cart-price">' . $_cart_total . '</span>';
		$_cart_qty                             = WC()->cart->cart_contents_count;
		$_cart_qty                             = ( $_cart_qty > 0 ? $_cart_qty : '0' );
		$fragments['.cart-toggle .cart-count'] = '<span class="cart-count">' . ( (int) $_cart_qty ) . '</span>';
		return $fragments;
	}
}

if ( ! function_exists( 'alpha_wc_add_to_cart' ) ) {
	/**
	 * AJAX add to cart.
	 */
	function alpha_wc_add_to_cart() {
		ob_start();

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['product_id'] ) ) {
			return;
		}

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$product           = wc_get_product( $product_id );
		$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status    = get_post_status( $product_id );
		$variation_id      = 0;
		$variation         = array();

		if ( $product && 'variation' === $product->get_type() ) {
			$variation_id = $product_id;
			$product_id   = $product->get_parent_id();
			$variation    = $product->get_variation_attributes();
			if ( ! empty( $variation ) ) {
				foreach ( $variation as $k => $v ) {
					if ( empty( $v ) && ! empty( $_REQUEST[ $k ] ) ) {
						$variation[ $k ] = wp_unslash( $_REQUEST[ $k ] );
					}
				}
			}
		}

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity ), true );
			}

			WC_AJAX::get_refreshed_fragments();

		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors.
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);

			wp_send_json( $data );
		}
		// phpcs:enable
	}
}

if ( ! function_exists( 'alpha_wc_cart_item_name' ) ) {
	function alpha_wc_cart_item_name( $name, $cart_item, $cart_item_key ) {
		if ( $cart_item['data']->is_type( 'variation' ) && is_array( $cart_item['variation'] ) ) {
			$first = true;
			$link  = false;
			foreach ( $cart_item['variation'] as $attr_name => $value ) {
				$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $attr_name ) ) );
				if ( taxonomy_exists( $taxonomy ) ) {
					// If this is a term slug, get the term's nice name.
					$term = get_term_by( 'slug', $value, $taxonomy );
					if ( ! is_wp_error( $term ) && $term && $term->name ) {
						$value = $term->name;
					}
				} else {
					// If this is a custom option slug, get the options name.
					$value = apply_filters( 'woocommerce_variation_option_name', $value, null, $taxonomy, $cart_item['data'] );
				}
				// Check the nicename against the title.
				if ( $value && ! wc_is_attribute_in_product_name( $value, $cart_item['data']->get_name() ) ) {
					if ( $first ) {
						if ( false !== strpos( $name, '</a>' ) ) {
							$link = true;
							$name = str_replace( '</a>', '', $name );
						}
						$name .= ' - ' . $value;
						$first = false;
					} else {
						$name .= ', ' . $value;
					}
				}
			}
			if ( $link ) {
				$name .= '</a>';
			}
		}
		$name = '<span>' . $name . '</span>';
		return $name;
	}
}

if ( ! function_exists( 'alpha_wc_cart_item_remove' ) ) {
	function alpha_wc_cart_item_remove() {
		//check_ajax_referer( 'alpha-nonce', 'nonce' );
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		$cart         = WC()->instance()->cart;
		$cart_id      = sanitize_text_field( $_POST['cart_id'] );
		$cart_item_id = $cart->find_product_in_cart( $cart_id );
		if ( $cart_item_id ) {
			$cart->set_quantity( $cart_item_id, 0 );
		}
		$cart_ajax = new WC_AJAX();
		$cart_ajax->get_refreshed_fragments();
		// phpcs:enable
		exit();
	}
}


if ( ! function_exists( 'alpha_ajax_add_to_cart' ) ) {

	/**
	 * Alpha Ajax addtocart feature
	 *
	 * @since 1.0
	 */
	function alpha_ajax_add_to_cart() {

		ob_start();

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['product_id'] ) ) {
			return;
		}

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$product           = wc_get_product( $product_id );
		$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status    = get_post_status( $product_id );
		$variation_id      = 0;
		$variation         = array();

		if ( $product && 'variation' === $product->get_type() ) {
			$variation_id = $product_id;
			$product_id   = $product->get_parent_id();
			$variation    = $product->get_variation_attributes();
			if ( ! empty( $variation ) ) {
				foreach ( $variation as $k => $v ) {
					if ( empty( $v ) && ! empty( $_REQUEST[ $k ] ) ) {
						$variation[ $k ] = wp_unslash( $_REQUEST[ $k ] );
					}
				}
			}
		}

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity ), true );
			}

			WC_AJAX::get_refreshed_fragments();

		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors.
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);

			wp_send_json( $data );
		}
	// phpcs:enable
	}
}

/**
 * Woocommerce Notice Skin
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_add_to_cart_message_html' ) ) {
	function alpha_wc_add_to_cart_message_html( $message ) {
		return str_replace( 'button wc-forward', 'btn btn-success btn-md', $message );
	}
}
if ( ! function_exists( 'alpha_wc_notice_class' ) ) {
	function alpha_wc_notice_class( $class, $notice, $type ) {

		if ( strpos( $notice['notice'], 'btn' ) ) {
			$class .= ' alert alert-simple alert-btn alert-' . ( 'error' == $type ? 'danger' : esc_attr( $type ) );
		} else {
			$class .= ' alert alert-simple alert-icon alert-close-top alert-' . ( 'error' == $type ? 'danger' : esc_attr( $type ) );
		}

		return $class;
	}
}
if ( ! function_exists( 'alpha_wc_notice_action' ) ) {
	function alpha_wc_notice_action( $notice, $type ) {
		if ( ! strpos( $notice['notice'], 'btn' ) ) {
			if ( 'success' == $type ) {
				echo '<i class="fas fa-check"></i>';
			} elseif ( 'notice' == $type ) {
				echo '<i class="fas fa-exclamation-circle"></i>';
			} elseif ( 'error' == $type ) {
				echo '<i class="fas fa-exclamation-triangle"></i>';
			}
		}
	}
}
if ( ! function_exists( 'alpha_wc_notice_close' ) ) {
	function alpha_wc_notice_close() {
		echo '<button type="button" class="btn btn-link btn-close"><i class="close-icon"></i></button>';
	}
}

/**
 * Alpha Woocommerce Checkout Page Functions
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_wc_address_fields_change_form_row' ) ) {
	function alpha_wc_address_fields_change_form_row( $fields ) {
		if ( ! is_cart() ) {
			$fields['city']['class']     = array( 'form-row-first', 'address-field' );
			$fields['state']['class']    = array( 'form-row-last', 'address-field' );
			$fields['postcode']['class'] = array( 'form-row-first', 'address-field' );
		}
		return $fields;
	}
}

if ( ! function_exists( 'alpha_wc_billing_fields_change_form_row' ) ) {
	function alpha_wc_billing_fields_change_form_row( $fields ) {
		if ( ! is_cart() ) {
			$fields['billing_phone']['class'] = array( 'form-row-last' );
		}
		return $fields;
	}
}

if ( ! function_exists( 'alpha_wc_form_field_args' ) ) {
	function alpha_wc_form_field_args( $args ) {
		$args['custom_attributes']['rows'] = 5;
		return $args;
	}
}


/**
 * Alpha Woocommerce Cart Page Functions
 */

/**
 * Alpha YITH Wishlist Page Functions
 */
if ( ! function_exists( 'alpha_yith_wcwl_edit_title_icon' ) ) {
	function alpha_yith_wcwl_edit_title_icon( $icon ) {
		return '<i class="fas fa-pencil-alt"></i>';
	}
}

if ( ! function_exists( 'alpha_yith_wcwl_wishlist_params' ) ) {
	function alpha_yith_wcwl_wishlist_params( $additional_params, $action, $action_params, $pagination, $per_page ) {
		$social_shares = alpha_get_social_shares();

		$additional_params['share_atts']['share_facebook_icon']  = '<i class="' . $social_shares['facebook']['icon'] . '"></i>';
		$additional_params['share_atts']['share_twitter_icon']   = '<i class="' . $social_shares['twitter']['icon'] . '"></i>';
		$additional_params['share_atts']['share_pinterest_icon'] = '<i class="' . $social_shares['pinterest']['icon'] . '"></i>';
		$additional_params['share_atts']['share_email_icon']     = '<i class="' . $social_shares['email']['icon'] . '"></i>';
		$additional_params['share_atts']['share_whatsapp_icon']  = '<i class="' . $social_shares['whatsapp']['icon'] . '"></i>';

		return $additional_params;
	}
}

if ( ! function_exists( 'alpha_yith_wcwl_localize_script' ) ) {
	function alpha_yith_wcwl_localize_script( $variables ) {
		$variables['labels']['added_to_cart_message'] = sprintf( '<div class="woocommerce-notices-wrapper"><div class="woocommerce-message alert alert-simple alert-icon alert-success" role="alert"><i class="fas fa-check"></i>%s<button type="button" class="btn btn-link btn-close"><i class="close-icon"></i></button></div></div>', apply_filters( 'yith_wcwl_added_to_cart_message', esc_html__( 'Product added to cart successfully', 'alpha' ) ) );
		return $variables;
	}
}

/**
 * YITH Wishlist Remove Notice
 *
 * @since 1.0.0
 * @since 1.2.0 Fixed notice error in wishlist page, when wishlist is removed in quickview.
 */
if ( ! function_exists( 'alpha_yith_wcwl_before_remove_notice' ) ) {
	function alpha_yith_wcwl_before_remove_notice() {
		if ( ! ( isset( $_REQUEST['context'] ) && 'frontend' == $_REQUEST['context'] ) && empty( $_REQUEST['is_quickview'] ) ) {
			wc_add_notice( 'alpha_yith_wcwl_before_remove_notice' );
		}
	}
}

if ( ! function_exists( 'alpha_yith_wcwl_remove_notice' ) ) {
	function alpha_yith_wcwl_remove_notice() {
		if ( WC()->session ) {
			$notices = WC()->session->get( 'wc_notices', array() );
			if ( ! empty( $notices['success'] ) ) {
				$cnt = count( $notices['success'] );

				for ( $i = 0; $i < $cnt; ++$i ) {
					if ( isset( $notices['success'][ $i ]['notice'] ) && 'alpha_yith_wcwl_before_remove_notice' == $notices['success'][ $i ]['notice'] ) {
						if ( $i < $cnt-- ) {
							array_splice( $notices['success'], $i, 1 );
							if ( $i < $cnt-- ) {
								array_splice( $notices['success'], $i, 1 );
							}
							-- $i;
						}
					}
				}

				WC()->session->set( 'wc_notices', $notices );
			}
		}
	}
}

/**
 * Alpha YITH Ajax Filter Functions
 */
if ( ! function_exists( 'alpha_yith_empty_filter_class' ) ) {
	function alpha_yith_empty_filter_class( $class ) {
		if ( empty( $class ) ) {
			return 'class="empty"';
		} else {
			return substr( $class, 0, -1 ) . ' empty' . "'";
		}
	}
}

/**
 * WooCommerce Horizontal Filter
 */
if ( ! function_exists( 'alpha_wc_shop_top_sidebar' ) ) {
	function alpha_wc_shop_top_sidebar() {
		$show_default_orderby    = 'menu_order' == apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
		$catalog_orderby_options = apply_filters(
			'woocommerce_catalog_orderby',
			array(
				'menu_order' => esc_html__( 'Default sorting', 'alpha' ),
				'popularity' => esc_html__( 'Sort by popularity', 'alpha' ),
				'rating'     => esc_html__( 'Sort by average rating', 'alpha' ),
				'date'       => esc_html__( 'Sort by latest', 'alpha' ),
				'price'      => esc_html__( 'Sort by price: low to high', 'alpha' ),
				'price-desc' => esc_html__( 'Sort by price: high to low', 'alpha' ),
			)
		);

		$default_orderby = alpha_wc_get_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', '' ) );
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$orderby = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : $default_orderby;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( alpha_wc_get_loop_prop( 'is_search' ) ) {
			$catalog_orderby_options = array_merge( array( 'relevance' => esc_html__( 'Relevance', 'alpha' ) ), $catalog_orderby_options );

			unset( $catalog_orderby_options['menu_order'] );
		}

		if ( ! $show_default_orderby ) {
			unset( $catalog_orderby_options['menu_order'] );
		}

		if ( ! wc_review_ratings_enabled() ) {
			unset( $catalog_orderby_options['rating'] );
		}

		if ( ! array_key_exists( $orderby, $catalog_orderby_options ) ) {
			$orderby = current( array_keys( $catalog_orderby_options ) );
		}

		wc_get_template(
			'loop/orderby.php',
			array(
				'catalog_orderby_options' => $catalog_orderby_options,
				'orderby'                 => $orderby,
				'show_default_orderby'    => $show_default_orderby,
			)
		);
	}
}

/**
 * Cart Page
 */
if ( ! function_exists( 'alpha_clear_cart_action' ) ) {
	/**
	 * Clear cart action
	 *
	 * @since 1.0
	 */
	function alpha_clear_cart_action() {
		if ( alpha_get_option( 'clear_cart_button' ) ) {
			return;
		}

		if ( ! empty( $_POST['clear_cart'] ) && wp_verify_nonce( wc_get_var( $_REQUEST['woocommerce-cart-nonce'] ), 'woocommerce-cart' ) ) {
			WC()->cart->empty_cart();
			wc_add_notice( esc_html__( 'Cart is cleared.', 'alpha' ) );

			$referer = wp_get_referer() ? remove_query_arg(
				array(
					'remove_item',
					'add-to-cart',
					'added-to-cart',
				),
				add_query_arg( 'cart_emptied', '1', wp_get_referer() )
			) : wc_get_cart_url();
			wp_safe_redirect( $referer );
			exit;
		}
	}
}

// return count of columns of cross sell products
if ( ! function_exists( 'alpha_cross_sell_columns' ) ) {
	function alpha_cross_sell_columns() {
		/**
		 * Filters the count of columns of cross sell products.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'alpha_cross_sell_columns', 4 );
	}
}

// return the total number of products of cross sell
if ( ! function_exists( 'alpha_cross_sell_products_count' ) ) {
	function alpha_cross_sell_products_count() {
		/**
		 * Filters the total number of products of cross sell.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'alpha_cross_sell_products_count', 4 );
	}
}


if ( ! function_exists( 'alpha_formatted_address_force_country_display' ) ) {
	function alpha_formatted_address_force_country_display() {
		return true;
	}
}

// change default address format with alpha's one
if ( ! function_exists( 'alpha_formatted_address_replacements' ) ) {
	function alpha_formatted_address_replacements( $replacements, $args ) {
		global $alpha_customer_address;

		$state        = $args['state'];
		$country      = $args['country'];
		$full_country = '';
		$full_state   = '';
		// @start feature: fs_plugin_woocommerce
		if ( class_exists( 'WooCommerce' ) ) {
			$countries = apply_filters( 'woocommerce_countries', include WC()->plugin_path() . '/i18n/countries.php' );
			$states    = apply_filters( 'woocommerce_states', include WC()->plugin_path() . '/i18n/states.php' );

			// Handle full country name.
			$full_country = ( isset( $countries[ $country ] ) ) ? $countries[ $country ] : $country;

			// Handle full state name.
			$full_state = ( $country && $state && isset( $states[ $country ][ $state ] ) ) ? $states[ $country ][ $state ] : $state;
		}
		// @end feature: fs_plugin_woocommerce

		$alpha_customer_address = array(
			__( 'Name', 'alpha' )     => sprintf(
				/* translators: 1: first name 2: last name */
				_x( '%1$s %2$s', 'full name', 'alpha' ),
				$args['first_name'],
				$args['last_name']
			),
			__( 'Company', 'alpha' )  => $args['company'],
			__( 'Address', 'alpha' )  => $args['address_1'] . ' ' . $args['address_2'],
			__( 'City', 'alpha' )     => isset( $full_state ) ? $args['city'] . ', ' . $full_state : $args['city'],
			__( 'Country', 'alpha' )  => $full_country,
			__( 'Postcode', 'alpha' ) => $args['postcode'],
			__( 'Phone', 'alpha' )    => isset( $args['phone'] ) ? $args['phone'] : '',
		);

		return array(
			'{first_name}'       => $args['first_name'],
			'{last_name}'        => $args['last_name'],
			'{name}'             => sprintf(
				/* translators: 1: first name 2: last name */
				_x( '%1$s %2$s', 'full name', 'alpha' ),
				$args['first_name'],
				$args['last_name']
			),
			'{company}'          => $args['company'],
			'{address_1}'        => $args['address_1'],
			'{address_2}'        => $args['address_2'],
			'{city}'             => $args['city'],
			'{state}'            => $full_state,
			'{postcode}'         => $args['postcode'],
			'{country}'          => $full_country,
			'{phone}'            => isset( $args['phone'] ) ? $args['phone'] : '',
			'{first_name_upper}' => wc_strtoupper( $args['first_name'] ),
			'{last_name_upper}'  => wc_strtoupper( $args['last_name'] ),
			'{name_upper}'       => wc_strtoupper(
				sprintf(
					/* translators: 1: first name 2: last name */
					_x( '%1$s %2$s', 'full name', 'alpha' ),
					$args['first_name'],
					$args['last_name']
				)
			),
			'{company_upper}'    => wc_strtoupper( $args['company'] ),
			'{address_1_upper}'  => wc_strtoupper( $args['address_1'] ),
			'{address_2_upper}'  => wc_strtoupper( $args['address_2'] ),
			'{city_upper}'       => wc_strtoupper( $args['city'] ),
			'{state_upper}'      => wc_strtoupper( $full_state ),
			'{state_code}'       => wc_strtoupper( $state ),
			'{postcode_upper}'   => wc_strtoupper( $args['postcode'] ),
			'{country_upper}'    => wc_strtoupper( $full_country ),
		);
	}
}

if ( ! function_exists( 'alpha_wc_save_account_description' ) ) {
	/**
	 * Update account description in save action of "My Account / Account Details" page.
	 *
	 * @since 1.0
	 * @see woocommerce_save_account_details
	 * @param int $user_ID User ID
	 */
	function alpha_wc_save_account_description( $user_ID ) {
		$description = ! empty( $_POST['user_description'] ) ? alpha_strip_script_tags( $_POST['user_description'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		update_user_meta( $user_ID, 'description', $description );
	}
}

if ( ! function_exists( 'alpha_woocommerce_account_menu_items' ) ) {
	/**
	 * Update my account menu items
	 *
	 * @since 1.1.0
	 * @see woocommerce_account_menu_items
	 * @param array $items
	 */
	function alpha_woocommerce_account_menu_items( $items ) {
		$has_logout = false;

		// Move customer logout to last
		if ( isset( $items['customer-logout'] ) ) {
			$has_logout = $items['customer-logout'];
			unset( $items['customer-logout'] );
		}

		// add wishlist
		if ( defined( 'YITH_WCWL' ) ) {
			$items['wishlist'] = esc_html__( 'Wishlist', 'alpha' );
		}

		if ( defined( 'ALPHA_VENDORS' ) ) {
			$items['vendor_dashboard'] = esc_html__( 'Vendor Dashboard', 'alpha' );
		}

		if ( $has_logout ) {
			$items['customer-logout'] = $has_logout;
		}

		return $items;
	}
}

if ( ! function_exists( 'alpha_wc_track_product_view' ) ) {
	/**
	 * Track recently viewed products even if recently viewed widget is not active.
	 *
	 * @since 1.0
	 * @see wc_track_product_view
	 */
	function alpha_wc_track_product_view() {
		if ( ! is_singular( 'product' ) ) {
			return;
		}

		global $post;

		$cookie_handle = 'woocommerce_recently_viewed_' . get_current_blog_id();

		if ( empty( $_COOKIE[ $cookie_handle ] ) ) { // @codingStandardsIgnoreLine.
			$viewed_products = array();
		} else {
			$viewed_products = wp_parse_id_list( (array) explode( '|', wp_unslash( $_COOKIE[ $cookie_handle ] ) ) ); // @codingStandardsIgnoreLine.
		}

		// Unset if already in viewed products list.
		$keys = array_flip( $viewed_products );

		if ( isset( $keys[ $post->ID ] ) ) {
			unset( $viewed_products[ $keys[ $post->ID ] ] );
		}

		$viewed_products[] = $post->ID;

		if ( count( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only.
		wc_setcookie( $cookie_handle, implode( '|', $viewed_products ) );
	}
}

if ( ! function_exists( 'alpha_yith_update_mini_wishlist' ) ) {
	/**
	 * alpha_yith_update_mini_wishlist
	 *
	 * update mini wishlit when product is added or removed
	 *
	 * @since 1.1.0
	 */
	function alpha_yith_update_mini_wishlist() {
		ob_start();

		if ( defined( 'ALPHA_HEADER_BUILDER' ) ) {
			$atts = array(
				'miniwishlist' => true,
				'show_count'   => true,
				'show_icon'    => true,
			);

			require alpha_core_framework_path( ALPHA_HEADER_BUILDER . '/widgets/wishlist/render-wishlist-elementor.php' );
		}

		wp_send_json( ob_get_clean() );
	}
}

if ( ! function_exists( 'alpha_get_product_featured_image_src' ) ) {
	/**
	 * alpha_get_product_featured_image_sr
	 *
	 * get url of featured image of product
	 *
	 * @since 1.0
	 * @param WC_Product $product
	 */
	function alpha_get_product_featured_image_src( $product ) {
		$image_id  = $product->get_image_id();
		$full_size = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
		$full_src  = wp_get_attachment_image_src( $image_id, $full_size );

		return ! empty( $full_src ) ? $full_src[0] : '';
	}
}

/**
 * Gets the shipping calculator template.
 */
if ( ! function_exists( 'woocommerce_shipping_calculator' ) ) {
	function woocommerce_shipping_calculator() {
		wc_get_template( 'cart/shipping-calculator.php' );
	}
}

if ( ! function_exists( 'alpha_before_shop_loop_start' ) ) {
	/**
	 * Before shop loop start.
	 *
	 * @since 1.2.0
	 */
	function alpha_before_shop_loop_start() {
		wp_enqueue_script( 'alpha-woocommerce' );
	}
}
