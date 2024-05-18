<?php
/**
 * Assets Extend Class
 *
 * Enqueue framework assets including css, js and images.
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

class Alpha_Assets_Extend extends Alpha_Base {

	/**
	 * Constructor
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {
		add_filter( 'alpha_vars', array( $this, 'customzie_localize_vars' ) );
	}

	/**
	 * Customize localize vars
	 *
	 * @since 1.0
	 */
	public function customzie_localize_vars( $localize_vars ) {
		if ( ! defined( 'ALPHA_PRO_VERSION' ) ) {
			$localize_vars['skeleton_screen'] = false;
			$localize_vars['blog_ajax']       = false;

			if ( class_exists( 'WooCommerce' ) ) {
				$localize_vars['shop_ajax']     = false;
				$localize_vars['cart_show_qty'] = false;
			}
		}
		return $localize_vars;
	}

}

Alpha_Assets_Extend::get_instance();
