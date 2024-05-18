<?php
/**
 * Alpha Tools
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */

// Direct access is denied
defined( 'ABSPATH' ) || die;

class Alpha_Tools extends Alpha_Base {

	/**
	 * The Page slug
	 *
	 * @since 1.0
	 * @access public
	 */
	public $page_slug = 'alpha-tools';

	/**
	 * The Result
	 *
	 * @since 1.0
	 * @access public
	 */
	private $result;

	/**
	 * Constructor
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {
		if ( ! current_user_can( 'administrator' ) || ! isset( $_REQUEST['page'] ) || $this->page_slug != $_REQUEST['page'] ) {
			return;
		}

		$this->handle_request();

		if ( isset( $_REQUEST['page'] ) && $this->page_slug == $_REQUEST['page'] ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}
	}


	/**
	 * Enqueue scripts for tools.
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'alpha-tools', alpha_framework_uri( '/admin/tools/tools' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_VERSION );
	}

	/**
	 * Handle request to execute tools
	 *
	 * @since 1.0
	 * @access public
	 */
	public function handle_request() {

		$tools = $this->get_tools();

		$result_success = true;
		$message        = '';
		if ( ! empty( $_GET['action'] ) ) {
			if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'alpha-tools' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'alpha' ) );
			}

			$action = wp_unslash( $_GET['action'] ); // WPCS: input var ok.

			if ( array_key_exists( $action, $tools ) ) {

				$this->result = $this->execute_tool( $action );

				$tool = $tools[ $action ];
				$tool = array(
					'id'          => $action,
					'name'        => $tool['action_name'],
					'action'      => $tool['button_text'],
					'description' => $tool['description'],
				);
				$tool = array_merge( $tool, $this->result );

				/**
				 * Fires after a Alpha tool has been executed.
				 *
				 * @param array  $tool  Details about the tool that has been executed.
				 */
				do_action( 'alpha_tool_executed', $tool );
			} else {
				$this->result = array(
					'success' => false,
					'message' => esc_html__( 'Tool does not exist.', 'alpha' ),
				);
			}
		}
	}

	/**
	 * Refresh all blocks
	 *
	 * @since 1.0
	 * @access public
	 */
	public function refresh_blocks() {

	}

	/**
	 * Get available Tools
	 *
	 * @since 1.0
	 * @access public
	 */
	public function get_tools() {
		$tools = array(
			'clear_merge_css_js'      => array(
				'action_name' => esc_html__( 'Merged css and js files', 'alpha' ),
				'button_text' => esc_html__( 'Clear resources', 'alpha' ),
				'description' => esc_html__( 'This tool will clear the all combined stylesheets and javascripts.', 'alpha' ),
			),
			'clear_transients'        => array(
				'action_name' => esc_html__( 'Addon transients', 'alpha' ),
				'button_text' => esc_html__( 'Clear transients', 'alpha' ),
				'description' => sprintf( esc_html__( 'This tool will clear the %s Addon features(Brand, Vendor, Patcher etc) transients cache.', 'alpha' ), ALPHA_DISPLAY_NAME ),
			),
			'clear_plugin_transients' => array(
				'action_name' => esc_html__( 'Plugin transients', 'alpha' ),
				'button_text' => esc_html__( 'Clear transients', 'alpha' ),
				'description' => sprintf( esc_html__( 'This tool will clear the plugin(%s Core Plugin, WPBakery Page Builder) update transients cache.', 'alpha' ), ALPHA_DISPLAY_NAME ),
			),
			'clear_studio_transient'  => array(
				'action_name' => esc_html__( 'Studio block transients', 'alpha' ),
				'button_text' => esc_html__( 'Clear transients', 'alpha' ),
				'description' => sprintf( esc_html__( 'This tool will clear the %s Studio block transients cache.', 'alpha' ), ALPHA_DISPLAY_NAME ),
			),
		);

		/**
		 * Filters available tools.
		 *
		 * @since 1.0
		 */
		return apply_filters( 'alpha_admin_get_tools', $tools );
	}

	/**
	 * Execute tool
	 *
	 * @since 1.0
	 * @access public
	 */
	public function execute_tool( $tool ) {
		$ran = true;
		switch ( $tool ) {
			case 'clear_transients':
				alpha_clear_transient();
				$message = __( 'Addon transients are cleared.', 'alpha' );
				break;
			case 'clear_plugin_transients':
				delete_site_transient( 'alpha_plugins' );
				delete_site_transient( 'alpha_theme_rollback_versions' );
				delete_site_transient( 'alpha_plugin_rollback_versions' );
				$message = __( 'Plugin transients are cleared.', 'alpha' );
				break;
			case 'clear_studio_transient':
				delete_site_transient( 'alpha_blocks_e' );
				delete_site_transient( 'alpha_blocks_g' );
				delete_site_transient( 'alpha_blocks_w' );
				delete_site_transient( 'alpha_block_categories_e' );
				delete_site_transient( 'alpha_block_categories_g' );
				delete_site_transient( 'alpha_block_categories_w' );
				$message = sprintf( esc_html__( '%s Studio transients are cleared.', 'alpha' ), ALPHA_DISPLAY_NAME );
				break;
			case 'clear_merge_css_js':
				$upload_dir  = wp_upload_dir();
				$upload_path = $upload_dir['basedir'] . '/' . ALPHA_NAME . '_merged_resources/';
				if ( file_exists( $upload_path ) ) {
					foreach ( scandir( $upload_path ) as $file ) {
						if ( ! is_dir( $file ) ) {
							unlink( $upload_path . $file );
						}
					}
					rmdir( $upload_path );
				}
				$message = esc_html__( 'Merged javascripts and stylesheets are all cleared.', 'alpha' );
				break;
			case 'refresh_blocks':
				$this->refresh_blocks();
				$message = esc_html__( 'Refreshed successfully.', 'alpha' );
				break;
			default:
				$tools = $this->get_tools();
				if ( isset( $tools[ $tool ]['callback'] ) ) {
					$callback = $tools[ $tool ]['callback'];
					$return   = call_user_func( $callback );
					if ( is_string( $return ) ) {
						$message = $return;
					} elseif ( false === $return ) {
						$callback_string = is_array( $callback ) ? get_class( $callback[0] ) . '::' . $callback[1] : $callback;
						$ran             = false;
						/* translators: %s: callback string */
						$message = sprintf( esc_html__( 'There was an error calling %s', 'alpha' ), $callback_string );
					} else {
						$message = esc_html__( 'Tool ran.', 'alpha' );
					}
				} else {
					$ran     = false;
					$message = __( 'There was an error calling this tool. There is no callback present.', 'alpha' );
				}
				/**
				 * Fires after setting default execute options.
				 *
				 * @since 1.0
				 */
				do_action( 'alpha_execute_tool', $this, $tool );
				break;
		}

		return array(
			'success' => $ran,
			'message' => $message,
		);
	}


	/**
	 * Render tools page
	 *
	 * @since 1.0
	 * @access public
	 */
	public function view_tools() {
		$admin_config = Alpha_Admin::get_instance()->admin_config;
		$title        = array(
			'title' => esc_html__( 'Management Tools', 'alpha' ),
			'desc'  => sprintf( esc_html__( 'Keep your site health instantly using %s Tools.', 'alpha' ), ALPHA_DISPLAY_NAME ),
		);
		Alpha_Admin_Panel::get_instance()->view_header( 'tools', $admin_config, $title );

		$tools = $this->get_tools();
		$nonce = wp_create_nonce( 'alpha-tools' );
		?>		
		<div class="alpha-admin-panel-body alpha-available-tools">
			<?php
			if ( isset( $this->result ) ) {
				if ( $this->result['success'] ) {
					echo '<div class="alpha-notify updated inline"><p>' . esc_html( $this->result['message'] ) . '</p></div>';
				} else {
					echo '<div class="alpha-notify error inline"><p>' . esc_html( $this->result['message'] ) . '</p></div>';
				}
			}
			?>
			<table class="wp-list-table widefat" id="alpha_tools_table">
				<thead>
					<tr>
						<th scope="col" id="title" class="manage-column column-title column-primary"><?php esc_html_e( 'Action Name', 'alpha' ); ?></th>
						<th scope="col" id="remove" class="manage-column column-remove"><?php esc_html_e( 'Action', 'alpha' ); ?></th>
					</tr>
				</thead>
				<tbody id="the-list">
					<?php foreach ( $tools as $action => $tool ) : ?>
						<tr class="<?php echo sanitize_html_class( $action ); ?>">
							<th>
								<strong class="action-name"><?php echo esc_html( $tool['action_name'] ); ?></strong>
								<p class="description"><?php echo alpha_strip_script_tags( $tool['description'] ); ?></p>
							</th>
							<td class="run-tool">
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=alpha-tools&action=' . $action . '&&_wpnonce=' . $nonce ) ); ?>" class="button button-large button-outline <?php echo esc_attr( $action ); ?>"><?php echo esc_html( $tool['button_text'] ); ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
		Alpha_Admin_Panel::get_instance()->view_footer( 'tools', $admin_config );
	}
}

Alpha_Tools::get_instance();
