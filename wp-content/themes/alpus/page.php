<?php
/**
 * The page template
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
	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			the_content();
			alpha_get_page_links_html();
		}
	} else {
		echo '<h2 class="entry-title">' . esc_html__( 'Nothing Found', 'alpha' ) . '</h2>';
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
