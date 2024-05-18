/**
 * Javascript Library for Admin
 * 
 * - Admin Dashboard
 * 
 * @since 1.0
 * @package  Alpus Theme
 */
'use strict';

window.themeAdmin = window.themeAdmin || {};

// Admin Dashboard
( function ( wp, $ ) {
    themeAdmin.$body = $( 'body' );
    themeAdmin.$window = $( window );

    /**
     * Ajax Activation
     * 
     * @since 1.0
     */
    themeAdmin.initActivation = function () {

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
        } );
    };
} )( wp, jQuery );