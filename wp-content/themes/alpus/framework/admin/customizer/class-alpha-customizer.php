<?php
/**
 * Alpha Customizer
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Alpha_Customizer' ) ) :

	class Alpha_Customizer extends Alpha_Base {

		/**
		 * The WP_Customizer instance
		 *
		 * @var WP_Customizer
		 * @since 1.0
		 */
		protected $wp_customize;
		public $blocks;
		public $popups;
		public $product_layouts;

		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'customize_controls_print_styles', array( $this, 'load_styles' ) );
			add_action( 'customize_controls_print_scripts', array( $this, 'load_scripts' ), 30 );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_selective_assets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_global_custom_css' ), 21 );
			add_action( 'customize_save_after', array( $this, 'save_theme_options' ), 1 );
			add_action( 'customize_register', array( $this, 'customize_register' ) );

			// Theme Option Import/Export
			add_action( 'wp_ajax_alpha_import_theme_options', array( $this, 'import_options' ) );
			add_action( 'wp_ajax_nopriv_alpha_import_theme_options', array( $this, 'import_options' ) );
			add_action( 'wp_ajax_alpha_export_theme_options', array( $this, 'export_options' ) );
			add_action( 'wp_ajax_nopriv_alpha_export_theme_options', array( $this, 'export_options' ) );

			// Theme Option Reset
			add_action( 'wp_ajax_alpha_reset_theme_options', array( $this, 'reset_options' ) );
			add_action( 'wp_ajax_nopriv_alpha_reset_theme_options', array( $this, 'reset_options' ) );

			// Get Page Links ( Load other page for previewer )
			add_filter( 'alpha_admin_vars', array( $this, 'add_local_vars' ) );

			// Customize Navigator
			add_action( 'customize_controls_print_scripts', array( $this, 'customizer_navigator' ) );

			add_action( 'wp_ajax_alpha_save_custom_options', array( $this, 'save_custom_options' ) );
			add_action( 'wp_ajax_nopriv_alpha_save_custom_options', array( $this, 'save_custom_options' ) );

			// Setup options
			add_action( 'init', array( $this, 'setup_options' ) );

			// Selective Refresh
			add_action( 'customize_register', array( $this, 'selective_refresh' ) );

			// Update Product Placeholder
			if ( class_exists( 'WooCommerce' ) ) {
				add_filter( 'pre_set_theme_mod_product_placeholder_image', array( $this, 'update_woocommerce_placeholder_image' ), 10, 2 );
			}
		}

		/**
		 * load selective refresh JS
		 *
		 * @since 1.0
		 */
		public function load_selective_assets() {

			wp_enqueue_script( 'alpha-selective', alpha_framework_uri( '/admin/customizer/selective-refresh' . ALPHA_JS_SUFFIX ), array( 'jquery-core' ), ALPHA_VERSION, true );

			wp_localize_script(
				'alpha-selective',
				'alpha_selective_vars',
				array(
					'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'nonce'    => wp_create_nonce( 'alpha-selective' ),
				)
			);
		}

		/**
		 * load custom css
		 *
		 * @since 1.0
		 */
		public function load_global_custom_css() {
			wp_enqueue_style( 'alpha-preview-custom', ALPHA_FRAMEWORK_ADMIN_URI . '/customizer/preview-custom.css' );
			wp_add_inline_style( 'alpha-preview-custom', wp_strip_all_tags( wp_specialchars_decode( alpha_get_option( 'custom_css' ) ) ) );
		}

		/**
		 * Add CSS for Customizer Options
		 *
		 * @since 1.0
		 */
		public function load_styles() {
			wp_enqueue_style( 'alpha-customizer', alpha_framework_uri( '/admin/customizer/customizer' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), null, ALPHA_VERSION, 'all' );
			wp_enqueue_style( 'alpha-magnific-popup' );
		}

		/**
		 * Add JS for Customizer Options
		 *
		 * @since 1.0
		 */
		public function load_scripts() {

			wp_enqueue_script( 'alpha-customizer', alpha_framework_uri( '/admin/customizer/customizer' . ALPHA_JS_SUFFIX ), array(), ALPHA_VERSION, true );

			wp_localize_script(
				'alpha-customizer',
				'alpha_customizer_vars',
				array(
					'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'nonce'    => wp_create_nonce( 'alpha-customizer' ),
				)
			);
		}

		/**
		 * Save theme options
		 *
		 * @since 1.0
		 */
		public function save_theme_options() {
			ob_start();
			include alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/customizer/dynamic/dynamic_vars.php' );

			global $wp_filesystem;
			// Initialize the WordPress filesystem, no more using file_put_contents function
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}

			try {
				$target      = wp_upload_dir()['basedir'] . '/' . ALPHA_NAME . '_styles/dynamic_vars.min.css';
				$target_path = dirname( $target );
				if ( ! file_exists( $target_path ) ) {
					wp_mkdir_p( $target_path );
				}

				// check file mode and make it writable.
				if ( is_writable( $target_path ) == false ) {
					@chmod( get_theme_file_path( $target ), 0755 );
				}
				if ( file_exists( $target ) ) {
					if ( is_writable( $target ) == false ) {
						@chmod( $target, 0755 );
					}
					@unlink( $target );
				}

				$wp_filesystem->put_contents( $target, ob_get_clean(), FS_CHMOD_FILE );
			} catch ( Exception $e ) {
				var_dump( $e );
				var_dump( 'error occured while saving dynamic css vars.' );
			}
		}

		public function customize_register( $wp_customize ) {
			$this->wp_customize = $wp_customize;
		}

		/**
		 * Import theme options
		 *
		 * @since 1.0
		 */
		public function import_options() {
			if ( ! $this->wp_customize->is_preview() ) {
				wp_send_json_error( 'not_preview' );
			}

			if ( ! check_ajax_referer( 'alpha-customizer', 'nonce', false ) ) {
				wp_send_json_error( 'invalid_nonce' );
			}

			if ( empty( $_FILES['file'] ) || empty( $_FILES['file']['name'] ) ) {
				wp_send_json_error( 'Empty file pathname' );
			}

			$filename = $_FILES['file']['name'];

			if ( empty( $_FILES['file']['tmp_name'] ) || '.json' !== substr( $filename, -5 ) ) {
				wp_send_json_error( 'invalid_type' );
			}

			// filesystem
			global $wp_filesystem;
			// Initialize the WordPress filesystem, no more using file_put_contents function
			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}
			$options = $wp_filesystem->get_contents( $_FILES['file']['tmp_name'] );

			if ( $options ) {
				$options = json_decode( $options, true );
			}
			if ( $options ) {
				update_option( 'theme_mods_' . get_option( 'stylesheet' ), $options );
				wp_send_json_success();
			} else {
				wp_send_json_error( 'invalid_type' );
			}
		}

		/**
		 * Get menus
		 *
		 * @since 1.0
		 */
		public function get_menus() {
			$nav_menus = wp_get_nav_menus();
			$menus     = array();
			foreach ( $nav_menus as $menu ) {
				if ( ! preg_match( '/[^a-z\d]/i', $menu->name ) ) { // only for English (demos)
					$menus[ $menu->slug ] = esc_html( $menu->name );
				} else {
					$menus[ $menu->term_id ] = esc_html( $menu->name );
				}
			}
			return $menus;
		}

		/**
		 * Export theme options.
		 *
		 * @since 1.0
		 */
		public function export_options() {
			if ( ! $this->wp_customize->is_preview() ) {
				wp_send_json_error( 'not_preview' );
			}

			if ( ! check_ajax_referer( 'alpha-customizer', 'nonce', false ) ) {
				wp_send_json_error( 'invalid_nonce' );
			}

			header( 'Content-Description: File Transfer' );
			header( 'Content-type: application/txt' );
			header( 'Content-Disposition: attachment; filename="alpha_theme_options_backup_' . date( 'Y-m-d' ) . '.json"' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate' );
			header( 'Pragma: public' );
			echo json_encode( get_theme_mods() );
			exit;
		}

		/**
		 * Reset theme options
		 *
		 * @since 1.0
		 */
		public function reset_options() {
			if ( ! $this->wp_customize->is_preview() ) {
				wp_send_json_error( 'not_preview' );
			}

			if ( ! check_ajax_referer( 'alpha-customizer', 'nonce', false ) ) {
				wp_send_json_error( 'invalid_nonce' );
			}

			remove_theme_mods();

			// Delete compiled css in uploads/alpha_style directory.
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}

			try {
				$wp_filesystem->delete( wp_upload_dir()['basedir'] . '/' . ALPHA_NAME . '_styles', true );
			} catch ( Exception $e ) {
				wp_send_json_error( 'error occured while deleting compiled css.' );
			}

			wp_send_json_success();
		}

		/**
		 * Get Page Links
		 *
		 * @since 1.0
		 */
		public function add_local_vars( $vars ) {

			$home_url     = esc_js( home_url() );
			$blog_url     = '';
			$post_url     = '';
			$shop_url     = '';
			$cart_url     = '';
			$checkout_url = '';
			$product_url  = '';

			$post = get_posts( array( 'posts_per_page' => 1 ) );
			if ( is_array( $post ) && count( $post ) ) {
				$blog_url = esc_js( get_post_type_archive_link( 'post' ) );
				$post_url = esc_js( get_permalink( $post[0] ) );
			}
			// @start feature: fs_plugin_woocommerce
			if ( class_exists( 'WooCommerce' ) ) {
				$shop_url     = esc_js( wc_get_page_permalink( 'shop' ) );
				$cart_url     = esc_js( wc_get_page_permalink( 'cart' ) );
				$checkout_url = esc_js( wc_get_page_permalink( 'checkout' ) );
				$product_url  = '';
				$product      = get_posts(
					array(
						'posts_per_page' => 1,
						'post_type'      => 'product',
					)
				);
				if ( is_array( $product ) && count( $product ) ) {
					$product_url = esc_js( get_permalink( $product[0] ) );
				}
			}
			// @end feature: fs_plugin_woocommerce

			$vars['page_links'] = apply_filters(
				'alpha_customize_page_links',
				array(
					'blog_archive'         => array(
						'url'      => $blog_url,
						'is_panel' => false,
					),
					'blog_single'          => array(
						'url'      => $post_url,
						'is_panel' => false,
					),
					'products_archive'     => array(
						'url'      => $shop_url,
						'is_panel' => false,
					),
					'product_type'     => array(
						'url'      => $shop_url,
						'is_panel' => false,
					),
					'product_detail'       => array(
						'url'      => $product_url,
						'is_panel' => false,
					),
					'wc_cart'              => array(
						'url'      => $cart_url,
						'is_panel' => false,
					),
					'woocommerce_checkout' => array(
						'url'      => $checkout_url,
						'is_panel' => false,
					),
				)
			);

			return $vars;
		}

		/**
		 * Get Navigator Template
		 *
		 * @since 1.0
		 */
		public function customizer_navigator() {
			$nav_items = alpha_get_option( 'navigator_items' );

			ob_start();
			?>
			<div class="customizer-nav">
				<h3><?php esc_html_e( 'Navigator', 'alpha' ); ?><a href="#" class="navigator-toggle"><i class="fas fa-chevron-left"></i></a></h3>
				<div class="customizer-nav-content">
					<ul class="customizer-nav-items">
						<?php foreach ( $nav_items as $section => $label ) : ?>
						<li>
							<a href="#" data-target="<?php echo esc_attr( $section ); ?>" data-type="<?php echo esc_attr( $label[1] ); ?>" class="customizer-nav-item"><?php echo esc_html( $label[0] ); ?></a>
							<a href="#" class="customizer-nav-remove"><i class="fas fa-trash"></i></a>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php
			echo ob_get_clean();
		}

		/**
		 * Save Navigator Items
		 *
		 * @since 1.0
		 */
		public function save_custom_options() {
			if ( ! check_ajax_referer( 'alpha-customizer', 'nonce', false ) ) {
				wp_send_json_error( 'invalid_nonce' );
			}

			if ( isset( $_REQUEST['options']['nav'] ) ) {
				set_theme_mod( 'navigator_items', $_REQUEST['options']['nav'] );
			} else {
				set_theme_mod( 'navigator_items', array() );
			}

			// if ( isset( $_REQUEST['options']['mlabels'] ) ) {
			// 	set_theme_mod( 'menu_labels', wp_unslash( $_REQUEST['options']['mlabels'] ) );
			// } else {
			// 	set_theme_mod( 'menu_labels', '' );
			// }
			wp_send_json_success();
		}

		/**
		 * Get editing menu label control.
		 *
		 * @since 1.0
		 */
		public function get_edit_menu_label_control() {
			ob_start();
			?>
			<div class="label-list">
				<label><?php esc_html_e( 'Menu Labels', 'alpha' ); ?></label>
				<select id="label-select" name="label-select">
				<?php
				$labels = json_decode( alpha_get_option( 'menu_labels' ), true );
				if ( $labels ) :
					foreach ( $labels as $text => $color ) :
						?>
						<option value="<?php echo esc_attr( $color ); ?>"><?php echo esc_html( $text ); ?></option>
						<?php
					endforeach;
				endif;
				?>
				</select>
			</div>
			<div class="menu-label">
				<label><?php esc_html_e( 'Label Text to Change', 'alpha' ); ?></label>
				<input type="text" class="label-text" value="<?php echo esc_attr( $labels ? array_keys( $labels )[0] : '' ); ?>">
				<label><?php esc_html_e( 'Label Background Color to Change', 'alpha' ); ?></label>
				<input type="text" class="alpha-color-picker" value="<?php echo esc_attr( $labels ? $labels[ array_keys( $labels )[0] ] : '' ); ?>">
				<div class="label-actions">
					<button class="button button-primary btn-change-label"><?php esc_html_e( 'Change', 'alpha' ); ?></button>
					<button class="button btn-remove-label"><?php esc_html_e( 'Remove', 'alpha' ); ?></button>
				</div>
				<p class="error-msg"></p>
			</div>
			<?php
			return ob_get_clean();
		}

		public function get_new_menu_label_control() {
			ob_start();
			?>
			<div class="menu-label">
				<label><?php esc_html_e( 'Input Label Text', 'alpha' ); ?></label>
				<input type="text" class="label-text">
				<label><?php esc_html_e( 'Choose Label Background Color', 'alpha' ); ?></label>
				<input type="text" class="alpha-color-picker" value="">
				<div class="label-actions">
					<button class="button button-primary btn-add-label"><?php esc_html_e( 'Add', 'alpha' ); ?></button>
				</div>
				<p class="error-msg"></p>
			</div>
			<?php
			return ob_get_clean();
		}


		public function setup_options() {

			$alpha_templates = alpha_get_global_templates_sidebars();
			if ( ! empty( $alpha_templates['block'] ) ) {
				$custom_tab_block     = $alpha_templates['block'];
				$custom_tab_block[''] = esc_html__( 'None', 'alpha' );
			}

			$panels = array(
				'style'     => array(
					'title'    => esc_html__( 'Style', 'alpha' ),
					'priority' => 20,
				),
				'layouts'   => array(
					'title'    => esc_html__( 'Page Layouts', 'alpha' ),
					'priority' => 30,
				),
				'nav_menus' => array(
					'title'    => esc_html__( 'Menus', 'alpha' ),
					'priority' => 40,
				),
				'widgets'   => array(
					'title'    => esc_html__( 'Widgets', 'alpha' ),
					'priority' => 100,
				),
				'advanced'  => array(
					'title'    => esc_html__( 'Miscellaneous', 'alpha' ),
					'priority' => 120,
				),
				'features'  => array(
					'title'    => esc_html__( 'Features', 'alpha' ),
					'priority' => 110,
				),
			);

			$sections = array(
				// General Panel
				'general'           => array(
					'title'    => esc_html__( 'General', 'alpha' ),
					'priority' => 10,
				),
				// Header Panel
				'header_footer'     => array(
					'title'    => esc_html__( 'Header & Footer', 'alpha' ),
					'priority' => 10,
				),
				// Blog Panel
				'blog'              => array(
					'title'    => esc_html__( 'Blog', 'alpha' ),
					'priority' => 50,
				),
				// Custom CSS & JS Panel
				'custom_css_js'     => array(
					'title'    => esc_html__( 'Custom CSS & JS', 'alpha' ),
					'priority' => 130,
				),
				'maintenance'       => array(
					'title'    => esc_html__( 'Maintenance', 'alpha' ),
					'priority' => 140,
				),
				// Change Orders
				'title_tagline'     => array(
					'title'    => esc_html__( 'Site Identity', 'alpha' ),
					'priority' => 150,
				),
				'static_front_page' => array(
					'title'    => esc_html__( 'Homepage Settings', 'alpha' ),
					'priority' => 160,
				),
				'colors'            => array(
					'title'    => esc_html__( 'Color', 'alpha' ),
					'priority' => 160,
				),
				'header_image'      => array(
					'title'    => esc_html__( 'Header Image', 'alpha' ),
					'priority' => 170,
				),
				'background_image'  => array(
					'title'    => esc_html__( 'Background Image', 'alpha' ),
					'priority' => 180,
				),
				// Style Panel
				'color'             => array(
					'title'    => esc_html__( 'Color', 'alpha' ),
					'panel'    => 'style',
					'priority' => 10,
				),
				'typo'              => array(
					'title'    => esc_html__( 'Typography', 'alpha' ),
					'panel'    => 'style',
					'priority' => 20,
				),
				'title_bar'         => array(
					'title'    => esc_html__( 'Page Title Bar', 'alpha' ),
					'panel'    => 'style',
					'priority' => 30,
				),
				'breadcrumb'        => array(
					'title'    => esc_html__( 'Breadcrumbs', 'alpha' ),
					'panel'    => 'style',
					'priority' => 40,
				),
				// Menus
				'menu_labels'       => array(
					'title'    => esc_html__( 'Menu Labels', 'alpha' ),
					'panel'    => 'nav_menus',
					'priority' => 3,
				),
				'mobile_menu'       => array(
					'title'    => esc_html__( 'Mobile Menu', 'alpha' ),
					'panel'    => 'nav_menus',
					'priority' => 6,
				),
				'mobile_bar'        => array(
					'title'    => esc_html__( 'Mobile Sticky Icon Bar', 'alpha' ),
					'priority' => 8,
					'panel'    => 'nav_menus',
				),
				// Features
				'ajax_filter'       => array(
					'title'    => esc_html__( 'Ajax Filter', 'alpha' ),
					'panel'    => 'features',
					'priority' => 10,
				),
				'lazyload'          => array(
					'title'    => esc_html__( 'Lazy Load', 'alpha' ),
					'priority' => 50,
					'panel'    => 'features',
				),
				'quickview'         => array(
					'title'    => esc_html__( 'Quickview', 'alpha' ),
					'panel'    => 'features',
					'priority' => 60,
				),
				'search'            => array(
					'title'    => esc_html__( 'Search', 'alpha' ),
					'priority' => 70,
					'panel'    => 'features',
				),
				'sociallogin'       => array(
					'title'    => esc_html__( 'Social Login', 'alpha' ),
					'priority' => 80,
					'panel'    => 'features',
				),
				// Advanced Panel
				'images'            => array(
					'title'    => esc_html__( 'Image Size & Quality', 'alpha' ),
					'priority' => 30,
					'panel'    => 'advanced',
				),
				'reset_options'     => array(
					'title'    => esc_html__( 'Import/Export/Reset', 'alpha' ),
					'priority' => 40,
					'panel'    => 'advanced',
				),
				'seo'               => array(
					'title'    => esc_html__( 'SEO', 'alpha' ),
					'priority' => 50,
					'panel'    => 'advanced',
				),
				'white_label'       => array(
					'title'    => esc_html__( 'White Label', 'alpha' ),
					'priority' => 60,
					'panel'    => 'advanced',
				),
			);

			$fields = array(
				// General / Site Layout
				'cs_site_layout'                => array(
					'section' => 'general',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Site Layout', 'alpha' ) . '</h3>',
				),
				'site_type'                     => array(
					'section'   => 'general',
					'type'      => 'radio_image',
					'label'     => esc_html__( 'Site Type', 'alpha' ),
					'transport' => 'postMessage',
					'choices'   => array(
						'full'   => ALPHA_ASSETS . '/images/admin/customizer/site-full.png',
						'boxed'  => ALPHA_ASSETS . '/images/admin/customizer/site-boxed.png',
						'framed' => ALPHA_ASSETS . '/images/admin/customizer/site-framed.png',
					),
				),
				'site_width'                    => array(
					'section'         => 'general',
					'type'            => 'text',
					'label'           => esc_html__( 'Site Width (px)', 'alpha' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'site_type',
							'operator' => '!=',
							'value'    => 'full',
						),
					),
				),
				'site_gap'                      => array(
					'section'         => 'general',
					'type'            => 'text',
					'label'           => esc_html__( 'Site Gap (px)', 'alpha' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'site_type',
							'operator' => '!=',
							'value'    => 'full',
						),
					),
				),
				'site_bg'                       => array(
					'section'         => 'general',
					'type'            => 'background',
					'label'           => esc_html__( 'Site Background', 'alpha' ),
					'tooltip'         => esc_html__( 'Change background of outside the frame.', 'alpha' ),
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'site_type',
							'operator' => '!=',
							'value'    => 'full',
						),
					),
				),
				'content_bg'                    => array(
					'section'   => 'general',
					'type'      => 'background',
					'label'     => esc_html__( 'Content Background', 'alpha' ),
					'default'   => '',
					'transport' => 'postMessage',
				),

				// General / Site Content
				'cs_general_content_title'      => array(
					'section' => 'general',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Container Width', 'alpha' ) . '</h3>',
				),
				'container'                     => array(
					'section'   => 'general',
					'type'      => 'text',
					'label'     => esc_html__( 'Container Width (px)', 'alpha' ),
					'transport' => 'postMessage',
				),
				'container_fluid'               => array(
					'section'   => 'general',
					'type'      => 'text',
					'label'     => esc_html__( 'Container Fluid Width (px)', 'alpha' ),
					'transport' => 'postMessage',
				),
				'cs_gutter_spacing'               => array(
					'section' => 'general',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Gutter Spacing', 'alpha' ) . '</h3><p style="margin-bottom: 10px; cursor: auto;">' . sprintf( esc_html__( 'Set site gutter spacing (default gap spacing between columns, projects, products etc) in Elementor %1$sSite Settings%2$s/Layout/Layout Settings', 'alpha' ), '<a href="https://elementor.com/help/site-settings/" target="_blank">', '</a>' ) . '</p>' .
					'<img class="description-image" src="' . ALPHA_ASSETS . '/images/admin/customizer/gutter.jpg' . '" alt="' . esc_html__( 'Theme Option Descrpition Image', 'alpha' ) . '">',
				),

				// Header & Footer
				'cs_header_title'               => array(
					'section' => 'header_footer',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Header', 'alpha' ) . '</h3><p style="margin-bottom: 10px; cursor: auto;">' . esc_html__( 'Create your header template and show it in Layout Builder', 'alpha' ) . '</p>' .
						(
							class_exists( 'Alpha_Builders' ) ?
							'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'edit.php?post_type=' . ALPHA_NAME . '_template&' . ALPHA_NAME . '_template_type=header' ) ) . '" target="_blank">' . esc_html__( 'Header Builder', 'alpha' ) . '</a>' :
							'<p>' . sprintf( esc_html__( 'Please install %s Core Plugin', 'alpha' ), ALPHA_DISPLAY_NAME ) . '</p>' .
							'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=alpha-setup-wizard&step=default_plugins' ) ) . '" target="_blank">' . esc_html__( 'Install Plugins', 'alpha' ) . '</a>'
						) .
						'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=alpha-layout-builder' ) ) . '" target="_blank">' . esc_html__( 'Layout Builder', 'alpha' ) . '</a>',
				),
				'cs_footer_title'               => array(
					'section' => 'header_footer',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Footer', 'alpha' ) . '</h3><p style="margin-bottom: 10px; cursor: auto;">' . esc_html__( 'Create your footer template and show it in Layout Builder', 'alpha' ) . '</p>' .
					(
						class_exists( 'Alpha_Builders' ) ?
						'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'edit.php?post_type=' . ALPHA_NAME . '_template&' . ALPHA_NAME . '_template_type=footer' ) ) . '" target="_blank">' :
						'<p>' . sprintf( esc_html__( 'Please install %s Core Plugin', 'alpha' ), ALPHA_DISPLAY_NAME ) . '</p>' .
							'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=alpha-setup-wizard&step=default_plugins' ) ) . '" target="_blank">' . esc_html__( 'Install Plugins', 'alpha' ) . '</a>'
					) . esc_html__( 'Footer Builder', 'alpha' ) . '</a><a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=alpha-layout-builder' ) ) . '" target="_blank">' . esc_html__( 'Layout Builder', 'alpha' ) . '</a>',
				),

				// Style / Color
				'cs_colors_title'               => array(
					'section' => 'color',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Colors', 'alpha' ) . '</h3>',
				),
				'primary_color'                 => array(
					'section'   => 'color',
					'type'      => 'color',
					'label'     => esc_html__( 'Primary Color', 'alpha' ),
					'choices'   => array(
						'alpha' => true,
					),
					'transport' => 'postMessage',
				),
				'secondary_color'               => array(
					'section'   => 'color',
					'type'      => 'color',
					'label'     => esc_html__( 'Secondary Color', 'alpha' ),
					'choices'   => array(
						'alpha' => true,
					),
					'transport' => 'postMessage',
				),
				'dark_color'                    => array(
					'section'   => 'color',
					'type'      => 'color',
					'label'     => esc_html__( 'Dark Color', 'alpha' ),
					'choices'   => array(
						'alpha' => true,
					),
					'transport' => 'postMessage',
				),
				'light_color'                   => array(
					'section'   => 'color',
					'type'      => 'color',
					'label'     => esc_html__( 'Light Color', 'alpha' ),
					'choices'   => array(
						'alpha' => true,
					),
					'transport' => 'postMessage',
				),

				// Style / Typography
				'cs_typo_default_font'          => array(
					'section' => 'typo',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Default Typography', 'alpha' ) . '</h3>',
				),
				'typo_default'                  => array(
					'section'   => 'typo',
					'type'      => 'typography',
					'label'     => '',
					'choices'   => apply_filters( 'alpha_kirki_typo_control_choices', array() ),
					'transport' => 'postMessage',
				),
				'cs_typo_heading'               => array(
					'section' => 'typo',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Heading Typography', 'alpha' ) . '</h3>',
				),
				'typo_heading'                  => array(
					'section'   => 'typo',
					'type'      => 'typography',
					'label'     => '',
					'choices'   => apply_filters( 'alpha_kirki_typo_control_choices', array() ),
					'transport' => 'postMessage',
				),
				'cs_typo_google_title'          => array(
					'section' => 'typo',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Google Fonts', 'alpha' ) . '</h3>',
				),
				'cs_typo_google_desc'           => array(
					'section' => 'typo',
					'type'    => 'custom',
					'default' => '<p style="margin: 0;">' . esc_html__( 'Select google fonts use throughout the site.', 'alpha' ) . '</p>',
				),
				'typo_custom_part'              => array(
					'section'   => 'typo',
					'type'      => 'radio-buttonset',
					'default'   => '1',
					'transport' => 'postMessage',
					'choices'   => array(
						'1' => esc_html__( 'Font 1', 'alpha' ),
						'2' => esc_html__( 'Font 2', 'alpha' ),
						'3' => esc_html__( 'Font 3', 'alpha' ),
					),
				),
				'typo_custom1'                  => array(
					'section'         => 'typo',
					'type'            => 'typography',
					'label'           => esc_html__( 'Font 1', 'alpha' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'typo_custom_part',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
				'typo_custom2'                  => array(
					'section'         => 'typo',
					'type'            => 'typography',
					'label'           => esc_html__( 'Font 2', 'alpha' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'typo_custom_part',
							'operator' => '==',
							'value'    => '2',
						),
					),
				),
				'typo_custom3'                  => array(
					'section'         => 'typo',
					'type'            => 'typography',
					'label'           => esc_html__( 'Font 3', 'alpha' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'typo_custom_part',
							'operator' => '==',
							'value'    => '3',
						),
					),
				),

				// Style / Title Bar
				'cs_ptb_bar_style_title'        => array(
					'section'   => 'title_bar',
					'type'      => 'custom',
					'label'     => '',
					'default'   => '<h3 class="options-custom-title">' . esc_html__( 'Title Bar Style', 'alpha' ) . '</h3>',
					'transport' => 'postMessage',
				),
				'ptb_height'                    => array(
					'section'   => 'title_bar',
					'type'      => 'number',
					'label'     => esc_html__( 'Title Bar Height (px)', 'alpha' ),
					'transport' => 'postMessage',
				),
				'ptb_bg'                        => array(
					'section'   => 'title_bar',
					'type'      => 'background',
					'label'     => esc_html__( 'Title Bar Background', 'alpha' ),
					'transport' => 'postMessage',
				),
				'cs_ptb_typo_title'             => array(
					'section'   => 'title_bar',
					'type'      => 'custom',
					'label'     => '',
					'default'   => '<h3 class="options-custom-title">' . esc_html__( 'Title Bar Typography', 'alpha' ) . '</h3>',
					'transport' => 'postMessage',
				),
				'typo_ptb_title'                => array(
					'section'   => 'title_bar',
					'type'      => 'typography',
					'label'     => esc_html__( 'Page Title', 'alpha' ),
					'choices'   => apply_filters( 'alpha_kirki_typo_control_choices', array() ),
					'transport' => 'postMessage',
				),
				'typo_ptb_subtitle'             => array(
					'section'   => 'title_bar',
					'type'      => 'typography',
					'label'     => esc_html__( 'Page Subtitle', 'alpha' ),
					'choices'   => apply_filters( 'alpha_kirki_typo_control_choices', array() ),
					'transport' => 'postMessage',
				),

				// Style / Breadcrumb
				'cs_ptb_breadcrumb_style_title' => array(
					'section'   => 'breadcrumb',
					'type'      => 'custom',
					'label'     => '',
					'default'   => '<h3 class="options-custom-title">' . esc_html__( 'Breadcrumb', 'alpha' ) . '</h3>',
					'transport' => 'postMessage',
				),
				'ptb_delimiter'                 => array(
					'section'   => 'breadcrumb',
					'type'      => 'text',
					'label'     => esc_html__( 'Breadcrumb Delimiter', 'alpha' ),
					'transport' => 'postMessage',
				),
				'typo_ptb_breadcrumb'           => array(
					'section'   => 'breadcrumb',
					'type'      => 'typography',
					'label'     => esc_html__( 'Breadcrumb Typography', 'alpha' ),
					'choices'   => apply_filters( 'alpha_kirki_typo_control_choices', array() ),
					'transport' => 'postMessage',
				),

				// Menus / Menu Labels
				'cs_menu_labels_title'          => array(
					'section' => 'menu_labels',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Menu Labels', 'alpha' ) . '</h3>',
				),
				'menu_labels'                   => array(
					'section'           => 'menu_labels',
					'type'              => 'text',
					'label'             => esc_html__( 'Menu Labels', 'alpha' ),
					'transport'         => 'refresh',
					'sanitize_callback' => 'wp_strip_all_tags',
				),
				'cs_menu_labels'                => array(
					'section' => 'menu_labels',
					'type'    => 'custom',
					'default' => $this->get_edit_menu_label_control(),
				),
				'cs_new_label'                  => array(
					'section' => 'menu_labels',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'New Label', 'alpha' ) . '</h3>',
				),
				'cs_new_menu_label'             => array(
					'section' => 'menu_labels',
					'type'    => 'custom',
					'default' => $this->get_new_menu_label_control(),
				),

				// Menus / Mobile Menu
				'cs_mobile_menu_title'          => array(
					'section'   => 'mobile_menu',
					'type'      => 'custom',
					'label'     => '',
					'default'   => '<h3 class="options-custom-title">' . esc_html__( 'Mobile Menu', 'alpha' ) . '</h3><a class="button button-outline button-xlarge" style="margin-top: 20px" href="' . esc_url( admin_url( 'nav-menus.php?action=edit&menu=0' ) ) . '" target="_blank">' . esc_html__( 'Create New Menu', 'alpha' ) . '</a>',
					'transport' => 'postMessage',
				),
				'mobile_menu_items'             => array(
					'section'   => 'mobile_menu',
					'type'      => 'sortable',
					'label'     => esc_html__( 'Mobile Menus', 'alpha' ),
					'transport' => 'refresh',
					'choices'   => $this->get_menus(),
				),

				// Menus / Mobile Sticky Icon Bar
				'cs_mobile_bar_title'           => array(
					'section'   => 'mobile_bar',
					'type'      => 'custom',
					'label'     => '',
					'default'   => '<h3 class="options-custom-title">' . esc_html__( 'Mobile Icon Bar', 'alpha' ) . '</h3>',
					'transport' => 'postMessage',
				),
				'mobile_bar_icons'              => array(
					'section'   => 'mobile_bar',
					'type'      => 'sortable',
					'label'     => esc_html__( 'Mobile Bar Icons', 'alpha' ),
					'transport' => 'refresh',
					'choices'   => array(
						'menu'     => esc_html__( 'Mobile Menu Toggle', 'alpha' ),
						'home'     => esc_html__( 'Home', 'alpha' ),
						'shop'     => esc_html__( 'Shop', 'alpha' ),
						'wishlist' => esc_html__( 'Wishlist', 'alpha' ),
						'account'  => esc_html__( 'Account', 'alpha' ),
						'compare'  => esc_html__( 'Compare', 'alpha' ),
						'cart'     => esc_html__( 'Cart', 'alpha' ),
						'search'   => esc_html__( 'Search', 'alpha' ),
						'top'      => esc_html__( 'To Top', 'alpha' ),
					),
				),
				'mobile_bar_menu_label'         => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Menu Label', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_menu_icon'          => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Menu Icon', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_home_label'         => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Home Label', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_home_icon'          => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Home Icon', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_shop_label'         => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Shop Label', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_shop_icon'          => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Shop Icon', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_wishlist_label'     => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Wishlist Label', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_wishlist_icon'      => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Wishlist Icon', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_account_label'      => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Account Label', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_account_icon'       => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Account Icon', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_cart_label'         => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Cart Label', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_cart_icon'          => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Cart Icon', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_search_label'       => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Search Label', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_search_icon'        => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Search Icon', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_top_label'          => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'To Top Label', 'alpha' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_top_icon'           => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'To Top Icon', 'alpha' ),
					'transport' => 'postMessage',
				),

				// Blog
				'cs_blog_single_title'          => array(
					'section' => 'blog',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Single Post', 'alpha' ) . '</h3><p style="margin-bottom: 10px; cursor: auto;">' . esc_html__( 'Create your post single page template and show it in Layout Builder', 'alpha' ) . '</p>' .
						(
							class_exists( 'Alpha_Builders' ) ?
							'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'edit.php?post_type=' . ALPHA_NAME . '_template&' . ALPHA_NAME . '_template_type=single' ) ) . '" target="_blank">' . esc_html__( 'Single Builder', 'alpha' ) . '</a>' :
							'<p>' . sprintf( esc_html__( 'Please install %s Core Plugin', 'alpha' ), ALPHA_DISPLAY_NAME ) . '</p>' .
							'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=alpha-setup-wizard&step=default_plugins' ) ) . '" target="_blank">' . esc_html__( 'Install Plugins', 'alpha' ) . '</a>'
						) .
						'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=alpha-layout-builder&layout=single_post' ) ) . '" target="_blank">' . esc_html__( 'Layout Builder', 'alpha' ) . '</a>',
				),
				'cs_blog_archive_title'         => array(
					'section' => 'blog',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Blog', 'alpha' ) . '</h3><p style="margin-bottom: 10px; cursor: auto;">' . esc_html__( 'Create your post archive page template and show it in Layout Builder', 'alpha' ) . '</p>' .
					(
						class_exists( 'Alpha_Builders' ) ?
						'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'edit.php?post_type=' . ALPHA_NAME . '_template&' . ALPHA_NAME . '_template_type=archive' ) ) . '" target="_blank">' :
						'<p>' . sprintf( esc_html__( 'Please install %s Core Plugin', 'alpha' ), ALPHA_DISPLAY_NAME ) . '</p>' .
							'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=alpha-setup-wizard&step=default_plugins' ) ) . '" target="_blank">' . esc_html__( 'Install Plugins', 'alpha' ) . '</a>'
					) . esc_html__( 'Archive Builder', 'alpha' ) . '</a><a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=alpha-layout-builder&layout=archive_post' ) ) . '" target="_blank">' . esc_html__( 'Layout Builder', 'alpha' ) . '</a>',
				),

				// Custom CSS, JS
				'custom_css'                    => array(
					'section'   => 'custom_css_js',
					'type'      => 'code',
					'label'     => esc_html__( 'CSS code', 'alpha' ),
					'transport' => 'postMessage',
					'choices'   => array(
						'language' => 'css',
						'theme'    => 'monokai',
					),
				),
				// Maintenance Mode
				'is_maintenance'                => array(
					'section' => 'maintenance',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Maintenance Mode', 'alpha' ),
					'tooltip' => esc_html__( 'This mode is for showing alternative page during the maintenance of the site.', 'alpha' ),
				),
				'maintenance_page'              => array(
					'section'         => 'maintenance',
					'type'            => 'select',
					'label'           => esc_html__( 'Select a Maintenance Page', 'alpha' ),
					'choices'         => Kirki_Helper::get_posts(
						array(
							'post_type'   => 'page',
							'post_status' => 'publish',
						)
					),
					'active_callback' => array(
						array(
							'setting'  => 'is_maintenance',
							'operator' => '==',
							'value'    => true,
						),
					),
				),
				// Custom Image Size
				'cs_image_quality_title'        => array(
					'section' => 'images',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Image Quality and Threshold', 'alpha' ) . '</h3>',
				),
				'image_quality'                 => array(
					'section' => 'images',
					'type'    => 'number',
					'label'   => esc_html__( 'Image Quality(%)', 'alpha' ),
					'tooltip' => esc_html__( 'Quality level between 0 (low) and 100 (high) of the JPEG. After changing this value, please install and run the Regenerate Thumbnails plugin once.', 'alpha' ),
					'choices' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'big_image_threshold'           => array(
					'section' => 'images',
					'type'    => 'number',
					'label'   => esc_html__( 'Big Image Size Threshold(px)', 'alpha' ),
					'tooltip' => esc_html__( 'Threshold for image height and width in pixels. WordPress will scale down newly uploaded images to this values as max-width or max-height. Set to "0" to disable the threshold completely.', 'alpha' ),
					'choices' => array(
						'min' => 0,
					),
				),
				'cs_image_size_title'           => array(
					'section' => 'images',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Custom Image Size', 'alpha' ) . '</h3>',
				),
				'custom_image_sizes'            => array(
					'section'   => 'images',
					'type'      => 'repeater',
					'row_label' => array(
						'type'  => 'field',
						'value' => esc_attr__( 'image size', 'alpha' ),
						'field' => 'size_name',
					),
					'fields'    => array(
						'size_name' => array(
							'type'  => 'text',
							'label' => esc_html__( 'Image Size Name', 'alpha' ),
						),
						'width'     => array(
							'type'  => 'number',
							'label' => esc_html__( 'Width (px)', 'alpha' ),
						),
						'height'    => array(
							'type'  => 'number',
							'label' => esc_html__( 'Height (px)', 'alpha' ),
						),
					),
				),
				// 'custom_image_size'             => array(
				// 	'section' => 'images',
				// 	'type'    => 'dimensions',
				// 	'label'   => esc_html__( 'Register Custom Image Size (px)', 'alpha' ),
				// 	'tooltip' => esc_html__( 'Don\'t forget to regenerate previously uploaded images.', 'alpha' ),
				// ),

				// Import/Export/Reset Options
				'cs_import_title'               => array(
					'section' => 'reset_options',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Import Options', 'alpha' ) . '</h3>',
				),
				'import_src'                    => array(
					'section'   => 'reset_options',
					'type'      => 'custom',
					'transport' => 'postMessage',
					'label'     => esc_html__( 'Please select source option file to import', 'alpha' ),
					'default'   => '<input type="file">',
				),
				'cs_import_option'              => array(
					'section' => 'reset_options',
					'type'    => 'custom',
					'default' => '<button name="import" id="alpha-import-options" class="button button-primary" disabled>' . esc_html__( 'Import', 'alpha' ) . '</button>',
				),
				'cs_export_title'               => array(
					'section' => 'reset_options',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Export Options', 'alpha' ) . '</h3>',
				),
				'cs_export_option'              => array(
					'section' => 'reset_options',
					'type'    => 'custom',
					'default' => '<p>' . esc_html__( 'Export theme options', 'alpha' ) . '</p><a href="' . esc_url( admin_url( 'admin-ajax.php?action=alpha_export_theme_options&wp_customize=on&nonce=' . wp_create_nonce( 'alpha-customizer' ) ) ) . '" name="export" id="alpha-export-options" class="button button-primary">' . esc_html__( 'Download Theme Options', 'alpha' ) . '</a>',
				),
				'cs_reset_title'                => array(
					'section' => 'reset_options',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Reset Options', 'alpha' ) . '</h3>',
				),
				'cs_reset_option'               => array(
					'section' => 'reset_options',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<button name="reset" id="alpha-reset-options" class="button button-primary">' . esc_html__( 'Reset Theme Options', 'alpha' ) . '</button>',
				),

				// SEO / Options
				'cs_nofollow_title'             => array(
					'section' => 'seo',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Use by search engines for ranking', 'alpha' ) . '</h3>',
				),
				'share_link_nofollow'           => array(
					'section' => 'seo',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Share &amp; Social Links', 'alpha' ),
					'tooltip' => esc_html__( 'Add "nofollow" attribute to social links for SEO.', 'alpha' ),
				),
				'menu_item_nofollow'            => array(
					'section' => 'seo',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Mobile Menu Items', 'alpha' ),
					'tooltip' => esc_html__( 'Add "nofollow" attribute to mobile menu items for SEO.', 'alpha' ),
				),

				//White Label / Options
				'cs_white_label_title'          => array(
					'section' => 'white_label',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'White Label', 'alpha' ) . '</h3>',
				),
				'white_label_title'             => array(
					'section' => 'white_label',
					'type'    => 'text',
					'label'   => esc_html__( 'White Label', 'alpha' ),
					'tooltip' => esc_html__( 'Theme name in AdminPanel', 'alpha' ),
				),
				'white_label_icon'              => array(
					'section' => 'white_label',
					'type'    => 'image',
					'label'   => esc_html__( 'White Icon', 'alpha' ),
					'tooltip' => esc_html__( 'Theme icon in Admin Menu and Admin Bar', 'alpha' ),
				),
				'white_label_logo'              => array(
					'section' => 'white_label',
					'type'    => 'image',
					'label'   => esc_html__( 'White Logo', 'alpha' ),
					'tooltip' => esc_html__( 'Theme logo in AdminPanel', 'alpha' ),
				),

				// Features / Social Login
				'cs_social_login_about_title'   => array(
					'section' => 'sociallogin',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title option-feature-title">' . esc_html__( 'About This Feature', 'alpha' ) . '</h3>',
				),
				'cs_social_login_about_desc'    => array(
					'section' => 'sociallogin',
					'type'    => 'custom',
					'label'   => esc_html__( 'With this feature, customers could be allowed to login your site with famous social site\'s user information.', 'alpha' ),
					'default' => '<p class="options-custom-description option-feature-description"><img class="description-image" src="' . ALPHA_ASSETS . '/images/admin/customizer/social-login.jpg' . '" alt="' . esc_html__( 'Theme Option Descrpition Image', 'alpha' ) . '"></p>',
				),
				'cs_social_login_title'         => array(
					'section' => 'sociallogin',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Social Login', 'alpha' ) . '</h3>',
				),
				'social_login'                  => array(
					'section' => 'sociallogin',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Enable Social Login', 'alpha' ),
					'tooltip' => esc_html__( 'Enable login by Nextend Social Login plugin.', 'alpha' ),
				),
				// Features / Ajax Filter
				'cs_archive_ajax_about_title'   => array(
					'section' => 'ajax_filter',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title option-feature-title">' . esc_html__( 'About This Feature', 'alpha' ) . '</h3>',
				),
				'cs_archive_ajax_about_desc'    => array(
					'section' => 'ajax_filter',
					'type'    => 'custom',
					'label'   => esc_html__( 'Make your page-speed faster than the others with modern ajax search feature.', 'alpha' ),
					'default' => '<p class="options-custom-description option-feature-description"><img class="description-image" src="' . ALPHA_ASSETS . '/images/admin/customizer/ajax-shop.jpg' . '" alt="' . esc_html__( 'Theme Option Descrpition Image', 'alpha' ) . '"></p>',
				),
				'cs_archive_ajax_title'         => array(
					'section' => 'ajax_filter',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Ajax Filter', 'alpha' ) . '</h3>',
				),
				'archive_ajax'                  => array(
					'type'    => 'toggle',
					'label'   => esc_html__( 'Enable Ajax Filter', 'alpha' ),
					'section' => 'ajax_filter',
				),

				// Features / Lazyload
				'cs_lazyload_about_title'       => array(
					'section' => 'lazyload',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title option-feature-title">' . esc_html__( 'About This Feature', 'alpha' ) . '</h3>',
				),
				'cs_lazyload_about_desc'        => array(
					'section' => 'lazyload',
					'type'    => 'custom',
					'label'   => esc_html__( 'All images will be lazyloaded when they come into screen.', 'alpha' ),
					'default' => '<p class="options-custom-description option-feature-description"><img class="description-image" src="' . ALPHA_ASSETS . '/images/admin/customizer/lazyload.jpg" alt="' . esc_html__( 'Theme Option Descrpition Image', 'alpha' ) . '"></p>',
				),
				'cs_lazyload_title'             => array(
					'section' => 'lazyload',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Lazy Load', 'alpha' ) . '</h3>',
				),
				'loading_animation'             => array(
					'section' => 'lazyload',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Loading Overlay', 'alpha' ),
					'tooltip' => esc_html__( 'Display overlay animation while loading.', 'alpha' ),
				),
				'skeleton_screen'               => array(
					'section' => 'lazyload',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Skeleton Screen', 'alpha' ),
					'tooltip' => esc_html__( 'Display the virtual area of each element on page while loading.', 'alpha' ),
				),
				'lazyload_menu'                 => array(
					'section' => 'lazyload',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Menu Lazyload', 'alpha' ),
					'tooltip' => esc_html__( 'Menus will be saved in browsers after lazyload.', 'alpha' ),
				),
				'lazyload'                      => array(
					'section' => 'lazyload',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Images Lazyload', 'alpha' ),
					'tooltip' => esc_html__( 'All images will be lazyloaded.', 'alpha' ),
				),
				'lazyload_bg'                   => array(
					'section'         => 'lazyload',
					'type'            => 'color',
					'label'           => esc_html__( 'Lazyload Image Initial Color', 'alpha' ),
					'choices'         => array(
						'alpha' => true,
					),
					'active_callback' => array(
						array(
							'setting'  => 'lazyload',
							'operator' => '==',
							'value'    => true,
						),
					),
				),

				// Features / Search
				'cs_search_about_title'         => array(
					'section' => 'search',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title option-feature-title">' . esc_html__( 'About This Feature', 'alpha' ) . '</h3>',
				),
				'cs_search_desc'                => array(
					'section' => 'search',
					'type'    => 'custom',
					'label'   => esc_html__( 'Without redirecting or entering search results page, you can get the results instantly and quickly.', 'alpha' ),
					'default' => '<p class="options-custom-description option-feature-description"><img class="description-image" src="' . ALPHA_ASSETS . '/images/admin/customizer/search.jpg' . '" alt="' . esc_html__( 'Theme Option Descrpition Image', 'alpha' ) . '"></p>',
				),
				'cs_search_title'               => array(
					'section' => 'search',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Search', 'alpha' ) . '</h3>',
				),
				'live_search'                   => array(
					'section' => 'search',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Live Search', 'alpha' ),
					'tooltip' => esc_html__( 'Search results will be displayed instantly.', 'alpha' ),
				),
				'live_relevanssi'               => array(
					'section'         => 'search',
					'type'            => 'toggle',
					'label'           => sprintf( esc_html__( 'Use %1$sRelevanssi%2$s for Live Search', 'alpha' ), '<a href="https://wordpress.org/plugins/relevanssi/" target="_blank">', '</a>'),
					'active_callback' => array(
						array(
							'setting'  => 'live_search',
							'operator' => '!=',
							'value'    => '',
						),
					),
				),
				'search_post_type'              => array(
					'section'         => 'search',
					'type'            => 'radio-buttonset',
					'transport'       => 'postMessage',
					'label'           => esc_html__( 'Search Post Type', 'alpha' ),
					'choices'         => apply_filters(
						'alpha_search_content_types',
						class_exists( 'WooCommerce' ) ? array(
							''        => esc_html__( 'All', 'alpha' ),
							'product' => esc_html__( 'Product', 'alpha' ),
							'post'    => esc_html__( 'Post', 'alpha' ),
						) : array(
							''     => esc_html__( 'All', 'alpha' ),
							'post' => esc_html__( 'Post', 'alpha' ),
						)
					),
					'active_callback' => array(
						array(
							'setting'  => 'live_search',
							'operator' => '!=',
							'value'    => '',
						),
					),
				),
			);

			if ( current_user_can( 'unfiltered_html' ) ) {
				$fields['custom_js'] = array(
					'section'   => 'custom_css_js',
					'type'      => 'code',
					'label'     => esc_html__( 'JS code', 'alpha' ),
					'transport' => 'postMessage',
					'choices'   => array(
						'language' => 'js',
						'theme'    => 'monokai',
					),
				);
			}

			if ( class_exists( 'WooCommerce' ) ) {

				$panels = array_merge(
					$panels,
					array(
						'woocommerce' => array(
							'title'    => esc_html__( 'WooCommerce', 'alpha' ),
							'priority' => 90,
						),
					)
				);

				$sections = array_merge(
					$sections,
					array(
						// Woocommerce
						'products_archive'     => array(
							'title'    => esc_html__( 'Shop', 'alpha' ),
							'panel'    => 'woocommerce',
							'priority' => 0,
						),
						'product_detail'       => array(
							'title'    => esc_html__( 'Single Product', 'alpha' ),
							'panel'    => 'woocommerce',
							'priority' => 0,
						),
						'product_type'         => array(
							'title'    => esc_html__( 'Product Type', 'alpha' ),
							'panel'    => 'woocommerce',
							'priority' => 0,
						),
						'woo_compare'          => array(
							'title'    => esc_html__( 'Compare', 'alpha' ),
							'panel'    => 'woocommerce',
							'priority' => 0,
						),

						// Product
						'product_instagram'    => array(
							'title'    => esc_html__( 'Instagram Photos', 'alpha' ),
							'panel'    => 'product',
							'priority' => 60,
						),
						// WooCommerce Panel
						'wc_cart'              => array(
							'title'    => esc_html__( 'Cart Page', 'alpha' ),
							'panel'    => 'woocommerce',
							'priority' => 20,
						),
						'woocommerce_checkout' => array(
							'title'    => esc_html__( 'Checkout', 'alpha' ),
							'panel'    => 'woocommerce',
							'priority' => 20,
						),
					)
				);

				$fields = array_merge(
					$fields,
					array(
						// Woocommerce / Shop
						'cs_shop_title'                 => array(
							'section' => 'products_archive',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Shop', 'alpha' ) . '</h3><p style="margin-bottom: 10px; cursor: auto;">' . esc_html__( 'Create your shop page template and show it in Layout Builder', 'alpha' ) . '</p>' .
								(
									class_exists( 'Alpha_Template_Shop_Builder' ) ?
									'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'edit.php?post_type=' . ALPHA_NAME . '_template&' . ALPHA_NAME . '_template_type=shop_layout' ) ) . '" target="_blank">' . esc_html__( 'Shop Builder', 'alpha' ) . '</a>' :
									'<p>' . sprintf( esc_html__( 'Please install %s Core Plugin', 'alpha' ), ALPHA_DISPLAY_NAME ) . '</p>' .
									'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=alpha-setup-wizard&step=default_plugins' ) ) . '" target="_blank">' . esc_html__( 'Install Plugins', 'alpha' ) . '</a>'
								) .
								'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=alpha-layout-builder&layout=archive_product' ) ) . '" target="_blank">' . esc_html__( 'Layout Builder', 'alpha' ) . '</a>',
						),

						// Woocommerce / Product Type
						'cs_product_type_title'         => array(
							'section'  => 'product_type',
							'type'     => 'custom',
							'default'  => '<h3 class="options-custom-title">' . esc_html__( 'Product Type', 'alpha' ) . '</h3>',
							'priority' => 5,
						),
						'hover_change'                  => array(
							'section'  => 'product_type',
							'type'     => 'toggle',
							'label'    => esc_html__( 'Change Image on Hover', 'alpha' ),
							'tooltip'  => esc_html__( 'Enable to show second product image when mouse enters.', 'alpha' ),
							'priority' => 5,
						),
						'prod_open_click_mob'           => array(
							'section'  => 'product_type',
							'type'     => 'toggle',
							'label'    => esc_html__( 'Open product on second click on mobile', 'alpha' ),
							'tooltip'  => esc_html__( 'Enable to navigate to product detail page on second click. First click would work as hover effect on mobile.', 'alpha' ),
							'priority' => 5,
						),

						'cs_product_quickview_title'    => array(
							'section'  => 'product_type',
							'type'     => 'custom',
							'label'    => '',
							'default'  => '<h3 class="options-custom-title">' . esc_html__( 'Quickview', 'alpha' ) . '</h3>
							<p>' . sprintf( esc_html__( 'You can customize quickview options in %1$sFeatures/Quickview%2$s panel.', 'alpha' ), '<b>', '</b>' ) . '</p>' .
							'<a class="button button-xlarge customizer-nav-item" style="margin-top: 0" data-target="quickview" data-type="section" href="#">' . esc_html__( 'Go to Quickview Options', 'alpha' ) . '</a>',
							'priority' => 20,
						),

						// Woocommerce / Compare
						'cs_woo_compare_advanced'       => array(
							'section' => 'woo_compare',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Compare', 'alpha' ) . '</h3>
							<p>' . sprintf( esc_html__( 'You can customize compare options in %1$sFeatures/Compare%2$s panel.', 'alpha' ), '<b>', '</b>' ) . '</p>' .
							'<a class="button button-outline button-xlarge customizer-nav-item" style="margin-top: 0" data-target="compare" data-type="section" href="#">' . esc_html__( 'Go to Compare Options', 'alpha' ) . '</a>',
						),

						// Features / Quickview
						'cs_shop_quickview_about_title' => array(
							'section' => 'quickview',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title option-feature-title">' . esc_html__( 'About This Feature', 'alpha' ) . '</h3>',
						),
						'cs_shop_quickview_desc'        => array(
							'section' => 'quickview',
							'type'    => 'custom',
							'label'   => esc_html__( 'Choose your favourite one of 3 quickview types - default, offcanvas or animate.', 'alpha' ),
							'default' => '<p class="options-custom-description option-feature-description"><img class="description-image" src="' . ALPHA_ASSETS . '/images/admin/customizer/quickview.jpg' . '" alt="' . esc_html__( 'Theme Option Descrpition Image', 'alpha' ) . '"></p>',
						),
						'cs_quickview_title'            => array(
							'section' => 'quickview',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Quickview', 'alpha' ) . '</h3>',
						),
						'quickview_type'                => array(
							'section' => 'quickview',
							'type'    => 'radio-image',
							'label'   => esc_html__( 'Quickview Type', 'alpha' ),
							'choices' => array(
								''          => ALPHA_ASSETS . '/images/options/quickview-popup.jpg',
								'zoom'      => ALPHA_ASSETS . '/images/options/quickview-zoom.jpg',
								'offcanvas' => ALPHA_ASSETS . '/images/options/quickview-offcanvas.jpg',
							),
						),
						'quickview_thumbs'              => array(
							'section'         => 'quickview',
							'type'            => 'radio-image',
							'label'           => esc_html__( 'Thumbnails Position', 'alpha' ),
							'choices'         => array(
								'vertical'   => ALPHA_ASSETS . '/images/options/quickview1.png',
								'horizontal' => ALPHA_ASSETS . '/images/options/quickview2.png',
							),
							'active_callback' => array(
								array(
									'setting'  => 'quickview_type',
									'operator' => '!=',
									'value'    => 'offcanvas',
								),
							),
						),

						// Woocommerce / Single Product
						'cs_sp_title'                   => array(
							'section' => 'product_detail',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Single Product', 'alpha' ) . '</h3><p style="margin-bottom: 10px; cursor: auto;">' . esc_html__( 'Create your single product page template and show it in Layout Builder', 'alpha' ) . '</p>' .
								(
									class_exists( 'Alpha_Single_Product_Builder' ) ?
									'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'edit.php?post_type=' . ALPHA_NAME . '_template&' . ALPHA_NAME . '_template_type=product_layout' ) ) . '" target="_blank">' . esc_html__( 'Product Builder', 'alpha' ) . '</a>' :
									'<p>' . sprintf( esc_html__( 'Please install %s Core Plugin', 'alpha' ), ALPHA_DISPLAY_NAME ) . '</p>' .
									'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=alpha-setup-wizard&step=default_plugins' ) ) . '" target="_blank">' . esc_html__( 'Install Plugins', 'alpha' ) . '</a>'
								) .
								'<a class="button button-outline button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=alpha-layout-builder&layout=single_product' ) ) . '" target="_blank">' . esc_html__( 'Layout Builder', 'alpha' ) . '</a>',
						),

						// Product Page / Product Data / Custom Tab
						'cs_product_data'               => array(
							'section'  => 'product_detail',
							'type'     => 'custom',
							'label'    => '',
							'default'  => '<h3 class="options-custom-title">' . esc_html__( 'Product Data Tab', 'alpha' ) . '</h3>',
							'priority' => 10,
						),
						'product_description_title'     => array(
							'section'   => 'product_detail',
							'type'      => 'text',
							'label'     => esc_html__( 'Description Title', 'alpha' ),
							'transport' => 'postMessage',
							'priority'  => 10,
						),
						'product_specification_title'   => array(
							'section'     => 'product_detail',
							'type'        => 'text',
							'label'       => esc_html__( 'Specification Title', 'alpha' ),
							'placeholder' => esc_html__( 'Specification', 'alpha' ),
							'transport'   => 'postMessage',
							'priority'    => 10,
						),
						'product_reviews_title'         => array(
							'section'     => 'product_detail',
							'type'        => 'text',
							'label'       => esc_html__( 'Reviews Title', 'alpha' ),
							'placeholder' => esc_html__( 'Customer Reviews', 'alpha' ),
							'transport'   => 'postMessage',
							'priority'    => 10,
						),

						// Product Page / Product Data / Custom Tab
						'cs_product_custom_tab'         => array(
							'section'  => 'product_detail',
							'type'     => 'custom',
							'label'    => '',
							'default'  => '<h3 class="options-custom-title">' . esc_html__( 'Custom Tab', 'alpha' ) . '</h3>',
							'priority' => 15,
						),
						'product_tab_title'             => array(
							'section'   => 'product_detail',
							'type'      => 'text',
							'label'     => esc_html__( 'Custom Tab Title', 'alpha' ),
							'tooltip'   => esc_html__( 'Show custom tab in all product pages.', 'alpha' ),
							'transport' => 'postMessage',
							'priority'  => 15,
						),
						'product_tab_block'             => array(
							'section'  => 'product_detail',
							'type'     => 'select',
							'label'    => esc_html__( 'Custom Tab Content ( Block Builder )', 'alpha' ),
							'choices'  => empty( $alpha_templates['block'] ) ? array() : $custom_tab_block,
							'priority' => 15,
						),

						// WooCommerce Panel
						'cart_show_qty'                 => array(
							'section' => 'wc_cart',
							'type'    => 'toggle',
							'label'   => esc_html__( 'Quantity Input in Mini Cart', 'alpha' ),
							'tooltip' => esc_html__( 'Show quantity input in mini cart list.', 'alpha' ),
						),
					)
				);
			}

			/**
			 * Filters the customize panels.
			 *
			 * @since 1.0
			 */
			$panels = apply_filters( 'alpha_customize_panels', $panels );
			foreach ( $panels as $panel => $settings ) {
				Kirki::add_panel( $panel, $settings );
			}

			/**
			 * Filters the customize sections.
			 *
			 * @since 1.0
			 */
			$sections = apply_filters( 'alpha_customize_sections', $sections );
			foreach ( $sections as $section => $settings ) {
				Kirki::add_section( $section, $settings );
			}

			/**
			 * Filters the customize fields.
			 *
			 * @since 1.0
			 */
			$fields = apply_filters( 'alpha_customize_fields', $fields );
			foreach ( $fields as $field => $settings ) {
				if ( ! isset( $settings['default'] ) ) {
					$settings['default'] = alpha_get_option( $field );
				}
				$settings['settings'] = $field;
				Kirki::add_field( 'option', $settings );
			}
		}

		public function selective_refresh( $customize ) {
			$customize->selective_refresh->add_partial(
				'selective-gdpr',
				array(
					'selector'            => '.cookies-popup',
					'settings'            => array( 'cookie_text', 'cookie_agree_btn' ),
					'container_inclusive' => true,
					'render_callback'     => function() {
						if ( class_exists( 'Alpha_GDPR' ) ) {
							Alpha_GDPR::get_instance()->print_cookie_popup( true );
						}
					},
				)
			);
			// @start feature: fs_plugin_woocommerce
			if ( class_exists( 'WooCommerce' ) ) {
				$customize->selective_refresh->add_partial(
					'selective-breadcrumb',
					array(
						'selector'            => '.breadcrumb-container',
						'settings'            => array( 'ptb_delimiter' ),
						'container_inclusive' => true,
						'render_callback'     => function() {
							alpha_breadcrumb();
						},
					)
				);
			}
			// @end feature: fs_plugin_woocommerce
		}

		// @start feature: fs_plugin_woocommerce
		/**
		 * Change placeholder image for product
		 *
		 * @since 1.0
		 */
		public function update_woocommerce_placeholder_image( $value, $old_value ) {
			update_option( 'woocommerce_placeholder_image', $value );
			return $value;
		}
		// @end feature: fs_plugin_woocommerce
	}
endif;

if ( class_exists( 'Kirki' ) ) {
	Alpha_Customizer::get_instance();
}
