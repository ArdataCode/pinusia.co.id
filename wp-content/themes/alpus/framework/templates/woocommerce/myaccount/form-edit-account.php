<?php
/**
 * Edit account form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-edit-account.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || die;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<div class="icon-box icon-box-side woocommerce-MyAccount-content-caption">
	<span class="icon-box-icon">
		<i class="<?php echo ALPHA_ICON_PREFIX; ?>-icon-user"></i>
	</span>
	<div class="icon-box-content">
		<h4 class="icon-box-title text-normal"><?php esc_html_e( 'Account Details', 'alpha' ); ?></h4>
	</div>
</div>

<form class="woocommerce-EditAccountForm edit-account row" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first">
		<label for="account_first_name" class="text-body"><?php esc_html_e( 'First name', 'alpha' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</p>
	<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last">
		<label for="account_last_name" class="text-body"><?php esc_html_e( 'Last name', 'alpha' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" />
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_display_name" class="text-body"><?php esc_html_e( 'Display name', 'alpha' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" /> <span><em><?php esc_html_e( 'This will be how your name will be displayed in the account section and in reviews', 'alpha' ); ?></em></span>
	</p>
	<div class="clear"></div>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
		<label for="account_email" class="text-body"><?php esc_html_e( 'Email address', 'alpha' ); ?>&nbsp;<span class="required">*</span></label>
		<input type="email" class="woocommerce-Input woocommerce-Input--email input-text" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
	</p>

	<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide woocommerce-form-row-biography mb-0">
		<label for="user_description" class="text-body"><?php esc_html_e( 'Biography', 'alpha' ); ?></label>		
		<?php wp_editor( $user->description, 'user_description', array( 'quicktags' => true ) ); ?>
	</p>

	<fieldset>
		<legend><?php esc_html_e( 'Password change', 'alpha' ); ?></legend>

		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password_current">
				<span class="text-body"><?php esc_html_e( 'Current password ', 'alpha' ); ?></span>
				<?php esc_html_e( 'leave blank to leave unchanged', 'alpha' ); ?>
			</label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_current" id="password_current" autocomplete="off" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password_1">
				<span class="text-body"><?php esc_html_e( 'New password ', 'alpha' ); ?></span>
				<?php esc_html_e( 'leave blank to leave unchanged', 'alpha' ); ?>
			</label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_1" id="password_1" autocomplete="off" />
		</p>
		<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
			<label for="password_2" class="text-body"><?php esc_html_e( 'Confirm password', 'alpha' ); ?></label>
			<input type="password" class="woocommerce-Input woocommerce-Input--password input-text" name="password_2" id="password_2" autocomplete="off" />
		</p>
	</fieldset>
	<div class="clear"></div>
	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<p>
		<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
		<button type="submit" class="woocommerce-Button button btn btn-dark btn-rounded btn-sm" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'alpha' ); ?>"><?php esc_html_e( 'Save changes', 'alpha' ); ?></button>
		<input type="hidden" name="action" value="save_account_details" />
	</p>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
