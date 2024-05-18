<?php
/**
 * Header content template
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 */

defined( 'ABSPATH' ) || die;

global $alpha_layout;
if ( ALPHA_NAME . '_template' == get_post_type() && 'header' == get_post_meta( get_the_ID(), ALPHA_NAME . '_template_type', true ) ) {
	/**
	 * View Header Template
	 *
	 * @since 1.0
	 */

	$settings = get_post_meta( get_the_ID(), '_elementor_page_settings', true );

	if ( ! empty( $settings['header_pos'] ) ) {
		echo '<div class="header-area">';
	}

	echo '<header class="header custom-header header-' . get_the_ID() . '" id="header">';

	if ( have_posts() ) :
		the_post();
			the_content();
		wp_reset_postdata();
	endif;

	echo '</header>';

	if ( ! empty( $settings['header_pos'] ) ) {
		echo '</div>';
	}
} elseif ( ! empty( $alpha_layout['header'] ) && 'elementor_pro' == $alpha_layout['header'] ) {

	/**
	 * Fires for elementor Pro Header
	 *
	 * @since 1.0
	 */
	do_action( 'alpha_elementor_pro_header_location' );

} elseif ( ! empty( $alpha_layout['header'] ) && 'hide' == $alpha_layout['header'] ) {

	// Hide

} elseif ( ! empty( $alpha_layout['header'] ) && 'publish' == get_post_status( intval( $alpha_layout['header'] ) ) && alpha_get_builder_status( 'header' ) ) {

	/**
	 * Custom Block Header
	 *
	 * @since 1.0
	 */
	$settings = get_post_meta( $alpha_layout['header'], '_elementor_page_settings', true );

	if ( ! empty( $settings['header_pos'] ) ) {
		echo '<div class="header-area">';
	}

	echo '<header class="header custom-header header-' . intval( $alpha_layout['header'] ) . '" id="header">';
	alpha_print_template( $alpha_layout['header'] );
	echo '</header>';

	if ( ! empty( $settings['header_pos'] ) ) {
		echo '</div>';

		echo '<div class="content-area">';
	}
} else {
	/**
	 * Default Header
	 *
	 * @since 1.0
	 */
	?>
	<header class="header default-header" id="header">
		<div class="container d-flex align-items-center">
			<a href="<?php echo esc_url( home_url() ); ?>" style="margin-<?php echo is_rtl() ? 'left' : 'right'; ?>: 20px;">
				<?php if ( alpha_get_option( 'custom_logo' ) ) : ?>
					<img class="logo" src="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', wp_get_attachment_url( alpha_get_option( 'custom_logo' ) ) ) ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>">
				<?php else : ?>
					<img class="logo" src="<?php echo ALPHA_ASSETS . '/images/logo.png'; ?>" width="270" height="84" alt="<?php esc_attr_e( 'Logo', 'alpha' ); ?>"/>
				<?php endif; ?>
			</a>
			<?php
			if ( has_nav_menu( 'main-menu' ) && get_term( get_nav_menu_locations()[ 'main-menu' ], 'nav_menu' ) ) {
				?>
				<a href="#" class="mobile-menu-toggle d-lg-none" aria-label="<?php esc_attr_e( 'Mobile Menu', 'alpha' ); ?>"><i class="<?php echo esc_attr( ALPHA_ICON_PREFIX . '-icon-hamburger' ); ?>"></i></a>
				<?php
				wp_nav_menu(
					array(
						'theme_location'  => 'main-menu',
						'container'       => 'nav',
						'container_class' => 'main-menu d-none d-lg-flex',
						'items_wrap'      => '<ul id="%1$s" class="menu menu-main-menu">%3$s</ul>',
						'walker'          => class_exists( 'Alpha_Walker_Nav_Menu' ) ? new Alpha_Walker_Nav_Menu() : new Walker_Nav_Menu(),
					)
				);
			}
			?>
		</div>
	</header>
	<?php
}

