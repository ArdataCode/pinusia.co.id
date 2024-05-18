/**
 * Theme JS Library
 * 
 * @author     AlpusTheme
 * @package    Alpus Theme
 * @subpackage Theme
 * @since      1.0
 * @version    1.0
 */
'use strict';
window.theme || ( window.theme = {} );

( function ( $ ) {
    $( 'body' ).addClass( 'pre-loaded' );

    /**
     * Make sidebar sticky
     *
     * @since 1.0
     * @param {string} selector
     * @return {void}
     */
    theme.stickySidebar = function ( selector ) {
        if ( $.fn.themeSticky ) {
            theme.$( selector ).each(
                function () {
                    var $this = $( this ),
                        aside = $this.closest( '.sidebar' ),
                        options = theme.defaults.stickySidebar,
                        top = 0;

                    // Do not sticky for off canvas sidebars.
                    if ( aside.hasClass( 'sidebar-offcanvas' ) ) {
                        return;
                    }

                    // Add wrapper class
                    ( aside.length ? aside : $this.parent() ).addClass( 'sticky-sidebar-wrapper' );

                    $( '.sticky-sidebar > .filter-actions' ).length || $( '.sticky-content.fix-top' ).each( function ( e ) {
                        if ( $( this ).hasClass( 'sticky-toolbox' ) ) {
                            return;
                        }

                        if ( !( $( this ).closest( '.header' ).length && theme.$body.hasClass( 'side-header' ) &&
                            ( ( theme.$body.hasClass( 'side-on-desktop' ) && window.innerWidth > 991 ) ||
                                ( theme.$body.hasClass( 'side-on-tablet' ) && window.innerWidth > 767 ) ||
                                ( theme.$body.hasClass( 'side-on-mobile' ) && window.innerWidth > 575 ) ||
                                ( !theme.$body.hasClass( 'side-on-desktop' ) && !theme.$body.hasClass( 'side-on-tablet' ) && !theme.$body.hasClass( 'side-on-mobile' ) ) ) ) ) {

                            var $fixed = $( this ).hasClass( 'fixed' );
                            top += $( this ).addClass( 'fixed' ).outerHeight();
                            $fixed || $( this ).removeClass( 'fixed' );

                        }

                    } );

                    options[ 'padding' ][ 'top' ] = top;

                    $this.themeSticky( $.extend( {}, options, theme.parseOptions( $this.attr( 'data-sticky-options' ) ) ) );

                    // issue: tab change of single product's tab in summary sticky sidebar
                    theme.$window.on( 'alpha_complete', function () {
                        theme.refreshLayouts();
                        $this.on( 'click', '.nav-link', function () {
                            setTimeout( function () {
                                $this.trigger( 'recalc.pin' );
                            } );
                        } );
                    } );
                }
            );
        }
    }

    /**
     * Refresh layouts
     * 
     * @since 1.0
     * @return {void}
     */
    theme.refreshLayouts = function () {
        $( '.sticky-sidebar, .side-header .custom-header' ).trigger( 'recalc.pin' );
        theme.$window.trigger( 'update_lazyload' );
    }

    /**
     * Make side header sticky in desktop
     *
     * @since 1.0
     * @param {string} selector
     * @return {void}
     */
    theme.stickySideHeader = function ( selector ) {
        if ( $.fn.themeSticky ) {
            var $this = $( selector );

            // Add wrapper class
            if ( $this.length ) {
                if ( !$this.find( '.elementor-edit-area-active' ).length ) {
                    theme.disableSticky = false;

                    $this.closest( '.header-area' ).addClass( 'sticky-sidebar-wrapper' );

                    $this.themeSticky( $.extend( {}, {
                        autoInit: true,
                        minWidth: theme.$body.hasClass( 'side-on-desktop' ) ? 991 : theme.$body.hasClass( 'side-on-tablet' ) ? 767 : theme.$body.hasClass( 'side-on-mobile' ) ? 575 : '',
                        containerSelector: '.sticky-sidebar-wrapper',
                        autoFit: true,
                        activeClass: 'sticky-sidebar-fixed',
                        padding: {
                            top: 0,
                            bottom: 0
                        },
                    }, theme.parseOptions( $this.attr( 'data-sticky-options' ) ) ) );

                    // issue: tab change of single product's tab in summary sticky sidebar
                    theme.$window.on( 'alpha_complete', function () {
                        theme.refreshLayouts();
                    } );
                } else {
                    theme.disableSticky = true;
                    $this.trigger( 'recalc.pin' );
                }
            }
        }
    }

    theme.scrolltoContainer = function ( $container, timeout ) {
        if ( $container.get( 0 ) ) {
            if ( window.innerWidth < 992 ) {
                $( '.sidebar-overlay' ).trigger( 'click' );
            }
            if ( !timeout ) {
                timeout = 600;
            }
            $( 'html, body' ).stop().animate( {
                scrollTop: $container.offset().top - $( '.scroll-progress' ).height() - ( $( '#wpadminbar' ).length ? $( '#wpadminbar' ).outerHeight() : 0 )
            }, timeout, 'easeOutQuad' );
        }
    }
    theme.priceSlider = function () {
        // Slider For category pages / filter price
        if ( $.fn.slider ) {
            $( document.body ).on(
                'price_slider_create price_slider_slide',
                function ( event, min, max ) {

                    $( '.price_label span.from' ).html(
                        accounting.formatMoney(
                            min,
                            {
                                symbol: woocommerce_price_slider_params.currency_format_symbol,
                                decimal: woocommerce_price_slider_params.currency_format_decimal_sep,
                                thousand: woocommerce_price_slider_params.currency_format_thousand_sep,
                                precision: woocommerce_price_slider_params.currency_format_num_decimals,
                                format: woocommerce_price_slider_params.currency_format
                            }
                        )
                    );

                    $( '.price_label span.to' ).html(
                        accounting.formatMoney(
                            max,
                            {
                                symbol: woocommerce_price_slider_params.currency_format_symbol,
                                decimal: woocommerce_price_slider_params.currency_format_decimal_sep,
                                thousand: woocommerce_price_slider_params.currency_format_thousand_sep,
                                precision: woocommerce_price_slider_params.currency_format_num_decimals,
                                format: woocommerce_price_slider_params.currency_format
                            }
                        )
                    );

                    $( '.ui-slider-handle:first-of-type' ).html( '<span class="noUi-tooltip">' + $( '.price_label span.from' ).html() + '</span>' );
                    $( '.ui-slider-handle:last-of-type' ).html( '<span class="noUi-tooltip">' + $( '.price_label span.to' ).html() + '</span>' );

                    $( document.body ).trigger( 'price_slider_updated', [ min, max ] );
                }
            );
            this.initPriceFilter();
            theme.$body.on( 'alpha_ajax_finish_layout', this.initPriceFilter );
        }

        $( 'body' ).on(
            'price_slider_change',
            function () {
                $( '.price_slider_wrapper' ).closest( 'form' ).submit();
            }
        )
    }
    theme.initPriceFilter = function () {
        $( 'input#min_price, input#max_price' ).hide();
        $( '#price_slider, .price_label' ).show();

        var min_price = $( '.price_slider_amount #min_price' ).data( 'min' ),
            max_price = $( '.price_slider_amount #max_price' ).data( 'max' ),
            step = $( '.price_slider_amount' ).data( 'step' ) || 1,
            current_min_price = $( '.price_slider_amount #min_price' ).val(),
            current_max_price = $( '.price_slider_amount #max_price' ).val();

        $( '#price_slider:not(.ui-slider)' ).slider(
            {
                range: true,
                animate: true,
                min: min_price,
                max: max_price,
                step: step,
                tooltips: true,
                values: [ current_min_price, current_max_price ],
                create: function () {

                    $( '.price_slider_amount #min_price' ).val( current_min_price );
                    $( '.price_slider_amount #max_price' ).val( current_max_price );

                    $( document.body ).trigger( 'price_slider_create', [ current_min_price, current_max_price ] );
                },
                slide: function ( event, ui ) {

                    $( 'input#min_price' ).val( ui.values[ 0 ] );
                    $( 'input#max_price' ).val( ui.values[ 1 ] );

                    $( document.body ).trigger( 'price_slider_slide', [ ui.values[ 0 ], ui.values[ 1 ] ] );
                },
                change: function ( event, ui ) {
                    $( 'body' ).on(
                        'click',
                        '.filter-button',
                        function () {
                            $( document.body ).trigger( 'price_slider_change', [ ui.values[ 0 ], ui.values[ 1 ] ] );
                        }
                    )

                }
            }
        );
    }

    /**
     * Initialize WPForms - Label Floating
     *
     * @since 4.0
     */
    theme.initWPForms = function () {
        $( document.body )
            .on( 'focusin', '.label-floating input, .label-floating textarea', function ( e ) {
                $( e.currentTarget ).closest( '.wpforms-field' ).addClass( 'field-float' );
            } )
            .on( 'focusout', '.label-floating input, .label-floating textarea', function ( e ) {
                e.currentTarget.value || $( e.currentTarget ).closest( '.wpforms-field' ).removeClass( 'field-float' );
            } )
    }


    /**
     * Active Current Sticky Nav
     * 
     * @since 1.0
     */
    theme.activeMenuItems = ( function () {
        function getTarget( href ) {
            if ( '#' == href || href.endsWith( '#' ) ) {
                return false;
            }
            var target;

            if ( href.indexOf( '#' ) == 0 ) {
                target = $( href );
            } else {
                var url = window.location.href;
                url = url.substring( url.indexOf( '://' ) + 3 );
                if ( url.indexOf( '#' ) != -1 )
                    url = url.substring( 0, url.indexOf( '#' ) );
                href = href.substring( href.indexOf( '://' ) + 3 );
                href = href.substring( href.indexOf( url ) + url.length );
                if ( href.indexOf( '#' ) == 0 ) {
                    target = $( href );
                }
            }
            return target;
        }
        function activeMenuItem() {
            var scrollPos = $( window ).scrollTop(),
                $adminbar = $( '#wpadminbar' ),
                offset = 100;

            if ( theme.$body.innerHeight() - theme.$window.height() - offset < scrollPos ) scrollPos = theme.$body.height() - offset;
            else if ( scrollPos > offset ) scrollPos += theme.$window.height() / 2;
            else scrollPos = offset;

            var $menu_items = $( '.menu-item > a[href*="#"], .sticky-nav-container .nav > li > a[href*="#"]' );
            if ( $menu_items.length ) {
                $menu_items.each( function () {
                    var $this = $( this ),
                        href = $this.attr( 'href' ),
                        target = getTarget( href ),
                        activeClass = 'current-menu-item';

                    if ( $this.closest( '.sticky-nav-container' ).length ) {
                        activeClass = 'active';
                    }

                    if ( target && target.get( 0 ) ) {
                        var scrollTo = target.offset().top,
                            $parent = $this.parent();

                        if ( $adminbar.length ) {
                            scrollTo = parseInt( scrollTo - $adminbar.innerHeight() );
                        }

                        if ( scrollTo <= scrollPos ) {
                            $parent.siblings().removeClass( activeClass );
                            $parent.addClass( activeClass );
                        } else {
                            $parent.removeClass( activeClass );
                        }
                        if ( scrollTo + $( target ).outerHeight() < scrollPos ) {
                            $parent.removeClass( activeClass );
                        }
                    }

                } )
            }
        }

        function refresh() {
            var $sticky_container = $( '.sticky-nav-container' ),
                options = $sticky_container.find( '.nav-secondary' ).data( 'plugin-options' ),
                minWidth = options ? options.minWidth : 320;

            $sticky_container.each( function () {
                var $this = $( this );
                if ( minWidth > window.innerWidth && $this.hasClass( 'fixed' ) ) {
                    $this.parent().css( 'height', '' )
                    $this.removeClass( 'fixed' ).css( { 'margin-top': '', 'margin-bottom': '', 'z-index': '' } );
                }
            } )
        }

        return function () {
            activeMenuItem();
            theme.$window.on( 'sticky_refresh.alpha', refresh );
            window.addEventListener( 'scroll', activeMenuItem, { passive: true } );
        }
    } )();

    /**
     * Theme Setup
     */
    $( window )
        .on( 'alpha_load', function () {
            if ( theme.$body.hasClass( 'elementor-editor-active' ) ) {				// Sticky Side Header
                elementor.on( 'document:loaded', function () {
                    theme.stickySideHeader( '.side-header .custom-header' );
                } );
            } else {
                theme.stickySideHeader( '.side-header .custom-header' );
            }


            /**
             * Initialize Sticky Content
             * 
             * @class StickyContent
             * @since 1.0
             * @param {string, Object} selector
             * @param {Object} options
             * @return {void}
             */
            theme.stickyContent = ( function () {
                function StickyContent( $el, options ) {
                    return this.init( $el, options );
                }

                function refreshAll() {
                    theme.$window.trigger( 'sticky_refresh.alpha', {
                        index: 0,
                        offsetTop: window.innerWidth > 600 && $( '#wp-toolbar' ).length && $( '#wp-toolbar' ).parent().is( ':visible' ) ? $( '#wp-toolbar' ).parent().outerHeight() : 0,
                        offsetBottom: 0
                    } );
                }

                function refreshAllSize( e ) {
                    if ( !e || theme.windowResized( e.timeStamp ) ) {
                        theme.$window.trigger( 'sticky_refresh_size.alpha' );
                        theme.requestFrame( refreshAll );
                    }
                }

                StickyContent.prototype.init = function ( $el, options ) {
                    this.$el = $el;
                    this.options = $.extend( true, {}, theme.defaults.sticky, options, theme.parseOptions( $el.attr( 'data-sticky-options' ) ) );
                    this.scrollPos = window.pageYOffset; // issue: heavy js performance : 30.7ms
                    this.originalHeight = this.$el.outerHeight();
                    theme.$window
                        .on( 'sticky_refresh.alpha', this.refresh.bind( this ) )
                        .on( 'sticky_refresh_size.alpha', this.refreshSize.bind( this ) );
                }

                StickyContent.prototype.refreshSize = function ( e ) {

                    var beWrap = window.innerWidth >= this.options.minWidth && window.innerWidth <= this.options.maxWidth;
                    if ( typeof this.top == 'undefined' ) {
                        this.top = this.options.top;
                    }

                    if ( window.innerWidth >= 768 && this.getTop ) {
                        this.top = this.getTop();
                    } else if ( !this.options.top ) {
                        this.top = this.isWrap ?
                            this.$el.parent().offset().top :
                            this.$el.offset().top + this.$el[ 0 ].offsetHeight;

                        // if sticky header has toggle dropdown menu, increase top
                        if ( this.$el.find( '.toggle-menu.show-home' ).length ) {
                            this.top += this.$el.find( '.toggle-menu .dropdown-box' )[ 0 ].offsetHeight;
                        }
                    }

                    if ( !this.isWrap ) {
                        beWrap && this.wrap();
                    } else {
                        beWrap || this.unwrap();
                    }
                    e && theme.requestTimeout( this.refreshSize.bind( this ), 50 );

                }

                StickyContent.prototype.wrap = function () {
                    this.$el.wrap( '<div class="sticky-content-wrapper"></div>' );
                    this.$el.closest( '.toolbox-horizontal' ).addClass( 'horizontal-fixed' );
                    this.isWrap = true;
                }

                StickyContent.prototype.unwrap = function () {
                    this.$el.unwrap( '.sticky-content-wrapper' );
                    this.$el.closest( '.toolbox-horizontal' ).removeClass( 'horizontal-fixed' );
                    this.isWrap = false;
                }

                StickyContent.prototype.refresh = function ( e, data ) {
                    var pageYOffset = window.pageYOffset /* + data.offsetTop*/; // issue: heavy js performance, 6.7ms
                    var $el = this.$el;

                    this.refreshSize();
                    $( '.fixed.fix-top' ).each( function () {
                        if ( $( this ).is( ':visible' ) ) {
                            pageYOffset += $( this ).outerHeight();
                        }
                    } );

                    var needUnsticky = false;
                    if ( theme.$body.hasClass( 'side-header' ) ) {
                        if ( ( theme.$body.hasClass( 'side-on-desktop' ) && window.innerWidth > 991 ) ||
                            ( theme.$body.hasClass( 'side-on-tablet' ) && window.innerWidth > 767 ) ||
                            ( theme.$body.hasClass( 'side-on-mobile' ) && window.innerWidth > 575 ) ||
                            ( !theme.$body.hasClass( 'side-on-desktop' ) && !theme.$body.hasClass( 'side-on-tablet' ) && !theme.$body.hasClass( 'side-on-mobile' ) ) ) {
                            needUnsticky = true;
                        }
                    }

                    // Make sticky
                    if ( !needUnsticky && ( pageYOffset >= this.top + this.originalHeight ) && this.isWrap ) {

                        // calculate height
                        this.height = $el[ 0 ].offsetHeight;
                        $el.hasClass( 'sticky-content-transparent' ) || $el.hasClass( 'fixed' ) || $el.parent().css( 'height', this.height + 'px' );

                        // update sticky status
                        if ( this.options.scrollMode ) {
                            if ( ( this.scrollPos >= window.pageYOffset && $el.hasClass( 'fix-top' ) ) ||
                                this.scrollPos <= window.pageYOffset && $el.hasClass( 'fix-bottom' ) ) {
                                $el.addClass( 'fixed' );
                                this.onFixed && this.onFixed();
                            } else {
                                $el.removeClass( 'fixed' ).css( { 'margin-top': '', 'margin-bottom': '', 'z-index': '' } );
                                this.onUnfixed && this.onUnfixed();
                            }
                            this.scrollPos = window.pageYOffset;
                        } else {
                            $el.addClass( 'fixed' );
                            this.onFixed && this.onFixed();
                        }

                        // update sticky order
                        if ( $el.hasClass( 'fixed' ) && $el.hasClass( 'fix-top' ) ) {
                            // this.zIndex = this.options.max_index - data.index;
                            this.zIndex = this.options.max_index - $( '.fix-top' ).index( $el );
                            $el.css( { 'margin-top': data.offsetTop + 'px', 'z-index': this.zIndex } );
                        } else if ( $el.hasClass( 'fixed' ) && $el.hasClass( 'fix-bottom' ) ) {
                            this.zIndex = this.options.max_index - data.index;
                            $el.css( { 'margin-bottom': data.offsetBottom + 'px', 'z-index': this.zIndex } );
                        } else {
                            $el.css( { 'transition': 'opacity .5s' } );
                        }

                        // stack offset
                        if ( $el.hasClass( 'fixed' ) ) {
                            if ( $el.hasClass( 'fix-top' ) ) {
                                data.offsetTop += $el[ 0 ].offsetHeight;
                            } else if ( $el.hasClass( 'fix-bottom' ) ) {
                                data.offsetBottom += $el[ 0 ].offsetHeight;
                            }
                        }
                    } else {
                        $el.parent().css( 'height', '' );
                        $el.removeClass( 'fixed' ).css( { 'margin-top': '', 'margin-bottom': '', 'z-index': '' } );
                        this.onUnfixed && this.onUnfixed();
                    }
                    theme.$window.trigger( 'alpha_finish_sticky' );
                }

                // theme.$window.on( 'alpha_complete', function () {
                //     window.addEventListener( 'scroll', refreshAll, { passive: true } );
                //     theme.$window.on( 'resize', refreshAllSize );
                //     setTimeout( function () {
                //         refreshAllSize();
                //     }, 1000 );
                // } )

                return function ( selector, options ) {
                    theme.$( selector ).each( function () {
                        var $this = $( this );
                        $this.data( 'sticky-content' ) || $this.data( 'sticky-content', new StickyContent( $this, options ) );
                    } )
                }
            } )()
        } )
        .on( 'alpha_complete', function () {
            theme.initWPForms();                                 // Intiialize wpforms
            theme.activeMenuItems();                             // Initialize Active Current Sticky Nav
            theme.priceSlider();

            // Stop collapsible widgets
            theme.$body.off( 'click', '.sidebar .widget-collapsible .widget-title' );
        } );
} )( jQuery );