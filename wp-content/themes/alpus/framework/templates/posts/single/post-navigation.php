<?php
/**
 * Post Navigation
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 * @version    1.0
 */
defined( 'ABSPATH' ) || die;

if ( 'attachment' != get_post_type() ) {
	the_post_navigation(
		apply_filters(
			'alpha_post_navigation_args',
			array(
				'prev_text' => '<span class="label">' . esc_html__( 'Previous Post', 'alpha' ) . '</span>' . '<span class="pager-link-title">%title</span>',
				'next_text' => '<span class="label">' . esc_html__( 'Next Post', 'alpha' ) . '</span>' . '<span class="pager-link-title">%title</span>',
			)
		)
	);
}
