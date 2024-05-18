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

?>
<div class="alpha-admin-panel-body">
	<div class="alpha-pro-promo">
		<div class="go-pro-img"></div>
		<h2><?php esc_html_e( 'Get Alpus Pro', 'alpha' ); ?></h2>
		<p><?php printf( esc_html__( 'Please install %1$s Pro to get access here. Supercharge your %1$s with Pro Features & Addons that you won\'t find anywhere else.', 'alpha' ), ALPHA_DISPLAY_NAME, ALPHA_DISPLAY_NAME ); ?></p>
		<a href="<?php echo esc_url( ALPHA_GET_PRO_URI ); ?>" class="button button-primary button-large button-next" target="_blank"><?php esc_html_e( 'Get Pro', 'alpha' ); ?></a>
	</div>
</div>
