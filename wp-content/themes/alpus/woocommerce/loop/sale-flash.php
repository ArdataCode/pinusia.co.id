<?php
/**
 * Product loop sale flash
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/sale-flash.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     1.6.4
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// If product type is list widget, return
if ( 'widget' == alpha_wc_get_loop_prop( 'product_type' ) ) {
	return;
}

// Show labels only when show information is enabled

$html = '';

// Show enabled labels only.
$show_labels = alpha_wc_get_loop_prop( 'show_labels', array( 'hot', 'stock', 'sale', 'new' ) );

if ( is_array( $show_labels ) && count( $show_labels ) ) {

	global $post, $product;

	// New label
	if ( in_array( 'new', $show_labels ) && strtotime( $product->get_date_created() ) > strtotime( '-' . (int) alpha_get_option( 'new_product_period' ) . ' day' ) ) {
		$html .= '<label class="product-label label-new">' . esc_html__( 'New', 'alpha' ) . '</label>';
	}

	// Featured label
	if ( $product->is_featured() && in_array( 'hot', $show_labels ) ) {
		$html .= '<label class="product-label label-featured">' . esc_html__( 'Hot', 'alpha' ) . '</label>';
	}

	// Sale label
	if ( $product->is_on_sale() && in_array( 'sale', $show_labels ) ) {
		$percentage = 0;
		if ( $product->get_regular_price() ) {
			$percentage = round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
		} elseif ( 'variable' == $product->get_type() && $product->get_variation_regular_price() ) {
			$percentage = round( ( ( $product->get_variation_regular_price() - $product->get_variation_sale_price() ) / $product->get_variation_regular_price() ) * 100 );
		}

		if ( $percentage ) {
			/* translators: %d represents sale rate. */
			$html .= '<label class="product-label label-sale">' . sprintf( esc_html__( '-%d%%', 'alpha' ), $percentage ) . '</label>';
		}
	}

	// Out of stock label
	if ( in_array( 'stock', $show_labels ) && 'outofstock' == $product->get_stock_status() ) {
		$html .= '<label class="product-label label-stock">' . esc_html__( 'Out', 'alpha' ) . '</label>';
	}
}

// Custom Label
$custom_labels = get_post_meta( get_the_ID(), 'alpha_custom_labels', true );

if ( is_array( $custom_labels ) ) {
	foreach ( $custom_labels as $custom_label ) {
		if ( $custom_label['type'] ) {
			if ( isset( $custom_label['img_id'] ) ) {
				$html .= '<label class="product-label label-img">';

				$att_id = $custom_label['img_id'];
				$html  .= wp_get_attachment_image( $att_id );

				$html .= '</label>';
			}
		} else {
			$style_escaped = ' style="';
			if ( ! empty( $custom_label['color'] ) ) {
				$style_escaped .= 'color:' . esc_attr( $custom_label['color'] ) . ';';
			} else {
				$style_escaped .= 'color: #fff;';
			}
			if ( ! empty( $custom_label['bgColor'] ) ) {
				$style_escaped .= 'background-color:' . esc_attr( $custom_label['bgColor'] );
			} else {
				$style_escaped .= 'background-color: ' . esc_attr( alpha_get_option( 'primary_color' ) );
			}
			$style_escaped .= '"';
			$html          .= '<label class="product-label"' . $style_escaped . '>';
			$html          .= alpha_strip_script_tags( $custom_label['label'] );
			$html          .= '</label>';
		}
	}
}


// Finally, print labels
if ( $html ) {
	/**
	 * Filters the class of product label group.
	 *
	 * @since 1.0
	 */
	$html = '<div class="product-label-group' . esc_attr( apply_filters( 'alpha_product_label_group_class', '' ) ) . '">' . $html . '</div>';

	echo apply_filters( 'woocommerce_sale_flash', $html, $post, $product );
}
