<?php
/**
 * Alpha Setup Wizard
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 *
 */
defined( 'ABSPATH' ) || die;

define( 'ALPHA_SETUP_WIZARD', ALPHA_FRAMEWORK_ADMIN . '/setup-wizard' );

if ( ! defined( 'ALPHA_SITE_URL' ) ) {
	define( 'ALPHA_SITE_URL', ALPHA_SERVER_URI . 'alpus/' );
}

if ( ! class_exists( 'Alpha_Setup_Wizard' ) ) :
	/**
	 * Alpha Theme Setup Wizard
	 *
	 * @since 1.0
	 */
	class Alpha_Setup_Wizard extends Alpha_Base {

		/**
		 * The setup wizard version.
		 *
		 * @var string
		 */
		protected $version = '1.0';

		/**
		 * Current theme name.
		 *
		 * @var string
		 */
		protected $theme_name = '';

		/**
		 * The current step.
		 * @var string
		 */
		protected $step = '';

		/**
		 * The wizard steps.
		 * @var array
		 */
		protected $steps = array();

		/**
		 * The current page slug in setup wizard.
		 *
		 * @var string
		 */
		public $page_slug;

		/**
		 * The TGM plugin instance.
		 *
		 * @var mixed
		 */
		protected $tgmpa_instance;

		/**
		 * The TGM plugin menu slug.
		 *
		 * @var string
		 */
		public $tgmpa_menu_slug = 'tgmpa-install-plugins';

		/**
		 * The TGM plugin url.
		 *
		 * @var string
		 */
		public $tgmpa_url = 'themes.php?page=tgmpa-install-plugins';

		/**
		 * The page slug.
		 *
		 * @var string
		 */
		protected $page_url;

		/**
		 * The theme url.
		 *
		 * @var string
		 */
		protected $site_url = ALPHA_SITE_URL;

		/**
		 * The demo for import.
		 *
		 * @var string
		 */
		protected $demo;

		/**
		 * The post types for demo import.
		 *
		 * @var array
		 */
		public $demo_import_post_types = array();

		/**
		 * The taxonomies for demo import.
		 *
		 * @var array
		 */
		public $demo_import_taxonomies = array();

		/**
		 * The product attributes.
		 *
		 * @var array
		 */
		public $demo_import_product_attributes = array();

		/**
		 * The woocommerce page.
		 *
		 * @var array
		 */
		public $woopages = array(
			'woocommerce_shop_page_id'      => 'Shop',
			'woocommerce_cart_page_id'      => 'Cart',
			'woocommerce_checkout_page_id'  => 'Checkout',
			'woocommerce_myaccount_page_id' => 'My account',
		);

		/**
		 * Setup Wizard Notifications
		 * 
		 * @var array
		 */
		public $sw_notes = array();

		/**
		 * The class Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			$this->demo_import_post_types         = apply_filters(
				'alpha_import_post_types',
				array(
					'page',
					ALPHA_NAME . '_template',
					'post',
					'product',
					'nav_menu_item',
				)
			);
			$this->demo_import_taxonomies         = apply_filters(
				'alpha_import_taxonomies',
				array(
					'category',
					'product_cat',
					'product_brand',
				)
			);
			$this->demo_import_product_attributes = apply_filters(
				'alpha_import_product_attributes',
				array(
					'Color',
					'Size',
				)
			);
			$this->current_theme_meta();
			$this->init_setup_wizard();

			$this->sw_notes = array(
				sprintf( esc_html__( 'You can improve your site performance by using %s Optimize Wizard ( Dashboard > Optimize Wizard ).', 'alpha' ), ALPHA_DISPLAY_NAME ),
				esc_html__( 'Using Critical CSS can noticeably reduce page-load times, as it speeds up the page rendering process.', 'alpha' ),
				sprintf( esc_html__( '%s Optimize Wizard provides Critical CSS & Merge JS/CSS functionality for your site performance.', 'alpha' ), ALPHA_DISPLAY_NAME ),
				esc_html__( 'Critical CSS & Merge JS/CSS functionality should be applied after your site complete development.', 'alpha' ),
			);
		}

		/**
		 * Get current theme meta.
		 *
		 * @since 1.0
		 */
		public function current_theme_meta() {
			$current_theme    = wp_get_theme();
			$this->theme_name = strtolower( preg_replace( '#[^a-zA-Z]#', '', $current_theme->get( 'Name' ) ) );
			$this->page_slug  = 'alpha-setup-wizard';
			$this->page_url   = 'admin.php?page=' . $this->page_slug;
		}

		/**
		 * Init setup wizard.
		 *
		 * @since 1.0
		 */
		public function init_setup_wizard() {
			add_action( 'upgrader_post_install', array( $this, 'upgrader_post_install' ), 10, 2 );

			if ( apply_filters( $this->theme_name . '_enable_setup_wizard', false ) ) {
				return;
			}

			if ( ! is_child_theme() ) {
				add_action( 'after_switch_theme', array( $this, 'switch_theme' ) );
			}

			if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
				add_action( 'init', array( $this, 'get_tgmpa_instanse' ), 30 );
				add_action( 'init', array( $this, 'set_tgmpa_url' ), 40 );
			}

			if ( ALPHA_ENVATO_CODE ) {
				add_action( 'admin_init', array( $this, 'admin_redirects' ), 30 );
			}

			add_action( 'admin_init', array( $this, 'init_wizard_steps' ), 30 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 30 );

			// Plugin Install
			add_filter( 'tgmpa_load', array( $this, 'tgmpa_load' ), 10, 1 );
			add_action( 'wp_ajax_alpha_setup_wizard_plugins', array( $this, 'ajax_plugins' ) );

			//Demo Import
			add_action( 'wp_ajax_alpha_reset_menus', array( $this, 'reset_menus' ) );
			add_action( 'wp_ajax_alpha_reset_widgets', array( $this, 'reset_widgets' ) );
			add_action( 'wp_ajax_alpha_import_dummy', array( $this, 'import_dummy' ) );
			add_action( 'wp_ajax_alpha_import_dummy_step_by_step', array( $this, 'import_dummy_step_by_step' ) );
			add_action( 'wp_ajax_alpha_import_widgets', array( $this, 'import_widgets' ) );
			add_action( 'wp_ajax_alpha_import_options', array( $this, 'import_options' ) );
			add_action( 'wp_ajax_alpha_delete_tmp_dir', array( $this, 'delete_tmp_dir' ) );
			add_action( 'wp_ajax_alpha_download_demo_file', array( $this, 'download_demo_file' ) );

			add_filter( 'wp_import_existing_post', array( $this, 'import_override_contents' ), 10, 2 );
			add_action( 'import_start', array( $this, 'import_dummy_start' ) );
			add_action( 'import_end', array( $this, 'import_dummy_end' ) );

			if ( ( ! empty( $_GET['page'] ) && $this->page_slug === $_GET['page'] ) || ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 0 === strpos( $_REQUEST['action'], 'alpha_' ) ) ) {
				require_once alpha_framework_path( ALPHA_SETUP_WIZARD . '/class-alpha-demo-history.php' );
				new Alpha_Demo_History;
			}
			
			if ( ! empty( $_GET['page'] ) && $this->page_slug === $_GET['page'] && isset( $_GET['step'] ) && 'activation' != $_GET['step'] && 'status' != $_GET['step'] ) {
				if ( ALPHA_ENVATO_CODE && ! Alpha_Admin::get_instance()->is_registered() ) {
					wp_redirect( ALPHA_GET_PRO_URI );
					die();
				}
			}

			if ( (int) get_transient( 'alpha_clean_after_setup' ) && ! alpha_doing_ajax() ) {
				$this->clean_after_import();
			}
		}

		/**
		 * Upgrade post install.
		 *
		 * @param bool  $return  Installation response.
		 * @param array $theme
		 * @return bool          Installation response.
		 * @since 1.0
		 */
		public function upgrader_post_install( $return, $theme ) {
			if ( is_wp_error( $return ) ) {
				return $return;
			}
			if ( get_stylesheet() != $theme ) {
				return $return;
			}
			update_option( 'alpha_setup_complete', false );

			return $return;
		}

		/**
		 * Set transient to switching theme activation redirection.
		 *
		 * @since 1.0
		 */
		public function switch_theme() {
			set_transient( '_' . $this->theme_name . '_activation_redirect', 1 );
		}

		/**
		 * Admin redirect
		 *
		 * @since 1.0
		 */
		public function admin_redirects() {
			ob_start();

			if ( ! get_transient( '_' . $this->theme_name . '_activation_redirect' ) || get_option( 'alpha_setup_complete', false ) ) {
				return;
			}
			delete_transient( '_' . $this->theme_name . '_activation_redirect' );
			wp_safe_redirect( admin_url( $this->page_url ) );
			exit;
		}

		/**
		 * Get TGM instance.
		 *
		 * @since 1.0
		 */
		public function get_tgmpa_instanse() {
			$this->tgmpa_instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		}

		/**
		 * Get TGM url
		 *
		 * @since 1.0
		 */
		public function set_tgmpa_url() {

			$this->tgmpa_menu_slug = ( property_exists( $this->tgmpa_instance, 'menu' ) ) ? $this->tgmpa_instance->menu : $this->tgmpa_menu_slug;
			$this->tgmpa_menu_slug = apply_filters( $this->theme_name . '_theme_setup_wizard_tgmpa_menu_slug', $this->tgmpa_menu_slug );

			$tgmpa_parent_slug = ( property_exists( $this->tgmpa_instance, 'parent_slug' ) && 'themes.php' !== $this->tgmpa_instance->parent_slug ) ? 'admin.php' : 'themes.php';

			$this->tgmpa_url = apply_filters( $this->theme_name . '_theme_setup_wizard_tgmpa_url', $tgmpa_parent_slug . '?page=' . $this->tgmpa_menu_slug );

		}

		/**
		 * Init wizard steps.
		 *
		 * @since 1.0
		 */
		public function init_wizard_steps() {

			$this->steps['status'] = array(
				'name'    => esc_html__( 'System Status', 'alpha' ),
				'view'    => array( $this, 'view_status' ),
				'handler' => '',
			);

			if ( ALPHA_ENVATO_CODE || defined( 'ALPHA_PRO_VERSION' ) ) {
				$this->steps['activation'] = array(
					'name'    => esc_html__( 'Activation', 'alpha' ),
					'view'    => array( $this, 'view_activation' ),
					'handler' => '',
				);
			}

			$this->steps['customize'] = array(
				'name'    => esc_html__( 'Child Theme', 'alpha' ),
				'view'    => array( $this, 'view_customize' ),
				'handler' => '',
			);

			if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
				$this->steps['default_plugins'] = array(
					'name'    => esc_html__( 'Install Plugins', 'alpha' ),
					'view'    => array( $this, 'view_default_plugins' ),
					'handler' => '',
				);

				// $this->steps['addons'] = array(
				// 	'name'    => esc_html__( 'Install Addons', 'alpha' ),
				// 	'view'    => array( $this, 'view_addons' ),
				// 	'handler' => '',
				// );
			}

			$this->steps['demo_content'] = array(
				'name'    => esc_html__( 'Import Demo', 'alpha' ),
				'view'    => array( $this, 'view_demo_content' ),
				'handler' => array( $this, 'alpha_setup_wizard_demo_content_save' ),
			);

			$this->steps['ready'] = array(
				'name'    => esc_html__( 'Ready!', 'alpha' ),
				'view'    => array( $this, 'view_ready' ),
				'handler' => '',
			);

			/**
			 * Filters the steps of setup wizard.
			 *
			 * @since 1.0
			 */
			$this->steps = apply_filters( 'alpha_setup_wizard_steps', $this->steps );
		}

		/**
		 * Enqueue style & script for setup wizard.
		 *
		 * @since 1.0
		 */
		public function enqueue() {

			if ( empty( $_GET['page'] ) || $this->page_slug !== $_GET['page'] ) {
				return;
			}

			$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

			// Style
			wp_enqueue_style( 'alpha-magnific-popup' );
			wp_enqueue_style( 'wp-admin' );
			wp_enqueue_media();

			// Script
			wp_enqueue_script( 'isotope-pkgd' );
			wp_enqueue_script( 'alpha-magnific-popup' );
			wp_enqueue_script( 'alpha-admin-wizard', alpha_framework_uri( '/admin/panel/wizard' . ALPHA_JS_SUFFIX ), array( 'jquery-core' ), true, 50 );
			wp_enqueue_script( 'media' );

			wp_localize_script(
				'alpha-admin-wizard',
				'alpha_setup_wizard_params',
				apply_filters(
					'alpha_setup_wizard_params',
					array(
						'tgm_plugin_nonce' => array(
							'update'  => wp_create_nonce( 'tgmpa-update' ),
							'install' => wp_create_nonce( 'tgmpa-install' ),
						),
						'tgm_bulk_url'     => esc_url( admin_url( $this->tgmpa_url ) ),
						'wpnonce'          => wp_create_nonce( 'alpha_setup_wizard_nonce' ),
						'texts'            => array(
							'confirm_leave'    => esc_html__( 'Are you sure you want to leave?', 'alpha' ),
							'confirm_override' => esc_html__( 'Are you sure to import demo contents and override old one?', 'alpha' ),
							/* translators: $1 and $2 opening and closing strong tags respectively */
							'import_failed'    => vsprintf( esc_html__( 'Failed importing! Please check the %1$s"System Status"%2$s tab to ensure your server meets all requirements for a successful import. Settings that need attention will be listed in red. If your server provider does not allow to update settings, please try using alternative import mode.', 'alpha' ), array( '<a href="' . esc_url( $this->page_url . '&step=status' ) . '" target="_blank">', '</a>' ) ),
							'install_failed'   => esc_html__( 'Installation is failed!', 'alpha' ),
							'install_finished' => esc_html__( 'Installation is finished!', 'alpha' ),
							'installing'       => esc_html__( 'Installing', 'alpha' ),
							'demo_import'      => esc_html__( 'Demo Import', 'alpha' ),
							'visit_your_site'  => esc_html__( 'Visit your site.', 'alpha' ),
							'failed'           => esc_html__( 'Failed', 'alpha' ),
							'ajax_error'       => esc_html__( 'Ajax error', 'alpha' ),
							'try_again'        => esc_html__( 'Removed failed. Please refresh and try again.', 'alpha' ),
							'removed'          => esc_html__( 'Removed successfully.', 'alpha' ),
						),
					)
				)
			);
		}

		/**
		 * Display setup wizard
		 *
		 * @since 1.0
		 */
		public function view_setup_wizard() {
			$title        = array(
				'title' => esc_html__( 'Setup Wizard', 'alpha' ),
				'desc'  => sprintf( esc_html__( '%s quick setup wizard will help you to configure your new website. This wizard will import a pre-built demo and install the required plugins with just some clicks.', 'alpha' ), ALPHA_DISPLAY_NAME ),
			);
			$admin_config = Alpha_Admin::get_instance()->admin_config;
			Alpha_Admin_Panel::get_instance()->view_header( 'setup_wizard', $admin_config, $title );
			include alpha_framework_path( ALPHA_SETUP_WIZARD . '/views/index.php' );
			Alpha_Admin_Panel::get_instance()->view_footer( 'setup_wizard', $admin_config );
		}

		/**
		 * Evaluate step callback for current.
		 *
		 * @since 1.0
		 */
		public function view_step() {
			$show_content = true;
			if ( ! empty( $_REQUEST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
				$show_content = call_user_func( $this->steps[ $this->step ]['handler'] );
			}
			if ( $show_content && isset( $this->steps[ $this->step ] ) ) {
				call_user_func( $this->steps[ $this->step ]['view'] );
			}
		}

		/**
		 * Output the step contents.
		 *
		 * @since 1.0
		 */
		public function view_status() {
			include alpha_framework_path( ALPHA_SETUP_WIZARD . '/views/status.php' );
		}

		/**
		 * Output the activation contents.
		 *
		 * @since 1.0
		 */
		public function view_activation() {
			include alpha_framework_path( ALPHA_SETUP_WIZARD . '/views/activation.php' );
		}
		
		/**
		 * Output the child theme step.
		 *
		 * @since 1.0
		 */
		public function view_customize() {
			include alpha_framework_path( ALPHA_SETUP_WIZARD . '/views/customize.php' );
		}

		/**
		 * View default plugins.
		 *
		 * @since 1.0
		 */
		public function view_default_plugins() {

			tgmpa_load_bulk_installer();
			if ( ! class_exists( 'TGM_Plugin_Activation' ) || ! isset( $GLOBALS['tgmpa'] ) ) {
				die( esc_html__( 'Failed to find TGM', 'alpha' ) );
			}
			$url     = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'alpha-setup-wizard' );
			$plugins = $this->_get_plugins();

			$method = '';
			$fields = array_keys( $_POST );

			if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
				return true;
			}

			if ( ! WP_Filesystem( $creds ) ) {
				request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );
				return true;
			}

			include alpha_framework_path( ALPHA_SETUP_WIZARD . '/views/plugins.php' );
		}

		/**
		 * View addons.
		 *
		 * @since 1.0
		 */
		public function view_addons() {

			tgmpa_load_bulk_installer();
			if ( ! class_exists( 'TGM_Plugin_Activation' ) || ! isset( $GLOBALS['tgmpa'] ) ) {
				die( esc_html__( 'Failed to find TGM', 'alpha' ) );
			}
			$url     = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'alpha-setup-wizard' );
			$plugins = $this->_get_plugins();

			$method = '';
			$fields = array_keys( $_POST );

			if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
				return true;
			}

			if ( ! WP_Filesystem( $creds ) ) {
				request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );
				return true;
			}

			include alpha_framework_path( ALPHA_SETUP_WIZARD . '/views/addons.php' );
		}

		/**
		 * Output the demo contents.
		 *
		 * @since 1.0
		 */
		public function view_demo_content() {
			$url    = wp_nonce_url( add_query_arg( array( 'demo_content' => 'go' ) ), 'alpha-setup-wizard' );
			$method = '';
			$fields = array_keys( $_POST );
			if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) { // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments.Found
				return true;
			}

			if ( ! WP_Filesystem( $creds ) ) {
				request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );
				return true;
			}
			include alpha_framework_path( ALPHA_SETUP_WIZARD . '/views/demo.php' );
		}

		/**
		 * Output the support step.
		 *
		 * @since 1.0
		 */
		public function view_support() {
			include alpha_framework_path( ALPHA_SETUP_WIZARD . '/views/support.php' );
		}

		/**
		 * Output the ready step.
		 *
		 * @since 1.0
		 */
		public function view_ready() {
			include alpha_framework_path( ALPHA_SETUP_WIZARD . '/views/ready.php' );
		}

		/**
		 * Save actions
		 *
		 * @since 1.0
		 */
		public function alpha_setup_wizard_welcome_save() {
			check_admin_referer( 'alpha-setup-wizard' );
			return false;
		}

		/**
		 * Save custom logo in demo content step.
		 *
		 * @since 1.0
		 */
		public function alpha_setup_wizard_demo_content_save() {
			check_admin_referer( 'alpha-setup-wizard' );
			if ( ! empty( $_POST['new_logo_id'] ) ) {
				$new_logo_id = (int) $_POST['new_logo_id'];
				if ( $new_logo_id ) {
					set_theme_mod( 'custom_logo', $new_logo_id );
				}
			}
			wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
			die();
		}

		/**
		 * Create child theme
		 *
		 * @param string $new_theme_title Child theme name.
		 * @since 1.0
		 */
		private function _make_child_theme( $new_theme_title ) {

			$parent_theme_template = get_template();
			$new_theme_name        = sanitize_title( $new_theme_title );
			$theme_root            = get_theme_root();

			$new_theme_path = $theme_root . '/' . $new_theme_name;
			if ( ! file_exists( $new_theme_path ) ) {
				wp_mkdir_p( $new_theme_path );

				$plugin_folder = get_parent_theme_file_path( 'inc/admin/setup-wizard/alpus-child/' );

				ob_start();
				require $plugin_folder . 'style.css.php';
				$css = ob_get_clean();

				// filesystem
				global $wp_filesystem;
				// Initialize the WordPress filesystem, no more using file_put_contents function
				if ( empty( $wp_filesystem ) ) {
					require_once ABSPATH . '/wp-admin/includes/file.php';
					WP_Filesystem();
				}

				if ( ! $wp_filesystem->put_contents( $new_theme_path . '/style.css', $css, FS_CHMOD_FILE ) ) {
					echo '<p class="lead success">';
					/* translators: %s: path */
					printf( esc_html__( 'Directory permission required for %s', 'alpha' ), '/wp-content/themes.' );
					echo '</p>';
					return;
				}

				// Copy functions.php
				copy( $plugin_folder . 'functions.php', $new_theme_path . '/functions.php' );

				// Copy screenshot
				copy( $plugin_folder . 'screenshot.jpg', $new_theme_path . '/screenshot.jpg' );

				// Make child theme an allowed theme (network enable theme)
				$allowed_themes[ $new_theme_name ] = true;
			}

			// Switch to theme
			if ( $parent_theme_template !== $new_theme_name ) {

				echo '<p class="lead success">';
				/* translators: %1$s: Theme name, %1$s: br tag, %3$s: path */
				printf( esc_html__( 'Child Theme %1$s has been created and activated!%2$s Folder is located in %3$s', 'alpha' ), '<strong>' . esc_html( $new_theme_title ) . '</strong>', '<br />', 'wp-content/themes/<strong>' . esc_html( $new_theme_name ) . '</strong>' );
				echo '</p>';
				switch_theme( $new_theme_name, $new_theme_name );

				if ( empty( get_theme_mod( 'container' ) ) ) {
					update_option( 'theme_mods_' . $new_theme_name, get_option( 'theme_mods_' . $parent_theme_template ) );
				}
			}
		}

		/**
		 * Install plugins
		 *
		 * @since 1.0
		 */
		public function tgmpa_load( $status ) {
			return is_admin() || current_user_can( 'install_themes' );
		}

		/**
		 * Get plugins for demo import.
		 *
		 * @return array Get plugins.
		 * @since 1.0
		 */
		private function _get_plugins() {
			$instance         = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
			$plugin_func_name = 'is_plugin_active';
			$plugins          = array(
				'all'      => array(), // Meaning: all plugins which still have open actions.
				'install'  => array(),
				'update'   => array(),
				'activate' => array(),
			);
			foreach ( $instance->plugins as $slug => $plugin ) {
				$new_version = $instance->does_plugin_have_update( $slug );
				if ( empty( $plugin['visibility'] ) || 'setup_wizard' != $plugin['visibility'] || ( $instance->$plugin_func_name( $slug ) && false === $new_version ) ) {
					continue;
				} else {
					$plugins['all'][ $slug ] = $plugin;

					if ( ! $instance->is_plugin_installed( $slug ) ) {
						$plugins['install'][ $slug ] = $plugin;
					} else {
						if ( false !== $new_version ) {
							$plugins['update'][ $slug ] = $plugin;
							$plugins['update'][ $slug ]['version'] = $new_version;
						}

						if ( $instance->can_plugin_activate( $slug ) ) {
							$plugins['activate'][ $slug ] = $plugin;
						}
					}
				}
			}
			return $plugins;
		}

		/**
		 * Ajax plugins
		 *
		 * @since 1.0
		 */
		public function ajax_plugins() {
			if ( ! check_ajax_referer( 'alpha_setup_wizard_nonce', 'wpnonce' ) || empty( $_POST['slug'] ) ) {
				wp_send_json_error(
					array(
						'error'   => 1,
						'message' => esc_html__(
							'No Slug Found',
							'alpha'
						),
					)
				);
			}
			$json = array();
			// send back some json we use to hit up TGM
			$plugins = $this->_get_plugins();
			// what are we doing with this plugin?
			foreach ( $plugins['activate'] as $slug => $plugin ) {
				if ( $_POST['slug'] == $slug ) {
					$json = array(
						'url'           => esc_url( admin_url( $this->tgmpa_url ) ),
						'plugin'        => array( $slug ),
						'tgmpa-page'    => $this->tgmpa_menu_slug,
						'plugin_status' => 'all',
						'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
						'action'        => 'tgmpa-bulk-activate',
						'action2'       => -1,
						'message'       => esc_html__( 'Activating', 'alpha' ),
					);
					break;
				}
			}
			foreach ( $plugins['update'] as $slug => $plugin ) {
				if ( $_POST['slug'] == $slug ) {
					$json = array(
						'url'           => esc_url( admin_url( $this->tgmpa_url ) ),
						'plugin'        => array( $slug ),
						'tgmpa-page'    => $this->tgmpa_menu_slug,
						'plugin_status' => 'all',
						'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
						'action'        => 'tgmpa-bulk-update',
						'action2'       => -1,
						'message'       => esc_html__( 'Updating', 'alpha' ),
					);
					break;
				}
			}
			foreach ( $plugins['install'] as $slug => $plugin ) {
				if ( $_POST['slug'] == $slug ) {
					$json = array(
						'url'           => esc_url( admin_url( $this->tgmpa_url ) ),
						'plugin'        => array( $slug ),
						'tgmpa-page'    => $this->tgmpa_menu_slug,
						'plugin_status' => 'all',
						'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
						'action'        => 'tgmpa-bulk-install',
						'action2'       => -1,
						'message'       => esc_html__( 'Installing', 'alpha' ),
					);
					break;
				}
			}

			if ( $json ) {
				$json['hash'] = md5( serialize( $json ) ); // used for checking if duplicates happen, move to next plugin
				wp_send_json( $json );
			} else {
				wp_send_json(
					array(
						'done'    => 1,
						'message' => esc_html__(
							'Success',
							'alpha'
						),
					)
				);
			}
			exit;
		}

		/**
		 * Step links.
		 *
		 * @param string $step The current step.
		 * @since 1.0
		 */
		public function get_step_link( $step ) {
			return add_query_arg( 'step', $step, admin_url( 'admin.php?page=' . $this->page_slug ) );
		}

		/**
		 * Get next step link.
		 *
		 * @since 1.0
		 */
		public function get_next_step_link() {
			$keys = array_keys( $this->steps );
			return add_query_arg( 'step', $keys[ array_search( $this->step, array_keys( $this->steps ) ) + 1 ], remove_query_arg( 'translation_updated' ) );
		}

		/**
		 * get demo import file
		 *
		 * @param bool|string $demo Demo for import
		 * @since 1.0
		 */
		public function get_demo_file( $demo = false ) {
			if ( ! $demo ) {
				$demo = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? sanitize_text_field( $_POST['demo'] ) : 'landing';
			}

			$this->demo = $demo;

			// Return demo file path
			require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/importer/importer-api.php' );

			$importer_api = new Alpha_Importer_API( $demo );

			$demo_file_path = $importer_api->get_remote_demo();

			if ( ! $demo_file_path ) {
				echo json_encode(
					array(
						'process' => 'error',
						'message' => esc_html__( 'Remote API error.', 'alpha' ),
					)
				);
				die();
			} elseif ( is_wp_error( $demo_file_path ) ) {
				echo json_encode(
					array(
						'process' => 'error',
						'message' => $demo_file_path->get_error_message(),
					)
				);
				die();
			}
			return $demo_file_path;
		}

		/**
		 * Get file data.
		 *
		 * @param string $path Import demo file path.
		 * @since 1.0
		 */
		public function get_file_data( $path ) {
			$data = false;
			$path = wp_normalize_path( $path );
			// filesystem
			global $wp_filesystem;
			// Initialize the WordPress filesystem, no more using file_put_contents function
			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}
			if ( $wp_filesystem->exists( $path ) ) {
				$data = $wp_filesystem->get_contents( $path );
			}
			return $data;
		}

		/**
		 * Download demo file from server.
		 *
		 * @since 1.0
		 */
		public function download_demo_file() {
			if ( ! check_ajax_referer( 'alpha_setup_wizard_nonce', 'wpnonce', false ) ) {
				die();
			}
			$this->get_demo_file();
			echo json_encode( array( 'process' => 'success' ) );
			die();
		}

		/**
		 * Delete temporary directory.
		 *
		 * @since 1.0
		 */
		public function delete_tmp_dir() {
			if ( ! check_ajax_referer( 'alpha_setup_wizard_nonce', 'wpnonce', false ) ) {
				die();
			}
			$demo = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? sanitize_text_field( $_POST['demo'] ) : 'landing';

			// Importer remote API
			require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/importer/importer-api.php' );
			$importer_api = new Alpha_Importer_API( $demo );
			$importer_api->delete_temp_dir();
			die();
		}

		/**
		 * Reset menus for importing progress.
		 *
		 * @since 1.0
		 */
		public function reset_menus() {
			if ( ! check_ajax_referer( 'alpha_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) ) {
				/**
				 * Fires before reset menus.
				 *
				 * @since 1.0
				 */
				do_action( 'alpha_importer_before_reset_menus' );

				$import_shortcodes = ( isset( $_POST['import_shortcodes'] ) && 'true' == $_POST['import_shortcodes'] ) ? true : false;
				if ( $import_shortcodes ) {
					$menus = array( 'Main Menu', 'Category Menu', 'Top Navigation', 'Currency Switcher', 'Language Switcher', 'Footer Nav 1', 'Footer Nav 2', 'Footer Nav 3', 'Deal Menu', 'Category Menu 1', 'Category Menu 2', 'Header Nav' );
				} else {
					$menus = array( 'Main Menu', 'Category Menu', 'Top Navigation', 'Currency Switcher', 'Language Switcher', 'Footer Nav 1', 'Footer Nav 2', 'Footer Nav 3', 'Deal Menu', 'Category Menu 1', 'Category Menu 2', 'Header Nav' );
				}
				/**
				 * Filters the menus for importing progress.
				 *
				 * @since 1.0
				 */
				$menus = apply_filters( 'alpha_reset_menus', $menus );
				foreach ( $menus as $menu ) {
					wp_delete_nav_menu( $menu );
				}
				esc_html_e( 'Successfully reset menus!', 'alpha' );
			}
			die;
		}

		/**
		 * Reset widgets.
		 *
		 * @since 1.0
		 */
		public function reset_widgets() {
			if ( ! check_ajax_referer( 'alpha_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) ) {
				/**
				 * Fires before import widgets.
				 *
				 * @since 1.0
				 */
				do_action( 'alpha_importer_before_import_widgets' );

				ob_start();
				$sidebars_widgets = retrieve_widgets();
				foreach ( $sidebars_widgets as $area => $widgets ) {
					foreach ( $widgets as $key => $widget_id ) {
						$pieces       = explode( '-', $widget_id );
						$multi_number = array_pop( $pieces );
						$id_base      = implode( '-', $pieces );
						$widget       = get_option( 'widget_' . $id_base );
						unset( $widget[ $multi_number ] );
						update_option( 'widget_' . $id_base, $widget );
						unset( $sidebars_widgets[ $area ][ $key ] );
					}
				}

				update_option( 'sidebars_widgets', $sidebars_widgets );
				ob_clean();
				ob_end_clean();
				esc_html_e( 'Successfully reset widgets!', 'alpha' );
			}
			die;
		}

		/**
		 * Import dummy.
		 *
		 * @since 1.0
		 */
		public function import_dummy() {
			if ( ! check_ajax_referer( 'alpha_setup_wizard_nonce', 'wpnonce', false ) ) {
				die();
			}
			global $import_logo;
			if ( empty( $import_logo ) ) {
				$import_logo = alpha_get_option( 'custom_logo' );
			}
			if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
				define( 'WP_LOAD_IMPORTERS', true ); // we are loading importers
			}
			if ( ! class_exists( 'WP_Importer' ) ) { // if main importer class doesn't exist
				require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			}
			if ( ! class_exists( 'WP_Import' ) ) { // if WP importer doesn't exist
				require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/importer/wordpress-importer.php' );
			}

			if ( current_user_can( 'manage_options' ) && class_exists( 'WP_Importer' ) && class_exists( 'WP_Import' ) ) { // check for main import class and wp import class

				$demo                        = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? sanitize_text_field( $_POST['demo'] ) : 'landing';
				$process                     = ( isset( $_POST['process'] ) && $_POST['process'] ) ? sanitize_text_field( $_POST['process'] ) : 'import_start';
				$demo_path                   = $this->get_demo_file();
				$importer                    = new WP_Import();
				$theme_xml                   = $demo_path . '/content.xml';
				$importer->fetch_attachments = true;

				$this->import_before_functions( $demo );

				// ob_start();

				do_action( 'alpha_importer_before_import_dummy' );
				$response = $importer->import( $theme_xml, $process );

				// ob_end_clean();

				if ( 'import_start' == $process && $response ) {
					echo json_encode(
						array(
							'process' => 'importing',
							'count'   => 0,
							'index'   => 0,
							'message' => 'success',
						)
					);
				} else {
					$this->import_after_functions( $demo );
				}
			}
			die();
		}

		/**
		 * Import override contents.
		 *
		 * @param int $post_exists Existing post id
		 * @param int $post        new post id
		 * @since 1.0
		 */
		public function import_override_contents( $post_exists, $post ) {
			$override_contents = ( isset( $_POST['override_contents'] ) && 'true' == $_POST['override_contents'] ) ? true : false;
			if ( ! $override_contents || ( $post_exists && get_post_type( $post_exists ) != 'revision' ) ) {
				return $post_exists;
			}

			// remove posts which have same ID
			$processed_duplicates = get_option( 'alpha_import_processed_duplicates', array() );
			if ( in_array( $post['post_id'], $processed_duplicates ) ) {
				return false;
			}
			$old_post = get_post( $post['post_id'] );
			if ( $old_post ) {
				if ( $old_post->post_type == $post['post_type'] && in_array( $post['post_type'], $this->demo_import_post_types ) ) {
					return $post['post_id'];
				}
				if ( alpha_get_feature( 'fs_pb_elementor' ) && defined( 'ELEMENTOR_VERSION' ) && 'kit' == get_post_meta( $post['post_id'], '_elementor_template_type', true ) ) {
					$_GET['force_delete_kit'] = true;
				}
				wp_delete_post( $post['post_id'], true );
				unset( $_GET['force_delete_kit'] );

				if ( get_post( (int) $post['post_id'] ) ) {
					wp_cache_flush();
				}
			}

			// remove posts which have same title and slug
			global $wpdb;

			$post_title = wp_unslash( sanitize_post_field( 'post_title', $post['post_title'], 0, 'db' ) );
			$post_name  = wp_unslash( sanitize_post_field( 'post_name', $post['post_name'], 0, 'db' ) );

			$query  = "SELECT ID FROM $wpdb->posts WHERE 1=1";
			$args   = array();
			$query .= ' AND post_title = %s';
			$args[] = $post_title;
			$query .= ' AND post_name = %s';
			$args[] = $post_name;

			$old_post = (int) $wpdb->get_var( $wpdb->prepare( $query, $args ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			if ( $old_post && get_post_type( $old_post ) == $post['post_type'] ) {
				if ( in_array( $post['post_type'], $this->demo_import_post_types ) ) {
					$processed_duplicates[] = $old_post;
					update_option( 'alpha_import_processed_duplicates', $processed_duplicates );
					return $old_post;
				}
				if ( alpha_get_feature( 'fs_pb_elementor' ) && defined( 'ELEMENTOR_VERSION' ) && 'kit' == get_post_meta( $old_post, '_elementor_template_type', true ) ) {
					$_GET['force_delete_kit'] = true;
				}
				wp_delete_post( $old_post, true );
				unset( $_GET['force_delete_kit'] );

				if ( get_post( $old_post ) ) {
					wp_cache_flush();
				}
			}

			return false;
		}

		/**
		 * Import dummy start.
		 *
		 * @since 1.0
		 */
		public function import_dummy_start() {
			$process = ( isset( $_POST['process'] ) && $_POST['process'] ) ? sanitize_text_field( $_POST['process'] ) : 'import_start';
			if ( current_user_can( 'manage_options' ) && 'import_start' == $process ) {
				delete_option( 'alpha_import_processed_duplicates' );
			}

			if ( class_exists( 'WC_Comments' ) ) {
				remove_action( 'wp_update_comment_count', array( 'WC_Comments', 'clear_transients' ) );
			}
		}

		/**
		 * Import dummy end.
		 *
		 * @since 1.0
		 */
		public function import_dummy_end() {
			if ( current_user_can( 'manage_options' ) && isset( $_POST['action'] ) && 'alpha_import_dummy' == $_POST['action'] ) {
				ob_end_clean();
				ob_start();
				echo json_encode(
					array(
						'process' => 'complete',
						'message' => 'success',
					)
				);
				ob_end_flush();
				ob_start();
			}

			if ( class_exists( 'WC_Comments' ) ) {
				add_action( 'wp_update_comment_count', array( 'WC_Comments', 'clear_transients' ) );
			}
		}

		/**
		 * Import dummy step by step.
		 *
		 * @since 1.0
		 */
		public function import_dummy_step_by_step() {
			if ( ! check_ajax_referer( 'alpha_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}

			global $import_logo;
			if ( empty( $import_logo ) ) {
				$import_logo = alpha_get_option( 'custom_logo' );
			}

			if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
				define( 'WP_LOAD_IMPORTERS', true ); // we are loading importers
			}

			if ( ! class_exists( 'WP_Importer' ) ) { // if main importer class doesn't exist
				$wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
				include $wp_importer;
			}

			if ( ! class_exists( 'Alpha_WP_Import' ) ) { // if WP importer doesn't exist
				$wp_import = alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/importer/theme-wordpress-importer.php' );
				include $wp_import;
			}

			if ( current_user_can( 'manage_options' ) && class_exists( 'WP_Importer' ) && class_exists( 'Alpha_WP_Import' ) ) { // check for main import class and wp import class

				$process   = ( isset( $_POST['process'] ) && $_POST['process'] ) ? sanitize_text_field( $_POST['process'] ) : 'import_start';
				$demo      = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? sanitize_text_field( $_POST['demo'] ) : 'landing';
				$index     = ( isset( $_POST['index'] ) && $_POST['index'] ) ? (int) $_POST['index'] : 0;
				$demo_path = $this->get_demo_file();

				$importer                    = new Alpha_WP_Import();
				$theme_xml                   = $demo_path . '/content.xml';
				$importer->fetch_attachments = true;

				if ( 'import_start' == $process ) {
					$this->import_before_functions( $demo );
				}

				$loop = (int) ( ini_get( 'max_execution_time' ) / 60 );
				if ( $loop < 1 ) {
					$loop = 1;
				}
				if ( $loop > 10 ) {
					$loop = 10;
				}
				$i = 0;

				do_action( 'alpha_importer_before_import_dummy' );

				while ( $i < $loop ) {
					$response = $importer->import( $theme_xml, $process, $index );
					if ( isset( $response['count'] ) && isset( $response['index'] ) && $response['count'] && $response['index'] && $response['index'] < $response['count'] ) {
						++ $i;
						$index = $response['index'];
					} else {
						break;
					}
				}

				echo json_encode( $response );
				ob_start();
				if ( 'complete' == $response['process'] ) {
					$this->import_after_functions( $demo );
				}
				ob_end_clean();
			}
			die();
		}

		/**
		 * Import widgets.
		 *
		 * @since 1.0
		 */
		public function import_widgets() {
			if ( ! check_ajax_referer( 'alpha_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) ) {
				/**
				 * Fires before import widgets.
				 *
				 * @since 1.0
				 */
				do_action( 'alpha_importer_before_import_widgets' );

				// Import widgets
				$demo_path   = $this->get_demo_file();
				$widget_data = $this->get_file_data( $demo_path . '/widget_data.json' );
				$this->before_replacement();
				$widget_data = preg_replace_callback( '|(\"nav_menu\":)(\d+)|', array( $this, 'replace_term_ids' ), $widget_data );
				$this->import_widget_data( $widget_data );
				esc_html_e( 'Successfully imported widgets!', 'alpha' );
				flush_rewrite_rules();
			}
			die();
		}

		/**
		 * Import override subpages.
		 *
		 * @param WP_Post $post_exists
		 * @param WP_Post $post         The sub page post instance
		 * @since 1.0
		 */
		public function import_override_subpages( $post_exists, $post ) {
			// remove posts which have same title and slug
			global $wpdb;

			$post_title = wp_unslash( sanitize_post_field( 'post_title', $post['post_title'], 0, 'db' ) );
			$post_name  = wp_unslash( sanitize_post_field( 'post_name', $post['post_name'], 0, 'db' ) );

			$query  = "SELECT ID FROM $wpdb->posts WHERE 1=1";
			$args   = array();
			$query .= ' AND post_title = %s';
			$args[] = $post_title;
			$query .= ' AND post_name = %s';
			$args[] = $post_name;

			$old_post = (int) $wpdb->get_var( $wpdb->prepare( $query, $args ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

			if ( $old_post && get_post_type( $old_post ) == $post['post_type'] ) {
				wp_delete_post( $old_post, true );
			}

			return false;
		}

		/**
		 * Import theme options.
		 *
		 * @since 1.0
		 */
		public function import_options() {
			if ( ! check_ajax_referer( 'alpha_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) ) {
				/**
				 * Fires before import theme options.
				 *
				 * @since 1.0
				 */
				do_action( 'alpha_importer_before_import_options' );

				$demo_path = $this->get_demo_file();
				ob_start();
				include $demo_path . '/theme_options.php';
				$options = ob_get_clean();

				ob_start();
				$options = str_replace( 'IMPORT_SITE_URL', get_home_url(), $options );
				$options = json_decode( $options, true );
				if ( ! isset( $options['theme'] ) || ! isset( $options['sidebars'] ) ) {
					die();
				}

				ob_clean();
				ob_end_clean();
				echo 'success';
				try {
					update_option( 'alpha_sidebars', $options['sidebars'] );
					alpha_import_theme_options( false, $options['theme'] );
				} catch ( Exception $e ) {
					esc_html_e( 'Please compile default css files by publishing options in customize panel.', 'alpha' );
				}

				// after setup, compile dynamic css vars.
				set_transient( 'alpha_clean_after_setup', 1 );
			}

			die();
		}

		/**
		 * Get post id from imported id.
		 *
		 * @since 1.0
		 */
		private function get_post_id_from_imported_id( $import_id, $demo ) {
			global $wpdb;
			$result = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_alpha_demo' AND meta_value = %s LIMIT 1", sanitize_title( $demo ) . '#' . sanitize_title( $import_id ) ) );
			if ( $result ) {
				return array(
					'id'    => (int) $result,
					'title' => '',
				);
			}
			return false;
		}

		/**
		 * Parsing Widgets Function
		 *
		 * @see http://wordpress.org/plugins/widget-settings-importexport/
		 * @since 1.0
		 */
		private function import_widget_data( $widget_data ) {
			$json_data = $widget_data;
			$json_data = json_decode( $json_data, true );

			$sidebar_data = $json_data[0];
			$widget_data  = $json_data[1];

			foreach ( $widget_data as $widget_data_title => $widget_data_value ) {
				$widgets[ $widget_data_title ] = array();
				foreach ( $widget_data_value as $widget_data_key => $widget_data_array ) {
					if ( is_int( $widget_data_key ) ) {
						$widgets[ $widget_data_title ][ $widget_data_key ] = 'on';
					}
				}
			}
			unset( $widgets[''] );

			foreach ( $sidebar_data as $title => $sidebar ) {
				$count = count( $sidebar );
				for ( $i = 0; $i < $count; $i++ ) {
					$widget               = array();
					$widget['type']       = trim( substr( $sidebar[ $i ], 0, strrpos( $sidebar[ $i ], '-' ) ) );
					$widget['type-index'] = trim( substr( $sidebar[ $i ], strrpos( $sidebar[ $i ], '-' ) + 1 ) );
					if ( ! isset( $widgets[ $widget['type'] ][ $widget['type-index'] ] ) ) {
						unset( $sidebar_data[ $title ][ $i ] );
					}
				}
				$sidebar_data[ $title ] = array_values( $sidebar_data[ $title ] );
			}

			foreach ( $widgets as $widget_title => $widget_value ) {
				foreach ( $widget_value as $widget_key => $widget_value ) {
					$widgets[ $widget_title ][ $widget_key ] = $widget_data[ $widget_title ][ $widget_key ];
				}
			}

			$sidebar_data = array( array_filter( $sidebar_data ), $widgets );
			$this->parse_import_data( $sidebar_data );
		}

		/**
		 * Parse import demo data.
		 *
		 * @param array $import_array
		 * @since 1.0
		 */
		private function parse_import_data( $import_array ) {
			global $wp_registered_sidebars;
			$sidebars_data    = $import_array[0];
			$widget_data      = $import_array[1];
			$current_sidebars = get_option( 'sidebars_widgets' );
			$new_widgets      = array();

			foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :

				foreach ( $import_widgets as $import_widget ) :
					// if the sidebar exists
					if ( isset( $wp_registered_sidebars[ $import_sidebar ] ) ) :
						$title               = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
						$index               = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
						$current_widget_data = get_option( 'widget_' . $title );
						$new_widget_name     = $this->get_new_widget_name( $title, $index );
						$new_index           = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

						if ( ! empty( $new_widgets[ $title ] ) && is_array( $new_widgets[ $title ] ) ) {
							while ( array_key_exists( $new_index, $new_widgets[ $title ] ) ) {
								++$new_index;
							}
						}
						$current_sidebars[ $import_sidebar ][] = $title . '-' . $new_index;
						if ( array_key_exists( $title, $new_widgets ) ) {
							$new_widgets[ $title ][ $new_index ] = $widget_data[ $title ][ $index ];
							$multiwidget                         = $new_widgets[ $title ]['_multiwidget'];
							unset( $new_widgets[ $title ]['_multiwidget'] );
							$new_widgets[ $title ]['_multiwidget'] = $multiwidget;
						} else {
							$current_widget_data[ $new_index ] = $widget_data[ $title ][ $index ];
							$current_multiwidget               = ( isset( $current_widget_data['_multiwidget'] ) ) ? $current_widget_data['_multiwidget'] : '';
							$new_multiwidget                   = isset( $widget_data[ $title ]['_multiwidget'] ) ? $widget_data[ $title ]['_multiwidget'] : false;
							$multiwidget                       = ( $current_multiwidget != $new_multiwidget ) ? $current_multiwidget : 1;
							unset( $current_widget_data['_multiwidget'] );
							$current_widget_data['_multiwidget'] = $multiwidget;
							$new_widgets[ $title ]               = $current_widget_data;
						}

					endif;
				endforeach;
			endforeach;

			if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
				update_option( 'sidebars_widgets', $current_sidebars );

				foreach ( $new_widgets as $title => $content ) {
					update_option( 'widget_' . $title, $content );
				}

				return true;
			}

			return false;
		}

		/**
		 * Get new widget name.
		 *
		 * @param string $widget_name
		 * @param int    $widet_index
		 * @since 1.0
		 */
		private function get_new_widget_name( $widget_name, $widget_index ) {
			$current_sidebars = get_option( 'sidebars_widgets' );
			$all_widget_array = array();
			foreach ( $current_sidebars as $sidebar => $widgets ) {
				if ( ! empty( $widgets ) && is_array( $widgets ) && 'wp_inactive_widgets' != $sidebar ) {
					foreach ( $widgets as $widget ) {
						$all_widget_array[] = $widget;
					}
				}
			}
			while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
				++$widget_index;
			}
			$new_widget_name = $widget_name . '-' . $widget_index;
			return $new_widget_name;
		}

		/**
		 * Get page by title for import demo.
		 *
		 * @param string $page_title The page title.
		 * @param string The required return type.
		 * @return WP_Post|array|null Return post if existed, null not.
		 * @since 1.0
		 */
		private function importer_get_page_by_title( $page_title, $output = OBJECT ) {
			global $wpdb;
			$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = %s ) WHERE $wpdb->posts.post_title = %s AND $wpdb->posts.post_type = %s order by $wpdb->postmeta.meta_value desc limit 1", 'alpha_imported_date', $page_title, 'page' ) );

			if ( $page ) {
				return get_post( $page, $output );
			}
		}

		/**
		 * Executes before import.
		 *
		 * @param string $demo The demo slug.
		 * @since 1.0
		 */
		private function import_before_functions( $demo ) {

		}

		/**
		 * Executes after import.
		 *
		 * @param string $demo The demo slug.
		 * @since 1.0
		 */
		private function import_after_functions( $demo ) {
			delete_option( 'alpha_import_processed_duplicates' );

			foreach ( $this->woopages as $woo_page_name => $woo_page_title ) {
				$woopage = alpha_get_page_by_title( $woo_page_title );
				if ( ! empty( $woopage ) && $woopage->ID ) {
					update_option( $woo_page_name, $woopage->ID ); // Front Page
				}
			}

			// We no longer need to install pages
			$notices = array_diff( get_option( 'woocommerce_admin_notices', array() ), array( 'install', 'update' ) );
			update_option( 'woocommerce_admin_notices', $notices );
			delete_option( '_wc_needs_pages' );
			delete_transient( '_wc_activation_redirect' );

			// Set reading options
			$homepage   = $this->importer_get_page_by_title( 'Home' );
			$shop_page  = $this->importer_get_page_by_title( 'Shop' );
			$posts_page = $this->importer_get_page_by_title( 'Blog' ) ? $this->importer_get_page_by_title( 'Blog' ) : $this->importer_get_page_by_title( 'News' );

			if ( ( $homepage && $homepage->ID ) || ( $shop_page && $shop_page->ID ) || ( $posts_page && $posts_page->ID ) ) {
				update_option( 'show_on_front', 'page' );
				if ( $homepage && $homepage->ID ) {
					update_option( 'page_on_front', $homepage->ID ); // Front Page
				} elseif ( $shop_page && $shop_page->ID ) {
					update_option( 'page_on_front', $shop_page->ID ); // Shop Page
				}
				if ( $posts_page && $posts_page->ID ) {
					update_option( 'page_for_posts', $posts_page->ID ); // Blog Page
				}
			}

			update_option( 'permalink_structure', '/%year%/%monthnum%/%day%/%postname%/' );

			/**
			 * Update imported IDs
			 */
			$this->before_replacement();

			//Logo
			global $import_logo, $alpha_import_posts_map;
			if ( ! empty( $import_logo ) ) {
				$new_id = $import_logo;
				if ( isset( $alpha_import_posts_map[ $import_logo ] ) ) {
					$new_id = $alpha_import_posts_map[ $import_logo ];
				}
				set_theme_mod( 'custom_logo', $new_id );
			}

			// Theme Options / Update blocks_menu imported id
			$data = alpha_get_option( '_alpha_blocks_menu' );
			if ( $data ) {
				$data = preg_replace_callback( '|(\\\")(\d+)(\\\":)|', array( $this, 'replace_term_ids' ), json_encode( $data ) );
				set_theme_mod( '_alpha_blocks_menu', json_decode( $data, true ) );
			}

			// update post ids in pages
			$args = array(
				'posts_per_page' => -1,
				'post_type'      => array( 'page', ALPHA_NAME . '_template' ),
				'post_status'    => 'publish',
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'meta_key' => '_alpha_demo',
						'compare'  => 'EXISTS',
					),
				),
			);

			// Update id for Visual Composer posts and wpb posts
			if ( 0 === strpos( $demo, 'wpb-' ) ) {
				$post_query = new WP_Query( $args );
				if ( $post_query->have_posts() ) {
					foreach ( $post_query->posts as $post ) {
						$new_content = $post->post_content;
						$new_content = preg_replace_callback( '|(id=")(\d+)(")|', array( $this, 'replace_post_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(category_ids=")([^"]*)(")|', array( $this, 'replace_term_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(product_ids=")([^"]*)(")|', array( $this, 'replace_post_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(image=")(\d+)(")|', array( $this, 'replace_post_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(images=")([^"]*)(")|', array( $this, 'replace_post_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(product_category_ids=")([^"]*)(")|', array( $this, 'replace_term_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(categories=")([^"]*)(")|', array( $this, 'replace_term_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(menu_id=")(\d+)(")|', array( $this, 'replace_term_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(nav_menu=")(\d+)(")|', array( $this, 'replace_term_ids' ), $new_content );
						$new_content = apply_filters( 'alpha_after_wpb_content', $new_content );
						if ( $post->post_content != $new_content ) {
							$post->post_content = $new_content;
							wp_update_post( $post );
						}
					}
				}
			} else {
				// Update id for Elementor posts
				$args['meta_query'][] = array(
					'meta_key' => '_elementor_data',
					'compare'  => 'EXISTS',
				);
				$post_query           = new WP_Query( $args );
				if ( $post_query->have_posts() ) {
					foreach ( $post_query->posts as $post ) {
						$data = get_post_meta( $post->ID, '_elementor_data', true );
						$data = preg_replace_callback( '|(id=\")(\d+)(\")|', array( $this, 'replace_post_ids' ), $data );
						$data = preg_replace_callback( '|(\"id\":\")(\d+)(\")|', array( $this, 'replace_post_ids' ), $data );
						$data = preg_replace_callback( '|(\"menu_id\":\")(\d+)(\")|', array( $this, 'replace_term_ids' ), $data );
						$data = preg_replace_callback( '|(\"category_ids\":\")([^\"]*)(\")|', array( $this, 'replace_term_ids' ), $data );
						$data = preg_replace_callback( '|(\"category_ids\":\[)([^\]]*)(\])|', array( $this, 'replace_term_ids' ), $data );
						$data = preg_replace_callback( '|(\"product_ids\":\")([^\"]*)(\")|', array( $this, 'replace_post_ids' ), $data );
						$data = preg_replace_callback( '|(\"product_ids\":\[)([^\]]*)(\])|', array( $this, 'replace_post_ids' ), $data );
						$data = preg_replace_callback( '|(\"categories\":\[)([^\]]*)(\])|', array( $this, 'replace_term_ids' ), $data );
						$data = preg_replace_callback( '|(\"product_category_ids\":\[)([^\]]*)(\])|', array( $this, 'replace_term_ids' ), $data );
						$data = preg_replace_callback( '|(\"nav_menu\":\")(\d+)(\")|', array( $this, 'replace_term_ids' ), $data );
						$data = apply_filters( 'alpha_after_elementor_content', $data );
						update_post_meta( $post->ID, '_elementor_data', wp_slash( $data ) );
					}
				}
			}

			/* Menu Item*/
			$menu_query = new WP_Query(
				array(
					'posts_per_page' => -1,
					'post_type'      => array( 'nav_menu_item' ),
					'post_status'    => 'publish',
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'meta_key' => '_menu_item_block',
							'compare'  => 'EXISTS',
						),
					),
				)
			);
			if ( $menu_query->have_posts() ) {
				foreach ( $menu_query->posts as $menu_item ) {
					$menu_item_block = get_post_meta( $menu_item->ID, '_menu_item_block', true );
					if ( isset( $alpha_import_posts_map[ $menu_item_block ] ) ) {
						update_post_meta( $menu_item->ID, '_menu_item_block', $alpha_import_posts_map[ $menu_item_block ] );
					}
				}
			}

			// compile dynamic css
			$this->clean_after_import();

			// @start feature: fs_pb_elementor
			// Set elementor options
			if ( alpha_get_feature( 'fs_pb_elementor' ) && defined( 'ELEMENTOR_VERSION' ) ) {
				$elementor_cpt_support = get_option( 'elementor_cpt_support' );
				if ( empty( $elementor_cpt_support ) ) {
					$elementor_cpt_support = array( 'post', 'page' );
				}
				$elementor_cpt_support[] = ALPHA_NAME . '_template';
				update_option( 'elementor_cpt_support', $elementor_cpt_support );
				if ( version_compare( ELEMENTOR_VERSION, '3.0' ) < 0 ) {
					update_option( 'elementor_disable_color_schemes', 'yes' );
					update_option( 'elementor_disable_typography_schemes', 'yes' );
				} else {
					update_option( 'elementor_disable_color_schemes', true );
					update_option( 'elementor_disable_typography_schemes', true );
				}
				// after setup, set elementor options
				set_transient( 'alpha_clean_after_setup_e', 3 );
			}
			// @end feature: fs_pb_elementor

			// update conditions
			$all_conditions = alpha_get_option( 'conditions' );
			if ( ! empty( $all_conditions ) ) {
				global $alpha_import_terms_map;

				$option_keys = apply_filters(
					'alpha_option_keys',
					array(
						'popup',
						'top_bar',
						'header',
						'footer',
						'ptb',
						'top_block',
						'bottom_block',
						'inner_top_block',
						'inner_bottom_block',
					)
				);

				if ( $all_conditions && is_array( $all_conditions ) ) {
					foreach ( $all_conditions as $category => $conditions ) {
						if ( is_array( $conditions ) ) {
							foreach ( $conditions as $condition_no => $condition ) {
								if ( ! empty( $condition['options'] ) ) {
									$options = $condition['options'];
									if ( is_array( $options ) ) {
										foreach ( $option_keys as $key ) {
											if ( ! empty( $options[ $key ] ) && 'hide' != $options[ $key ] && (int) $options[ $key ] ) {
												$old_post_id = (int) $options[ $key ];
												if ( ! empty( $alpha_import_posts_map[ $old_post_id ] ) ) {
													$all_conditions[ $category ][ $condition_no ]['options'][ $key ] = $alpha_import_posts_map[ $old_post_id ];
												}
											}
										}
									}
								}

								if ( ! empty( $condition['scheme'] ) ) {
									$scheme = $condition['scheme'];
									if ( is_array( $scheme ) && empty( $scheme['all'] ) ) {
										foreach ( $scheme as $scheme_key => $scheme_data ) {
											if ( post_type_exists( $scheme_key ) && is_array( $scheme_data ) ) {
												// Posts array
												foreach ( $scheme_data as $i => $post_id ) {
													if ( $post_id && ! empty( $alpha_import_posts_map[ (int) $post_id ] ) ) {
														$all_conditions[ $category ][ $condition_no ]['scheme'][ $scheme_key ][ $i ] = $alpha_import_posts_map[ (int) $post_id ];
													}
												}
											} elseif ( 'category' == $scheme_key || 'post_tag' == $scheme_key || taxonomy_exists( $scheme_key ) ) {
												// Terms array
												foreach ( $scheme_data as $i => $term_id ) {
													if ( $term_id && isset( $alpha_import_terms_map[ (int) $term_id ] ) ) {
														$all_conditions[ $category ][ $condition_no ]['scheme'][ $scheme_key ][ $i ] = $alpha_import_terms_map[ (int) $term_id ];
													}
												}
											}
										}
									}
								}
							}
						}
					}

					$GLOBALS['alpha_option']['conditions'] = $all_conditions;
					set_theme_mod( 'conditions', $all_conditions );
				}
			}

			// @start feature: fs_plugin_woocommerce
			// Clear all woocommerce caches
			if ( class_exists( 'WooCommerce' ) ) {
				wc_update_product_lookup_tables();

				// update attribute lookup table
				if ( WC()->version && version_compare( WC()->version, '6.3', '>=' ) ) {
					$data_store = wc_get_container()->get( Automattic\WooCommerce\Internal\ProductAttributesLookup\LookupDataStore::class );
					if ( ! $data_store->regeneration_is_in_progress() ) {
						$data_regenerator = wc_get_container()->get( Automattic\WooCommerce\Internal\ProductAttributesLookup\DataRegenerator::class );
						$data_regenerator->initiate_regeneration();
					}
				}

				wc_delete_product_transients();
				wc_delete_shop_order_transients();
				delete_transient( 'wc_count_comments' );
				delete_transient( 'as_comment_count' );

				$attribute_taxonomies = wc_get_attribute_taxonomies();

				if ( $attribute_taxonomies ) {
					foreach ( $attribute_taxonomies as $attribute ) {
						delete_transient( 'wc_layered_nav_counts_pa_' . $attribute->attribute_name );
					}
				}

				WC_Cache_Helper::get_transient_version( 'shipping', true );

				wc_delete_expired_transients();

				wc_clear_template_cache();
			}
			// @end feature: fs_plugin_woocommerce

			if ( class_exists( 'YITH_WCWL' ) ) {
				$wishlist = $this->importer_get_page_by_title( 'Wishlist' );
				if ( $wishlist && $wishlist->ID ) {
					update_option( 'yith-wcwl-page-id', $wishlist->ID );
				}
				update_option( 'yith_wcwl_variation_show', 'no' );
				update_option( 'yith_wcwl_price_show', 'yes' );
				update_option( 'yith_wcwl_stock_show', 'yes' );
				update_option( 'yith_wcwl_show_dateadded', 'no' );
				update_option( 'yith_wcwl_add_to_cart_show', 'yes' );
				update_option( 'yith_wcwl_show_remove', 'yes' );
				update_option( 'yith_wcwl_repeat_remove_button', 'no' );
			}

			/**
			 * Fires after import demo.
			 *
			 * @since 1.0
			 */
			do_action( 'alpha_demo_imported', $demo, true );
			flush_rewrite_rules();
		}

		/**
		 * Before replacement.
		 *
		 * @since 1.0
		 */
		private function before_replacement() {
			global $wpdb, $alpha_import_terms_map, $alpha_import_posts_map;

			$alpha_import_posts_map = array();

			$posts_result = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_alpha_demo'" );
			foreach ( $posts_result as $result ) {
				$data = explode( '#', $result->meta_value );
				if ( 2 == count( $data ) ) {
					if ( $this->demo == $data[0] ) {
						$alpha_import_posts_map[ (int) $data[1] ] = (int) $result->post_id;
					}
				}
			}

			$alpha_import_terms_map = array();

			$terms_result = $wpdb->get_results( "SELECT term_id, meta_value FROM {$wpdb->termmeta} WHERE meta_key = '_alpha_demo'" );
			foreach ( $terms_result as $result ) {
				$data = explode( '#', $result->meta_value );
				if ( 2 == count( $data ) ) {
					if ( $this->demo == $data[0] ) {
						$alpha_import_terms_map[ (int) $data[1] ] = (int) $result->term_id;
					}
				}
			}
			/**
			 * Fires before replacement.
			 *
			 * @since 1.0
			 */
			do_action( 'alpha_before_replacement' );
		}

		/**
		 * Replace post ids.
		 *
		 * @since 1.0
		 */
		public function replace_post_ids( $matches ) {
			global $alpha_import_posts_map;
			$ids     = array_map( 'intval', explode( ',', str_replace( '"', '', $matches[2] ) ) );
			$new_ids = array();
			foreach ( $ids as $id ) {
				if ( ! empty( $alpha_import_posts_map[ $id ] ) ) {
					$new_ids[] = $alpha_import_posts_map[ $id ];
				} elseif ( $id ) {
					$new_ids[] = $id;
				}
			}
			return $matches[1] . implode( ',', $new_ids ) . $matches[3];
		}

		/**
		 * Replace term ids.
		 *
		 * @since 1.0
		 */
		public function replace_term_ids( $matches ) {
			global $alpha_import_terms_map;
			$ids     = array_map( 'intval', explode( ',', str_replace( '"', '', $matches[2] ) ) );
			$new_ids = array();
			foreach ( $ids as $id ) {
				if ( ! empty( $alpha_import_terms_map[ $id ] ) ) {
					$new_ids[] = $alpha_import_terms_map[ $id ];
				} elseif ( $id ) {
					$new_ids[] = $id;
				}
			}
			return $matches[1] . implode( ',', $new_ids ) . $matches[3];
		}

		/**
		 * Clean after import.
		 *
		 * @since 1.0
		 */
		public function clean_after_import() {

			// Compile Theme Style
			require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/customizer/class-alpha-customizer.php' );
			require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/customizer/dynamic/dynamic-color-lib.php' );
			require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/customizer/customizer-function.php' );

			Alpha_Customizer::get_instance()->save_theme_options();

			/*
			 * Prevent Optimize Option.
			 * Lazyload Menu.
			 * Merge Style Sheet.
			 * The Critical CSS.
			 */
			set_theme_mod( 'lazyload_menu', false );
			set_theme_mod( 'resource_merge_stylesheets', false );
			set_theme_mod( 'resource_critical_css', false );

			delete_transient( 'alpha_clean_after_setup' );
			/**
			 * Fires after clean import.
			 *
			 * @since 1.0
			 */
			do_action( 'alpha_clean_after_import', $this );
		}

		/**
		 * Get demo types.
		 *
		 * @return array The demos array.
		 * @since 1.0
		 */
		public function demo_types() {
			return apply_filters(
				'alpha_demo_types',
				array(
					'main'                => array(
						'alt'     => 'Main',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/main.jpg',
						'filter'  => 'business',
						'plugins' => array( 'wpforms-lite' ),
						'editors' => array(),
						'free'    => true,
					),
					'business-consulting' => array(
						'alt'     => 'Business Consulting 1',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/business-consulting.jpg',
						'filter'  => 'business',
						'plugins' => array( 'wpforms-lite' ),
						'editors' => array(),
						'free'    => true,
					),
					'business-consulting-2' => array(
						'alt'     => 'Business Consulting 2',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/business-consulting-2.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'business-consulting-3' => array(
						'alt'     => 'Business Consulting 3',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/business-consulting-3.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'business-consulting-4' => array(
						'alt'     => 'Business Consulting 4',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/business-consulting-4.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'corporate' => array(
						'alt'     => 'Corporate 1',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/corporate.jpg',
						'filter'  => 'business',
						'plugins' => array( 'wpforms-lite' ),
						'editors' => array(),
						'free'    => true,
					),
					'corporate-2' => array(
						'alt'     => 'Corporate 2',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/corporate-2.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'fashion'             => array(
						'alt'     => 'Fashion 1',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/fashion.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => true,
					),
					'fashion-2'             => array(
						'alt'     => 'Fashion 2',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/fashion-2.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => false,
					),
					'food'                => array(
						'alt'     => 'Food Store',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/food.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => true,
					),
					'digital-agency' => array(
						'alt'     => 'Digital Agency',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/digital-agency.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'startup-agency' => array(
						'alt'     => 'Startup Agency',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/startup-agency.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'it-services' => array(
						'alt'     => 'IT Services',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/it-services.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'cleaning' => array(
						'alt'     => 'Cleaning Services',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/cleaning-services.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'medical' => array(
						'alt'     => 'Medical Services',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/medical.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'auto-services' => array(
						'alt'     => 'Auto Services',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/auto-services.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'woocommerce', 'yith-woocommerce-wishlist', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'law-firm'                 => array(
						'alt'     => 'Law Firm',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/law-firm.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'furniture'                 => array(
						'alt'     => 'Furniture Store',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/furniture.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => true,
					),
					'jeans'                 => array(
						'alt'     => 'Jeans Store',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/jeans.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => false,
					),
					'sports'                 => array(
						'alt'     => 'Sports Shop',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/sports.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => false,
					),
					'environmental-ngo'   => array(
						'alt'     => 'Environmental NGO',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/ngo.jpg',
						'filter'  => 'business',
						'plugins' => array( 'wpforms-lite' ),
						'editors' => array(),
						'free'    => true,
					),
					'construction'                 => array(
						'alt'     => 'Construction Company',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/construction.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'transport'                 => array(
						'alt'     => 'Transport Company',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/transport.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'restaurant'                 => array(
						'alt'     => 'Restaurant',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/restaurant.jpg',
						'filter'  => 'business',
						'plugins' => array( 'wpforms-lite', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'app'                 => array(
						'alt'     => 'App Landing',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/app.jpg',
						'filter'  => 'business',
						'plugins' => array( 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'resume'                 => array(
						'alt'     => 'Resume',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/resume.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'seo'                 => array(
						'alt'     => 'SEO',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/seo.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'hosting'                 => array(
						'alt'     => 'Hosting Services',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/hosting.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'cryptocurrency'                 => array(
						'alt'     => 'Cryptocurrency',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/cryptocurrency.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'gym'            => array(
						'alt'     => 'Gym',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/gym.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'yoga'                 => array(
						'alt'     => 'Yoga Class',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/yoga.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'health-coach'                 => array(
						'alt'     => 'Health Coach',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/health-coach.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => false,
					),
					'insurance'                 => array(
						'alt'     => 'Insurance Company',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/insurance.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'finance'                 => array(
						'alt'     => 'Financial Services',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/finance.jpg',
						'filter'  => 'business',
						'plugins' => array( 'wpforms-lite' ),
						'editors' => array(),
						'free'    => true,
					),
					'loan'                 => array(
						'alt'     => 'Loan Group',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/loan.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'accountant'                 => array(
						'alt'     => 'Accountant',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/accountant.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'psychology'                 => array(
						'alt'     => 'Psychology',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/psychology.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'nutritionist'                 => array(
						'alt'     => 'Nutritionist',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/nutritionist.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'photography'                 => array(
						'alt'     => 'Photographer 1',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/photography.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'photography-2'                 => array(
						'alt'     => 'Photographer 2',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/photography-2.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'podcast'                 => array(
						'alt'     => 'Podcasts',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/podcast.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'gardener'                 => array(
						'alt'     => 'Gardening Services',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/gardener.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'plumber'                 => array(
						'alt'     => 'Plumbing Services',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/plumber.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'real-estate'                 => array(
						'alt'     => 'Real Estate',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/real-estate.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'hotel'                 => array(
						'alt'     => 'Hotel',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/hotel.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'interior-design'                 => array(
						'alt'     => 'Interior Design',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/interior-design.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'taxi'                 => array(
						'alt'     => 'Taxi',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/taxi.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'contact-form-7' ),
						'editors' => array(),
						'free'    => false,
					),
					'travel'                 => array(
						'alt'     => 'Travel Agency',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/travel.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'tea'                 => array(
						'alt'     => 'Tea Shop',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/tea.jpg',
						'filter'  => 'business',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => true,
					),
					'cafe'                 => array(
						'alt'     => 'Cafe',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/cafe.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'barber'                 => array(
						'alt'     => 'Barber',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/barber.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => false,
					),
					'makeup'                 => array(
						'alt'     => 'Makeup',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/makeup.jpg',
						'filter'  => 'business',
						'plugins' => array( 'advanced-custom-fields', 'post-types-unlimited', 'wpforms-lite' ),
						'editors' => array(),
						'free'    => false,
					),
					'kid'                 => array(
						'alt'     => 'Kids Store',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/kid.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => true,
					),
					'watch'                 => array(
						'alt'     => 'Watch Store',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/watch.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => false,
					),
					'shoes'                 => array(
						'alt'     => 'Shoes Store',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/shoes.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => false,
					),
					'toy'                 => array(
						'alt'     => 'Toys Store',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/toy.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => false,
					),
					'gifts'                 => array(
						'alt'     => 'Gifts Shop',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/gifts.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => false,
					),
					'diamond'                 => array(
						'alt'     => 'Diamond Store',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/diamond.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => false,
					),
					'sunglass'                 => array(
						'alt'     => 'Sunglass Shop',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/sunglass.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => false,
					),
					'pet'                 => array(
						'alt'     => 'Pet Store',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/pet.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => false,
					),
					'beauty'                 => array(
						'alt'     => 'Beauty Salon',
						'img'     => ALPHA_ASSETS . '/images/admin/setup-wizard/beauty.jpg',
						'filter'  => 'shop',
						'plugins' => array( 'wpforms-lite', 'woocommerce', 'yith-woocommerce-wishlist' ),
						'editors' => array(),
						'free'    => false,
					),
				)
			);
		}
	}
endif;

add_action( 'after_setup_theme', 'alpha_theme_setup_wizard', 10 );

if ( ! function_exists( 'alpha_theme_setup_wizard' ) ) :
	function alpha_theme_setup_wizard() {
		$instance = Alpha_Setup_Wizard::get_instance();
	}
endif;

if ( ! function_exists( 'alpha_import_theme_options' ) ) {
	function alpha_import_theme_options( $plugin_options, $imported_options ) {
		update_option( 'theme_mods_' . get_option( 'stylesheet' ), $imported_options );

		// Reset alpha_option
		unset( $GLOBALS['alpha_option'] );

		/**
		 * Reset alpha_option with default options after importing theme options
		 *
		 * @since 1.0
		 */
		global $alpha_option;
		require alpha_framework_path( ALPHA_FRAMEWORK_PATH . '/theme-options.php' );

		$theme_mods = get_theme_mods();
		if ( is_array( $theme_mods ) ) {
			$alpha_option = array_merge( $alpha_option, $theme_mods );
		}
		if ( empty( $alpha_option['conditions'] ) ) {
			$alpha_option['conditions'] = $default_conditions;
		}
	}
}