<?php
/**
 * Post Archive
 *
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 * @version    1.0
 */
defined( 'ABSPATH' ) || die;



global $alpha_layout;

$wrapper_class = alpha_get_loop_prop( 'wrapper_class', array() );
$wrapper_attrs = alpha_get_loop_prop( 'wrapper_attrs', '' );

$cpt            = alpha_get_loop_prop( 'cpt' );
$post_type      = alpha_get_loop_prop( 'type' );
$posts_layout   = alpha_get_loop_prop( 'posts_layout' );
$overlay        = alpha_get_loop_prop( 'overlay' );
$col_cnt        = alpha_get_loop_prop( 'col_cnt' );
$loop_classes   = alpha_get_loop_prop( 'loop_classes', array() );
$post_type      = $post_type ? $post_type : 'default';
$loop_classes[] = "post-{$post_type}";
if ( 'product' != $cpt ) {
	$wrapper_class[] = 'posts';
}

if ( 'post' != $cpt && $cpt ) {
	$wrapper_class[] = $cpt . 's';
}
// Archive
if ( ! alpha_get_loop_prop( 'widget' ) && ! alpha_get_loop_prop( 'related' ) ) {
	$posts_column = alpha_get_loop_prop( 'posts_column' );
	$image_size   = alpha_get_loop_prop( 'image_size' );
	if ( $posts_column > 1 ) {
		if ( 2 == $posts_column ) {
			$image_size = 'alpha-post-medium';
		}
	} else {
		$loop_classes[] = 'post-lg';
		$image_size     = 'full';
	}
	alpha_set_loop_prop( 'image_size', $image_size );
}

if ( ! empty( $overlay ) ) {
	$loop_classes[] = alpha_get_overlay_class( $overlay );
}
alpha_set_loop_prop( 'loop_classes', $loop_classes );

// Layouts - Slider, Creative Grid(widget)
if ( 'slider' == $posts_layout ) {

	if ( ! alpha_get_loop_prop( 'widget' ) ) {

		$wrapper_class[] = alpha_get_slider_class();
		/**
		 * Filters the option of related slider.
		 *
		 * @since 1.0
		 */
		$wrapper_attrs = ' data-slider-options="' . esc_attr(
			json_encode( alpha_get_slider_attrs( apply_filters( 'alpha_related_slider_options', array(), alpha_get_loop_prop( 'related' ) ), alpha_get_loop_prop( 'col_cnt' ) ) )
		) . '"';
	}
}

if ( 'creative' != $posts_layout ) {
	$wrapper_class[] = trim( alpha_get_col_class( $col_cnt ) );
}

// Loadmore Button or Pagination
$posts_query = alpha_get_loop_prop( 'posts' );
if ( empty( $posts_query ) ) {
	$posts_query = $GLOBALS['wp_query'];
}

if ( alpha_get_option( 'archive_ajax' ) ) {
	if ( 1 < $posts_query->max_num_pages || alpha_get_loop_prop( 'is_filter_cat' ) ) {
		if ( 'scroll' == alpha_get_loop_prop( 'loadmore_type' ) ) {
			$wrapper_class[] = 'load-scroll';
		}
		$wrapper_attrs .= ' ' . alpha_loadmore_attributes(
			$cpt,
			alpha_get_loop_prop( 'loadmore_props' ),
			alpha_get_loop_prop( 'loadmore_args' ),
			'page',
			$posts_query->max_num_pages
		);
	}
	
	if ( 'scroll' == alpha_get_loop_prop( 'loadmore_type' ) || 'button' == alpha_get_loop_prop( 'loadmore_type' ) || alpha_get_loop_prop( 'is_filter_cat' ) ) {
		wp_enqueue_script( 'alpha-ajax' );
	}
}

// Print Posts
/**
 * Filters the classes of post loop wrapper.
 *
 * @since 1.0
 */
$wrapper_class = apply_filters( 'alpha_post_loop_wrapper_classes', $wrapper_class );
echo '<div class="' . esc_attr( implode( ' ', $wrapper_class ) ) . '"' . $wrapper_attrs . '>';
