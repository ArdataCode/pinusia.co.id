<?php
/**
 * Activation template
 *
 * @author     D-Themes
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.2.0
 */
$disable_field = '';
$errors        = get_option( 'alpha_register_error_msg' );
update_option( 'alpha_register_error_msg', '' );
$purchase_code = Alpha_Admin::get_instance()->get_purchase_code_asterisk();
$regist_flag   = false;

?>
	<form id="alpha_registration" method="post">
		<?php
		if ( $purchase_code && ! empty( $purchase_code ) && Alpha_Admin::get_instance()->is_registered() ) {
			$disable_field = ' disabled=true';
		}
		?>
		<input type="hidden" name="alpha_registration" />
		<?php if ( Alpha_Admin::get_instance()->is_envato_hosted() ) : ?>
			<p class="confirm unregister">
				<?php esc_html_e( 'You are using Envato Hosted, this subscription code can not be deregistered.', 'alpha' ); ?>
			</p>
		<?php else : ?>
			<div class="alpha-input-wrapper">
				<input type="text" id="alpha_purchase_code" name="code" class="regular-text alpha-input" value="<?php echo esc_attr( $purchase_code ); ?>" placeholder="<?php esc_attr_e( 'Please register your purchase', 'alpha' ); ?>" <?php echo alpha_escaped( $disable_field ); ?> />
				<?php if ( ! Alpha_Admin::get_instance()->is_registered() ) : ?>
					<a href="javascript:;" class="alpha-toggle-howto"><span>?</span></a>
				<?php elseif ( get_transient( '_alpha_register_redirect' ) ) : ?>
					<?php 
					$redirection = get_transient( '_alpha_register_redirect' );
					delete_transient( '_alpha_register_redirect' );
					?>
					<input type="hidden" id="alpha_register_redirect" value="<?php echo esc_url( $redirection ); ?>">
				<?php endif; ?>
			</div>
			<?php if ( Alpha_Admin::get_instance()->is_registered() ) : ?>
				<input type="hidden" id="alpha_active_action" name="action" value="unregister" data-toggle-html='<?php echo '<i class="admin-svg-key"></i>' . esc_html__( 'Registered', 'alpha' ); ?>' />
				<?php submit_button( esc_html__( 'Deactivate', 'alpha' ), array( 'button-dark', 'large', 'alpha-large-button' ), '', true ); ?>
			<?php else : ?>
				<input type="hidden" id="alpha_active_action" name="action" value="register" data-toggle-html='<?php echo '<i class="admin-svg-key"></i>' . esc_html__( 'Unregistered', 'alpha' ); ?>' />
				<?php submit_button( esc_html__( 'Activate', 'alpha' ), array( 'button-dark', 'large', 'alpha-large-button' ), '', true ); ?>
			<?php endif; ?>
		<?php endif; ?>
		<?php wp_nonce_field( 'alpha-setup-wizard' ); ?>
	</form>
<?php
if ( ! empty( $errors ) ) {
	echo '<div class="notice-error notice-block"><strong style="font-weight: 500"><i class="fa fa-times-circle"></i></strong>' . alpha_escaped( $errors ) . esc_html__( ' Please check purchase code again.', 'alpha' ) . '</div>';
}
if ( ! empty( $purchase_code ) ) {
	if ( ! empty( $errors ) ) {
		echo '<div class="notice-warning notice-block">' . esc_html__( 'Purchase code not updated. We will keep the existing one.', 'alpha' ) . '</div>';
	} else {
		/* translators: $1 and $2 opening and closing strong tags respectively */
		echo '<div class="notice-success notice-block">' . sprintf( esc_html__( '%1$s Welcome! Your product is registered now. Enjoy %2$s Theme and automatic updates.', 'alpha' ), '<strong style="font-weight: 500"><i class="fas fa-check-circle"></i></strong>', ALPHA_DISPLAY_NAME ) . '</div>';
	}
} elseif ( empty( $errors ) ) {
	echo '<div class="notice-block">' . sprintf( esc_html__( 'Thank you for choosing %s theme from ThemeForest. Please register your purchase and make sure that you have fulfilled all of the requirements.', 'alpha' ), ALPHA_DISPLAY_NAME ) . '</div>';
}

if ( ! Alpha_Admin::get_instance()->is_registered() ) :
	?>
	<div class="alpha-active-howto" style="display: none;">
		<h3><?php esc_html_e( 'Where can I find my purchase code?', 'alpha' ); ?></h3>
		<ol>
			<li><?php printf( esc_html__( 'Please go to %1$sThemeForest.net/downloads%2$s', 'alpha' ), '<a target="_blank" href="https://themeforest.net/downloads" rel="noopener noreferrer">', '</a>' ); //phpcs:ignore ?></li>
			<li><?php esc_html_e( 'Click the ', 'alpha' ) . '<strong style="font-weight: 500">' . esc_html__( 'Download', 'alpha' ) . '</strong> ' . sprintf( esc_html__( 'button in %1$s row', 'alpha' ), ALPHA_DISPLAY_NAME ); //phpcs:ignore ?></li>
			<li><?php esc_html_e( 'Select ', 'alpha' ) . '<strong style="font-weight: 500">' . esc_html__( 'License Certificate &amp; Purchase code', 'alpha' ) . '</strong>'; ?></li>
			<li><?php esc_html_e( 'Copy', 'alpha' ) . ' <strong style="font-weight: 500">' . esc_html__( 'Item Purchase Code', 'alpha' ) . '</strong>'; ?></li>
		</ol>
	</div>
	<?php
endif;
