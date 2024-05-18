<?php
/**
 * Post Archive
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 * @version    1.0
 */
defined( 'ABSPATH' ) || die;

echo '</div>';

$posts_query = alpha_get_loop_prop( 'posts' );
if ( empty( $posts_query ) ) {
	$posts_query = $GLOBALS['wp_query'];
}

if ( 1 < $posts_query->max_num_pages && ( 'grid' == alpha_get_loop_prop( 'posts_layout' ) || empty( alpha_get_loop_prop( 'posts_layout' ) ) ) ) {
	alpha_loadmore_html(
		$posts_query,
		alpha_get_loop_prop( 'loadmore_type' ),
		alpha_get_loop_prop( 'loadmore_label', esc_html__( 'Load More', 'alpha' ) )
	);
}
