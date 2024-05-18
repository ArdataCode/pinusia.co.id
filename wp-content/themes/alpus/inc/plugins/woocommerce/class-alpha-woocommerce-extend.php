<?php
/**
 * Alpha WooCommerce Functions
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */

defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Alpha_Woocommerce_Extend' ) ) {
	class Alpha_Woocommerce_Extend extends Alpha_Base {

		/**
		 * The Constructor.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			// Product loop
			add_action( 'alpha_after_product_loop_hooks', array( $this, 'after_product_loop_hooks' ) );
			// Single product
			add_action( 'alpha_after_ps_hooks', array( $this, 'after_ps_hooks' ) );

			if ( ! empty( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] && is_admin() ) {
				add_action( 'init', array( $this, 'load_functions' ), 8 );
			} else {
				$this->load_functions();
			}
		}

		/**
		 * Fires after product type(loop)
		 *
		 * @since 1.0
		 */
		public function after_product_loop_hooks() {

		}

		/**
		 * Fires after product single hooks.
		 *
		 * @since 1.0
		 */
		public function after_ps_hooks() {

		}

		/**
		 * Load functions
		 *
		 * @since 1.0
		 * @access public
		 */
		public function load_functions() {
			require_once ALPHA_INC_PATH . '/plugins/woocommerce/product-loop-extend.php';
			require_once ALPHA_INC_PATH . '/plugins/woocommerce/product-single-extend.php';
		}
	}
}

Alpha_Woocommerce_Extend::get_instance();
