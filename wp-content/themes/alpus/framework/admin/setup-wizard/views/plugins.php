<?php
/**
 * Plugin panel
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 *
 */
defined( 'ABSPATH' ) || die;
?>
<h2 class="wizard-title"><?php esc_html_e( 'Install Plugins', 'alpha' ); ?></h2>
<p class="wizard-description">
	<?php printf( esc_html__( 'Below is the list of required | recommended plugins used in %s Theme. Please check them for installation.', 'alpha' ), ALPHA_DISPLAY_NAME ); ?>
	<?php esc_html_e( 'Please check the plugins to install:', 'alpha' ); ?>
</p>
<form method="post">
	<?php
	$plugins = $this->_get_plugins();
	$require = array_map(
		function( $item ) {
			return $item['required'];
		},
		$plugins['all']
	);
	array_multisort( $require, SORT_DESC, $plugins['all'] );

	if ( count( $plugins['all'] ) ) {
		?>
		<ul class="alpha-plugins">
			<?php
			$idx      = 0;
			$loadmore = false;
			foreach ( $plugins['all'] as $slug => $plugin ) {
				if ( isset( $plugin['visibility'] ) && 'optimize_wizard' == $plugin['visibility'] ) {
					continue;
				}
				++ $idx;
				?>
				<?php
				if ( $idx > 6 && ! $loadmore ) :
					?>
					<div class="button-load-plugins">
						<a href="#"><b><?php esc_html_e( 'Load More', 'alpha' ); ?></b></i></a>
					</div>
					<?php
					$loadmore = true;
				endif;
				?>
				<li data-slug="<?php echo esc_attr( $slug ); ?>"<?php echo 6 < $idx ? ' class="hidden"' : ''; ?>>
					<div class="plugin-heading">
						<div class="plugin-img"><img src="<?php echo esc_url( $plugin['image_url'] ); ?>"></div>
						<?php echo esc_html( $plugin['name'] ); ?>
					</div>
					<div class="plugin-status">
					<?php
						$key = '';
						if ( isset( $plugins['install'][ $slug ] ) ) {
							$key = esc_html__( 'Installation', 'alpha' );
						} elseif ( isset( $plugins['update'][ $slug ] ) ) {
							$key = esc_html__( 'Update', 'alpha' );
						} elseif ( isset( $plugins['activate'][ $slug ] ) ) {
							$key = esc_html__( 'Activation', 'alpha' );
						}
						if ( $plugin['required'] ) {
							echo '<span class="info required">' . esc_html__( 'Required', 'alpha' ) . '</span>';
						} else {
							echo '<span class="info recommended">' . esc_html__( 'Recommended', 'alpha' ) . '</span>';
						}
						if ( isset( $plugins['update'][ $slug ] ) ) {
							echo '<span class="info update">' . esc_html__( 'Update', 'alpha' ) . '</span>';
						}
					?>
					</div>
					<div class="plugin-version">
					<?php
						$current_version = '';
						$new_version = '';
						if ( isset( $plugins['update'][ $slug ] ) ) {
							$new_version = ! empty( $plugins['update'][ $slug ]['version'] ) ? ( '<span>' . $plugins['update'][ $slug ]['version'] . '</span>' ) : '';
						}
						if ( isset( $plugins['update'][ $slug ] ) || isset( $plugins['activate'][ $slug ] ) ) {
							if ( ! function_exists( 'get_plugins' ) ) {
								require_once ABSPATH . 'wp-admin/includes/plugin.php';
							}
							$installed_plugins = get_plugins();
							$current_version = ! empty( $installed_plugins[ $plugin['url'] ]['Version'] ) ? ( '<span>' . $installed_plugins[ $plugin['url'] ]['Version'] . '</span>' ) : '';
						}

						echo alpha_strip_script_tags( $current_version . $new_version );
					?>
					</div>
					<div class="plugin-action">
						<label class="checkbox checkbox-inline">
							<input type="checkbox" name="setup-plugin"<?php echo ! $plugin['required'] ? '' : ' checked="checked"'; ?>>
							<div class="d-loading small"><i></i></div>
						</label>
					</div>
				</li>
			<?php } ?>
		</ul>
		<div class="use-multiple-editors notice-warning notice-alt notice-large" style="display: none;margin-bottom:0">
			<?php /* translators: $1 and $2 opening and closing bold tags respectively */ ?>
			<?php printf( esc_html__( 'Using %1$sElementor%2$s and %1$sVisual Composer%2$s togther affects your site performance.', 'alpha' ), '<b>', '</b>' ); ?>
		</div>
		<?php
	} else {
		echo '<p class="lead">' . esc_html__( 'Good news! All plugins are already installed and up to date. Please continue.', 'alpha' ) . '</p>';
	}
	?>

	<p class="alpha-admin-panel-actions">
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-dark button button-large button-next" data-callback="install_plugins"><?php esc_html_e( 'Continue', 'alpha' ); ?></a>
		<?php wp_nonce_field( 'alpha-setup-wizard' ); ?>
	</p>
</form>
