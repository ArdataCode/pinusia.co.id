<?php
/**
 * Sidebar template
 *
 * @var $position           Sidebar position of current page.
 * @global $alpha_layout     Layout options for current page.
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */

defined( 'ABSPATH' ) || die;
wp_enqueue_script( 'alpha-sidebar' );
wp_enqueue_script( 'alpha-sticky-lib' );

global $alpha_layout;

$sidebar_class     = array( 'sidebar' );
$layout_name       = alpha_get_page_layout();
$sidebar           = $alpha_layout[ $position . '_sidebar' ];
$is_active_sidebar = is_active_sidebar( $sidebar );
$is_render_sidebar = false;
$sidebar_widgets   = get_option( 'sidebars_widgets' );

/**
 * Filters whether current page is vendor or not.
 *
 * @since 1.0
 */
if ( apply_filters( 'alpha_is_vendor_store', false ) ) {
	/**
	 * Filters if sidebar stored in vendor has content.
	 *
	 * @since 1.0
	 */
	$is_render_sidebar = apply_filters( 'alpha_vendor_store_sidebar_has_content', $sidebar_widgets );
} else {
	foreach ( $sidebar_widgets as $area => $widgets ) {
		if ( $sidebar == $area && is_array( $widgets ) && count( $widgets ) > 0 ) {
			$is_render_sidebar = true;
		}
	}
}

if ( ! $is_render_sidebar ) {
	return;
}

$toggle_class = 'sidebar-toggle';

if ( 'top' == $position ) { // Horizontal filter widgets in Shop page
	if ( 'archive_product' == $layout_name ) {
		$sidebar_class[] = 'top-sidebar';
		$sidebar_class[] = 'sidebar-fixed';
		$sidebar_class[] = 'shop-sidebar';
		$sidebar_class[] = 'horizontal-sidebar';
	}
} else { // Left & Right sidebar
	if ( 'offcanvas' == $alpha_layout[ $position . '_sidebar_type' ] ) { // Off-Canvas Type
		$sidebar_class[] = 'sidebar-offcanvas';
		if ( 'left' == $position && alpha_is_shop() ) {
			$toggle_class .= ' d-lg-none';
		}
	} else { // Classic Type
		$sidebar_class[] = 'sidebar-fixed';
	}

	if ( is_rtl() ) {
		$position = 'left' == $position ? 'right' : 'left';
	}

	$sidebar_class[] = $position . '-sidebar';

	if ( 'archive_product' == $layout_name ) {
		$sidebar_class[] = 'shop-sidebar';
	}
}
?>

<aside class="offcanvas sidebar-side <?php echo esc_attr( implode( ' ', apply_filters( 'alpha_sidebar_classes', $sidebar_class ) ) ); ?>" id="<?php echo esc_attr( $sidebar ); ?>">

	<div class="sidebar-overlay offcanvas-overlay"></div>
	<a class="sidebar-close" href="#"><i class="close-icon"></i></a>

	<?php if ( 'top' == $position && 'archive_product' == $layout_name ) : ?>

		<div class="sidebar-content offcanvas-content toolbox-left">
			<?php
			if ( $is_active_sidebar ) {
				dynamic_sidebar( $sidebar );
			}
			?>
		</div>

	<?php else : ?>

		<?php
		// Display arrow toggle except only left sidebar without horizontal filter widgets in shop page.
		if ( ! alpha_is_shop() || 'right' == $position || (
			! empty( $alpha_layout['top_sidebar'] ) && 'hide' != $alpha_layout['top_sidebar'] && is_active_sidebar( $alpha_layout['top_sidebar'] )
			) ) {
			echo '<a href="#" class="' . esc_attr( $toggle_class ) . '"><i class="fas fa-chevron-' . esc_attr( 'left' == $position ? 'right' : 'left' ) . '"></i></a>';
		}
		?>

		<div class="sidebar-content offcanvas-content">
			<?php
			/**
			 * Fires before print sidebar content.
			 *
			 * @since 1.0
			 */
			do_action( 'alpha_sidebar_content_start' );
			?>

			<div class="sticky-sidebar">
				<?php
				if ( $is_active_sidebar ) {
					dynamic_sidebar( $sidebar );
				}
				?>
			</div>

			<?php
			/**
			 * Fires after print sidebar content.
			 *
			 * @since 1.0
			 */
			do_action( 'alpha_sidebar_content_end' );
			?>

		</div>

	<?php endif; ?>
</aside>
