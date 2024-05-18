<?php
/**
 * Entrypoint of theme
 *
 * Here, proper features of theme are added or removed.
 * If framework has unnecessary features, you can remove features
 * using alpha_remove_feature.
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

/**************************************/
/* Define Constants                   */
/**************************************/
define( 'ALPHA_INC_URI', ALPHA_URI . '/inc' );
define( 'ALPHA_INC_PATH', ALPHA_PATH . '/inc' );
define( 'ALPHA_INC_PLUGINS', ALPHA_INC_PATH . '/plugins' );
define( 'ALPHA_INC_PLUGINS_URI', ALPHA_INC_URI . '/plugins' );

if ( ! class_exists( 'Alpha_Theme_Setup' ) ) {
	class Alpha_Theme_Setup extends Alpha_Base {

		/**
		 * The Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {

			/*******************************************/
			/* 1. Load the general function and action */
			/*******************************************/
			require_once ALPHA_INC_PATH . '/general-function.php';
			require_once ALPHA_INC_PATH . '/general-actions.php';
			require_once ALPHA_INC_PATH . '/class-alpha-assets-extend.php';

			/**************************************/
			/* 2. Load the plugin functions       */
			/**************************************/
			// woocommerce relates
			if ( alpha_get_feature( 'fs_plugin_woocommerce' ) && class_exists( 'WooCommerce' ) ) {
				require_once ALPHA_INC_PATH . '/plugins/woocommerce/class-alpha-woocommerce-extend.php';
			}

			/**************************************/
			/* 3. Load admin extend               */
			/**************************************/
			require_once ALPHA_INC_PATH . '/admin/customizer/class-alpha-customizer-extend.php';

			add_filter( 'alpha_image_sizes', array( $this, 'additional_image_sizes' ) );
		}

		/**
		 * Add more image sizes
		 *
		 * @since 1.0
		 */
		public function additional_image_sizes( $image_sizes ) {
			$image_sizes = array_merge(
				$image_sizes,
				array(
					'alpha-thumb-custom-1' => array(
						'width'  => 390,
						'height' => 204,
						'crop'   => true,
					),
					'alpha-thumb-custom-2' => array(
						'width'  => 390,
						'height' => 220,
						'crop'   => true,
					),
					'alpha-thumb-custom-3' => array(
						'width'  => 390,
						'height' => 294,
						'crop'   => true,
					),
					'alpha-thumb-custom-4' => array(
						'width'  => 285,
						'height' => 180,
						'crop'   => true,
					),
					'alpha-thumb-custom-5' => array(
						'width'  => 285,
						'height' => 200,
						'crop'   => true,
					),
				)
			);
			return $image_sizes;
		}
	}
}

Alpha_Theme_Setup::get_instance();
