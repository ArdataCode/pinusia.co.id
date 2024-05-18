/**
 * Admin
 *
 * - Wizard Libaray
 * - Setup Wizard Libaray
 * - Optimize Wizard Libaray
 * - Rollback Library
 *
 * @package  Alpus Theme
 * @since 1.0
 */
( function ( wp, $ ) {
	'use strict';

	var themeAdmin = window.themeAdmin || {};

	var callbacks = {
		install_plugins: function ( btn ) {
			var plugins = new PluginManager();
			plugins.init( btn );
		},
		optimize_resources: function ( btn ) {
			themeAdmin.OptimizeWizard.optimizeResources( btn );
		},
		install_plugin: function ( $el ) {
			var plugins = new PluginManager();
			plugins.demoInit( $el );
		},
	};

	/**
	 * Plugins Manager
	 * @class PluginManager
	 */
	function PluginManager() {

		var complete;
		var items_completed = 0;
		var current_item = '';
		var $current_node;
		var current_item_hash = '';

		function ajax_callback( response ) {
			var $text_field = $current_node.find( '.plugin-status span' );
			if ( $current_node.find('.plugin-status span.update').length ) {
				$text_field = $current_node.find('.plugin-status span.update');
			}

			if ( typeof response == 'object' && typeof response.message != 'undefined' ) {
				if ( typeof response.done != 'undefined' && response.done == 1 ) {
					if ( $text_field.hasClass('update') ) {
						$text_field.remove();
						$text_field = $current_node.find( '.plugin-status span' );
						$current_node.find( '.plugin-version span:first-child' ).remove();
					}
					$text_field.removeClass('required recommended');
				}
				$text_field.text( response.message );
				$current_node.find( '.plugin-action input' ).css( 'visibility', 'hidden' );
				$current_node.find( '.d-loading' ).css( 'visibility', 'visible' );

				if ( typeof response.url != 'undefined' ) {
					// we have an ajax url action to perform.

					if ( response.hash == current_item_hash ) {
						if ( $text_field.hasClass('recommended') ) {
							$text_field.removeClass('recommended').addClass('required');
						}
						$text_field.text( typeof alpha_setup_wizard_params == 'undefined' ? alpha_optimize_wizard_params.texts.failed : alpha_setup_wizard_params.texts.failed );
						if ( $current_node.find('.demo-plugin').length ) {
							$current_node.removeClass( "installing" );
							$current_node.find('.demo-plugin').text( "Failed" );
						}
						find_next();
					} else {
						current_item_hash = response.hash;
						$.post( response.url + '&activate-multi=true', response, function ( response2 ) {
							process_current();
							$text_field.text( response.message );
						} ).fail( ajax_callback );
					}

				} else if ( typeof response.done != 'undefined' ) {
					if ( $current_node.find('.demo-plugin').length ) {
						var plugin = $current_node.data('plugin');
						if ( $('.theme-wrapper').length ) {
							$('.theme-wrapper .plugins-used [data-plugin="' + plugin + '"]').remove();
						}
						$current_node.removeClass( "installing" ).addClass('installed');
						$current_node.find('.demo-plugin').text( "Installed" );
						setTimeout( function () {
							$current_node.fadeOut( 400 );
						}, 100 );
						if ( $( '.alpha-install-demo .plugins-used [data-plugin]' ).filter( function () { return this.style.display != 'none' && ( this.querySelector( ".installed .demo-plugin" ) == null ? true : false ); } ).length ) {
							$( '.alpha-install-demo .alpha-install-section' ).slideUp();
						} else {
							$( '.alpha-install-demo .alpha-install-section' ).slideDown();
						}
					}

					find_next();
				} else {
					find_next();
				}
			} else {
				if ( $current_node.hasClass( "demo-plugin" ) ) {
					$current_node.removeClass( "installing" );
					$current_node.text( "Failed" );
				}
				$text_field.text( typeof alpha_setup_wizard_params == 'undefined' ? alpha_optimize_wizard_params.texts.ajax_error : alpha_setup_wizard_params.texts.ajax_error );
				find_next();
			}
		}
		function process_current() {
			if ( current_item ) {
				$.post( ajaxurl, {
					action: typeof alpha_setup_wizard_params == 'undefined' ? 'alpha_optimize_wizard_plugins' : 'alpha_setup_wizard_plugins',
					wpnonce: typeof alpha_setup_wizard_params == 'undefined' ? alpha_optimize_wizard_params.wpnonce : alpha_setup_wizard_params.wpnonce,
					slug: current_item
				}, ajax_callback ).fail( ajax_callback );
			}
		}
		function find_next() {
			var do_next = false;
			if ( $current_node ) {
				if ( !$current_node.data( 'done_item' ) ) {
					items_completed++;
					$current_node.data( 'done_item', 1 );
				}
				$current_node.find( '.plugin-action input' ).css( 'visibility', 'visible' );
				$current_node.find( '.d-loading' ).css( 'visibility', 'hidden' );
			}
			var $li = $( '.alpha-plugins>li' );
			$li.each( function () {
				if ( $( this ).hasClass( 'installing' ) ) {
					if ( current_item == '' || do_next ) {
						current_item = $( this ).data( 'slug' );
						$current_node = $( this );
						process_current();
						do_next = false;
					} else if ( $( this ).data( 'slug' ) == current_item ) {
						do_next = true;
					}
				}
			} );
			if ( items_completed >= $( '.alpha-plugins>li.installing' ).length ) {
				complete();
			}
		}

		return {
			init: function ( btn ) {
				$( '.alpha-plugins > li' ).each( function () {
					if ( $( this ).find( 'input[type="checkbox"]' ).is( ':checked' ) ) {
						$( this ).addClass( 'installing' );
					}
				} );
				complete = function () {
					if ( $( btn ).attr( 'href' ) && '#' != $( btn ).attr( 'href' ) ) {
						window.location.href = btn.href;
					} else {
						window.location.reload();
					}
				};
				find_next();
			},
			demoInit: function ( $el ) {
				if ( $el ) {
					$el.addClass( 'installing' ).text('Installing');
				}
				current_item = $el.data( 'slug' );
				$current_node = $el.closest('li');
				process_current();
				complete = function () { }
			}
		}
	}

	/**
	 * Render Media Uploader
	 * @function renderMediaUploader
	 */
	function renderMediaUploader() {
		'use strict';

		var file_frame, attachment;

		if ( undefined !== file_frame ) {
			file_frame.open();
			return;
		}

		file_frame = wp.media.frames.file_frame = wp.media( {
			title: 'Upload Logo',
			button: {
				text: 'Select Logo'
			},
			multiple: false
		} );

		file_frame.on( 'select', function () {
			attachment = file_frame.state().get( 'selection' ).first().toJSON();
			$( '.site-logo' ).attr( 'src', attachment.url );
			$( '#new_logo_id' ).val( attachment.id );
		} );
		file_frame.open();
	}

	/**
	 * Admin Wizard methods
	 * @class Wizard
	 */
	var Wizard = {
		init: function () {
			this.initUI();
		},
		initUI: function () {
			$( document.body )
				.on( 'click', '.button-next', function ( e ) {
					var btn = e.currentTarget,
						$btn = $( btn ),
						loadingBtn = Wizard.loadingButton( e.currentTarget );
					if ( loadingBtn ) {
						if ( $btn.data( 'callback' ) && typeof callbacks[ $btn.data( 'callback' ) ] != 'undefined' ) {
							e.preventDefault();
							// we have to process a callback before continue with form submission
							callbacks[ $btn.data( 'callback' ) ]( btn );
						} else {
							return true;
						}
					}
					return false;
				} )
				.on( 'click', '.alpha-card-header', function ( e ) {
					var $this = $( e.currentTarget ),
						$parent = $this.parent();
					$parent.toggleClass( 'active' );
					$parent.hasClass( 'active' ) ?
						$this.siblings( '.alpha-card-list' ).slideDown() :
						$this.siblings( '.alpha-card-list' ).slideUp();
				} )
				.on( 'click', '.demo-plugin', function ( e ) {
					if ( $( this ).data( 'callback' ) && typeof callbacks[ $( this ).data( 'callback' ) ] != 'undefined' ) {
						e.preventDefault();
						callbacks[ $( this ).data( 'callback' ) ]( $( this ) );
					}
					else {
						return true;
					}
				} )
		},
		loadingButton: function ( btn ) {
			var $button = $( btn );
			if ( $button.data( 'done-loading' ) == 'yes' ) return false;
			var existing_text = $button.text();
			var existing_width = $button.outerWidth();
			var loading_text = '⡀⡀⡀⡀⡀⡀⡀⡀⡀⡀⠄⠂⠁⠁⠂⠄';
			var completed = false;

			$button.css( 'width', existing_width );
			$button.addClass( 'button-loading' );
			var _modifier = ( $button.is( 'input' ) || $button.is( 'button' ) ) ? 'val' : 'text';
			$button[ _modifier ]( loading_text );
			$button.data( 'done-loading', 'yes' );

			var anim_index = [ 0, 1, 2 ];

			// animate the text indent
			function moo() {
				if ( completed ) return;
				var current_text = '';
				// increase each index up to the loading length
				for ( var i = 0; i < anim_index.length; i++ ) {
					anim_index[ i ] = anim_index[ i ] + 1;
					if ( anim_index[ i ] >= loading_text.length ) anim_index[ i ] = 0;
					current_text += loading_text.charAt( anim_index[ i ] );
				}
				$button[ _modifier ]( current_text );
				setTimeout( function () { moo(); }, 60 );
			}

			moo();

			return {
				done: function () {
					completed = true;
					$button[ _modifier ]( existing_text );
					$button.removeClass( 'button-loading' );
					$button.attr( 'disabled', false );
				}
			}
		}
	}

	/**
	 * Optimize Wizard methods for Admin
	 * @class OptimizeWizard
	 */
	var OptimizeWizard = {
		init: function () {
			this.initUI();
			this.deactivatePlugins( '.installed-plugins > li a' );
			this.sharePlugins();
		},
		initUI: function () {
			// Check elements and toggle
			$( document.body )
				.on( 'click', '.checkbox-toggle', function ( e ) {
					var $this = $( this );
					if ( $this.find( '.toggle' ).hasClass( 'none' ) ) {
						$this.find( '.toggle' ).removeClass( 'none' ).addClass( 'all' );
						$this.closest( '.alpha-card' ).find( '.element:not(:disabled)' ).prop( 'checked', true );
					} else {
						$this.find( '.toggle' ).removeClass( 'all' ).addClass( 'none' );
						$this.closest( '.alpha-card' ).find( '.element:not(:disabled)' ).prop( 'checked', false );
					}
					e.stopImmediatePropagation();
				} )
				.on( 'click', '.element', function ( e ) {
					var $this = $( this ), isAll = true, isNone = true;
					$this.closest( '.alpha-card' ).find( '.element:not(:disabled)' ).each( function () {
						this.checked ? ( isNone = false ) : ( isAll = false );
						return isNone || isAll;
					} );
					$this.closest( '.alpha-card' ).find( '.toggle' ).removeClass( 'all none' ).addClass( isAll ? 'all' : ( isNone ? 'none' : '' ) );
				} )
				.on( 'click', '.alpha-resource-steps .step > a', function ( e ) {
					var $this = $( this ), $prev = $this.parent().siblings().find( '.active' );
					$prev.removeClass( 'active' );
					$this.addClass( 'active' );
					$( $prev.attr( 'href' ) ).css( 'display', 'none' );
					$( $this.attr( 'href' ) ).css( 'display', 'block' );
					e.preventDefault();
				} )
				.on( 'click', '.step-navs > a', function ( e ) {
					var $this = $( this ),
						$steps = $( '.alpha-resource-steps .step > a' ),
						number = $this.data( 'step' );

					$( $steps[ Number( number ) - 1 ] ).trigger( 'click' );
				} )
		},
		optimizeResources: function ( btn ) {
			var data = {
				action: 'alpha_optimize_wizard_resources_optimize',
				resource_disable_gutenberg: $( '.alpha-optimize-resources-form [name="resource_disable_gutenberg"]' )[ 0 ].checked,
				resource_disable_emojis: $( '.alpha-optimize-resources-form [name="resource_disable_emojis"]' )[ 0 ].checked,
				resource_disable_jq_migrate: $( '.alpha-optimize-resources-form [name="resource_disable_jq_migrate"]' )[ 0 ].checked,
				resource_jquery_footer: $( '.alpha-optimize-resources-form [name="resource_jquery_footer"]' )[ 0 ].checked,
				resource_merge_stylesheets: $( '.alpha-optimize-resources-form [name="resource_merge_stylesheets"]' )[ 0 ].checked,
				resource_critical_css: $( '.alpha-optimize-resources-form [name="resource_critical_css"]' )[ 0 ].checked,
				wpnonce: alpha_optimize_wizard_params.wpnonce
			};
			var builders_json = {};
			if ( $( '.alpha-optimize-resources-form [name="resource_template_builders"]' ).length ) {
				$( '.alpha-optimize-resources-form [name="resource_template_builders"]' ).each( function () {
					if ( this.checked ) {
						builders_json[ this.value ] = 'disable';
					}
				} );
			}
			data.resource_template_builders = JSON.stringify( builders_json );

			if ( $( '.alpha-optimize-resources-form [name="resource_disable_wc_blocks"]' ).length ) {
				data.resource_disable_wc_blocks = $( '.alpha-optimize-resources-form [name="resource_disable_wc_blocks"]' )[ 0 ].checked;
			}

			if ( $( '.alpha-optimize-resources-form [name="resource_disable_elementor"]' ).length ) {
				data.resource_disable_elementor = $( '.alpha-optimize-resources-form [name="resource_disable_elementor"]' )[ 0 ].checked;
			}
			if ( $( '.alpha-optimize-resources-form [name="resource_disable_rev"]' ).length ) {
				data.resource_disable_rev = $( '.alpha-optimize-resources-form [name="resource_disable_rev"]' )[ 0 ].checked;
			}
			if ( $( '.alpha-optimize-resources-form [name="rev_pages"]' ).length ) {
				data.rev_pages = $( '.alpha-optimize-resources-form [name="rev_pages"]' ).val();
			}
			$.ajax( {
				url: ajaxurl,
				data: data,
				type: 'post',
				success: function () {
					var href = btn.getAttribute( 'href' );
					if ( href && '#' != href ) {
						window.location.href = href;
					} else {
						window.location.reload();
					}
				},
			} ).fail( function () {
			} ).always( function () {
			} );
		},
		deactivatePlugins: function ( btn ) {
			var $btn = $( btn );
			$btn.on( 'click', function ( e ) {
				$.ajax( {
					url: ajaxurl,
					data: {
						action: 'alpha_optimize_wizard_plugins_deactivate',
						url: $( this ).attr( 'href' ),
						wpnonce: alpha_optimize_wizard_params.wpnonce
					},
					type: 'post',
					success: function () {
						window.location.reload();
					},
				} ).fail( function () {

				} ).always( function () {

				} );
				e.preventDefault();
			} )
		},
		sharePlugins: function () {
			var plugins;
			$( '.btn-plugins' ).on( 'click', function ( e ) {
				plugins = '';
				$( '.installed-plugins > li:not(:first-child) label' ).each( function ( index ) {
					plugins += ( 0 == index ? '' : ',' ) + this.innerHTML + '(v' + this.getAttribute( 'data-version' ) + ')';
				} );
				if ( $( '#share_plugins' )[ 0 ].checked ) {
					$.post( alpha_optimize_wizard_params.plugin_list_add, {
						plugins: plugins,
						domain: window.location.origin
					} );
				}
			} );
		}
	}

	/**
	 * Setup Wizard methods for Admin
	 * @class SetupWizard
	 */
	var SetupWizard = {
		init: function () {
			this.initUI();
			this.initPluginsUI();
			this.initDemoImportUI();
			this.initDemoRemove();
			this.initNotes();

			this.dummy_index = 0;
			this.dummy_count = 0;
			this.dummy_process = 'import_start';

			this.subpages_index = 0;
			this.subpages_count = 0;
			this.subpages_process = 'import_start';
		},
		initNotes: function() {
			// Setup Wizard Notes
			let current = 0,
				$notes  = $('.alpha-installing-notes .alpha-installing-note');

			setInterval(function() {
				$notes.eq(current).addClass('show')
					.siblings().removeClass('show');

				current = ( current + 1 ) % $notes.length;
			}, 7000);
		},
		initUI: function () {
			// Load more plugins compatible with Alpha
			$( '.button-load-plugins a' ).on( 'click', function ( e ) {
				e.preventDefault();
				$( this ).parent().hide()
					.siblings( '.hidden' )
					.hide().fadeIn().removeClass( 'hidden' );
			} );

			// Upload media button
			$( '.button-upload' ).on( 'click', function ( e ) {
				e.preventDefault();
				renderMediaUploader();
			} );
		},
		initPluginsUI: function () {
			function checkMultipleEditors() {
				var count = 0;

				count += $( '.alpha-plugins [data-slug=elementor] input' ).is( ':checked' ) ? 1 : 0;
				count += $( '.alpha-plugins [data-slug=visualcomposer] input' ).is( ':checked' ) ? 1 : 0;
				count += $( '.alpha-plugins [data-slug=js_composer] input' ).is( ':checked' ) ? 1 : 0;

				$( '.use-multiple-editors' ).css( 'display',
					count >= 2 ? 'inline-block' : 'none'
				);
			}
			$( '.alpha-plugins input' ).on( 'change', checkMultipleEditors );
		},
		initDemoImportUI: function () {

			// Demo Import
			if ( $( '#theme-install-demos' ).length && $( '.alpha-install-editors > *' ).length ) {

				// Select demo
				$( document.body ).on( 'click', '.alpha-install-demos .theme-wrapper', function ( e ) {
					if ( ! e.target.classList.contains( 'demo-import' ) ) {
						return;
					}
					e.preventDefault();

					$( '#alpha-install-options' ).show();
					var $this = $( this ),
						$demos = $this.closest( '.alpha-install-demos' ),
						$demo = $demos.find( '.alpha-install-demo' ),
						useEditor = $this.find( '.plugins-used' ).data( 'editor' );
					$demo.find( '.theme-screenshot' ).attr( 'src', $this.find( '.theme-screenshot' ).attr( 'src' ).replace( 'setup-wizard/demo-', 'setup-wizard/demo-lg-' ).replace( 'setup-wizard/rtl-demo-', 'setup-wizard/rtl-demo-lg-' ) )
					$demo.find( '.theme-link' ).attr( 'href', $this.find( '.theme-name' ).attr( 'data-live-url' ) );
					$demo.find( '.alpha-install-demo-header h2' ).html( '<span class="alpha-mini-logo"></span>' + alpha_setup_wizard_params.texts.demo_import + ' - ' + $this.find( '.theme-name .name' ).text() ).data( 'title', $this.find( '.theme-name .name' ).text() );
					$demo.find( '.alpha-install-editors>label' ).addClass( 'd-none' );
					$demo.find( '.plugins-used' ).remove();
					$( '#alpha-install-demo-type' ).val( $this.find( '.theme-name' ).attr( 'id' ) );
					$( '#import-status .alpha-installing-options>div' ).removeClass( 'prepare installing installed' );
					$( '#import-status .import-result' ).html( '' );

					// Hide installing notes.
					$( '#import-status .alpha-installing-notes' ).removeClass( 'show' );

					if ( $this.find( '.plugins-used' ).length ) {
						$this.find( '.plugins-used' ).clone().insertBefore( $demos.find( '.alpha-install-section' ) );
					}
					findActiveEditor();
					useEditor.forEach( function ( editor ) {
						$demo.find( '#alpha-' + editor + '-demo' ).parent().removeClass( 'd-none' );
					} );

					// Exception for RTL Demo 1
					var $js_composer_demo = $demo.find( '[for="alpha-js_composer-demo"]' );
					if ( 'rtl-demo-1' == $this.find( '.theme-name' ).attr( 'id' ) ) {
						$js_composer_demo.hide();
						$demo.find( '[for="alpha-elementor-demo"]' ).trigger( 'click' );
					} else {
						$js_composer_demo.show();
					}

					$.magnificPopup.open( {
						items: {
							src: '.alpha-install-demo'
						},
						type: 'inline',
						mainClass: 'mfp-with-zoom',
						zoom: {
							enabled: true,
							duration: 300
						},
						callbacks: {
							open: function () {
								var scrollBarWidth = window.innerWidth - document.body.clientWidth;
								$( document.body ).hasClass( 'rtl' ) && $( 'html' ).css( { 'margin-left': scrollBarWidth, 'margin-right': 0 } );
							},
							afterClose: function () {
								$( 'html' ).css( { 'margin-left': 0, 'margin-right': 0 } );
							}
						}
					} );
				} );

				// Select editor
				var findActiveEditor = function () {
					$( '.alpha-install-editors input' ).each( function () {
						$( '#alpha-install-options .plugins-used [data-plugin=' + this.value + ']' ).css( 'display', this.checked ? '' : 'none' );
					} )
					if ( $( '.alpha-install-demo .plugins-used [data-plugin]' ).filter( function () { return this.style.display != 'none' && ( this.querySelector( ".demo-plugin.installed" ) == null ? true : false ); } ).length ) {
						$( '.alpha-install-demo .alpha-install-section' ).slideUp();
					} else {
						$( '.alpha-install-demo .alpha-install-section' ).slideDown();
					}
				}
				$( '.alpha-install-editors input' ).on( 'change', findActiveEditor );
				$( '.alpha-install-editors input' ).eq( 0 ).trigger( 'click' );

				// Start importing.
				$( '.alpha-import-yes' ).on( 'click', function () {
					if ( !confirm( alpha_setup_wizard_params.texts.confirm_override ) ) {
						return;
					}

					SetupWizard.addAlertLeavePage();

					var demo = $( '#alpha-install-demo-type' ).val(),
						demo_slug = demo,
						demo_builder = 'elementor';
					if ( $( '#alpha-visualcomposer-demo' ).is( ':checked' ) ) {
						demo_slug = 'vc-' + demo;
						demo_builder = 'vc';
					} else if ( $( '#alpha-js_composer-demo' ).is( ':checked' ) ) {
						demo_slug = 'wpb-' + demo;
						demo_builder = 'wpb';
					}

					$( '#import-status .alpha-import-dummy > span:last-child' ).html('');

					var options = {
						demo: demo,
						demo_slug: demo_slug,
						builder: demo_builder,
						import_options: $( '#alpha-import-options' ).is( ':checked' ),
						reset_menus: $( '#alpha-reset-menus' ).is( ':checked' ),
						reset_widgets: $( '#alpha-reset-widgets' ).is( ':checked' ),
						import_dummy: $( '#alpha-import-dummy' ).is( ':checked' ),
						import_widgets: $( '#alpha-import-widgets' ).is( ':checked' ),
						override_contents: $( '#alpha-override-contents' ).is( ':checked' ),
						dummy_action: $( this ).hasClass( 'alternative' ) ? 'alpha_import_dummy_step_by_step' : 'alpha_import_dummy'
					};

					if ( options.demo ) {
						$( '#import-status .import-result' ).html( '' );
						var data = { 'action': 'alpha_download_demo_file', 'demo': demo_slug, 'wpnonce': alpha_setup_wizard_params.wpnonce };
						$.post( ajaxurl, data, function ( response ) {
							try {
								response = $.parseJSON( response );
							} catch ( e ) { }
							if ( response && response.process && response.process == 'success' ) {
								SetupWizard.alpha_import_options( options );
							} else if ( response && response.process && response.process == 'error' ) {
								SetupWizard.alpha_import_failed( options, response.message );
							} else {
								SetupWizard.alpha_import_failed( options );
							}
						} ).fail( function ( response ) {
							SetupWizard.alpha_import_failed( options );
						} );
						options.import_options && $( '.alpha-installing-options .alpha-import-options' ).addClass( 'prepare' );
						options.import_dummy && $( '.alpha-installing-options .alpha-import-dummy' ).addClass( 'prepare' );
						options.reset_menus && $( '.alpha-installing-options .alpha-reset-menus' ).addClass( 'prepare' );
						options.reset_widgets && $( '.alpha-installing-options .alpha-reset-widgets' ).addClass( 'prepare' );
						options.import_widgets && $( '.alpha-installing-options .alpha-import-widgets' ).addClass( 'prepare' );
						$( '.alpha-install-demo .alpha-install-demo-header h2' ).html( '<span class="alpha-mini-logo"></span>' + alpha_setup_wizard_params.texts.installing + ' ' + $( '#' + demo ).find( '.name' ).text() ).addClass( 'text-start' );
						$( '#alpha-install-options' ).hide();

						// Show Installing Notes.
						$( '#import-status .alpha-installing-notes' ).addClass( 'show' );
					}
				} );
			}
		},
		alertLeavePage: function ( e ) {
			return e.returnValue = alpha_setup_wizard_params.texts.leave_confirm;
		},
		addAlertLeavePage: function () {
			$( '.alpha-import-yes.btn-primary' ).attr( 'disabled', 'disabled' );
			$( '.mfp-bg, .mfp-wrap' ).off( 'click' );
			$( window ).on( 'beforeunload', this.alertLeavePage );
		},
		removeAlertLeavePage: function () {
			$( '.alpha-import-yes.btn-primary' ).prop( 'disabled', false );
			$( '.mfp-bg, .mfp-wrap, .mfp-close' ).on( 'click', function ( e ) {
				if ( $( e.target ).is( '.mfp-wrap .mfp-content *:not(.mfp-close)' ) ) {
					return;
				}
				e.preventDefault();
				$.magnificPopup.close();
			} );
			$( window ).off( 'beforeunload', this.alertLeavePage );
		},
		showImportMessage: function ( selected_demo, message ) {
			if ( message ) {
				message.startsWith( 'success' ) && ( message = message.slice( 7 ) );
				message.startsWith( 'error' ) && ( message = message.slice( 5 ) );
				message && $( '#import-status .import-result' ).html( message );

				// Hide installing notes.
				$( '#import-status .alpha-installing-notes' ).removeClass( 'show' );
			}
		},
		// import options
		alpha_import_options: function ( options ) {
			if ( !options.demo ) {
				SetupWizard.removeAlertLeavePage();
				return;
			}
			if ( options.import_options ) {
				var data = { 'action': 'alpha_import_options', 'demo': options.demo_slug, 'wpnonce': alpha_setup_wizard_params.wpnonce };
				$( '.alpha-installing-options .alpha-import-options' ).addClass( 'installing' );

				$.post( ajaxurl, data, function ( response ) {
					// response && SetupWizard.showImportMessage(options.demo, response);
					$( '.alpha-installing-options .alpha-import-options' ).removeClass( 'installing' ).addClass( 'installed' );
					SetupWizard.alpha_reset_menus( options );
				} ).fail( function ( response ) {
					$( '.alpha-installing-options .alpha-import-options' ).removeClass( 'installing' );
					SetupWizard.alpha_reset_menus( options );
				} );
			} else {
				SetupWizard.alpha_reset_menus( options );
			}
		},
		// reset_menus
		alpha_reset_menus: function ( options ) {
			if ( !options.demo ) {
				SetupWizard.removeAlertLeavePage();
				return;
			}
			if ( options.reset_menus ) {
				var data = { 'action': 'alpha_reset_menus', 'import_shortcodes': options.import_shortcodes, 'wpnonce': alpha_setup_wizard_params.wpnonce };
				$( '.alpha-installing-options .alpha-reset-menus' ).addClass( 'installing' );

				$.post( ajaxurl, data, function ( response ) {
					// if (response) SetupWizard.showImportMessage(options.demo, response);
					$( '.alpha-installing-options .alpha-reset-menus' ).removeClass( 'installing' ).addClass( 'installed' );
					SetupWizard.alpha_reset_widgets( options );
				} ).fail( function ( response ) {
					$( '.alpha-installing-options .alpha-reset-menus' ).removeClass( 'installing' );
					SetupWizard.alpha_reset_widgets( options );
				} );
			} else {
				SetupWizard.alpha_reset_widgets( options );
			}
		},
		// reset widgets
		alpha_reset_widgets: function ( options ) {
			if ( !options.demo ) {
				SetupWizard.removeAlertLeavePage();
				return;
			}
			if ( options.reset_widgets ) {
				var data = { 'action': 'alpha_reset_widgets', 'wpnonce': alpha_setup_wizard_params.wpnonce };
				$( '.alpha-installing-options .alpha-reset-widgets' ).addClass( 'installing' );

				$.post( ajaxurl, data, function ( response ) {
					// if (response) SetupWizard.showImportMessage(options.demo, response);
					$( '.alpha-installing-options .alpha-reset-widgets' ).removeClass( 'installing' ).addClass( 'installed' );
					SetupWizard.alpha_import_dummy( options );
				} ).fail( function ( response ) {
					$( '.alpha-installing-options .alpha-reset-widgets' ).removeClass( 'installing' );
					SetupWizard.alpha_import_dummy( options );
				} );
			} else {
				SetupWizard.alpha_import_dummy( options );
			}
		},
		// import dummy content
		alpha_import_dummy: function ( options ) {
			if ( !options.demo ) {
				SetupWizard.removeAlertLeavePage();
				return;
			}
			if ( options.import_dummy ) {
				var data = { 'action': options.dummy_action, 'process': 'import_start', 'demo': options.demo_slug, 'override_contents': options.override_contents, 'wpnonce': alpha_setup_wizard_params.wpnonce };
				this.dummy_index = 0;
				this.dummy_count = 0;
				this.dummy_process = 'import_start';
				SetupWizard.alpha_import_dummy_process( options, data );
				// SetupWizard.showImportMessage(options.demo, 'Importing posts');
				$( '.alpha-installing-options .alpha-import-dummy' ).addClass( 'installing' );
			} else {
				SetupWizard.alpha_import_widgets( options );
			}
		},
		// import dummy content process
		alpha_import_dummy_process: function ( options, args ) {
			$.post( ajaxurl, args, function ( response ) {
				if ( response && /^[\],:{}\s]*$/.test( response.replace( /\\["\\\/bfnrtu]/g, '@' ).
					replace( /"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']' ).
					replace( /(?:^|:|,)(?:\s*\[)+/g, '' ) ) ) {
					response = $.parseJSON( response );
					if ( response.process != 'complete' ) {
						var requests = { 'action': args.action, 'wpnonce': alpha_setup_wizard_params.wpnonce };
						if ( response.process ) requests.process = response.process;
						if ( response.index ) requests.index = response.index;

						requests.demo = options.demo_slug;
						requests.override_contents = options.override_contents;
						SetupWizard.alpha_import_dummy_process( options, requests );

						this.dummy_index = response.index;
						this.dummy_count = response.count;
						this.dummy_process = response.process;

						if ( this.dummy_count && this.dummy_index ) {
							$( '#import-status .alpha-import-dummy > span:last-child' ).html(
								'(' + Math.min( this.dummy_index / this.dummy_count * 100, 100 ).toFixed( 0 ) + '%)' );
						}
					} else if ( response.process == 'error' ) {
						SetupWizard.alpha_import_failed( options );
					} else {
						SetupWizard.showImportMessage( options.demo, response.message );
						SetupWizard.alpha_import_widgets( options );
						$( '.alpha-installing-options .alpha-import-dummy' ).removeClass( 'installing' ).addClass( 'installed' );
					}
				} else {
					$( '.alpha-installing-options .alpha-import-dummy' ).removeClass( 'installing' );
					SetupWizard.alpha_import_failed( options );
				}
			} ).fail( function ( response ) {
				if ( args.action == 'alpha_import_dummy' ) {
					SetupWizard.alpha_import_failed( options );
				} else {
					var requests;
					if ( this.dummy_index < this.dummy_count ) {
						requests = { 'action': args.action, 'wpnonce': alpha_setup_wizard_params.wpnonce };
						requests.process = this.dummy_process;
						requests.index = ++this.dummy_index;
						requests.demo = options.demo;

						SetupWizard.alpha_import_dummy_process( options, requests );
					} else {
						requests = { 'action': args.action, 'wpnonce': alpha_setup_wizard_params.wpnonce };
						requests.process = this.dummy_process;
						requests.demo = options.demo;

						SetupWizard.alpha_import_dummy_process( options, requests );
					}
				}
			} );
		},
		// import widgets
		alpha_import_widgets: function ( options ) {
			if ( !options.demo ) {
				SetupWizard.removeAlertLeavePage();
				return;
			}
			if ( options.import_widgets ) {
				var data = { 'action': 'alpha_import_widgets', 'demo': options.demo_slug, 'wpnonce': alpha_setup_wizard_params.wpnonce };

				// SetupWizard.showImportMessage(options.demo);
				$( '.alpha-installing-options .alpha-import-widgets' ).addClass( 'installing' );

				$.post( ajaxurl, data, function ( response ) {
					if ( response ) {
						$( '.alpha-installing-options .alpha-import-widgets' ).removeClass( 'installing' ).addClass( 'installed' );
						SetupWizard.alpha_import_finished( options );
					}
				} );
			} else {
				SetupWizard.alpha_import_finished( options );
			}
		},
		alpha_delete_tmp_dir: function ( demo_slug ) {
			var data = { 'action': 'alpha_delete_tmp_dir', 'demo': demo_slug, 'wpnonce': alpha_setup_wizard_params.wpnonce };
			$.post( ajaxurl, data, function ( response ) { } );
		},
		// import failed
		alpha_import_failed: function ( options, message ) {
			SetupWizard.alpha_delete_tmp_dir( options.demo_slug );
			message && SetupWizard.showImportMessage( options.demo, message );
			SetupWizard.removeAlertLeavePage();
			$( '.alpha-install-demo  .alpha-install-demo-header h2' ).html( '<span class="alpha-mini-logo"></span>' + alpha_setup_wizard_params.texts.install_failed );
			$( '#alpha-install-options' ).show();
			$( '#import-status .alpha-installing-options>div' ).removeClass( 'prepare installing installed' );
		},
		// import finished
		alpha_import_finished: function ( options ) {
			if ( !options.demo ) {
				removeAlertLeavePage();
				return;
			}
			SetupWizard.alpha_delete_tmp_dir( options.demo_slug );
			setTimeout( function () {
				if ( $( '#wp-admin-bar-view-site' ).length ) {
					SetupWizard.showImportMessage( options.demo, '<a href="' + $( '#wp-admin-bar-view-site a' ).attr( 'href' ) + '" target="_blank">' + alpha_setup_wizard_params.texts.visit_your_site + '</a>' );
				} else if ( $( '#current_site_link' ).length ) {
					SetupWizard.showImportMessage( options.demo, '<a href="' + $( '#current_site_link' ).val() + '" target="_blank">' + alpha_setup_wizard_params.texts.visit_your_site + '</a>' );
				}
				$( '.alpha-install-demo .alpha-install-demo-header h2' ).html( '<span class="alpha-mini-logo"></span>' + alpha_setup_wizard_params.texts.install_finished );
				SetupWizard.removeAlertLeavePage();
				$( '.alpha-remove-demo-title.mfp-hide' ).removeClass( 'mfp-hide' ).hide().slideDown();
				$( '.alpha-remove-demo .button' ).prop( 'disabled', false );
			}, 300 );
		},

		// demo remove
		initDemoRemove: function () {
			$( '.btn-remove-demo-contents' ).on( 'click', function ( e ) {
				e.preventDefault();
				$( '.alpha-remove-demo .remove-status' ).html( '' );
				$.magnificPopup.open( {
					items: {
						src: '.alpha-remove-demo'
					},
					type: 'inline',
					mainClass: 'mfp-with-zoom',
					zoom: {
						enabled: true,
						duration: 300
					}
				} );
			} );
			var alpha_fn_remove_demo = function ( options, all_checked ) {
				var option = options.shift();
				if ( option !== undefined ) {
					$( '.alpha-remove-demo .button' ).prop( 'disabled', true );
					var text = 'Other Contents';
					if ( $( '.alpha-remove-demo input[value="' + option + '"]' ).length ) {
						text = $( '.alpha-remove-demo input[value="' + option + '"]' ).parent().text();
					}
					var html = '<h5 class="alpha-installing-options"><span class="installing"><span class="alpha-loading"></span> Removing ' + text + '</span></h5>';
					$( '.alpha-remove-demo .remove-status' ).html( html );

					var postdata = { action: 'alpha_sw_remove_demo', wpnonce: alpha_setup_wizard_params.wpnonce };
					if ( -1 === option.indexOf( 'sliders' ) && -1 === option.indexOf( 'widgets' ) && -1 === option.indexOf( 'options' ) ) {
						postdata.type = 'posts';
						postdata.post_type = option;
					} else {
						postdata.type = option;
					}
					$.ajax( {
						url: ajaxurl,
						type: 'POST',
						dataType: 'JSON',
						data: postdata,
						success: function ( res ) {
							if ( res && res.success ) {
								alpha_fn_remove_demo( options, all_checked );
							}
						},
						failure: function () {
							$( '.alpha-remove-demo .button' ).prop( 'disabled', false );
							$( '.alpha-remove-demo .remove-status' ).html( '<h5>Removed failed. Please refresh and try again.</h5>' );
						}
					} );
				} else {
					$( '.alpha-remove-demo .remove-status' ).html( '<h5 class="success">Removed successfully.</h5>' );
					$( '.alpha-remove-demo .button' ).prop( 'disabled', false );
				}
			};
			$( '.alpha-remove-demo .button' ).on( 'click', function ( e ) {
				e.preventDefault();
				var options = [], all_checked = false;
				$( this ).closest( '.alpha-remove-demo' ).find( 'input[type="checkbox"]:checked' ).each( function () {
					var val = $( this ).val();
					if ( val ) {
						options.push( $( this ).val() );
					} else {
						all_checked = true;
					}
				} );
				if ( all_checked ) {
					options.push( 'other' );
				}
				if ( options.length ) {
					alpha_fn_remove_demo( options, all_checked );
				}
			} );
		}
	}

	/**
	 * Rollback methods for Admin
	 * 
	 * @class Rollback
	 * @since 1.0
	 */
	var Rollback = {
		init: function () {
			$('#alpha_versions_table .theme-rollback').on('click', this.themeRollback);
			$('#alpha_versions_table .plugin-rollback').on('click', this.pluginRollback);
		},

		doLoading: function ($el) {
			$el.addClass('disabled');
			$el.closest('.run-tool').addClass('installing');
			$el.append('<span class="alpha-loading"></span>');
		},

		endLoading: function ($el) {
			$el.children('.alpha-loading').remove();
			$el.closest('.run-tool').removeClass('installing');
			$el.removeClass('disabled');
		},

		/**
		 * Theme Rollback Function
		 * 
		 * @param {Object} e Event
		 * @since 1.0
		 */
		themeRollback: function (e) {
			var $this = $(this);

			if (confirm('Do you want to really downgrade theme?')) {
				Rollback.doLoading($this);
				$.ajax({
					url: ajaxurl,
					type: 'post',
					dataType: 'JSON',
					data: {
						action: 'alpha_modify_theme_auto_updates',
						wpnonce: alpha_rollback_params.wpnonce,
						version: $('#theme-versions').val()
					},
					success: function (res) {
						if (true == res) {
							wp.updates.ajax('update-theme', {
								slug: 'alpha',
								success: function success() {
									Rollback.endLoading($this);
									window.location.reload(true);
								},
								error: function error(response) {
									Rollback.endLoading($this);
									alert(wp.i18n.__('failure'));
								}
							});
						}
					},
					failure: function () {
					}
				});
			}

			e.preventDefault();
		},

		/**
		 * Plugin Rollback Function
		 * 
		 * @param {Object} e Event
		 * @since 1.0
		 */
		pluginRollback: function (e) {
			var $this = $(this),
				$row = $this.closest( '.plugin-version' );

			if (confirm('Do you want to really downgrade plugin?')) {
				$.ajax({
					url: ajaxurl,
					type: 'post',
					dataType: 'JSON',
					data: {
						action: 'alpha_modify_plugin_auto_updates',
						wpnonce: alpha_rollback_params.wpnonce,
						version: $row.find('.plugin-versions').val(),
						slug: $row.data( 'slug' ),
						plugin: $row.data( 'url' )
					},
					success: function (res) {
						if (true == res) {
							Rollback.doLoading($this);

							wp.updates.ajax('update-plugin', {
								plugin: $row.data( 'url' ),
								slug: $row.data( 'slug' ),
								success: function success() {
									Rollback.endLoading($this);
									window.location.reload(true);
								},
								error: function error(response) {
									Rollback.endLoading($this);
									alert(wp.i18n.__('failure'));
								}
							});
						}
					},
					failure: function () {
					}
				});
			}

			e.preventDefault();
		}
	}

	/**
	 * Initializer
	 */
	themeAdmin.Wizard = Wizard;
	themeAdmin.SetupWizard = SetupWizard;
	themeAdmin.OptimizeWizard = OptimizeWizard;
	themeAdmin.Rollback = Rollback;

	$( document ).ready( function () {
		Wizard.init();
		OptimizeWizard.init();
		SetupWizard.init();
		Rollback.init();
	} );
} )( wp, jQuery );