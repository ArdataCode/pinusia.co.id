/**
 * Customizer Script
 * 
 * - Preview Page
 * - Menu Labels
 * - Reset Options
 * - Navigator
 * - Color Pickers
 *
 * @package  Alpha FrameWork
 * @since 1.0
 */
( function ( api, wp, $ ) {
    'use strict';
    $( document ).ready( function () {

        initPreviewPage();
        initMenuLabels();
        initResetOptions();
        initNavigator();
        initGoToPanel();
        initHomeButton();
        $( 'input.alpha-color-picker' ).wpColorPicker();

        // Load other page for previewer
        function initPreviewPage() {
            if ( window.alpha_admin_vars && alpha_admin_vars.page_links ) {
                for ( const section in alpha_admin_vars.page_links ) {
                    if ( alpha_admin_vars.page_links[ section ].is_panel ) {
                        if ( api.panel( section ) ) {
                            api.panel( section ).expanded.bind( function ( t ) {
                                if ( t && alpha_admin_vars.page_links[ section ].url && alpha_admin_vars.page_links[ section ].url != api.previewer.previewUrl() ) {
                                    api.previewer.previewUrl.set( alpha_admin_vars.page_links[ section ].url );
                                }
                            } );
                        }
                    } else {
                        if ( api.section( section ) ) {
                            api.section( section ).expanded.bind( function ( t ) {
                                if ( t && alpha_admin_vars.page_links[ section ].url && alpha_admin_vars.page_links[ section ].url != api.previewer.previewUrl() ) {
                                    api.previewer.previewUrl.set( alpha_admin_vars.page_links[ section ].url );
                                }
                            } );
                        }
                    }
                }
            }
        }

        // Edit Menu Labels
        function initMenuLabels() {
            $( document.body )
                .on( 'click', '#customize-control-cs_new_menu_label .btn-add-label', onAddMenuLabel )
                .on( 'click', '#customize-control-cs_menu_labels .btn-change-label', onChangeMenuLabel )
                .on( 'click', '#customize-control-cs_menu_labels .btn-remove-label', onRemoveMenuLabel )
                .on( 'change', '#customize-control-cs_menu_labels #label-select', onSelectMenuLabel );

            function getMenuLabels() {
                var labels = $( '#customize-control-menu_labels input' ).val();
                return labels ? JSON.parse( labels ) : {};
            }

            function setMenuLabels() {
                var $select = $( '#customize-control-cs_menu_labels #label-select' ),
                    $options = $select.children( 'option' ),
                    labels = {};

                if ( $options.length ) {
                    $options.map(
                        function () {
                            labels[ $( this ).text() ] = $( this ).val();
                        }
                    )
                }

                var input = window.top.document.querySelector('#customize-control-menu_labels input');
                var nativeInputValueSetter = Object.getOwnPropertyDescriptor(window.HTMLInputElement.prototype, "value").set;

                if ('{}' == JSON.stringify( labels )) {
                    nativeInputValueSetter.call(input, '');
                } else {
                    nativeInputValueSetter.call(input, JSON.stringify( labels ));
                }

                var ev2 = new Event('input', { bubbles: true});
                input.dispatchEvent(ev2);
            }

            function onAddMenuLabel( e ) {
                e.preventDefault();

                var labels = getMenuLabels(),
                    new_text = $( '#customize-control-cs_new_menu_label .label-text' ).val(),
                    new_color = $( '#customize-control-cs_new_menu_label .alpha-color-picker' ).val();

                if ( !new_text || !new_color ) {
                    alert( 'Please input label text and label color' );
                } else if ( undefined != labels[ new_text ] ) {
                    alert( 'This label already exists. Please add another one.' );
                } else {
                    $( '#customize-control-cs_menu_labels select' ).children().prop( 'selected', false );
                    $( '#customize-control-cs_menu_labels select' ).append( '<option value="' + new_color + '" selected>' + new_text + '</option>' );
                    setMenuLabels();
                    $( '#customize-control-cs_new_menu_label .label-text, #customize-control-cs_new_menu_label .alpha-color-picker' ).val( '' );
                    $( '#customize-control-cs_new_menu_label .wp-color-result' ).css( 'background-color', '' );
                    $( '#customize-control-cs_menu_labels #label-select' ).trigger( 'change' );
                }
            }

            function onChangeMenuLabel( e ) {
                e.preventDefault();

                var new_text = $( '#customize-control-cs_menu_labels .menu-label .label-text' ).val(),
                    new_color = $( '#customize-control-cs_menu_labels .menu-label .alpha-color-picker' ).val();

                if ( new_text && new_color ) {
                    $( '#customize-control-cs_menu_labels select option:selected' ).val( new_color ).text( new_text );
                    setMenuLabels();
                }
            }

            function onRemoveMenuLabel( e ) {
                e.preventDefault();

                var cur_text = $( '#customize-control-cs_menu_labels select option:selected' ).text(),
                    cur_color = $( '#customize-control-cs_menu_labels select option:selected' ).val();

                if ( cur_text && cur_color ) {
                    $( '#customize-control-cs_menu_labels select option[value=' + cur_color + ']' ).remove();
                    setMenuLabels();
                    $( '#customize-control-cs_menu_labels select option' ).eq( 0 ).prop( 'selected', true );
                    $( '#customize-control-cs_menu_labels #label-select' ).trigger( 'change' );
                }
            }

            function onSelectMenuLabel( e ) {
                e.preventDefault();

                $( '#customize-control-cs_menu_labels .label-text' ).val( $( this ).find( 'option:selected' ).text() );
                $( '#customize-control-cs_menu_labels .alpha-color-picker' ).val( $( this ).val() );
                $( '#customize-control-cs_menu_labels .wp-color-result' ).css( 'background-color', $( this ).val() );
            }
        }

        // Import / Export / Reset Options
        function initResetOptions() {
            $( document.body )
                .on( 'input', '#customize-control-import_src input, #customize-control-export_src input', onInputFile )
                .on( 'click', '#alpha-import-options', onImportOption )
                .on( 'click', '#alpha-reset-options', onResetOptions );

            function onInputFile( e ) {
                $( this ).closest( 'li' ).next().find( 'button' ).attr( 'disabled', $( this ).val() ? false : true );
            }

            function onImportOption( e ) {
                e.preventDefault();

                if ( !$( '#customize-control-import_src input' ).val() ) {
                    alert( 'Please input source file pathname.' );
                    return;
                }

                if ( !confirm( "Are you sure to import another theme options? All current options will be overwritten." ) ) {
                    return;
                }

                var $this = $( this );
                $( this ).attr( 'disabled', 'disabled' );

                var formData = new FormData();
                formData.append( 'wp_customize', 'on' );
                formData.append( 'action', 'alpha_import_theme_options' );
                formData.append( 'nonce', alpha_customizer_vars.nonce );
                formData.append( 'file', $( '#customize-control-import_src input' )[ 0 ].files[ 0 ] );

                $.ajax( {
                    url: alpha_customizer_vars.ajax_url,
                    data: formData,
                    type: 'post',
                    contentType: false,
                    processData: false,
                    success: function ( response ) {
                        $this.removeAttr( 'disabled' );
                        alert( 'Theme options imported seccessfully.' );
                        window.location.reload();
                    }
                } ).fail( function ( response ) {
                    alert( 'Something went wrong while importing theme options.' );
                    console.log( response );
                } );
            }

            function onResetOptions( e ) {
                e.preventDefault();

                if ( !confirm( "Are you sure to reset all theme options?" ) ) {
                    return;
                }

                $( this ).attr( 'disabled', 'disabled' );

                $.ajax( {
                    url: alpha_customizer_vars.ajax_url,
                    data: {
                        wp_customize: 'on',
                        action: 'alpha_reset_theme_options',
                        nonce: alpha_customizer_vars.nonce
                    },
                    type: 'post',
                    success: function ( response ) {
                        window.location.reload();
                    }
                } ).fail( function ( response ) {
                    console.log( response );
                } );
            }
        }

        function initGoToPanel() {
            // Go to panel on first load.
            var url = location.href,
                idPos = url.search( '#' );

            if ( 0 < idPos ) {
                var target = url.substr( idPos + 1 );
                if ( typeof wp.customize[ 'section' ]( target ) == 'undefined' ) {
                    return;
                }
                setTimeout( function () {
                    wp.customize[ 'section' ]( target ).focus();
                }, 2000 );
            }
        }

        // Customize Navigator
        function initNavigator() {
            $( '.customize-pane-child .accordion-section-title .panel-title' )
                .add( '.customize-pane-child .customize-section-title h3' )
                .append( '<a href="#" class="section-nav-status" title="Customize Navigator"><i class="far fa-star"></i></a>' );

            $( '.customizer-nav-item' ).each( function () {
                $( '#sub-accordion-' + ( 'section' == $( this ).data( 'type' ) ? 'section' : 'panel' ) + '-' + $( this ).data( 'target' ) + ' .section-nav-status' ).addClass( 'active' );
            } )

            $( document.body )
                .on( 'click', '.navigator-toggle', onClickToggle )
                .on( 'click', '.customizer-nav-item', onClickNav )
                .on( 'click', '.section-nav-status', onAddNav )
                .on( 'click', '.customizer-nav-remove', onRemoveNav );

            function onClickToggle( e ) {
                e.preventDefault();
                $( this ).closest( '.customizer-nav' ).toggleClass( 'active' );
            }

            function onClickNav( e ) {
                e.preventDefault();
                api[ $( this ).data( 'type' ) ]( $( this ).data( 'target' ) ).focus();
            }

            function onAddNav( e ) {
                var $this = $( this );
                e.preventDefault();

                if ( !$this.hasClass( 'disabled' ) ) {
                    var $pane = $this.closest( '.customize-pane-child' ),
                        title = '',
                        target = '',
                        type = '';

                    if ( $pane.hasClass( 'control-panel' ) ) {
                        target = $pane[ 0 ].id.replace( 'sub-accordion-panel-', '' );
                        type = 'panel';
                    } else {
                        target = $pane[ 0 ].id.replace( 'sub-accordion-section-', '' );
                        type = 'section';
                    }

                    $this.addClass( 'disabled' ).toggleClass( 'active' );

                    if ( $this.hasClass( 'active' ) ) {
                        if ( $this.closest( '.customize-section-title' ).length ) {
                            var section = $this.closest( '.customize-section-title' ),
                                parent = section.find( '.customize-action' ).text(),
                                current = section.find( 'h3' ).text().replace( parent, '' );
                            var split_pos = parent.indexOf( '▸' );
                            if ( -1 != split_pos ) {
                                parent = parent.slice( split_pos + 1 );
                            } else {
                                parent = '';
                            }
                        } else {
                            parent = '';
                            var current = $this.closest( '.panel-title' ).text();
                        }
                        parent && ( parent = parent + ' / ' );
                        title = parent + current;

                        $( '.customizer-nav-items' ).append( '<li><a href="#" data-target="' + target + '" data-type="' + type + '" class="customizer-nav-item">' + title + '</a><a href="#" class="customizer-nav-remove"><i class="fas fa-trash"></i></a></li>' );
                    } else {
                        $( '.customizer-nav-items .customizer-nav-item[data-target="' + target + '"]' ).parent().fadeOut( 200 ).addClass( 'hidden' );
                    }
                    $this.removeClass( 'disabled' );

                    api.state( 'saved' ).get() && api.state( 'saved' ).set( false );
                }
            }

            function onRemoveNav( e ) {
                e.preventDefault();
                var $li = $( this ).closest( 'li' ),
                    $item = $li.children( '.customizer-nav-item' );

                $li.fadeOut( 200 ).addClass( 'hidden' );
                $( '#sub-accordion-' + ( 'section' == $item.data( 'type' ) ? 'section' : 'panel' ) + '-' + $item.data( 'target' ) + ' .section-nav-status' ).removeClass( 'active' );

                api.state( 'saved' ).get() && api.state( 'saved' ).set( false );
            }

            $( '#customize-save-button-wrapper .save' ).on( 'click', function () {
                var csOptions = { nav: {} };
                $( '.customizer-nav-items li:not(.hidden) .customizer-nav-item' ).each( function () {
                    csOptions['nav'][ $( this ).data( 'target' ) ] = [ $( this ).text(), $( this ).data( 'type' ) ];
                } );

                // csOptions['mlabels'] = $('input#customize-input-menu_labels').val();
                setTimeout( function () {
                    $.ajax( {
                        url: alpha_customizer_vars.ajax_url,
                        data: { wp_customize: 'on', action: 'alpha_save_custom_options', nonce: alpha_customizer_vars.nonce, options: csOptions },
                        type: 'post',
                        dataType: 'json',
                        success: function ( response ) {
                        }
                    } );
                }, 1000 );
            } );
        }

        // Add Home Button
        function initHomeButton() {
            var $homeBtn;

            $homeBtn = $( "<span/>", {
                class: "customize-controls-home dashicons dashicons-admin-home",
                html: '<span class="screen-reader-text">Home</span>'
            } );
            $( "#customize-header-actions" ).append( $homeBtn );
            $homeBtn.on( 'click', function () {
                api.section.each( function ( section ) {
                    if ( section.expanded() ) {
                        section.collapse();
                    }
                } );
                setTimeout( function () {
                    api.panel.each( function ( panel ) {
                        if ( panel.expanded() ) {
                            panel.collapse();
                        }
                    } );
                }, 100 );
            } )
        }
    } );

    /* Kirki Compatibility */
    if ( api && api.controlConstructor && api.controlConstructor[ 'kirki-background' ] ) {
        api.controlConstructor[ 'kirki-background' ] = api.controlConstructor[ 'kirki-background' ].extend( {
            initKirkiControl: function () {

                var control = this,
                    value = control.setting._value,
                    picker = control.container.find( '.kirki-color-control' );

                // Background-Control Init
                if ( _.isUndefined( value[ 'background-image' ] ) ) {
                    control.setting._value = {
                        'background-attachment': '',
                        'background-color': '',
                        'background-image': '',
                        'background-position': '',
                        'background-repeat': '',
                        'background-size': '',
                    };
                }

                // Hide unnecessary controls if the value doesn't have an image.
                if ( _.isUndefined( value[ 'background-image' ] ) || '' === value[ 'background-image' ] ) {
                    control.container.find( '.background-wrapper > .background-repeat' ).hide();
                    control.container.find( '.background-wrapper > .background-position' ).hide();
                    control.container.find( '.background-wrapper > .background-size' ).hide();
                    control.container.find( '.background-wrapper > .background-attachment' ).hide();
                }

                // If we have defined any extra choices, make sure they are passed-on to Iris.
                if ( !_.isUndefined( control.params.choices ) ) {
                    picker.wpColorPicker( control.params.choices );
                }

                // Tweaks to make the "clear" buttons work.
                setTimeout( function () {
                    control.container.find( '.wp-picker-clear' ).on( 'click', function () {
                        control.saveValue( 'background-color', '' );
                    } );
                }, 200 );

                // Color.
                picker.wpColorPicker( {
                    change: function () {
                        setTimeout( function () {
                            control.saveValue( 'background-color', picker.val() );
                        }, 100 );
                    }
                } );

                control.container
                    // Background-Repeat.
                    .on( 'change', '.background-repeat select', function () {
                        control.saveValue( 'background-repeat', jQuery( this ).val() );
                    } )

                    // Background-Size.
                    .on( 'change click', '.background-size input', function () {
                        control.saveValue( 'background-size', jQuery( this ).val() );
                    } )

                    // Background-Position.
                    .on( 'change', '.background-position select', function () {
                        control.saveValue( 'background-position', jQuery( this ).val() );
                    } )

                    // Background-Attachment.
                    .on( 'change click', '.background-attachment input', function () {
                        control.saveValue( 'background-attachment', jQuery( this ).val() );
                    } )

                    // Background-Image.
                    .on( 'click', '.background-image-upload-button', function ( e ) {
                        var image = wp.media( { multiple: false } ).open().on( 'select', function () {

                            // This will return the selected image from the Media Uploader, the result is an object.
                            var uploadedImage = image.state().get( 'selection' ).first(),
                                previewImage = uploadedImage.toJSON().sizes.full.url,
                                imageUrl,
                                imageID,
                                imageWidth,
                                imageHeight,
                                preview,
                                removeButton;

                            if ( !_.isUndefined( uploadedImage.toJSON().sizes.medium ) ) {
                                previewImage = uploadedImage.toJSON().sizes.medium.url;
                            } else if ( !_.isUndefined( uploadedImage.toJSON().sizes.thumbnail ) ) {
                                previewImage = uploadedImage.toJSON().sizes.thumbnail.url;
                            }

                            imageUrl = uploadedImage.toJSON().sizes.full.url;
                            imageID = uploadedImage.toJSON().id;
                            imageWidth = uploadedImage.toJSON().width;
                            imageHeight = uploadedImage.toJSON().height;

                            // Show extra controls if the value has an image.
                            if ( '' !== imageUrl ) {
                                control.container.find( '.background-wrapper > .background-repeat, .background-wrapper > .background-position, .background-wrapper > .background-size, .background-wrapper > .background-attachment' ).show();
                            }

                            control.saveValue( 'background-image', imageUrl );
                            preview = control.container.find( '.placeholder, .thumbnail' );
                            removeButton = control.container.find( '.background-image-upload-remove-button' );

                            if ( preview.length ) {
                                preview.removeClass().addClass( 'thumbnail thumbnail-image' ).html( '<img src="' + previewImage + '" alt="" />' );
                            }
                            if ( removeButton.length ) {
                                removeButton.show();
                            }
                        } );

                        e.preventDefault();
                    } )
                    // Remove uploaded background image
                    .on( 'click', '.background-image-upload-remove-button', function ( e ) {

                        var preview = control.container.find( '.placeholder, .thumbnail' ),
                            removeButton = control.container.find( '.background-image-upload-remove-button' );

                        e.preventDefault();
                        control.saveValue( 'background-image', '' );

                        // Hide unnecessary controls.
                        control.container.find( '.background-wrapper > .background-repeat' ).hide();
                        control.container.find( '.background-wrapper > .background-position' ).hide();
                        control.container.find( '.background-wrapper > .background-size' ).hide();
                        control.container.find( '.background-wrapper > .background-attachment' ).hide();

                        if ( preview.length ) {
                            preview.removeClass().addClass( 'placeholder' ).html( 'No file selected' );
                        } if ( removeButton.length ) {
                            removeButton.hide();
                        }
                    } );
            }
        } );
    }
} )( wp.customize, wp, jQuery );
