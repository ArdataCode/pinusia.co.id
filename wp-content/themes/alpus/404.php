<?php
/**
 * Error 404 page template
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

get_header();

/**
 * Fires before rendering page content.
 *
 * @since 1.0
 */
do_action( 'alpha_before_content' );
?>

<div class="page-content">

	<?php
	/**
	 * Fires before print page layout.
	 *
	 * @since 1.0
	 */
	do_action( 'alpha_print_before_page_layout' );

	global $alpha_layout;

	if ( ! empty( $alpha_layout['error_block'] ) && 'hide' != $alpha_layout['error_block'] ) {

		alpha_print_template( $alpha_layout['error_block'] );

	} elseif ( empty( $alpha_layout['error_block'] ) || 'hide' != $alpha_layout['error_block'] ) {

		?>
		<div class="area_404">
			<div class="content_404">
				<img src="<?php echo ALPHA_URI; ?>/assets/images/404-promo.png">
				<h1><strong class="text-primary"><?php echo esc_html_e( 'Oops!', 'alpha' ); ?></strong> <?php esc_html_e( 'This page cannot be found.', 'alpha' ); ?></h1>
				<p><?php printf( esc_html( 'Sorry, the page you are looking for is not available.%sMaybe you want to go home', 'alpha' ), '<br>' ); ?></p>
				<a href="<?php echo esc_url( home_url() ); ?>" class="btn btn-primary btn-dark btn-md btn-icon-right"><?php esc_html_e( 'Go Home', 'alpha' ); ?><i class="a-icon-long-arrow-right-solid"></i></a>
			</div>
		</div>
		<?php

	}

	/**
	 * Fires after print page layout.
	 *
	 * @since 1.0
	 */
	do_action( 'alpha_print_after_page_layout' );

	?>

</div>

<?php

/**
 * Fires before rendering page content.
 *
 * @since 1.0
 */
do_action( 'alpha_after_content' );
get_footer();
