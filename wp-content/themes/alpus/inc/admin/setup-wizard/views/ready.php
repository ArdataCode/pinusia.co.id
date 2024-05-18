<?php
/**
 * Ready panel
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;
update_option( 'alpha_setup_complete', time() );
?>
<h2 class="wizard-title"><?php esc_html_e( 'Your Website is Ready!', 'alpha' ); ?></h2>

<p class="wizard-description"><?php esc_html_e( 'Congratulations! The theme has been activated and your website is ready. Please go to your WordPress dashboard to make changes and modify the content for your needs.', 'alpha' ); ?></p>

<p><?php esc_html_e( 'This theme comes with 6 months item support from purchase date (with the option to extend this period). This license allows you to use this theme on a single website. Please purchase an additional license to use this theme on another website.', 'alpha' ); ?></p>
<div class="alpha-admin-panel-row">
	<div class="alpha-support">
		<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
		<h4 class="success system-status"><i class="circle a-icon-check"></i> <?php printf( esc_html__( 'Item Support %1$sDOES%2$s Include:', 'alpha' ), '<strong class="success">', '</strong>' ); ?></h4>

		<ul class="list">
			<li><?php esc_html_e( 'Availability of the author response to the questions', 'alpha' ); ?></li>
			<li><?php esc_html_e( 'Answering technical questions about item features', 'alpha' ); ?></li>
			<li><?php esc_html_e( 'Assistance with reported bugs and issues', 'alpha' ); ?></li>
			<li><?php esc_html_e( 'Help with bundled 3rd party plugins', 'alpha' ); ?></li>
		</ul>
	</div>
	<div class="alpha-support">
		<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
		<h4 class="error system-status"><i class="circle a-icon-times"></i> <?php printf( esc_html__( 'Item Support %1$sDOES NOT%2$s Include:', 'alpha' ), '<strong class="error">', '</strong>' ); ?></h4>
		<ul class="list">
			<li><?php esc_html_e( 'Customization services', 'alpha' ); ?></li>
			<li><?php esc_html_e( 'Installation services', 'alpha' ); ?></li>
			<li><?php esc_html_e( 'Help and Support for non-bundled 3rd party plugins (i.e. plugins you installed yourself later on)', 'alpha' ); ?></li>
		</ul>
	</div>
</div>

<div class="alpha-setup-next-steps">
	<div class="alpha-setup-next-steps-first">
		<p class="info-qt light-info"><?php printf( esc_html__( 'Please come back and leave a %1$s5-star rating%2$s if you are happy with this theme. Thanks!', 'alpha' ), '<a href="' . Alpha_Admin::get_instance()->admin_config['links']['reviews']['url'] . '" target="_blank">', '</a>' ); ?></p>
	</div>
	<div class="alpha-admin-panel-actions">
		<a class="button button-large button-dark button-next" href="<?php echo esc_url( home_url() ); ?>"><?php esc_html_e( 'View your new website!', 'alpha' ); ?></a>
	</div>
</div>
