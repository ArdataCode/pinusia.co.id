<?php
/**
 * Header template in admin panel
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

$userinfo     = wp_get_current_user();
$errors       = get_option( 'alpha_register_error_msg' );
$is_activated = Alpha_Admin::get_instance()->is_registered() && ! $errors;

?>

<div class="alpha-wrap<?php echo alpha_escaped( $active_page ? ' alpha-' . $active_page : '' ); ?>"> <!-- Begin .alpha-wrap -->
	<?php if ( ! ( isset( $_REQUEST['page'] ) && 'alpha-layout-builder' == $_REQUEST['page'] && isset( $_REQUEST['is_elementor_preview'] ) && $_REQUEST['is_elementor_preview'] ) ) : // Hide if called in elementor preview ?>
	<div class="alpha-admin-header">
		<div class="alpha-admin-logo">
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=alpha' ) ); ?>">
				<?php echo '<img src="' . esc_url( alpha_get_option( 'white_label_logo' ) ? alpha_get_option( 'white_label_logo' ) : ALPHA_URI . '/assets/images/logo-white.png' ) . '"' . ( alpha_get_option( 'white_label_logo' ) ? '' : ' data-image-src="' . esc_attr( ALPHA_URI . '/assets/images/' ) . '"' ) . ' alt="' . esc_attr( get_bloginfo( 'name', 'display' ) ) . '"/>'; ?>
				<span class="alpha-version"><?php echo esc_html__( 'Version', 'alpha' ) . ' ' . ALPHA_VERSION; ?></span>
			</a>
		</div>
		<?php if ( ! empty( $admin_config['admin_navs'] ) ) : ?>
		<nav class="alpha-admin-nav">
			<?php
			foreach ( $admin_config['admin_navs'] as $key => $item ) {
				$url       = isset( $item['url'] ) ? $item['url'] : '#';
				$label     = isset( $item['label'] ) ? $item['label'] : '';
				$icon      = isset( $item['icon'] ) ? $item['icon'] : 'fas fa-th-large';
				$icon_html = ! isset( $item['is_svg'] ) ? '<i class="' . esc_attr( $icon ) . '"></i>' : esc_html( $icon );
				if ( empty( $item['submenu'] ) ) {
					$is_active = $key === $active_page ? ' active' : '';
					?>
					<a class="alpha-admin-nav-panel <?php echo esc_attr( $key . $is_active ); ?>" href="<?php echo esc_url( $url ); ?>">
						<?php echo alpha_strip_script_tags( $icon_html ); ?>
						<label><?php echo esc_html( $label ); ?></label>
					</a>
					<?php
				} elseif ( 1 == count( $item['submenu'] ) ) {
					foreach ( $item['submenu'] as $subkey => $subitem ) {
						$url       = isset( $subitem['url'] ) ? $subitem['url'] : '#';
						$label     = isset( $subitem['label'] ) ? $subitem['label'] : '';
						$is_active = $subkey === $active_page ? ' active' : '';
						?>
						<a class="alpha-admin-nav-panel <?php echo esc_attr( $subkey . $is_active ); ?>" href="<?php echo esc_url( $url ); ?>">
							<?php echo alpha_strip_script_tags( $icon_html ); ?>
							<label><?php echo esc_html( $label ); ?></label>
						</a>
						<?php
					}
				} else {
					$is_active = false !== array_search( $active_page, array_keys( $item['submenu'] ) ) ? ' active' : '';
					?>
					<div class="alpha-admin-nav-panel has-menu <?php echo esc_attr( $key . $is_active ); ?>">
						<?php echo alpha_strip_script_tags( $icon_html ); ?>
						<label><?php echo esc_html( $label ); ?></label>
						<div class="alpha-admin-subnavs">
						<?php
						foreach ( $item['submenu'] as $subkey => $subitem ) {
							$url       = isset( $subitem['url'] ) ? $subitem['url'] : '#';
							$label     = isset( $subitem['label'] ) ? $subitem['label'] : '';
							$icon      = isset( $subitem['icon'] ) ? $subitem['icon'] : 'fas fa-th-large';
							$desc      = isset( $subitem['desc'] ) ? $subitem['desc'] : '';
							$icon_html = ! isset( $subitem['is_svg'] ) ? '<i class="' . esc_attr( $icon ) . '"></i>' : $icon;
							?>
							<a class="alpha-admin-subnav <?php echo esc_attr( $subkey ); ?>" href="<?php echo esc_url( $url ); ?>">
								<?php echo alpha_strip_script_tags( $icon_html ); ?>
								<label>
									<?php echo esc_html( $label ); ?>
									<span><?php echo esc_html( $desc ); ?></span>
								</label>
							</a>	
							<?php
						}
						?>
						</div>
					</div>
					<?php
				}
			}
			?>
		</nav>
		<?php endif; ?>
		<div class="alpha-active-status">
			<?php if ( ! ALPHA_ENVATO_CODE && ! defined( 'ALPHA_PRO_VERSION' ) ) { ?>
			<a href="<?php echo esc_url( ALPHA_GET_PRO_URI ); ?>" class="alpha-active-toggle activated" target="_blank">
				<?php esc_html_e( 'Get Pro', 'alpha' ); ?>
			</a>
			<?php } else { ?>
			<a href="<?php echo esc_url( $is_activated ? ALPHA_ADMIN_PAGE . 'alpha-setup-wizard&step=activation' : ALPHA_GET_PRO_URI ); ?>" class="alpha-active-toggle<?php echo esc_attr( $is_activated ? ' activated' : '' ); ?>">
				<?php
				if ( $is_activated ) {
					esc_html_e( 'Activated', 'alpha' );
				} else {
					esc_html_e( 'Not Activated', 'alpha' );
				}
				?>
			</a>
			<?php } ?>
			<div class="alpha-active-content">
			</div>
		</div>
		<!-- <button class="button button-dark button-large alpha-layouts-save"><i class="far fa-save"></i><?php esc_html_e( 'Save Layouts', 'alpha' ); ?></button> -->
	</div>
	<?php endif; ?>

	<div class="alpha-admin-panel"> <!-- Begin .alpha-admin-panel -->

	<?php if ( ! ( isset( $_REQUEST['page'] ) && 'alpha-layout-builder' == $_REQUEST['page'] && isset( $_REQUEST['is_elementor_preview'] ) && $_REQUEST['is_elementor_preview'] ) ) : // Hide if called in elementor preview ?>
		<div class="alpha-admin-panel-header"> <!-- Begin .alpha-admin-panel-header -->
		<?php if ( empty( $title ) ) { ?>
			<h2 class="alpha-panel-title"><?php printf( esc_html__( 'Welcome to %1$s!', 'alpha' ), ALPHA_DISPLAY_NAME ); ?></h2>
			<p class="alpha-running-info"><?php printf( esc_html__( 'Thanks for choosing %1$s. Get ready to build beautiful and attractive website?%2$sWe hope you enjoy it!', 'alpha' ), ALPHA_DISPLAY_NAME, '<br>' ); ?></p>
		<?php } else { ?>
			<h2 class="alpha-panel-title"><?php echo esc_html( $title['title'] ); ?></h2>
			<p class="alpha-running-info"><?php echo esc_html( $title['desc'] ); ?></p>
		<?php } ?>
		</div> <!-- End .alpha-admin-panel-header -->
	<?php endif; ?>
