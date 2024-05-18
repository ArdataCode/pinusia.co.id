<?php
/**
 * Manages Imported Demo content
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Alpha_Demo_History {

	/**
	 * demo type => demo contents
	 * 
	 * @var Array
	 * @since 1.0
	 */
	private $demo_history;

	/**
	 * Constructor
	 *
	 * @access public
	 * @since 1.0
	 */
	public function __construct() {

		$this->demo_history = get_option( 'alpha_demo_history', array() );

		add_action( 'alpha_importer_update_post', array( $this, 'add_import_symbol' ), 10, 2 );
		add_action( 'wp_import_insert_post', array( $this, 'add_import_symbol' ), 10, 2 );
		add_action( 'alpha_importer_insert_attachment', array( $this, 'add_import_symbol' ), 10, 2 );
		add_action( 'alpha_importer_insert_nav_menu_item', array( $this, 'add_import_symbol' ), 10, 2 );

		add_action( 'alpha_importer_insert_term', array( $this, 'add_term_import_symbol' ), 10, 2 );
		add_action( 'alpha_importer_update_term', array( $this, 'add_term_import_symbol' ), 10, 2 );

		add_action( 'alpha_importer_before_import_widgets', array( $this, 'backup_original_widgets' ) );
		add_action( 'alpha_importer_before_import_options', array( $this, 'backup_original_options' ) );
		add_action( 'alpha_importer_import_revslider', array( $this, 'add_revslider_import_symbol' ) );
		add_action( 'alpha_importer_before_reset_menus', array( $this, 'backup_original_generaldata' ) );

		/* register ajax actions */
		add_action( 'wp_ajax_alpha_sw_remove_demo', array( $this, 'remove_demo' ) );
	}

	/**
	 * Add a meta value which indicates that this post was imported from Alpha demo sites
	 * 
	 * @access public
	 * @since 1.0
	 */
	public function add_import_symbol( $post_id, $old_id = false ) {
		$demo = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? $_POST['demo'] : '';
		update_post_meta( $post_id, '_alpha_demo', sanitize_text_field( $demo ) . ( $old_id ? '#' . (int) $old_id : '' ) );
	}

	/**
	 * Add a term meta value which indicates that this term was imported from Alpha demo sites
	 * 
	 * @access public
	 * @since 1.0
	 */
	public function add_term_import_symbol( $term_id, $old_id = false ) {
		$demo = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? $_POST['demo'] : '';
		update_term_meta( $term_id, '_alpha_demo', sanitize_text_field( $demo ) . ( $old_id ? '#' . (int) $old_id : '' ) );
	}

	/**
	 * Add a revolution import symbol.
	 * 
	 * @access public
	 * @since 1.0
	 */
	public function add_revslider_import_symbol( $slider_id ) {
		if ( ! isset( $this->demo_history['revsliders'] ) ) {
			$this->demo_history['revsliders'] = array();
		}
		$this->demo_history['revsliders'][] = $slider_id;
		$this->update_demo_history();
	}

	/**
	 * Backup original widgets
	 * 
	 * @access public
	 * @since 1.0
	 */
	public function backup_original_widgets() {
		if ( empty( $this->demo_history['widgets'] ) && empty( $this->demo_history['sidebars_widgets'] ) && empty( $this->demo_history['sbg_sidebars'] ) ) {
			$this->demo_history['widgets']          = $this->fetch_widgets();
			$this->demo_history['sidebars_widgets'] = get_option( 'sidebars_widgets' );
			$this->demo_history['sbg_sidebars']     = get_option( 'sbg_sidebars' );
			$this->update_demo_history();
		}
	}

	/**
	 * Backup original theme options
	 * 
	 * @access public
	 * @since 1.0
	 */
	public function backup_original_options() {
		if ( empty( $this->demo_history['options'] ) ) {
			$theme_options                 = get_theme_mods();
			$this->demo_history['options'] = $theme_options;
			$this->update_demo_history();
		}
	}

	/**
	 * Backup general data
	 * 
	 * @access public
	 * @since 1.0
	 */
	public function backup_original_generaldata() {
		if ( empty( $this->demo_history['blogname'] ) ) {
			$this->demo_history['blogname'] = get_option( 'blogname' );
		}
		if ( empty( $this->demo_history['page_on_front'] ) ) {
			$this->demo_history['page_on_front'] = get_option( 'page_on_front' );
		}
		if ( empty( $this->demo_history['show_on_front'] ) ) {
			$this->demo_history['show_on_front'] = get_option( 'show_on_front' );
		}
		if ( empty( $this->demo_history['nav_menu_locations'] ) ) {
			$this->demo_history['nav_menu_locations'] = get_theme_mod( 'nav_menu_locations' );
		}
		$this->update_demo_history();
	}

	/**
	 * Fetches the original widgets data
	 * 
	 * @access public
	 * @since 1.0
	 */
	protected function fetch_widgets() {
		global $wpdb;
		$results = $wpdb->get_results( "SELECT * FROM $wpdb->options WHERE option_name LIKE 'widget_%'" );
		if ( is_wp_error( $results ) ) {
			$results = array();
		}
		return $results;
	}

	/**
	 * Update demo history from variables to database
	 * 
	 * @access public
	 * @since 1.0
	 */
	public function update_demo_history() {
		update_option( 'alpha_demo_history', $this->demo_history, false );
	}

	/**
	 * Remove demo contents
	 * 
	 * @access public
	 * @since 1.0
	 */
	public function remove_demo() {
		if ( ! check_ajax_referer( 'alpha_setup_wizard_nonce', 'wpnonce' ) || ! current_user_can( 'manage_options' ) ) {
			die();
		}

		$type      = isset( $_POST['type'] ) ? $_POST['type'] : '';
		$post_type = isset( $_POST['post_type'] ) ? $_POST['post_type'] : '';
		if ( 'posts' == $type && $post_type ) {
			$reset_general_data = false;
			if ( 'other' === $post_type ) {
				$reset_general_data = true;
				if ( alpha_get_feature( 'fs_pb_elementor' ) && defined( 'ELEMENTOR_VERSION' ) ) {
					$_GET['force_delete_kit'] = true;
				}
				$post_type = array( 'nav_menu_item', 'elementor_library', 'wpcf7_contact_form', 'revision', 'inherit' );
			}
			$args = array(
				'posts_per_page' => -1, // phpcs:ignore WPThemeReview.CoreFunctionality.PostsPerPage
				'post_type'      => is_array( $post_type ) ? $post_type : array( $post_type ),
				'post_status'    => 'any',
				'fields'         => 'ids',
				'meta_query'     => array(
					array(
						'key'     => '_alpha_demo',
						'compare' => 'EXISTS',
					),
				),
			);

			$query = new WP_Query( $args );

			if ( ! empty( $query->posts ) && is_array( $query->posts ) ) {
				foreach ( $query->posts as $post_id ) {
					wp_delete_post( $post_id, true );
				}
			}

			$args       = array(
				'object_type' => is_array( $post_type ) ? $post_type : array( $post_type ),
			);
			$output     = 'names'; // or objects
			$operator   = 'and'; // 'and' or 'or'
			$taxonomies = get_taxonomies( $args, $output, $operator );
			if ( ! empty( $taxonomies ) ) {
				$terms = get_terms(
					array(
						'taxonomy'   => $taxonomies,
						'meta_query' => array(
							array(
								'key'     => '_alpha_demo',
								'compare' => 'EXISTS',
							),
						),
					)
				);
				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						wp_delete_term( $term->term_id, $term->taxonomy );
					}
				}
			}

			if ( $reset_general_data ) {
				if ( ! empty( $this->demo_history['blogname'] ) ) {
					update_option( 'blogname', $this->demo_history['blogname'] );
				}
				if ( ! empty( $this->demo_history['page_on_front'] ) ) {
					update_option( 'page_on_front', $this->demo_history['page_on_front'] );
				}
				if ( ! empty( $this->demo_history['show_on_front'] ) ) {
					update_option( 'show_on_front', $this->demo_history['show_on_front'] );
				}
				if ( ! empty( $this->demo_history['nav_menu_locations'] ) ) {
					foreach ( $this->demo_history['nav_menu_locations'] as $location => $menu_id ) {

						if ( 0 === $menu_id ) {
							continue;
						}

						if ( ! term_exists( (int) $menu_id, 'nav_menu' ) ) {
							unset( $this->demo_history['nav_menu_locations'][ $location ] );
						}
					}
					set_theme_mod( 'nav_menu_locations', $this->demo_history['nav_menu_locations'] );
				}
				unset( $this->demo_history['blogname'], $this->demo_history['page_on_front'], $this->demo_history['show_on_front'], $this->demo_history['nav_menu_locations'] );
			}
		} elseif ( 'widgets' == $type ) {
			if ( isset( $this->demo_history['sidebars_widgets'] ) ) {
				update_option( 'sidebars_widgets', $this->demo_history['sidebars_widgets'] );
				unset( $this->demo_history['sidebars_widgets'] );
			}
			if ( isset( $this->demo_history['sbg_sidebars'] ) ) {
				update_option( 'sbg_sidebars', $this->demo_history['sbg_sidebars'] );
				unset( $this->demo_history['sbg_sidebars'] );
			}

			if ( ! empty( $this->demo_history['widgets'] ) ) {
				foreach ( $this->demo_history['widgets'] as $widget ) {
					update_option( $widget->option_name, maybe_unserialize( $widget->option_value ) );
				}
			}

			unset( $this->demo_history['widgets'] );

		} elseif ( 'options' == $type ) {
			if ( ! empty( $this->demo_history['options'] ) ) {
				update_option( 'theme_mods_' . get_option( 'stylesheet' ), $this->demo_history['options'] );
			}
			unset( $this->demo_history['options'] );
		} elseif ( 'sliders' == $type && class_exists( 'RevSliderSlider' ) ) {

			$slider = new RevSliderSlider();

			if ( ! empty( $this->demo_history['revsliders'] ) ) {

				foreach ( $this->demo_history['revsliders'] as $index => $slider_id ) {
					$slider->initByID( $slider_id );
					$slider->deleteSlider();

					unset( $this->demo_history['revsliders'][ $index ] );
				}

				if ( empty( $this->demo_history['revsliders'] ) ) {
					unset( $this->demo_history['revsliders'] );
				}
			}
		}

		$this->update_demo_history();

		wp_send_json_success();
	}
}
