<?php
/**
 * Alpha Tools
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.3.0
 */

// Direct access is denied
defined( 'ABSPATH' ) || die;

class Alpha_Rollback extends Alpha_Base {

	/**
	 * The Page slug
	 *
	 * @since 1.3.0
	 * @access public
	 */
	public $page_slug = 'alpha-rollback';

	/**
	 * The Result
	 *
	 * @since 1.3.0
	 * @access public
	 */
	private $result;

    
	/**
	 * Theme Versions
	 *
	 * @since 1.3.0
	 * @access public
	 * @var $theme_version
	 */
	public $theme_versions = array();

	/**
	 * Plugin Versions
	 *
	 * @since 1.3.0
	 * @access public
	 * @var $plugin_versions
	 */
	public $plugin_versions = array();

	/**
	 * Theme URL
	 *
	 * @since 1.3.0
	 * @access public
	 * @var $theme_url
	 */
	public $theme_url = ALPHA_THEME_URL;

    /**
	 * Theme Slug
	 *
	 * @since 1.3.0
	 * @access public
	 * @var $theme_slug
	 */
	public $theme_slug = ALPHA_NAME;

	/**
	 * Constructor
	 *
	 * @since 1.3.0
	 * @access public
	 */
	public function __construct() {

		add_action( 'wp_ajax_alpha_modify_theme_auto_updates', array( $this, 'alpha_modify_theme_auto_updates' ) );
		add_action( 'wp_ajax_alpha_modify_plugin_auto_updates', array( $this, 'alpha_modify_plugin_auto_updates' ) );

		if ( wp_doing_ajax() ) {
			if ( $_REQUEST['action'] == 'update-theme' ) {
				add_filter( 'site_transient_update_themes', array( $this, 'alpha_check_for_update_theme' ), 1 );
			}
			
			if ( $_REQUEST['action'] == 'update-plugin' ) {
				add_filter( 'site_transient_update_plugins', array( $this, 'alpha_check_for_update_plugin' ), 1 );
			}
		}

		if ( ! current_user_can( 'administrator' ) || ! isset( $_GET['page'] ) || $this->page_slug != $_GET['page'] ) {
			return;
		}


		$this->init();
	}

    /**
	 * Initialize
	 *
	 * @since 1.3.0
	 * @access public
	 */
	public function init() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 30 );

		$this->theme_versions  = $this->get_theme_versions();
		$this->plugin_versions = $this->get_plugin_versions();
	}

	/**
	 * Enqueue Styles & Scripts
	 *
	 * @since 1.3.0
	 * @access public
	 */
	public function enqueue() {
		wp_enqueue_script( 'updates' );
		wp_enqueue_script( 'alpha-admin-wizard', alpha_framework_uri( '/admin/panel/wizard' . ALPHA_JS_SUFFIX ), array( 'jquery-core' ), true, 50 );

		wp_localize_script(
			'alpha-admin-wizard',
			'alpha_rollback_params',
			array(
				'wpnonce'     => wp_create_nonce( 'alpha_rollback_nonce' ),
			)
		);
	}

	/**
	 * Get Theme Versions
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @return array versions
	 */
	public function get_theme_versions() {
		$rollback_versions = get_site_transient( 'alpha_theme_rollback_versions' );

		if ( false === $rollback_versions ) {
			$max_version   = 20;
			$current_index = 0;

			require_once ALPHA_FRAMEWORK_ADMIN . '/importer/importer-api.php';
			$importer_api = new Alpha_Importer_API();

			$versions = $importer_api->get_response( 'theme_rollback_versions' );

			if ( is_wp_error( $versions ) || empty( $versions ) ) {
				return array();
			}

			$rollback_versions = array();

			foreach ( $versions as $version ) {
				if ( $max_version <= $current_index ) {
					break;
				}

				if ( version_compare( $version, ALPHA_VERSION, '>=' ) ) {
					continue;
				}

				$current_index ++;
				$rollback_versions[] = $version;
			}

			if ( ! empty( $rollback_versions ) ) {
				set_site_transient( 'alpha_theme_rollback_versions', $rollback_versions, WEEK_IN_SECONDS );
			}
		}

		return $rollback_versions;
	}

	/**
	 * Get Plugin Versions
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @return array plugin versions
	 */
	public function get_plugin_versions() {
		$rollback_versions = get_site_transient( 'alpha_plugin_rollback_versions' );

		if ( false === $rollback_versions ) {
			$max_version   = 20;
			$current_index = 0;

			require_once ALPHA_FRAMEWORK_ADMIN . '/importer/importer-api.php';
			$importer_api = new Alpha_Importer_API();

			$versions = $importer_api->get_response( 'plugin_rollback_versions' );

			if ( is_wp_error( $versions ) || empty( $versions ) ) {
				return array();
			}

			$rollback_versions = array();

			foreach ( $versions as $plugin_slug => $version_arr ) {
				if ( is_numeric( $plugin_slug ) && ! is_array( $version_arr ) ) { // old, only for core plugin
					if ( $max_version <= $current_index ) {
						break;
					}

					if ( defined( 'ALPHA_CORE_VERSION ' ) && version_compare( $version_arr, ALPHA_CORE_VERSION, '>=' ) ) {
						continue;
					}

					$current_index ++;
					$rollback_versions[] = $version_arr;
				} else {
					$current_index = 0;
					foreach ( $version_arr as $version ) {
						if ( $max_version <= $current_index ) {
							break;
						}
						if ( ! isset( $rollback_versions[ $plugin_slug ] ) ) {
							$rollback_versions[ $plugin_slug ] = array();
						}

						$current_index ++;
						$rollback_versions[ $plugin_slug ][] = $version;
					}
				}
			}

			set_site_transient( 'alpha_plugin_rollback_versions', $rollback_versions, WEEK_IN_SECONDS );
		}

		return $rollback_versions;
	}

	/**
	 * Check for update theme.
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @param array $data themes info.
	 * @return array $data
	 */
	public function alpha_check_for_update_theme( $data ) {
		if ( empty( $data ) ) {
			$data = (object) array( 'response' => '' );
			$data->response = array();
		}

		$transient_data = get_site_transient( 'alpha_modify_theme_auto_update' );

		if ( ! empty( $transient_data ) ) {
			$data->checked['alpha']    = $transient_data['old_version'];
			$data->response[ 'alpha' ] = $transient_data;
		}

		delete_site_transient( 'alpha_theme_rollback_versions' );

		return $data;
	}

	/**
	 * Check for update plugin
	 *
	 * @since 1.3.0
	 * @access public
	 *
	 * @param array $data themes info.
	 * @return array $data
	 */
	public function alpha_check_for_update_plugin( $data ) {
		if ( empty( $data ) ) {
			$data = (object) array( 'response' => '' );
			$data->response = array();
		}

		$transient_data = get_site_transient( 'alpha_modify_plugin_auto_update' );

		if ( ! empty( $transient_data ) ) {
			$p_data = json_decode( wp_json_encode( $transient_data ), false );
			$data->response[ $p_data->plugin ] = $p_data;
		}

		delete_site_transient( 'alpha_plugin_rollback_versions' );

		return $data;
	}

	/**
	 * Modify theme auto updates
	 *
	 * @since 1.3.0
	 * @access public
	 */
	public function alpha_modify_theme_auto_updates() {
		if ( ! isset( $_REQUEST['wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['wpnonce'], 'alpha_rollback_nonce' ) ) {
			wp_send_json( false );
			die();
		}

		delete_transient( 'alpha_modify_theme_auto_update' );

		require_once ALPHA_FRAMEWORK_ADMIN . '/importer/importer-api.php';
		$importer_api = new Alpha_Importer_API();

		$args            = $importer_api->generate_args( false );
		$version         = isset( $_REQUEST['version'] ) ? wp_unslash( $_REQUEST['version'] ) : '';
		$args['version'] = $version;
		$package_url     = add_query_arg( $args, $importer_api->get_url( 'theme_rollback' ) );

		$transient_data = array(
			'theme'           => 'alpha',
			'old_version'     => $version,
			'new_version'     => $version,
			'url'             => $this->theme_url,
			'package'         => $package_url,
		);

		set_site_transient( 'alpha_modify_theme_auto_update', $transient_data, WEEK_IN_SECONDS );

		wp_send_json( true );
		die();
	}

	/**
	 * Modify plugin auto updates
	 *
	 * @since 1.3.0
	 * @access public
	 */
	public function alpha_modify_plugin_auto_updates() {
		if ( ! isset( $_REQUEST['wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['wpnonce'], 'alpha_rollback_nonce' ) ) {
			wp_send_json( false );
			die();
		}

		delete_transient( 'alpha_modify_plugin_auto_update' );

		require_once ALPHA_FRAMEWORK_ADMIN . '/importer/importer-api.php';
		$importer_api = new Alpha_Importer_API();

		$args            = $importer_api->generate_args( false );
		$version         = isset( $_REQUEST['version'] ) ? wp_unslash( $_REQUEST['version'] ) : '';
		$args['version'] = $version;
		$args['slug']    = isset( $_REQUEST['slug'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['slug'] ) ) : '';
		$package_url     = add_query_arg( $args, $importer_api->get_url( 'plugin_rollback' ) );

		$transient_data = array(
			'slug'            => $args['slug'] ? $args['slug'] : 'alpha-core',
			'plugin'          => ! empty( $_REQUEST['plugin'] ) ? sanitize_text_field( $_REQUEST['plugin'] ) : 'alpha-core/alpha-core.php',
			'old_version'     => $version,
			'new_version'     => $version,
			'url'             => $this->theme_url,
			'package'         => $package_url,
		);

		set_site_transient( 'alpha_modify_plugin_auto_update', $transient_data, WEEK_IN_SECONDS );

		wp_send_json( true );
		die();
	}

	/**
	 * Render tools page
	 *
	 * @since 1.0
	 * @access public
	 */
	public function view_tools() {
		if ( ! Alpha_Admin::get_instance()->is_registered() ) {
			set_transient( '_alpha_register_redirect', admin_url( 'admin.php?page=alpha-rollback' ) );
			wp_redirect( admin_url( 'admin.php?page=alpha' ) );
			die();
		}

        $admin_config = Alpha_Admin::get_instance()->admin_config;
		$title        = array(
			'title' => esc_html__( 'Rollback', 'alpha' ),
			'desc'  => sprintf( esc_html__( 'Experiencing an issue with New version? Rollback to a previous version before the issue appeared.', 'alpha' ), ALPHA_DISPLAY_NAME ),
		);
		Alpha_Admin_Panel::get_instance()->view_header( 'rollback', $admin_config, $title );

		$nonce = wp_create_nonce( 'alpha-rollback' );
		?>
		<?php
		if ( isset( $this->result ) ) {
			if ( $this->result['success'] ) {
				echo '<div class="alpha-notify updated inline"><p>' . esc_html( $this->result['message'] ) . '</p></div>';
			} else {
				echo '<div class="alpha-notify error inline"><p>' . esc_html( $this->result['message'] ) . '</p></div>';
			}
		}

		$alpha_plugins_obj = new Alpha_TGM_Plugins();
		$alpha_plugins_obj->get_plugins_list();
		$all_plugins      = get_site_transient( 'alpha_plugins' );
		$required_plugins = array();
		if ( ! empty( $all_plugins ) ) {
			foreach ( $all_plugins as $p ) {
				$required_plugins[ $p['slug'] ] = $p;
			}
		}

		?>
		<div class="alpha-admin-panel-body alpha-card-box alpha-rollback">
			<table class="wp-list-table widefat" id="alpha_versions_table">
				<thead>
					<tr>
						<th scope="col" id="title" class="manage-column column-title column-primary"><?php echo esc_html__( 'Action Name', 'alpha' ); ?></th>
						<th scope="col" id="remove" class="manage-column column-remove"><?php echo esc_html__( 'Action', 'alpha' ); ?></th>
					</tr>
				</thead>
				<tbody id="the-list">
					<tr class="theme-version" id="alpha-theme-version">
						<th>
							<strong class="action-name"><?php echo esc_html( ALPHA_DISPLAY_NAME ); ?></strong>
							<p class="description warning"><?php echo esc_html__( 'Warning: Please backup your database before making the rollback.', 'alpha' ); ?></p>
						</th>
						<td class="run-tool">
							<select class="version-select theme-versions" id="theme-versions">
								<?php
								foreach ( $this->theme_versions as $version ) {
									?>
										<option value="<?php echo esc_attr( $version ); ?>"><?php echo esc_html( $version ); ?></option>
										<?php
								}
								?>
							</select>
							<a href="#" class="button button-large button-light theme-rollback"><?php echo esc_html__( 'Downgrade', 'alpha' ); ?></a>
						</td>
					</tr>
				<?php if ( ! empty( $required_plugins ) && ! empty( $this->plugin_versions ) ) : ?>
					<?php foreach ( $required_plugins as $slug => $plugin ) : ?>
					<?php
						if ( empty( $this->plugin_versions[ $slug ] ) ) {
							if ( ! isset( $this->plugin_versions[0] ) ) {
								continue;
							} else {
								$p_versions = $this->plugin_versions[0];
							}
						} else {
							$p_versions = $this->plugin_versions[ $slug ];
						}

						$current_version = '';
						if ( function_exists( 'get_plugin_data' ) ) {
							$p_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin['url'] );
							if ( ! empty( $p_data ) && isset( $p_data['Version'] ) ) {
								$current_version = $p_data['Version'];
							}
						}

					?>
						<tr class="plugin-version" id="<?php echo esc_attr( $slug ); ?>-version" data-url="<?php echo esc_attr( $plugin['url'] ); ?>" data-slug="<?php echo esc_attr( $slug ); ?>">
							<th>
								<strong class="action-name"><?php echo esc_html( $plugin['name'] ); ?></strong>
								<p class="description warning"><?php echo esc_html__( 'Warning: Please backup your database before making the rollback.', 'alpha' ); ?></p>
							</th>
							<td class="run-tool">
								<select class="version-select plugin-versions">
									<?php
									foreach ( $p_versions as $version ) {
										if ( $current_version && version_compare( $version, $current_version, '>=' ) ) {
											continue;
										}
										?>
											<option value="<?php echo esc_attr( $version ); ?>"><?php echo esc_html( $version ); ?></option>
											<?php
									}
									?>
								</select>
								<a href="#" class="button button-large button-light plugin-rollback"><?php echo esc_html__( 'Downgrade', 'alpha' ); ?></a>
							</td>
						</tr>
					<?php
						if ( empty( $this->plugin_versions[ $slug ] ) && isset( $this->plugin_versions[0] ) ) {
							break;
						}
					?>
					<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
		Alpha_Admin_Panel::get_instance()->view_footer();
	}
}

Alpha_Rollback::get_instance();
