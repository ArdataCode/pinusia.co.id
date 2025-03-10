<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( defined( 'ALPHA_CORE_VERSION' ) ) {
	wp_enqueue_style( 'alpha-tab', alpha_core_framework_uri( '/widgets/tab/tab' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ), array(), ALPHA_CORE_VERSION );
}

?>

<div class="login-popup" id="customer_login">
	<?php do_action( 'woocommerce_before_customer_login_form' ); ?>
	<div class="tab tab-underline tab-nav-fill form-tab">
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
				<a href="signin" class="justify-content-center nav-link active" data-toggle="tab"><?php esc_html_e( 'Login', 'alpha' ); ?></a>
			</li>
		<?php if ( 'yes' == get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
			<li class="nav-item">
				<a href="signup" class="justify-content-center nav-link" data-toggle="tab"><?php esc_html_e( 'Register', 'alpha' ); ?></a>
			</li>
		<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="signin">
				<form class="woocommerce-form woocommerce-form-login login" method="post">

					<?php do_action( 'woocommerce_login_form_start' ); ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="username"><?php esc_html_e( 'Username or email address', 'alpha' ); ?></label>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" required value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
					</p>
					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="password"><?php esc_html_e( 'Password', 'alpha' ); ?></label>
						<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required />
					</p>

					<?php do_action( 'woocommerce_login_form' ); ?>

					<div class="form-row form-footer">
						<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme form-checkbox">
							<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'alpha' ); ?></span>
						</label>
						<p class="woocommerce-LostPassword lost_password">
							<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Forgot Password?', 'alpha' ); ?></a>
						</p>
					</div>

					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="woocommerce-button button woocommerce-form-login__submit btn-dark" name="login" value="<?php esc_attr_e( 'LOGIN', 'alpha' ); ?>"><?php esc_html_e( 'LOGIN', 'alpha' ); ?></button>

					<p class="submit-status"></p>

					<?php do_action( 'woocommerce_login_form_end' ); ?>

				</form>
			</div>

		<?php if ( 'yes' == get_option( 'woocommerce_enable_myaccount_registration' ) ) : ?>
			<div class="tab-pane" id="signup">
				<form method="post" class="woocommerce-form woocommerce-form-login register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

					<?php do_action( 'woocommerce_register_form_start' ); ?>

					<?php if ( 'no' == get_option( 'woocommerce_registration_generate_username' ) ) : ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_username"><?php esc_html_e( 'Username', 'alpha' ); ?></label>
							<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" required value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
						</p>

					<?php endif; ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="reg_email"><?php esc_html_e( 'Your Email address', 'alpha' ); ?></label>
						<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" required value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" /><?php // @codingStandardsIgnoreLine ?>
					</p>

					<?php if ( 'no' == get_option( 'woocommerce_registration_generate_password' ) ) : ?>

						<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
							<label for="reg_password"><?php esc_html_e( 'Password', 'alpha' ); ?></label>
							<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required />
						</p>

					<?php else : ?>

						<p><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'alpha' ); ?></p>

					<?php endif; ?>

					<?php do_action( 'woocommerce_register_form' ); ?>

					<?php
					/**
					 * Fires for register form.
					 *
					 * @since 1.0
					 */
					do_action( 'alpha_register_form' );
					?>

					<?php
					/* translators: opening and ending p tag */
					$text = sprintf( esc_html__( '%1$sAccept the Terms and %2$s', 'alpha' ), '<p class="custom-checkbox"><input type="checkbox" id="register-policy" required=""> <label for="register-policy">', '[privacy_policy]</label></p>' );
					echo wpautop( wc_replace_policy_page_link_placeholders( $text ) );
					?>

					<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
					<button type="submit" class="woocommerce-Button woocommerce-button button woocommerce-form-register__submit btn-dark" name="register" value="<?php esc_attr_e( 'Register', 'alpha' ); ?>"><?php esc_html_e( 'Register', 'alpha' ); ?></button>

					<p class="submit-status"></p>

					<?php do_action( 'woocommerce_register_form_end' ); ?>

				</form>
			</div>
		<?php endif; ?>
		</div>
	</div>

	<?php
	/**
	 * Fires after customer login form.
	 *
	 * @since 1.0
	 */
	do_action( 'alpha_after_customer_login_form' );
	?>
</div>
