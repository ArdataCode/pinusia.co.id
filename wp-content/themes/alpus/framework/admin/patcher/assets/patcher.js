/**
 * Alpha Patcher Admin JS
 * 
 * @since 1.3.0
 */
jQuery( document ).ready( function( $ ) {
    'use strict';
    $( '#patch-apply:not(.inactive)' ).on( 'click', function( e ) {
        e.preventDefault();
        $( '#patcher-tbody' ).addClass( 'loading' ).append( '<i class="alpha-ajax-loader"></i>' );
        $( '.alpha-patch-layout .button' ).attr( 'disabled', true );
        $.ajax( {
            url: alpha_admin_vars.ajax_url,
            type: 'POST',
            data: {
                'action': 'alpha_apply_patches',
                'nonce': alpha_admin_vars.nonce,
            },
            success: function( response ) {
                $( '.apply-alert' ).remove();
                if ( response.success ) {
                    if ( response.data ) {
                        var update_patches = response.data.update,
                            delete_patches = response.data.delete;
                        if ( 'object' == typeof update_patches ) {
                            update_patches = Object.keys( update_patches );
                            update_patches.forEach( patch => {
                                $( '[data-path="update-' + patch ).remove();
                            } );
                        }

                        if ( 'object' == typeof delete_patches ) {
                            delete_patches = Object.keys( delete_patches );
                            delete_patches.forEach( patch => {
                                $( '[data-path="delete-' + patch ).remove();
                            } );
                        }
                    }


                    if ( response.data.error ) {
                        console.log( response.data );
                        $( '.alpha-patch-table-main' ).prepend( '<div class="apply-alert error"><p>' + wp.i18n.__( 'The below patches could not be applied. Because your files have write permission or aren\'t existed.', 'alpha' ) + '</p></div>' );
                    } else {
                        $( '.alpha-patch-table-main' ).prepend( '<div class="apply-alert updated"><p>' + wp.i18n.__( 'All files patched successfully.', 'alpha' ) + '</p></div>' );

                        // Remove Apply Patch Button
                        $('.action-footer .button-primary').remove();

                        // Change Patch Status Icon
                        $('.alpha-patch-log:not(.alpha-patch-applied) svg:first-child').css('display', 'none');
                        $('.alpha-patch-log:not(.alpha-patch-applied) svg:nth-child(2)').css('display', 'block');

                        // Update Changelog Style.
                        $('.alpha-patch-log:not(alpha-patch-applied)').addClass('alpha-patch-applied');
                    }
                } else {
                    $( '.alpha-patch-table-main' ).prepend( '<div class="apply-alert error"><p>' + wp.i18n.__( 'The API server could not be reached.', 'alpha' ) + '</p></div>' );
                }
                $( '#patcher-tbody' ).removeClass( 'loading' ).find( '.alpha-ajax-loader' ).remove();
                $( '.alpha-patch-layout .button' ).removeAttr( 'disabled' );
            },
        } );
    } )
} );