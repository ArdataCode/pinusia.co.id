<?php
/**
 * Initialize TGM plugins
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage theme
 * @since      1.0
 */

// Do not allow directly accessing this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Direct script access denied.' );
}

class Alpha_TGM_Plugins {

	/**
	 * Array of plugins. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 *
	 * @var   array
	 * @since 1.0
	 */
	protected $plugins;

	/**
	 * Constructor
	 *
	 * Init plugins and register actions and filters.
	 *
	 * @since 1.0
	 */
	public function __construct() {
		$this->plguins = ALPHA_ENVATO_CODE ? array(
			array(
				'name'       => ALPHA_PRO_NAME,
				'slug'       => ALPHA_PRO_SLUG,
				'required'   => true,
				'url'        => ALPHA_PRO_PLUGIN_URI,
				'image_url'  => ALPHA_ASSETS . '/images/admin/plugins/' . ALPHA_PRO_SLUG . '.png',
				'visibility' => 'setup_wizard',
			)
		) : array();
		$this->plugins = apply_filters(
			'alpha_plugin',
			array_merge(
				$this->plguins,
				array(
					array(
						'name'       => ALPHA_CORE_NAME,
						'slug'       => ALPHA_CORE_SLUG,
						'required'   => ALPHA_ENVATO_CODE ? true : false,
						'url'        => ALPHA_CORE_PLUGIN_URI,
						'image_url'  => ALPHA_ASSETS . '/images/admin/plugins/' . ALPHA_CORE_SLUG . '.png',
						'visibility' => 'setup_wizard',
					),
					array(
						'name'       => 'Kirki',
						'slug'       => 'kirki',
						'required'   => ALPHA_ENVATO_CODE ? true : false,
						'url'        => 'kirki/kirki.php',
						'image_url'  => ALPHA_ASSETS . '/images/admin/plugins/kirki.png',
						'visibility' => 'setup_wizard',
					),
					array(
						'name'       => 'Woocommerce',
						'slug'       => 'woocommerce',
						'required'   => false,
						'url'        => 'woocommerce/woocommerce.php',
						'image_url'  => ALPHA_ASSETS . '/images/admin/plugins/woocommerce.png',
						'visibility' => 'setup_wizard',
					),
					array(
						'name'       => 'Meta-Box',
						'slug'       => 'meta-box',
						'required'   => ALPHA_ENVATO_CODE ? true : false,
						'url'        => 'meta-box/meta-box.php',
						'image_url'  => ALPHA_ASSETS . '/images/admin/plugins/meta_box.png',
						'visibility' => 'setup_wizard',
					),
					array(
						'name'       => 'Elementor',
						'slug'       => 'elementor',
						'required'   => ALPHA_ENVATO_CODE ? true : false,
						'url'        => 'elementor/elementor.php',
						'image_url'  => ALPHA_ASSETS . '/images/admin/plugins/elementor.png',
						'visibility' => 'setup_wizard',
					),
					array(
						'name'       => 'Advanced Custom Fields',
						'slug'       => 'advanced-custom-fields',
						'required'   => false,
						'url'        => 'advanced-custom-fields/acf.php',
						'image_url'  => ALPHA_ASSETS . '/images/admin/plugins/acf.png',
						'visibility' => 'setup_wizard',
					),
					array(
						'name'       => 'Post Types Unlimited',
						'slug'       => 'post-types-unlimited',
						'required'   => false,
						'url'        => 'post-types-unlimited/post-types-unlimited.php',
						'image_url'  => ALPHA_ASSETS . '/images/admin/plugins/unlimited.png',
						'visibility' => 'setup_wizard',
					),
					array(
						'name'       => 'WPForms Lite',
						'slug'       => 'wpforms-lite',
						'required'   => false,
						'url'        => 'wpforms-lite/wpforms.php',
						'image_url'  => ALPHA_ASSETS . '/images/admin/plugins/wpform.png',
						'visibility' => 'setup_wizard',
					),
					array(
						'name'       => 'YITH Woocommerce Wishlist',
						'slug'       => 'yith-woocommerce-wishlist',
						'required'   => false,
						'image_url'  => ALPHA_ASSETS . '/images/admin/plugins/yith_wishlist.png',
						'url'        => 'yith-woocommerce-wishlist/init.php',
						'visibility' => 'setup_wizard',
					),
					array(
						'name'       => 'Contact Form 7',
						'slug'       => 'contact-form-7',
						'required'   => false,
						'url'        => 'contact-form-7/wp-contact-form-7.php',
						'image_url'  => ALPHA_ASSETS . '/images/admin/plugins/contact-form-7.png',
						'visibility' => 'setup_wizard',
					),
					array(
						'name'       => 'WP Super Cache',
						'slug'       => 'wp-super-cache',
						'required'   => false,
						'url'        => 'wp-super-cache/wp-cache.php',
						'visibility' => 'optimize_wizard',
						'desc'       => esc_html__( 'This plugin generates static html files from your dynamic WordPress blog.', 'alpha' ),
					),
					array(
						'name'       => 'Fast Velocity Minify',
						'slug'       => 'fast-velocity-minify',
						'required'   => false,
						'url'        => 'fast-velocity-minify/fvm.php',
						'visibility' => 'optimize_wizard',
						'desc'       => esc_html__( 'This plugin reduces HTTP requests by merging CSS & Javascript files into groups of files, while attempting to use the least amount of files as possible.', 'alpha' ),
					),
				)
			)
		);
		/*************************/
		/* TGM Plugin Activation */
		/*************************/
		$plugin = alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/plugins/tgm-plugin-activation/class-tgm-plugin-activation.php' );
		if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
			require_once $plugin;
		}
		add_action( 'tgmpa_register', array( $this, 'register_required_plugins' ) );
		add_filter( 'tgmpa_notice_action_links', array( $this, 'update_action_links' ), 10, 1 );

		$this->plugins = $this->get_plugins_list();
	}

	/**
	 * Get plugins list including transient.
	 *
	 * @return array All plugins list.
	 * @since 1.0
	 */
	public function get_plugins_list() {
		// get transient
		$plugins = get_site_transient( 'alpha_plugins' );
		if ( false === $plugins ) {
			$plugins = $this->update_plugins_list();
		}
		if ( ! $plugins ) {
			return $this->plugins;
		}

		if ( ! ALPHA_ENVATO_CODE ) {
			foreach ( $plugins as $key => $plugin ) {
				$plugins[ $key ][ 'required' ] = false;
			}
		}
		
		return array_merge( $plugins, $this->plugins );
	}

	/**
	 * Set transient to plugins list.
	 *
	 * @return array Plugins list.
	 * @since 1.0
	 */
	private function update_plugins_list() {

		require_once alpha_framework_path( ALPHA_FRAMEWORK_ADMIN . '/importer/importer-api.php' );
		$importer_api = new Alpha_Importer_API();
		$args         = $importer_api->generate_args( false );
		$url          = $importer_api->get_url( 'plugins_version' );

		if ( isset( $args['code'] ) ) {
			$url = add_query_arg( 'code', $args['code'], $url );
		}
		$url = add_query_arg( 'template', get_template(), $url );

		$plugins = $importer_api->get_response( $url );
		if ( ! $plugins || is_wp_error( $plugins ) ) {
			if ( is_wp_error( $plugins ) ) {
				set_transient( 'alpha_purchase_code_error_msg', $plugins->get_error_message(), HOUR_IN_SECONDS * 24 * 7 );
			}
			set_site_transient( 'alpha_plugins', array(), HOUR_IN_SECONDS * 24 * 7 );
			return false;
		}
		delete_transient( 'alpha_purchase_code_error_msg' );
		setcookie( 'alpha_dismiss_code_error_msg', '', time() - 3600 );

		foreach ( $plugins as $key => $plugin ) {
			if ( ! ALPHA_ENVATO_CODE && ! Alpha_Admin::get_instance()->is_registered() && ( ALPHA_PRO_SLUG == $plugin['slug'] || 'alpus-elementor-addon' == $plugin['slug'] ) ) {
				continue;
			}
			$args['plugin']                = $plugin['slug'];
			$plugins[ $key ]['source']     = add_query_arg( $args, $importer_api->get_url( 'plugins' ) );
			$plugins[ $key ]['image_url']  = ALPHA_ASSETS . '/images/admin/plugins/' . $args['plugin'] . '.png';

			if ( $plugin['required'] ) {
				$plugins[ $key ]['visibility'] = 'setup_wizard';
			}
		}
		// set transient
		set_site_transient( 'alpha_plugins', $plugins, 7 * 24 * HOUR_IN_SECONDS );

		return $plugins;
	}

	/**
	 * Register required plugins.
	 *
	 * @since 1.0
	 */
	public function register_required_plugins() {
		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 *
		 * @var   array
		 * @since 1.0
		 */
		$config = array(
			'domain'       => 'alpha',                    // Text domain - likely want to be the same as your theme.
			'default_path' => '',                          // Default absolute path to pre-packaged plugins
			'menu'         => 'install-required-plugins',  // Menu slug
			'has_notices'  => true,                        // Show admin notices or not
			'is_automatic' => true,                        // Automatically activate plugins after installation or not
			'message'      => '',                          // Message to output right before the plugins table
		);

		tgmpa( $this->plugins, $config );
	}

	/**
	 * Update plugins action url in setup wizard.
	 *
	 * @param  array $action_links Plugins action link.
	 * @return array               Return updated action link.
	 * @since 1.0
	 */
	public function update_action_links( $action_links ) {
		$url = add_query_arg(
			array(
				'page' => 'alpha-setup-wizard',
				'step' => 'default_plugins',
			),
			self_admin_url( 'admin.php' )
		);
		foreach ( $action_links as $key => $link ) {
			if ( $link ) {
				$link                 = preg_replace( '/<a([^>]*)href="([^"]*)"/i', '<a$1href="' . esc_url( $url ) . '"', $link );
				$action_links[ $key ] = $link;
			}
		}
		return $action_links;
	}
}

new Alpha_TGM_Plugins;
