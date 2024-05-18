<?php
/**
 * Alpha Admin Panel
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

/**
 * Alpha Admin Panel Class
 *
 * @since 1.0
 */
if ( ! class_exists( 'Alpha_Admin_Panel' ) ) {
	class Alpha_Admin_Panel extends Alpha_Base {

		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'add_admin_menus' ) );
			add_action(
				'admin_enqueue_scripts',
				function () {
					wp_enqueue_script( 'admin-swiper', ALPHA_ASSETS . '/vendor/swiper/swiper' . ALPHA_JS_SUFFIX, array( 'jquery-core' ), '6.7.0', true );
				}
			);
		}

		/**
		 * Add admin menus
		 *
		 * @since 1.0
		 */
		public function add_admin_menus() {
			$title = alpha_get_option( 'white_label_title' ) ? sprintf( esc_html__( '%s', 'alpha' ), alpha_get_option( 'white_label_title' ) ) : ALPHA_DISPLAY_NAME;
			// Menu - alpha
			add_menu_page( $title, $title, 'administrator', 'alpha', array( $this, 'panel_activate' ), alpha_get_option( 'white_label_icon' ) ? alpha_get_option( 'white_label_icon' ) : 'dashicons-alpha-logo', 2 );

			$admin_menus = apply_filters( 
				'alpha_admin_menus',
				array(
					'dashboard' => array(
						'condition'   => '',
						'parent_slug' => 'alpha',
						'page_title'  => esc_html__( 'Dashboard', 'alpha' ),
						'menu_title'  => esc_html__( 'Dashboard', 'alpha' ),
						'capability'  => 'administrator',
						'menu_slug'   => 'alpha',
						'callback'    => array( $this, 'panel_activate' ),
						'position'    => 1,
					),
					'theme-options' => array(
						'condition'   => '',
						'parent_slug' => 'alpha',
						'page_title'  => esc_html__( 'Theme Options', 'alpha' ),
						'menu_title'  => esc_html__( 'Theme Options', 'alpha' ),
						'capability'  => 'administrator',
						'menu_slug'   => 'customize.php',
						'callback'    => '',
						'position'    => 2,
					),
					'setup-wizard' => array(
						'condition'   => class_exists( 'Alpha_Setup_Wizard' ),
						'class'       => 'Alpha_Setup_Wizard',
						'parent_slug' => 'alpha',
						'page_title'  => esc_html__( 'Setup Wizard', 'alpha' ),
						'menu_title'  => esc_html__( 'Setup Wizard', 'alpha' ),
						'capability'  => 'manage_options',
						'menu_slug'   => 'alpha-setup-wizard',
						'callback'    => 'view_setup_wizard',
						'position'    => 3,
					),
					'optimize-wizard' => array(
						'condition'   => class_exists( 'Alpha_Optimize_Wizard' ),
						'class'       => 'Alpha_Optimize_Wizard',
						'parent_slug' => 'alpha',
						'page_title'  => esc_html__( 'Optimize Wizard', 'alpha' ),
						'menu_title'  => esc_html__( 'Optimize Wizard', 'alpha' ),
						'capability'  => 'manage_options',
						'menu_slug'   => 'alpha-optimize-wizard',
						'callback'    => 'view_optimize_wizard',
						'position'    => 4,
					),
					'layout-builder' => array(
						'condition'   => class_exists( 'Alpha_Layout_Builder_Admin' ),
						'class'       => 'Alpha_Layout_Builder_Admin',
						'parent_slug' => 'alpha',
						'page_title'  => esc_html__( 'Layout Builder', 'alpha' ),
						'menu_title'  => esc_html__( 'Layout Builder', 'alpha' ),
						'capability'  => 'manage_options',
						'menu_slug'   => 'alpha-layout-builder',
						'callback'    => 'view_layout_builder',
						'position'    => 5,
					),
					'templates' => array(
						'condition'   => class_exists( 'Alpha_Builders' ),
						'parent_slug' => 'alpha',
						'page_title'  => esc_html__( 'Templates', 'alpha' ),
						'menu_title'  => esc_html__( 'Templates', 'alpha' ),
						'capability'  => 'administrator',
						'menu_slug'   => 'edit.php?post_type=' . ALPHA_NAME . '_template',
						'callback'    => '',
						'position'    => 8,
					),
					'sidebars' => array(
						'condition'   => class_exists( 'Alpha_Sidebar_Builder' ),
						'class'       => 'Alpha_Sidebar_Builder',
						'parent_slug' => 'alpha',
						'page_title'  => esc_html__( 'Sidebars', 'alpha' ),
						'menu_title'  => esc_html__( 'Sidebars', 'alpha' ),
						'capability'  => 'administrator',
						'menu_slug'   => 'alpha-sidebar',
						'callback'    => 'sidebar_view',
						'position'    => 10,
					),
					'tools' => array(
						'condition'   => class_exists( 'Alpha_Tools' ),
						'class'       => 'Alpha_Tools',
						'parent_slug' => 'alpha',
						'page_title'  => esc_html__( 'Tools', 'alpha' ),
						'menu_title'  => esc_html__( 'Tools', 'alpha' ),
						'capability'  => 'manage_options',
						'menu_slug'   => 'alpha-tools',
						'callback'    => 'view_tools',
						'position'    => 13,
					),
					'critical' => array(
						'condition'   => class_exists( 'Alpha_Critical' ) && alpha_get_option( 'resource_critical_css' ),
						'class'       => 'Alpha_Critical',
						'parent_slug' => 'alpha',
						'page_title'  => esc_html__( 'Critical CSS', 'alpha' ),
						'menu_title'  => '<span>' . esc_html__( 'Critical CSS', 'alpha' ) . '</span>',
						'capability'  => 'manage_options',
						'menu_slug'   => 'alpha-critical',
						'callback'    => 'view_critical',
						'position'    => 14,
					),
					'patcher' => array(
						'condition'   => class_exists( 'Alpha_Patcher' ),
						'class'       => 'Alpha_Patcher',
						'parent_slug' => 'alpha',
						'page_title'  => esc_html__( 'Patcher', 'alpha' ),
						'menu_title'  => '<span>' . esc_html__( 'Patcher', 'alpha' ) . '</span>',
						'capability'  => 'manage_options',
						'menu_slug'   => 'alpha-patcher',
						'callback'    => 'view_patcher',
						'position'    => 15,
					),
					'version' => array(
						'condition'   => class_exists( 'Alpha_Rollback' ),
						'class'       => 'Alpha_Rollback',
						'parent_slug' => 'alpha',
						'page_title'  => esc_html__( 'Rollback', 'alpha' ),
						'menu_title'  => '<span>' . esc_html__( 'Rollback', 'alpha' ) . '</span>',
						'capability'  => 'manage_options',
						'menu_slug'   => 'alpha-rollback',
						'callback'    => 'view_tools',
						'position'    => 16,
					),
				)
			);
			
			foreach ( $admin_menus as $key => $args ) {
				if ( '' === $args['condition'] || $args['condition'] ) {
					if ( ! empty( $args['class'] ) ) {
						$callback = array( $args['class']::get_instance(), $args['callback'] );
					} else {
						$callback = $args['callback'];
					}
					add_submenu_page( $args['parent_slug'], $args['page_title'], $args['menu_title'], $args['capability'], $args['menu_slug'], $callback, $args['position'] );
				}
			}
		}

		/**
		 * Load header template for admin panel.
		 *
		 * @since 1.0
		 */
		public function view_header( $active_page, $admin_config = array(), $title = array() ) {
			require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/panel/views/header.php' );
		}

		/**
		 * Load footer template for admin panel.
		 *
		 * @since 1.0
		 */
		public function view_footer( $active_page = 'dashboard', $admin_config = array() ) {
			require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/panel/views/footer.php' );
		}

		/**
		 * Load dashboard panel template.
		 *
		 * @since 1.0
		 */
		public function panel_activate() {

			$admin_config = Alpha_Admin::get_instance()->admin_config;
			$this->view_header( 'dashboard', $admin_config );
			require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/panel/views/dashboard.php' );
			$this->view_footer( 'dashboard', $admin_config );

		}
	}
}

Alpha_Admin_Panel::get_instance();
