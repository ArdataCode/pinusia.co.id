/**
 * Selective Refresh Extend for Customize
 * 
 * @package  Alpus Theme
 * @since 1.0
 */
'use strict';
var alpha_cs_tooltips = [ {
    target: '.page-header',
    text: 'Page Title Bar',
    elementID: 'title_bar',
    pos: 'bottom',
    type: 'section',
}, {
    target: '.single .product-single',
    text: 'Product Page',
    elementID: 'product_detail',
    pos: 'top',
    type: 'section',
}, {
    target: '.products .product-wrap .product',
    text: 'Product Type',
    elementID: 'product_type',
    pos: 'center',
    type: 'section',
}, {
    target: '.product-single .woocommerce-tabs',
    text: 'Product DataTab Type',
    elementID: 'product_description_title',
    pos: 'center',
    type: 'control',
}];

jQuery( document ).ready( function ( $ ) {

    eventsInit();

    function getCustomize( option ) {
        var o = wp.customize( option );
        return o ? o.get() : '';
    }

    var options = [
        [ 'primary_color', 'secondary_color', 'dark_color', 'light_color', 'success_color', 'alert_color', 'danger_color' ],
        'typo_h1_size', 'typo_h2_size', 'typo_h3_size', 'typo_h4_size', 'typo_h5_size', 'typo_h6_size'
    ];

    for ( var i = 0; i < options.length; i++ ) {
        if ( Array.isArray( options[ i ] ) ) {
            var option = options[ i ];
        } else {
            var option = [ options[ i ] ];
        }

        for ( var j = 0; j < option.length; j++ ) {
            wp.customize( option[ j ], function ( e ) {
                var event = option[ 0 ];
                e.bind( function ( value ) {
                    $( document.body ).trigger( event );
                } );
            } );
        }

        $( document.body ).trigger( option[ 0 ] );
    }

    function eventsInit() {

        var style = $( 'html' )[ 0 ].style,
            headings = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];

        headings.forEach( function ( heading ) {
            $( document.body ).on( 'typo_' + heading + '_size', function () {
                if ( document.querySelector( heading ) )
                    alpha_selective_size( style, heading, getCustomize( 'typo_' + heading + '_size' ) );
            } )
        } );

        $( document.body ).on( 'primary_color', function () {
            style.setProperty( '--alpha-success-color', getCustomize( 'success_color' ) );
            style.setProperty( '--alpha-success-color-hover', getLighten( getCustomize( 'success_color' ) ) );
            style.setProperty( '--alpha-alert-color', getCustomize( 'alert_color' ) );
            style.setProperty( '--alpha-alert-color-hover', getLighten( getCustomize( 'alert_color' ) ) );
            style.setProperty( '--alpha-danger-color', getCustomize( 'danger_color' ) );
            style.setProperty( '--alpha-danger-color-hover', getLighten( getCustomize( 'danger_color' ) ) );

            style.setProperty( '--alpha-primary-color-light', getLighten( getCustomize( 'primary_color' ), 40 ) );
            style.setProperty( '--alpha-secondary-color-light', getLighten( getCustomize( 'secondary_color' ), 40 ) );
            style.setProperty( '--alpha-dark-color-light', getLighten( getCustomize( 'dark_color' ), 40 ) );
            style.setProperty( '--alpha-light-color-light', getLighten( getCustomize( 'light_color' ), 40 ) );
            style.setProperty( '--alpha-success-color-light', getLighten( getCustomize( 'success_color' ), 40 ) );
            style.setProperty( '--alpha-alert-color-light', getLighten( getCustomize( 'alert_color' ), 40 ) );
            style.setProperty( '--alpha-danger-color-light', getLighten( getCustomize( 'danger_color' ), 40 ) );

            style.setProperty( '--alpha-primary-gradient-1', getDarken( getCustomize( 'primary_color' ), 0.6 ) );
            style.setProperty( '--alpha-primary-gradient-2', getLighten( getCustomize( 'primary_color' ), 10 ) );

            style.setProperty( '--alpha-change-border-color', '#ccc' );
            style.setProperty( '--alpha-change-light-border-color', '#e1e1e1' );
            style.setProperty( '--alpha-change-color-light-2', '#aaa' );
            style.setProperty( '--alpha-change-color-light-3', '#ddd' );
            style.setProperty( '--alpha-change-color-light-4', '#999' );
            style.setProperty( '--alpha-change-color-light-5', '#f6f6f6' );
            style.setProperty( '--alpha-change-color-dark-1', getCustomize( 'dark_skin' ) ? '#ccc' : getCustomize( 'dark_color' ) );
            style.setProperty( '--alpha-change-color-dark-2', '#3a3a3a' );
            style.setProperty( '--alpha-change-color-dark-3', '#555' );
        } )
    }

    function alpha_selective_size( style, id, size ) {
        var default_sizes = {
            h1: '5rem',
            h2: '3.8rem',
            h3: '2.8rem',
            h4: '2.2rem',
            h5: '1.8rem',
            h6: '1.6rem',
        }

        if ( size ) {
            var unit = size.replace( /[0-9.]*/, '' );
            if ( !unit ) {
                size += 'px';
            }

            style.setProperty( '--alpha-heading-' + id + '-font-size', size );
        } else {
            style.setProperty( '--alpha-heading-' + id + '-font-size', default_sizes[ id ] );
        }
    }

    /**
     * Transform color format.
     * 
     * @since 1.0
     */
    function getHSL( color ) {
        color = Number.parseInt( color.slice( 1 ), 16 );
        var $blue = color % 256;
        color /= 256;
        var $green = color % 256;
        var $red = color = color / 256;

        var $min = Math.min( $red, $green, $blue );
        var $max = Math.max( $red, $green, $blue );

        var $l = $min + $max;
        var $d = Number( $max - $min );
        var $h = 0;
        var $s = 0;

        if ( $d ) {
            if ( $l < 255 ) {
                $s = $d / $l;
            } else {
                $s = $d / ( 510 - $l );
            }

            if ( $red == $max ) {
                $h = 60 * ( $green - $blue ) / $d;
            } else if ( $green == $max ) {
                $h = 60 * ( $blue - $red ) / $d + 120;
            } else if ( $blue == $max ) {
                $h = 60 * ( $red - $green ) / $d + 240;
            }
        }

        return [ ( $h + 360 ) % 360, ( $s * 100 ), ( $l / 5.1 + 7 ) ];
    }

    /**
     * Change hue to rgb.
     * 
     * @since 1.0
     * @param {int} $m1 
     * @param {int} $m2 
     * @param {int} $h 
     */
    function hueToRGB( $m1, $m2, $h ) {
        if ( $h < 0 ) {
            $h += 1;
        } else if ( $h > 1 ) {
            $h -= 1;
        }

        if ( $h * 6 < 1 ) {
            return $m1 + ( $m2 - $m1 ) * $h * 6;
        }

        if ( $h * 2 < 1 ) {
            return $m2;
        }

        if ( $h * 3 < 2 ) {
            return $m1 + ( $m2 - $m1 ) * ( 2 / 3 - $h ) * 6;
        }

        return $m1;
    }

    /**
     * Get RGB
     * 
     * @since 1.0
     * @param {int} $hue 
     * @param {int} $saturation 
     * @param {int} $lightness 
     */
    function getRGB( $hue, $saturation, $lightness ) {
        if ( $hue < 0 ) {
            $hue += 360;
        }

        var $h = $hue / 360;
        var $s = Math.min( 100, Math.max( 0, $saturation ) ) / 100;
        var $l = Math.min( 100, Math.max( 0, $lightness ) ) / 100;

        var $m2 = $l <= 0.5 ? $l * ( $s + 1 ) : $l + $s - $l * $s;
        var $m1 = $l * 2 - $m2;

        var $r = hueToRGB( $m1, $m2, $h + 1 / 3 ) * 255;
        var $g = hueToRGB( $m1, $m2, $h ) * 255;
        var $b = hueToRGB( $m1, $m2, $h - 1 / 3 ) * 255;

        var $out = [ Math.ceil( $r ), Math.ceil( $g ), Math.ceil( $b ) ];

        return $out;
    }

    /**
     * Adjust the hsl.
     * 
     * @since 1.0
     * @param {string} color 
     * @param {int} amount 
     */
    function adjustHsl( color, amount ) {
        var $hsl = getHSL( color );
        $hsl[ 2 ] += amount;
        var $out = getRGB( $hsl[ 0 ], $hsl[ 1 ], $hsl[ 2 ] );
        return 'rgb(' + $out[ 0 ] + ',' + $out[ 1 ] + ',' + $out[ 2 ] + ')';
    }

    /**
     * Returns the light color.
     * 
     * @since 1.0
     * @param {string} color 
     * @param {int} amount
     */
    function getLighten( color, amount = 5 ) {
        if ( !color || 'transparent' == color ) {
            return 'transparent';
        }
        if ( color.length == 4 ) {
            color = '#' + color[ 1 ] + color[ 1 ] + color[ 2 ] + color[ 2 ] + color[ 3 ] + color[ 3 ];
        }
        return adjustHsl( color, amount );
    }

    /**
     * Returns the dark color.
     * 
     * @since 1.0
     * @param {string} color 
     * @param {int} amount
     */
    function getDarken( color, amount = 5 ) {
        if ( !color || 'transparent' == color ) {
            return 'transparent';
        }
        if ( color.length == 4 ) {
            color = '#' + color[ 1 ] + color[ 1 ] + color[ 2 ] + color[ 2 ] + color[ 3 ] + color[ 3 ];
        }
        return adjustHsl( color, -amount );
    }
} )