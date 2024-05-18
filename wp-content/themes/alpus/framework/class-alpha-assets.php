<?php
/**
 * Alpha FrameWork Assets Class
 *
 * Enqueue framework assets including css, js and images.
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

class Alpha_Assets extends Alpha_Base {

	/**
	 * Constructor
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {

		// Manage Theme and Plugin Assets

		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 30 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_custom_css' ), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 5 );
		add_action( 'wp_footer', array( $this, 'enqueue_theme_js_css' ) );

		// Custom JS
		if ( ! is_admin() ) {
			add_action( 'wp_print_footer_scripts', array( $this, 'enqueue_custom_js' ), 20 );
		}
	}

	/**
	 * Register styles and scripts.
	 *
	 * @since 1.0
	 */
	public function register_scripts() {

		// Styles
		wp_register_style( 'alpha-style', ALPHA_URI . '/style.css', array(), ALPHA_VERSION );
		wp_register_style( 'alpha-icons', ALPHA_ASSETS . '/vendor/icons/css/icons.min.css', array(), ALPHA_VERSION );
		wp_register_style( 'alpha-flag', ALPHA_CSS . '/flags.min.css', array(), ALPHA_VERSION );
		wp_register_style( 'fontawesome-free', ALPHA_ASSETS . '/vendor/fontawesome-free/css/all.min.css', array(), '5.14.0' );
		wp_register_style( 'alpha-animate', ALPHA_ASSETS . '/vendor/animate/animate.min.css' );
		wp_register_style( 'alpha-magnific-popup', ALPHA_ASSETS . '/vendor/jquery.magnific-popup/magnific-popup' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), '1.0' );

		// Theme Styles
		$css_files  = array( 'theme', 'blog', 'single-post', 'shop', 'shop-other', 'single-product' );
		$uploads    = wp_upload_dir();
		$upload_dir = $uploads['basedir'];
		$upload_url = $uploads['baseurl'];
		if ( ! isset( $_REQUEST['debug'] ) ) {
			foreach ( $css_files as $file ) {
				$filename = 'theme' . ( 'theme' == $file ? '' : '-' . $file );
				if ( file_exists( wp_normalize_path( $upload_dir . '/' . ALPHA_NAME . '_styles/' . $filename . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ) ) ) {
					wp_register_style( 'alpha-' . $filename, $upload_url . '/' . ALPHA_NAME . '_styles/' . $filename . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), ALPHA_VERSION );
				} else {
					wp_register_style( 'alpha-' . $filename, ALPHA_CSS . '/' . ( 'theme' == $file ? '' : 'pages/' ) . $file . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), ALPHA_VERSION );
				}
			}
		}

		if ( file_exists( wp_normalize_path( $upload_dir . '/' . ALPHA_NAME . '_styles/dynamic_vars.min.css' ) ) ) {
			$dynamic_url = $upload_url . '/' . ALPHA_NAME . '_styles/dynamic_vars.min.css';
		} else {
			$dynamic_url = ALPHA_CSS . '/dynamic_vars.min.css';
		}

		// global css
		$custom_css_handle = 'alpha-theme';
		if ( ! is_customize_preview() ) {
			wp_register_style( 'alpha-dynamic-vars', $dynamic_url, array( $custom_css_handle ), ALPHA_VERSION );
		} else {
			global $wp_filesystem;
			// Initialize the WordPress filesystem, no more using file_put_contents function
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}
			$dynamic_url = str_replace( 'https:', 'http:', $dynamic_url );
			$data        = $wp_filesystem->get_contents( $dynamic_url );
				wp_add_inline_style( $custom_css_handle, $data );
			}

		// Scripts
		wp_register_script( 'alpha-sidebar', alpha_framework_uri( '/assets/js/sidebar' . ALPHA_JS_SUFFIX ), array( 'jquery-core' ), ALPHA_VERSION, true );
		wp_register_script( 'alpha-sticky-lib', alpha_framework_uri( '/assets/js/sticky' . ALPHA_JS_SUFFIX ), array( 'jquery-core' ), ALPHA_VERSION, true );
		wp_register_script( 'alpha-framework', alpha_framework_uri( '/assets/js/framework' . ALPHA_JS_SUFFIX ), array( 'jquery-core' ), ALPHA_VERSION, true );
		wp_register_script( 'alpha-framework-async', alpha_framework_uri( '/assets/js/framework-async' . ALPHA_JS_SUFFIX ), array( 'alpha-framework' ), ALPHA_VERSION, true );
		wp_register_script( 'alpha-woocommerce', alpha_framework_uri( '/assets/js/woocommerce/woocommerce' . ALPHA_JS_SUFFIX ), array( 'alpha-framework' ), ALPHA_VERSION, true );
		wp_register_script( 'alpha-shop', alpha_framework_uri( '/assets/js/woocommerce/shop' . ALPHA_JS_SUFFIX ), array( 'alpha-woocommerce' ), ALPHA_VERSION, true );
		wp_register_script( 'alpha-single-product', alpha_framework_uri( '/assets/js/woocommerce/single-product' . ALPHA_JS_SUFFIX ), array( 'alpha-woocommerce' ), ALPHA_VERSION, true );
		wp_register_script( 'alpha-ajax', alpha_framework_uri( '/assets/js/ajax' . ALPHA_JS_SUFFIX ), array( 'alpha-framework' ), ALPHA_VERSION, true );
		wp_register_script( 'alpha-theme', ALPHA_JS . '/theme' . ALPHA_JS_SUFFIX, array( 'alpha-framework-async' ), ALPHA_VERSION, true );
		wp_register_script( 'isotope-pkgd', ALPHA_ASSETS . '/vendor/isotope/isotope.pkgd' . ALPHA_JS_SUFFIX, array( 'jquery-core', 'imagesloaded' ), '3.0.6', true );
		wp_register_script( 'jquery-cookie', ALPHA_ASSETS . '/vendor/jquery.cookie/jquery.cookie' . ALPHA_JS_SUFFIX, array(), '1.4.1', true );
		wp_register_script( 'jquery-count-to', ALPHA_ASSETS . '/vendor/jquery.count-to/jquery.count-to' . ALPHA_JS_SUFFIX, array( 'jquery-core' ), false, true );
		wp_register_script( 'jquery-fitvids', ALPHA_ASSETS . '/vendor/jquery.fitvids/jquery.fitvids.min.js', array( 'jquery-core' ), false, true );
		wp_register_script( 'alpha-magnific-popup', ALPHA_ASSETS . '/vendor/jquery.magnific-popup/jquery.magnific-popup' . ALPHA_JS_SUFFIX, array( 'jquery-core', 'imagesloaded' ), '1.1.0', true );
		// if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			wp_register_script( 'swiper', ALPHA_ASSETS . '/vendor/swiper/swiper' . ALPHA_JS_SUFFIX, '6.7.0', true );
		// }
	}

	/**
	 * Enqueue styles and scripts for admin.
	 *
	 * @since 1.0
	 */
	public function enqueue_admin_scripts() {
		wp_register_style( 'alpha-admin-google-font', '//fonts.googleapis.com/css?family=Poppins', array(), ALPHA_VERSION );
		wp_register_style( 'fontawesome-free', ALPHA_ASSETS . '/vendor/fontawesome-free/css/all.min.css', array(), '5.14.0' );
		wp_register_style( 'jquery-select2', ALPHA_ASSETS . '/vendor/select2/select2.css', array(), '4.0.3' );
		wp_register_style( 'alpha-magnific-popup', ALPHA_ASSETS . '/vendor/jquery.magnific-popup/magnific-popup' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), '1.0' );
		wp_register_script( 'isotope-pkgd', ALPHA_ASSETS . '/vendor/isotope/isotope.pkgd' . ALPHA_JS_SUFFIX, array( 'jquery-core', 'imagesloaded' ), '3.0.6', true );
		wp_register_script( 'alpha-magnific-popup', ALPHA_ASSETS . '/vendor/jquery.magnific-popup/jquery.magnific-popup' . ALPHA_JS_SUFFIX, array( 'jquery-core', 'imagesloaded' ), '1.1.0', true );
		wp_register_script( 'jquery-select2', ALPHA_ASSETS . '/vendor/select2/select2' . ALPHA_JS_SUFFIX, array( 'jquery' ), '4.0.3', true );

		// Admin Scripts
		wp_enqueue_style( 'alpha-admin-google-font' );
		wp_enqueue_style( 'fontawesome-free' );
		wp_enqueue_style( 'alpha-admin-dynamic', ALPHA_CSS . '/dynamic_vars.min.css', array(), ALPHA_VERSION );
		wp_enqueue_style( 'alpha-admin', alpha_framework_uri( '/admin/admin/admin' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_VERSION );
		wp_enqueue_script( 'alpha-admin', alpha_framework_uri( '/admin/admin/admin' . ALPHA_JS_SUFFIX ), array( 'jquery-core' ), ALPHA_VERSION, true );
		wp_enqueue_script( 'wp-color-picker' );

		// Load google font
		wp_enqueue_style( 'alpha-admin-fonts', apply_filters( 'alpha_admin_fonts', '//fonts.googleapis.com/css?family=Poppins%3A400%2C500%2C600%2C700' ) );

		wp_localize_script(
			'alpha-admin',
			'alpha_admin_vars',
			apply_filters(
				'alpha_admin_vars',
				array(
					'theme'              => ALPHA_NAME,
					'theme_icon_prefix'  => ALPHA_ICON_PREFIX,
					'theme_display_name' => ALPHA_DISPLAY_NAME,
					'ajax_url'           => esc_url( admin_url( 'admin-ajax.php' ) ),
					'dummy_url'          => ALPHA_SERVER_URI . 'dummy/api/api',
					'nonce'              => wp_create_nonce( 'alpha-admin' ),
				)
			)
		);
	}

	/**
	 * Enqueue frontend styles.
	 *
	 * @since 1.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( 'alpha-animate' );

		/**
		 * Fires before enqueue theme style.
		 *
		 * @since 1.0
		 */
		do_action( 'alpha_before_enqueue_theme_style' );

		/**
		 * Filters the critical css.
		 *
		 * @since 1.0
		 */
		$critical_css = apply_filters( 'alpha_critical_css', 'body{opacity: 0; overflow-x: hidden}' );
		echo '<style id="alpha-critical-css">';
		echo alpha_escaped( $critical_css );
		echo '</style>' . PHP_EOL;

		/**
		 * Fires after enqueue theme style.
		 *
		 * @since 1.0
		 */
		do_action( 'alpha_after_enqueue_theme_style' );

	}

	/**
	 * Enqueue custom css.
	 *
	 * @since 1.0
	 */
	public function enqueue_custom_css() {

		// Enqueue Page CSS
		if ( function_exists( 'alpha_is_elementor_preview' ) && alpha_is_elementor_preview() ) {

			wp_enqueue_script( 'isotope-pkgd' );
			wp_enqueue_script( 'isotope-plugin' );
			wp_enqueue_script( 'jquery-countdown' );
		}

		/**
		 * Fires after enqueue custom style.
		 *
		 * @since 1.0
		 */
		do_action( 'alpha_after_enqueue_custom_style' );
	}

	/**
	 * Enqueue frontend scripts and localize vars.
	 *
	 * @since 1.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'imagesloaded' );
		wp_enqueue_script( 'alpha-framework' );
		wp_enqueue_script( 'alpha-framework-async' );

		$layout = alpha_get_page_layout();
		if ( 'archive_product' == $layout || ( class_exists( 'WooCommerce' ) && is_cart() ) ) { // Product Archive Page
			// shop
			wp_enqueue_script( 'alpha-shop' );
		} elseif ( 'archive_' == substr( $layout, 0, 8 ) ) { // Blog Page
			// blog
		} elseif ( 'single_page' == $layout ) { // Page
			if (
				( defined( 'YITH_WCWL' ) && function_exists( 'yith_wcwl_is_wishlist_page' ) && yith_wcwl_is_wishlist_page() ) ||
				( class_exists( 'WooCommerce' ) && ( is_cart() || is_checkout() || ( function_exists( 'alpha_is_compare' ) && alpha_is_compare() ) ) )
			) {
				// cart or checkout
				wp_enqueue_script( 'alpha-woocommerce' );
				alpha_quickview_add_scripts();
			}
		} elseif ( 'single_product' == $layout ) { // Single Product Page
			// single product
			wp_enqueue_script( 'alpha-single-product' );
		} elseif ( 'single_' == substr( $layout, 0, 7 ) ) { // Single Post Page
			// single post
		}
		$localize_vars = array(
			'theme'                => ALPHA_NAME,
			'theme_icon_prefix'    => ALPHA_ICON_PREFIX,
			'alpha_gap'            => ALPHA_GAP,
			'home_url'             => esc_url( home_url( '/' ) ),
			'ajax_url'             => esc_url( admin_url( 'admin-ajax.php' ) ),
			'nonce'                => wp_create_nonce( 'alpha-nonce' ),
			'lazyload'             => alpha_get_option( 'lazyload' ),
			'container'            => alpha_get_option( 'container' ),
			'assets_url'           => ALPHA_ASSETS,
			'texts'                => array(
				'loading'        => esc_html__( 'Loading...', 'alpha' ),
				'loadmore_error' => esc_html__( 'Loading failed', 'alpha' ),
				'popup_error'    => esc_html__( 'The content could not be loaded.', 'alpha' ),
				'quick_access'   => esc_attr__( 'Click to edit this element.', 'alpha' ),
			),
			'resource_split_tasks' => alpha_get_option( 'resource_split_tasks' ),
			'resource_after_load'  => alpha_get_option( 'resource_after_load' ),
			'alpha_cache_key'      => 'alpha_cache_' . MD5( home_url() ),
			'lazyload_menu'        => alpha_get_option( 'lazyload_menu' ),
			'countdown'            => array(
				'labels'       => array(
					esc_html__( 'Years', 'alpha' ),
					esc_html__( 'Months', 'alpha' ),
					esc_html__( 'Weeks', 'alpha' ),
					esc_html__( 'Days', 'alpha' ),
					esc_html__( 'Hours', 'alpha' ),
					esc_html__( 'Minutes', 'alpha' ),
					esc_html__( 'Seconds', 'alpha' ),
				),
				'labels_short' => array(
					esc_html__( 'Years', 'alpha' ),
					esc_html__( 'Months', 'alpha' ),
					esc_html__( 'Weeks', 'alpha' ),
					esc_html__( 'Days', 'alpha' ),
					esc_html__( 'Hrs', 'alpha' ),
					esc_html__( 'Mins', 'alpha' ),
					esc_html__( 'Secs', 'alpha' ),
				),
				'label1'       => array(
					esc_html__( 'Year', 'alpha' ),
					esc_html__( 'Month', 'alpha' ),
					esc_html__( 'Week', 'alpha' ),
					esc_html__( 'Day', 'alpha' ),
					esc_html__( 'Hour', 'alpha' ),
					esc_html__( 'Minute', 'alpha' ),
					esc_html__( 'Second', 'alpha' ),
				),
				'label1_short' => array(
					esc_html__( 'Year', 'alpha' ),
					esc_html__( 'Month', 'alpha' ),
					esc_html__( 'Week', 'alpha' ),
					esc_html__( 'Day', 'alpha' ),
					esc_html__( 'Hour', 'alpha' ),
					esc_html__( 'Min', 'alpha' ),
					esc_html__( 'Sec', 'alpha' ),
				),
			),
		);

		// Scripts for page editors (edit link tooltip)
		if ( current_user_can( 'edit_pages' ) ) {
			wp_enqueue_script( 'bootstrap-tooltip', ALPHA_ASSETS . '/vendor/bootstrap/bootstrap.tooltip' . ALPHA_JS_SUFFIX, array( 'jquery-core' ), '4.1.3', true );
		}

		if ( alpha_get_option( 'lazyload_menu' ) ) {
			$localize_vars['menu_last_time'] = alpha_get_option( 'menu_last_time' );
		}

		if ( alpha_get_option( 'skeleton_screen' ) ) {
			$localize_vars['skeleton_screen'] = true;
			$localize_vars['posts_per_page']  = get_query_var( 'posts_per_page' );
		}

		if ( alpha_get_option( 'archive_ajax' ) && ( is_archive() || is_home() || is_search() ) && ! alpha_is_shop() ) {
			$localize_vars['blog_ajax'] = 1;
		}

		// @start feature: fs_plugin_woocommerce
		if ( class_exists( 'WooCommerce' ) && alpha_get_option( 'compare_available' ) ) {
			$localize_vars['compare_limit'] = alpha_get_option( 'compare_limit' );
		}
		// @end feature: fs_plugin_woocommerce

		// @start feature: fs_pb_elementor
		if ( alpha_get_feature( 'fs_pb_elementor' ) && defined( 'ELEMENTOR_VERSION' ) ) {
			// if ( class_exists( 'Elementor\Plugin' ) && Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_assets_loading' ) ) {
			// 	$localize_vars['swiper_url'] = plugins_url( 'elementor/assets/lib/swiper/swiper' . ALPHA_JS_SUFFIX );
			// }
			/**
			 * Filters the resource that disable elementor.
			 *
			 * @since 1.0
			 */
			if ( apply_filters( 'alpha_resource_disable_elementor', alpha_get_option( 'resource_disable_elementor' ) ) && ! current_user_can( 'edit_pages' ) ) {
				$localize_vars['resource_disable_elementor'] = 1;
			}
		}
		// @end feature: fs_pb_elementor

		// @start feature: fs_plugin_woocommerce
		if ( class_exists( 'WooCommerce' ) ) {
			/**
			 * Filters whether current page is vendor or not.
			 *
			 * @since 1.0
			 */
			if ( alpha_get_option( 'archive_ajax' ) && ! apply_filters( 'alpha_is_vendor_store', false ) && alpha_is_shop() ) {
				$localize_vars['shop_ajax'] = 1;
			}

			$localize_vars = array_merge_recursive(
				$localize_vars,
				array(
					'home_url'            => esc_js( home_url( '/' ) ),
					'shop_url'            => esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ),
					'post_type'           => get_post_type(),
					'quickview_type'      => alpha_get_option( 'quickview_type' ),
					'quickview_thumbs'    => alpha_get_option( 'quickview_thumbs' ),
					'quickview_wrap_1'    => esc_js( 'col-md-6' ),
					'quickview_wrap_2'    => esc_js( 'col-md-6' ),
					'quickview_percent'   => esc_js( '50%' ),
					'prod_open_click_mob' => alpha_get_option( 'prod_open_click_mob' ),
					'texts'               => array(
						/* translators: %d represents loaded products count. */
						'show_info_all'   => esc_html__( 'all %d', 'alpha' ),
						'already_voted'   => esc_html__( 'You already voted!', 'alpha' ),
						'view_checkout'   => esc_html__( 'Checkout', 'alpha' ),
						'view_cart'       => esc_html__( 'View Cart', 'alpha' ),
						'add_to_wishlist' => esc_html__( 'Add to wishlist', 'alpha' ),
						'cart_suffix'     => esc_html__( 'has been added to cart', 'alpha' ),
						'select_category' => esc_js( __( 'Select a category', 'alpha' ) ),
						'no_matched'      => esc_js( _x( 'No matches found', 'enhanced select', 'alpha' ) ),
					),
					'pages'               => array(
						'cart'     => wc_get_page_permalink( 'cart' ),
						'checkout' => wc_get_page_permalink( 'checkout' ),
					),
					'single_product'      => array(
						'zoom_enabled' => true,
						'zoom_options' => array(),
					),
					'cart_auto_update'    => alpha_get_option( 'cart_auto_update' ),
					'cart_show_qty'       => alpha_get_option( 'cart_show_qty' ),
				)
			);
		}
		// @end feature: fs_plugin_woocommerce
		/**
		 * Filters the vars.
		 *
		 * @since 1.0
		 */
		wp_localize_script( 'alpha-framework', 'alpha_vars', apply_filters( 'alpha_vars', $localize_vars ) );
		if ( ! empty( $localize_vars['shop_ajax'] ) || ! empty( $localize_vars['blog_ajax'] ) ) {
			wp_enqueue_script( 'alpha-ajax' );
		}
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Enqueue theme js at last.
	 *
	 * @since 1.2.0
	 */
	public function enqueue_theme_js_css() {

		// Theme js
		wp_enqueue_script( 'alpha-theme' );

		// Theme page style
		$custom_css_handle = 'alpha-theme';
		$layout            = alpha_get_page_layout();
		if ( 'archive_product' == $layout ) { // Product Archive Page
			$custom_css_handle = 'alpha-theme-shop';
			if ( defined( 'ALPHA_CORE_VERSION' ) ) {
				wp_enqueue_style( 'alpha-product', alpha_core_framework_uri( '/widgets/products/product' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_CORE_VERSION );
			}
			wp_enqueue_style( 'alpha-theme-single-product', ALPHA_ASSETS . '/css/pages/single-product' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), ALPHA_VERSION );
		} elseif ( 'archive_' == substr( $layout, 0, 8 ) ) { // Blog Page
			$custom_css_handle = 'alpha-theme-blog';
			if ( defined( 'ALPHA_CORE_VERSION' ) ) {
				wp_enqueue_style( 'alpha-post', alpha_core_framework_uri( '/widgets/posts/post' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_CORE_VERSION );
			}
		} elseif ( 'single_page' == $layout ) { // Page
			if (
				( defined( 'YITH_WCWL' ) && function_exists( 'yith_wcwl_is_wishlist_page' ) && yith_wcwl_is_wishlist_page() ) ||
				( class_exists( 'WooCommerce' ) && is_account_page() )
			) {
				$custom_css_handle = 'alpha-theme-shop-other';
				if ( defined( 'ALPHA_CORE_VERSION' ) ) {
					wp_enqueue_style( 'alpha-product', alpha_core_framework_uri( '/widgets/products/product' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_CORE_VERSION );
				}
				wp_enqueue_style( 'alpha-theme-single-product', ALPHA_ASSETS . '/css/pages/single-product' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), ALPHA_VERSION );
			}
			// Compare Page
			if ( ( class_exists( 'WooCommerce' ) && function_exists( 'alpha_is_compare' ) && alpha_is_compare() ) && defined( 'ALPHA_CORE_VERSION' ) ) {
				wp_enqueue_style( 'alpha-product', alpha_core_framework_uri( '/widgets/products/product' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_CORE_VERSION );
			}
		} elseif ( 'single_product' == $layout ) { // Single Product Page
			$custom_css_handle = 'alpha-theme-single-product';
			if ( defined( 'ALPHA_CORE_VERSION' ) ) {
				wp_enqueue_style( 'alpha-tab', alpha_core_framework_uri( '/widgets/tab/tab' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_CORE_VERSION );
				wp_enqueue_style( 'alpha-accordion', alpha_core_framework_uri( '/widgets/accordion/accordion' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_CORE_VERSION );
				wp_enqueue_style( 'alpha-product', alpha_core_framework_uri( '/widgets/products/product' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_CORE_VERSION );
			}
			wp_enqueue_script( 'photoswipe' );
		} elseif ( 'cart' == $layout || 'checkout' == $layout ) {
			$custom_css_handle = 'alpha-theme-shop-other';
			if ( defined( 'ALPHA_CORE_VERSION' ) && 'cart' == $layout ) {
				wp_enqueue_style( 'alpha-product', alpha_core_framework_uri( '/widgets/products/product' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_CORE_VERSION );
			}
			wp_enqueue_style( 'alpha-theme-single-product', ALPHA_ASSETS . '/css/pages/single-product' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), ALPHA_VERSION );
		} elseif ( 'single_' == substr( $layout, 0, 7 ) ) { // Single Post Page
			$custom_css_handle = 'alpha-theme-single-post';
			if ( defined( 'ALPHA_CORE_VERSION' ) ) {
				wp_enqueue_style( 'alpha-post', alpha_core_framework_uri( '/widgets/posts/post' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_CORE_VERSION );
			}
		}

		wp_enqueue_style( 'alpha-theme' );
		wp_enqueue_style( 'alpha-dynamic-vars' );

		if ( 'alpha-theme' != $custom_css_handle ) {
			wp_enqueue_style( $custom_css_handle );
		}

		/**
		 * Fires before enqueue custom style.
		 *
		 * @since 1.0
		 */
		do_action( 'alpha_before_enqueue_custom_css' );
		// Theme Style
		wp_enqueue_style( 'alpha-style' );
		// Global css
		if ( ! is_customize_preview() ) {
			$custom_css = alpha_get_option( 'custom_css' );
			if ( $custom_css ) {
				wp_add_inline_style( 'alpha-style', '/* Global CSS */' . PHP_EOL . wp_strip_all_tags( wp_specialchars_decode( $custom_css ) ) );
			}
		}

		// Getting Page ID
		if ( class_exists( 'WooCommerce' ) && is_shop() ) { // Shop Page
			$page_id = wc_get_page_id( 'shop' );
		} elseif ( is_home() && get_option( 'page_for_posts' ) ) { // Blog Page
			$page_id = get_option( 'page_for_posts' );
		} elseif ( is_archive() || is_search() ) {
			$page_id = get_queried_object_id();
		} else {
			$page_id = get_the_ID();
		}

		$page_css  = '';
		$meta_type = 'post';
		if ( is_tax() ) {
			$meta_type = 'term';
		}
		if ( ! ( function_exists( 'alpha_is_elementor_preview' ) && alpha_is_elementor_preview() ) ) {
			$page_css = get_metadata( $meta_type, intval( $page_id ), 'page_css', true );
		}
		$page_css .= get_metadata( $meta_type, $page_id, ALPHA_NAME . '_blocks_style_options_css', true );

		if ( $page_css ) {
			wp_add_inline_style( 'alpha-style', '/* Page CSS */' . PHP_EOL . wp_strip_all_tags( $page_css ) );
		}

		wp_enqueue_style( 'fontawesome-free' );
		wp_enqueue_style( 'alpha-icons' );
		// Styles for page editors (edit link tooltip)
		if ( current_user_can( 'edit_pages' ) ) {
			wp_enqueue_style( 'bootstrap-tooltip', ALPHA_ASSETS . '/vendor/bootstrap/bootstrap.tooltip.css', array(), '4.1.3' );
		}

		alpha_load_google_font();
	}

	/**
	 * Enqueue custom js.
	 *
	 * @since 1.0
	 */
	public function enqueue_custom_js() {
		global $alpha_layout;
		$global_js = alpha_get_option( 'custom_js' );
		if ( $global_js ) {
			?>
			<script id="alpha_custom_global_script">
				<?php echo alpha_strip_script_tags( $global_js ); ?>
			</script>
			<?php
		}

		// Getting Page ID
		if ( class_exists( 'WooCommerce' ) && is_shop() ) { // Shop Page
			$page_id = wc_get_page_id( 'shop' );
		} elseif ( is_home() && get_option( 'page_for_posts' ) ) { // Blog Page
			$page_id = get_option( 'page_for_posts' );
		} else {
			$page_id = get_the_ID();
		}

		$page_js = get_post_meta( intval( $page_id ), 'page_js', true );
		if ( $page_js ) {
			?>
			<script id="alpha_custom_page_script">
				<?php echo alpha_strip_script_tags( $page_js ); ?>
			</script>
			<?php
		}

		if ( isset( $alpha_layout['used_blocks'] ) && $alpha_layout['used_blocks'] ) {
			foreach ( $alpha_layout['used_blocks'] as $block_id => $value ) {
				$script = get_post_meta( $block_id, 'page_js', true );
				if ( $script ) {
					?>
				<script id="alpha_block_<?php echo esc_attr( $block_id ); ?>_script">
					<?php echo alpha_strip_script_tags( $script ); ?>
				</script>
					<?php
				}

				$alpha_layout['used_blocks'][ $block_id ]['js'] = true;
			}
		}
	}
}

Alpha_Assets::get_instance();
