/**
 * Alpha FrameWork Async JS Library
 * 
 * @author     D-THEMES
 * @package    WP Alpha Framework
 * @subpackage Theme
 * @since      1.0
 * @version    1.0
 */
'use strict';

( function( $ ) {
	/**
	 * jQuery easing
	 * 
	 * @since 1.0
	 */
	$.extend( $.easing, {
		def: 'easeOutQuad',
		swing: function( x, t, b, c, d ) {
			return $.easing[$.easing.def]( x, t, b, c, d );
		},
		easeOutQuad: function( x, t, b, c, d ) {
			return -c * ( t /= d ) * ( t - 2 ) + b;
		},
		easeInOutQuart: function( x, t, b, c, d ) {
			if ( ( t /= d / 2 ) < 1 ) return c / 2 * t * t * t * t + b;
			return -c / 2 * ( ( t -= 2 ) * t * t * t - 2 ) + b;
		},
		easeOutQuint: function( x, t, b, c, d ) {
			return c * ( ( t = t / d - 1 ) * t * t * t * t + 1 ) + b;
		}
	} );

	theme.defaults.popup = {
		fixedContentPos: true,
		closeOnBgClick: false,
		removalDelay: 350,
		callbacks: {
			beforeOpen: function() {
				if ( this.fixedContentPos ) {
					var scrollBarWidth = window.innerWidth - document.body.clientWidth;
					$( '.sticky-content.fixed' ).css( 'padding-right', scrollBarWidth );
					$( '.mfp-wrap' ).css( 'overflow', 'hidden auto' );
				}
			},
			close: function() {
				if ( this.fixedContentPos ) {
					$( '.mfp-wrap' ).css( 'overflow', '' );
					$( '.sticky-content.fixed' ).css( 'padding-right', '' );
				}
			}
		},
	}

	theme.defaults.isotope = {
		itemSelector: '.grid-item',
		layoutMode: 'masonry',
		percentPosition: true,
		masonry: {
			columnWidth: '.grid-space'
		},
		getSortData: {
			order: '[data-creative-order] parseInt',
			order_xxl: '[data-creative-order-xxl] parseInt',
			order_xlg: '[data-creative-order-xlg] parseInt',
			order_xl: '[data-creative-order-xl] parseInt',
			order_lg: '[data-creative-order-lg] parseInt',
			order_md: '[data-creative-order-md] parseInt',
			order_sm: '[data-creative-order-sm] parseInt',
		},
	}

	theme.defaults.popupPresets = {
		login: {
			type: 'ajax',
			mainClass: "mfp-login mfp-fade",
			tLoading: '<div class="login-popup"><div class="d-loading"><i></i></div></div>',
			preloader: true,
			items: {
				src: alpha_vars.ajax_url,
			},
			ajax: {
				settings: {
					method: 'post',
					data: {
						action: 'alpha_account_form',
						nonce: alpha_vars.nonce
					}
				}, cursor: 'mfp-ajax-cur' // CSS class that will be added to body during the loading (adds "progress" cursor)
			}
		},
		video: {
			type: 'iframe',
			mainClass: "mfp-fade",
			preloader: false,
			closeBtnInside: false
		},
		firstpopup: {
			type: 'inline',
			mainClass: 'mfp-popup-template mfp-newsletter-popup mfp-flip-popup',
			callbacks: {
				beforeClose: function() {
					// if "do not show" is checked
					$( '.mfp-alpha .popup .hide-popup input[type="checkbox"]' ).prop( 'checked' ) && theme.setCookie( 'hideNewsletterPopup', true, 7 );
				}
			}
		},
		popup_template: {
			type: 'ajax',
			mainClass: "mfp-popup-template mfp-flip-popup",
			tLoading: '<div class="popup-template"><div class="d-loading"><i></i></div></div>',
			preloader: true,
			items: {
				src: alpha_vars.ajax_url,
			},
			ajax: {
				settings: {
					method: 'post',
				}, cursor: 'mfp-ajax-cur' // CSS class that will be added to body during the loading (adds "progress" cursor)
			}
		},
	}

	theme.defaults.slider = {
		a11y: false,
		containerModifierClass: 'slider-container-', // NEW
		slideClass: 'slider-slide',
		wrapperClass: 'slider-wrapper',
		slideActiveClass: 'slider-slide-active',
		slideDuplicateClass: 'slider-slide-duplicate',
	}
	theme.defaults.sliderPresets = {
		'compare-slider': {
			spaceBetween: 10,
			slidesPerView: 2,
			breakpoints: {
				992: {
					spaceBetween: 30,
					slidesPerView: 4,
				},
				768: {
					spaceBetween: 20,
					slidesPerView: 3,
				}
			}
		},
		'products-flipbook': {
			onInitialized: function() {
				function stopDrag( e ) {
					$( e.target ).closest( '.product-single-carousel, .product-gallery-carousel, .product-thumbs' ).length && e.stopPropagation();
				}
				this.wrapperEl.addEventListener( 'mousedown', stopDrag );
				if ( 'ontouchstart' in document ) {
					this.wrapperEl.addEventListener( 'touchstart', stopDrag, { passive: true } );
				}
			}
		}
	}

	/**
	 * Prevent default handler
	 *
	 * @since 1.0
	 * @param {Event} e
	 * @return {void}
	 */
	theme.preventDefault = function( e ) { e.preventDefault() }

	/**
	 * Initialize template's content.
	 * 
	 * @since 1.0
	 * @param {jQuery} $template
	 * @return {void}
	 */
	theme.initTemplate = function( $template ) {
		theme.lazyload( $template );
		theme.slider( $template.find( '.slider-wrapper' ) );
		theme.isotopes( $template.find( '.grid' ) );
		theme.woocommerce.initProducts( $template );
		if ( typeof theme.countdown == 'function' ) {
			theme.countdown( $template.find( '.countdown' ) );
		}
		theme.call( function() {
			theme.$window.trigger( 'alpha_loadmore' );
		}, 300 );
		theme.$body.trigger( 'alpha_init_tab_template' );
	}

	/**
	 * Load template's content.
	 * 
	 * @since 1.0
	 * @param {jQuery} $template
	 * @return {void}
	 */
	theme.loadTemplate = function( $template ) {
		var html = '';
		var orignal_split = alpha_vars.resource_split_tasks;

		// To run carousel immediately
		alpha_vars.resource_split_tasks = 0;

		$template.children( '.load-template' ).each( function() {
			html += this.text;
		} );
		if ( html ) {
			$template.html( html );
			if ( theme.skeleton ) {
				theme.skeleton( $( '.skeleton-body' ), function() {
					theme.initTemplate( $template );
				} );
			} else {
				theme.initTemplate( $template );
			}
		}

		alpha_vars.resource_split_tasks = orignal_split;
	}

	/**
	 * Check if window's width is really resized.
	 * 
	 * @since 1.0
	 * @param {number} timeStamp
	 * @return {boolean}
	 */
	theme.windowResized = function( timeStamp ) {
		if ( timeStamp == theme.resizeTimeStamp ) {
			return theme.resizeChanged;
		}
		theme.resizeChanged = theme.canvasWidth != window.innerWidth;
		theme.canvasWidth = window.innerWidth;
		theme.resizeTimeStamp = timeStamp;
		return theme.resizeChanged;
	}

	/**
	 * Set cookie
	 * 
	 * @since 1.0
	 * @param {string} name Cookie name
	 * @param {string} value Cookie value
	 * @param {number} exdays Expire period
	 * @return {void}
	 */
	theme.setCookie = function( name, value, exdays ) {
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
	theme.getCookie = function( name ) {
		var n = name + "=";
		var ca = document.cookie.split( ';' );
		for ( var i = 0; i < ca.length; ++i ) {
			var c = ca[i];
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
	 * Scroll to given target in given duration.
	 *
	 * @since 1.0
	 * @param {mixed}  target   This can be number or string seletor or jQuery object.
	 * @param {number} duration This can be omitted.
	 * @return {void}
	 */
	theme.scrollTo = function( target, duration ) {
		var _duration = typeof duration == 'undefined' ? 0 : duration;
		var offset;

		if ( typeof target == 'number' ) {
			offset = target;
		} else {
			var $target = theme.$( target ).closest( ':visible' );
			if ( $target.length ) {
				var offset = $target.offset().top;
				var $wpToolbar = $( '#wp-toolbar' );
				window.innerWidth > 600 && $wpToolbar.length && ( offset -= $wpToolbar.parent().outerHeight() );
				$( '.sticky-content.fix-top.fixed' ).each( function() {
					offset -= this.offsetHeight;
				} )
			}
		}

		$( 'html,body' ).stop().animate( { scrollTop: offset }, _duration );
	}

	/**
	 * Scroll to fixed content
	 * 
	 * @since 1.0
	 * @param {number} arg
	 * @param {number} duration
	 * @return {void}
	 */
	theme.scrollToFixedContent = function( arg, duration ) {
		var stickyTop = 0,
			toolbarHeight = window.innerWidth > 600 && $( '#wp-toolbar' ).parent().length ? $( '#wp-toolbar' ).parent().outerHeight() : 0;

		$( '.sticky-content.fix-top' ).each( function() {
			if ( $( this ).hasClass( 'toolbox-top' ) ) {
				var pt = $( this ).css( 'padding-top' ).slice();
				if ( pt.length > 2 ) {
					stickyTop -= Number( pt.slice( 0, -2 ) );
				}
				return;
			}

			var fixed = $( this ).hasClass( 'fixed' );

			stickyTop += $( this ).addClass( 'fixed' ).outerHeight();

			fixed || $( this ).removeClass( 'fixed' );
		} )

		theme.scrollTo( arg - stickyTop - toolbarHeight, duration );
	}

	/**
	 * Get value by given param from url
	 *
	 * @since 1.0
	 * @param {string} href
	 * @param {string} name
	 * @return {string} value
	 */
	theme.getUrlParam = function( href, name ) {
		var url = document.createElement( 'a' ), s, r;
		url.href = decodeURIComponent( decodeURI( href ) );
		s = url.search;
		if ( s.startsWith( '?' ) ) {
			s = s.substr( 1 );
		}
		var params = {};
		s.split( '&' ).forEach( function( v ) {
			var i = v.indexOf( '=' );
			if ( i >= 0 ) {
				params[v.substr( 0, i )] = v.substr( i + 1 );
			}
		} );
		return params[name] ? params[name] : '';
	}

	/**
	 * Add param to url
	 *
	 * @since 1.0
	 * @param {string} href
	 * @param {string} name
	 * @param {mixed} value
	 * @return {string}
	 */
	theme.addUrlParam = function( href, name, value ) {
		var url = document.createElement( 'a' ), s, r;
		href = decodeURIComponent( decodeURI( href ) );
		url.href = href;
		s = url.search;
		if ( 0 <= s.indexOf( name + '=' ) ) {
			r = s.replace( new RegExp( name + '=[^&]*' ), name + '=' + value );
		} else {
			r = ( s.length && 0 <= s.indexOf( '?' ) ) ? s : '?';
			r.endsWith( '?' ) || ( r += '&' );
			r += name + '=' + value;
		}
		return encodeURI( href.replace( s, '' ) + r.replace( /&+/, '&' ) );
	}

	/**
	 * Remove param from url
	 *
	 * @since 1.0
	 * @param {string} href
	 * @param {string} name
	 * @return {string}
	 */
	theme.removeUrlParam = function( href, name ) {
		var url = document.createElement( 'a' ), s, r;
		href = decodeURIComponent( decodeURI( href ) );
		url.href = href;
		s = url.search;
		if ( 0 <= s.indexOf( name + '=' ) ) {
			r = s.replace( new RegExp( name + '=[^&]*' ), '' ).replace( /&+/, '&' ).replace( '?&', '?' );
			r.endsWith( '&' ) && ( r = r.substr( 0, r.length - 1 ) );
			r.endsWith( '?' ) && ( r = r.substr( 0, r.length - 1 ) );
			r = r.replace( '&&', '&' );
		} else {
			r = s;
		}
		return encodeURI( href.replace( s, '' ) + r );
	}

	/**
	 * Show More
	 *
	 * @since 1.0
	 * @param {string} selector
	 */
	theme.showMore = function( selector ) {
		theme.$( selector ).after( '<div class="d-loading relative"><i></i></div>' );
		theme.$window.trigger( 'scroll.loadingPosition' );
	}

	/**
	 * Hide more
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	theme.hideMore = function( selector ) {
		theme.$( selector ).children( '.d-loading-stick, .d-loading' ).remove();
	}

	/**
	 * Start count to number
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	theme.countTo = function( selector, runAsSoon = false ) {
		if ( $.fn.countTo ) {
			theme.$( selector ).each( function() {
				var el = this;
				var $this = $( this );
				function runProgress() {
					setTimeout( function() {
						var options = {
							onComplete: function() {
								$this.addClass( 'complete' );
							}
						};
						$this.data( 'duration' ) && ( options.speed = $this.data( 'duration' ) );
						$this.data( 'from-value' ) && ( options.from = $this.data( 'from-value' ) );
						$this.data( 'to-value' ) && ( options.to = $this.data( 'to-value' ) );

						options.decimals = options.to && typeof options.to === 'string' && options.to.indexOf( '.' ) >= 0 ? ( options.to.length - options.to.indexOf( '.' ) - 1 ) : 0;
						$this.countTo( options );
					}, 300 );
				}
				runAsSoon ? runProgress() : theme.appear( el, runProgress );
			} );
		}
	}
	/**
	 * Initialize Parallax Background
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	theme.parallax = ( function() {

		function Parallax( $el, opts ) {
			return this.init( $el, opts );
		};

		Parallax.prototype.init = function( $el, opts ) {
			if ( $el.data( '__parallax' ) ) {
				return this;
			}

			this.$el = $el;

			this
				.setData()
				.setOptions( opts )
				.build();

			return this;
		}

		Parallax.prototype.setData = function() {
			this.$el.data( '__parallax', this );

			return this;
		}

		Parallax.prototype.setOptions = function( opts ) {
			this.options = $.extend( true, {}, Parallax.defaults, opts, {
				wrapper: this.$el
			} );

			return this;
		}

		Parallax.prototype.build = function() {
			var self = this,
				background;

			// Create Parallax Element
			background = $( '<div class="parallax-background"></div>' );

			// Set Style for Parallax Element
			var bg = self.options.wrapper.attr( 'data-parallax-image' ) ? 'url(' + self.options.wrapper.attr( 'data-parallax-image' ) + ')' : self.options.wrapper.css( 'background-image' );
			background.css( {
				'background-image': bg,
				'background-size': 'cover',
				'background-position': '50% 0%',
				'position': 'absolute',
				'width': '100%',
				'height': self.options.speed * 100 + '%'
			} );

			// Add Parallax Element on DOM
			self.options.wrapper.prepend( background );

			// Set Overlfow Hidden and Position Relative to Parallax Wrapper
			self.options.wrapper.css( {
				'position': 'relative',
				'overflow': 'hidden'
			} );

			if ( self.options.direction == 'left' || self.options.direction == 'right' ) { // horizontal
				background.css( {
					'background-position': '0% 50%',
					'width': self.options.speed * 100 + '%',
					'height': '100%',
				} );
			}

			// Parallax Effect on Scroll & Resize
			var parallaxEffectOnScrollResize = function() {
				var skrollr_size = 100 * self.options.speed,
					skrollr_start = -( skrollr_size - 100 );
				if ( self.options.direction == 'up' ) {
					background.attr( "data-bottom-top", "bottom: " + skrollr_start + "%;" ).attr( "data-top-bottom", "bottom: 0%;" );
				} else if ( self.options.direction == 'down' ) {
					background.attr( "data-bottom-top", "top: " + skrollr_start + "%;" ).attr( "data-top-bottom", "top: 0%;" );
				} else if ( self.options.direction == 'left' ) {
					background.attr( "data-bottom-top", "right: " + skrollr_start + "%;" ).attr( "data-top-bottom", "right: 0%;" );
				} else {
					background.attr( "data-bottom-top", "left: " + skrollr_start + "%;" ).attr( "data-top-bottom", "left: 0%;" );
				}
			}

			if ( !theme.isMobile ) {
				parallaxEffectOnScrollResize();
			} else {
				if ( self.options.enableOnMobile == true ) {
					parallaxEffectOnScrollResize();
				} else {
					self.options.wrapper.addClass( 'parallax-disabled' );
				}
			}

			return this;
		}


		// jquery plugin
		$.fn.themeParallax = function( opts ) {
			if ( theme.isMobile ) {
				return;
			}

			if ( typeof skrollr == 'undefined' ) {
				return this;
			}
			var obj = this.map( function() {
				var $this = $( this );

				if ( $this.data( '__parallax' ) ) {
					return $this.data( '__parallax' );
				} else {
					return new Parallax( $this, opts );
				}

			} );

			if ( theme.parallaxSkrollr && skrollr.get() ) {
				theme.parallaxSkrollr.refresh();
			} else if ( !theme.isMobile ) {
				theme.parallaxSkrollr = skrollr.init( { forceHeight: false, smoothScrolling: true } );
				theme.parallaxSkrollr.refresh();
			}
			return obj;
		}

	} )()


	// Show loading overlay when $.fn.block is called
	var funcBlock = $.fn.block;
	$.fn.block = function( opts ) {
		if ( theme.status == 'complete' ) { // To prevent single product widget's found variation blocking while page loading
			this.append( '<div class="d-loading"><i></i></div>' );
			funcBlock.call( this, opts );
		}
		return this;
	}

	// Hide loading overlay when $.fn.block is called
	var funcUnblock = $.fn.unblock;
	$.fn.unblock = function( opts ) {
		if ( theme.status == 'complete' ) { // To prevent single product widget's found variation blocking while page loading
			funcUnblock.call( this, opts );
			this.hasClass( 'processing' ) || this.parents( '.processing' ).length || this.children( '.d-loading' ).remove();
			if ( theme.woocommerce && typeof theme.woocommerce.initAlertAction == 'function' ) {
				theme.woocommerce.initAlertAction();
			}

		}
		return this;
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
	theme.stickyContent = ( function() {
		function StickyContent( $el, options ) {
			return this.init( $el, options );
		}

		function refreshAll() {
			theme.$window.trigger( 'sticky_refresh.alpha', {
				index: 0,
				offsetTop: window.innerWidth > 600 && $( '#wp-toolbar' ).length && $( '#wp-toolbar' ).parent().is( ':visible' ) ? $( '#wp-toolbar' ).parent().outerHeight() : 0
			} );
		}

		function refreshAllSize( e ) {
			if ( !e || theme.windowResized( e.timeStamp ) ) {
				theme.$window.trigger( 'sticky_refresh_size.alpha' );
				theme.requestFrame( refreshAll );
			}
		}

		StickyContent.prototype.init = function( $el, options ) {
			this.$el = $el;
			this.options = $.extend( true, {}, theme.defaults.sticky, options, theme.parseOptions( $el.attr( 'data-sticky-options' ) ) );
			this.scrollPos = window.pageYOffset; // issue: heavy js performance : 30.7ms
			this.originalHeight = this.$el.outerHeight();
			theme.$window
				.on( 'sticky_refresh.alpha', this.refresh.bind( this ) )
				.on( 'sticky_refresh_size.alpha', this.refreshSize.bind( this ) );
		}

		StickyContent.prototype.refreshSize = function( e ) {

			var beWrap = window.innerWidth >= this.options.minWidth && window.innerWidth <= this.options.maxWidth;
			if ( typeof this.top == 'undefined' ) {
				this.top = this.options.top;
			}

			if ( window.innerWidth >= 768 && this.getTop ) {
				this.top = this.getTop();
			} else if ( !this.options.top ) {
				this.top = this.isWrap ?
					this.$el.parent().offset().top :
					this.$el.offset().top + this.$el[0].offsetHeight;

				// if sticky header has toggle dropdown menu, increase top
				if ( this.$el.find( '.toggle-menu.show-home' ).length ) {
					this.top += this.$el.find( '.toggle-menu .dropdown-box' )[0].offsetHeight;
				}
			}

			if ( !this.isWrap ) {
				beWrap && this.wrap();
			} else {
				beWrap || this.unwrap();
			}
			e && theme.requestTimeout( this.refreshSize.bind( this ), 50 );

		}

		StickyContent.prototype.wrap = function() {
			this.$el.wrap( '<div class="sticky-content-wrapper"></div>' );
			this.$el.closest( '.toolbox-horizontal' ).addClass( 'horizontal-fixed' );
			this.isWrap = true;
		}

		StickyContent.prototype.unwrap = function() {
			this.$el.unwrap( '.sticky-content-wrapper' );
			this.$el.closest( '.toolbox-horizontal' ).removeClass( 'horizontal-fixed' );
			this.isWrap = false;
		}

		StickyContent.prototype.refresh = function( e, data ) {
			var pageYOffset = window.pageYOffset /* + data.offsetTop*/; // issue: heavy js performance, 6.7ms
			var $el = this.$el;

			this.refreshSize();
			$( '.fixed.fix-top' ).each( function() {
				pageYOffset += $( this ).outerHeight();
			} );

			// Make sticky
			if ( ( pageYOffset >= this.top + this.originalHeight ) && this.isWrap ) {

				// calculate height
				this.height = $el[0].offsetHeight;
				$el.hasClass( 'fixed' ) || $el.parent().css( 'height', this.height + 'px' );

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
						data.offsetTop += $el[0].offsetHeight;
					} else if ( $el.hasClass( 'fix-bottom' ) ) {
						data.offsetBottom += $el[0].offsetHeight;
					}
				}
			} else {
				$el.parent().css( 'height', '' );
				$el.removeClass( 'fixed' ).css( { 'margin-top': '', 'margin-bottom': '', 'z-index': '' } );
				this.onUnfixed && this.onUnfixed();
			}
			theme.$window.trigger( 'alpha_finish_sticky' );
		}

		theme.$window.on( 'alpha_complete', function() {
			window.addEventListener( 'scroll', refreshAll, { passive: true } );
			theme.$window.on( 'resize', refreshAllSize );
			setTimeout( function() {
				refreshAllSize();
			}, 1000 );
		} )

		return function( selector, options ) {
			theme.$( selector ).each( function() {
				var $this = $( this );
				$this.data( 'sticky-content' ) || $this.data( 'sticky-content', new StickyContent( $this, options ) );
			} )
		}
	} )()


	/**
	 * Register events for alert
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	theme.alert = function( selector ) {
		theme.$body.on( 'click', selector + ' .btn-close', function( e ) {
			e.preventDefault();
			if ( $( this ).closest( '.elementor-widget-' + alpha_vars.theme + '_widget_alert' ).length ) {
				selector = '.elementor-widget-' + alpha_vars.theme + '_widget_alert';
			}
			$( this ).closest( selector ).fadeOut( function() {
				$( this ).remove();
			} );
		} );
	}

	/**
	 * Run isotopes
	 *
	 * @since 1.0
	 * @param {string} selector
	 * @param {Object} options
	 * @return {void}
	 */
	theme.isotopes = ( function() {
		function _isotopeSort( e, $selector ) {
			var $grid = $selector ? $selector : $( '.grid' );

			if ( !$grid.length ) {
				return;
			}

			$grid.each( function( e ) {
				var $this = $( this );
				if ( !$this.attr( 'data-creative-breaks' ) || $this.hasClass( 'float-grid' ) ) {
					return;
				}

				$this.children( '.grid-item' ).css( { 'animation-fill-mode': 'none', '-webkit-animation-fill-mode': 'none' } );

				var width = window.innerWidth,
					breaks = JSON.parse( $this.attr( 'data-creative-breaks' ) ),
					cur_break = $this.attr( 'data-current-break' );

				if ( width >= breaks['xlg'] ) {
					width = '';
				} else if ( width >= breaks['xlg'] && width < breaks['xxl'] ) {
					width = 'xxl';
				} else if ( width >= breaks['xl'] && width < breaks['xlg'] ) {
					width = 'xlg';
				} else if ( width >= breaks['lg'] && width < breaks['xl'] ) {
					width = 'xl';
				} else if ( width >= breaks['md'] && width < breaks['lg'] ) {
					width = 'lg';
				} else if ( width >= breaks['sm'] && width < breaks['md'] ) {
					width = 'md';
				} else if ( width < breaks['sm'] ) {
					width = 'sm';
				}

				if ( width == cur_break ) {
					return;
				}

				if ( $this.data( 'isotope' ) ) {
					$this.isotope( {
						sortBy: 'order' + ( width ? '_' + width : '' ),
					} ).isotope( 'layout' );
				} else {
					var options = theme.parseOptions( $this.attr( 'data-grid-options' ) );
					options.sortBy = 'order' + ( width ? '_' + width : '' );
					$this.attr( 'data-grid-options', JSON.stringify( options ) );
				}
				$this.attr( 'data-current-break', width );
			} );
		}

		return function( selector, options ) {
			if ( !$.fn.imagesLoaded || !$.fn.isotope ) {
				return;
			}
			theme.$( selector ).each( function() {
				var $this = $( this );
				if ( $this.hasClass( 'grid-float' ) ) {
					return;
				}

				var settings = $.extend( true, {},
					theme.defaults.isotope,
					theme.parseOptions( this.getAttribute( 'data-grid-options' ) ),
					options ? options : {},
					$this.hasClass( 'masonry' ) ? { horizontalOrder: true } : {}
				);

				_isotopeSort( '', $this );

				if ( settings.masonry.columnWidth && !$this.children( settings.masonry.columnWidth ).length ) {
					delete settings.masonry.columnWidth;
				}

				Object.setPrototypeOf( this, HTMLElement.prototype );
				$this.children().each( function() {
					Object.setPrototypeOf( this, HTMLElement.prototype );
				} );

				if ( $this.attr( 'data-creative-breaks' ) ) {
					var width = window.innerWidth,
						breaks = JSON.parse( $this.attr( 'data-creative-breaks' ) );

					if ( width >= breaks['xlg'] ) {
						width = '';
					} else if ( width >= breaks['xlg'] && width < breaks['xxl'] ) {
						width = 'xxl';
					} else if ( width >= breaks['xl'] && width < breaks['xlg'] ) {
						width = 'xlg';
					} else if ( width >= breaks['lg'] && width < breaks['xl'] ) {
						width = 'xl';
					} else if ( width >= breaks['md'] && width < breaks['lg'] ) {
						width = 'lg';
					} else if ( width >= breaks['sm'] && width < breaks['md'] ) {
						width = 'md';
					} else if ( width < breaks['sm'] ) {
						width = 'sm';
					}

					$.extend( settings, {
						sortBy: 'order' + ( width ? '_' + width : '' ),
					} );
				}
				$this.imagesLoaded( function() {
					$this.addClass( 'isotope-loaded' ).isotope( settings );
					'undefined' != typeof elementorFrontend && $this.trigger( 'resize.waypoints' );
				} );
			} );

			theme.$window.on( 'resize', _isotopeSort );
		}
	} )();

	/**
	 * Register events for accordion
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	theme.accordion = function( selector ) {
		theme.$body.on( 'click', selector, function( e ) {
			var $this = $( this ),
				$body = $this.closest( '.card' ),
				$parent = $this.closest( '.accordion' );

			var link = $this.attr( 'href' );
			if ( '#' == link ) {
				$body = $body.children( ".card-body" );
			} else {
				$body = $body.find( '#' == link[0] ? $this.attr( 'href' ) : '#' + $this.attr( 'href' ) );
			}
			if ( !$body.length ) {
				return;
			}
			e.preventDefault();

			if ( !$parent.find( ".collapsing" ).length && !$parent.find( ".expanding" ).length ) {
				if ( $body.hasClass( 'expanded' ) ) {
					$parent.hasClass( 'radio-type' ) || slideToggle( $body );
				} else if ( $body.hasClass( 'collapsed' ) ) {
					if ( $parent.find( '.expanded' ).length > 0 ) {
						if ( theme.isIE ) {
							slideToggle( $parent.find( '.expanded' ), function() {
								slideToggle( $body );
							} );
						} else {
							slideToggle( $parent.find( '.expanded' ) );
							slideToggle( $body );
						}
					} else {
						slideToggle( $body );
					}
				}
			}
		} );

		// define slideToggle method
		var slideToggle = function( $wrap, callback ) {
			var $card = $wrap.closest( '.card' ),
				$header = $card.find( selector );
			if ( $wrap.hasClass( "expanded" ) ) {
				$header.removeClass( "collapse" ).addClass( "expand" );
				$card.removeClass( "collapse" ).addClass( "expand" );
				$wrap.addClass( "collapsing" ).slideUp( 300, function() {
					$wrap.removeClass( "expanded collapsing" ).addClass( "collapsed" );
					callback && callback();
				} );
			} else if ( $wrap.hasClass( "collapsed" ) ) {
				$header.removeClass( "expand" ).addClass( "collapse" );
				$card.removeClass( "expand" ).addClass( "collapse" );
				$wrap.addClass( "expanding" ).slideDown( 300, function() {
					$wrap.removeClass( "collapsed expanding" ).addClass( "expanded" );
					callback && callback();
				} );
			}
		};
	}

	/**
	 * Register events for tab
	 * 
	 * @since 1.0
	 * @param string selector
	 * @return {void}
	 */
	theme.tab = function( selector ) {
		if ( selector == '' ) {
			return;
		}
		var initTab = function( self, e ) {
			var $link = $( self );

			// if tab is loading, return
			if ( $link.closest( selector ).hasClass( 'loading' ) ) {
				return;
			}

			// get href
			var href = 'SPAN' == self.tagName ? $link.data( 'href' ) : $link.attr( 'href' );

			// get panel
			var $panel;
			if ( '#' == href ) {
				$panel = $link.closest( '.nav' ).siblings( '.tab-content' ).children( '.tab-pane' ).eq( $link.parent().index() );
			} else {
				$panel = $( ( '#' == href.substring( 0, 1 ) ? '' : '#' ) + href );
			}

			if ( $( '.elementor-element[data-id=' + href + ']' ).length ) {
				$panel = $( '.elementor-element[data-id=' + href + ']' );
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

			theme.loadTemplate( $panel );
			theme.slider( $panel.find( '.slider-wrapper' ) );
			$activePanel.removeClass( 'in active' );
			$panel.addClass( 'active in' );
			theme.refreshLayouts();
		}

		if ( typeof selector == 'object' ) {
			selector.find( '.nav-link' ).on( 'click', function( e ) { initTab( this, e ) } );
		} else {
			theme.$body.on( 'click', selector + ' .nav-link', function( e ) { initTab( this, e ) } );
		}
	}

	/**
	 * Playable video
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	theme.playableVideo = function( selector ) {
		$( selector + ' .video-play' ).on( 'click', function( e ) {
			var $video = $( this ).closest( selector );
			if ( $video.hasClass( 'playing' ) ) {
				$video.removeClass( 'playing' )
					.addClass( 'paused' )
					.find( 'video' )[0].pause();
			} else {
				$video.removeClass( 'paused' )
					.addClass( 'playing' )
					.find( 'video' )[0].play();
			}
			e.preventDefault();
		} );
		$( selector + ' video' ).on( 'ended', function() {
			$( this ).closest( '.post-video' ).removeClass( 'playing' );
		} );
	}


	/**
	 * Run appear animation
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	theme.appearAnimate = function( selector ) {
		var appearClass = typeof selector == 'string' && selector.indexOf( 'elementor-invisible' ) > 0 ? 'elementor-invisible' : 'appear-animate';

		theme.$( selector ).each( function() {
			var el = this;
			theme.appear( el, function() {
				if ( el.classList.contains( appearClass ) && !el.classList.contains( 'appear-animation-visible' ) ) {
					var settings = theme.parseOptions( el.getAttribute( 'data-settings' ) ),
						duration = 1000;

					if ( el.classList.contains( 'animated-slow' ) ) {
						duration = 2000;
					} else if ( el.classList.contains( 'animated-fast' ) ) {
						duration = 750;
					}

					theme.call( function() {
						el.style['animation-duration'] = duration + 'ms';
						el.style['animation-delay'] = settings._animation_delay + 'ms';
						el.style['transition-property'] = 'visibility, opacity';
						el.style['transition-duration'] = '0s';
						el.style['transition-delay'] = settings._animation_delay + 'ms';

						var animation_name = settings.animation || settings._animation || settings._animation_name;
						animation_name && el.classList.add( animation_name );

						el.classList.add( 'appear-animation-visible' );
						setTimeout(
							function() {
								el.style['transition-property'] = '';
								el.style['transition-duration'] = '';
								el.style['transition-delay'] = '';

								el.classList.add( 'animating' );

								setTimeout( function() {
									el.classList.add( 'animated-done' );
								}, duration );
							},
							settings._animation_delay ? settings._animation_delay + 500 : 500
						);
					} );
				}
			} );
		} );

		if ( typeof elementorFrontend == 'object' ) {
			theme.$window.trigger( 'resize.waypoints' );
		}
	}

	var videoIndex = {
		youtube: 'youtube.com',
		vimeo: 'vimeo.com/',
		gmaps: '//maps.google.',
		hosted: ''
	}

	/**
	 * Initialize popups
	 *
	 * @since 1.0
	 * @return {void}
	 */
	theme.initPopups = function() {

		// Register "Play Video" Popup
		theme.$body.on( 'click', '.btn-video-iframe', function( e ) {
			e.preventDefault();
			theme.popup( {
				items: {
					src: '<video src="' + $( this ).attr( 'href' ) + '" autoplay loop controls>',
					type: 'inline'
				},
				mainClass: 'mfp-video-popup'
			}, 'video' );
		} );

		// Register Image Popup
		theme.$body.on( 'click', '.btn-image-popup', function( e ) {
			e.preventDefault();
			var $this = $( this );
			if ( !$.fn.magnificPopup ) {
				var target = document.getElementById( 'alpha-theme-css' );
				if ( !target ) {
					target = document.getElementById( 'alpha-merged-css' );
				}
				target.parentNode.insertBefore( $( document.createElement( 'link' ) ).attr( { 'rel': 'stylesheet', 'id': 'alpha-magnific-popup-css', 'media': 'all', 'href': alpha_vars.assets_url + '/vendor/jquery.magnific-popup/magnific-popup.min.css' } )[0], target )
				$( document.createElement( 'script' ) ).attr( 'id', 'alpha-magnific-popup-js' ).appendTo( 'body' ).attr( 'src', alpha_vars.assets_url + '/vendor/jquery.magnific-popup/jquery.magnific-popup.min.js' ).on( 'load', function() {
					theme.popup( {
						type: 'image',
						items: {
							src: $this.attr( 'href' ),
						},
					} );
				} );
			} else {
				theme.popup( {
					type: 'image',
					items: {
						src: $this.attr( 'href' ),
					},
				} );
			}
		} );

		// Close mangific popup by mousedown on outside of the content
		function closePopupByClickBg( e ) {
			var $this = $( e.target );
			if ( $this.closest( '.mfp-gallery' ).length ) {
				return;
			}
			if ( !$this.closest( '.mfp-content' ).length || $this.hasClass( 'mfp-content' ) ) {
				if ( $.fn.magnificPopup ) {
					$.magnificPopup.instance.close();
				}
			}
		}

		theme.$body.on( 'mousedown', '.mfp-wrap', closePopupByClickBg );
		if ( 'ontouchstart' in document ) {
			document.body.addEventListener( 'touchstart', closePopupByClickBg, { passive: true } );
		}

		/**
		 * Open first popup
		 * 
		 * @since 1.0
		 */
		function openFirstPopup( $this ) {
			var options = theme.parseOptions( $this.attr( 'data-popup-options' ) );
			setTimeout( function() {
				if ( theme.getCookie( 'hideNewsletterPopup' ) ) {
					return;
				}
				$this.imagesLoaded( function() {
					theme.popup( {
						mainClass: 'mfp-fade mfp-alpha mfp-alpha-' + options.popup_id,
						items: {
							src: $this.get( 0 )
						},
						callbacks: {
							open: function() {
								this.content.css( { 'animation-duration': options.popup_duration, 'animation-timing-function': 'linear' } );
								this.content.addClass( options.popup_animation + ' animated' );

								$( '#alpha-popup-' + options.popup_id ).css( 'display', '' );
							}
						}
					}, 'firstpopup' );
				} );
			}, 1000 * options.popup_delay );
		}

		// Open first popup
		$( 'body > .popup, .side-header .page-wrapper > .popup' ).each( function( e ) {
			var $this = $( this );
			if ( $this.attr( 'data-popup-options' ) ) {
				openFirstPopup( $this );
			}
		} );

		// Popup on click event
		theme.$body.on( 'click', 'a[href^="#' + alpha_vars.theme + '-action"], .show-popup', function( e ) {

			e.preventDefault();

			if ( !$( this ).hasClass( 'show-popup' ) ) {
				var $href = this.getAttribute( 'href' ),
					$arrayOfString = $href.split( ':' );

				// unselected any popup 
				if ( $arrayOfString[1].length < 10 ) {
					return;
				}

				this.classList.add( $arrayOfString[1] );
				this.classList.add( 'show-popup' );
			}

			var id = -1;
			for ( var className of this.classList ) {
				className && className.startsWith( 'popup-id-' ) && ( id = className.substr( 9 ) );
			}

			theme.popup( {
				mainClass: 'mfp-alpha mfp-alpha-' + id,
				ajax: {
					settings: {
						data: {
							action: 'alpha_print_popup',
							nonce: alpha_vars.nonce,
							popup_id: id
						}
					}
				},
				callbacks: {
					afterChange: function() {
						this.container.html( '<div class="mfp-content"></div><div class="mfp-preloader"><div class="popup-template"><div class="d-loading"><i></i></div></div></div>' );
						this.contentContainer = this.container.children( '.mfp-content' );
						this.preloader = false;
					},
					beforeClose: function() {
						this.container.empty();
					},
					ajaxContentAdded: function() {
						var self = this,
							$popupContainer = this.container.find( '.popup' ),
							options = JSON.parse( $popupContainer.attr( 'data-popup-options' ) );

						self.contentContainer.next( '.mfp-preloader' ).css( 'max-width', $popupContainer.css( 'max-width' ) ).find( '.d-loading' ).remove();
						setTimeout( function() {
							self.contentContainer.next( '.mfp-preloader' ).remove();
						}, 10000 );

						this.container.css( { 'animation-duration': options.popup_duration, 'animation-timing-function': 'linear' } );
						this.container.addClass( options.popup_animation + ' animated' );
						$( '#alpha-popup-' + id ).css( 'display', '' );
						if ( this.container.find( '.wpcf7' ).length ) {
							var $wpcf7 = this.container.find( '.wpcf7 .wpcf7-form' );
							if ( typeof wpcf7 == 'object' ) {
								if ( typeof wpcf7.initForm == 'function' ) {
									wpcf7.initForm( $wpcf7 );
								} else if ( typeof wpcf7.init == 'function' ) {
									wpcf7.init( $wpcf7.get(0) );
									$wpcf7.get(0).classList.replace("no-js", "js")
								}
							}
						}
						if ( this.container.find( '.wpforms-container' ).length ) {
							if ( typeof wpforms == 'object' && typeof wpforms.init == 'function' ) {
								wpforms.init();
							}
						}
					}
				}
			}, 'popup_template' );
		} )
	}

	/**
	 * Initialize scroll to top button
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	theme.initScrollTopButton = function() {
		// register scroll top button
		var domScrollTop = theme.byId( 'scroll-top' );
		if ( domScrollTop ) {
			theme.$body.on( 'click', '#scroll-top', function( e ) {
				theme.scrollTo( 0 );
				e.preventDefault();
			} )

			function _refreshScrollTop() {
				if ( window.pageYOffset > 200 ) { // issue: heavy js performance, 8.3ms
					domScrollTop.classList.add( 'show' );

					// Show scroll position percent in scroll top button
					var d_height = $( document ).height(),
						w_height = $( window ).height(),
						c_scroll_pos = $( window ).scrollTop();

					var perc = c_scroll_pos / ( d_height - w_height ) * 214;

					if ( $( '#progress-indicator' ).length > 0 ) {
						$( '#progress-indicator' ).css( 'stroke-dasharray', perc + ', 400' );
					}
				} else {
					domScrollTop.classList.remove( 'show' );
				}
			}

			theme.call( _refreshScrollTop, 500 );
			window.addEventListener( 'scroll', _refreshScrollTop, { passive: true } );
		}
	}

	/**
	 * Initialize scroll to.
	 *
	 * @since 1.0
	 * @return {void}
	 */
	theme.initScrollTo = function() {

		// Scroll to hash target
		setTimeout( function() {
			theme.scrollTo( theme.hash );
		}, 200 )

		if ( typeof elementorFrontend == 'object' ) {
			elementorFrontend.utils.anchors.setSettings( 'scrollDuration', 0 );
		}

		// Scroll to target by click button.
		theme.$body.on( 'click', '.scroll-to', function( e ) {
			var target = $( this ).attr( 'href' ).replace( location.origin + location.pathname, '' );
			if ( target.startsWith( '#' ) && target.length > 1 ) {
				e.preventDefault();
				theme.scrollTo( target );
			}
		} )
	}

	/**
	 * Initialize contact forms.
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	theme.initContactForms = function() {
		$( '.wpcf7-form [aria-required="true"]' ).prop( 'required', true );
	}


	/**
	 * Initialize search form.
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	theme.initSearchForm = function() {
		var $search = $( '.search-wrapper' );
		$search.each( function() {
			var $el = $( this );
			// full screen search
			if ( $el.hasClass( 'hs-fullscreen' ) && $el.length ) {
				var headerHeight = $( 'header' ).height();
				$el.find( '.search-form-wrapper' ).css( 'min-height', headerHeight + 60 );
				$el.find( '.scrollable' ).css( 'max-height', 'calc(100vh - 350px)' );
				$el.on( 'click', '.search-toggle', function( e ) {
					$( 'body' ).css( 'overflow', 'hidden' );
					$( 'body' ).css( 'margin-right', ( window.innerWidth - document.body.clientWidth ) + 'px' );
				} );
			}
			// overlap 
			if ( $el.hasClass( 'hs-overlap' ) ) {
				$el.on( 'click', '.hs-close', function( e ) {
					e.preventDefault();
					$( this ).closest( '.search-wrapper' ).removeClass( 'show' );
				} );
			}
			if ( $el.hasClass( 'hs-dropdown' ) ) {
				$el.find( '.form-control' ).on( 'focusin', function( e ) {
					$( this ).closest( '.hs-toggle' ).addClass( 'show' );
				} ).on( 'focusout', function( e ) {
					$( this ).closest( '.hs-toggle' ).removeClass( 'show' );
				} );
			}
		} );
		theme.$body.on( 'click', '.hs-toggle .search-toggle', function( e ) {
			e.preventDefault();
			$search = $( this ).closest( '.hs-toggle' );
			if ( !$search.hasClass( 'hs-dropdown' ) ) {
				$search.toggleClass( 'show' );
			}
		} );
		$( window ).on( 'resize', function() {
			$( 'body' ).css( 'overflow', '' );
			$( 'body' ).css( 'margin-right', '' );
		} );
	}

	/**
	 * Compatibility with Elementor
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	theme.initElementor = function() {
		if ( 'undefined' != typeof elementorFrontend ) {
			// Compatibility with Elementor Counter Widget
			elementorFrontend.waypoint( $( '.elementor-counter-number' ), function() {
				var $this = $( this ),
					data = $this.data(),
					decimalDigits = data.toValue.toString().match( /\.(.*)/ );

				if ( decimalDigits ) {
					data.rounding = decimalDigits[1].length;
				}

				$this.numerator( data );
			} );
		}
	}


	/**
	 * Compatibility with Vendor plugins
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	theme.initVendorCompatibility = function() {

		// Dokan / 
		theme.$body.on( 'keydown', '.store-search-input', function( e ) {
			if ( e.keyCode == 13 ) {
				setTimeout( function() {
					$( '#dokan-store-listing-filter-form-wrap #apply-filter-btn' ).trigger( 'click' );
				}, 150 );
			}
		} );

		// WC Marketplace
		theme.$body
			.on( 'click', '.wcmp-report-abouse-wrapper .close', function( e ) {
				$( ".wcmp-report-abouse-wrapper #report_abuse_form_custom" ).fadeOut( 100 );
			} )
			.on( 'click', '.wcmp-report-abouse-wrapper #report_abuse', function( e ) {
				$( ".wcmp-report-abouse-wrapper #report_abuse_form_custom" ).fadeIn( 100 );
			} );

		$( 'select#rating' ).prev( 'p.stars' ).prevAll( 'p.stars' ).remove();

		// Single product / summary / "more products" button
		theme.$body.on( 'click', '.goto_more_offer_tab', function( e ) {
			e.preventDefault();
			if ( !$( '.singleproductmultivendor_tab' ).hasClass( 'active' ) ) {
				$( '.singleproductmultivendor_tab a, #tab_singleproductmultivendor' ).trigger( 'click' );
			}
			if ( $( '.woocommerce-tabs' ).length > 0 ) {
				$( 'html, body' ).animate( {
					scrollTop: $( ".woocommerce-tabs" ).offset().top - 120
				}, 1500 );
			}
		} );
	}

	/**
	 * Initialize floating elements
	 * 
	 * @since 1.0
	 * @param {string|jQuery} selector
	 * @return {void}
	 */
	theme.initFloatingElements = function( selector ) {
		if ( $.fn.parallax ) {
			var $selectors = '';

			if ( selector ) {
				$selectors = selector;
			} else {
				$selectors = $( '[data-plugin="floating"]' );
			}

			$selectors.each( function( e ) {
				var $this = $( this );
				if ( $this.data( 'parallax' ) ) {
					$this.parallax( 'disable' );
					$this.removeData( 'parallax' );
					$this.removeData( 'options' );
				}
				if ( $this.hasClass( 'elementor-element' ) ) {
					if ( $this.hasClass( 'elementor-column' ) ) {
						$this.children( '.elementor-widget-wrap' ).addClass( 'layer' ).attr( 'data-depth', $this.attr( 'data-floating-depth' ) );
					} else {
						$this.children( '.elementor-widget-container, .elementor-container' ).addClass( 'layer' ).attr( 'data-depth', $this.attr( 'data-floating-depth' ) );
					}

				} else {
					$this.children( '.layer' ).attr( 'data-depth', $this.attr( 'data-floating-depth' ) );
				}
				$this.parallax( $this.data( 'options' ) );
			} );
		}
	}

	/**
	 * Initialize advanced motions
	 *
	 * @since 1.0
	 * @param {string} selector
	 * @param {string} action
	 * @return {void}
	 */
	theme.initAdvancedMotions = function( selector, action ) {
		if ( theme.isMobile ) {
			return;
		}

		if ( typeof skrollr == 'undefined' ) {
			return;
		}

		var $selectors = '';

		if ( selector ) {
			$selectors = selector;
		} else {
			$selectors = $( '[data-plugin="skrollr"], .parallax' );
		}

		$selectors.removeAttr( 'data-bottom-top data-top data-center-top data-center' ).css( {} );
		if ( $selectors.length && skrollr.get() ) {
			skrollr.get().destroy();
		}

		if ( action == 'destroy' ) {
			$selectors.removeAttr( 'data-plugin data-options' );
			return;
		}

		$selectors.each( function( e ) {
			var $this = $( this ),
				options = {},
				keys = [];

			if ( $( this ).attr( 'data-options' ) ) {
				options = JSON.parse( $( this ).attr( 'data-options' ) );
				keys = Object.keys( options );
			}

			if ( 'object' == typeof options && ( keys = Object.keys( options ) ).length ) {
				keys.forEach( function( key ) {
					$this.attr( key, options[key] );
				} )
			}
		} );

		if ( $selectors.length ) {
			skrollr.init( { forceHeight: false, smoothScrolling: true } );
		}
	}

	/**
	 * Initialize video player
	 * 
	 * @since 1.0
	 * @param selector 
	 * @return {void}
	 */
	theme.initVideoPlayer = function( selector ) {
		if ( typeof selector == 'undefined' ) {
			selector = '.btn-video-player';
		}
		theme.$( selector ).on( 'click', function( e ) {
			var video_banner = $( this ).closest( '.video-banner' );
			if ( video_banner.length && video_banner.find( 'video' ).length ) {
				var video = video_banner.find( 'video' );
				video = video[0];

				if ( video_banner.hasClass( 'playing' ) ) {
					video_banner.removeClass( 'playing' ).addClass( 'paused' );
					video.pause();
				} else {
					video_banner.removeClass( 'paused' ).addClass( 'playing' );
					video.play();
				}
			}

			if ( video_banner.find( '.parallax-background' ).length > 0 ) {
				video_banner.find( '.parallax-background' ).css( 'z-index', '-1' );
			}
			e.preventDefault();
		} )
		theme.$( selector ).closest( '.video-banner' ).find( 'video' ).on( 'playing', function() {
			$( this ).closest( '.video-banner' ).removeClass( 'paused' ).addClass( 'playing' );
		} )
		theme.$( selector ).closest( '.video-banner' ).find( 'video' ).on( 'ended', function() {
			$( this ).closest( '.video-banner' ).removeClass( 'playing' ).addClass( 'paused' );
		} )
	}

	/**
	 * Menu Class
	 *
	 * @class Menu
	 * @since 1.0
	 * @return {Object} Menu
	 */
	theme.menu = ( function() {

		function _showMobileMenu( e, callback ) {
			var $mmenuContainer = $( '.mobile-menu-wrapper .mobile-menu-container' );
			theme.$body.addClass( 'mmenu-active' );
			e.preventDefault();

			function initMobileMenu() {
				theme.liveSearch && theme.liveSearch( '', $( '.mobile-menu-wrapper .search-wrapper' ) );
				theme.menu.addToggleButtons( '.mobile-menu li' );
			}

			if ( !$mmenuContainer.find( '.mobile-menu' ).length ) {
				var cache = theme.getCache( cache );

				// check cached mobile menu.
				if ( cache.mobileMenu && cache.mobileMenuLastTime && alpha_vars.menu_last_time &&
					parseInt( cache.mobileMenuLastTime ) >= parseInt( alpha_vars.menu_last_time ) ) {

					// fetch mobile menu from cache
					$mmenuContainer.append( cache.mobileMenu );
					initMobileMenu();
					theme.setCurrentMenuItems( '.mobile-menu-wrapper' );
				} else {
					// fetch mobile menu from server
					theme.doLoading( $mmenuContainer );
					$.post( alpha_vars.ajax_url, {
						action: "alpha_load_mobile_menu",
						nonce: alpha_vars.nonce,
						load_mobile_menu: true,
					}, function( result ) {
						result && ( result = result.replace( /(class=".*)current_page_parent\s*(.*")/, '$1$2' ) );
						$mmenuContainer.css( 'height', '' );
						theme.endLoading( $mmenuContainer );

						// Add mobile menu search
						$mmenuContainer.append( result );
						initMobileMenu();
						theme.setCurrentMenuItems( '.mobile-menu-wrapper' );

						// save mobile menu cache
						cache.mobileMenuLastTime = alpha_vars.menu_last_time;
						cache.mobileMenu = result;
						theme.setCache( cache );

						if ( typeof callback == 'function' ) {
							callback();
						}
					} );
				}
			} else {
				initMobileMenu();

				if ( typeof callback == 'function' ) {
					callback();
				}
			}
		}

		function _hideMobileMenu( e ) {
			e.preventDefault();
			theme.$body.removeClass( 'mmenu-active' );
		}

		var _initMegaMenu = function() {
			// calc megamenu position
			function _recalcMenuPosition() {
				$( 'nav .menu.horizontal-menu .megamenu' ).each( function() {
					var $this = $( this ),
						o = $this.offset(),
						left = o.left - parseInt( $this.css( 'margin-left' ) ),
						outerWidth = $this.outerWidth(),
						offsetLeft = ( left + outerWidth ) - ( window.innerWidth - 20 );

					if ( $this.hasClass( 'full-megamenu' ) && 0 == $this.closest( '.container-fluid' ).length ) {
						$this.css( "margin-left", ( $( window ).width() - outerWidth ) / 2 - left + 'px' );
					} else if ( offsetLeft > 0 && left > 20 ) {
						$this.css( "margin-left", -offsetLeft + 'px' );
					}

					$this.parent().addClass( 'loaded' );
				} );
			}

			if ( $( '.toggle-menu.dropdown' ).length ) {
				var $togglebtn = $( '.toggle-menu.dropdown .vertical-menu' );
				var toggleBtnTop = $togglebtn.length > 0 && $togglebtn.offset().top,
					verticalTop = toggleBtnTop;

				$( '.vertical-menu .menu-item-has-children' ).on( 'mouseenter', function( e ) {
					var $this = $( this );
					if ( $this.children( '.megamenu' ).length ) {
						var $item = $this.children( '.megamenu' ),
							offset = $item.offset(),
							top = offset.top - parseInt( $item.css( 'margin-top' ) ),
							outerHeight = $item.outerHeight();

						if ( window.pageYOffset > toggleBtnTop ) {
							verticalTop = $this.closest( '.menu' ).offset().top;
						} else {
							verticalTop = toggleBtnTop;
						};

						if ( typeof ( verticalTop ) !== 'undefined' && top >= verticalTop ) {
							var offsetTop = ( top + outerHeight ) - window.innerHeight - window.pageYOffset;
							if ( offsetTop <= 0 ) {
								$item.css( "margin-top", "0px" );
							} else if ( offsetTop < top - verticalTop ) {
								$item.css( "margin-top", -( offsetTop + 5 ) + 'px' );
							} else {
								$item.css( "margin-top", -( top - verticalTop ) + 'px' )
							}
						}
					}
				}
				);
			}

			_recalcMenuPosition();
			theme.$window.on( 'resize recalc_menus', _recalcMenuPosition );
		}

		return {
			init: function() {
				this.initMenu();
				this.initMobileMenu();
				this.initFilterMenu();
				this.initCollapsibleWidget();
				this.initCollapsibleWidgetToggle();
				this.initFlyoutMenu();
			},
			initMenu: function( $selector ) {
				if ( typeof $selector == 'undefined' ) {
					$selector = '';
				}

				theme.$body
					// no link
					.on( 'click', $selector + ' .menu-item .nolink', theme.preventDefault )

				this.addToggleButtons( $selector + ' .collapsible-menu li' );

				// toggle dropdown
				theme.$body.on( "click", '.dropdown-menu-toggle', theme.preventDefault );

				// megamenu
				_initMegaMenu();

				// lazyload menu image
				alpha_vars.lazyload && theme.call( function() {
					$( '.megamenu [data-lazy]' ).each( function() {
						theme._lazyload_force( this );
					} )
				} );

				// simple menu
				$( 'nav .menu ul:not(.megamenu)' ).parent().addClass( 'loaded' );
			},
			initMobileMenu: function( $selector ) {
				theme.$body
					// mobile menu
					.on( 'click', '.mobile-menu-toggle', _showMobileMenu )
					.on( 'click', '.mobile-menu-overlay', _hideMobileMenu )
					.on( 'click', '.mobile-menu-close', _hideMobileMenu )
					.on( 'click', '.mobile-item-categories.show-categories-menu', function( e ) {
						_showMobileMenu( e, function() {
							$( '.mobile-menu-container .nav a[href="#categories"]' ).trigger( 'click' );
						} );
					} )

				window.addEventListener( 'resize', _hideMobileMenu, { passive: true } );
			},
			addToggleButtons: function( selector ) {
				theme.$( selector ).each( function() {
					var $this = $( this );
					if ( $this.hasClass( 'menu-item-has-children' ) && !$this.children( 'a' ).children( '.toggle-btn' ).length && $this.children( 'ul' ).text().trim() ) {
						$this.children( 'a' ).each( function() {
							if ( $( this ).parent().parent( '.megamenu' ).length ) {
								if ( !this.classList.contains( 'menu-item-has-children' ) ) {
									return;
								}
							}
							var span = document.createElement( 'span' );
							span.className = "toggle-btn";
							this.append( span );
						} )
					}
				} );
			},
			initFilterMenu: function() {
				theme.$body.on( 'click', '.with-ul > a i, .menu .toggle-btn, .mobile-menu .toggle-btn', function( e ) {
					var $this = $( this );
					var $ul = $this.parent().siblings( ':not(.count)' );
					if ( $ul.length > 1 ) {
						$this.parent().toggleClass( "show" ).next( ':not(.count)' ).slideToggle( 300 );
					} else if ( $ul.length > 0 ) {
						$ul.slideToggle( 300 ).parent().toggleClass( "show" );
					}
					setTimeout( function() {
						$this.closest( '.sticky-sidebar' ).trigger( 'recalc.pin' );
					}, 320 );
					e.preventDefault();
				} );
			},
			initCollapsibleWidgetToggle: function( selector ) {
				$( '.widget .product-categories li' ).add( '.sidebar .widget.widget_categories li' ).add( '.widget .product-brands li' ).add( '.store-cat-stack-dokan li' ).each( function() { // updated(47(
					if ( this.lastElementChild && this.lastElementChild.tagName === 'UL' ) {
						var i = document.createElement( 'i' );
						i.className = alpha_vars.theme_icon_prefix + "-icon-angle-down";
						this.classList.add( 'with-ul' );
						this.classList.add( 'cat-item' );
						this.firstElementChild.appendChild( i );
					}
				} );

				theme.$( 'undefined' == typeof selector ? '.sidebar .widget-collapsible .widget-title' : selector )
					.each( function() {
						var $this = $( this );
						if ( $this.closest( '.top-filter-widgets' ).length ||
							$this.closest( '.toolbox-horizontal' ).length ||  // if in shop pages's top-filter sidebar
							$this.siblings( '.slider-wrapper' ).length ) {
							return;
						}
						// generate toggle icon
						if ( !$this.children( '.toggle-btn' ).length ) {
							var span = document.createElement( 'span' );
							span.className = 'toggle-btn';
							this.appendChild( span );
						}
					} );
			},
			initCollapsibleWidget: function() {
				// slideToggle
				theme.$body.on( 'click', '.sidebar .widget-collapsible .widget-title', function( e ) {
					var $this = $( e.currentTarget );

					if ( $this.closest( '.top-filter-widgets' ).length ||
						$this.closest( '.toolbox-horizontal' ).length ||  // if in shop pages's top-filter sidebar
						$this.siblings( '.slider-wrapper' ).length ||
						$this.hasClass( 'sliding' ) ) {
						return;
					}
					var $content = $this.siblings( '*:not(script):not(style)' );
					$this.hasClass( "collapsed" ) || $content.css( 'display', 'block' );
					$this.addClass( "sliding" );
					$content.slideToggle( 300, function() {
						$this.removeClass( "sliding" );
						theme.$window.trigger( 'update_lazyload' );
						$( '.sticky-sidebar' ).trigger( 'recalc.pin' );
					} );
					$this.toggleClass( "collapsed" );
				} );
			},
			initFlyoutMenu: function() {
				theme.$body.on( 'click', '.flyout-menu-container .dropdown-menu-toggle, .flyout-menu-container .flyout-close', function( e ) {
					e.preventDefault();
					$( this ).parent().toggleClass( 'show' );

					if ( !$( this ).parent().hasClass( 'show' ) ) {
						$( this ).parent().find( '.menu ul' ).slideUp();
					}
				} );

				document.addEventListener( 'keydown', function( e ) {
					var keyName = e.key;
					if ( 'Escape' == keyName ) {
						if ( $('.flyout-menu-container').hasClass( 'show' ) ) {
							$('.flyout-menu-container').removeClass( 'show' );
						}
					}
				} );

				theme.menu.addToggleButtons( '.flyout-menu-container li' );
			}
		}
	} )();

	/**
	 * Open magnific popup
	 *
	 * @since 1.0
	 * @param {Object} options
	 * @param {string} preset
	 * @return {void}
	 */
	theme.popup = function( options, preset ) {
		var mpInstance = $.magnificPopup.instance;
		// if something is already opened, retry after 5seconds
		if ( mpInstance.isOpen ) {
			if ( mpInstance.content ) {
				setTimeout( function() {
					theme.popup( options, preset );
				}, 5000 );
			} else {
				$.magnificPopup.close();
			}
		} else {
			// if nothing is opened, open new
			$.magnificPopup.open(
				$.extend( true, {},
					theme.defaults.popup,
					preset ? theme.defaults.popupPresets[preset] : {},
					options
				)
			);
		}
	}

	/**
	 * Create minipopup object
	 * 
	 * @class Minipopup
	 * @since 1.0
	 * @return {Object} Minipopup
	 */
	theme.minipopup = ( function() {
		var timerInterval = 200;
		var $area;
		var boxes = [];
		var timers = [];
		var isPaused = false;
		var timerId = false;
		var timerClock = function() {
			if ( isPaused ) {
				return;
			}
			for ( var i = 0; i < timers.length; ++i ) {
				( timers[i] -= timerInterval ) <= 0 && this.close( i-- );
			}
		}

		return {
			init: function() {
				// init area
				var area = document.createElement( 'div' );
				area.className = "minipopup-area";
				$( theme.byClass( 'page-wrapper' ) ).append( area );

				$area = $( area );

				// call methods
				this.close = this.close.bind( this );
				timerClock = timerClock.bind( this );
			},
			open: function( options, callback ) {
				var self = this,
					settings = $.extend( true, {}, theme.defaults.minipopup, options ),
					$box;

				$box = $( settings.content );

				// open
				$box.find( "img" ).on( 'load', function() {
					setTimeout( function() {
						$box.addClass( 'show' );
					}, 300 );
					if ( $box.offset().top - window.pageYOffset < 0 ) {
						self.close();
					}
					$box.on( 'mouseenter', function() {
						self.pause();
					} );
					$box.on( 'mouseleave', function( e ) {
						self.resume();
					} );

					$box[0].addEventListener( 'touchstart', function( e ) {
						self.pause();
						e.stopPropagation();
					}, { passive: true } );

					theme.$body[0].addEventListener( 'touchstart', function() {
						self.resume();
					}, { passive: true } );

					$box.on( 'mousedown', function() {
						$box.css( 'transform', 'translateX(0) scale(0.96)' );
					} );
					$box.on( 'mousedown', 'a', function( e ) {
						e.stopPropagation();
					} );
					$box.on( 'mouseup', function() {
						self.close( boxes.indexOf( $box ) );
					} );
					$box.on( 'mouseup', 'a', function( e ) {
						e.stopPropagation();
					} );

					boxes.push( $box );
					timers.push( settings.delay );

					( timers.length > 1 ) || (
						timerId = setInterval( timerClock, timerInterval )
					);

					callback && callback( $box );
				} ).on( 'error', function() {
					$box.remove();
				} );
				$box.appendTo( $area );
			},
			close: function( indexToClose ) {
				var self = this;
				var index = ( 'undefined' === typeof indexToClose ) ? 0 : indexToClose;
				var $box = boxes.splice( index, 1 )[0];

				if ( $box ) {
					// remove timer
					timers.splice( index, 1 )[0];

					// remove box
					$box.css( 'transform', '' ).removeClass( 'show' );
					self.pause();

					setTimeout( function() {
						var $next = $box.next();
						if ( $next.length ) {
							$next.animate( {
								'margin-bottom': -1 * $box[0].offsetHeight - 20
							}, 300, 'easeOutQuint', function() {
								$next.css( 'margin-bottom', '' );
								$box.remove();
							} );
						} else {
							$box.remove();
						}
						self.resume();
					}, 300 );

					// clear timer
					boxes.length || clearTimeout( timerId );
				}
			},
			pause: function() {
				isPaused = true;
			},
			resume: function() {
				isPaused = false;
			}
		}
	} )();

	/**
	 * Initialize account
	 * @since 1.0
	 * @return {void}
	 */
	theme.initAccount = function() {
		/**
		 * Launch login form popup for both login and register buttons
		 * 
		 * @since 1.0
		 */
		function launchPopup( e ) {
			if ( this.classList.contains( 'logout' ) ) {
				return;
			}

			e.preventDefault();

			var isRegister = this.classList.contains( 'register' );
			theme.popup( {
				mainClass: "mfp-login mfp-fade" + ( this.classList.contains( 'offcanvas' ) ? ' offcanvas-type' : '' ),
				callbacks: {
					afterChange: function() {
						this.container.html( '<div class="mfp-content"></div><div class="mfp-preloader"><div class="login-popup"><div class="d-loading"><i></i></div></div></div>' );
						this.contentContainer = this.container.children( '.mfp-content' );
						this.preloader = false;
					},
					beforeClose: function() {
						this.container.empty();
					},
					ajaxContentAdded: function() {
						var self = this;
						if ( isRegister ) {
							this.wrap.find( '[href="signup"]' ).trigger( 'click' );
						}
						setTimeout( function() {
							self.contentContainer.next( '.mfp-preloader' ).remove();
						}, 200 );
					}
				}
			}, 'login' );
		}

		/**
		 * Check if user input validation
		 *
		 * @since 1.0
		 */
		function checkValidation( e ) {
			var $form = $( this ), isLogin = $form[0].classList.contains( 'login' );
			$form.find( 'p.submit-status' ).show().text( 'Please wait...' ).addClass( 'loading' );
			$form.find( 'button[type=submit]' ).attr( 'disabled', 'disabled' );
			$.ajax( {
				type: 'POST',
				dataType: 'json',
				url: alpha_vars.ajax_url,
				data: $form.serialize() + '&action=alpha_account_' + ( isLogin ? 'signin' : 'signup' ) + '_validate',
				success: function( data ) {
					$form.find( 'p.submit-status' ).html( data.message.replace( '/<script.*?\/script>/s', '' ) ).removeClass( 'loading' );
					$form.find( 'button[type=submit]' ).removeAttr( 'disabled' );
					if ( data.loggedin === true ) {
						location.reload();
					}
				}
			} );
			e.preventDefault();
		}

		theme.$body
			.on( 'click', '.header .account > a:not(.logout), .header .account > .links > a:not(.logout)', launchPopup )
			.on( 'submit', '#customer_login form', checkValidation )
	}

	/**
	 * Initialize keydown events
	 * @since 1.0
	 * @return {void}
	 */
	theme.initKeyDown = function() {
		var $fullSearchElement = $( '.hs-fullscreen' ),
			$offCanvasElement = $( '.offcanvas-type' );

		if ( $fullSearchElement.length || $offCanvasElement.length ) {
			document.
				addEventListener( 'keydown', function( e ) {
					var keyName = e.key;
					if ( 'Escape' == keyName ) {
						if ( $offCanvasElement.hasClass( 'opened' ) ) {
							$offCanvasElement.removeClass( 'opened' );
						}
						if ( $fullSearchElement.length ) {
							$fullSearchElement.find( '.hs-close' ).trigger( 'click' );
						}
					}
				} );
		}

		theme.$body.on( 'keydown', '.search-wrapper .form-control', function( e ) {
			if ( e.keyCode == 13 ) {
				var $this = $( this );
				setTimeout( function() {
					$this.closest( 'form' ).find( '.btn' ).trigger( 'click' );
				}, 150 );
			}
		} );
	}

	/**
	 * Create slider object by using swiper js
	 * 
	 * @class Slider
	 * @since 1.0
	 */
	theme.slider = ( function() {

		function Slider( $el, options ) {
			return this.init( $el, options );
		}

		function onInitialized() {
			var $wrapper = $( this.slider.wrapperEl );
			var slider = this.slider;

			$wrapper.trigger( 'initialized.slider', slider );
			$wrapper.find( '.slider-slide:not(.slider-slide-active) .appear-animate' ).removeClass( 'appear-animate' ); // Prevent appear animation of inactive slides

			// Video
			$wrapper.find( 'video' )
				.removeAttr( 'style' )
				.on( 'ended', function() {
					var $this = $( this );
					if ( $this.closest( '.slider-slide' ).hasClass( 'slider-slide-active' ) ) {

						if ( true === slider.params.autoplay.enabled ) {
							if ( slider.params.loop && slider.slides.length === slider.activeIndex ) {
								this.loop = true;
								try {
									this.play();
								} catch ( e ) { }
							}
							slider.slideNext();
							slider.autoplay.start();
						} else {
							this.loop = true;
							try {
								this.play();
							} catch ( e ) { }
						}
					}
				} );

			sliderLazyload.call( this );
		}

		function onTranslated() {
			$( window ).trigger( 'appear.check' );

			var $wrapper = $( this.slider.wrapperEl );
			var slider = this.slider;

			// Video Play
			var $activeVideos = $wrapper.find( '.slider-slide-active video' );
			$wrapper.find( '.slider-slide:not(.slider-slide-active) video' ).each( function() {
				if ( !this.paused ) {
					slider.autoplay.start();
				}
				this.pause();
				this.currentTime = 0;
			} );

			if ( $activeVideos.length ) {
				var slider = $wrapper.data( 'slider' );
				if ( slider && slider.params && slider.params.autoplay.enabled ) {
					slider.autoplay.stop();
				}
				$activeVideos.each( function() {
					try {
						if ( this.paused ) {
							this.play();
						}
					} catch ( e ) { }
				} );
			}

			sliderLazyload.call( this );
		}

		function onSliderInitialized() {
			var self = this,
				$el = $( this.slider.wrapperEl );

			// carousel content animation
			$el.find( '.slider-slide-active .slide-animate' ).each( function() {
				var $animation_item = $( this ),
					settings = 'undefined' != typeof $animation_item.data( 'settings' ) ? $animation_item.data( 'settings' ) : {},
					duration,
					delay = settings._animation_delay ? settings._animation_delay : 0,
					aniName = settings._animation;

				if ( $animation_item.hasClass( 'animated-slow' ) ) {
					duration = 2000;
				} else if ( $animation_item.hasClass( 'animated-fast' ) ) {
					duration = 750;
				} else {
					duration = 1000;
				}

				$animation_item.css( 'animation-duration', duration + 'ms' );

				duration = duration ? duration : 750;

				// var temp = theme.requestTimeout( function () {
				// 	$animation_item.addClass( aniName );
				// 	$animation_item.addClass( 'show-content' );
				// 	self.timers.splice( self.timers.indexOf( temp ), 1 )
				// }, ( delay ? delay : 0 ) );
			} );

			var $activeSlider = $( this.slider.slides ).slice( this.slider.activeIndex, this.slider.activeIndex + this.slider.params.slidesPerView ),
				$slideAnimations = $activeSlider.find( '.slide-animate' );
			$el.find( '.slider-slide .slide-animate' ).each( function() {
				if ( $slideAnimations.index( $( this ) ) >= 0 ) return;

				var $animation_item = $( this ),
					settings = 'undefined' != typeof $animation_item.data( 'settings' ) ? $animation_item.data( 'settings' ) : {},
					duration,
					delay = settings._animation_delay ? settings._animation_delay : 0;

				if ( $animation_item.hasClass( 'animated-slow' ) ) {
					duration = 2000;
				} else if ( $animation_item.hasClass( 'animated-fast' ) ) {
					duration = 750;
				} else {
					duration = 1000;
				}

				$animation_item.css( 'animation-duration', duration + 'ms' );

				duration = duration ? duration : 750;

				var temp = theme.requestTimeout( function() {
					$animation_item.removeClass( settings._animation + ' animated' );
					self.timers.splice( self.timers.indexOf( temp ), 1 )
				}, delay + duration );
			} );

		}

		function sliderLazyload() {
			if ( $.fn.lazyload ) {
				$( this.slider.wrapperEl ).find( '[data-lazy]' )
					.filter( function() {
						return !$( this ).data( '_lazyload_init' );
					} )
					.data( '_lazyload_init', 1 )
					.each( function() {
						$( this ).lazyload( theme.defaults.lazyload );
					} );
			}
		}

		function onSliderResized() {
			$( this.slider.wrapperEl ).find( '.slider-slide-active .slide-animate' ).each( function() {
				$( this )
					.addClass( 'show-content' )
					.css( {
						'animation-name': '',
						'animation-duration': '',
						'animation-delay': '',
					} );
			} );
		}

		function onSliderTranslate() {
			var self = this,
				$el = $( this.slider.wrapperEl ),
				$activeSlides = $( this.slider.slides ).slice( this.slider.activeIndex, this.slider.activeIndex + this.slider.params.slidesPerView );
			self.translateFlag = 1;
			self.prev = self.next;
			$el.find( '.slider-slide .slide-animate' ).each( function() {
				var $animation_item = $( this ),
					$slide = $animation_item.closest( '.slider-slide' ),
					settings = 'undefined' != typeof $animation_item.data( 'settings' ) ? $animation_item.data( 'settings' ) : {};

				if ( $activeSlides.index( $slide ) < 0 && settings ) {
					$animation_item.removeClass( 'animated' );
				}
			} );
		}

		function onSliderTranslated() {
			var self = this,
				$el = $( this.slider.wrapperEl );
			if ( 1 != self.translateFlag ) {
				return;
			}

			$el.find( '.slider-slide .show-content' ).removeClass( 'show-content' );

			var $activeSlider = $( this.slider.slides ).slice( this.slider.activeIndex, this.slider.activeIndex + this.slider.params.slidesPerView ),
				$slideAnimations = $activeSlider.find( '.slide-animate' );

			self.next = this.slider.activeIndex;
			if ( self.prev != self.next ) {
				$el.find( '.slider-slide .show-content' ).removeClass( 'show-content' );

				/* clear all animations that are running. */
				if ( $el.hasClass( "animation-slider" ) ) {
					for ( var i = 0; i < self.timers.length; i++ ) {
						theme.deleteTimeout( self.timers[i] );
					}
					self.timers = [];
				}

				$slideAnimations.each( function() {
					var $animation_item = $( this ),
						settings = 'undefined' != typeof $animation_item.data( 'settings' ) ? $animation_item.data( 'settings' ) : {},
						duration,
						delay = settings._animation_delay ? settings._animation_delay : 0,
						aniName = settings._animation;

					if ( $animation_item.hasClass( 'animated' ) ) return;

					if ( $animation_item.hasClass( 'animated-slow' ) ) {
						duration = 2000;
					} else if ( $animation_item.hasClass( 'animated-fast' ) ) {
						duration = 750;
					} else {
						duration = 1000;
					}

					$animation_item.css( {
						'animation-duration': duration + 'ms',
						'animation-delay': delay + 'ms',
						'transition-property': 'visibility, opacity',
						'transition-duration': duration + 'ms',
						'transition-delay': delay + 'ms',
					} ).addClass( aniName );

					if ( $animation_item.hasClass( 'maskLeft' ) ) {
						$animation_item.css( 'width', 'fit-content' );
						var width = $animation_item.width();
						$animation_item
							.css( 'width', 0 )
							.css( 'transition', 'width ' + ( duration ? duration : 750 ) + 'ms linear ' + ( delay ? delay : '0s' ) )
							.css( 'width', width );
					}


					duration = duration ? duration : 750;
					$animation_item.addClass( 'show-content' );

					var temp = theme.requestTimeout( function() {
						$animation_item.css( 'transition-property', '' );
						$animation_item.css( 'transition-delay', '' );
						$animation_item.css( 'transition-duration', '' );

						self.timers.splice( self.timers.indexOf( temp ), 1 )
					}, ( delay ? ( delay + 200 ) : 200 ) );
					self.timers.push( temp );
				} );

				$el.find( '.slider-slide .slide-animate' ).each( function() {
					var $animation_item = $( this ),
						settings = 'undefined' != typeof $animation_item.data( 'settings' ) ? $animation_item.data( 'settings' ) : {};

					if ( $slideAnimations.index( $( this ) ) < 0 && settings ) {
						$animation_item.removeClass( settings._animation + ' animated appear-animation-visible elementor-invisible appear-animate' );
					}
				} );

				if ( this.slider.activeIndex == this.slider.params.slidesPerView - 1 ) {
					this.slider.emit( 'theme.from.begin' );
				}
			} else {
				$slideAnimations.addClass( 'show-content' );
			}

			self.translateFlag = 0;
		}

		function onSliderFromEdge() {
			if ( !this.slider.params.loop ) return;

			const slidesPerView = this.slider.params.slidesPerView;
			var $activeSlides, $slideAnimations;

			if ( this.slider.activeIndex > slidesPerView - 1 ) {
				$activeSlides = $( this.slider.slides ).slice( slidesPerView + 1, slidesPerView * 2 );
				$slideAnimations = $activeSlides.find( '.slide-animate' );
			} else {
				$activeSlides = $( this.slider.slides ).slice( - slidesPerView - 1, -1 );
				$slideAnimations = $activeSlides.find( '.slide-animate' );
			}

			$slideAnimations.each( function() {
				var $animation_item = $( this ),
					settings = 'undefined' != typeof $animation_item.data( 'settings' ) ? $animation_item.data( 'settings' ) : {};

				if ( settings && !$animation_item.hasClass( 'show-content' ) ) {
					$animation_item.addClass( 'show-content animated' );
				}
			} )
		}

		// Public Properties

		Slider.prototype.init = function( $el, options ) {
			this.timers = [];
			this.translateFlag = 0;

			// # Extend settings
			var settings = $.extend( true, {}, theme.defaults.slider );
			$el.attr( 'class' ).split( ' ' ).forEach( function( className ) {
				theme.defaults.sliderPresets[className] && $.extend( true, settings, theme.defaults.sliderPresets[className] );
			} );

			$.extend( true, settings, theme.parseOptions( $el.attr( 'data-slider-options' ) ), options );

			// # Set all video's loop as false
			$el.find( 'video' )
				.each( function() {
					this.loop = false;
				} );

			var $children = $el.children();
			var childrenCount = $children.length;
			if ( childrenCount ) {
				if ( $children.filter( '.row' ).length ) {
					$children.wrap( '<div class="slider-slide"></div>' );
					$children = $el.children();
				} else {
					$children.addClass( 'slider-slide' );
				}
			}

			// # Remove grid classes
			var cls = $el.attr( 'class' );
			var pattern = /gutter\-\w\w|cols\-\d|cols\-\w\w-\d|cols\-\w\w\w-\d/g;
			var match = cls.match( pattern ) || '';
			if ( match ) {
				match.push( 'row' );
				$el.data( 'slider-layout', match );
				$el.attr( 'class', cls.replace( pattern, '' ).replace( /\s+/, ' ' ) ).removeClass( 'row' );
			}

			// Display helper class for responsive navigation and pagination.
			var displayClass = [];
			if ( settings.breakpoints ) {
				var hideClasses = ['d-none', 'd-sm-none', 'd-md-none', 'd-lg-none', 'd-xl-none'];
				var showClasses = ['d-block', 'd-sm-block', 'd-md-block', 'd-lg-block', 'd-xl-block'];
				var bi = 0;
				for ( var i in settings.breakpoints ) {
					if ( childrenCount <= settings.breakpoints[i].slidesPerView ) {
						displayClass.push( hideClasses[bi] );
					} else if ( displayClass.length ) {
						displayClass.push( showClasses[bi] );
					}
					++bi;
				}
			}
			displayClass = ' ' + displayClass.join( ' ' );

			// Add navigation and pagination.
			var nav_dot = '';
			if ( !settings.dotsContainer && settings.pagination ) {
				nav_dot += '<div class="slider-pagination' + displayClass + '"></div>';
			}
			if ( settings.navigation ) {
				nav_dot += '<button class="slider-button slider-button-prev' + displayClass + '" aria-label="Prev"></button><button class="slider-button slider-button-next' + displayClass + '" aria-label="Next"></button>';
			}

			// Prepare slider
			$el.siblings( '.slider-button,.slider-pagination' ).remove();
			$el.parent().addClass( 'slider-container' + ( settings.statusClass ? ' ' + settings.statusClass : '' ) + ( $el.attr( 'data-slider-status' ) ? ' ' + $el.attr( 'data-slider-status' ) : '' ) )
				.parent().addClass( 'slider-relative' );
			$el.after( nav_dot );

			if ( !settings.dotsContainer && settings.pagination ) {
				settings.pagination = {
					clickable: true,
					el: $el.siblings( '.slider-pagination' )[0],
					bulletClass: 'slider-pagination-bullet',
					bulletActiveClass: 'active',
					modifierClass: 'slider-pagination-',
				}
			}
			if ( settings.navigation ) {
				settings.navigation = {
					prevEl: $el.siblings( '.slider-button-prev' )[0],
					nextEl: $el.siblings( '.slider-button-next' )[0],
					hideOnClick: true,
					disabledClass: 'disabled',
					hiddenClass: 'slider-button-hidden',
				}
			}

			if ( $el.hasClass( 'product-single-carousel' ) ) {
				var $thumbs = $el.closest( '.product-gallery' ).find( '.product-thumbs' );
				settings.thumbs.swiper = $thumbs.data( 'slider' );
			}

			settings.legacy = false;
			if ( typeof Swiper == 'undefined' ) return;
			// Setup slider
			this.slider = new ( theme.Swiper || Swiper )( $el[0].parentElement, settings );

			// # Register events for slider
			onInitialized.call( this );
			this.slider.on( 'resize', sliderLazyload.bind( this ) );
			this.slider.on( 'transitionEnd', onTranslated.bind( this ) );
			settings.onInitialized && settings.onInitialized.call( this.slider );

			// # Register animation slider
			if ( $el.hasClass( 'animation-slider' ) ) {
				onSliderInitialized.call( this );
				this.slider.on( 'resize', onSliderResized.bind( this ) );
				this.slider.on( 'transitionStart', onSliderTranslate.bind( this ) );
				this.slider.on( 'transitionEnd', onSliderTranslated.bind( this ) );
				this.slider.on( 'fromEdge theme.from.begin', onSliderFromEdge.bind( this ) );
			}

			// # Run thumb dots
			if ( settings.dotsContainer && 'preview' != settings.dotsContainer ) {
				var slider = this.slider;
				theme.$body.on( 'click', settings.dotsContainer + ' button', function() {
					slider.slideTo( $( slider.$el ).find( '.slider-slide-duplicate' ).length ? $( this ).index() + 1 : $( this ).index() );
				} );
				slider.on( 'transitionStart', function() {
					$( settings.dotsContainer ).children().removeClass( 'active' ).eq( this.realIndex % $( slider.$el ).find( '.slider-slide:not(.slider-slide-duplicate)' ).length ).addClass( 'active' );
				} );
			}

			// # Mount slider
			$el.trigger( 'initialize.slider', [this.slider] );
			$el.data( 'slider', this.slider );

			theme.initElementor();
		}

		return function( selector, options, createOnly ) {
			theme.$( selector ).each( function() {

				var $this = $( this );

				// If disable mobile slider is enabled, return
				if ( theme.disableMobileSlider &&
					!$this.parent().parent().hasClass( 'elementor-widget-wrap' ) &&
					!$this.parent().hasClass( 'elementor-widget-wrap' ) &&
					!$this.parent().parent().hasClass( 'elementor-section' ) ) {
					return;
				}

				// If slider is already created, return
				if ( $this.data( 'slider' ) ) {
					return;
				}

				// If slider has animated items
				var $anim_items = $this.find( '.elementor-invisible, .appear-animate, .animated' ).filter( ':not(.elementor-column)' );
				if ( $anim_items.length ) {
					$this.addClass( 'animation-slider' );
					$anim_items.addClass( 'slide-animate' ).each( function() {
						var $this = $( this );
						var pre = $this.data( 'settings' );
						if ( pre ) {
							var settings = {
								'_animation': pre._animation ? pre._animation : pre.animation
							};
							if ( pre._animation_delay ) {
								settings['_animation_delay'] = Number( pre._animation_delay );
							}
							$this
								.data( 'settings', settings )
								.attr( 'data-settings', JSON.stringify( settings ) );
						}
					} );
				}

				var runSlider = function() {
					// if in passive tab
					if ( selector == '.slider-wrapper' ) {
						var $pane = $this.closest( '.tab-pane' );
						if ( $pane.length && !$pane.hasClass( 'active' ) && $pane.closest( '.elementor-widget-' + alpha_vars.theme + '_widget_products_tab' ).length ) {
							return;
						}
					}

					// create slider
					new Slider( $this, options );
				}
				createOnly ? new runSlider : theme.call( runSlider );
			} );
		}
	} )();

	/**
	 * Initalize sliders and check their slide animations
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	theme.initSlider = function( selector ) {

		// Is mobile slider disabled?
		theme.disableMobileSlider = theme.$body.hasClass( 'alpha-disable-mobile-slider' ) && ( 'ontouchstart' in document ) && ( theme.$window.width() < 1200 );

		// Initialize sliders
		theme.slider( selector );
	}

	/**
	 * Initialize offcanvas
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	theme.initOffcanvasAction = function() {
		theme.$body
			// Open Offcanvas
			.on( 'click', '.offcanvas .offcanvas-open', function( e ) {
				if ( $( this ).parent().hasClass( 'cart-offcanvas' ) && $( document.body ).hasClass( 'woocommerce-cart' ) ) {
					return;
				}
				$( this ).parent().has( 'offcanvas' ) && $( this ).parent().toggleClass( 'opened' );
				e.preventDefault();
			} )
			.on( 'click', '.offcanvas .btn-close', function( e ) {
				e.preventDefault();
				$( this ).closest( '.offcanvas' ).removeClass( 'opened' );
			} )
			.on( 'click', '.offcanvas .offcanvas-overlay', function( e ) {
				e.preventDefault();
				$( this ).closest( '.offcanvas' ).removeClass( 'opened' );
			} )
			.on( 'click', '.submit-review-toggle', function( e ) {
				// review
				e.preventDefault();
				$( '#review_form_wrapper' ).toggleClass( 'opened' );
			} );

	}
	/**
	 * Show edit page tooltip
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	theme.showEditPageTooltip = function() {
		if ( $.fn.tooltip ) {
			$( '.alpha-edit-link' ).each( function() {
				var $this = $( this ),
					title = $this.data( 'title' ),
					$target = $this.next( '.alpha-block' );

				if ( $this.siblings( '.slider-wrapper' ).length ) {
					$target = $this.siblings( '.slider-wrapper' ).find( '.alpha-block' );
				}

				$target.addClass( 'alpha-has-edit-link' ).tooltip( {
					html: true,
					template: '<div class="tooltip alpha-tooltip-wrap" role="tooltip"><div class="arrow"></div><div class="tooltip-inner alpha-tooltip"></div></div>',
					trigger: 'manual',
					title: '<a href="' + $this.data( 'link' ) + '" target="_blank">' + title + '</a>',
					delay: 300
				} );
				var tooltipData = $target.data( 'bs.tooltip' );
				if ( tooltipData && tooltipData.element ) {
					$( tooltipData.element ).on( 'mouseenter.bs.tooltip', function( e ) {
						tooltipData._enter( e );
					} );
					$( tooltipData.element ).on( 'mouseleave.bs.tooltip', function( e ) {
						tooltipData._leave( e );
					} );
				}
			} );

			theme.$body.on( 'mouseenter mouseleave', '.tooltip[role="tooltip"]', function( e ) {
				var $element = $( '.alpha-block[aria-describedby="' + $( this ).attr( 'id' ) + '"]' );
				if ( $element.length && $element.data( 'bs.tooltip' ) ) {
					var fn_name = 'mouseenter' == e.type ? '_enter' : '_leave';
					$element.data( 'bs.tooltip' )[fn_name]( false, $element.data( 'bs.tooltip' ) );
				}
			} );
		}
	}

	/**
	 * Live Search
	 * 
	 * @param {*} e 
	 * @param {*} $selector 
	 */
	theme.liveSearch = function( e, $selector ) {
		var timeout = '',
			request = '',
			state = {
				requesting: false,
				open: false,
				prevResults: ''
			},
			minChars = 3;

		if ( !$.fn.devbridgeAutocomplete ) {
			return;
		}

		if ( 'undefined' == typeof $selector ) {
			$selector = $( '.search-wrapper' );
		} else {
			$selector = $selector;
		}

		$selector.each( function() {
			var $this = $( this ),
				isFullscreen = $this.hasClass( 'hs-fullscreen' ),
				$searchResult = $this.find( '.search-results' ),
				$searchResultContainer = $this.find( '.search-container' ),
				appendTo = isFullscreen ? $searchResult : $this.find( '.live-search-list' ),
				searchCat = $this.find( '.cat' ),
				postType = $this.find( 'input[name="post_type"]' ).val(),
				serviceUrl = alpha_vars.ajax_url + '?action=alpha_ajax_search&nonce=' + alpha_vars.nonce + ( postType ? '&post_type=' + postType : '' );

			if ( isFullscreen ) {

				function getResults() {

					// Deleted all chars / too few chars.
					var val = $this.find( 'input[type="search"]' ).val();
					if ( val.length == 0 || val.length < minChars ) {
						requestCheck();
						resetHeight();
						return;
					}

					request = $.ajax( {
						type: 'GET',
						url: serviceUrl,
						data: {
							query: val,
							posts_per_page: 21,
							cat: searchCat.length ? searchCat.val() : 0,
							is_full_screen: true
						},
						success: function( response ) {

							state.requesting = false;

							// No results.
							if ( !response.suggestions ) {
								resetHeight( true );
								appendTo.html( alpha_vars.search_result );
							}

							if ( response.suggestions && response.suggestions !== state.prevResults ) {

								appendTo.html( response.suggestions );
								$this.imagesLoaded( function() {

									$searchResultContainer.css( {
										'max-height': parseInt( $searchResult.outerHeight() ) + 'px'
									} );

									setTimeout( function() {
										$this.closest( '.search-wrapper' ).addClass( 'results-shown' );
									}, 200 );

									// Desktop animation
									if ( window.innerWidth > 992 ) {

										appendTo.find( '.product, .post-wrap' ).css( {
											'opacity': '0',
											'transform': 'translateY(25px)',
											'transition': 'none'
										} );

										setTimeout( function() {
											appendTo.find( '.product, .post-wrap' ).css( {
												'transition': 'box-shadow 0.25s ease, opacity 0.55s cubic-bezier(0.2, 0.6, 0.4, 1), transform 0.55s cubic-bezier(0.2, 0.6, 0.4, 1)'
											} );
										}, 50 );


										appendTo.find( '.product, .post-wrap' ).each( function( i ) {
											var $that = $( this );
											setTimeout( function() {
												$that.css( {
													'opacity': '1',
													'transform': 'translateY(0)'
												} )
											}, 50 + ( i * 60 ) );
										} );

									}

									state.open = true;
									state.prevResults = response.suggestions;
								} )

							}
						}
					} );

					state.requesting = true;
				}

				function requestCheck() {
					if ( state.requesting === true ) {
						request.abort();
						state.requesting = false;
					}
				};

				function resetHeight( is_empty = false ) {

					$searchResultContainer.css( {
						'max-height': is_empty ? '20px' : ''
					} );

					setTimeout( function() {
						$this.closest( '.search-wrapper' ).removeClass( 'results-shown' );
					}, 400 );

					state.prevResults = '';
					state.open = false;
				}

				$this.find( 'input.form-control' ).on( 'keyup', function( e ) {

					// Verify Key.
					var keysToSkip = [16, 91, 32, 37, 39, 17];

					if ( keysToSkip.indexOf( e.keyCode ) != -1 ) {
						return;
					}

					clearTimeout( timeout );
					timeout = setTimeout( function() {
						if ( state.requesting ) {
							return;
						}
						getResults();
					}, 400 );
				} );

				theme.$window.on( 'resize', function() {

					$this.find( '.search-container' ).css( {
						'max-height': ''
					} );

					if ( state.open === true ) {
						$this.find( '.search-container' ).css( {
							'max-height': parseInt( $searchResult.outerHeight() ) + 'px'
						} );
					}

				} );
				$this.find( '.close-overlay, .hs-close' ).on( 'click', function( e ) {
					e.preventDefault();
					resetHeight();
					requestCheck();

					var closeTimeoutDur = ( $this.closest( '.search-wrapper' ).hasClass( 'results-shown' ) ) ? 800 : 400;
					setTimeout( function() {
						$this.find( 'input.form-control' ).val( '' );
					}, closeTimeoutDur );

					$( this ).closest( '.search-wrapper' ).removeClass( 'show' );
					$( 'body' ).css( 'overflow', '' );
					$( 'body' ).css( 'margin-right', '' );
				} );

			} else {
				$this.find( 'input[type="search"]' ).devbridgeAutocomplete( {
					minChars: minChars,
					appendTo: appendTo,
					triggerSelectOnValidInput: false,
					serviceUrl: serviceUrl,
					onSearchStart: function() {
						$this.addClass( 'skeleton-body' );
						appendTo.children().eq( 0 )
							.html( alpha_vars.skeleton_screen ? '<div class="skel-pro-search"></div><div class="skel-pro-search"></div><div class="skel-pro-search"></div>' : '<div class="d-loading"><i></i></div>' )
							.css( { position: 'relative', display: 'block' } );
					},
					onSelect: function( item ) {
						if ( item.id != -1 ) {
							window.location.href = item.url;
						}
					},
					onSearchComplete: function( q, suggestions ) {
						if ( !suggestions.length ) {
							appendTo.children().eq( 0 ).hide();
						}
					},
					beforeRender: function( container ) {
						$( container ).removeAttr( 'style' );
					},
					formatResult: function( item, currentValue ) {
						var pattern = '(' + $.Autocomplete.utils.escapeRegExChars( currentValue ) + ')',
							html = '';
						if ( item.img ) {
							html += '<img class="search-image" src="' + item.img + '">';
						}
						html += '<div class="search-info">';
						html += '<div class="search-name">' + item.value.replace( new RegExp( pattern, 'gi' ), '<strong>$1<\/strong>' ) + '</div>';
						if ( item.price ) {
							html += '<span class="search-price">' + item.price + '</span>';
						}
						html += '</div>';

						return html;
					}
				} );

				if ( searchCat.length ) {
					var searchForm = $this.find( 'input[type="search"]' ).devbridgeAutocomplete();
					searchCat.on( 'change', function( e ) {
						if ( searchCat.val() && searchCat.val() != '0' ) {
							searchForm.setOptions( {
								serviceUrl: serviceUrl + '&cat=' + searchCat.val()
							} );
						} else {
							searchForm.setOptions( {
								serviceUrl: serviceUrl
							} );
						}

						searchForm.hide();
						searchForm.onValueChange();
					} );
				}
			}
		} );
	}

	/**
	 * Init Youtube and Vimeo API
	 *
	 * @since 1.2.0
	 */
	theme.initSocialVideo = function() {
		// init Youtube video api
		var $youtube_videos = $( '.alpha-video-social.video-youtube' );
		if ( $youtube_videos.length ) {
			window.onYouTubeIframeAPIReady = function() {
				$youtube_videos.each( function() {
					var $this = $( this ),
						$wrap = $this.parent( '.video-wrapper' ),
						item_id = $this.attr( 'id' ),
						youtube_id = $this.data( 'video' ),
						is_loop = $this.data( 'loop' ),
						enable_audio = $this.data( 'audio' ),
						autoplay = 1,
						controls = 0;
					if ( '0' === $this.data( 'autoplay' ) ) {
						autoplay = 0;
					}
					if ( $this.data( 'controls' ) ) {
						controls = parseInt( $this.data( 'controls' ) );
					}
					new YT.Player( item_id, {
						width: '100%',
						//height: '100%',
						videoId: youtube_id,
						playerVars: {
							'autoplay': autoplay,
							'controls': controls,
							'modestbranding': 1,
							'rel': 0,
							'playsinline': 1,
							'showinfo': 0,
							'loop': is_loop
						},
						events: {
							onReady: function( t ) {
								if ( $wrap.length ) {
									theme.fitVideoSize( $wrap );
								}
								if ( 0 === parseInt( enable_audio ) && t && t.target && t.target.mute ) {
									t.target.mute();
								}
							}
						}
					} );
				} );
			};

			if ( $( 'script[src*="www.youtube.com/iframe_api"]' ).length ) {
				setTimeout( onYouTubeIframeAPIReady, 350 );
			} else {
				var tag = document.createElement( 'script' );
				tag.src = "//www.youtube.com/iframe_api";
				var firstScriptTag = document.getElementsByTagName( 'script' )[0];
				firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );
			}
		}

		// init Vimeo video api
		var $vimeo_videos = $( '.alpha-video-social.video-vimeo' );
		if ( $vimeo_videos.length ) {
			var alphaVimeoInit = function() {
				$vimeo_videos.each( function() {
					var $this = $( this ),
						$wrap = $this.parent( '.fit-video' ),
						item_id = $this.attr( 'id' ),
						video_id = $this.data( 'video' ),
						is_loop = $this.data( 'loop' ),
						enable_audio = $this.data( 'audio' ),
						autoplay = true;
					if ( '0' === $this.data( 'autoplay' ) ) {
						autoplay = false;
					}
					var player = new Vimeo.Player( item_id, {
						id: video_id,
						loop: 1 === parseInt( is_loop ) ? true : false,
						autoplay: autoplay,
						transparent: false,
						background: true,
						muted: 0 === parseInt( enable_audio ) ? true : false,
						events: {
							onReady: function( t ) {
								if ( $wrap.length ) {
									theme.fitVideoSize( $wrap );
								}
								if ( 0 === parseInt( enable_audio ) && t && t.target && t.target.mute ) {
									t.target.mute();
								}
							}
						}
					} );
					if ( 0 === parseInt( enable_audio ) ) {
						player.setVolume( 0 );
					}
					if ( $wrap.length ) {
						player.ready().then( function() {
							theme.fitVideoSize( $wrap );
						} );
					}
				} );
			};

			if ( $( 'script[src="https://player.vimeo.com/api/player.js"]' ).length ) {
				setTimeout( alphaVimeoInit, 350 );
			} else {
				var tag = document.createElement( 'script' );
				tag.addEventListener( 'load', function( event ) {
					setTimeout( alphaVimeoInit, 50 );
				} );
				tag.src = "https://player.vimeo.com/api/player.js";
				var firstScriptTag = document.getElementsByTagName( 'script' )[0];
				firstScriptTag.parentNode.insertBefore( tag, firstScriptTag );
			}
		}
	}

	/**
	 * Alpha Theme Async Setup
	 * 
	 * Initialize Method which runs asynchronously after document has been loaded
	 * 
	 * @since 1.0
	 */
	theme.initAsync = function() {
		theme.appearAnimate( '.appear-animate' );            // Runs appear animations
		if ( alpha_vars.resource_disable_elementor && typeof elementorFrontend != 'object' ) {
			theme.appearAnimate( '.elementor-invisible' );            // Runs appear animations
			theme.countTo( '.elementor-counter-number' );             // Runs counter
		}
		theme.minipopup.init();                              // Initialize minipopup
		theme.stickyContent( '.sticky-content:not(.mobile-icon-bar):not(.sticky-toolbox)' ); // Initialize sticky content
		theme.stickyContent( '.mobile-icon-bar', theme.defaults.stickyMobileBar );		 // Initialize sticky mobile bar
		theme.stickyContent( '.sticky-toolbox', theme.defaults.stickyToolbox );			 // Initialize sticky toolbox
		theme.initSlider( '.slider-wrapper' );               // Initialize slider
		theme.$window.trigger( 'alpha_after_sticky' );
		theme.initOffcanvasAction();						 // Initialize offcanvas
		theme.playableVideo( '.post-video' );                // Initialize playable video
		theme.accordion( '.card-header > a' );               // Initialize accordion
		theme.tab( '.nav-tabs:not(.alpha-comment-tabs)' );   // Initialize tab
		theme.isotopes( '.grid:not(.lp-courses-bar)' );							 // Masonry Layout
		theme.alert( '.alert' );                             // Initialize alert

		// Initialize parallax
		theme.$body.find( '.parallax' ).each( function() {
			var $this = $( this ),
				opts;

			var pluginOptions = $this.data( 'parallax-options' );
			if ( pluginOptions )
				opts = pluginOptions;

			$this.themeParallax( opts );
		} );

		theme.countTo( '.count-to' );                        // Initialize countTo
		theme.menu.init();                                   // Initialize menus
		theme.initPopups();                                  // Initialize popups: login, register, play video, newsletter popup
		theme.initAccount();                                 // Initialize account popup
		theme.initScrollTopButton();                         // Initialize scroll top button.
		theme.initScrollTo();                                // Initialize scroll top button.
		theme.initContactForms();                            // Initialize contact forms
		theme.initSearchForm();                              // Initialize search form
		theme.initVideoPlayer();							 // Initialize VideoPlayer
		theme.initElementor();							     // Compatibility with Elementor
		theme.initVendorCompatibility();                     // Compatibility with Vendor Plugins
		theme.initFloatingElements();						 // Initialize floating widgets
		theme.initAdvancedMotions();						 // Initialize scrolling widgets
		theme.initKeyDown();                                 // Initialize keydown events
		theme.liveSearch();                                  // Live Search
		theme.initSocialVideo();                             // Initialize Youtube & Vimeo video
		// Setup Events
		theme.$window.on( 'resize', theme.onResize );

		// Complete!
		theme.status == 'load' && ( theme.status = 'complete' );
		theme.$window.trigger( 'alpha_complete' );

		// For admin
		theme.showEditPageTooltip();
	}
} )( jQuery );