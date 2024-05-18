<?php
/**
 * WPForms Lite Compatibility
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */

if ( ! class_exists( 'Alpha_WPForms' ) ) {

	/**
	 * Alpha WPForms Class
	 */
	class Alpha_WPForms extends Alpha_Base {

		protected $counter;

		/**
		 * Main Class construct
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'wp_footer', array( $this, 'enqueue_style' ), 19 );
		}

		/**
		 * Custom style for WPForms
		 *
		 * @since 1.0
		 */
		function enqueue_style() {
			wp_enqueue_style( 'alpha-wpforms-style', alpha_framework_uri( '/plugins/wpforms/wpforms' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array( 'alpha-theme' ), ALPHA_VERSION );
		}
	}
}

Alpha_WPForms::get_instance();
