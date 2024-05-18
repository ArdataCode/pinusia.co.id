<?php
/**
 * Alpha Tools
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */

// Direct access is denied
defined( 'ABSPATH' ) || die;

class Alpha_Tools_Extend extends Alpha_Base {

	/**
	 * The Page slug
	 *
	 * @since 1.0
	 * @access public
	 */
	public $page_slug = 'alpha-tools';

	/**
	 * The Result
	 *
	 * @since 1.0
	 * @access public
	 */
	private $result;

	/**
	 * Constructor
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {
		add_filter( 'alpha_admin_menus', array( $this, 'customize_admin_menu' ) );

		if ( ! current_user_can( 'administrator' ) || ! isset( $_REQUEST['page'] ) || $this->page_slug != $_REQUEST['page'] ) {
			return;
		}
	}

	/**
	 * Add Tools to admin menu
	 *
	 * @since 1.0
	 * @access public
	 */
	public function customize_admin_menu( $menus ) {
		if ( ! Alpha_Admin::get_instance()->is_registered() ) {
			unset( $menus['critical'] );
			unset( $menus['patcher'] );
			unset( $menus['version'] );
		}
		return $menus;
	}

	/**
	 * Render tools page
	 *
	 * @since 1.0
	 * @access public
	 */
	public function view_tools() {
		$admin_config = Alpha_Admin::get_instance()->admin_config;
		Alpha_Admin_Panel::get_instance()->view_header( 'activation', $admin_config, array() );
		include alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/panel/views/go_pro.php' );
		Alpha_Admin_Panel::get_instance()->view_footer( 'activation', $admin_config );
	}
}

Alpha_Tools_Extend::get_instance();
