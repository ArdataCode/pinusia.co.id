<?php
/**
 * Result Count
 *
 * Shows text: Showing x - x of x results.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/result-count.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     3.7.0
 */

defined( 'ABSPATH' ) || die;

global $alpha_layout;

$ts = isset( $alpha_layout['top_sidebar'] ) && isset( $alpha_layout['top_sidebar']['slug'] ) && is_active_sidebar( $alpha_layout['top_sidebar']['slug'] );

if ( 1 == $total ) {
	echo '<p class="woocommerce-result-count show-info">';
	esc_html_e( 'Showing the single result', 'alpha' );
	echo '</p>';
} elseif ( $total <= $per_page || -1 == $per_page ) {
	echo '<p class="woocommerce-result-count show-info">';
	/* translators: %d: total results */
	printf( esc_html( _n( 'Showing %1$sall %2$d%3$s Product', 'Showing %1$sall %2$d%3$s Products', $total, 'alpha' ) ), '<span>', $total, '</span>' );
	echo '</p>';
} else {
	echo '<p class="woocommerce-result-count show-info">';
	$first = ( $per_page * $current ) - $per_page + 1;
	$last  = min( $total, $per_page * $current );
	/* translators: 1: first result 2: last result 3: total results */
	printf( esc_html( _nx( 'Showing %4$s%1$d&ndash;%2$d of %3$d%5$s Product', 'Showing %4$s%1$d&ndash;%2$d of %3$d%5$s Products', $total, 'with first and last result', 'alpha' ) ), $first, $last, $total, '<span>', '</span>' );
	echo '</p>';
}
