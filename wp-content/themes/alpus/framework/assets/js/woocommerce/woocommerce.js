/**
 * WP Alpha Theme Framework
 * Alpha Woocommerce
 *
 * @package WP Alpha Framework
 * @since 1.2.0
 */

window.theme = window.theme || {};

( function( $ ) {
    theme.woocommerce = {
        init: function() {
            this.removerId = 0;
            // Functions for products
            this.initProductsAttributeAction();
            this.initProductsQuickview();
            this.initProductsCartAction();
            this.initProductsWishlistAction();
            this.initProductsHover();
            this.initAjaxAddToCart();
            this.initResetVariation();
            theme.call( this.initProducts.bind( this ), 500 );
        },
        /**
         * Initialize products
         * - rating tooltip
         * - product types
         * 
         * @since 1.0
         * @param {HTMLElement|jQuery|string} selector
         * @return {void}
         */
        initProducts: function( selector ) {
            this.ratingTooltip( selector );
            // theme.quantityInput(theme.$(selector, '.qty'));
            // theme.$(selector, 'input.qty').off('change', handleQTY).on('change', handleQTY);
        },
        /**
         * Init ajax add to cart for quickview.
         * 
         * 
         * @since 1.2.0
         */
        initAjaxAddToCart: function() {
            theme.$body.on( 'click', '.single_add_to_cart_button', function( e ) {

                var $btn = $( e.currentTarget );

                if ( $btn.hasClass( 'disabled' ) || $btn.hasClass( 'has_buy_now' ) ) {
                    return;
                }

                var $product = $btn.closest( '.product-single' );
                if ( !$product.length || $product.hasClass( 'product-type-external' ) || $product.hasClass( 'product-type-grouped' ) ||
                    !$product.hasClass( 'product-widget' ) && !$product.hasClass( 'product-quickview' ) ) {
                    return;
                }
                e.preventDefault();

                var $form = $btn.closest( 'form.cart' );
                if ( $form.hasClass( 'd-loading' ) ) {
                    return;
                }

                var variation_id = $form.find( 'input[name="variation_id"]' ).val(),
                    product_id = variation_id ? $form.find( 'input[name="product_id"]' ).val() : $btn.val(),
                    quantity = $form.find( 'input[name="quantity"]' ).val(),
                    $attributes = $form.find( 'select[data-attribute_name]' ),
                    data = {
                        product_id: variation_id ? variation_id : product_id,
                        quantity: quantity
                    };

                $attributes.each( function() {
                    var $this = $( this );
                    data[$this.attr( 'data-attribute_name' )] = $this.val();
                } );

                // Initialize ajax url
                var ajax_url = '';

                // Resolve issue. For the variable product that has any type, ajax add to cart does not work
                // in single product widget and quickview
                // 2021-06-20
                if ( $product.hasClass( 'product-widget' ) || $product.hasClass( 'product-quickview' ) ) {
                    ajax_url = alpha_vars.ajax_url;
                    data.action = 'alpha_ajax_add_to_cart';
                } else {
                    ajax_url = wc_add_to_cart_params.wc_ajax_url.toString().replace( '%%endpoint%%', 'add_to_cart' );
                }

                theme.doLoading( $btn, 'small' );
                $btn.removeClass( 'added' );

                // Trigger event.
                theme.$body.trigger( 'adding_to_cart', [$btn, data] );

                $.ajax( {
                    type: 'POST',
                    url: ajax_url,
                    data: data,
                    dataType: 'json',
                    success: function( response ) {
                        if ( !response ) {
                            return;
                        }
                        if ( response.error && response.product_url ) {
                            location = response.product_url;
                            return;
                        }

                        // Redirect to cart option
                        if ( wc_add_to_cart_params.cart_redirect_after_add === 'yes' ) {
                            location = wc_add_to_cart_params.cart_url;
                            return;
                        }

                        // trigger event
                        $( document.body ).trigger( 'added_to_cart', [response.fragments, response.cart_hash, $btn] );

                        // show minipopup box
                        var link = $form.attr( 'action' ),
                            image = $product.find( '.wp-post-image' ).attr( 'src' ),
                            title = $product.find( '.product_title' ).text(),
                            price = variation_id ? $form.find( '.woocommerce-variation-price .price' ).html() : $product.find( '.price' ).html(),
                            count = parseInt( $form.find( '.qty' ).val() ),
                            id = $product.attr( 'id' );

                        price || ( price = $product.find( '.price' ).html() );

                        var $popup_product = $( '.minipopup-area' ).find( "#" + id );

                        if ( id == $popup_product.attr( 'id' ) ) {
                            $popup_product.find( '.cart-count' ).html( parseInt( $popup_product.find( '.cart-count' ).html() ) + count );
                        } else {
                            theme.minipopup.open( {
                                content: '<div class="minipopup-box">\
                                    <div class="product product-list-sm" id="' + id + '">\
                                        <figure class="product-media"><a href="' + link + '"><img src="' + image + '"></img></a></figure>\
                                        <div class="product-details"><a class="product-title" href="' + link + '"><span class="cart-count">' + count + '</span> x ' + title + '</a>' + alpha_vars.texts.cart_suffix + '</div></div>\
                                        <div class="minipopup-footer">' + '<a href="' + alpha_vars.pages.cart + '" class="btn btn-sm btn-rounded">' + alpha_vars.texts.view_cart + '</a><a href="' + alpha_vars.pages.checkout + '" class="btn btn-sm btn-dark btn-rounded">' + alpha_vars.texts.view_checkout + '</a></div></div>'
                            } );
                        }
                    },
                    complete: function() {
                        theme.endLoading( $btn );
                    }
                } );
            } );
        },
        /**
         * Ajax add to cart for variation products
         * 
         * @since 1.0
         */
        initProductsAttributeAction: function() {
            theme.$body
                .on( 'click', '.product-variation-wrapper button', function( e ) {
                    var $this = $( this ),
                        $variation = $this.parent(),
                        $wrapper = $this.closest( '.product-variation-wrapper' ),
                        attr = 'attribute_' + String( $variation.data( 'attr' ) ),
                        variationData = $wrapper.data( 'product_variations' ),
                        attributes = $wrapper.data( 'product_attrs' ),
                        attrValue = $this.attr( 'name' ),
                        $price = $wrapper.closest( '.product-loop' ).find( '.price' ),
                        priceHtml = $wrapper.data( 'price' );

                    if ( $this.hasClass( 'disabled' ) ) {
                        return;
                    }
                    // if ( $this.hasClass( 'active' ) ) {
                    // 	$this.removeClass( 'active' )
                    // 		.parent().next().val( '' ).change();
                    // } else {
                    // 	$this.addClass( 'active' ).siblings().removeClass( 'active' );
                    // 	$this.parent().next().val( $this.attr( 'name' ) ).change();
                    // }

                    var suitableData = variationData,
                        matchedData = variationData;

                    // Get Attributes
                    if ( undefined == attributes ) {
                        attributes = [];
                        $wrapper.find( '.product-variations' ).each( function() {
                            attributes.push( 'attribute_' + String( $( this ).data( 'attr' ) ) );
                        } );
                        $wrapper.data( 'product_attrs', attributes );
                    }

                    // Save HTML
                    if ( undefined == priceHtml ) {
                        priceHtml = $price.html();
                        $wrapper.data( 'price', priceHtml );
                    }


                    // Update Matched Array
                    if ( attrValue == $wrapper.data( attr ) ) {
                        $wrapper.removeData( attr );
                        let tempArray = [];
                        variationData.forEach( function( item, index ) {
                            var flag = true;
                            attributes.forEach( function( attr_item ) {
                                if ( undefined != $wrapper.data( attr_item ) && $wrapper.data( attr_item ) != item['attributes'][attr_item] && "" != item['attributes'][attr_item] ) {
                                    flag = false;
                                }
                            } );
                            if ( flag ) {
                                tempArray.push( item );
                            }
                        } );

                        matchedData = tempArray;
                    } else {
                        $wrapper.data( attr, attrValue );
                        let tempArray = [];
                        variationData.forEach( function( item, index ) {
                            var flag = true;
                            attributes.forEach( function( attr_item ) {
                                if ( undefined != $wrapper.data( attr_item ) && $wrapper.data( attr_item ) != item['attributes'][attr_item] && "" != item['attributes'][attr_item] ) {
                                    flag = false;
                                }
                            } );
                            if ( flag ) {
                                tempArray.push( item );
                            }
                        } );

                        matchedData = tempArray;
                    }

                    var showPrice = true;
                    attributes.forEach( function( attr_item ) {
                        if ( attr != attr_item || ( attr_item == attr && undefined == $wrapper.data( attr ) ) ) {
                            let $variation = $wrapper.find( '.' + attr_item.slice( 10 ) + ' > *:not(.guide-link)' );

                            $variation.each( function() {
                                var $this = $( this );
                                if ( !$this.hasClass( 'select-box' ) ) {
                                    $this.addClass( 'disabled' );
                                } else {
                                    $this.find( 'option' ).css( 'display', 'none' );
                                }
                            } )

                            variationData.forEach( function( item ) {
                                let flag = true;
                                attributes.forEach( function( atr_item ) {
                                    if ( undefined != $wrapper.data( atr_item ) && attr_item != atr_item && item['attributes'][atr_item] != $wrapper.data( atr_item ) && "" != item['attributes'][atr_item] ) {
                                        flag = false;
                                    }
                                } );
                                if ( true == flag ) {
                                    if ( "" == item['attributes'][attr_item] ) {
                                        $variation.removeClass( 'disabled' );
                                        $variation.each( function() {
                                            var $this = $( this );
                                            if ( !$this.hasClass( 'select-box' ) ) {
                                                $this.removeClass( 'disabled' );
                                            } else {
                                                $this.find( 'option' ).css( 'display', '' );
                                            }
                                        } )
                                    } else {
                                        $variation.each( function() {
                                            var $this = $( this );
                                            if ( !$this.hasClass( 'select-box' ) ) {
                                                if ( $this.attr( 'name' ) == item['attributes'][attr_item] ) {
                                                    $this.removeClass( 'disabled' );
                                                }
                                            } else {
                                                $this.find( 'option' ).each( function() {
                                                    var $this = $( this );
                                                    if ( $this.attr( 'value' ) == item['attributes'][attr_item] || $this.attr( 'value' ) == '' ) {
                                                        $this.css( 'display', '' );
                                                    }
                                                } );
                                            }
                                        } );
                                    }
                                }
                            } );
                        }
                        if ( undefined == $wrapper.data( attr_item ) ) {
                            showPrice = false;
                        }
                    } );

                    if ( true == showPrice && 1 == matchedData.length ) {
                        $price.closest( '.product-loop' ).data( 'variation', matchedData[0]['variation_id'] );
                        $price.html( $( matchedData[0]['price_html'] ).html() );
                        $price.closest( '.product-loop' ).find( '.add_to_cart_button' )
                            .removeClass( 'product_type_variable' )
                            .addClass( 'product_type_simple' );
                    } else {
                        $price.html( priceHtml );
                        $price.closest( '.product-loop' ).removeData( 'variation' )
                            .find( '.add_to_cart_button' )
                            .removeClass( 'product_type_simple' )
                            .addClass( 'product_type_variable' );
                    }
                } )
                .on( 'change', '.product-variation-wrapper select', function( e ) {
                    var $this = $( this ),
                        $variation = $this.parent(),
                        $wrapper = $this.closest( '.product-variation-wrapper' ),
                        attr = $this.data( 'attribute_name' ),
                        variationData = $wrapper.data( 'product_variations' ),
                        attributes = $wrapper.data( 'product_attrs' ),
                        attrValue = $this.val(),
                        $price = $wrapper.closest( '.product-loop' ).find( '.price' ),
                        priceHtml = $wrapper.data( 'price' );


                    var suitableData = variationData,
                        matchedData = variationData;

                    // Get Attributes
                    if ( undefined == attributes ) {
                        attributes = [];
                        $wrapper.find( '.product-variations' ).each( function() {
                            attributes.push( 'attribute_' + String( $( this ).data( 'attr' ) ) );
                        } );
                        $wrapper.data( 'product_attrs', attributes );
                    }

                    // Save HTML
                    if ( undefined == priceHtml ) {
                        priceHtml = $price.html();
                        $wrapper.data( 'price', priceHtml );
                    }


                    // Update Matched Array
                    if ( "" == attrValue ) {
                        $wrapper.removeData( attr );
                        let tempArray = [];
                        variationData.forEach( function( item, index ) {
                            var flag = true;
                            attributes.forEach( function( attr_item ) {
                                if ( undefined != $wrapper.data( attr_item ) && $wrapper.data( attr_item ) != item['attributes'][attr_item] && "" != item['attributes'][attr_item] ) {
                                    flag = false;
                                }
                            } );
                            if ( flag ) {
                                tempArray.push( item );
                            }
                        } );

                        matchedData = tempArray;
                    } else {
                        $wrapper.data( attr, attrValue );
                        let tempArray = [];
                        variationData.forEach( function( item, index ) {
                            var flag = true;
                            attributes.forEach( function( attr_item ) {
                                if ( undefined != $wrapper.data( attr_item ) && $wrapper.data( attr_item ) != item['attributes'][attr_item] && "" != item['attributes'][attr_item] ) {
                                    flag = false;
                                }
                            } );
                            if ( flag ) {
                                tempArray.push( item );
                            }
                        } );

                        matchedData = tempArray;
                    }

                    var showPrice = true;
                    attributes.forEach( function( attr_item ) {
                        if ( attr != attr_item || ( attr_item == attr && undefined == $wrapper.data( attr ) ) ) {
                            let $variation = $wrapper.find( '.' + attr_item.slice( 10 ) + ' > *' );

                            $variation.each( function() {
                                var $this = $( this );
                                if ( !$this.hasClass( 'select-box' ) ) {
                                    $this.addClass( 'disabled' );
                                } else {
                                    $this.find( 'option' ).css( 'display', 'none' );
                                }
                            } );

                            variationData.forEach( function( item ) {
                                let flag = true;
                                attributes.forEach( function( atr_item ) {
                                    if ( undefined != $wrapper.data( atr_item ) && attr_item != atr_item && item['attributes'][atr_item] != $wrapper.data( atr_item ) && "" != item['attributes'][atr_item] ) {
                                        flag = false;
                                    }
                                } );
                                if ( true == flag ) {
                                    if ( "" == item['attributes'][attr_item] ) {
                                        $variation.removeClass( 'disabled' );
                                        $variation.each( function() {
                                            var $this = $( this );
                                            if ( !$this.hasClass( 'select-box' ) ) {
                                                $this.removeClass( 'disabled' );
                                            } else {
                                                $this.find( 'option' ).css( 'display', '' );
                                            }
                                        } );
                                    } else {
                                        $variation.each( function() {
                                            var $this = $( this );
                                            if ( !$this.hasClass( 'select-box' ) ) {
                                                if ( $this.attr( 'name' ) == item['attributes'][attr_item] ) {
                                                    $this.removeClass( 'disabled' );
                                                }
                                            } else {
                                                $this.find( 'option' ).each( function() {
                                                    var $this = $( this );
                                                    if ( $this.attr( 'value' ) == item['attributes'][attr_item] || $this.attr( 'value' ) == '' ) {
                                                        $this.css( 'display', '' );
                                                    }
                                                } );
                                            }
                                        } );
                                    }
                                }
                            } );
                        }
                        if ( undefined == $wrapper.data( attr_item ) ) {
                            showPrice = false;
                        }
                    } );

                    if ( true == showPrice && 1 == matchedData.length ) {
                        $price.closest( '.product-loop' ).data( 'variation', matchedData[0]['variation_id'] );
                        $price.html( $( matchedData[0]['price_html'] ).html() );
                        $price.closest( '.product-loop' ).find( '.add_to_cart_button' )
                            .removeClass( 'product_type_variable' )
                            .addClass( 'product_type_simple' );
                    } else {
                        $price.html( priceHtml );
                        $price.closest( '.product-loop' ).removeData( 'variation' )
                            .find( '.add_to_cart_button' )
                            .removeClass( 'product_type_simple' )
                            .addClass( 'product_type_variable' );
                    }
                } )
                .on( 'click', '.product-loop.product-type-variable .add_to_cart_button', function( e ) {
                    var $this = $( this ),
                        $variations = $this.closest( '.product' ).find( '.product-variation-wrapper' ),
                        attributes = $variations.data( 'product_attrs' ),
                        $product = $this.closest( '.product-loop' );

                    if ( undefined != $product.data( 'variation' ) ) {
                        let data = {
                            action: "alpha_add_to_cart",
                            product_id: $product.data( 'variation' ),
                            quantity: 1
                        };
                        attributes.forEach( function( item ) {
                            data[item] = $variations.data( item );
                        } );
                        $.ajax(
                            {
                                type: 'POST',
                                dataType: 'json',
                                url: alpha_vars.ajax_url,
                                data: data,
                                success: function( response ) {
                                    $( document.body ).trigger( 'added_to_cart', [response.fragments, response.cart_hash, $this] );
                                }
                            }
                        );
                        e.preventDefault();
                    }
                } )
                .on( 'found_variation', '.variations_form', function( e, variation ) {
                    var $product = $( e.currentTarget ).closest( '.product' );
                    // Display sale countdown of matched variation.
                    var $counter = $product.find( '.countdown-variations' );
                    if ( $counter.length ) {
                        if ( variation && variation.is_purchasable && variation.alpha_date_on_sale_to ) {
                            var $countdown = $counter.find( '.countdown' );
                            if ( $countdown.data( 'until' ) != variation.alpha_date_on_sale_to && typeof theme.countdown == 'function' ) {
                                theme.countdown( $countdown, { until: new Date( variation.alpha_date_on_sale_to ) } );
                                $countdown.data( 'until', variation.alpha_date_on_sale_to );
                            }
                            $counter.slideDown();
                        } else {
                            $counter.slideUp();
                        }
                    }

                    // Refresh price in sticky add to cart
                    if ( $( '.product-sticky-content .price' ).length && typeof variation.price_html != 'undefined' ) {
                        $( '.product-sticky-content .price' ).html( variation.price_html );
                    }
                } )
                .on( 'reset_image', '.variations_form', function( e ) {
                    var $product = $( e.currentTarget ).closest( '.product' );
                    $product.find( '.countdown-variations' ).slideUp();

                    // Refresh price in sticky add to cart
                    if ( $( '.product-sticky-content .price' ).length ) {
                        $( '.product-sticky-content .price' ).html( $product.find( 'p.price:not(.price-sticky)' ).html() );
                    }
                } );
        },
        /**
         * Initialize products quickview action
         * 
         * @since 1.0
         */
        initProductsQuickview: function() {
            theme.$body.on( 'click', '.btn-quickview', function( e ) {
                e.preventDefault();

                var $this = $( this );
                var ajax_data = {
                    action: 'alpha_quickview',
                    product_id: $this.data( 'product' )
                };
                var quickviewType = alpha_vars.quickview_type || 'loading';
                if ( quickviewType == 'zoom' && window.innerWidth < 768 ) {
                    quickviewType = 'loading';
                }

                if ( $this.closest( '.shop_table' ).length ) {
                    theme.doLoading( $this, 'small' );
                }

                function finishQuickView() {
                    theme.createProductSingle( '.mfp-product .product-single' );
                    if ( $this.closest( '.shop_table' ).length ) {
                        theme.endLoading( $this );
                    }
                    theme.woocommerce.ratingTooltip( '.mfp-product .product-single' );

                    theme.$body.trigger( 'alpha_ajax_yith_wcwl_require' );
                }

                function openQuickview( quickviewType ) {
                    theme.popup( {
                        type: 'ajax',
                        mainClass: 'mfp-product mfp-fade' + ( quickviewType == 'offcanvas' ? ' mfp-offcanvas' : '' ),
                        items: {
                            src: alpha_vars.ajax_url
                        },
                        ajax: {
                            settings: {
                                method: 'POST',
                                data: ajax_data
                            },
                            cursor: 'mfp-ajax-cur', // CSS class that will be added to body during the loading (adds "progress" cursor)
                            tError: '<div class="alert alert-warning alert-round alert-inline">' + alpha_vars.texts.popup_error + '<button type="button" class="btn btn-link btn-close"><i class="close-icon"></i></button></div>'
                        },
                        preloader: false,
                        callbacks: {
                            afterChange: function() {
                                var skeletonTemplate;
                                if ( alpha_vars.skeleton_screen ) {
                                    var extraClass = alpha_vars.quickview_thumbs == 'horizontal' ? '' : ' pg-vertical';
                                    if ( quickviewType == 'offcanvas' ) {
                                        skeletonTemplate = '<div class="product skeleton-body' + extraClass + '"><div class="skel-pro-gallery"></div><div class="skel-pro-summary" style="margin-top: 20px"></div></div>';;
                                    } else {
                                        skeletonTemplate = '<div class="product skeleton-body row"><div class="' + alpha_vars.quickview_wrap_1 + extraClass + '"><div class="skel-pro-gallery"></div></div><div class="' + alpha_vars.quickview_wrap_2 + '"><div class="skel-pro-summary"></div></div></div>';
                                    }
                                } else {
                                    skeletonTemplate = '<div class="product product-single"><div class="d-loading"><i></i></div></div>';
                                }
                                this.container.html( '<div class="mfp-content"></div><div class="mfp-preloader">' + skeletonTemplate + '</div>' );
                                this.contentContainer = this.container.children( '.mfp-content' );
                                this.preloader = false;
                            },
                            beforeClose: function() {
                                this.container.empty();
                            },
                            ajaxContentAdded: function() {
                                var self = this;
                                this.wrap.imagesLoaded( function() {
                                    finishQuickView();
                                } );

                                // Move close button out of product because of product's overflow.
                                this.wrap.find( '.mfp-close' ).appendTo( this.content );

                                // Remove preloader
                                setTimeout( function() {
                                    self.contentContainer.next( '.mfp-preloader' ).remove();
                                }, 300 );
                            }
                        }
                    } );
                }

                // 1. Quickview / Preload skeleton screen for "loading", "offcanvas".

                if ( alpha_vars.skeleton_screen && quickviewType != 'zoom' ) {
                    openQuickview( quickviewType );

                } else if ( quickviewType == 'zoom' ) { // 2. Quickview / Zoomed Product

                    var zoomLoadedData = '';
                    function zoomInit() {
                        var instance = $.magnificPopup.instance;
                        if ( instance.isOpen && instance.content && instance.wrap.hasClass( 'zoom-start2' ) && !instance.wrap.hasClass( 'zoom-finish' ) && zoomLoadedData ) {

                            var i = 1;
                            var timer = theme.requestInterval( function() {
                                instance.wrap.addClass( 'zoom-start3' );
                                if ( instance.content ) {

                                    var $data = $( zoomLoadedData );
                                    var $gallery = $data.find( '.woocommerce-product-gallery' );
                                    var $summary = $data.find( '.summary' );
                                    var $product = instance.content.find( '.product-single' );
                                    $product.children( 'div:first-child' ).html( $gallery );
                                    $product.find( '.entry-summary.summary' ).remove();
                                    $product.attr( 'id', $data.attr( 'id' ) );
                                    $product.attr( 'class', $data.attr( 'class' ) );

                                    instance.content.css( 'clip-path', i < 30 ? 'inset(0 calc(' + ( ( 31 - i ) * 50 / 30 ) + '% - 20px) 0 0)' : 'none' );
                                    if ( i >= 30 ) {
                                        theme.deleteTimeout( timer );
                                        instance.wrap.addClass( 'zoom-finish' );
                                        $product.children( 'div:last-child' ).append( $summary );

                                        $( '.mfp-animated-image' ).remove();

                                        theme.requestTimeout( function() {
                                            instance.wrap.addClass( 'zoom-loaded mfp-anim-finish' );
                                            theme.endLoading( $product.children( 'div:last-child' ) );
                                            finishQuickView();
                                        }, 50 );
                                    }
                                    ++i;
                                } else {
                                    theme.deleteTimeout( timer );
                                }
                            }, 16 );
                        }
                    }

                    var $image;
                    if ( $this.hasClass( 'alpha-tb-quickview' ) ) { // post type builder
                        $image = $this.parent().find( '.alpha-tb-featured-image img:first-child' );
                    } else if ( $this.parent( '.hotspot-product' ).length ) {
                        $image = $this.parent().find( '.product-media img' );
                    } else if ( $this.closest( '.shop_table' ).length ) {
                        $image = $this.closest( 'tr' ).find( '.product-thumbnail img' );
                    } else {
                        $image = $this.closest( '.product' ).find( '.product-media img:first-child' );
                    }
                    if ( !$image.length ) {
                        openQuickview( 'loading' );
                        return;
                    }
                    var imageSrc = $this.data( 'mfp-src' );

                    $( '<img src="' + imageSrc + '">' ).imagesLoaded( function() {
                        $this.data( 'magnificPoup' ) ||
                            $this
                                .magnificPopup( {
                                    type: 'image',
                                    mainClass: 'mfp-product mfp-zoom mfp-anim',
                                    preloader: false,
                                    item: {
                                        src: imageSrc
                                    },
                                    closeOnBgClick: false,
                                    zoom: {
                                        enabled: true,
                                        duration: 550,
                                        easing: 'cubic-bezier(.55,0,.1,1)',
                                        opener: function() {
                                            return $image;
                                        }
                                    },
                                    callbacks: {
                                        beforeOpen: theme.defaults.popup.callbacks.beforeOpen,
                                        open: function() {
                                            var wrapper = '<div class="product-single product-quickview product row product-quickview-loading"><div class="' + alpha_vars.quickview_wrap_1 + '"></div><div class="' + alpha_vars.quickview_wrap_2 + '"></div></div>';

                                            if ( alpha_vars.quickview_thumbs != 'horizontal' && window.innerWidth >= 992 ) {
                                                this.content.addClass( 'vertical' );
                                            }

                                            this.content.find( 'figcaption' ).remove();
                                            if ( this.items[0] ) {
                                                var $wrap = this.items[0].img.wrap( wrapper );
                                                if ( !this.items[0].el.closest( '.product' ).find( '.woocommerce-placeholder' ).length ) {
                                                    $wrap.after( '<div class="thumbs"><img src="' + this.items[0].img.attr( "src" ) + '" /><img src="' + this.items[0].img.attr( "src" ) + '" /><img src="' + this.items[0].img.attr( "src" ) + '" /><img src="' + this.items[0].img.attr( "src" ) + '" /></div>' );
                                                }
                                            }

                                            var self = this;
                                            setTimeout( function() {
                                                self.bgOverlay.removeClass( 'mfp-ready' );
                                            }, 16 );

                                            setTimeout( function() {
                                                self.wrap.addClass( 'zoom-start' );
                                                theme.requestFrame( function() {
                                                    var $img = self.content.find( '.thumbs>img:first-child' );
                                                    var w = $img.length ? $img.width() : 0;
                                                    var h = $img.length ? $img.height() : 0;
                                                    var i = 0;
                                                    self.bgOverlay.addClass( 'mfp-ready' );
                                                    var timer = theme.requestInterval( function() {
                                                        if ( self.content ) {
                                                            self.content.css(
                                                                'clip-path',
                                                                alpha_vars.quickview_thumbs != 'horizontal' && window.innerWidth >= 992 ?
                                                                    'inset(' + ( 30 - i ) + 'px calc(' + alpha_vars.quickview_percent + ' + ' + ( 10 - i ) + 'px) ' + ( 30 - i ) + 'px ' + ( ( 30 - i ) * ( 30 + w ) / 30 ) + 'px)' :
                                                                    'inset(' + ( 30 - i ) + 'px calc(' + alpha_vars.quickview_percent + ' + ' + ( 10 - i ) + 'px) ' + ( ( 30 - i ) * ( 30 + h ) / 30 ) + 'px ' + ( 30 - i ) + 'px)'
                                                            );


                                                            if ( i >= 30 ) {
                                                                theme.deleteTimeout( timer );
                                                                self.wrap.addClass( 'zoom-start2' );
                                                                if ( !zoomLoadedData ) {
                                                                    theme.doLoading( self.content.find( '.product > div:first-child' ) );
                                                                }
                                                                zoomInit();
                                                            } else {
                                                                i += 3;
                                                            }
                                                        } else {
                                                            theme.deleteTimeout( timer );
                                                        }
                                                    }, 16 );
                                                } );
                                            }, 560 );
                                        },
                                        beforeClose: function() {
                                            $this.removeData( 'magnificPopup' );
                                            $this.off( 'click.magnificPopup' );
                                            $( '.mfp-animated-image' ).remove();
                                        },
                                        close: theme.defaults.popup.callbacks.close
                                    }
                                } );
                        $this.magnificPopup( 'open' );
                    } );

                    // Get images loaded ajax content
                    $.post( alpha_vars.ajax_url, ajax_data )
                        .done( function( data ) {
                            $( data ).imagesLoaded( function() {
                                zoomLoadedData = data;
                                zoomInit();
                            } );
                        } );

                } else { // 3. Quickview / Loading Icon Inner Product

                    if ( $this.hasClass( 'alpha-tb-quickview' ) ) {
                        theme.doLoading( $this.closest( '.product' ).find( '.alpha-tb-featured-image' ) );
                    } else {
                        theme.doLoading( $this.closest( '.product' ).find( '.product-media' ) );
                    }

                    // Get images loaded ajax content
                    $.post( alpha_vars.ajax_url, ajax_data )
                        .done( function( data ) {
                            $( data ).imagesLoaded( function() {
                                theme.popup( {
                                    type: 'inline',
                                    mainClass: 'mfp-product mfp-fade ' + ( quickviewType == 'offcanvas' ? 'mfp-offcanvas' : 'mfp-anim' ),
                                    items: {
                                        src: data
                                    },
                                    callbacks: {
                                        open: function() {
                                            var self = this;
                                            function finishLoad() {
                                                self.wrap.addClass( 'mfp-anim-finish' );
                                            }

                                            if ( quickviewType == 'offcanvas' ) {
                                                setTimeout( finishLoad, 316 );
                                            } else {
                                                theme.requestFrame( finishLoad );
                                            }

                                            finishQuickView();
                                        }
                                    }
                                } )

                                if ( $this.hasClass( 'alpha-tb-quickview' ) ) {
                                    theme.endLoading( $this.closest( '.product' ).find( '.alpha-tb-featured-image' ) );
                                } else {
                                    theme.endLoading( $this.closest( '.product' ).find( '.product-media' ) );
                                }
                            } )
                        } );
                }
            } );
        },
        /**
         * Initialize products cart action
         * 
         * @since 1.0
         */
        initProductsCartAction: function() {
            theme.$body
                // Before product is added to cart
                .on( 'click', '.add_to_cart_button:not(.product_type_variable)', function( e ) {
                    $( '.minicart-icon' ).addClass( 'adding' );
                    theme.doLoading( e.currentTarget, 'small' );
                } )

                // After product is added to cart
                .on( 'added_to_cart', function( e, fragments, cart_hash, $thisbutton ) {

                    var $product = $thisbutton.closest( '.product' );
                    if ( $thisbutton.closest( '.minipopup-area' ).length ) {
                        $product = theme.$body.find( '.product.post-' + $thisbutton.attr( 'data-product_id' ) );
                    }

                    // remove newly added "view cart" button.
                    if ( typeof alpha_elementor != 'undefined' ) {
                        // For elementor editor preview
                        setTimeout( function() {
                            $thisbutton.next( '.added_to_cart' ).remove();
                        } );
                    } else {
                        $thisbutton.next( '.added_to_cart' ).remove();
                    }

                    // if not product single, then open minipopup
                    if ( !$product.hasClass( 'product-single' ) ) {
                        var link, image, title, price, id;

                        if ( $product.length ) { // inside product element
                            link = $product.find( '.product-media .woocommerce-loop-product__link' ).attr( 'href' );
                            image = $product.find( '.product-media img:first-child, .alpha-tb-featured-image img:first-child' ).attr( 'src' );
                            title = $product.find( '.woocommerce-loop-product__title a' ).text();
                            price = $product.find( '.price' ).html();
                            id = $thisbutton.data( 'product_id' );
                            var $popup_product = $( '.minipopup-area' ).find( "#product-" + id );
                        } else {
                            $product = $thisbutton.closest( '.compare-basic-info' );
                            link = $product.find( '.product-title' ).attr( 'href' );
                            image = $product.find( '.product-media img' ).attr( 'src' );
                            title = $product.closest( '.alpha-compare-table' ).find( '.compare-title .compare-value' ).eq( $thisbutton.closest( '.compare-value' ).index() - 1 ).find( '.product-title' ).html();
                            if ( title == undefined ) {
                                title = $product.children( '.product-title' ).html();
                            }
                            price = $product.closest( '.alpha-compare-table' ).find( '.compare-price .compare-value' ).eq( $thisbutton.closest( '.compare-value' ).index() - 1 ).html();
                        }

                        if ( $popup_product && id == $popup_product.attr( 'data-product-id' ) ) {
                            $popup_product.find( '.cart-count' ).html( parseInt( $popup_product.find( '.cart-count' ).html() ) + 1 );
                        } else {
                            if ( $product.hasClass( 'alpha-tb-item' ) ) { // post type builder
                                title = $product.data( 'title' );
                                link = $product.data( 'link' );
                            }
                            theme.minipopup.open( {
                                content: '<div class="minipopup-box minipopup-cart">\
            <div class="product product-list-sm" data-product-id=' + id + ' id="product-' + id + '">\
                <figure class="product-media"><a href="' + link + '"><img src="' + image + '"></img></a></figure>\
                <div class="product-details"><a class="product-title" href="' + link + '"><span class="cart-count">' + $thisbutton[0].dataset.quantity + '</span> x ' + title + '</a>' + alpha_vars.texts.cart_suffix + '</div></div>\
                <div class="minipopup-footer">' + '<a href="' + alpha_vars.pages.cart + '" class="btn btn-rounded">' + alpha_vars.texts.view_cart + '</a><a href="' + alpha_vars.pages.checkout + '" class="btn btn-dark btn-rounded">' + alpha_vars.texts.view_checkout + '</a></div></div>'
                            } );
                        }
                    }

                    theme.quantityInput( '.qty' );
                    $( '.minicart-icon' ).removeClass( 'adding' );

                } )
                .on( 'added_to_cart ajax_request_not_sent.adding_to_cart', function( e, f, c, $thisbutton ) {
                    if ( typeof $thisbutton !== 'undefined' ) {
                        theme.endLoading( $thisbutton );
                    }
                } )
                .on( 'wc_fragments_refreshed', function( e, f ) {
                    theme.quantityInput( '.qty' );

                    setTimeout( function() {
                        $( '.sticky-sidebar' ).trigger( 'recalc.pin' );
                    }, 400 );
                } )

                // Refresh cart table when cart item is removed
                .off( 'click', '.widget_shopping_cart .remove' )
                .on( 'click', '.widget_shopping_cart .remove', function( e ) {
                    e.preventDefault();
                    var $this = $( this );
                    var cart_id = $this.data( "cart_item_key" );

                    $.ajax(
                        {
                            type: 'POST',
                            dataType: 'json',
                            url: alpha_vars.ajax_url,
                            data: {
                                action: "alpha_cart_item_remove",
                                nonce: alpha_vars.nonce,
                                cart_id: cart_id
                            },
                            success: function( response ) {
                                var this_page = location.toString(),
                                    item_count = $( response.fragments['div.widget_shopping_cart_content'] ).find( '.mini_cart_item' ).length;

                                this_page = this_page.replace( 'add-to-cart', 'added-to-cart' );
                                $( document.body ).trigger( 'wc_fragment_refresh' );
                                
                                if ( $('.mini-basket-box.cart-dropdown').length ) {
                                    var $minilist = $('.mini-basket-box.cart-dropdown'),
                                        $dropdown = $minilist.find('.dropdown-box');
                                    if ( $dropdown.length ) {
                                        if ( $minilist.find('.mini-basket-empty').length ) {
                                            if ( !$dropdown.hasClass('empty') ) {
                                                $dropdown.addClass('empty');
                                            }
                                        } else {
                                            if ( $dropdown.hasClass('empty') ) {
                                                $dropdown.removeClass('empty');
                                            }
                                        }
                                    }
                                }                              

                                // Block widgets and fragments
                                if ( item_count == 0 && ( $( 'body' ).hasClass( 'woocommerce-cart' ) || $( 'body' ).hasClass( 'woocommerce-checkout' ) ) ) {
                                    $( '.page-content' ).block();
                                } else {
                                    $( '.shop_table.cart, .shop_table.review-order, .updating, .cart_totals' ).block();
                                }

                                // Unblock
                                $( '.widget_shopping_cart, .updating' ).stop( true ).unblock();

                                // Cart page elements
                                if ( item_count == 0 && ( $( 'body' ).hasClass( 'woocommerce-cart' ) || $( 'body' ).hasClass( 'woocommerce-checkout' ) ) ) {
                                    $( '.page-content' ).load( this_page + ' .page-content:eq(0) > *', function() {
                                        $( '.page-content' ).unblock();
                                    } );
                                } else {
                                    $( '.shop_table.cart' ).load( this_page + ' .shop_table.cart:eq(0) > *', function() {
                                        $( '.shop_table.cart' ).unblock();
                                        theme.quantityInput( '.shop_table .qty' );
                                    } );

                                    $( '.cart_totals' ).load( this_page + ' .cart_totals:eq(0) > *', function() {
                                        $( '.cart_totals' ).unblock();
                                    } );

                                    // Checkout page elements
                                    $( '.shop_table.review-order' ).load( this_page + ' .shop_table.review-order:eq(0) > *', function() {
                                        $( '.shop_table.review-order' ).unblock();
                                    } );
                                }
                            }
                        }
                    );
                    return false;
                } )
                // Removing cart item from minicart
                .on( 'click', '.remove_from_cart_button', function( e ) {
                    theme.doLoading( $( this ).closest( '.mini_cart_item' ), 'small' );
                } );
        },
        /**
         * Initialize products wishlist action
         * 
         * @since 1.0
         */
        initProductsWishlistAction: function() {
            function updateMiniWishList() {
                var $minilist = $( '.mini-basket-box .widget_wishlist_content' );

                if ( !$minilist.length ) {
                    return;
                }

                if ( !$minilist.find( '.d-loading' ).length ) {
                    theme.doLoading( $minilist, 'small' );
                }

                $.ajax( {
                    url: alpha_vars.ajax_url,
                    data: {
                        action: 'alpha_update_mini_wishlist'
                    },
                    type: 'post',
                    success: function( data ) {
                        if ( $minilist.closest( '.mini-basket-box' ).find( '.wish-count' ).length ) {
                            $minilist.closest( '.mini-basket-box' ).find( '.wish-count' ).text( $( data ).find( '.wish-count' ).text() );
                        }
                        $minilist.html( $( data ).find( '.widget_wishlist_content' ).html() );
                        
                        var $dropdown = $minilist.closest('.dropdown-box');
                        if ( $dropdown.length ) {
                            if ( $minilist.find('.mini-basket-empty').length ) {
                                if ( !$dropdown.hasClass('empty') ) {
                                    $dropdown.addClass('empty');
                                }
                            } else {
                                if ( $dropdown.hasClass('empty') ) {
                                    $dropdown.removeClass('empty');
                                }
                            }
                        }
                    }
                } );
            };

            theme.$body
                // Add item to wishlist
                .on( 'click', '.add_to_wishlist, .yith-wcwl-add-button .delete_item', function( e ) {
                    theme.doLoading( $( e.currentTarget ).closest( '.yith-wcwl-add-to-wishlist' ), 'small' );
                } )
                .on( 'added_to_wishlist', function() {
                    $( '.wish-count' ).each(
                        function() {
                            $( this ).html( parseInt( $( this ).html() ) + 1 );
                        }
                    );
                    updateMiniWishList();
                } )
                .on( 'removed_from_wishlist', function() {
                    $( '.wish-count' ).each(
                        function() {
                            $( this ).html( parseInt( $( this ).html() ) - 1 );
                        }
                    );
                    updateMiniWishList();
                } )
                .on( 'added_to_cart', function( e, fragments, cart_hash, $button ) {
                    if ( $button.closest( '#yith-wcwl-form' ).length ) {
                        $( '.wish-count' ).each(
                            function() {
                                $( this ).html( parseInt( $( this ).html() ) - 1 );
                            }
                        )
                    };
                    
                    if ( $('.mini-basket-box.cart-dropdown').length ) {
                        var $minilist = $('.mini-basket-box.cart-dropdown'),
                            $dropdown = $minilist.find('.dropdown-box');
                        if ( $dropdown.length ) {
                            if ( $minilist.find('.mini-basket-empty').length ) {
                                if ( !$dropdown.hasClass('empty') ) {
                                    $dropdown.addClass('empty');
                                }
                            } else {
                                if ( $dropdown.hasClass('empty') ) {
                                    $dropdown.removeClass('empty');
                                }
                            }
                        }
                    }
                    updateMiniWishList();
                } )
                .on( 'alpha_ajax_yith_wcwl_require', function() {
                    // Ajax load at the first by Yith Wishlist Plugin
                    if ( ( 'undefined' !== typeof yith_wcwl_l10n ) && yith_wcwl_l10n.enable_ajax_loading ) {
                        if ( $( '.wishlist-fragment' ).length ) {
                            var options = {},
                                $product = $( '.wishlist-fragment' ),
                                id = $product.attr( 'class' ).split( ' ' ).filter( ( val ) => {
                                    return val.length && val !== 'exists';
                                } ).join( yith_wcwl_l10n.fragments_index_glue );
                            options[id] = $product.data( 'fragment-options' );

                            if ( !options ) {
                                return;
                            }

                            var data = {
                                action: yith_wcwl_l10n.actions.load_fragments,
                                context: 'frontend',
                                fragments: options
                            };

                            if ( typeof yith_wcwl_l10n.nonce != 'undefined' ) {
                                data.nonce = yith_wcwl_l10n.nonce.load_fragments_nonce;
                            }

                            $.ajax( {
                                data: data,
                                method: 'post',
                                success: function( data ) {
                                    if ( typeof data.fragments !== 'undefined' ) {
                                        $.each( data.fragments, function( i, v ) {
                                            var itemSelector = '.' + i.split( yith_wcwl_l10n.fragments_index_glue ).filter( ( val ) => { return val.length && val !== 'exists' && val !== 'with-count'; } ).join( '.' ),
                                                toReplace = $( itemSelector );

                                            // find replace tempalte
                                            var replaceWith = $( v ).filter( itemSelector );

                                            if ( !replaceWith.length ) {
                                                replaceWith = $( v ).find( itemSelector );
                                            }

                                            if ( toReplace.length && replaceWith.length ) {
                                                toReplace.replaceWith( replaceWith );
                                            }
                                        } );
                                    }
                                },
                                url: yith_wcwl_l10n.ajax_url
                            } );
                        }
                    }
                } )
                .on( 'click', '.wishlist-dropdown .wishlist-item .remove_from_wishlist', function( e ) {
                    e.preventDefault();

                    var id = $( this ).attr( 'data-product_id' ),
                        $table = $( '.wishlist_table #yith-wcwl-row-' + id + ' .remove_from_wishlist' );

                    theme.doLoading( $( this ).closest( '.wishlist-item' ), 'small' );
                    
                    if ( $table.length ) {
                        $table.trigger( 'click' );
                    } else {
                        $.ajax( {
                            url: yith_wcwl_l10n.ajax_url,
                            data: {
                                action: yith_wcwl_l10n.actions.remove_from_wishlist_action,
                                nonce: yith_wcwl_l10n.nonce.remove_from_wishlist_nonce,
                                remove_from_wishlist: id,
                                from: 'theme'
                            },
                            method: 'post',
                            success: function( data ) {
                                theme.$body.trigger( 'removed_from_wishlist' );
                            }
                        } );
                    }
                } )

            if ( alpha_vars.skeleton_screen ) {
                theme.$body.trigger( 'yith_wcwl_reload_fragments' );
            }
        },
        /**
         * Initialize products hover in double touch
         * 
         * @since 1.0
         */
        initProductsHover: function() {
            if ( !$( 'html' ).hasClass( 'touchable' ) || !alpha_vars.prod_open_click_mob ) {
                return;
            }

            var isTouchFired = false;

            function _clickProduct( e ) {
                if ( isTouchFired && !$( this ).hasClass( 'hover-active' ) ) {
                    e.preventDefault();
                    $( '.hover-active' ).removeClass( 'hover-active' );
                    $( this ).addClass( 'hover-active' );
                }
            }

            function _clickGlobal( e ) {
                isTouchFired = e.type == 'touchstart';
                $( e.target ).closest( '.hover-active' ).length || $( '.hover-active' ).removeClass( 'hover-active' );
            }

            theme.$body.on( 'click', '.product-wrap .product', _clickProduct );
            $( document ).on( 'click', _clickGlobal );
            document.addEventListener( 'touchstart', _clickGlobal, { passive: true } );
        },
        /**
         * Initialize rating tooltips
         * Find all .star-rating from selector, and initialize tooltip.
         * 
         * @since 1.0
         * @param {HTMLElement|jQuery|string} selector
         * @return {void}
         */
        ratingTooltip: function( selector ) {
            var ratingHandler = function() {
                var res = this.firstElementChild.getBoundingClientRect().width / this.getBoundingClientRect().width * 5;
                this.lastElementChild.innerText = res ? res.toFixed( 2 ) : res;
                this.classList.add( 'rating-loaded' );
            }

            theme.$( selector, '.star-rating' ).each( function() {
                if ( this.lastElementChild && !this.lastElementChild.classList.contains( 'tooltiptext' ) ) {
                    var span = document.createElement( 'span' );
                    span.classList.add( 'tooltiptext' );
                    span.classList.add( 'tooltip-top' );

                    this.appendChild( span );
                    this.addEventListener( 'mouseover', ratingHandler );
                    this.addEventListener( 'touchstart', ratingHandler, { passive: true } );
                }
            } );
        },
        /**
         * Remove alerts automatically
         *
         * @since 1.0
         * @return {void}
         */
        initAlertAction: function() {
            this.removerId && clearTimeout( this.removerId );
            this.removerId = setTimeout( function() {
                $( '.woocommerce-page .main-content .alert:not(.woocommerce-info) .btn-close' ).not( ':hidden' ).trigger( 'click' );
            }, 10000 );
        },
        /**
         * Initialize reset variation link
         *
         * @since 1.0
         * @return {void}
         */
        initResetVariation: function() {
            theme.$body.on( 'check_variations', '.variations_form', function() {
				var $reset = $( theme.byClass( 'reset_variations', this ) );
				$reset.css( 'visibility' ) == 'hidden' ? $reset.hide() : $reset.show();
            } );
        }
    }
    /**
     * Create product gallery object
     * 
     * @class ProductGallery
     * @since 1.0
     * @param {string|jQuery} selector
     * @return {void}
     */
    theme.createProductGallery = ( function() {
        function ProductGallery( $el ) {
            return this.init( $el );
        }

        var firstScrollTopOnSticky = true;

        function setupThumbs( self ) {
            self.$thumbs = self.$wc_gallery.find( '.product-thumbs' );
            self.$thumbsDots = self.$thumbs.children();
            self.isVertical = self.$thumbs.parent().parent().hasClass( 'pg-vertical' );
            self.$thumbsWrap = self.$thumbs.parent();

            // # setup thumbs slider
            theme.slider( self.$thumbs, {}, true );

            // # refresh thumbs
            self.isVertical && window.addEventListener( 'resize', function() {
                theme.requestTimeout( function() {
                    self.$thumbs.data( 'slider' ).update();
                }, 100 )
            }, { passive: true } );
        }

        // Public Properties

        ProductGallery.prototype.init = function( $wc_gallery ) {
            var self = this;

            // If woocommmerce product gallery is undefined, create it
            typeof $wc_gallery.data( 'product_gallery' ) == 'undefined' && $wc_gallery.wc_product_gallery();
            this.$wc_gallery = $wc_gallery;
            this.wc_gallery = $wc_gallery.data( 'product_gallery' );

            // Remove woocommerce zoom triggers
            $( '.woocommerce-product-gallery__trigger' ).remove();

            // Add full image trigger, and init zoom
            this.$slider = $wc_gallery.find( '.product-single-carousel' );

            if ( this.$slider.length ) {
                this.initThumbs(); // init thumbs together for single slider
            } else {
                this.$slider = this.$wc_gallery.find( '.product-gallery-carousel' );
                if ( this.$slider.length ) {	// gallery slider
                    this.$slider.on( 'initialized.slider', this.initZoom.bind( this ) );
                } else { // other types
                    this.initZoom();
                }
            }

            // Prevent going to image link
            $wc_gallery
                .off( 'click', '.woocommerce-product-gallery__image a' )
                .on( 'click', theme.preventDefault );

            if ( !$wc_gallery.closest( '.product-quickview' ).length && !$wc_gallery.closest( '.product-widget' ).length ) {
                // If only single product page
                if ( !document.body.classList.contains( 'single-' + alpha_vars.theme + '_template' ) )
                    $wc_gallery.on( 'click', '.woocommerce-product-gallery__image a', this.openImageFull.bind( this ) );

                // Initialize sticky thumbs type.
                if ( $wc_gallery.find( '.product-sticky-thumbs' ).length ) {
                    $wc_gallery.on( 'click', '.product-sticky-thumbs img', this.clickStickyThumbnail.bind( this ) );
                    window.addEventListener( 'scroll', this.scrollStickyThumbnail.bind( this ), { passive: true } );
                }
            }

            // init slider after load, such as quickview
            if ( 'complete' === theme.status ) {
                self.$slider && self.$slider.length && theme.slider( self.$slider );
            }

            theme.$window.on( 'alpha_complete', function() {
                setTimeout( self.initAfterLazyload.bind( self ), 200 );
            } )
        }

        ProductGallery.prototype.initAfterLazyload = function() {
            this.currentPostImageSrc = this.$wc_gallery.find( '.wp-post-image' ).attr( 'src' );
        }

        /**
         * Intialize thumbs in vertical thumbs type
         * 
         * @since 1.0
         */
        ProductGallery.prototype.initThumbs = function() {
            var self = this;

            setupThumbs( self );

            // init thumbs
            this.$slider
                .on( 'initialized.slider', function( e ) {
                    // init thumbnails
                    self.initZoom();
                } )
        }

        ProductGallery.prototype.openImageFull = function( e ) {
            if ( wc_single_product_params.photoswipe_options ) {
                e.preventDefault();

                // Carousel Type
                var carousel = this.$wc_gallery.find( '.product-single-carousel' ).data( 'slider' );
                if ( carousel ) {
                    wc_single_product_params.photoswipe_options.index = carousel.activeIndex;
                }
                if ( this.wc_gallery.$images.filter( '.yith_featured_content' ).length ) {
                    wc_single_product_params.photoswipe_options.index = carousel ? carousel.activeIndex - 1 : $( e.currentTarget ).closest( '.woocommerce-product-gallery__image' ).index() - 1;
                }

                this.wc_gallery.openPhotoswipe( e );

                // to disable elementor's light box.
                e.stopPropagation();
            }
        }

        /**
         * Event handler triggered when sticky thumbnail is clicked
         *
         * @since 1.0
         * @param {Event} e Mouse click event
         */
        ProductGallery.prototype.clickStickyThumbnail = function( e ) {
            var self = this;
            var $thumb = $( e.currentTarget );

            $thumb.addClass( 'active' ).siblings( '.active' ).removeClass( 'active' );
            this.isStickyScrolling = true;
            theme.scrollTo( this.$wc_gallery.find( '.product-sticky-images > :nth-child(' + ( $thumb.index() + 1 ) + ')' ) );
            setTimeout( function() {
                self.isStickyScrolling = false;
            }, 300 );
        }

        /**
         * Event handler triggered while scrolling on sticky thumbnails
         *
         * @since 1.0
         */
        ProductGallery.prototype.scrollStickyThumbnail = function() {
            var self = this;
            if ( this.isStickyScrolling ) {
                return;
            }
            this.$wc_gallery.find( '.product-sticky-images img:not(.zoomImg)' ).each( function() {
                if ( theme.isOnScreen( this ) ) {
                    self.$wc_gallery.find( '.product-sticky-thumbs-inner > :nth-child(' +
                        ( $( this ).closest( '.woocommerce-product-gallery__image' ).index() + 1 ) + ')' )
                        .addClass( 'active' ).siblings().removeClass( 'active' );
                    return false;
                }
            } );
        }

        ProductGallery.prototype.initZoomImage = function( zoomTarget ) {
            if ( alpha_vars.single_product.zoom_enabled ) {
                var width = zoomTarget.children( 'img' ).attr( 'data-large_image_width' ),
                    // zoom option
                    zoom_options = $.extend( {
                        touch: false
                    }, alpha_vars.single_product.zoom_options );

                if ( 'ontouchstart' in document.documentElement ) {
                    zoom_options.on = 'click';
                }

                zoomTarget.trigger( 'zoom.destroy' ).children( '.zoomImg' ).remove();

                // zoom
                if ( 'undefined' != typeof width && zoomTarget.width() < width ) {
                    zoomTarget.zoom( zoom_options );

                    // show zoom on hover
                    // setTimeout(function () {
                    zoomTarget.find( ':hover' ).length && zoomTarget.trigger( 'mouseover' );
                    // }, 100);
                }
            }
        }

        ProductGallery.prototype.changePostImage = function( variation ) {

            var $image = this.$wc_gallery.find( '.wp-post-image' );

            // Has post image been changed?
            if ( $image.hasClass( 'd-lazyload' ) || this.currentPostImageSrc == $image.attr( 'src' ) ) {
                return;
            } else {
                this.currentPostImageSrc = $image.attr( 'src' );
            }

            // Add found class to form, change nav thumbnail image on found variation
            var $postThumbImage = this.$wc_gallery.find( '.product-thumbs img' ).eq( 0 ).add( '.product-sticky-content .wp-post-image' ),
                $gallery = this.$wc_gallery.find( '.product-gallery' );

            if ( $postThumbImage.length ) {
                if ( typeof variation != 'undefined' ) {
                    if ( 'reset' == variation ) {
                        $postThumbImage.wc_reset_variation_attr( 'src' );
                        $postThumbImage.wc_reset_variation_attr( 'srcset' );
                        $postThumbImage.wc_reset_variation_attr( 'sizes' );
                        $postThumbImage.wc_reset_variation_attr( 'alt' );
                    } else {
                        $postThumbImage.wc_set_variation_attr( 'src', variation.image.gallery_thumbnail_src );
                        variation.image.alt && $postThumbImage.wc_set_variation_attr( 'alt', variation.image.alt );
                        variation.image.srcset && $postThumbImage.wc_set_variation_attr( 'srcset', variation.image.srcset );
                        variation.image.sizes && $postThumbImage.wc_set_variation_attr( 'sizes', variation.image.sizes );
                    }
                } else {
                    $postThumbImage.wc_set_variation_attr( 'src', this.currentPostImageSrc );
                    $image.attr( 'srcset' ) && $postThumbImage.wc_set_variation_attr( 'srcset', $image.attr( 'srcset' ) );
                    $image.attr( 'sizes' ) && $postThumbImage.wc_set_variation_attr( 'sizes', $image.attr( 'sizes' ) );
                    $image.attr( 'alt' ) && $postThumbImage.wc_set_variation_attr( 'alt', $image.attr( 'alt' ) );
                }
            }

            // Refresh zoom
            this.initZoomImage( $image.parent() );

            // Refresh if carousel layout
            var carousel = $gallery.children( '.product-single-carousel,.product-gallery-carousel' ).data( 'slider' );
            carousel && ( carousel.update() );

            if ( !firstScrollTopOnSticky ) {
                // If sticky, go to top;
                if ( this.$wc_gallery.closest( '.product' ).find( '.sticky-sidebar .summary' ).length ) {
                    theme.scrollTo( this.$wc_gallery, 400 );
                }
            }
            firstScrollTopOnSticky = false;
        }

        ProductGallery.prototype.initZoom = function() {
            if ( alpha_vars.single_product.zoom_enabled ) {
                var self = this;

                // if not quickview, widget
                if ( !this.$wc_gallery.closest( '.product-quickview' ).length && !this.$wc_gallery.closest( '.product-widget' ).length ) {

                    var buttons = '';
                    if ( !this.$wc_gallery.hasClass( 'woocommerce-product-gallery--without-images' ) ) {
                        buttons = '<button class="product-gallery-btn product-image-full ' + alpha_vars.theme_icon_prefix + '-icon-zoom" aria-label="Product Gallery"></button>';
                    }
                    buttons += ( this.$wc_gallery.data( 'buttons' ) || '' );
                    // show image full toggler
                    if ( this.$slider.length && this.$slider.hasClass( 'product-single-carousel' ) ) {
                        // if default or horizontal type, show only one
                        if ( !this.$slider.find( '.product-gallery-btn' ).length ) {
                            this.$slider.after( buttons );
                        }
                    } else {
                        // else other types
                        this.$wc_gallery.find( '.woocommerce-product-gallery__image > a' ).each( function() {
                            if ( !$( this ).parent().find( '.product-gallery-btn' ).length ) {
                                $( this ).after( buttons );
                            }
                        } );
                    }
                }

                // zoom images
                this.$wc_gallery.find( '.woocommerce-product-gallery__image > a' ).each( function() {
                    self.initZoomImage( $( this ) );
                } );
            }
        }

        return function( selector ) {
            if ( $.fn.wc_product_gallery ) {
                theme.$( selector ).each( function() {
                    var $this = $( this );
                    $this.data( 'alpha_product_gallery', new ProductGallery( $this ) );
                } );
            }
        }
    } )();

    /**
     * Create product single object
     * 
     * @class ProductSingle
     * @since 1.0
     * @param {string|jQuery} selector 
     * @return {void}
     */
    theme.createProductSingle = ( function() {
        function ProductSingle( $el ) {
            return this.init( $el );
        }

        // Public Properties
        ProductSingle.prototype.init = function( $el ) {
            this.$product = $el;

            // gallery
            $el.find( '.woocommerce-product-gallery' ).each( function() {
                theme.createProductGallery( $( this ) );
            } )

            // variation        
            $( '.reset_variations' ).hide().removeClass( 'd-none' );

            // after load, such as quickview
            if ( 'complete' === theme.status ) {
                // variation form
                if ( $.fn.wc_variation_form && typeof wc_add_to_cart_variation_params !== 'undefined' ) {
                    this.$product.find( '.variations_form' ).wc_variation_form();
                }

                // quantity input
                theme.quantityInput( this.$product.find( '.qty' ) );

                // countdown
                if ( typeof theme.countdown == 'function' ) {
                    theme.countdown( this.$product.find( '.product-countdown' ) );
                }
            } else {
                // sticky add to cart cart
                if ( !this.$product.hasClass( 'product-widget' ) || this.$product.hasClass( 'product-quickview' ) ) {
                    this.stickyCartForm( this.$product.find( '.product-sticky-content' ) );
                }
            }
        }

        /**
         * Make cart form as sticky
         * 
         * @since 1.0
         * @param {string|jQuery} selector 
         * @return {void}
         */
        ProductSingle.prototype.stickyCartForm = function( selector ) {
            var $stickyForm = theme.$( selector );

            if ( $stickyForm.length != 1 ) {
                return;
            }

            var $product = $stickyForm.closest( '.product' );

            var sticky = $stickyForm.data( 'sticky-content' );
            if ( sticky ) {
                /**
                 * Register getTop function for sticky "add to cart" form, that runs above 768px.
                 * 
                 * @since 1.0
                 */
                sticky.getTop = function() {
                    var $parent;
                    if ( $stickyForm.closest( '.sticky-sidebar' ).length ) {
                        $parent = $product.find( '.woocommerce-product-gallery' );
                    } else {
                        $parent = $stickyForm.closest( 'form.cart' );
                        // if ( $parent.hasClass( 'elementor' ) ) {
                        //     $parent = $stickyForm.closest( '.cart' );
                        // }
                    }
                    return $parent.offset().top + $parent.height();
                }

                sticky.onFixed = function() {
                    theme.$body.addClass( 'addtocart-fixed' );
                }

                sticky.onUnfixed = function() {
                    theme.$body.removeClass( 'addtocart-fixed' );
                }
            }

            // Fix top in mobile, fix bottom otherwise
            function _changeFixPos() {
                theme.requestTimeout( function() {
                    $stickyForm.removeClass( 'fix-top fix-bottom' ).addClass( window.innerWidth < 768 ? 'fix-top' : 'fix-bottom' );
                }, 50 );
            }

            theme.$window.on( 'sticky_refresh_size.alpha', _changeFixPos );

            _changeFixPos();
        }

        return function( selector ) {
            theme.$( selector ).each( function() {
                var $this = $( this );
                $this.data( 'alpha_product_single', new ProductSingle( $this ) );
            } );
        }
    } )();

    /**
     * Create quantity input object
     * 
     * @class QuantityInput
     * @since 1.0
     * @param {string} selector
     * @return {void}
     */
    theme.quantityInput = ( function() {

        function QuantityInput( $el ) {
            return this.init( $el );
        }

        QuantityInput.min = 1;
        QuantityInput.max = 1000000;

        QuantityInput.prototype.init = function( $el ) {
            var self = this;

            self.$minus = false;
            self.$plus = false;
            self.$value = false;
            self.value = false;

            // call Events
            self.startIncrease = self.startIncrease.bind( self );
            self.startDecrease = self.startDecrease.bind( self );
            self.stop = self.stop.bind( self );

            // Variables
            self.min = parseInt( $el.attr( 'min' ) );
            self.max = parseInt( $el.attr( 'max' ) );

            self.min || ( $el.attr( 'min', self.min = QuantityInput.min ) )
            self.max || ( $el.attr( 'max', self.max = QuantityInput.max ) )

            // Add DOM elements and event listeners
            self.$value = $el.val( self.value = Math.max( parseInt( $el.val() ), 1 ) );
            self.$minus = $el.parent().find( '.quantity-minus' ).on( 'click', theme.preventDefault );
            self.$plus = $el.parent().find( '.quantity-plus' ).on( 'click', theme.preventDefault );

            if ( 'ontouchstart' in document ) {
                self.$minus.get( 0 ).addEventListener( 'touchstart', self.startDecrease, { passive: true } )
                self.$plus.get( 0 ).addEventListener( 'touchstart', self.startIncrease, { passive: true } )
            } else {
                self.$minus.on( 'mousedown', self.startDecrease )
                self.$plus.on( 'mousedown', self.startIncrease )
            }

            theme.$body.on( 'mouseup', self.stop )
                .on( 'touchend', self.stop );
        }

        QuantityInput.prototype.startIncrease = function( e ) {
            var self = this;
            self.value = self.$value.val();
            self.value < self.max && ( self.$value.val( ++self.value ), self.$value.trigger( 'change' ) );
            self.increaseTimer = theme.requestTimeout( function() {
                self.speed = 1;
                self.increaseTimer = theme.requestInterval( function() {
                    self.$value.val( self.value = Math.min( self.value + Math.floor( self.speed *= 1.05 ), self.max ) );
                }, 50 );
            }, 400 );
        }

        QuantityInput.prototype.stop = function( e ) {
            ( this.increaseTimer || this.decreaseTimer ) && this.$value.trigger( 'change' );
            this.increaseTimer && ( theme.deleteTimeout( this.increaseTimer ), this.increaseTimer = 0 );
            this.decreaseTimer && ( theme.deleteTimeout( this.decreaseTimer ), this.decreaseTimer = 0 );
        }

        QuantityInput.prototype.startDecrease = function( e ) {
            var self = this;
            self.value = self.$value.val();
            self.value > self.min && ( self.$value.val( --self.value ), self.$value.trigger( 'change' ) );
            self.decreaseTimer = theme.requestTimeout( function() {
                self.speed = 1;
                self.decreaseTimer = theme.requestInterval( function() {
                    self.$value.val( self.value = Math.max( self.value - Math.floor( self.speed *= 1.05 ), self.min ) );
                }, 50 );
            }, 400 );
        }

        return function( selector ) {
            theme.$( selector ).each( function() {
                var $this = $( this );
                // if not initialized
                $this.data( 'quantityinput' ) ||
                    $this.data( 'quantityinput', new QuantityInput( $this ) );
            } );
        }
    } )();

    $( window ).on( 'alpha_complete', function() {
        theme.woocommerce.init();
        theme.quantityInput( '.qty' );
    } );
} )( window.jQuery );