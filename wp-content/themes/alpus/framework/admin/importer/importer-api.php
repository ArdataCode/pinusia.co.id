<?php
/**
 * Importer API
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

class Alpha_Importer_API {

	protected $demo = '';
	protected $code = '';

	protected $path_tmp  = '';
	protected $path_demo = '';

	protected $url = array(
		'changelog'                => ALPHA_SERVER_URI . 'dummy/api/api?method=theme_changelog',
		'theme_version'            => ALPHA_SERVER_URI . 'dummy/api/api?method=theme_version',
		'theme'                    => ALPHA_SERVER_URI . 'dummy/api/api?method=theme',
		'plugins_version'          => ALPHA_SERVER_URI . 'dummy/api/api?method=plugins_version',
		'plugins'                  => ALPHA_SERVER_URI . 'dummy/api/api?method=plugins',
		'plugins_install_count'    => ALPHA_SERVER_URI . 'dummy/api/api?method=plugins_install_count',
		'demos'                    => ALPHA_SERVER_URI . 'dummy/api/api?method=demos',
		'plugin_list_add'          => ALPHA_SERVER_URI . 'dummy/api/api?method=plugin_list_add',
		'studio_blocks'            => ALPHA_SERVER_URI . 'dummy/api/api?method=studio_blocks',
		'studio_block_categories'  => ALPHA_SERVER_URI . 'dummy/api/api?method=studio_categories',
		'studio_block_content'     => ALPHA_SERVER_URI . 'dummy/api/api?method=studio_block_content',
		'theme_rollback_versions'  => ALPHA_SERVER_URI . 'dummy/api/api?method=theme_rollback_versions',
		'plugin_rollback_versions' => ALPHA_SERVER_URI . 'dummy/api/api?method=plugin_rollback_versions',
		'theme_rollback'           => ALPHA_SERVER_URI . 'dummy/api/api?method=theme_rollback',
		'plugin_rollback'          => ALPHA_SERVER_URI . 'dummy/api/api?method=plugin_rollback',
		'patcher'          	       => ALPHA_SERVER_URI . 'dummy/api/api?method=patcher',
	);

	/**
	 * Get Patches from the server
	 * 
	 * @since 1.3.0
	 */
	public function get_patch_files() {
		if ( defined( 'ALPHA_CORE_VERSION' ) ) {
			
			$args = $this->generate_args( false );
			$url  = $this->get_url( 'patcher' );

			if ( isset( $args['code'] ) ) {
				$args = array(
					'theme_version' => ALPHA_VERSION,
					'func_version'  => ALPHA_CORE_VERSION,
					'code'          => $args['code'],
					'template'      => get_template(),

				);
				$url = add_query_arg( $args, $url );
			}

			$response = $this->get_response( $url, array(), 'json' );

			if ( is_wp_error( $response ) ) {
				return false;
			}
			if ( $response ) {
				return $response;
			}

		}
		return false;
	}

	/**
	 * Get Patch Content
	 * 
	 * @since 1.3.0
	 */
	public function get_patch_content( $patches_data ) {
		if ( defined( 'ALPHA_CORE_VERSION' ) ) {
			$args = $this->generate_args( false );
			$url  = $this->get_url( 'patcher' );

			if ( isset( $args['code'] ) ) {
				$args = array(
					'patches_files' => urlencode( json_encode( $patches_data ) ),
					'code'          => $args['code'],
					'template'      => get_template(),
				);
				$url = add_query_arg( $args, $url );
			}

			$response = $this->get_response( $url, array(), 'gzencode' );

			if ( is_wp_error( $response ) ) {
				return false;
			}

			if ( $response ) {
				return json_decode( gzdecode( $response ), true );
			}
		}
		return false;
	}

	/**
	 * The constructor
	 *
	 * @since 1.0
	 */
	public function __construct( $demo = false ) {
		
		$this->url = apply_filters( 'alpha_importer_api_urls', $this->url );
		
		if ( $demo ) {
			$this->demo     = $demo;
			$upload_dir     = wp_upload_dir();
			$this->path_tmp = wp_normalize_path( $upload_dir['basedir'] . '/alpha_tmp_dir' );
			$this->makedir();
		}
		if ( class_exists( 'Alpha_Admin' ) ) {
			$this->code = Alpha_Admin::get_instance()->get_purchase_code();
		} else {
			$this->code = get_option( 'envato_purchase_code_' . ALPHA_ENVATO_CODE );
		}
		add_filter( 'http_request_args', array( $this, 'http_request_args' ), 10, 2 );
	}

	/**
	 * Get url
	 *
	 * @since 1.0
	 */
	public function get_url( $id ) {
		return isset( $this->url[ $id ] ) ? $this->url[ $id ] : false;
	}

	/**
	 * Filters http request args
	 *
	 * @since 1.0
	 */
	public function http_request_args( $parsed_args = [], $url = '' ) {
		if ( false === strpos( $url, ALPHA_SERVER_URI . 'dummy/api/' ) ) {
			return $parsed_args;
		}
		if ( ! isset( $parsed_args['headers'] ) || ! is_array( $parsed_args['headers'] ) ) {
			$parsed_args['headers'] = array();
		}
		$parsed_args['headers']['Referer'] = site_url();
		return $parsed_args;
	}

	/**
	 * Get message
	 *
	 * @since 1.0
	 */
	public function get_message( $data, $type = 'message' ) {
		$msg_code = false;

		if ( 'error' == $type ) {
			$msg_code = $data['error'];
		} elseif ( 'message' == $type ) {
			$msg_code = $data['message'];
		}

		return Alpha_Admin::get_instance()->get_api_message( $msg_code );
	}
	/**
	 * Create directories
	 *
	 * @since 1.0
	 */
	protected function makedir() {
		
		if ( ! file_exists( $this->path_tmp ) ) {
			wp_mkdir_p( $this->path_tmp );
		}
		$this->path_demo = wp_normalize_path( $this->path_tmp . '/' . $this->demo );
		if ( ! file_exists( $this->path_demo ) ) {
			wp_mkdir_p( $this->path_demo );
		}
	}

	/**
	 * Delete temporary directory
	 *
	 * @since 1.0
	 */
	public function delete_temp_dir() {

		// filesystem
		global $wp_filesystem;
		// Initialize the WordPress filesystem, no more using file_put_contents function
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		// directory is located outside wp uploads dir
		$upload_dir      = wp_upload_dir();
		$this->path_demo = $this->path_tmp . '/' . $this->demo;
		if ( false === strpos( str_replace( '\\', '/', $this->path_demo ), str_replace( '\\', '/', $upload_dir['basedir'] ) ) ) {
			return false;
		}

		$wp_filesystem->delete( $this->path_tmp, true );
	}

	/**
	 * Get response
	 *
	 * @since 1.0
	 */
	public function get_response( $target, $args = array(), $data_type = 'json' ) {

		$defaults = array(
			'user-agent' => ALPHA_NAME . '/' . ALPHA_VERSION,
			'timeout'    => 100,
			'body'       => array(
				'template' => get_template(),
			),
		);
		$args     = wp_parse_args( $args, $defaults );

		$url = $this->get_url( $target );
		if ( ! $url ) {
			$url = $target;
		}
		$response = wp_remote_get( esc_url_raw( $url ), $args );
		
		if ( is_wp_error( $response ) ) {
			return $response;
		}
		$response = wp_remote_retrieve_body( $response );
		if ( 'json' == $data_type ) {
			$data = json_decode( $response, true );
			if ( isset( $data['error'] ) ) {
				return new WP_Error( 'invalid_response', $this->get_message( $data, 'error' ) );
			}
			return $data;
		}
		return $response;
	}

	public function generate_args( $ish = true ) {
		preg_match( '/[a-z0-9\-]{1,63}\.[a-z\.]{2,6}$/', parse_url( home_url(), PHP_URL_HOST ), $_domain_tld );
		if ( isset( $_domain_tld[0] ) ) {
			$domain = $_domain_tld[0];
		} else {
			$domain = parse_url( home_url(), PHP_URL_HOST );
		}
		$args = array(
			'code'     => $this->code,
			'domain'   => $domain,
			'template' => get_template(),
		);

		if ( $this->is_localhost() ) {
			$args['local'] = 'true';
		}
		if ( $ish && Alpha_Admin::get_instance()->is_envato_hosted() ) {
			$args['ish'] = Alpha_Admin::get_instance()->get_ish();
		}
		return $args;
	}

	/**
	 * Get remote demo files
	 *
	 * @since 1.0
	 */
	public function get_remote_demo( $target = 'demos' ) {
		$path_unzip = wp_normalize_path( $this->path_demo . '/' . $this->demo );
		if ( is_dir( $path_unzip ) ) {
			return $path_unzip;
		}

		$url          = $this->url[ $target ];
		$args         = $this->generate_args();
		$args['demo'] = $this->demo;
		$args['name'] = parse_url( get_site_url(), PHP_URL_HOST );

		$url = add_query_arg( $args, $url );

		$args = array(
			'user-agent' => ALPHA_NAME . '/' . ALPHA_VERSION,
			'timeout'    => 60,
		);
		$response = wp_remote_get( esc_url_raw( $url ), $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$body = wp_remote_retrieve_body( $response );

		if ( empty( $body ) ) {
			return new WP_Error( 'error_download', esc_html__( 'The package could not be downloaded.', 'alpha' ) );
		}

		$json = json_decode( $body, true );
		if ( $json ) {
			if ( isset( $json['error'] ) ) {
				return new WP_Error( 'invalid_response', $this->get_message( $json, 'error' ) );
			}
		}

		// filesystem
		global $wp_filesystem;
		// Initialize the WordPress filesystem, no more using file_put_contents function
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();
		}

		$path_package = wp_normalize_path( $this->path_demo . '/' . $this->demo . '.zip' );

		if ( ! $wp_filesystem->put_contents( $path_package, $body, FS_CHMOD_FILE ) ) {
			@unlink( $path_package );
			return new WP_Error( 'error_fs', esc_html__( 'WordPress filesystem error.', 'alpha' ) );
		}

		$unzip = unzip_file( $path_package, $this->path_demo );
		if ( is_wp_error( $unzip ) ) {
			return new WP_Error( 'error_unzip', esc_html__( 'The package could not be unziped.', 'alpha' ) );
		}

		if ( ! is_dir( $path_unzip ) ) {
			/* translators: %s: upload path */
			return new WP_Error( 'error_folder', sprintf( esc_html__( 'Demo data directory does not exist (%s).', 'alpha' ), $path_unzip ) );
		}

		return $path_unzip;
	}

	/**
	 * Get remote theme version
	 *
	 * @since 1.0
	 */
	public function get_latest_theme_version() {
		$response = $this->get_response( 'theme_version' );
		if ( is_wp_error( $response ) ) {
			return false;
		}
		if ( empty( $response['version'] ) ) {
			return false;
		}
		return $response['version'];
	}

	/**
	 * Get remote plugin version
	 *
	 * @since 1.0
	 */
	public function get_latest_core_version() {
		$response = $this->get_response( 'plugins_version' );
		if ( is_wp_error( $response ) || ! is_array( $response ) ) {
			return false;
		}
		foreach ( $response as $item ) {
			if ( ! empty( $item['slug'] ) && ALPHA_CORE_SLUG == $item['slug'] ) {
				return $item['version'];
			}
		}
		return false;
	}

	/**
	 * Is localhost?
	 *
	 * @since 1.0
	 */
	public function is_localhost() {
		if ( current_user_can( 'manage_options' ) ) {
			$current_sessions = wp_get_all_sessions();
			$whitelist        = array(
				'127.0.0.1',
				'localhost',
				'::1',
			);
			if ( isset( $current_sessions[0] ) && isset( $current_sessions[0]['ip'] ) && in_array( $current_sessions[0]['ip'], $whitelist ) ) {
				return true;
			}
		}
		return false;
	}
}
