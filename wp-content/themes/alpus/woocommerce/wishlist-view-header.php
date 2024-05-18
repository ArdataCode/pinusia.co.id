<?php
/**
 * Wishlist header
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist \YITH_WCWL_Wishlist Current wishlist
 * @var $is_custom_list bool Whether current wishlist is custom
 * @var $form_action string Action for the wishlist form
 * @var $page_title string Page title
 * @var $fragment_options array Array of items to use for fragment generation
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly

if ( defined( 'ALPHA_CORE_VERSION' ) && defined( 'ALPUS_PRODUCT_COMPARE_VERSION' ) ) {
	wp_enqueue_script( 'alpus-product-compare', ALPUS_PRODUCT_COMPARE_ASSETS . 'js/product-compare' . ALPUS_PLUGIN_JS_SUFFIX, null, ALPUS_PRODUCT_COMPARE_VERSION, true );
	wp_enqueue_style( 'alpus-product-compare', ALPUS_PRODUCT_COMPARE_ASSETS . 'css/product-compare' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), ALPUS_PRODUCT_COMPARE_VERSION );
}
?>

<?php do_action( 'yith_wcwl_before_wishlist_form', $wishlist ); ?>

<form id="yith-wcwl-form" action="<?php echo esc_attr( $form_action ); ?>" method="post" class="woocommerce yith-wcwl-form wishlist-fragment" data-fragment-options="<?php echo esc_attr( json_encode( $fragment_options ) ); ?>">

	<!-- TITLE -->
	<?php
	do_action( 'yith_wcwl_before_wishlist_title', $wishlist );

	if ( ! empty( $page_title ) && $wishlist && $wishlist->has_items() ) :
		?>
		<div class="wishlist-title <?php echo esc_attr( $is_custom_list ) ? 'wishlist-title-with-form' : ''; ?>">
			<?php echo apply_filters( 'yith_wcwl_wishlist_title', '<h2>' . $page_title . '</h2>' ); ?>
			<?php if ( $is_custom_list ) : ?>
				<a class="btn show-title-form btn-dark btn-icon-left">
					<?php echo apply_filters( 'yith_wcwl_edit_title_icon', '<i class="fa fa-pencil"></i>' ); ?>
					<?php esc_html_e( 'Edit title', 'alpha' ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php if ( $is_custom_list ) : ?>
			<div class="hidden-title-form">
				<input type="text" class="form-control" size="54" value="<?php echo esc_attr( $page_title ); ?>" name="wishlist_name"/>
				<input type="submit" class="btn btn-dark" name="save_title" value="<?php esc_attr_e( 'Save', 'alpha' ); ?>" />
				<a class="hide-title-form btn btn-dark btn-icon-left">
					<?php echo apply_filters( 'yith_wcwl_cancel_wishlist_title_icon', '<i class="fa fa-undo"></i>' ); ?>
					<?php esc_html_e( 'Cancel', 'alpha' ); ?>
				</a>
			</div>
		<?php endif; ?>
		<?php
	endif;

	do_action( 'yith_wcwl_before_wishlist', $wishlist );
	?>
