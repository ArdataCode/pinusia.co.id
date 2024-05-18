<?php
/**
 * Single Product Custom Tab
 *
 * @author D-THEMES
 * @package WP Alpha Framework
 * @subpackage Theme
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

if ( isset( $tab_name ) ) {

	if ( 'alpha_product_tab' == $tab_name ) {
		$tab_title = alpha_get_option( 'product_tab_title' );
		$tab_block = alpha_get_option( 'product_tab_block' );
		/**
		 * Filters the tab title of product.
		 *
		 * @since 1.0
		 */
		echo apply_filters( 'alpha_product_tab_title', ( $tab_title ? ( '<h2>' . esc_html( $tab_title ) . '</h2>' ) : '' ), $tab_name );
		if ( ! empty( $tab_block ) ) {
			alpha_print_template( $tab_block );
		}
	}
	if ( 'alpha_custom_tab_1st' == $tab_name ) {
		$tab_title = get_post_meta( get_the_ID(), 'alpha_custom_tab_title_1st', true );
		if ( $tab_title ) {
			$tab_content = get_post_meta( get_the_ID(), 'alpha_custom_tab_content_1st', true );
			echo '<h2>' . esc_html( $tab_title ) . '</h2>';
			echo alpha_strip_script_tags( $tab_content );
		}
	} elseif ( 'alpha_custom_tab_2nd' == $tab_name ) {
		$tab_title = get_post_meta( get_the_ID(), 'alpha_custom_tab_title_2nd', true );
		if ( $tab_title ) {
			$tab_content = get_post_meta( get_the_ID(), 'alpha_custom_tab_content_2nd', true );
			echo '<h2>' . esc_html( $tab_title ) . '</h2>';
			echo alpha_strip_script_tags( $tab_content );
		}
	} elseif ( 'alpha_pa_block_' == substr( $tab_name, 0, strlen( 'alpha_pa_block_' ) ) && ! empty( $tab_data['block_id'] ) ) {
		alpha_print_template( absint( $tab_data['block_id'] ) );
	}
}
