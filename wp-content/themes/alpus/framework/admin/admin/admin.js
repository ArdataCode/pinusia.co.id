/**
 * Javascript Library for Admin
 * 
 * - Admin Dashboard
 * 
 * @since 1.0
 * @package  Alpha FrameWork
 */
'use strict';

window.themeAdmin = window.themeAdmin || {};

// Admin Dashboard
( function ( wp, $ ) {
    themeAdmin.$body = $( 'body' );
    themeAdmin.$window = $( window );

    /**
     * Set cookie
     * 
     * @since 1.0
     * @param {string} name Cookie name
     * @param {string} value Cookie value
     * @param {number} exdays Expire period
     * @return {void}
     */
    themeAdmin.setCookie = function ( name, value, exdays ) {
        var date = new Date();
        date.setTime( date.getTime() + ( exdays * 24 * 60 * 60 * 1000 ) );
        document.cookie = name + "=" + value + ";expires=" + date.toUTCString() + ";path=/";
    }

    /**
     * Get cookie
     *
     * @since 1.0
     * @param {string} name Cookie name
     * @return {string} Cookie value
     */
    themeAdmin.getCookie = function ( name ) {
        var n = name + "=";
        var ca = document.cookie.split( ';' );
        for ( var i = 0; i < ca.length; ++i ) {
            var c = ca[ i ];
            while ( c.charAt( 0 ) == ' ' ) {
                c = c.substring( 1 );
            }
            if ( c.indexOf( n ) == 0 ) {
                return c.substring( n.length, c.length );
            }
        }
        return "";
    }

    /**
     * initGreeting
     * 
     * Change greeting text.
     * 
     * @since 1.0
     */
    themeAdmin.initGreeting = ( function ( selector ) {
        var today = new Date(),
            hours = today.getHours(),
            $greet = $( selector );

        if ( hours <= 12 ) {
            $greet.html( 'Good Morning ' );
        } else if ( hours <= 18 ) {
            $greet.html( 'Good Afternoon ' );
        } else {
            $greet.html( 'Good Evening ' );
        }
    } )( '.greeting' );

    /**
     * initStickyLinks
     * 
     * @since 1.0
     */
    themeAdmin.initStickyLinks = ( function ( selector ) {
        var $wrapper = $( selector );
        $wrapper.find( 'a > span' ).each( function () {
            var $this = $( this );
            $this.css( 'width', this.clientWidth );
        } );
        $wrapper.addClass( 'loaded' );

    } )( '.alpha-admin-sticky-buttons' );


    /**
     * initChangeLogNavigation
     * 
     * @since 1.0
     */
    themeAdmin.initChangeLogNavigation = function ( selector ) {
        var $wrapper = $( selector ).closest( '.alpha-changelog-section' ),
            $logs = $wrapper.find( '.alpha-changelogs' );
        themeAdmin.$body.on( 'click', selector, function ( e ) {
            var $this = $( this ),
                id = $this.attr( 'href' ),
                $log = $logs.find( id );
            if ( $logs.length > 0 ) {
                $logs[ 0 ].scrollTo( {
                    top: $log[ 0 ].offsetTop,
                    left: 0,
                    behavior: 'smooth'
                } );
            }
            $this.parent().addClass( 'active' ).siblings().removeClass( 'active' );
            e.preventDefault();
        } );
        $logs.on( 'scroll', function ( e ) {
            var $this = $( this ),
                pos = this.scrollTop,
                height = this.clientHeight;
            $this.find( '.alpha-changelog' ).each( function ( index ) {
                var $item = $( this ),
                    itemPos = this.offsetTop;

                if ( pos <= itemPos && itemPos <= pos + height / 2 ) {
                    $wrapper.find( '[href="#' + $item.attr( 'id' ) + '"]' ).parent().addClass( 'active' ).siblings().removeClass( 'active' );
                    return;
                }
            } );
        } );
    }

    /**
     * Ajax Activation
     * 
     * @since 1.0
     */
    themeAdmin.initActivation = function () {
        // Dropdown Hide
        themeAdmin.$body.on( 'click', '.alpha-admin-panel', function ( e ) {
            if ( document.querySelector( '.alpha-active-content' ) && !$.contains( document.querySelector( '.alpha-active-content' ), e.target ) ) {
                $( '.alpha-active-dropdown' ).removeClass( 'show' );
            }
        } );

        themeAdmin.$body.on( 'submit', '#alpha_registration', function ( e ) {
            e.preventDefault();
            var $form = $( this ),
                $wrapper = $form.parent(),
                $toggleBtn = $( '.alpha-active-toggle' ),
                data = {
                    action: 'alpha_activation',
                    code: $form.find( '#alpha_purchase_code' ).val(),
                    form_action: $form.find( '[name="action"]' ).val(),
                    _wp_http_referer: $form.find( '[name="_wp_http_referer"]' ).val(),
                    _wpnonce: $form.find( '[name="_wpnonce"]' ).val(),
                    alpha_registration: true,
                    nonce: alpha_admin_vars.nonce,
                };
            $wrapper.addClass( 'loading' );
            $.ajax( {
                type: "POST",
                url: alpha_admin_vars.ajax_url,
                data: data
            } ).done( function ( response ) {
                var $response = $( response ),
                    $activeAction = $response.find( '#alpha_active_action' ),
                    redirect_url = $response.find( '#alpha_register_redirect' ).length ? $response.find( '#alpha_register_redirect' ).val() : '';
                $wrapper.removeClass( 'loading' );
                $wrapper.html( response );
                $toggleBtn.toggleClass( 'activated', $activeAction.val() === 'unregister' );
                $toggleBtn.html( $activeAction.data( 'toggle-html' ) );
                if ( redirect_url ) {
                    setTimeout( function() {
                        window.location.href = redirect_url;
                    }, 500 );
                }
            } );
        } ).on( 'click', '.alpha-active-toggle', function ( e ) {
            e.preventDefault();
            $( '#alpha_active_wrapper' ).slideToggle();
        } ).on( 'click', '.alpha-toggle-howto', function () {
            $( '.alpha-active-howto' ).slideToggle();
        } );
    };

    /**
     * initSlider
     * 
     * Init Swiper Slider
     * 
     * @sinde 1.0
     */
    themeAdmin.initSlider = function ( selector, options ) {
        if ( 'undefined' != window.Swiper ) {
            $( selector ).each( function () {
                let $this = $( this );
                $this.children().addClass( 'swiper-slide' );
                let
                    slider = new Swiper( $this.parent()[ 0 ], options );

                $this.trigger( 'initialize.slider', [ slider ] );
                $this.data( 'slider', slider );
            } );
        }
    }

    themeAdmin.tab = function () {

        themeAdmin.$body
            // tab nav link
            .on( 'click', '.nav-tabs .nav-link', function ( e ) {
                var $link = $( this );

                // if tab is loading, return
                if ( $link.closest( '.nav-tabs' ).hasClass( 'loading' ) ) {
                    return;
                }

                // get href
                var href = 'SPAN' == this.tagName ? $link.data( 'href' ) : $link.attr( 'href' );

                // get panel
                var $panel;
                if ( '#' == href ) {
                    $panel = $link.closest( '.nav' ).siblings( '.tab-content' ).children( '.tab-pane' ).eq( $link.parent().index() );
                } else {
                    $panel = $( ( '#' == href.substring( 0, 1 ) ? '' : '#' ) + href );
                }
                if ( !$panel.length ) {
                    return;
                }

                e.preventDefault();

                var $activePanel = $panel.parent().children( '.active' );


                if ( $link.hasClass( "active" ) || !href ) {
                    return;
                }
                // change active link
                $link.parent().parent().find( '.active' ).removeClass( 'active' );
                $link.addClass( 'active' );

                // change tab instantly
                _changeTab();

                // Change tab panel
                function _changeTab() {
                    // themeAdmin.slider($panel.find('.swiper-wrapper'));
                    $activePanel.removeClass( 'in active' );
                    $panel.addClass( 'active in' );
                }
            } )
    }

    /**
    * prompt
    * 
    * Show a dialog
    * 
    * @since 1.0
    */
    themeAdmin.prompt = {
        options: {
            title: wp.i18n.__( 'Title', 'alpha' ),
            content: '',
            closeOnOverlay: true,
            disMiss: true,
            customClass: '',
            actions: [ {
                title: wp.i18n.__( 'OK', 'alpha' ),
            } ]
        },
        init: function () {
            if ( $( '.alpha-dialog-wrapper' ).length ) {
                return;
            }

            $( document.body ).append( '<div class="alpha-dialog-wrapper"><div class="alpha-dialog-overlay"></div><div class="alpha-dialog"></div></div>' );
            this.dialog = $( '.alpha-dialog-wrapper' );

            $( document.body ).on( 'click', '.alpha-dialog-wrapper .alpha-dialog-close', function ( e ) {
                e.preventDefault();

                $( this ).closest( '.alpha-dialog-wrapper' ).addClass( 'alpha-dialog-closing' ).delay( 600 ).queue( function () {
                    $( this ).removeClass( 'show alpha-dialog-closing' ).dequeue();
                } );
            } );

            $( document.body ).on( 'click', '.alpha-dialog-wrapper .btn-yes', function ( e ) {
                e.preventDefault();
                if ( this.options.actions[ 0 ].callback ) {
                    this.options.actions[ 0 ].callback();
                }
                if ( 'undefined' == typeof ( this.options.actions[ 0 ].noClose ) ) {
                $( e.currentTarget ).closest( '.alpha-dialog-wrapper' ).find( '.alpha-dialog-close' ).trigger( 'click' );
                }

            }.bind( this ) );

            $( document.body ).on( 'click', '.alpha-dialog-wrapper .btn-no', function ( e ) {
                e.preventDefault();
                if ( this.options.actions[ 1 ].callback ) {
                    this.options.actions[ 1 ].callback();
                }
                if ( 'undefined' == typeof ( this.options.actions[ 1 ].noClose ) ) {
                $( e.currentTarget ).closest( '.alpha-dialog-wrapper' ).find( '.alpha-dialog-close' ).trigger( 'click' );
                }
            }.bind( this ) );

            $( document.body ).on( 'click', '.alpha-dialog-wrapper.close-on-overlay .alpha-dialog-overlay', function ( e ) {
                e.preventDefault();
                $( e.currentTarget ).closest( '.alpha-dialog-wrapper' ).find( '.alpha-dialog-close' ).trigger( 'click' );
            } );

            document.addEventListener( 'keydown', function ( e ) {
                var keyName = e.key;
                if ( 'Escape' == keyName ) {
                    if ( $( '.alpha-dialog-wrapper:not(.close-disabled)' ).length ) {
                        $( '.alpha-dialog-wrapper' ).find( '.alpha-dialog-close' ).trigger( 'click' );
                    }
                }
            } );
        },
        showDialog: function ( options ) {

            this.init();
            this.options = $.extend( {}, this.options, options );

            if ( !this.dialog.length ) {
                return;
            }

            if ( ! this.options.disMiss ) {
                this.dialog.addClass( 'close-disabled' );
            } else if ( this.options.closeOnOverlay ) {
                this.dialog.addClass( 'close-on-overlay' );
            }

            this.dialog.addClass( ' ' + this.options.customClass );

            var dialogClose = this.options.disMiss ? '<a href="#" class="alpha-dialog-close"></a>' : '', dialogHtml = '';

            dialogHtml += '<div class="alpha-dialog-header"><h3 class="alpha-dialog-title">' + this.options.title + '</h3>' + dialogClose + '</div>';
            dialogHtml += '<div class="alpha-dialog-content"><p>' + this.options.content + '</p></div>';
            dialogHtml += '<div class="alpha-dialog-footer">';

            var yesBtn = '', noBtn = '';

            if ( this.options.actions.length < 2 ) {
                yesBtn = '<button class="btn-yes">' + this.options.actions[ 0 ].title + '</button>';
            } else {
                yesBtn = '<button class="btn-yes">' + ( 'undefined' != typeof this.options.actions[ 0 ].title ? this.options.actions[ 0 ].title : wp.i18n.__( 'Yes', 'alpha' ) ) + '</button>';
                noBtn = '<button class="btn-no">' + ( 'undefined' != typeof this.options.actions[ 1 ].title ? this.options.actions[ 1 ].title : wp.i18n.__( 'No', 'alpha' ) ) + '</button>';
            }
            dialogHtml += noBtn + yesBtn + '</div></div>';

            this.dialog.find( '.alpha-dialog' ).html( dialogHtml );
            this.dialog.addClass( 'show' );

        }
    };


    themeAdmin.init = function () {
        themeAdmin.initActivation();
        themeAdmin.initSlider( '.alpha-demos', {                           // Demos Slider
            slidesPerView: 2,
            spaceBetween: 20,
            breakpoints: {
                768: {
                    slidesPerView: 3
                },
                992: {
                    slidesPerView: 4
                },
                1200: {
                    slidesPerView: 5
                }
            }
        } );
        themeAdmin.initSlider( '.alpha-products', {                           // Products Slider
            slidesPerView: 1,
            spaceBetween: 30,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                576: {
                    slidesPerView: 2
                },
                768: {
                    slidesPerView: 3
                }
            }
        } );
        themeAdmin.tab();
        themeAdmin.initChangeLogNavigation( '.alpha-log-version > a' );
    }

    $( window ).on( 'load', function () {
        if ( $( 'body.alpha-admin-page' ).length > 0 ) {
            themeAdmin.init();
        }
        if ( $( 'body' ).find( '#customize-controls' ).length && typeof kirkiTooltips == 'undefined' ) {
            themeAdmin.prompt.showDialog( {
                title: wp.i18n.__( 'Install Kirki Customizer', 'alpha' ),
                content: wp.i18n.__( 'You need to install <a href="https://wordpress.org/plugins/kirki/" target="_blank">Kirki Customizer</a> plugin to use full theme options.', 'alpha' ),
                disMiss: false,
                customClass: 'kirki-install-dialog',
                actions: [
                    {
                        title: wp.i18n.__( 'OK', 'alpha' ),
                        callback: function () {
                            window.location.href = "plugins.php";
                        }
                    },
                ]
            } );
        }
    } )
} )( wp, jQuery );