<?php
/**
 * Config function
 *
 * You can override config function.
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */


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
		if ( defined( 'ALPHA_PRO_VERSION' ) ) {
			if ( file_exists( str_replace( ALPHA_PATH . '/framework/', ALPHA_PRO_PATH . '/inc/', $path ) ) ) {
				return str_replace( ALPHA_PATH . '/framework/', ALPHA_PRO_PATH . '/inc/', $path );
			}
			if ( false !== strpos( ALPHA_PATH . '/inc/', $path ) && file_exists( str_replace( ALPHA_PATH . '/inc/', ALPHA_PRO_PATH . '/inc/', $path ) ) ) {
				return str_replace( ALPHA_PATH . '/inc/', ALPHA_PRO_PATH . '/inc/', $path );
			}
		}
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
		if ( defined( 'ALPHA_PRO_VERSION' ) && file_exists( ALPHA_PRO_PATH . '/inc' . $short_path ) ) {
			return ALPHA_PRO_URI . '/inc' . $short_path;
		}
		return file_exists( ALPHA_PATH . '/inc' . $short_path ) ? ALPHA_URI . '/inc' . $short_path : ALPHA_FRAMEWORK_URI . $short_path;
	}
}
