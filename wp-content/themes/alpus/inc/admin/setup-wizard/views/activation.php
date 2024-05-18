<?php
/**
 * Activation template
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
$disable_field = '';
$errors        = get_option( 'alpha_register_error_msg' );
update_option( 'alpha_register_error_msg', '' );
$purchase_code = Alpha_Admin::get_instance()->get_purchase_code_asterisk();
$regist_flag   = false;

if ( ! alpha_doing_ajax() ) {
	?>
<h2 class="wizard-title"><?php esc_html_e( 'Theme License', 'alpha' ); ?></h2>
<p class="wizard-description"><?php printf( esc_html__( 'Thanks for using %1$s Pro. Enter your license key to activate %2$s Pro and get full access to pro features.', 'alpha' ), ALPHA_DISPLAY_NAME, ALPHA_DISPLAY_NAME ); ?></p>
<div class="form-wrapper">
	<?php
}
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
				<label style="font-weight: bold;margin: 0 0 5px; display: block;" for="child-theme"><?php esc_html_e( 'Purchase Code:', 'alpha' ); ?></label>
				<input type="text" id="alpha_purchase_code" name="code" class="regular-text alpha-input" value="<?php echo esc_attr( $purchase_code ); ?>" placeholder="<?php esc_attr_e( 'Please enter your purchase code', 'alpha' ); ?>" <?php echo alpha_escaped( $disable_field ); ?> />
				<?php
				if ( Alpha_Admin::get_instance()->is_registered() && get_transient( '_alpha_register_redirect' ) ) :
					$redirection = get_transient( '_alpha_register_redirect' );
					delete_transient( '_alpha_register_redirect' );
					?>
					<input type="hidden" id="alpha_register_redirect" value="<?php echo esc_url( $redirection ); ?>">
					<?php
				endif;
				?>
			</div>
			<?php if ( Alpha_Admin::get_instance()->is_registered() ) : ?>
				<input type="hidden" id="alpha_active_action" name="action" value="unregister" data-toggle-html='<?php echo '<i class="admin-svg-key"></i>' . esc_html__( 'Activated', 'alpha' ); ?>' />
				<?php submit_button( esc_html__( 'Deactivate', 'alpha' ), array( 'button-danger', 'large', 'alpha-large-button' ), '', true ); ?>
			<?php else : ?>
				<input type="hidden" id="alpha_active_action" name="action" value="register" data-toggle-html='<?php echo '<i class="admin-svg-key"></i>' . esc_html__( 'Not Activated', 'alpha' ); ?>' />
				<?php submit_button( esc_html__( 'Activate', 'alpha' ), array( 'button-primary', 'large', 'alpha-large-button' ), '', true ); ?>
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
		echo '<div class="notice-block">' . sprintf( esc_html__( 'Already have a license? Find the license %1$shere%2$s', 'alpha' ), '<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="blank">', '</a>' ) . '</div>';
	}
	?>
<?php
if ( ! alpha_doing_ajax() ) {
	?>
</div>
<?php
if ( ! ALPHA_ENVATO_CODE ) {
	?>
<div class="free-pro-license-box">
	<div class="license-box free-license-box">
		<h3><?php esc_html_e( 'Free License', 'alpha' ); ?></h3>
		<ul>
			<li><i class="a-icon-times-solid error"></i><span class="label"><?php esc_html_e( 'Limited elements, templates and addons', 'alpha' ); ?></span></li>
			<li><i class="a-icon-times-solid error"></i><span class="label"><?php esc_html_e( 'Access to optimize wizard', 'alpha' ); ?></span></li>
			<li><i class="a-icon-times-solid error"></i><span class="label"><?php echo ALPHA_ENVATO_CODE ? esc_html__( 'Use of pre-made elementor templates', 'alpha' ) : esc_html__( 'Use of limited elementor templates', 'alpha' ); ?></span></li>
			<li><i class="a-icon-times-solid error"></i><span class="label"><?php esc_html_e( 'Premium support and updates', 'alpha' ); ?></span></li>
		</ul>
	</div>
	<div class="license-box pro-license-box">
		<h3><?php esc_html_e( 'Pro License', 'alpha' ); ?></h3>
		<ul>
			<li><i class="a-icon-check-solid"></i><span class="label"><?php esc_html_e( 'Unlimited elements, templates and addons', 'alpha' ); ?></span></li>
			<li><i class="a-icon-check-solid"></i><span class="label"><?php esc_html_e( 'Access to optimize wizard', 'alpha' ); ?></span></li>
			<li><i class="a-icon-check-solid"></i><span class="label"><?php echo ALPHA_ENVATO_CODE ? esc_html__( 'Use of pre-made elementor templates', 'alpha' ) : esc_html__( 'Use of unlimited elementor templates', 'alpha' ); ?></span></li>
			<li><i class="a-icon-check-solid"></i><span class="label"><?php esc_html_e( 'Premium support and updates', 'alpha' ); ?></span></li>
		</ul>
	</div>
</div>
	<?php
}
?>

<p class="alpha-admin-panel-actions">
	<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-dark button-large button-next"><?php esc_html_e( 'Continue', 'alpha' ); ?></a>
</p>
	<?php
}