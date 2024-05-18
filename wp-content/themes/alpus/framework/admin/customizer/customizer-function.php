<?php
/**
 * Theme SCSS Builder
 *
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

if ( ! function_exists( 'alpha_customizer_background' ) ) {
	function alpha_customizer_background( $bg ) {
		$res = '';

		if ( is_array( $bg ) ) {
			if ( isset( $bg['background-color'] ) && $bg['background-color'] ) {
				$res .= 'background-color: ' . $bg['background-color'] . ',' . PHP_EOL;
			} else {
				$res .= 'background-color: transparent,' . PHP_EOL;
			}

			if ( isset( $bg['background-image'] ) && $bg['background-image'] ) {
				$res .= 'background-image: url(' . $bg['background-image'] . '),' . PHP_EOL;

				if ( isset( $bg['background-repeat'] ) && $bg['background-repeat'] ) {
					$res .= 'background-repeat: ' . $bg['background-repeat'] . ',' . PHP_EOL;
				}
				if ( isset( $bg['background-position'] ) && $bg['background-position'] ) {
					$res .= 'background-position: ' . $bg['background-position'] . ',' . PHP_EOL;
				}
				if ( isset( $bg['background-size'] ) && $bg['background-size'] ) {
					$res .= 'background-size: ' . $bg['background-size'] . ',' . PHP_EOL;
				}
				if ( isset( $bg['background-attachment'] ) && $bg['background-attachment'] ) {
					$res .= 'background-attachment: ' . $bg['background-attachment'] . ',' . PHP_EOL;
				}
			} else {
				$res .= 'background-image: none,' . PHP_EOL;
			}
		}

		return $res;
	}
}

if ( function_exists( 'alpha_customizer_typography' ) ) {
	function alpha_customizer_typography( $typo, $allow_inherit = false ) {
		$res = '';

		if ( is_array( $typo ) ) {
			if ( isset( $typo['font-family'] ) && 'inherit' != $typo['font-family'] ) {
				$res .= 'font-family: "' . "'" . sanitize_text_field( $typo['font-family'] ) . "'" . ', sans-serif",' . PHP_EOL;

				if ( isset( $typo['variant'] ) && $typo['variant'] ) {
					$res .= 'font-weight: ' . ( 'regular' == $typo['variant'] ? 400 : (int) $typo['variant'] ) . ',' . PHP_EOL;
				}
			} elseif ( $allow_inherit ) {
				$res .= 'font-family: inherit,' . PHP_EOL;
			}
			if ( isset( $typo['font-size'] ) && '' != $typo['font-size'] ) {
				$res .= 'font-size: ' . ( is_numeric( $typo['font-size'] ) ? ( (int) $typo['font-size'] . 'px' ) : esc_attr( $typo['font-size'] ) ) . ',' . PHP_EOL;
			}
			if ( isset( $typo['line-height'] ) && '' != $typo['line-height'] ) {
				$res .= 'line-height: ' . esc_attr( $typo['line-height'] ) . ',' . PHP_EOL;
			}
			if ( isset( $typo['letter-spacing'] ) && '' != $typo['letter-spacing'] ) {
				$res .= 'letter-spacing: ' . esc_attr( $typo['letter-spacing'] ) . ',' . PHP_EOL;
			}
			if ( isset( $typo['text-transform'] ) && '' != $typo['text-transform'] ) {
				$res .= 'text-transform: ' . esc_attr( $typo['text-transform'] ) . ',' . PHP_EOL;
			}
			if ( isset( $typo['color'] ) && '' != $typo['color'] ) {
				$res .= 'color: ' . esc_attr( $typo['color'] ) . ',' . PHP_EOL;
			}
		}

		return $res;
	}
}

if ( ! function_exists( 'alpha_dynamic_vars_bg' ) ) {
	function alpha_dynamic_vars_bg( $id, $bg, &$arr ) {
		$style = '';

		if ( isset( $bg['background-color'] ) && $bg['background-color'] ) {
			$style .= '--alpha-' . $id . '-bg-color: ' . $bg['background-color'] . ';' . PHP_EOL;
			if ( isset( $arr ) ) {
				$arr[ '--alpha-' . $id . '-bg-color' ] = $bg['background-color'];
			}
		}
		if ( isset( $bg['background-image'] ) && $bg['background-image'] ) {
			$style .= '--alpha-' . $id . '-bg-image: url("' . $bg['background-image'] . '");' . PHP_EOL;
			if ( isset( $arr ) ) {
				$arr[ '--alpha-' . $id . '-bg-image' ] = 'url("' . $bg['background-image'] . '")';
			}
			if ( isset( $bg['background-repeat'] ) && $bg['background-repeat'] ) {
				$style .= '--alpha-' . $id . '-bg-repeat: ' . $bg['background-repeat'] . ';' . PHP_EOL;
				if ( isset( $arr ) ) {
					$arr[ '--alpha-' . $id . '-bg-repeat' ] = $bg['background-repeat'];
				}
			}
			if ( isset( $bg['background-position'] ) && $bg['background-position'] ) {
				$style .= '--alpha-' . $id . '-bg-position: ' . $bg['background-position'] . ';' . PHP_EOL;
				if ( isset( $arr ) ) {
					$arr[ '--alpha-' . $id . '-bg-position' ] = $bg['background-position'];
				}
			}
			if ( isset( $bg['background-size'] ) && $bg['background-size'] ) {
				$style .= '--alpha-' . $id . '-bg-size: ' . $bg['background-size'] . ';' . PHP_EOL;
				if ( isset( $arr ) ) {
					$arr[ '--alpha-' . $id . '-bg-size' ] = $bg['background-size'];
				}
			}
			if ( isset( $bg['background-attachment'] ) && $bg['background-attachment'] ) {
				$style .= '--alpha-' . $id . '-bg-attachment: ' . $bg['background-attachment'] . ';' . PHP_EOL;
				if ( isset( $arr ) ) {
					$arr[ '--alpha-' . $id . '-bg-attachment' ] = $bg['background-attachment'];
				}
			}
		}

		return $style;
	}
}

if ( ! function_exists( 'alpha_dynamic_vars_typo' ) ) {
	function alpha_dynamic_vars_typo( $id, $typo, &$arr, $default = array() ) {
		$style = '';

		if ( isset( $typo['font-family'] ) && 'inherit' != $typo['font-family'] ) {
			$style .= '--alpha-' . $id . '-font-family: ' . "'" . $typo['font-family'] . "';" . PHP_EOL;
			if ( isset( $arr ) ) {
				$arr[ '--alpha-' . $id . '-font-family' ] = "'" . $typo['font-family'] . "'";
			}
			if ( ! isset( $typo['variant'] ) ) {
				$typo['variant'] = 400;
			}
		} else {
			if ( empty( $typo['variant'] ) && isset( $default['font-weight'] ) ) {
				$typo['variant'] = $default['font-weight'];
			}
		}

		if ( isset( $typo['variant'] ) && $typo['variant'] ) {
			$style .= '--alpha-' . $id . '-font-weight: ' . ( 'regular' == $typo['variant'] ? 400 : $typo['variant'] ) . ';' . PHP_EOL;
			if ( isset( $arr ) ) {
				$arr[ '--alpha-' . $id . '-font-weight' ] = ( 'regular' == $typo['variant'] ? 400 : $typo['variant'] );
			}
		}

		if ( isset( $typo['font-size'] ) && '' != $typo['font-size'] ) {
			$size = $typo['font-size'];
			if ( $size ) {
				$unit = trim( preg_replace( '/[0-9.]/', '', $size ) );
				if ( ! $unit ) {
					$size .= 'px';
				}
				$style .= '--alpha-' . $id . '-font-size: ' . esc_html( $size ) . ';' . PHP_EOL;
				if ( isset( $arr ) ) {
					$arr[ '--alpha-' . $id . '-font-size' ] = esc_html( $size );
				}
			}
		}

		if ( isset( $typo['line-height'] ) && '' != $typo['line-height'] ) {
			$style .= '--alpha-' . $id . '-line-height: ' . $typo['line-height'] . ';' . PHP_EOL;
			if ( isset( $arr ) ) {
				$arr[ '--alpha-' . $id . '-line-height' ] = $typo['line-height'];
			}
		}

		if ( isset( $typo['letter-spacing'] ) && '' != $typo['letter-spacing'] ) {
			$style .= '--alpha-' . $id . '-letter-spacing: ' . $typo['letter-spacing'] . ';' . PHP_EOL;
			if ( isset( $arr ) ) {
				$arr[ '--alpha-' . $id . '-letter-spacing' ] = $typo['letter-spacing'];
			}
		}

		if ( isset( $typo['text-transform'] ) && '' != $typo['text-transform'] ) {
			$style .= '--alpha-' . $id . '-text-transform: ' . $typo['text-transform'] . ';' . PHP_EOL;
			if ( isset( $arr ) ) {
				$arr[ '--alpha-' . $id . '-text-transform' ] = $typo['text-transform'];
			}
		}

		if ( isset( $typo['color'] ) && '' != $typo['color'] ) {
			$style .= '--alpha-' . $id . '-color: ' . $typo['color'] . ';' . PHP_EOL;
			if ( isset( $arr ) ) {
				$arr[ '--alpha-' . $id . '-color' ] = $typo['color'];
			}
		}

		return $style;
	}
}
