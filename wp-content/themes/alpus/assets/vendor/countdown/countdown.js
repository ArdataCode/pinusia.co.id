/**
 * Countdown: Countdown
 *
 * @package Alpus Theme
 * @since   1.0
 */

window.theme = window.theme || {};

( function ( $ ) {

    /**
     * Countdown
     * 
     * Implement Countdown function by jquery-countdown plugin
     * 
     * @since 1.0.0
     * 
     * @param {String} selector
     * @param {Object} $obj
     */
    theme.countdown = function ( selector, options ) {
        if ( $.fn.countdown ) {
            theme.$( selector ).each( function () {
                var $this = $( this ),
                    untilDate = $this.attr( 'data-until' ),
                    compact = $this.attr( 'data-compact' ),
                    dateFormat = ( !$this.attr( 'data-format' ) ) ? 'DHMS' : $this.attr( 'data-format' ),
                    newLabels = ( !$this.attr( 'data-labels-short' ) ) ? alpha_vars.countdown.labels : alpha_vars.countdown.labels_short,
                    newLabels1 = ( !$this.attr( 'data-labels-short' ) ) ? alpha_vars.countdown.label1 : alpha_vars.countdown.label1_short,
                    server_time = function () {
                        return new Date( $( this ).data( 'time-now' ) );
                    };


                $this.data( 'countdown' ) && $this.countdown( 'destroy' );

                $this.countdown( $.extend(
                    $this.hasClass( 'user-tz' ) ?
                        {
                            until: ( !$this.attr( 'data-relative' ) ) ? new Date( untilDate ) : untilDate,
                            format: dateFormat,
                            padZeroes: true,
                            compact: compact,
                            compactLabels: [ ' y', ' m', ' w', ' days, ' ],
                            timeSeparator: ' : ',
                            labels: newLabels,
                            labels1: newLabels1
                        } : {
                            until: ( !$this.attr( 'data-relative' ) ) ? new Date( untilDate ) : untilDate,
                            format: dateFormat,
                            padZeroes: true,
                            compact: compact,
                            compactLabels: [ ' y', ' m', ' w', ' days, ' ],
                            timeSeparator: ' : ',
                            labels: newLabels,
                            labels1: newLabels1,
                            serverSync: server_time
                        },
                    options )
                );
            } );
        }
    }

    $( window ).on( 'alpha_complete', function () {
        theme.countdown( '.countdown' );    // Countdown
    } );

} )( window.jQuery );