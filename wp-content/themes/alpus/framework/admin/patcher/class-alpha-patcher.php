<?php
/**
 * Alpha Patcher
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.3.0
 *
 */

defined( 'ABSPATH' ) || die;

define( 'ALPHA_PATCHER', ALPHA_FRAMEWORK_ADMIN . '/patcher' );

if ( ! class_exists( 'Alpha_Patcher' ) ) :
	class Alpha_Patcher extends Alpha_Base {
        /**
         * Instance
         *
         * @since 1.3.0
         * @access protected
         * @var $instance
         */
        protected static $instance;
    
        /**
         * Page slug
         *
         * @since 1.3.0
         * @access public
         * @var $page_slug
         */
        public $page_slug = 'alpha-patcher';

        /**
		 * Theme Name
		 * 
         * @access protected
		 * @since 1.3.0
		 */
		protected $theme_name = '';

        /**
		 * Theme Slug
		 * 
         * @access public
		 * @since 1.3.0
		 */
		public $theme_slug = 'alpha';

        /**
		 * Page URL
		 * 
         * @access protected
		 * @since 1.3.0
		 */
		protected $page_url;

		/**
         * The transient name.
         *
         * @since 1.3.0
         */
        private static $transient_name = 'alpha_patch_transient';

        /**
         * Patches for the database
         * 
         * @since 1.3.0
         */
        public $patches = array();

        /**
         * Patches for applied
         * 
         * @since 1.3.0
         */
        public $patched_data = array();

        /**
         * Constructor
         *
         * @since 1.3.0
         * @access public
         */
        public function __construct() {
            // Enqueue Styles & Scripts.
            add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

            // Apply patches.
            add_action( 'wp_ajax_alpha_apply_patches', array( $this, 'apply_patches' ) );
        }

        /**
         * Enqueue Admin Styles
         */
        public function enqueue_admin_scripts() {           
            // Enqueue assests
            wp_enqueue_style( 'alpha-patcher', ALPHA_FRAMEWORK_ADMIN_URI . '/patcher/assets/patcher.min.css', NULL, ALPHA_VERSION );
            wp_enqueue_script( 'alpha-patcher', ALPHA_FRAMEWORK_ADMIN_URI . '/patcher/assets/patcher' . ALPHA_JS_SUFFIX, NULL, ALPHA_VERSION, true );
        }

        /**
         * Show the patch table
         * 
         * @since 1.3.0
         */
        public function view_patcher() {
            if ( ! Alpha_Admin::get_instance()->is_registered() ) {
				set_transient( '_alpha_register_redirect', admin_url( 'admin.php?page=alpha-patcher' ) );
				wp_redirect( admin_url( 'admin.php?page=alpha' ) );
                die();
			}

			$title        = array(
				'title' => esc_html__( 'Patcher', 'alpha' ),
				'desc'  => sprintf( esc_html__( 'With this, you can apply fixes to your site between %s releases and update partially.', 'alpha' ), ALPHA_DISPLAY_NAME ),
			);
			$admin_config = Alpha_Admin::get_instance()->admin_config;

			Alpha_Admin_Panel::get_instance()->view_header( 'patcher', $admin_config, $title );
            
            $reach_success = true;
            if ( ( isset( $_GET['action'] ) && 'refresh' == $_GET['action'] ) || ! $this->check_transients() ) {
                require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/importer/importer-api.php' );
                $importer_api = new Alpha_Importer_API();
                $reach_success = $importer_api->get_patch_files();
                $this->patches = $reach_success;
                $this->set_transient();
            } else {
                $this->get_transient();
            }

            if ( ! $reach_success ) {
                $atts = false;
            } else {
                $this->get_filter_patches();
                $atts = $this->patched_data;
            }

			include alpha_framework_path( ALPHA_PATCHER . '/views/index.php' );

			Alpha_Admin_Panel::get_instance()->view_footer( 'patcher', $admin_config );
        }

        public function check_patches() {
            $data_patches = get_site_option( self::$transient_name );
            $server_patches = $this->check_transients();
            if ( !$server_patches ) {
                $importer_api = new Alpha_Importer_API();
                $server_patches = $importer_api->get_patch_files();
                if ( ! $server_patches ) {
                    return false;
                }
                $this->patches = $server_patches;
                $this->set_transient();
            }

            // database and patches are empty
            if ( !$data_patches && empty( $server_patches['update'] ) && empty( $server_patches['delete'] ) ) {
                return false;
            }

            // patched files are the same as server patches
            if ( !empty( $server_patches ) && ( $server_patches == $data_patches ) ) {
                return false;
            }
            return true;
        }

        /**
         * Set & Update the transient
         *
         * @since 1.3.0
         */
        public function set_transient() {
            if ( ! empty ( $this->patches ) ) {
                // How often to check for updates
                set_site_transient( self::$transient_name, $this->patches, DAY_IN_SECONDS );
            }
        }

        /**
         * Get the transient
         *
         * @since 1.3.0
         */
        public function get_transient() {
            $this->patches = get_site_transient( self::$transient_name );
        }

        /**
         * Get the applied patches from database
         *
         * @since 1.3.0
         */
        public function get_applied_patches() {
            // Get patched files from database
            $data = get_site_option( self::$transient_name );

            if ( ! empty( $data ) ) {
                if ( ( ALPHA_VERSION != $data['theme_version'] ) || ( defined( 'ALPHA_CORE_VERSION ' ) && ALPHA_CORE_VERSION != $data['func_version'] ) ) {
                    delete_site_option( self::$transient_name );
                    $data = array();
                }
            } else {
                $data = array();
            }
            return $data;
        }

        /**
         * Get the new patches
         *
         * @since 1.3.0
         */
        public function get_filter_patches() {
            $legacy_patches = $this->get_applied_patches();
            if ( ! empty( $this->patches ) ) {
                $this->patched_data = $this->patches;
                foreach ( $this->patches as $key => $patches ) {
                    if ( 'update' == $key ) {
                        $legacy_updated_patches = ! empty( $legacy_patches['update'] ) ? $legacy_patches['update'] : array();
                        foreach ( $patches as $file_path => $value ) {
                            if ( ! empty( $legacy_updated_patches[ $file_path ] ) ) {
                                $patch =  $legacy_updated_patches[ $file_path ];
                                if ( $patch['patch_version'] == $value['patch_version'] ) {
                                    unset( $this->patched_data['update'][ $file_path ] );
                                }
                            }
                        }
                    } else if ( ( 'delete' == $key ) && ! empty( $file_path ) ) {
                        $delete_files = ! empty( $legacy_patches['delete'] ) ? $legacy_patches['delete'] : array();
                        foreach ( $delete_files as $path => $target ) {
                            if ( array_key_exists( $path, $this->patches['delete'] ) ) {
                                unset( $this->patched_data['delete'][ $path ] );
                            }
                        }
                    }
                }
            }
        }

        /**
         * Transient is existed, return transient
         * 
         * @since 1.3.0
         */
        public function check_transients() {
            $legacy_transient = get_site_transient( self::$transient_name );
            if ( ! empty( $legacy_transient ) ) {
                if ( ( ALPHA_VERSION == $legacy_transient['theme_version'] ) && ( defined( 'ALPHA_CORE_VERSION ' ) && ALPHA_CORE_VERSION == $legacy_transient['func_version'] ) ) {
                    return $legacy_transient;
                } else {
                    // Version updated
                    $this->reset_saved_patches();
                    return false;
                }
            }
            return false;
        }

        /**
         * Clear the transient
         *
         * @since 1.3.0
         */
        public function reset_saved_patches() {
            // delete transient
            delete_site_transient( self::$transient_name );
            // delete patched log in database
            if ( get_site_option( self::$transient_name ) ) {
                delete_site_option( self::$transient_name );
            }
        }

        /**
         * Apply patches and update database
         * 
         * @since 1.3.0
         */
        public function apply_patches() {
            // filesystem
            global $wp_filesystem;
            // initialize the WordPress filesystem, no more using file_put_contents function
            if ( empty( $wp_filesystem ) ) {
                require_once ABSPATH . '/wp-admin/includes/file.php';
                WP_Filesystem();
            }

            // path for parent directory of alpha-theme root directory - .../wp-content/themes/
            $theme_dir = wp_normalize_path( dirname( ALPHA_PATH ) ) . '/';
            // path for parent directory of alpha-core root directory - .../wp-content/plugins/
            if ( defined( 'ALPHA_CORE_FILE' ) ) {
                $plugin_folder = basename( dirname( ALPHA_CORE_FILE ) );
                $func_dir      = substr( wp_normalize_path( dirname( ALPHA_CORE_FILE ) ), 0, - strlen( $plugin_folder ) );
            }
            // get transient
            $this->get_transient();
            // get unapplied patches
            $this->get_filter_patches();
            $this->patched_data['error'] = false;

            $patches_data = array();
            foreach ( $this->patched_data as $action => $patches ) {
                if ( 'update' == $action && ! empty( $patches ) ) {
                    foreach ( $patches as $file_path => $value ) {
                        // get patch files content from server
                        $patches_data[ $file_path ] = $value['patch_path'];
                    }
                } elseif( 'delete' == $action && ! empty( $patches ) ) {
                    foreach ( $patches as $path => $target ) {
                        $status = true;
                        if ( 'theme' == $target ) {
                            $status = $this->delete_file( $theme_dir . $path );
                        } elseif ( 'functionality' == $target ) {
                            $status = $this->delete_file( $func_dir . $path );
                        }
                        if ( ! $status ) {
                            unset( $this->patches['delete'][$path] );
                            unset( $this->patched_data['delete'][$path] );
                            $this->patched_data['error'] = true;
                        }
                    }
                }
            }
          
            if ( ! empty( $patches_data ) ) {
                // apply patches except delete action
                require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/importer/importer-api.php' );
                $importer_api = new Alpha_Importer_API();
                $response     = $importer_api->get_patch_content( $patches_data );


                foreach( $this->patched_data['update'] as $file_path => $value ) {
                    if ( isset( $response[$file_path] ) ) {
                        $status = true;
                        if ( 'theme' == $value['target'] ) {
                            $status = $this->write_file( $theme_dir . $file_path, $response[$file_path]  );
                        } elseif ( 'functionality' == $value['target'] ) {
                            $status = $this->write_file( $func_dir . $file_path, $response[$file_path]  );
                        }
                    } else {
                        $status = false;
                    }

                    if ( ! $status ) {
                        unset($this->patched_data['update'][$file_path]);
                        unset($this->patches['update'][$file_path]);
                        $this->patched_data['error'] = true;
                    }
                }
            }
            
            // $this->patched_data['error'] = true;

            if ( false == $this->patched_data['error'] ) {
                // Save Patches only no errors
                update_site_option( self::$transient_name, $this->patches );
            } else {
                // Save Patches with errors
                $legacy_patches = $this->get_applied_patches();
                $this->patches['changelog'] = empty( $legacy_patches['changelog'] ) ? array() : $legacy_patches['changelog'];

                update_site_option( self::$transient_name, $this->patches );
            }

            wp_send_json_success( $this->patched_data );
            die;
        }

        /**
         * Create File - If the file isn't existed, create folder and file
         * 
         * @since 1.3.0
         */
        public function write_file( $file_path, $response ) {
            global $wp_filesystem;
            if ( ! $wp_filesystem->exists( $file_path ) ) {
                $pos = strripos( $file_path, '/' );

                if ( ! wp_mkdir_p( substr( $file_path, 0, $pos ) ) ) {
                    return false;
                }
            }
            return $wp_filesystem->put_contents( $file_path, $response, FS_CHMOD_FILE );
        }

        /**
         * Delete File
         * 
         * @since 1.3.0
         */
        public function delete_file( $file_path ) {
            global $wp_filesystem;
            if ( $wp_filesystem->is_dir( $file_path ) ) {
                return $wp_filesystem->rmdir( $file_path, true );
            } else if ( $wp_filesystem->is_file( $file_path )  ) {
                return $wp_filesystem->delete( $file_path );
            } else {
                // File is not existed
                return true;
            }
        }
	}
endif;

add_action( 'after_setup_theme', 'alpha_patcher', 10 );

if ( ! function_exists( 'alpha_patcher' ) ) :
	function alpha_patcher() {
		$instance = Alpha_Patcher::get_instance();
	}
endif;
