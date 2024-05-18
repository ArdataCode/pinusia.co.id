<?php
/**
 * Customizer
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 *
 */
defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Alpha_Customizer_Extend' ) ) {
	class Alpha_Customizer_Extend extends Alpha_Base {

		/**
		 * Constructor
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'load_selective_assets' ) );

			if ( ! defined( 'ALPHA_PRO_VERSION' ) ) {
				add_action( 'customize_register', array( $this, 'add_pro_sections' ) );
			}

			add_action( 'customize_controls_print_styles', array( $this, 'load_extend_style' ), 30 );
			add_filter( 'alpha_customize_sections', array( $this, 'customize_sections_extend' ), 20 );
			add_filter( 'alpha_customize_fields', array( $this, 'customize_fields_extend' ), 20 );
		}

		/**
		 * load selective refresh JS
		 *
		 * @since 1.0
		 */
		public function load_selective_assets() {
			wp_enqueue_script( 'alpha-selective-extend', ALPHA_INC_URI . '/admin/customizer/selective-refresh-extend' . ALPHA_JS_SUFFIX, array( 'jquery-core', 'alpha-selective' ), ALPHA_VERSION, true );
		}

		/**
		 * Add CSS for Customizer Options
		 *
		 * @since 1.0
		 */
		public function load_extend_style() {
			wp_enqueue_style( 'alpha-customizer-extend', ALPHA_INC_URI . '/admin/customizer/customizer-extend' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), ALPHA_VERSION, 'all' );
		}

		/**
		 * Remove or add sections in theme option.
		 *
		 * @since 1.0
		 */
		public function customize_sections_extend( $sections ) {
			// remove sections
			if ( ! defined( 'ALPHA_PRO_VERSION' ) ) {
				$sections = array_diff_key(
					$sections,
					array(
						'blog'             => false,
						'products_archive' => false,
						'wc_cart'          => false,
						'ajax_filter'      => false,
						'product_buy_now'  => false,
						'lazyload'         => false,
						'search'           => false,
						'reset_options'    => false,
						'seo'              => false,
						'white_label'      => false,
					)
				);
			}
			$sections['color']['title'] = esc_html__( 'Skin & Color', 'alpha' );
			unset( $sections['maintenance'] );

			return $sections;
		}

		/**
		 * Remove or add fields in theme option.
		 *
		 * @since 1.0
		 */
		public function customize_fields_extend( $fields ) {
			// add dummy field options
			if ( ! defined( 'ALPHA_PRO_VERSION' ) ) {
				$fields['go_pro_control']  = array(
					'section' => 'alpha_upsell',
					'type'    => 'hidden',
				);
				$fields['go_pro_control2'] = array(
					'section' => 'custom_cursor',
					'type'    => 'hidden',
				);
				$fields['go_pro_control3'] = array(
					'section' => 'ai_generator',
					'type'    => 'hidden',
				);
				$fields['cs_sp_title']     = array();
			}

			$fields = array_merge(
				$fields,
				array(
					// Style / Typography
					'cs_typo_default_font'  => array(
						'section'  => 'typo',
						'type'     => 'custom',
						'default'  => '<h3 class="options-custom-title">' . esc_html__( 'Default Typography', 'alpha' ) . '</h3>',
						'priority' => 5,
					),
					'typo_default'          => array(
						'section'   => 'typo',
						'type'      => 'typography',
						'label'     => '',
						'choices'   => apply_filters( 'alpha_kirki_typo_control_choices', array() ),
						'transport' => 'postMessage',
						'priority'  => 5,
					),
					'cs_typo_heading'       => array(
						'section'  => 'typo',
						'type'     => 'custom',
						'default'  => '<h3 class="options-custom-title">' . esc_html__( 'Heading Typography', 'alpha' ) . '</h3>',
						'priority' => 5,
					),
					'typo_heading'          => array(
						'section'   => 'typo',
						'type'      => 'typography',
						'label'     => '',
						'choices'   => apply_filters( 'alpha_kirki_typo_control_choices', array() ),
						'transport' => 'postMessage',
						'priority'  => 5,
					),
					'success_color'         => array(
						'section'   => 'color',
						'type'      => 'color',
						'label'     => esc_html__( 'Success Color', 'alpha' ),
						'choices'   => array(
							'alpha' => true,
						),
						'transport' => 'postMessage',
					),
					'alert_color'           => array(
						'section'   => 'color',
						'type'      => 'color',
						'label'     => esc_html__( 'Warning Color', 'alpha' ),
						'choices'   => array(
							'alpha' => true,
						),
						'transport' => 'postMessage',
					),
					'danger_color'          => array(
						'section'   => 'color',
						'type'      => 'color',
						'label'     => esc_html__( 'Danger Color', 'alpha' ),
						'choices'   => array(
							'alpha' => true,
						),
						'transport' => 'postMessage',
					),
					'typo_h1_size'          => array(
						'section'   => 'typo',
						'type'      => 'text',
						'label'     => esc_html__( 'H1 Font Size', 'alpha' ),
						'transport' => 'postMessage',
						'priority'  => 5,
					),
					'typo_h2_size'          => array(
						'section'   => 'typo',
						'type'      => 'text',
						'label'     => esc_html__( 'H2 Font Size', 'alpha' ),
						'transport' => 'postMessage',
						'priority'  => 5,
					),
					'typo_h3_size'          => array(
						'section'   => 'typo',
						'type'      => 'text',
						'label'     => esc_html__( 'H3 Font Size', 'alpha' ),
						'transport' => 'postMessage',
						'priority'  => 5,
					),
					'typo_h4_size'          => array(
						'section'   => 'typo',
						'type'      => 'text',
						'label'     => esc_html__( 'H4 Font Size', 'alpha' ),
						'transport' => 'postMessage',
						'priority'  => 5,
					),
					'typo_h5_size'          => array(
						'section'   => 'typo',
						'type'      => 'text',
						'label'     => esc_html__( 'H5 Font Size', 'alpha' ),
						'transport' => 'postMessage',
						'priority'  => 5,
					),
					'typo_h6_size'          => array(
						'section'   => 'typo',
						'type'      => 'text',
						'label'     => esc_html__( 'H6 Font Size', 'alpha' ),
						'transport' => 'postMessage',
						'priority'  => 5,
					),
					'cs_typo_google_title'  => array(
						'section'  => 'typo',
						'type'     => 'custom',
						'default'  => '<h3 class="options-custom-title">' . esc_html__( 'Google Fonts', 'alpha' ) . '</h3>',
						'priority' => 8,
					),
					'cs_typo_google_desc'   => array(
						'section'  => 'typo',
						'type'     => 'custom',
						'default'  => '<p style="margin: 0;">' . esc_html__( 'Select google fonts use throughout the site.', 'alpha' ) . '</p>',
						'priority' => 8,
					),
					'typo_custom_part'      => array(
						'section'   => 'typo',
						'type'      => 'radio-buttonset',
						'default'   => '1',
						'transport' => 'postMessage',
						'choices'   => array(
							'1' => esc_html__( 'Font 1', 'alpha' ),
							'2' => esc_html__( 'Font 2', 'alpha' ),
							'3' => esc_html__( 'Font 3', 'alpha' ),
						),
						'priority'  => 8,
					),
					'typo_custom1'          => array(
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
						'priority'        => 8,
					),
					'typo_custom2'          => array(
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
						'priority'        => 8,
					),
					'typo_custom3'          => array(
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
						'priority'        => 8,
					),
					
					// remove field options
					'cs_product_custom_tab' => false,
					'product_tab_title'     => false,
					'product_tab_block'     => false,
					'quickview_type'        => false,
				)
			);

			if ( class_exists( 'WooCommerce' ) ) {
				$fields = array_merge(
					$fields,
					array(
						'cs_shop_quickview_desc'        => array(
							'section' => 'quickview',
							'type'    => 'custom',
							'label'   => sprintf( esc_html__( '3 quickview types are available in %1$sAlpus Pro%2$s - default, offcanvas or animate.', 'alpha' ), '<a href="' . ALPHA_GET_PRO_URI . '" target="_blank">', '</a>' ),
							'default' => '<p class="options-custom-description option-feature-description"><img class="description-image" src="' . ALPHA_ASSETS . '/images/admin/customizer/quickview.jpg' . '" alt="' . esc_html__( 'Theme Option Descrpition Image', 'alpha' ) . '"></p>',
						),
					)
				);
			}

			return $fields;
		}

		public function add_pro_sections( $manager ) {
			// Load custom sections.
			require_once ALPHA_INC_PATH . '/admin/customizer/class-alpha-customizer-pro-section.php';

			// Register custom section types.
			$manager->register_section_type( 'Alpha_Customizer_Pro_Section' );

			// Register sections.
			$manager->add_section(
				new Alpha_Customizer_Pro_Section(
					$manager,
					'alpha_upsell',
					array(
						'title'    => esc_html__( 'More Options Available in Alpus Pro', 'alpha' ),
						'pro_url'  => ALPHA_GET_PRO_URI,
						'priority' => -999,
					)
				)
			);
			$manager->add_section(
				new Alpha_Customizer_Pro_Section(
					$manager,
					'blog',
					array(
						'title'    => esc_html__( 'Blog', 'alpha' ),
						'pro_url'  => ALPHA_GET_PRO_URI,
						'priority' => 50,
					)
				)
			);
			$manager->add_section(
				new Alpha_Customizer_Pro_Section(
					$manager,
					'products_archive',
					array(
						'title'    => esc_html__( 'Shop', 'alpha' ),
						'pro_url'  => ALPHA_GET_PRO_URI,
						'panel'    => 'woocommerce',
						'priority' => 0,
					)
				)
			);
			$manager->add_section(
				new Alpha_Customizer_Pro_Section(
					$manager,
					'wc_cart',
					array(
						'title'    => esc_html__( 'Cart Page', 'alpha' ),
						'pro_url'  => ALPHA_GET_PRO_URI,
						'panel'    => 'woocommerce',
						'priority' => 20,
					)
				)
			);
			$manager->add_section(
				new Alpha_Customizer_Pro_Section(
					$manager,
					'ai_generator',
					array(
						'title'    => esc_html__( 'AI Generator', 'alpha' ),
						'pro_url'  => ALPHA_GET_PRO_URI,
						'panel'    => 'features',
						'priority' => 5,
					)
				)
			);
			$manager->add_section(
				new Alpha_Customizer_Pro_Section(
					$manager,
					'ajax_filter',
					array(
						'title'    => esc_html__( 'Ajax Filter', 'alpha' ),
						'pro_url'  => ALPHA_GET_PRO_URI,
						'panel'    => 'features',
						'priority' => 10,
					)
				)
			);
			$manager->add_section(
				new Alpha_Customizer_Pro_Section(
					$manager,
					'product_buy_now',
					array(
						'title'    => esc_html__( 'Product Buy Now', 'alpha' ),
						'pro_url'  => ALPHA_GET_PRO_URI,
						'panel'    => 'features',
						'priority' => 10,
					)
				)
			);
			$manager->add_section(
				new Alpha_Customizer_Pro_Section(
					$manager,
					'lazyload',
					array(
						'title'    => esc_html__( 'Lazy Load', 'alpha' ),
						'pro_url'  => ALPHA_GET_PRO_URI,
						'panel'    => 'features',
						'priority' => 50,
					)
				)
			);
			$manager->add_section(
				new Alpha_Customizer_Pro_Section(
					$manager,
					'search',
					array(
						'title'    => esc_html__( 'Search', 'alpha' ),
						'pro_url'  => ALPHA_GET_PRO_URI,
						'panel'    => 'features',
						'priority' => 70,
					)
				)
			);
			$manager->add_section(
				new Alpha_Customizer_Pro_Section(
					$manager,
					'reset_options',
					array(
						'title'   => esc_html__( 'Import/Export/Reset', 'alpha' ),
						'pro_url' => ALPHA_GET_PRO_URI,
						'panel'   => 'advanced',
					)
				)
			);
			$manager->add_section(
				new Alpha_Customizer_Pro_Section(
					$manager,
					'seo',
					array(
						'title'   => esc_html__( 'SEO', 'alpha' ),
						'pro_url' => ALPHA_GET_PRO_URI,
						'panel'   => 'advanced',
					)
				)
			);
			$manager->add_section(
				new Alpha_Customizer_Pro_Section(
					$manager,
					'white_label',
					array(
						'title'   => esc_html__( 'White Label', 'alpha' ),
						'pro_url' => ALPHA_GET_PRO_URI,
						'panel'   => 'advanced',
					)
				)
			);
			$manager->add_section(
				new Alpha_Customizer_Pro_Section(
					$manager,
					'custom_cursor',
					array(
						'title'   => esc_html__( 'Custom Cursor', 'alpha' ),
						'pro_url' => ALPHA_GET_PRO_URI,
						'panel'   => 'advanced',
					)
				)
			);
		}
	}
}
Alpha_Customizer_Extend::get_instance();
