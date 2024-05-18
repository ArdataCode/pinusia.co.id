<?php
/**
 * Entrypoint of framework.
 *
 * Framework has many addons and admin functions. And also has plugin
 * compatibility. Please look below.
 *
 * 1. Define Constants
 * 2. Load the theme base
 * 3. Analyse the current request
 * 4. Load the plugin functions
 * 5. Load addons
 * 6. Load admin
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

/**************************************/
/* 1. Define Constants                */
/**************************************/
global $pagenow;

define( 'ALPHA_FRAMEWORK_PLUGINS', ALPHA_FRAMEWORK_PATH . '/plugins' );
define( 'ALPHA_FRAMEWORK_PLUGINS_URI', ALPHA_FRAMEWORK_URI . '/plugins' );

/**************************************/
/* 2. Load the theme base             */
/**************************************/

require_once alpha_framework_path( ALPHA_FRAMEWORK_PATH . '/class-alpha-support.php' );
require_once alpha_framework_path( ALPHA_FRAMEWORK_PATH . '/class-alpha-assets.php' );
if ( ! defined( 'ALPHA_CORE_VERSION' ) ) {
	require_once alpha_framework_path( ALPHA_FRAMEWORK_PATH . '/common-functions.php' );
}
require_once alpha_framework_path( ALPHA_FRAMEWORK_PATH . '/theme-functions.php' );
require_once alpha_framework_path( ALPHA_FRAMEWORK_PATH . '/theme-actions.php' );


/**************************************/
/* 3. Analyse the current request     */
/**************************************/

$request = array(
	'doing_ajax'        => alpha_doing_ajax(),
	'customize_preview' => is_customize_preview(),
	'can_manage'        => current_user_can( 'manage_options' ),
	'is_admin'          => is_admin(),
	'is_preview'        => function_exists( 'alpha_is_elementor_preview' ) && alpha_is_elementor_preview() ||
							function_exists( 'alpha_is_wpb_preview' ) && alpha_is_wpb_preview(),
	'product_edit_page' => ( 'post-new.php' == $GLOBALS['pagenow'] && isset( $_GET['post_type'] ) && 'product' == $_GET['post_type'] ) ||
							( 'post.php' == $GLOBALS['pagenow'] && isset( $_GET['post'] ) && 'product' == get_post_type( $_GET['post'] ) ) ||
							( 'edit.php' == $GLOBALS['pagenow'] && isset( $_GET['post_type'] ) && 'product' == $_GET['post_type'] ) ||
							( 'term.php' == $GLOBALS['pagenow'] && isset( $_GET['post_type'] ) && 'product' == $_GET['post_type'] ),
);


/**
 * Fires after framework init
 *
 * @since 1.0
 */
do_action( 'alpha_after_framework_init', $request );


/**************************************/
/* 4. Load the plugin functions       */
/**************************************/

// @start feature: fs_plugin_woocommerce
if ( ( 'widgets.php' == $pagenow || 'admin-ajax.php' == $pagenow || 'post.php' == $pagenow || 'index.php' == $pagenow ) && alpha_get_feature( 'fs_plugin_woocommerce' ) && class_exists( 'WooCommerce' ) ) {
	require_once alpha_framework_path( ALPHA_FRAMEWORK_PLUGINS . '/woocommerce/class-alpha-woocommerce.php' );
}
// @end feature: fs_plugin_woocommerce

// @start feature: fs_pb_elementor
if ( alpha_get_feature( 'fs_pb_elementor' ) && defined( 'ELEMENTOR_VERSION' ) ) {
	require_once alpha_framework_path( ALPHA_FRAMEWORK_PLUGINS . '/elementor/elementor.php' );
	if ( defined( 'ELEMENTOR_PRO_VERSION' ) ) {
		require_once alpha_framework_path( ALPHA_FRAMEWORK_PLUGINS . '/elementor/elementor-pro.php' );
	}
}
// @end feature: fs_pb_elementor

// @start feature: fs_plugin_wpforms
if ( alpha_get_feature( 'fs_plugin_wpforms' ) && class_exists( 'WPForms' ) ) {
	require_once alpha_framework_path( ALPHA_FRAMEWORK_PLUGINS . '/wpforms/class-alpha-wpforms.php' );
}
// @end feature: fs_plugin_wpforms

/**
 * Fires after loading framework plugin compatibility.
 *
 * @param array $request Request parameter for filter.
 * @since 1.0
 */
do_action( 'alpha_after_framework_plugins', $request );

/**************************************/
/* 5. Load addons                     */
/**************************************/

/**
 * Fires loading framework addons.
 *
 * @param array $request Request parameter for filter.
 * @since 1.0
 */
do_action( 'alpha_framework_addons', $request );

/**************************************/
/* 6. Load admin                      */
/**************************************/

// Merge and Critical css for Optimize

if ( defined( 'ALPHA_PRO_VERSION' ) ) {
	require_once alpha_framework_path( ALPHA_FRAMEWORK_PATH . '/admin/optimize-wizard/class-alpha-optimize-stylesheets.php' );
}

// Layout Builder
require_once alpha_framework_path( ALPHA_FRAMEWORK_PATH . '/admin/layout-builder/class-alpha-layout-builder.php' );
if ( $request['can_manage'] && $request['is_admin'] ) {
	require_once alpha_framework_path( ALPHA_FRAMEWORK_PATH . '/admin/layout-builder/class-alpha-layout-builder-admin.php' );
}

if ( $request['can_manage'] ) {

	// Define Constants
	define( 'ALPHA_FRAMEWORK_ADMIN', ALPHA_FRAMEWORK_PATH . '/admin' );
	define( 'ALPHA_FRAMEWORK_ADMIN_URI', ALPHA_FRAMEWORK_URI . '/admin' );                         // Template plugins directory uri
	require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/admin/class-alpha-admin.php' ); // Load admin

	// Load Admin Functions
	if ( ! $request['customize_preview'] && ( 'admin.php' == $pagenow || 'admin-ajax.php' == $pagenow || $request['is_admin'] ) ) {
		require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/plugins/class-alpha-tgm-plugins.php' ); // Load admin plugins
	}
	if ( ! $request['customize_preview'] ) {
		require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/panel/class-alpha-admin-panel.php' );   // Load admin panel
		require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/setup-wizard/class-alpha-setup-wizard.php' );          // Load admin setup wizard
		require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/optimize-wizard/class-alpha-optimize-wizard.php' );    // Load admin optimize wizard
		require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/tools/class-alpha-tools.php' );                        // Load admin tools
		require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/tools/class-alpha-tools-extend.php' );                        // Load admin tools
		require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/rollback/class-alpha-rollback.php' );    // Load admin rollback
		require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/patcher/class-alpha-patcher.php' );                    // Load admin patcher
	}

	// @start feature: fs_admin_customize
	if ( alpha_get_feature( 'fs_admin_customize' ) && $request['customize_preview'] ) {                                       // Load admin customizer
		require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/customizer/class-alpha-customizer.php' );
		require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/customizer/customizer-function.php' );
	}
	// @end feature: fs_admin_customize

	/**
	 * Fires after setting up framework admin.
	 *
	 * @param array $request Request parameter for filter.
	 * @since 1.0
	 */
	do_action( 'alpha_after_framework_admin', $request );
}


/**
 * Fires after setting up framework.
 *
 * @param array $request Request parameter for filter.
 * @since 1.0
 */
do_action( 'alpha_after_framework', $request );
