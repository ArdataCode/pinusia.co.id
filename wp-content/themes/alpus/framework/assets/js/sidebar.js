/**
 * WP Alpha Theme Framework
 * Alpha Sidebar
 * 
 * @package WP Alpha Framework
 * @since 1.2.0
 */

window.theme = window.theme || {};

( function ( $ ) {
    theme.sidebar = ( function () {
        /**
         * Initialize sidebar
         * Sidebar active class will be added to body tag : "sidebar class" + "-active"
         * 
         * @class Sidebar
         * @since 1.0
         * @param {string} name
         * @return {Sidebar}
         */
        function Sidebar( name ) {
            return this.init( name );
        }

        Sidebar.prototype.init = function ( name ) {
            var self = this;

            self.name = name;
            self.$sidebar = $( '.' + name );
            // self.isNavigation = false;

            // If sidebar exists
            if ( self.$sidebar.length ) {
                theme.$window.on( 'resize', function () {
                    theme.$body.removeClass( name + '-active' );
                } );

                // Register toggle event
                self.$sidebar.find( '.sidebar-toggle, .sidebar-toggle-btn' )
                    .add( '.' + name + '-toggle' )
                    .on( 'click', function ( e ) {
                        self.toggle();
                        e.preventDefault();
                        theme.$window.trigger( 'update_lazyload' );
                        $( '.sticky-sidebar' ).trigger( 'recalc.pin.left', [ 400 ] );
                    } );

                // Register close event
                self.$sidebar.find( '.sidebar-overlay, .sidebar-close' )
                    .on( 'click', function ( e ) {
                        e.stopPropagation();
                        self.toggle( 'close' );
                        e.preventDefault();
                        $( '.sticky-sidebar' ).trigger( 'recalc.pin.left', [ 400 ] );
                    } );


                // run lazyload on scroll
                self.$sidebar.find( '.sidebar-content' ).on( 'scroll', function () {
                    theme.$window.trigger( 'update_lazyload' );
                } );
            }
            return false;
        }

        Sidebar.prototype.toggle = function ( mode ) {
            var isOpened = theme.$body.hasClass( this.name + '-active' );
            if ( mode && mode == 'close' && !isOpened ) {
                return;
            }

            var width = $( '.' + this.name + ' .sidebar-content' ).outerWidth();
            var marginLeft = isOpened ? '' : ( 'right-sidebar' == this.name ? - width : width );
            var marginRight = isOpened ? '' : ( 'right-sidebar' == this.name ? width : - width );

            // move close button because of scroll bar width
            this.$sidebar.find( '.sidebar-overlay .sidebar-close' ).css( 'margin-left', - ( window.innerWidth - document.body.clientWidth ) );

            // activate sidebar
            theme.$body.toggleClass( this.name + '-active' ).removeClass( 'closed' );

            theme.call( theme.refreshLayouts, 300 );
        }

        theme.$window.on( 'alpha_complete', function () {
            $( '.sidebar' ).length && theme.$window.smartresize( function () {
                setTimeout( function () {
                    theme.$window.trigger( 'update_lazyload' );
                }, 300 );
            } );
        } )

        return function ( name ) {
            return new Sidebar().init( name );
        }
    } )();

    $( window ).on( 'alpha_complete', function () {
        theme.sidebar( 'left-sidebar' );                     // Initialize left sidebar
        theme.sidebar( 'right-sidebar' );                    // Initialize right sidebar
        theme.sidebar( 'top-sidebar' );                      // Initialize horizontal filter widgets
    } );
} )( window.jQuery );

