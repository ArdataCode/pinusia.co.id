<?php
/**
 * Default Theme Options
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 *
 * @var array $alpha_option
 */
defined( 'ABSPATH' ) || die;

$default_conditions = array(
	'site'            => array(
		array(
			'title' => esc_html__( 'Global Layout', 'alpha' ),
		),
	),
	'archive_product' => array(
		array(
			'title'   => esc_html__( 'Shop Page Layout', 'alpha' ),
			'scheme'  => array(
				'all' => true,
			),
			'options' => array(
				'left_sidebar'    => 'shop-sidebar',
				'ptb'             => 'hide',
				'show_breadcrumb' => 'yes',
			),
		),
	),
	'single_product'  => array(
		array(
			'title'   => esc_html__( 'Single Product Layout', 'alpha' ),
			'scheme'  => array(
				'all' => true,
			),
			'options' => array(
				'right_sidebar'   => 'product-sidebar',
				'ptb'             => 'hide',
				'show_breadcrumb' => 'yes',
			),
		),
	),
	'archive_post'    => array(
		array(
			'title'   => esc_html__( 'Blog Page Layout', 'alpha' ),
			'scheme'  => array(
				'all' => true,
			),
			'options' => array(
				'right_sidebar' => 'blog-sidebar',
			),
		),
	),
	'single_post'     => array(
		array(
			'title'   => esc_html__( 'Single Post Layout', 'alpha' ),
			'scheme'  => array(
				'all' => true,
			),
			'options' => array(
				'right_sidebar' => 'blog-sidebar',
			),
		),
	),
	'error'           => array(
		array(
			'title'   => esc_html__( '404 Page Layout', 'alpha' ),
			'options' => array(
				'wrap' => 'full',
				'ptb'  => 'hide',
			),
		),
	),
);

$alpha_option = array(
	// Navigator
	'navigator_items'              => array(
		'custom_css_js'  => array( esc_html__( 'Style / Additional CSS & Script', 'alpha' ), 'section' ),
		'color'          => array( esc_html__( 'Color', 'alpha' ), 'section' ),
		'blog'           => array( esc_html__( 'Blog', 'alpha' ), 'section' ),
		'product_type'   => array( esc_html__( 'Shop / Product Type', 'alpha' ), 'section' ),
		'product_detail' => array( esc_html__( 'WooCommerce / Product Page', 'alpha' ), 'section' ),
		'lazyload'       => array( esc_html__( 'Advanced / Lazy Load', 'alpha' ), 'section' ),
		'search'         => array( esc_html__( 'Advanced / Search', 'alpha' ), 'section' ),
	),

	// Conditions
	'conditions'                   => $default_conditions,

	// General
	'site_type'                    => 'full',
	'site_width'                   => '1400',
	'site_gap'                     => '20',
	'content_bg'                   => array(
		'background-color' => '#fff',
	),
	'site_bg'                      => array(
		'background-color' => '#fff',
	),
	'container'                    => '1280',
	'container_fluid'              => '1820',

	// Colors
	'primary_color'                => '#08c',
	'secondary_color'              => '#f93',
	'dark_color'                   => '#333',
	'light_color'                  => '#ccc',
	'white_color'                  => '#fff',

	// Typography
	'typo_default'                 => array(
		'font-family'    => 'Poppins',
		'variant'        => '400',
		'font-size'      => '14px',
		'line-height'    => '1.6',
		'letter-spacing' => '',
		'color'          => '#666',
	),
	'typo_heading'                 => array(
		'font-family'    => 'inherit',
		'variant'        => '600',
		'line-height'    => '1.2',
		'letter-spacing' => '-0.025em',
		'text-transform' => 'none',
		'color'          => '#333',
	),
	'typo_custom1'                 => array(
		'font-family' => 'inherit',
	),
	'typo_custom2'                 => array(
		'font-family' => 'inherit',
	),
	'typo_custom3'                 => array(
		'font-family' => 'inherit',
	),

	// Page Title Bar
	'ptb_height'                   => '180',
	'ptb_bg'                       => array(
		'background-color' => '#eee',
	),
	'ptb_delimiter'                => '>',
	'typo_ptb_title'               => array(
		'font-family'    => 'inherit',
		'variant'        => '600',
		'font-size'      => '34px',
		'line-height'    => '1.125',
		'letter-spacing' => '-0.025em',
		'text-transform' => 'capitalize',
		'color'          => '#333',
	),
	'typo_ptb_subtitle'            => array(
		'font-family'    => 'inherit',
		'variant'        => '',
		'font-size'      => '18px',
		'line-height'    => '1.8',
		'letter-spacing' => '',
		'color'          => '#666',
	),
	'typo_ptb_breadcrumb'          => array(
		'font-family'    => 'inherit',
		'font-size'      => '13px',
		'line-height'    => '',
		'letter-spacing' => '',
		'text-transform' => '',
		'color'          => '#333',
	),

	// Menu
	'menu_labels'                  => '',
	'mobile_menu_items'            => array(),

	// Mobile Sticky Icon Bar
	'mobile_bar_icons'             => array( 'home', 'shop', 'account', 'cart', 'search' ),
	'mobile_bar_menu_label'        => esc_html__( 'Menu', 'alpha' ),
	'mobile_bar_menu_icon'         => ALPHA_ICON_PREFIX . '-icon-bars',
	'mobile_bar_home_label'        => esc_html__( 'Home', 'alpha' ),
	'mobile_bar_home_icon'         => ALPHA_ICON_PREFIX . '-icon-home',
	'mobile_bar_shop_label'        => esc_html__( 'Categories', 'alpha' ),
	'mobile_bar_shop_icon'         => ALPHA_ICON_PREFIX . '-icon-category',
	'mobile_bar_wishlist_label'    => esc_html__( 'Wishlist', 'alpha' ),
	'mobile_bar_wishlist_icon'     => ALPHA_ICON_PREFIX . '-icon-heart',
	'mobile_bar_account_label'     => esc_html__( 'Account', 'alpha' ),
	'mobile_bar_account_icon'      => ALPHA_ICON_PREFIX . '-icon-account',
	'mobile_bar_cart_label'        => esc_html__( 'Cart', 'alpha' ),
	'mobile_bar_cart_icon'         => ALPHA_ICON_PREFIX . '-icon-cart',
	'mobile_bar_search_label'      => esc_html__( 'Search', 'alpha' ),
	'mobile_bar_search_icon'       => ALPHA_ICON_PREFIX . '-icon-search',
	'mobile_bar_top_label'         => esc_html__( 'To Top', 'alpha' ),
	'mobile_bar_top_icon'          => ALPHA_ICON_PREFIX . '-icon-long-arrow-up',

	'social_login'                 => true,

	// Single Product
	'product_description_title'    => esc_html__( 'Description', 'alpha' ),
	'product_specification_title'  => esc_html__( 'Specification', 'alpha' ),
	'product_reviews_title'        => esc_html__( 'Customer Reviews', 'alpha' ),
	'show_buy_now_btn'             => false,
	'buy_now_text'                 => esc_html__( 'Buy Now', 'alpha' ),
	'product_more_title'           => esc_html__( 'More Products From This Vendor', 'alpha' ),

	// Shop Advanced
	'new_product_period'           => 7,
	'hover_change'                 => true,
	'prod_open_click_mob'          => true,
	'catalog_price'                => true,
	'catalog_cart'                 => false,
	'catalog_review'               => false,

	// Shop / Product Type
	'product_type'                 => '',
	'classic_hover'                => '',
	'quickview_type'               => '',
	'quickview_thumbs'             => 'horizontal',

	// Features
	'archive_ajax'                 => true,

	// Advanced / Lazyload
	'skeleton_screen'              => false,
	'lazyload'                     => false,
	'lazyload_bg'                  => '#f4f4f4',
	'loading_animation'            => false,

	// Advanced / Search
	'live_search'                  => true,
	'search_post_type'             => '',
	'sales_popup'                  => '',
	'sales_popup_title'            => esc_html__( 'Someone Purchased', 'alpha' ),
	'sales_popup_count'            => 5,
	'sales_popup_start_delay'      => 60,
	'sales_popup_interval'         => 60,
	'sales_popup_category'         => '',
	'sales_popup_mobile'           => true,
	'custom_image_sizes'           => array(
		'size_name' => '',
		'width'     => '',
		'height'    => '',
	),
	'image_quality'                => 82,
	'big_image_threshold'          => 2560,

	// optimize wizard
	'font_face_display'            => false,
	'google_webfont'               => false,
	'lazyload_menu'                => false,
	'menu_last_time'               => 0,
	'mobile_disable_slider'        => false,
	'mobile_disable_animation'     => false,

	'preload_fonts'                => array( 'alpha', 'fas', 'fab' ),
	'resource_disable_gutenberg'   => false,
	'resource_disable_wc_blocks'   => false,
	'resource_disable_elementor'   => false,
	'resource_disable_emojis'      => false,
	'resource_disable_jq_migrate'  => false,
	'resource_disable_rev'         => false,
	'resource_jquery_footer'       => false,
	'resource_merge_stylesheets'   => false,
	'resource_critical_css'        => false,
	'resource_template_builders'   => '',

	'resource_async_js'            => true,
	'resource_split_tasks'         => true,
	'resource_after_load'          => true,

	// Custom CSS & JS
	'custom_css'                   => '',
	'custom_js'                    => '',

	'is_maintenance'               => false,
	'maintenance_page'             => '',
	// layouts
	'layout_default_wrap'          => 'container',
	'archive_layout_right_sidebar' => 'blog-sidebar',
	'single_layout_right_sidebar'  => 'blog-sidebar',
	'shop_layout_left_sidebar'     => 'shop-sidebar',
	'error_layout_wrap'            => 'full',
	'error_layout_ptb'             => 'hide',
);

$alpha_option['menu_labels'] = json_encode(
	array(
		'new' => get_theme_mod( 'primary_color', $alpha_option['primary_color'] ),
		'hot' => get_theme_mod( 'secondary_color', $alpha_option['secondary_color'] ),
	)
);

$social_shares = alpha_get_social_shares();

foreach ( $social_shares as $key => $data ) {
	$alpha_option[ 'social_addr_' . $key ] = '';
}

/**
 * Filters default values of theme options.
 *
 * @since 1.0
 */
$alpha_option = apply_filters( 'alpha_theme_option_default_values', $alpha_option );

/**
 * Fires after setting default options.
 *
 * Here you can change default options, add or remove.
 *
 * @since 1.0
 */
do_action( 'alpha_after_default_options' );
