/**
 * Javascript Library for Layout Builder Filter
 * 
 * Page Layouts Menu
 * 
 * @author     D-THEMES
 * @since      1.0
 * @package    WP Alpha Framework
 * @subpackage Theme
 */
'use strict';

window.themeAdmin = window.themeAdmin || {};

( function ( $ ) {
    /**
     * Layout Builder Menu Class
     * 
     * @since 1.0
     */
    var LayoutBuilderMenu = {

        /**
         * Add Menu Item
         * 
         * @param {jQuery} $wrapper 
         * @param {Object} item 
         * @param {Boolean} child 
         * 
         * @since 1.0
         */
        addMenuItem: function ( $wrapper, item, child = false ) {
            let html = '<li class="layout-part ' + ( child ? 'child' : '' ) + '">' +
                '<label class="layout-part-label">' + item.name + '</label>' +
                '<a href="' + item.layout_url + '">' + item.layout + '</a>';

            if ( item.block ) {
                html += ' | <a href="' + item.block_url + '">' + item.block + '</a>';
            }
            html += '</li>';

            $( html ).appendTo( $wrapper );
        },

        /**
         * Add Parent Item
         * 
         * @param {jQuery} $wrapper 
         * @param {String} name 
         * 
         * @since 1.0
         */
        addParentItem: function ( $wrapper, name ) {
            let html = '<li class="layout-part parent">' +
                '<label class="layout-part-label">' + name + '</label>';
            html += '</li>';

            $( html ).appendTo( $wrapper );
        },

        /**
         * Add Menu Content
         * 
         * @param {jQuery} $wrapper 
         * 
         * @since 1.0
         */
        addMenuContent: function ( $wrapper ) {
            let html = '<div class="ab-sub-wrapper"><ul class="ab-submenu"></ul></div>';

            $( html ).appendTo( $wrapper );
            let $dropdown = $wrapper.find( '.ab-submenu' );
            self = this;

            if ( alpha_layout_vars.layout_parts ) {
                let layouts = JSON.parse( alpha_layout_vars.layout_parts );
                if ( !layouts.length ) {
                    $dropdown.html( '<li style="float: none">' + wp.i18n.__( 'There is no layout option.', 'alpha' ) + '</li>' );
                }

                layouts.forEach( function ( layout_parts ) {
                    if ( 1 == layout_parts.length && layout_parts[ 0 ][ 'layout_part' ] == layout_parts[ 0 ][ 'name' ] ) {
                        self.addMenuItem( $dropdown, layout_parts[ 0 ] );
                    } else {
                        self.addParentItem( $dropdown, layout_parts[ 0 ][ 'layout_part' ] );
                        layout_parts.forEach( function ( item ) {
                            self.addMenuItem( $dropdown, item, true );
                        } );
                    }
                } );
            }
        },

        /**
         * Initialize Layout Menu
         * 
         * @param {String} selector 
         * 
         * @since 1.0
         */
        init: function ( selector ) {
            let $dropdown = $( selector );
            if ( $dropdown.length ) {
                this.addMenuContent( $dropdown );
            }
        }
    };

    /**
     * Setup Layout Builder Menu
     */
    themeAdmin.LayoutBuilderMenu = LayoutBuilderMenu;
    $( document ).ready( function () {
        LayoutBuilderMenu.init( '#wp-admin-bar-alpha-layout' );
    } );
} )( jQuery );
