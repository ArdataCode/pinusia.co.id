<?php
/**
 * Dynamic vars
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

require_once alpha_framework_path( ALPHA_FRAMEWORK_PATH . '/admin/customizer/dynamic/dynamic-color-lib.php' );

$dynamic_vars = array(
	'html'                        => array(
		// Layout
		'--alpha-container-width'          => alpha_get_option( 'container' ) . 'px',
		'--alpha-container-fluid-width'    => alpha_get_option( 'container_fluid' ) . 'px',
		// Color
		'--alpha-primary-color'            => alpha_get_option( 'primary_color' ),
		'--alpha-primary-color-hover'      => AlphaColorLib::lighten( alpha_get_option( 'primary_color' ), 5 ),
		'--alpha-secondary-color'          => alpha_get_option( 'secondary_color' ),
		'--alpha-secondary-color-hover'    => AlphaColorLib::lighten( alpha_get_option( 'secondary_color' ), 5 ),
		'--alpha-link-color'               => '#333',
		'--alpha-link-color-hover'         => alpha_get_option( 'primary_color' ),
		'--alpha-danger-color'             => '#F96768',
		'--alpha-danger-color-hover'       => AlphaColorLib::lighten( '#F96768', 5 ),
		'--alpha-alert-color'              => '#ffa800',
		'--alpha-alert-color-hover'        => AlphaColorLib::lighten( '#ffa800', 5 ),
		'--alpha-success-color'            => '#66bc7c',
		'--alpha-success-color-hover'      => AlphaColorLib::lighten( '#66bc7c', 5 ),
		'--alpha-dark-color'               => alpha_get_option( 'dark_color' ),
		'--alpha-dark-color-hover'         => AlphaColorLib::lighten( alpha_get_option( 'dark_color' ), 5 ),
		'--alpha-light-color'              => alpha_get_option( 'light_color' ),
		'--alpha-light-color-hover'        => AlphaColorLib::lighten( alpha_get_option( 'light_color' ), 5 ),
		'--alpha-white-color'              => alpha_get_option( 'white_color' ),
		'--alpha-grey-color'               => '#999',
		// Heading Typography
		'--alpha-heading-h1-font-size'     => '2em',
		'--alpha-heading-h1-line-height'   => '1.2',
		'--alpha-heading-h2-font-size'     => '1.7em',
		'--alpha-heading-h2-line-height'   => '1.3',
		'--alpha-heading-h3-font-size'     => '1.5em',
		'--alpha-heading-h3-line-height'   => '1.4',
		'--alpha-heading-h4-font-size'     => '1.3em',
		'--alpha-heading-h4-line-height'   => '1.5',
		'--alpha-heading-h5-font-size'     => '1.2em',
		'--alpha-heading-h5-line-height'   => '1.6',
		'--alpha-heading-h6-font-size'     => '1.1em',
		'--alpha-heading-h6-line-height'   => '1.7',
		// Other Style
		'--alpha-border-radius-form'       => '3px',
		/* Colors that should be changed for light/dark skins */
		'--alpha-change-border-color'      => '#eee', /* #2c2c2c */
		'--alpha-change-color-light-1'     => '#fff', /* #222 */
		'--alpha-change-color-light-1-op2' => AlphaColorLib::lighten( '#fff', 20 ), /* #222 */
		'--alpha-change-color-light-1-op9' => AlphaColorLib::lighten( '#fff', 90 ), /* #222 */
		'--alpha-change-color-light-2'     => '#f4f4f4', /* #333 */
		'--alpha-change-color-light-3'     => '#ccc', /* #333 */
		'--alpha-change-color-dark-1'      => '#333', /* #fff */
		'--alpha-change-color-dark-1-op1'  => AlphaColorLib::lighten( '#333', 10 ), /* #fff */
		'--alpha-change-color-dark-1-op7'  => AlphaColorLib::lighten( '#333', 70 ), /* #fff */
		'--alpha-change-color-dark-2'      => '#444', /* #ccc */
		'--alpha-change-color-dark-3'      => '#313438', /* #ccc */
	),
	'.page-wrapper'               => array(),
	'.page-header'                => array(),
	'.page-header .page-title'    => array(),
	'.page-header .page-subtitle' => array(),
	'.page-title-bar'             => array(
		'--alpha-ptb-height' => alpha_get_option( 'ptb_height' ) . 'px',
	),
	'.breadcrumb'                 => array(),
	'.d-lazyload'                 => array(
		'--alpha-lazy-load-bg' => alpha_get_option( 'lazyload_bg' ),
	),
);

// Basic Layout
$site_type = alpha_get_option( 'site_type' );
if ( 'full' != $site_type ) {
	alpha_dynamic_vars_bg( 'site', alpha_get_option( 'site_bg' ), $dynamic_vars['html'] );
	$dynamic_vars['html']['--alpha-site-width']  = alpha_get_option( 'site_width' ) . 'px';
	$dynamic_vars['html']['--alpha-site-margin'] = '0 auto';

	if ( 'boxed' == $site_type ) {
		$dynamic_vars['html']['--alpha-site-gap'] = '0 ' . alpha_get_option( 'site_gap' ) . 'px';
	} else {
		$dynamic_vars['html']['--alpha-site-gap'] = alpha_get_option( 'site_gap' ) . 'px';
	}
} else {
	alpha_dynamic_vars_bg( 'site', array( 'background-color' => '#fff' ), $dynamic_vars['html'] );
	$dynamic_vars['html']['--alpha-site-width']  = 'false';
	$dynamic_vars['html']['--alpha-site-margin'] = '0';
	$dynamic_vars['html']['--alpha-site-gap']    = '0';
}

/* Color & Typography */
$p_color_rgb = AlphaColorLib::hexToRGB( alpha_get_option( 'primary_color' ), false );
$dynamic_vars['html']['--alpha-primary-color-op-80'] = 'rgba(' . $p_color_rgb[0] . ',' . $p_color_rgb[1] . ',' . $p_color_rgb[2] . ', 0.8)';

alpha_dynamic_vars_typo( 'body', alpha_get_option( 'typo_default' ), $dynamic_vars['html'] );
alpha_dynamic_vars_typo( 'heading', alpha_get_option( 'typo_heading' ), $dynamic_vars['html'], array( 'font-weight' => 600 ) );
alpha_dynamic_vars_bg( 'page-wrapper', alpha_get_option( 'content_bg' ), $dynamic_vars['.page-wrapper'] );
alpha_dynamic_vars_bg( 'ptb', alpha_get_option( 'ptb_bg' ), $dynamic_vars['.page-header'] );
alpha_dynamic_vars_typo( 'ptb-title', alpha_get_option( 'typo_ptb_title' ), $dynamic_vars['.page-header .page-title'] );
alpha_dynamic_vars_typo( 'ptb-subtitle', alpha_get_option( 'typo_ptb_subtitle' ), $dynamic_vars['.page-header .page-subtitle'] );
alpha_dynamic_vars_typo( 'ptb-breadcrumb', alpha_get_option( 'typo_ptb_breadcrumb' ), $dynamic_vars['.breadcrumb'] );

/**
 * Filters the dynamic vars.
 *
 * @since 1.0
 */
$dynamic_vars = apply_filters( 'alpha_dynamic_vars', $dynamic_vars );
$style        = '';
foreach ( $dynamic_vars as $selector => $value ) {
	$style .= $selector . ' {' . PHP_EOL;
	foreach ( $value as $css_var => $option ) {
		$style .= $css_var . ': ' . $option . ';' . PHP_EOL;
	}
	$style .= '}' . PHP_EOL;
}

/* Responsive */
$style .= '@media (max-width: ' . ( (int) alpha_get_option( 'container' ) - 1 ) . 'px) {
    .container-fluid .container {
        padding-left: 0;
        padding-right: 0;
    }
}' . PHP_EOL;

$style .= '@media (max-width: ' . ( (int) alpha_get_option( 'container' ) - 1 ) . 'px) and (min-width: 480px) {
	.elementor-top-section.elementor-section-boxed > .elementor-container,
	.elementor-section-full_width .elementor-section-boxed > .elementor-container {
		width: calc(100% - var(--alpha-gap) * 4 + var(--alpha-el-section-gap) * 2);
	}
	
	.elementor-top-section.elementor-section-boxed > .slider-container.slider-shadow,
	.elementor-section-full_width .elementor-section-boxed > .slider-container.slider-shadow {
		width: calc(100% - var(--alpha-gap) * 4 + 40px) !important;
	}
}' . PHP_EOL;

$style .= '@media (max-width: ' . ( (int) alpha_get_option( 'container_fluid' ) - 1 ) . 'px) and (min-width: 480px) {
	.elementor-top-section.elementor-section-boxed > .elementor-container.container-fluid {
		width: calc( 100% - var(--alpha-gap) * 4 + var(--alpha-el-section-gap) * 2);
	}
}' . PHP_EOL;

$style .= '@media (max-width: ' . ( (int) alpha_get_option( 'container' ) + 119 ) . 'px) and (min-width: 992px) {
	.elementor-top-section.elementor-section-boxed > .elementor-container,
	.elementor-top-section.elementor-section-boxed > .elementor-container.container-fluid,
	.elementor-section-full_width .elementor-col-100 .elementor-section-boxed > .elementor-container {
		width: calc(86vw + var(--alpha-el-section-gap) * 2);
	}
	.container,
	.fixed .container {
		width: calc(86vw + 4 * var(--alpha-gap));
	}
	.elementor-container > .elementor-column > .col-half-section,
	.elementor-container > .elementor-row > .elementor-column > .col-half-section {
		max-width: calc((86vw + var(--alpha-el-section-gap) * 2) / 2);
	}
	.elementor-top-section.elementor-section-boxed > .slider-container.slider-shadow,
	.elementor-section-full_width .elementor-section-boxed > .slider-container.slider-shadow {
		width: calc(86vw + 40px) !important;
	}
}' . PHP_EOL;

/**
 * Filters the dynamic style.
 *
 * @since 1.0
 */
echo preg_replace( '/[\t]+/', '', apply_filters( 'alpha_dynamic_style', $style ) );
