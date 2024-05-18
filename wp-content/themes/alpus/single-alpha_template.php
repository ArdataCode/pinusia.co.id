<?php
/**
 * Single alpha template
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */

defined( 'ABSPATH' ) || die;

get_header();

/**
 * Fires before redering single template.
 *
 * @since 1.0
 */
do_action( 'alpha_before_template' );

$template_type = get_post_meta( get_the_ID(), ALPHA_NAME . '_template_type', true );

if ( 'header' == $template_type ) {

	/**
	 * Header Template
	 *
	 * Please refer templates/header/header.php file.
	 */

} elseif ( 'footer' == $template_type ) {

	/**
	 * Footer Template
	 *
	 * Please refer footer.php file.
	 */

} elseif ( 'popup' == $template_type ) {
	// In case of WPBakery page builder
	if ( function_exists( 'alpha_is_wpb_preview' ) && alpha_is_wpb_preview() ) {
		$id       = get_the_ID();
		$settings = get_post_meta( $id, 'popup_options', true );
		if ( $settings && ! is_array( $settings ) ) {
			$settings = json_decode( $settings, true );
		}
		if ( ! $settings ) {
			$settings          = array();
			$settings['width'] = '600';
			$settings['h_pos'] = 'center';
			$settings['v_pos'] = 'center';
		}
		echo '<div class="mfp-bg mfp-fade mfp-alpha-' . get_the_ID() . ' mfp-ready"></div>';
		echo '<div class="mfp-wrap mfp-close-btn-in mfp-auto-cursor mfp-fade mfp-alpha mfp-alpha-' . $id . ' mfp-ready" tabindex="-1" style="overflow: hidden auto;">';
			echo '<div class="mfp-container mfp-inline-holder">';
				echo '<div class="mfp-content" style="justify-content: ' . esc_attr( $settings['h_pos'] ) . '; align-items: ' . esc_attr( $settings['v_pos'] ) . '">';
					echo '<div id="alpha-popup-' . $id . '" class="popup mfp-fade" style="width: ' . (int) $settings['width'] . 'px;' . ( ! empty( $settings['top'] ) ? ( 'margin-top: ' . (int) $settings['top'] . 'px;' ) : '' ) . ( ! empty( $settings['right'] ) ? ( 'margin-right: ' . (int) $settings['right'] . 'px;' ) : '' ) . ( ! empty( $settings['bottom'] ) ? ( 'margin-bottom: ' . (int) $settings['bottom'] . 'px;' ) : '' ) . ( ! empty( $settings['left'] ) ? ( 'margin-left: ' . (int) $settings['left'] . 'px;' ) : '' ) . '">';
						echo '<div class="alpha-popup-content"' . ( ! empty( $settings['border'] ) ? ( 'style="border-radius: ' . (int) $settings['border'] ) . 'px"' : '' ) . '>';
							echo '<div class="alpha-wpb-edit-area">';
	}

	if ( have_posts() ) {

		the_post();

		the_content();

		wp_reset_postdata();
	}

	if ( function_exists( 'alpha_is_wpb_preview' ) && alpha_is_wpb_preview() ) {
							echo '</div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
} elseif ( 'type' == $template_type ) {
	global $post;
	$content_type = get_post_meta( $post->ID, 'content_type', true );
	$post_content = $post->post_content;
	$object       = apply_filters( 'alpha_builder_get_current_object', get_the_ID(), array( 'content_type' => $content_type ) );

	if ( $object ) {
		// backup global data
		$original_query          = $GLOBALS['wp_query'];
		$original_queried_object = $GLOBALS['wp_query']->queried_object;
		if ( 'term' == $content_type ) {
			$original_is_tax     = $GLOBALS['wp_query']->is_tax;
			$original_is_archive = $GLOBALS['wp_query']->is_archive;

			$GLOBALS['wp_query']->queried_object = $object;
			$GLOBALS['wp_query']->is_tax         = true;
			$GLOBALS['wp_query']->is_archive     = true;
		} else {
			$original_post = $GLOBALS['post'];

			$GLOBALS['post'] = $object;
			setup_postdata( $GLOBALS['post'] );
			$GLOBALS['wp_query']->queried_object = $GLOBALS['post'];

			if ( 'product' == $content_type && class_exists( 'Woocommerce' ) ) {
				$GLOBALS['product'] = wc_get_product( $object->ID );
			}
		}

		// render
		echo do_blocks( $post_content );

		// restore global data
		$GLOBALS['wp_query']                 = $original_query; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$GLOBALS['wp_query']->queried_object = $original_queried_object; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		if ( 'term' == $content_type ) {
			$GLOBALS['wp_query']->is_tax     = $original_is_tax; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$GLOBALS['wp_query']->is_archive = $original_is_archive; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		} else {
			$GLOBALS['post'] = $original_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			if ( 'product' == $content_type ) {
				unset( $GLOBALS['product'] );
			}
		}
	}
} else {

	global $product;

	if ( $product ) {

		/**
		 * Single Product Template
		 */
		wc_get_template( 'single-product.php' );

	} else {

		/**
		 * Block Template
		 */
		if ( have_posts() ) :

			the_post();

			the_content();

			wp_reset_postdata();

		endif;

	}

	/**
	 * Fires after redering single template.
	 *
	 * @since 1.0
	 */
	do_action( 'alpha_after_template' );
}

get_footer();
