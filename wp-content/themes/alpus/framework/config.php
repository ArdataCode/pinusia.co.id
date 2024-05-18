<?php
/**
 * The base configuration for Alpha FrameWork
 *
 * The framework/config.php defines framework_path and
 * adds all framework feature.
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @version    1.0
 * @since      1.0
 */

/**
 * Defines Alpha FrameWork Version
 * Defines Alpha FrameWork Path
 */
define( 'ALPHA_FRAMEWORK_VERSION', '1.3.0' );
define( 'ALPHA_FRAMEWORK_PATH', ALPHA_PATH . '/framework' );
define( 'ALPHA_FRAMEWORK_URI', ALPHA_URI . '/framework' );

/**
 * For theme developers
 *
 * You can override framework file by helping this function.
 * If you want to override framework/init.php, create 'inc' directory just inside of theme
 * and here you create init.php too. As a result inc/init.php is called by below function.
 *
 *
 * @param  string $path Full path of php, js, css file which is required.
 * @return string Returns filtered path if $path exists in inc directory, raw path otherwise.
 */
if ( ! function_exists( 'alpha_framework_path' ) ) {
	function alpha_framework_path( $path ) {
		return file_exists( str_replace( '/framework/', '/inc/', $path ) ) ? str_replace( '/framework/', '/inc/', $path ) : $path;
	}
}

/**
 * For theme developers
 *
 * You can override framework file by helping this function.
 * If you want to override framework/admin/admin.css, create 'inc' directory just inside of theme
 * and here you create admin/admin.css too. As a result inc/admin/admin.css is called by below function.
 *
 *
 * @param  string $short_path  Path in framework folder.
 * @return string Returns filtered uri if path exists in inc directory, raw uri otherwise.
 */
if ( ! function_exists( 'alpha_framework_uri' ) ) {
	function alpha_framework_uri( $short_path ) {
		return file_exists( ALPHA_PATH . '/inc' . $short_path ) ? ALPHA_URI . '/inc' . $short_path : ALPHA_FRAMEWORK_URI . $short_path;
	}
}

/**
 * Registers framework support for a given feature.
 *
 * Framework consists of features. If woocommerce feature isn't registered in theme,
 * framework doesn't require feature related file.
 *
 * @param array|string $features Features for framework. Likely core values include:
 *                      framework_support_pb_elementor
 *                      framework_support_pb_wpb
 *                      framework_support_plugin_woocommerce
 *                      framework_support_plugin_dokan
 *                      framework_support_admin_setup_wizard
 *                      ...
 */
if ( ! function_exists( 'alpha_add_feature' ) ) {
	function alpha_add_feature( $features ) {
		if ( empty( $features ) ) {
			return false;
		}
		if ( is_array( $features ) ) {
			foreach ( $features as $feature ) {
				add_theme_support( $feature );
			}
		} else {
			add_theme_support( $features );
		}
	}
}

/**
 * Allows a framework to de-register its support of a certain feature.
 *
 * Framework consists of features. If woocommerce feature isn't registered in theme,
 * framework doesn't require feature related file.
 *
 * @see alpha_add_feature()
 * @param array|string $features Features for framework.
 */
if ( ! function_exists( 'alpha_remove_feature' ) ) {
	function alpha_remove_feature( $features ) {
		if ( empty( $features ) ) {
			return false;
		}
		if ( is_array( $features ) ) {
			foreach ( $features as $feature ) {
				remove_theme_support( $feature );
			}
		} else {
			remove_theme_support( $features );
		}
	}
}

/**
 * Gets the framework support arguments passed when registering that support.
 *
 * Framework consists of features. If woocommerce feature isn't registered in theme,
 * framework doesn't require feature related file.
 *
 * @see alpha_add_feature()
 * @param string $feature The feature to check.
 */
if ( ! function_exists( 'alpha_get_feature' ) ) {
	function alpha_get_feature( $feature ) {
		if ( ! empty( $feature ) ) {
			return get_theme_support( $feature );
		}
	}
}


/**
 * Fires after setup Alpha FrameWork configuration.
 *
 * @since 1.0
 */
do_action( 'alpha_after_framework_setup' );
