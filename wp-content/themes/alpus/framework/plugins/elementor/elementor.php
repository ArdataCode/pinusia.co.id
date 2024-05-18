<?php
/**
 * Elementor Compatibility
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

add_action( 'alpha_demo_imported', 'alpha_update_elementor_settings', 99, 2 );
add_action( 'alpha_demo_imported', 'alpha_update_elementor_preferences', 99 );
add_action( 'customize_save_after', 'alpha_update_elementor_settings', 99 );
add_action( 'customize_save_after', 'alpha_update_elementor_preferences', 99 );
add_action( 'register_new_user', 'alpha_update_elementor_preferences', 99 );

/**
 * alpha_update_elementor_settings
 *
 * update default elementor active kit options
 *
 * @since 1.0
 */
if ( ! function_exists( 'alpha_update_elementor_settings' ) ) {
	function alpha_update_elementor_settings( $demo = false, $add_kit = false ) {
		if ( ! defined( 'ELEMENTOR_VERSION' ) ) {
			return;
		}

		if ( $add_kit && $demo ) {
			$kits = get_posts(
				array(
					'post_type'      => 'elementor_library',
					'posts_per_page' => 1,
					'orderby'        => 'modified',
					'order'          => 'DESC',
					'meta_query'     => array(
						array(
							'key'   => '_elementor_template_type',
							'value' => 'kit',
						),
						array(
							'key'     => '_alpha_demo',
							'value'   => sanitize_text_field( $demo ),
							'compare' => 'LIKE',
						),
					),
				)
			);
			if ( ! empty( $kits ) && ! is_wp_error( $kits ) && is_array( $kits ) ) {
				update_option( Elementor\Core\Kits\Manager::OPTION_ACTIVE, (int) $kits[0]->ID );
				$default_kit = (int) $kits[0]->ID;
				$add_kit     = false;
			}
		}

		if ( $add_kit ) {
			// Create elementor default kit
			$kit = Elementor\Plugin::$instance->kits_manager->get_active_kit();
			if ( ! $kit->get_id() ) {
				$default_kit = Elementor\Plugin::$instance->kits_manager->create_default();
				if ( $default_kit ) {
					update_option( Elementor\Core\Kits\Manager::OPTION_ACTIVE, $default_kit );
				}
			}
		}

		if ( ! isset( $default_kit ) ) {
			$default_kit = get_option( Elementor\Core\Kits\Manager::OPTION_ACTIVE, 0 );
		}

		if ( $default_kit ) {
			$general_settings = get_post_meta( $default_kit, '_elementor_page_settings', true );
			$changed          = false;

			if ( empty( $general_settings ) ) {
				$general_settings = array();
			}

			// container width
			if ( empty( $general_settings['container_width'] ) || ! isset( $general_settings['container_width']['size'] ) || alpha_get_option( 'container' ) != $general_settings['container_width']['size'] ) {
				$general_settings['container_width'] = array(
					'size'  => alpha_get_option( 'container' ),
					'unit'  => 'px',
					'sizes' => array(),
				);
				$changed                             = true;
			}

			// space between widgets
			if ( empty( $general_settings['space_between_widgets'] ) || ! isset( $general_settings['space_between_widgets']['size'] ) || 0 != $general_settings['space_between_widgets']['size'] ) {
				$general_settings['space_between_widgets'] = array(
					'size'  => 0,
					'unit'  => 'px',
					'sizes' => array(),
				);
				$changed                                   = true;
			}

			// responsive breakpoint
			if ( empty( $general_settings['viewport_tablet'] ) || 991 !== (int) $general_settings['viewport_tablet'] ) {
				$general_settings['viewport_tablet'] = 991;
				$changed                             = true;
			}
			if ( empty( $general_settings['viewport_mobile_extra'] ) || 767 !== (int) $general_settings['viewport_mobile_extra'] ) {
				$general_settings['viewport_mobile_extra'] = 767;
				$changed                                   = true;
			}
			if ( empty( $general_settings['viewport_mobile'] ) || 575 !== (int) $general_settings['viewport_mobile'] ) {
				$general_settings['viewport_mobile'] = 575;
				$changed                             = true;
			}
			if ( empty( $general_settings['active_breakpoints'] ) ) {
				$general_settings['active_breakpoints'] = array();
			}
			if ( ! in_array( 'viewport_mobile_extra', $general_settings['active_breakpoints'] ) ) {
				$general_settings['active_breakpoints'][] = 'viewport_mobile_extra';
				$changed                                  = true;
			}

			// system colors
			if ( empty( $general_settings['system_colors'] ) || ! isset( $general_settings['system_colors'][0] ) || alpha_get_option( 'primary_color' ) != $general_settings['system_colors'][0]['color'] ) {
				$general_settings['system_colors'][0]['color'] = alpha_get_option( 'primary_color' );
				$general_settings['system_colors'][0]['title'] = esc_html__( 'Primary', 'alpha' );
				$changed                                       = true;
			}
			if ( empty( $general_settings['system_colors'] ) || ! isset( $general_settings['system_colors'][1] ) || alpha_get_option( 'secondary_color' ) != $general_settings['system_colors'][1]['color'] ) {
				$general_settings['system_colors'][1]['color'] = alpha_get_option( 'secondary_color' );
				$general_settings['system_colors'][1]['title'] = esc_html__( 'Secondary', 'alpha' );
				$changed                                       = true;
			}
			if ( empty( $general_settings['system_colors'] ) || ! isset( $general_settings['system_colors'][2] ) || alpha_get_option( 'typo_default' )['color'] != $general_settings['system_colors'][2]['color'] ) {
				$general_settings['system_colors'][2]['color'] = alpha_get_option( 'typo_default' )['color'];
				$general_settings['system_colors'][2]['title'] = esc_html__( 'Text', 'alpha' );
				$changed                                       = true;
			}
			if ( empty( $general_settings['system_colors'] ) || ! isset( $general_settings['system_colors'][3] ) || alpha_get_option( 'success_color' ) != $general_settings['system_colors'][3]['color'] ) {
				$general_settings['system_colors'][3]['color'] = alpha_get_option( 'success_color' );
				$general_settings['system_colors'][3]['title'] = esc_html__( 'Success', 'alpha' );
				$changed                                       = true;
			}

			// system fonts
			if ( empty( $general_settings['system_typography'] ) ) {
				$general_settings['system_typography'] = array(
					array(
						'_id'                    => 'primary',
						'title'                  => esc_html__( 'Primary', 'alpha' ),
						'typography_typography'  => 'custom',
						'typography_font_family' => alpha_get_option( 'typo_default' )['font-family'],
						'typography_font_weight' => 'default',
					),
					array(
						'_id'                    => 'secondary',
						'title'                  => esc_html__( 'Secondary', 'alpha' ),
						'typography_typography'  => 'custom',
						'typography_font_family' => 'default',
						'typography_font_weight' => 'default',
					),
					array(
						'_id'                    => 'text',
						'title'                  => esc_html__( 'Text', 'alpha' ),
						'typography_typography'  => 'custom',
						'typography_font_family' => 'default',
						'typography_font_weight' => 'default',
					),
					array(
						'_id'                    => 'accent',
						'title'                  => esc_html__( 'Accent', 'alpha' ),
						'typography_typography'  => 'custom',
						'typography_font_family' => 'default',
						'typography_font_weight' => 'default',
					),
				);

				$changed = true;
			}

			if ( $changed ) {
				update_post_meta( $default_kit, '_elementor_page_settings', $general_settings );

				try {
					\Elementor\Plugin::$instance->files_manager->clear_cache();
				} catch ( Exception $e ) {
				}
			}
		}

		if ( false === get_option( 'elementor_disable_color_schemes', false ) ) {
			update_option( 'elementor_disable_color_schemes', 'yes' );
		}
		if ( false === get_option( 'elementor_disable_typography_schemes', false ) ) {
			update_option( 'elementor_disable_typography_schemes', 'yes' );
		}
		if ( false === get_option( 'elementor_experiment-e_dom_optimization', false ) ) {
			update_option( 'elementor_experiment-e_dom_optimization', 'active' );
		}
	}
}

/**
 * alpha_update_elementor_preferences
 *
 * update default elementor preference values
 *  - panel width to 340
 *
 * @since 1.0
 */
function alpha_update_elementor_preferences( $user_id = -1 ) {
	if ( -1 == $user_id ) {
		$user_id = get_current_user_id();
	}

	$preference = get_user_meta( $user_id, 'elementor_preferences' );
	if ( empty( $preference[0] ) || empty( $preference[0]['panel_width'] ) ) {
		$preference[0]['panel_width'] = array(
			'unit'  => 'px',
			'size'  => 340,
			'sizes' => array(),
		);
	}

	update_user_meta( $user_id, 'elementor_preferences', $preference[0] );
}
