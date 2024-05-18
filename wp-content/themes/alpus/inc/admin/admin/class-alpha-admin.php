<?php
/**
 * Admin Page
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;
if ( ! class_exists( 'Alpha_Admin' ) ) {
	class Alpha_Admin extends Alpha_Base {

		/**
		 * Check whether theme is activated or not.
		 *
		 * @var   bool
		 * @since 1.0
		 */
		private $checked_purchase_code;

		/**
		 * Activation url for checking license key.
		 *
		 * @var   string
		 * @since 1.0
		 */
		private $activation_url = ALPHA_SERVER_URI . 'dummy/api/pro';

		/**
		 * The admin configuration.
		 *
		 * @since 1.0
		 */
		public $admin_config;

		/**
		 * Constructor
		 *
		 * Add actions and filters for admin page.
		 *
		 * @since 1.0
		 */
		public function __construct() {

			add_action( 'admin_menu', array( $this, 'custom_admin_menu_order' ) );
			add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ) );
			add_action( 'after_switch_theme', array( $this, 'reset_child_theme_options' ), 15 );

			if ( is_child_theme() && empty( alpha_get_option( 'container' ) ) ) {
				$parent_theme_options = get_option( 'theme_mods_' . get_template() );
				update_option( 'theme_mods_' . get_stylesheet(), $parent_theme_options );
			}

			add_filter(
				'alpha_layout_vars',
				function( $vars ) {
					$vars['pro_title']    = esc_html__( 'Upgrade', 'alpha' );
					$vars['pro_desc']     = ALPHA_ENVATO_CODE ? esc_html__( 'The number of layouts has exceeded 7. Activate theme and get pro to continue.', 'alpha' ) : esc_html__( 'The number of layouts has exceeded 7. Purchase of Pro and theme activation are required to continue.', 'alpha' );
					$vars['purchase_pro'] = ALPHA_GET_PRO_URI;
					return $vars;
				}
			);

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 30 );

			add_action( 'admin_init', array( $this, 'check_activation' ) );
			// add_action( 'admin_init', array( $this, 'show_activation_notice' ) );
			add_action( 'admin_init', array( $this, 'add_admin_class' ) );
			add_filter( 'wp_ajax_alpha_activation', array( $this, 'ajax_activation' ) );
			if ( is_admin() ) {
				add_filter( 'pre_set_site_transient_update_themes', array( $this, 'pre_set_site_transient_update_themes' ) );
				add_filter( 'upgrader_pre_download', array( $this, 'upgrader_pre_download' ), 10, 3 );
			}
			
			defined( 'ALPHA_GET_PRO_URI' ) || define( 'ALPHA_GET_PRO_URI', ( ALPHA_ENVATO_CODE ? admin_url( ( ! $this->is_registered() ? ALPHA_ADMIN_PAGE . 'alpha-setup-wizard&step=activation' : ALPHA_ADMIN_PAGE . 'alpha-setup-wizard&step=default_plugins' ) ) :  ALPHA_SERVER_URI . 'buynow/alpus' ) );                       // Pro uri

			$this->admin_config = array(
				'social_links'   => array(
					'facebook'  => array(
						'label' => esc_html__( 'Facebook', 'alpha' ),
						'link'  => 'Facebook.com',
						'url'   => 'https://www.facebook.com/',
						'icon'  => 'fab fa-facebook-square',
						'color' => '#3b5999',
					),
					'twitter'   => array(
						'label' => esc_html__( 'Twitter', 'alpha' ),
						'link'  => 'Twitter.com',
						'url'   => 'https://www.twitter.com/',
						'icon'  => 'fab fa-twitter',
						'color' => '#00acee',
					),
					'instagram' => array(
						'label' => esc_html__( 'Instagram', 'alpha' ),
						'link'  => 'Instagram.com',
						'url'   => 'https://www.instagram.com/',
						'icon'  => 'fab fa-instagram',
						'color' => '#000000',
					),
					'wordpress' => array(
						'label' => esc_html__( 'WordPress', 'alpha' ),
						'link'  => 'WordPress.org',
						'url'   => 'https://wordpress.org/',
						'icon'  => 'fab fa-wordpress',
						'color' => '#0073aa',
					),
					'envato'    => array(
						'label' => esc_html__( 'Envato', 'alpha' ),
						'link'  => 'Themeforest.net',
						'url'   => 'https://themeforest.net/',
						'icon'  => 'icon-envato',
						'color' => '#81B441',
					),
				),
				'other_products' => array(
					'u-design' => array(
						'name'  => 'UDesign',
						'url'   => 'https://d-themes.com/wordpress/udesign/',
						'image' => ALPHA_ASSETS . '/images/admin/dashboard/udesign.jpg',
					),
					'wolmart'  => array(
						'name'  => 'Wolmart',
						'url'   => 'https://d-themes.com/wordpress/wolmart/',
						'image' => ALPHA_ASSETS . '/images/admin/dashboard/wolmart.jpg',
					),
					'riode'    => array(
						'name'  => 'Riode',
						'url'   => 'https://d-themes.com/wordpress/riode/',
						'image' => ALPHA_ASSETS . '/images/admin/dashboard/riode.jpg',
					),
					'molla'    => array(
						'name'  => 'Molla',
						'url'   => 'https://d-themes.com/wordpress/molla/',
						'image' => ALPHA_ASSETS . '/images/admin/dashboard/molla.jpg',
					),
					'more'     => array(
						'name'  => 'Comming Soon...',
						'url'   => '#',
						'image' => ALPHA_ASSETS . '/images/admin/dashboard/coming.jpg',
					),
				),
				'links'          => array(
					'setup-wizard' => array(
						'label' => esc_html__( 'Setup Wizard', 'alpha' ),
						'url'   => ALPHA_ADMIN_PAGE . 'alpha-setup-wizard',
						'icon'  => '<i class="admin-svg-setup-wizard" style="width:124px;"></i>',
						'desc'  => esc_html__( 'The easiest and fastest way to install and configure your website, just follow the steps and you\'re all set.', 'alpha' ),
					),
					'optimize-wizard' => array(
						'label' => esc_html__( 'Optimize Wizard', 'alpha' ),
						'url'   => ALPHA_ADMIN_PAGE . 'alpha-optimize-wizard',
						'icon'  => '<i class="admin-svg-optimize-wizard" style="width:191px;"></i>',
						'desc'  => esc_html__( 'Step-by-step wizard that removes render blocking resources, improves website speed and Pagespeed Insights scores.', 'alpha' ),
					),
					'documentation' => array(
						'label' => esc_html__( 'Documentation', 'alpha' ),
						'url'   => ALPHA_SERVER_URI . 'alpus/documentation/',
						'icon'  => '<i class="admin-svg-documentation" style="width:100px;"></i>',
						'desc'  => sprintf( esc_html__( 'Contains all descriptions related to %1$s usage and features. Before you use %1$s, read documentation first.', 'alpha' ), ALPHA_DISPLAY_NAME ),
					),
					'howto_videos' => array(
						'label' => esc_html__( 'How-to Videos', 'alpha' ),
						'url'   => ALPHA_SERVER_URI . 'alpus/documentation/#video-tutorials',
						'icon'  => '<i class="admin-svg-videos" style="width:102px;"></i>',
						'desc'  => esc_html__( 'We provide many How-to Videos for visual instructions. Check our video tutorials and find your answer.', 'alpha' ),
					),
					'support'       => array(
						'label' => esc_html__( 'Support', 'alpha' ),
						'url'   => ALPHA_SERVER_URI . 'forums/',
						'icon'  => '<i class="admin-svg-support" style="width:115px;"></i>',
						'desc'  => sprintf( esc_html__( 'Join to our community of %s fans creating eye-catching websites! Share your site, ask a question. We provide 24/7 supports.', 'alpha' ), ALPHA_DISPLAY_NAME ), 
					),
					'request'       => array(
						'label' => esc_html__( 'Request a Feature', 'alpha' ),
						'url'   => ALPHA_SERVER_URI . 'contact-us/',
						'icon'  => '<i class="admin-svg-request" style="width:100px"></i>',
						'desc'  => sprintf( esc_html__( 'Let us make %s more awesome and powerful. If you want any extra feature, we\'ll be happy with receiving your request.', 'alpha' ), ALPHA_DISPLAY_NAME ),
					),
					'products'  => array(
						'label' => esc_html__( 'Our Products', 'alpha' ),
						'url'   => ALPHA_SERVER_URI . 'shop',
						'icon'  => '<i class="admin-svg-plugins" style="width:173px;"></i>',
						'desc'  => esc_html__( 'Our products add more advanced features and functionalities. Powerful, effective and crafted for you.', 'alpha' )
					),
					'free_vs_pro'  => array(
						'label' => esc_html__( 'Free vs Pro', 'alpha' ),
						'url'   => ALPHA_SERVER_URI . '#free-vs-pro',
						'icon'  => '<i class="admin-svg-vs" style="width:275px;"></i>',
						'desc'  => sprintf( esc_html__( 'The %s Pro adds more powerful features, more enhanced performance and more flexibility.', 'alpha' ), ALPHA_DISPLAY_NAME ),
					),
					'buynow'        => array(
						'label' => esc_html__( 'Buy now!', 'alpha' ),
						'url'   => 'https://d-themes.com/buynow/' . get_template() . 'wp',
						'icon'  => 'fas fa-shopping-cart',
					),
				),
			);
			if ( ALPHA_ENVATO_CODE ) {
				$this->admin_config['links']['free_vs_pro'] = array(
					'label' => esc_html__( 'Reviews', 'alpha' ),
					'url'   => defined( 'ALPHA_ENVATO_CODE' ) && ALPHA_ENVATO_CODE ? 'http://themeforest.net/downloads' : 'https://wordpress.org/support/theme/alpus/reviews/#new-post',
					'icon'  => '<i class="admin-svg-reviews" style="width:122px;"></i>',
					'desc'  => sprintf( esc_html__( 'How did our customers rate our theme? Check their reviews and you will be more sure of choosing %s.', 'alpha' ), ALPHA_DISPLAY_NAME ),
				);
			}
			/**
			 * Filters the admin config.
			 *
			 * @since 1.0
			 */
			$this->admin_config = apply_filters( 'alpha_admin_config', $this->admin_config, $this );

		}

		/**
		 * Add alpha-admin-page class to body tag.
		 *
		 * @since 1.0
		 */
		public function add_admin_class() {
			if ( ( isset( $_REQUEST['page'] ) && 'alpha' == substr( $_REQUEST['page'], 0, 5 ) ) || ( isset( $_REQUEST['post_type'] ) && ALPHA_NAME . '_template' == $_REQUEST['post_type'] ) ) {
				add_filter(
					'admin_body_class',
					function() {
						return 'alpha-admin-page';
					}
				);
			}
		}

		/**
		 * Check Theme Activation Status
		 *
		 * @since 1.0
		 */
		public function ajax_activation() {
			check_ajax_referer( 'alpha-admin', 'nonce' );
			$this->check_activation();
			// $this->show_activation_notice();
			require_once alpha_framework_path( ALPHA_SETUP_WIZARD . '/views/activation.php' );
			die();
		}

		/**
		 * Change admin menu order.
		 *
		 * @since 1.0
		 */
		public function custom_admin_menu_order() {
			global $menu;

			$admin_menus = array();

			// Change dasbhoard menu order.
			$posts = array();
			$idx   = 0;
			foreach ( $menu as $key => $menu_item ) {
				if ( 'Posts' == $menu_item[0] ) {
					$admin_menus[9] = $menu_item;
				} elseif ( 'separator1' == $menu_item[2] ) {
					$admin_menus[8] = $menu_item;
				} else {
					$admin_menus[ $key ] = $menu_item;
				}
			}

			$menu = $admin_menus;
		}

		/**
		 * Check purchase code for license.
		 *
		 * @return string Return checking value of purchase code. e.g: verified, unregister and invalid.
		 * @since 1.0
		 */
		public function check_purchase_code() {
			if ( ! $this->checked_purchase_code ) {
				$code         = isset( $_POST['code'] ) ? sanitize_text_field( $_POST['code'] ) : '';
				$code_confirm = $this->get_purchase_code();
				if ( isset( $_POST['form_action'] ) && ! empty( $_POST['form_action'] ) ) {
					preg_match( '/[a-z0-9\-]{1,63}\.[a-z\.]{2,6}$/', parse_url( home_url(), PHP_URL_HOST ), $_domain_tld );
					if ( isset( $_domain_tld[0] ) ) {
						$domain = $_domain_tld[0];
					} else {
						$domain = parse_url( home_url(), PHP_URL_HOST );
					}

					if ( 'unregister' == $_POST['form_action'] && $code != $code_confirm ) {
						if ( $code_confirm ) {
							$result = $this->curl_purchase_code( $code_confirm, '', 'remove' );
						}
						if ( $result && isset( $result['result'] ) && 3 == (int) $result['result'] ) {
							$this->checked_purchase_code = 'unregister';
							$this->set_purchase_code( '' );
							delete_transient( 'alpha_purchase_code_error_msg' );
							if ( isset( $_COOKIE['alpha_dismiss_code_error_msg'] ) ) {
								setcookie( 'alpha_dismiss_code_error_msg', '', time() - 3600 );
							}
							return $this->checked_purchase_code;
						}
					}
					if ( $code ) {
						$result = $this->curl_purchase_code( $code, $domain, 'add' );
						if ( ! $result ) {
							$this->checked_purchase_code = 'invalid';
							$code_confirm                = '';
						} elseif ( isset( $result['result'] ) && 1 == (int) $result['result'] ) {
							$code_confirm                = $code;
							$this->checked_purchase_code = 'verified';
						} else {
							$this->checked_purchase_code = $this->get_api_message( $result['message'] );
							$code_confirm                = '';
						}
					} else {
						$code_confirm                = '';
						$this->checked_purchase_code = '';
					}
					$this->set_purchase_code( $code_confirm );
				} else {
					if ( $code && $code_confirm && $code == $code_confirm ) {
						$this->checked_purchase_code = 'verified';
					}
				}
			}
			return $this->checked_purchase_code;
		}

		/**
		 * Get api message to activate license.
		 *
		 * @param  string $msg_code  Messaeg code
		 * @return string Return msg to response for activating license.
		 * @since 1.0
		 */
		public function get_api_message( $msg_code ) {
			if ( 'blocked_spam' == $msg_code ) {
				return esc_html__( 'Your ip address is blocked as spam!!!', 'alpha' );
			} elseif ( 'code_invalid' == $msg_code ) {
				return esc_html__( 'Purchase Code is not valid!!!', 'alpha' );
			} elseif ( 'already_used' == $msg_code && ! empty( $data['domain'] ) ) {
				return sprintf( esc_html__( 'This code was already used in %s.', 'alpha' ), $data['domain'] );
			} elseif ( 'reactivate' == $msg_code ) {
				return esc_html__( 'Please re-activate the theme.', 'alpha' );
			} elseif ( 'unregistered' == $msg_code ) {
				return ALPHA_DISPLAY_NAME . esc_html__( ' Theme is not activated!', 'alpha' );
			} elseif ( 'activated' == $msg_code ) {
				return ALPHA_DISPLAY_NAME . esc_html__( ' Theme is activated!', 'alpha' );
			} elseif ( 'p_blocked' == $msg_code ) { //permanetly blocked
				return sprintf( esc_html__( 'You are using illegal version now. Please purchase legal version %1$shere%2$s.', 'alpha' ), '<a href="' . esc_url( $this->admin_config['links']['buynow']['url'] ) . '" target="_blank">', '</a>' );
			} elseif ( 's_blocked' == $msg_code ) { // soft blocked
				return sprintf( esc_html__( 'Your purchase code is temporarily blocked. Please contact us %1$shere%2$s to unblock it.', 'alpha' ), '<a href="' . esc_url( $this->admin_config['links']['support']['url'] ) . '" target="_blank">', '</a>' );
			} else {
				return $msg_code;
			}
			return '';
		}

		/**
		 * Get curl purchase code.
		 *
		 * @param  string $code       License code
		 * @param  string $domain     Theme user domain for license.
		 * @param  string $act        Actions for purchase code. e.g: 'add' or 'remove'
		 * @return string Result code
		 *
		 * @since 1.0
		 */
		public function curl_purchase_code( $code, $domain, $act ) {
			require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/importer/importer-api.php' );
			$importer_api = new Alpha_Importer_API();
			$template     = get_template();
			$site_url     = urlencode( home_url() );
			$result       = $importer_api->get_response( $this->activation_url . '?item=' . ALPHA_ENVATO_CODE . "&code=$code&siteurl=$site_url&domain=$domain&template=$template&act=$act" . ( $importer_api->is_localhost() ? '&local=true' : '' ) );
			if ( ! $result ) {
				return false;
			}
			if ( is_wp_error( $result ) ) {
				return array( 'message' => $result->get_error_message() );
			}
			return $result;
		}

		/**
		 * Get purchase code.
		 *
		 * @return string Return purchase code if registed.
		 * @since 1.0
		 */
		public function get_purchase_code() {
			if ( $this->is_envato_hosted() ) {
				return SUBSCRIPTION_CODE;
			}
			return get_option( 'envato_purchase_code_' . ALPHA_ENVATO_CODE );
		}

		/**
		 * Get whether theme is activated or not.
		 *
		 * @return bool True if registed, false not.
		 * @since 1.0
		 */
		public function is_registered() {
			if ( $this->is_envato_hosted() ) {
				return true;
			}
			return get_option( ALPHA_NAME . '_registered' );
		}

		/**
		 * Store purchase code to option table.
		 *
		 * @param string $code Verified purchase code.
		 * @since 1.0
		 */
		public function set_purchase_code( $code ) {
			update_option( 'envato_purchase_code_' . ALPHA_ENVATO_CODE, $code );
		}

		/**
		 * Is envato hosted ?
		 *
		 * @return bool True if defined, false not.
		 * @since 1.0
		 */
		public function is_envato_hosted() {
			return defined( 'ENVATO_HOSTED_KEY' ) ? true : false;
		}

		/**
		 * Get ish
		 *
		 * @return bool|string Host key code if defined, false not.
		 * @since 1.0
		 */
		public function get_ish() {
			if ( ! defined( 'ENVATO_HOSTED_KEY' ) ) {
				return false;
			}
			return substr( ENVATO_HOSTED_KEY, 0, 16 );
		}

		/**
		 * Get virtual code for displaying purchase code.
		 *
		 * @return string Return virtual code.
		 * @since 1.0
		 */
		public function get_purchase_code_asterisk() {
			$code = $this->get_purchase_code();
			if ( $code ) {
				$code = substr( $code, 0, 13 );
				$code = $code . '-****-****-************';
			}
			return $code;
		}

		/**
		 * Adjust transient before setting for update themes.
		 *
		 * @param array $transient Values for setting transient
		 * @return array Filtered transient.
		 */
		public function pre_set_site_transient_update_themes( $transient ) {
			if ( ALPHA_ENVATO_CODE && ! $this->is_registered() ) {
				return $transient;
			}
			if ( empty( $transient->checked ) ) {
				return $transient;
			}

			require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/importer/importer-api.php' );
			$importer_api   = new Alpha_Importer_API();
			$new_version    = $importer_api->get_latest_theme_version();
			$theme_template = get_template();
			if ( version_compare( wp_get_theme( $theme_template )->get( 'Version' ), $new_version, '<' ) ) {

				$args = $importer_api->generate_args( false );
				if ( $this->is_envato_hosted() ) {
					$args['ish'] = $this->get_ish();
				}

				$transient->response[ $theme_template ] = array(
					'theme'       => $theme_template,
					'new_version' => $new_version,
					'url'         => $importer_api->get_url( 'changelog' ),
					'package'     => add_query_arg( $args, $importer_api->get_url( 'theme' ) ),
				);

			}
			return $transient;
		}

		/**
		 * Filters whether to return the package.
		 *
		 * @param  bool        $reply   Whether to bail without returning the package. Default false.
		 * @param  string      $package The package file name.
		 * @param  WP_Upgrader $obj     The instance
		 * @return bool                 Returning package.
		 */
		public function upgrader_pre_download( $reply, $package, $obj ) {
			require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/importer/importer-api.php' );
			$importer_api = new Alpha_Importer_API();
			if ( strpos( $package, $importer_api->get_url( 'theme' ) ) !== false || strpos( $package, $importer_api->get_url( 'plugins' ) ) !== false ) {
				if ( strpos( $package, $importer_api->get_url( 'plugins' ) ) !== false ) {
					if ( ! empty( $obj->skin->options['extra']['slug'] ) ) {
						$plugin = $obj->skin->options['extra']['slug'];
					} elseif ( ! empty( $obj->skin->options['names'][0] ) && false !== strpos( $obj->skin->options['names'][0], 'Alpus' ) ) {
						$plugin_name       = $obj->skin->options['names'][0];
						$alpha_plugins_obj = new Alpha_TGM_Plugins();
						$plugins           = $alpha_plugins_obj->get_plugins_list();
						$plugin            = array_filter( $plugins, function ( $p ) use ( $plugin_name ) {
							return $plugin_name == $p['name'];
						} );
						if ( $plugin ) {
							$plugin = $plugin[ array_keys( $plugin )[0] ]['slug'];
						}
					}
					if ( ( defined( 'ALPUS_PLUGIN_FRAMEWORK_VERSION' ) && ! empty( $plugin ) && isset( Alpus_Plugin_Framework::get_instance()->alpus_plugins[ $plugin ] ) && Alpus_Plugin_Framework::get_instance()->alpus_plugins[ $plugin ]['free'] ) || ( ! ALPHA_ENVATO_CODE && 'alpus-core' == $plugin ) ) {
						return $reply;
					}
				}
				if ( ! $this->is_registered() ) {
					return new WP_Error( 'not_registerd', sprintf( esc_html__( 'Please %s theme to get access to pre-built demo websites and auto updates.', 'alpha' ), '<a href="' . ALPHA_ADMIN_PAGE . 'alpha">' . esc_html__( 'register', 'alpha' ) . '</a> ' . ALPHA_DISPLAY_NAME ) );
				}
				$code   = $this->get_purchase_code();
				$domain = $importer_api->generate_args()['domain'];
				$result = $this->curl_purchase_code( $code, $domain, 'add' );
				if ( ! isset( $result['result'] ) || 1 !== (int) $result['result'] ) {
					$message = isset( $result['message'] ) ? $result['message'] : esc_html__( 'Purchase Code is not valid or could not connect to the API server!', 'alpha' );
					return new WP_Error( 'purchase_code_invalid', alpha_strip_script_tags( $message ) );
				}
			}
			return $reply;
		}
		
		/**
		 * Enqueue styles and scripts for admin.
		 *
		 * @since 1.0
		 */
		public function enqueue_admin_scripts() {
			wp_enqueue_style( 'alpha-admin-extend', ALPHA_INC_URI . '/admin/admin/admin-extend' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), ALPHA_VERSION );
			wp_enqueue_script( 'alpha-admin-extend', ALPHA_INC_URI . '/admin/admin/admin-extend' . ALPHA_JS_SUFFIX, array( 'jquery-core' ), ALPHA_VERSION, true );
		}

		/**
		 * Clear transients after switching theme.
		 *
		 * @since 1.0
		 */
		public function after_switch_theme() {
			if ( $this->is_registered() ) {
				$this->refresh_transients();
			}
		}

		/**
		 * Reset child theme's options.
		 *
		 * @since 1.0
		 */
		public function reset_child_theme_options() {
			if ( is_child_theme() && empty( alpha_get_option( 'container' ) ) ) {
				update_option( 'theme_mods_' . get_stylesheet(), get_option( 'theme_mods_' . get_template() ) );
			}
		}

		/**
		 * Clear transients
		 *
		 * @since 1.0
		 */
		public function refresh_transients() {
			delete_transient( 'alpha_purchase_code_error_msg' );
			delete_site_transient( 'alpha_plugins' );
			delete_site_transient( 'update_themes' );
			unset( $_COOKIE['alpha_dismiss_activate_msg'] );
			setcookie( 'alpha_dismiss_activate_msg', null, -1, '/' );
			setcookie( 'alpha_dismiss_code_error_msg', '', time() - 3600 );
		}

		/**
		 * Check activation
		 *
		 * @since 1.0
		 */
		public function check_activation() {
			if ( isset( $_POST['alpha_registration'] ) && check_admin_referer( 'alpha-setup-wizard' ) ) {
				update_option( 'alpha_register_error_msg', '' );
				$result = $this->check_purchase_code();
				if ( 'verified' == $result ) {
					update_option( ALPHA_NAME . '_registered', true );
					$this->refresh_transients();
				} elseif ( 'unregister' == $result ) {
					update_option( ALPHA_NAME . '_registered', false );
					$this->refresh_transients();
				} elseif ( 'invalid' == $result ) {
					update_option( ALPHA_NAME . '_registered', false );
					update_option( 'alpha_register_error_msg', sprintf( esc_html__( 'There is a problem contacting to the %s API server. Please try again later.', 'alpha' ), ALPHA_DISPLAY_NAME ) );
				} else {
					update_option( ALPHA_NAME . '_registered', false );
					update_option( 'alpha_register_error_msg', $result );
				}
			}
		}
	}
}

Alpha_Admin::get_instance();
