<?php
/**
 * Post Readmore
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 * @version    1.0
 */
defined( 'ABSPATH' ) || die;

printf(
	'<a href="%s" class="btn %s">%s</a>',
	esc_url( get_the_permalink() ),
	esc_attr( alpha_get_loop_prop( 'read_more_class', 'btn-link btn-underline' ) ),
	alpha_get_loop_prop( 'read_more_label' ) . ( alpha_get_loop_prop( 'read_more_class' ) ? '' : '<i class="' . ALPHA_ICON_PREFIX . '-icon-long-arrow-' . ( is_rtl() ? 'left' : 'right' ) . '"></i>' )
);
