<?php
/**
 * Elementor Pro Compatibility
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

use ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;

if ( ! class_exists( 'Alpha_Elementor_Pro' ) ) :
	class Alpha_Elementor_Pro extends Alpha_Base {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'init' ), 20 );
			add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );
			add_action( 'alpha_elementor_pro_header_location', array( $this, 'do_header' ) );
			add_action( 'alpha_elementor_pro_footer_location', array( $this, 'do_footer' ) );
		}

		/**
		 * @param Locations_Manager $manager
		 */
		public function register_locations( $manager ) {
			$manager->register_core_location( 'header' );
			$manager->register_core_location( 'footer' );

			$module  = ElementorPro\Modules\ThemeBuilder\Module::instance();
			$headers = $module->get_conditions_manager()->get_documents_for_location( 'header' );
			$footers = $module->get_conditions_manager()->get_documents_for_location( 'footer' );
			if ( ! empty( $headers ) ) {
				$GLOBALS['alpha_layout']['header'] = 'elementor_pro';
			}
			if ( ! empty( $footers ) ) {
				$GLOBALS['alpha_layout']['footer'] = 'elementor_pro';
			}
		}

		public function do_header() {
			elementor_theme_do_location( 'header' );
		}

		public function do_footer() {
			elementor_theme_do_location( 'footer' );
		}

		public function init() {
			$module        = ElementorPro\Modules\ThemeBuilder\Module::instance();
			$theme_support = $module->get_component( 'theme_support' );
			if ( $theme_support ) {
				remove_action( 'elementor/theme/register_locations', array( $theme_support, 'after_register_locations' ), 99 );
			}
		}
	}
endif;

Alpha_Elementor_Pro::get_instance();
