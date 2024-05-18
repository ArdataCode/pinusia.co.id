<?php
/**
 * Header template
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */

defined( 'ABSPATH' ) || die;
?>

<!DOCTYPE html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

		<?php ! function_exists( 'alpha_print_favicon' ) || alpha_print_favicon(); ?>
		
		<?php
		$preload_fonts = alpha_get_option( 'preload_fonts' );
		if ( ! empty( $preload_fonts ) ) {
			if ( in_array( 'alpha', $preload_fonts ) ) {
				echo '<link rel="preload" href="' . ALPHA_ASSETS . '/vendor/icons/fonts/' . ALPHA_NAME . '.ttf?png09e" as="font" type="font/ttf" crossorigin>';
			}
			if ( in_array( 'fas', $preload_fonts ) ) {
				echo '<link rel="preload" href="' . ALPHA_ASSETS . '/vendor/fontawesome-free/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>';
			}
			if ( in_array( 'far', $preload_fonts ) ) {
				echo '<link rel="preload" href="' . ALPHA_ASSETS . '/vendor/fontawesome-free/webfonts/fa-regular-400.woff2" as="font" type="font/woff2" crossorigin>';
			}
			if ( in_array( 'fab', $preload_fonts ) ) {
				echo '<link rel="preload" href="' . ALPHA_ASSETS . '/vendor/fontawesome-free/webfonts/fa-brands-400.woff2" as="font" type="font/woff2" crossorigin>';
			}
		}
		if ( ! empty( $preload_fonts['custom'] ) ) {
			$font_urls = explode( PHP_EOL, $preload_fonts['custom'] );
			foreach ( $font_urls as $font_url ) {
				$dot_pos = strrpos( $font_url, '.' );
				if ( false !== $dot_pos ) {
					$type       = substr( $font_url, $dot_pos + 1 );
					$font_type  = array( 'ttf', 'woff', 'woff2', 'eot' );
					$image_type = array( 'jpg', 'jpeg', 'png', 'svg', 'gif' );
					if ( in_array( $type, $font_type ) ) {
						echo '<link rel="preload" href="' . esc_url( $font_url ) . '" as="font" type="font/' . esc_attr( $type ) . '" crossorigin/>';
					} elseif ( in_array( $type, $image_type ) ) {
						echo '<link rel="preload" href="' . esc_url( $font_url ) . '" as="image" />';
					} else {
						echo '<link rel="preload" href="' . esc_url( $font_url ) . '" />';
					}
				}
			}
		}
		?>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php wp_body_open(); ?>
		<?php if ( defined( 'ALPHA_PRO_VERSION' ) && ( is_customize_preview() || alpha_get_option( 'loading_animation' ) ) ) : ?>
			<?php
			echo apply_filters(
				'alpha_page_loading_animation',
				'<div class="loading-overlay">
					<div class="bounce-loader">
						<div></div>
						<div></div>
						<div></div>
					</div>
				</div>'
			);
			?>
		<?php endif; ?>

		<?php
		/**
		 * Fires before redering page wrapper.
		 *
		 * @since 1.0
		 */
		do_action( 'alpha_before_page_wrapper' );
		?>

		<div class="page-wrapper">

			<?php
			global $alpha_layout;
			if ( ! empty( $alpha_layout['top_bar'] ) && 'hide' != $alpha_layout['top_bar'] ) {
				echo '<div class="top-notification-bar">';
				alpha_print_template( $alpha_layout['top_bar'] );
				echo '</div>';
			}

			alpha_get_template_part( 'header/header' );

			alpha_print_title_bar();

			?>

			<?php
			/**
			 * Fires before rendering main.
			 *
			 * @since 1.0
			 */
			do_action( 'alpha_before_main' );
			?>

			<main id="main" class="<?php echo apply_filters( 'alpha_main_class', 'main' ); ?>">
