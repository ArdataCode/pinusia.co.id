<?php
/**
 * Alpha Optimize Wizard
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

define( 'ALPHA_OPTIMIZE_WIZARD', ALPHA_FRAMEWORK_ADMIN . '/optimize-wizard' );

if ( ! class_exists( 'Alpha_Optimize_Wizard' ) ) :
	/**
	* Alpha Theme Optimize Wizard
	*
	* @since 1.0
	*/
	class Alpha_Optimize_Wizard extends Alpha_Base {

		/**
		 * The Optimize Wizard Version
		 *
		 * @var string
		 * @since 1.0
		 */
		protected $version = '1.0';

		/**
		 * The current theme name.
		 *
		 * @var string
		 * @since 1.0
		 */
		protected $theme_name = '';

		/**
		 * The current step
		 *
		 * @var string
		 * @since 1.0
		 */
		protected $step = '';

		/**
		 * The wizard steps
		 *
		 * @var array
		 * @since 1.0
		 */
		protected $steps = array();

		/**
		 * The current page slug in optimize wizard.
		 *
		 * @var string
		 * @since 1.0
		 */
		public $page_slug;

		/**
		 * The TGM plugin instance.
		 *
		 * @var mixed
		 * @since 1.0
		 */
		protected $tgmpa_instance;

		/**
		 * The TGM plugin menu slug.
		 *
		 * @var string
		 * @since 1.0
		 */
		protected $tgmpa_menu_slug = 'tgmpa-install-plugins';

		/**
		 * The TGM plugin uri.
		 *
		 * @var string
		 * @since 1.0
		 */
		protected $tgmpa_url = 'themes.php?page=tgmpa-install-plugins';

		/**
		 * The page uri.
		 *
		 * @var string
		 * @since 1.0
		 */
		protected $page_url;

		/**
		 * The files.
		 *
		 * @since 1.0
		 */
		protected $files;

		/**
		 * The constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			$this->current_theme_meta();
			$this->init_actions();
		}

		/**
		 * Current theme meta
		 *
		 * @since 1.0
		 */
		public function current_theme_meta() {
			$current_theme    = wp_get_theme();
			$this->theme_name = strtolower( preg_replace( '#[^a-zA-Z]#', '', $current_theme->get( 'Name' ) ) );
			$this->page_slug  = 'alpha-optimize-wizard';
			$this->page_url   = 'admin.php?page=' . $this->page_slug;
		}

		/**
		 * Init actions
		 *
		 * @since 1.0
		 */
		public function init_actions() {
			add_filter( 'alpha_admin_menus', array( $this, 'customize_admin_menu' ) );
		}

		/**
		 * Add optimize wizard menu item to Alpha admin menu.
		 *
		 * @since 1.0
		 */
		public function customize_admin_menu( $menus ) {
			if ( ! empty( $menus['optimize-wizard'] ) ) {
				$menus['optimize-wizard']['menu_title'] = esc_html__( 'Optimize Wizard', 'alpha' ) . '<span class="alpha-pro-badge">' . esc_html( 'PRO', 'alpha' ) . '</span>';
				$menus['optimize-wizard']['callback'] = 'view_optimize_wizard';
			}
			return $menus;
		}

		/**
		 * Display optimize wizard
		 *
		 * @since 1.0
		 */
		public function view_optimize_wizard() {
			$admin_config = Alpha_Admin::get_instance()->admin_config;
			Alpha_Admin_Panel::get_instance()->view_header( 'activation', $admin_config, array() );
			include alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/panel/views/go_pro.php' );
			Alpha_Admin_Panel::get_instance()->view_footer( 'activation', $admin_config );
		}
	}
endif;

add_action( 'after_setup_theme', 'alpha_theme_optimize_optimize_wizard', 10 );

if ( ! function_exists( 'alpha_theme_optimize_optimize_wizard' ) ) :
	function alpha_theme_optimize_optimize_wizard() {
		Alpha_Optimize_Wizard::get_instance();
	}
endif;
