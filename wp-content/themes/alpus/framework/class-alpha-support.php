<?php
/**
 * Alpha Theme Class
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

class Alpha_Support {

	/**
	 * Constructor
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {
		// Setup theme
		add_action( 'after_setup_theme', array( $this, 'setup' ) );
		add_action( 'widgets_init', array( $this, 'setup_sidebars' ) );
	}

	/**
	 * Register nav menus, support, image sizes
	 *
	 * @since 1.0
	 * @access public
	 */
	public function setup() {

		load_theme_textdomain( 'alpha', ALPHA_PATH . '/languages' );
		load_child_theme_textdomain( 'alpha', get_theme_file_path() . '/languages' );

		// Regitster nav menus
		register_nav_menus(
			apply_filters(
				'alpha_theme_nav_menus',
				array(
					'cur-switcher'  => esc_html__( 'Currency Switcher', 'alpha' ),
					'lang-switcher' => esc_html__( 'Language Switcher', 'alpha' ),
					'main-menu'     => esc_html__( 'Main Menu', 'alpha' ),
				)
			)
		);

		// support WordPress
		add_theme_support( 'title-tag' );
		add_theme_support( 'custom-background', array() );
		add_theme_support( 'custom-header', array() );
		add_theme_support( 'custom-logo', array() );

		// support gutenberg
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );

		// support woocommerce
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-lightbox' );

		// default rss feed links
		add_theme_support( 'automatic-feed-links' );

		// add support for post thumbnails
		add_theme_support( 'post-thumbnails' );

		// add support for post formats
		add_theme_support( 'post-formats', array( 'video' ) );

		// switch default core markup for search form, comment form, and comments to output valid HTML5.
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'script',
				'style',
			)
		);

		// add editor custom font sizes
		add_theme_support(
			'editor-font-sizes',
			array(
				array(
					'name'      => esc_html__( 'Small', 'alpha' ),
					'shortName' => esc_html__( 'S', 'alpha' ),
					'size'      => 15,
					'slug'      => 'small',
				),
				array(
					'name'      => esc_html__( 'Normal', 'alpha' ),
					'shortName' => esc_html__( 'N', 'alpha' ),
					'size'      => 18,
					'slug'      => 'normal',
				),
				array(
					'name'      => esc_html__( 'Medium', 'alpha' ),
					'shortName' => esc_html__( 'M', 'alpha' ),
					'size'      => 24,
					'slug'      => 'medium',
				),
				array(
					'name'      => esc_html__( 'Large', 'alpha' ),
					'shortName' => esc_html__( 'L', 'alpha' ),
					'size'      => 30,
					'slug'      => 'large',
				),
				array(
					'name'      => esc_html__( 'Huge', 'alpha' ),
					'shortName' => esc_html__( 'huge', 'alpha' ),
					'size'      => 34,
					'slug'      => 'huge',
				),
			)
		);

		// editor color palette
		add_theme_support(
			'editor-color-palette',
			array(
				array(
					'name'  => esc_html__( 'Primary', 'alpha' ),
					'slug'  => 'primary',
					'color' => alpha_get_option( 'primary_color' ),
				),
				array(
					'name'  => esc_html__( 'Secondary', 'alpha' ),
					'slug'  => 'secondary',
					'color' => alpha_get_option( 'secondary_color' ),
				),
				array(
					'name'  => esc_html__( 'Alert', 'alpha' ),
					'slug'  => 'alert',
					'color' => alpha_get_option( 'alert_color' ),
				),
				array(
					'name'  => esc_html__( 'Dark', 'alpha' ),
					'slug'  => 'dark',
					'color' => '#333',
				),
				array(
					'name'  => esc_html__( 'White', 'alpha' ),
					'slug'  => 'white',
					'color' => '#fff',
				),
				array(
					'name'  => esc_html__( 'Default Font Color', 'alpha' ),
					'slug'  => 'font',
					'color' => '#666',
				),
				array(
					'name'  => esc_html__( 'Transparent', 'alpha' ),
					'slug'  => 'transparent',
					'color' => 'transparent',
				),
			)
		);

		add_editor_style();

		/**
		 * Filters the image sizes.
		 *
		 * @since 1.0
		 */
		$image_sizes = apply_filters(
			'alpha_image_sizes',
			array(
				'alpha-post-medium'       => array(
					'width'  => 600,
					'height' => 420,
					'crop'   => true,
				),
				'alpha-post-small'        => array(
					'width'  => 400,
					'height' => 280,
					'crop'   => true,
				),
				'alpha-product-thumbnail' => array(
					'width'  => 150,
					'height' => 0,
					'crop'   => true,
				),
			)
		);

		$sizes = alpha_get_option( 'custom_image_sizes' );
		if ( is_array( $sizes ) ) {
			$old_size = alpha_get_option( 'custom_image_size' );
			if ( ! empty( $old_size['Width'] ) && ! empty( $old_size['Height'] ) ) {
				$sizes[] = array(
					'size_name' => esc_html__( 'Alpha Custom', 'alpha' ),
					'width'     => $old_size['Width'],
					'height'    => $old_size['Height'],
				);
				set_theme_mod( 'custom_image_sizes', $sizes );
				remove_theme_mod( 'custom_image_size' );
			}
			foreach ( $sizes as $size ) {
				if ( ! empty( $size['size_name'] ) && ! empty( $size['width'] ) && ! empty( $size['height'] ) ) {
					$image_sizes[ $size['size_name'] ] = array(
						'width'  => (int) $size['width'],
						'height' => (int) $size['height'],
						'crop'   => true,
					);
				}
			}
		}

		foreach ( $image_sizes as $image => $size ) {
			add_image_size( $image, $size['width'], $size['height'], $size['crop'] );
		}

		// Content Width
		if ( ! isset( $content_width ) ) {
			$content_width = 1240;
		}
	}

	/**
	 * Add Widget Areas ( Sidebar )
	 *
	 * @since 1.0
	 * @access public
	 */
	public function setup_sidebars() {

		$sidebars                 = array();
		$sidebars['blog-sidebar'] = array(
			'name'          => esc_html__( 'Blog Sidebar', 'alpha' ),
			'before_widget' => '<nav id="%1$s" class="widget %2$s widget-collapsible">',
			'after_widget'  => '</nav>',
			'before_title'  => '<h3 class="widget-title"><span class="wt-area">',
			'after_title'   => '</span></h3>',
		);
		// @start feature: fs_plugin_woocommerce
		if ( class_exists( 'WooCommerce' ) ) {
			$sidebars['shop-sidebar']    = array(
				'name'          => esc_html__( 'Shop Sidebar', 'alpha' ),
				'before_widget' => '<nav id="%1$s" class="widget %2$s widget-collapsible">',
				'after_widget'  => '</nav>',
				'before_title'  => '<h3 class="widget-title"><span class="wt-area">',
				'after_title'   => '</span></h3>',
			);
			$sidebars['product-sidebar'] = array(
				'name'          => esc_html__( 'Product Sidebar', 'alpha' ),
				'before_widget' => '<nav id="%1$s" class="widget %2$s widget-collapsible">',
				'after_widget'  => '</nav>',
				'before_title'  => '<h3 class="widget-title"><span class="wt-area">',
				'after_title'   => '</span></h3>',
			);
		}
		// @end feature: fs_plugin_woocommerce

		/**
		 * Filters the sidebars.
		 *
		 * @param array $sidebars The sidebar array.
		 * @since 1.0
		 */
		$sidebars = apply_filters( 'alpha_sidebars', $sidebars );

		foreach ( $sidebars as $id => $sidebar ) {
			$sidebar['id'] = $id;
			register_sidebar( $sidebar );
		}

		// Extra sidebars

		$extra_sidebars = json_decode( get_option( 'alpha_sidebars', '[]' ), true );

		if ( $extra_sidebars ) {
			foreach ( $extra_sidebars as $slug => $name ) {
				register_sidebar(
					array(
						'name'          => sprintf( '%s', $name ),
						'id'            => $slug,
						'before_widget' => '<nav id="%1$s" class="widget %2$s">',
						'after_widget'  => '</nav>',
						'before_title'  => '<h3 class="widget-title">',
						'after_title'   => '</h3>',
					)
				);
			}
		}
	}
}

new Alpha_Support;
