/**
 * Admin UI behaviour — settings panel, media/gallery controls, repeaters,
 * live colour preview and destructive-action guards.
 *
 * WordPress ships jQuery in the admin, and the APIs used here (wp.media,
 * wpColorPicker, jQuery UI sortable) are jQuery based by definition, so this
 * file uses jQuery. The front-end bundle (assets/js/theme.js) stays vanilla.
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

/* global jQuery, wp, esAdmin */
( function ( $ ) {
	'use strict';

	var i18n = ( window.esAdmin && window.esAdmin.i18n ) || {};

	var COLOR_TOKENS = {
		color_primary: '--es-primary',
		color_secondary: '--es-secondary',
		color_accent: '--es-accent',
		color_background: '--es-background',
		color_surface: '--es-surface',
		color_surface_alt: '--es-surface-alt',
		color_text: '--es-text',
		color_muted: '--es-muted',
		color_border: '--es-border',
	};

	/* ---------------------------------------------------------------------
	 * Colour pickers + live preview
	 * ------------------------------------------------------------------ */

	function fieldKey( $field ) {
		return $field.attr( 'data-es-field' ) || '';
	}

	function buildPreview() {
		var $header = $( '.es-admin__header' ).first();

		if ( ! $header.length || $( '.es-admin__preview' ).length ) {
			return;
		}

		var $strip = $( '<div class="es-admin__preview" aria-live="polite"></div>' );
		$strip.append( '<span class="es-admin__preview-label">پیش‌نمایش زندهٔ پوسته:</span>' );

		$.each( COLOR_TOKENS, function ( key, token ) {
			var $field = $( '[data-es-field="' + key + '"] .es-color-field' );

			if ( ! $field.length ) {
				return;
			}

			var value = $field.val() || $field.attr( 'data-default-color' ) || '#000000';

			$strip.append(
				'<span class="es-admin__swatch" data-token="' +
					token +
					'"><i class="es-admin__swatch-dot" style="--es-swatch:' +
					value +
					'"></i>' +
					key +
					'</span>'
			);
		} );

		$header.append( $strip );
	}

	function syncPreview( key, value ) {
		var token = COLOR_TOKENS[ key ];

		if ( ! token ) {
			return;
		}

		$( '.es-admin__swatch[data-token="' + token + '"] .es-admin__swatch-dot' ).css( '--es-swatch', value );

		var $preview = $( '[data-es-field="' + key + '"] .es-color-preview' );

		if ( $preview.length ) {
			$preview.css( '--es-swatch', value );
		}
	}

	function initColors() {
		if ( ! $.fn.wpColorPicker ) {
			return;
		}

		$( '.es-color-field' ).each( function () {
			var $input = $( this );
			var key = fieldKey( $input.closest( '.es-field' ) );

			$input.wpColorPicker( {
				change: function ( event, ui ) {
					var value = ui.color.toString();

					syncPreview( key, value );
				},
				clear: function () {
					var fallback = $input.attr( 'data-default-color' ) || '';

					window.setTimeout( function () {
						syncPreview( key, fallback );
					}, 0 );
				},
			} );

			if ( ! $input.closest( '.es-field__control' ).find( '.es-color-preview' ).length ) {
				var value = $input.val() || $input.attr( 'data-default-color' ) || '';

				$input.after( '<span class="es-color-preview" style="--es-swatch:' + value + '"></span>' );
			}
		} );

		buildPreview();
	}

	/* ---------------------------------------------------------------------
	 * Icon picker
	 * ------------------------------------------------------------------ */

	function initIconPicker() {
		$( '[data-es-icon-picker]' ).each( function () {
			var $picker = $( this );
			var $input = $picker.find( 'input[type="hidden"]' );

			$picker.on( 'click', '.es-icon-picker__item', function () {
				var $item = $( this );

				$picker.find( '.es-icon-picker__item' ).removeClass( 'is-active' ).attr( 'aria-pressed', 'false' );
				$item.addClass( 'is-active' ).attr( 'aria-pressed', 'true' );
				$input.val( $item.attr( 'data-icon' ) ).trigger( 'change' );
			} );
		} );
	}

	/* ---------------------------------------------------------------------
	 * Range output
	 * ------------------------------------------------------------------ */

	function initRanges() {
		$( '.es-control--range' ).each( function () {
			var $range = $( this );
			var $output = $range.siblings( '.es-range__output' );

			$range.on( 'input change', function () {
				$output.text( $range.val() );
			} );
		} );
	}

	/* ---------------------------------------------------------------------
	 * Media (single image) control
	 * ------------------------------------------------------------------ */

	function initMedia() {
		$( '[data-es-media]' ).each( function () {
			var $wrap = $( this );
			var $input = $wrap.find( '[data-es-media-input]' );
			var $preview = $wrap.find( '[data-es-media-preview]' );
			var $name = $wrap.find( '[data-es-media-name]' );
			var $remove = $wrap.find( '[data-es-media-remove]' );
			var frame = null;

			$wrap.on( 'click', '[data-es-media-select]', function ( event ) {
				event.preventDefault();

				if ( frame ) {
					frame.open();

					return;
				}

				frame = wp.media( {
					title: i18n.selectImage || 'انتخاب تصویر',
					button: { text: i18n.useImage || 'استفاده از این تصویر' },
					library: { type: 'image' },
					multiple: false,
				} );

				frame.on( 'select', function () {
					var attachment = frame.state().get( 'selection' ).first().toJSON();
					var url =
						( attachment.sizes && attachment.sizes.medium && attachment.sizes.medium.url ) ||
						attachment.url;

					$input.val( attachment.id ).trigger( 'change' );
					$preview.addClass( 'has-image' ).html( '<img src="' + url + '" alt="">' );
					$name.text( attachment.filename || '' );
					$remove.prop( 'hidden', false );
				} );

				frame.open();
			} );

			$wrap.on( 'click', '[data-es-media-remove]', function ( event ) {
				event.preventDefault();

				$input.val( '' ).trigger( 'change' );
				$preview
					.removeClass( 'has-image' )
					.html( '<span class="es-media__placeholder"><span class="dashicons dashicons-format-image"></span></span>' );
				$name.text( '' );
				$remove.prop( 'hidden', true );
			} );
		} );
	}

	/* ---------------------------------------------------------------------
	 * Gallery control
	 * ------------------------------------------------------------------ */

	function gallerySync( $wrap ) {
		var ids = $wrap
			.find( '[data-es-gallery-items] .es-gallery__item' )
			.map( function () {
				return $( this ).attr( 'data-id' );
			} )
			.get()
			.filter( Boolean );

		$wrap.find( '[data-es-gallery-input]' ).val( ids.join( ',' ) ).trigger( 'change' );
	}

	function initGalleries() {
		$( '[data-es-gallery]' ).each( function () {
			var $wrap = $( this );
			var $items = $wrap.find( '[data-es-gallery-items]' );
			var frame = null;

			if ( $.fn.sortable ) {
				$items.sortable( {
					items: '> .es-gallery__item',
					tolerance: 'pointer',
					update: function () {
						gallerySync( $wrap );
					},
				} );
			}

			$wrap.on( 'click', '[data-es-gallery-add]', function ( event ) {
				event.preventDefault();

				if ( frame ) {
					frame.open();

					return;
				}

				frame = wp.media( {
					title: i18n.selectImage || 'انتخاب تصویر',
					button: { text: i18n.useImage || 'استفاده از این تصویر' },
					library: { type: 'image' },
					multiple: true,
				} );

				frame.on( 'select', function () {
					frame
						.state()
						.get( 'selection' )
						.each( function ( attachment ) {
							var data = attachment.toJSON();
							var url =
								( data.sizes && data.sizes.thumbnail && data.sizes.thumbnail.url ) || data.url;

							$items.append(
								'<li class="es-gallery__item" data-id="' +
									data.id +
									'"><img src="' +
									url +
									'" alt=""><button type="button" class="es-gallery__remove" data-es-gallery-remove aria-label="' +
									( i18n.removeImage || 'حذف تصویر' ) +
									'">&times;</button></li>'
							);
						} );

					gallerySync( $wrap );
				} );

				frame.open();
			} );

			$wrap.on( 'click', '[data-es-gallery-remove]', function ( event ) {
				event.preventDefault();
				$( this ).closest( '.es-gallery__item' ).remove();
				gallerySync( $wrap );
			} );
		} );
	}

	/* ---------------------------------------------------------------------
	 * Repeater control
	 * ------------------------------------------------------------------ */

	function repeaterTitle( $row ) {
		var $first = $row.find( '.es-repeater__fields input[type="text"], .es-repeater__fields textarea' ).first();
		var value = $.trim( $first.val() || '' );

		if ( value ) {
			$row.find( '[data-es-repeater-title]' ).text( value );
		}
	}

	function initRepeaters() {
		$( '[data-es-repeater]' ).each( function () {
			var $repeater = $( this );
			var $rows = $repeater.find( '[data-es-repeater-rows]' );
			var $template = $repeater.find( '[data-es-repeater-template]' );

			$repeater.on( 'click', '[data-es-repeater-add]', function ( event ) {
				event.preventDefault();

				var index = parseInt( $repeater.attr( 'data-next-index' ), 10 ) || 0;
				var html = ( $template.html() || '' ).replace( /__INDEX__/g, String( index ) );
				var $row = $( html );

				$rows.append( $row );
				$repeater.attr( 'data-next-index', index + 1 );
				$row.find( '.es-repeater__fields' ).prop( 'hidden', false );
				$row.find( '[data-es-repeater-toggle]' ).attr( 'aria-expanded', 'true' );
				$row.find( 'input, textarea, select' ).first().trigger( 'focus' );

				if ( $.fn.sortable && $rows.data( 'sortable' ) !== undefined ) {
					$rows.sortable( 'refresh' );
				}
			} );

			$repeater.on( 'click', '[data-es-repeater-toggle]', function ( event ) {
				event.preventDefault();

				var $toggle = $( this );
				var $fields = $toggle.closest( '.es-repeater__row' ).find( '.es-repeater__fields' );
				var expanded = $toggle.attr( 'aria-expanded' ) === 'true';

				$toggle.attr( 'aria-expanded', expanded ? 'false' : 'true' );
				$fields.prop( 'hidden', expanded );
			} );

			$repeater.on( 'click', '.es-repeater__handle', function ( event ) {
				if ( $( event.target ).closest( 'button' ).length ) {
					return;
				}

				$( this ).find( '[data-es-repeater-toggle]' ).trigger( 'click' );
			} );

			$repeater.on( 'click', '[data-es-repeater-remove]', function ( event ) {
				event.preventDefault();

				if ( ! window.confirm( i18n.confirmRow || 'این ردیف حذف شود؟' ) ) {
					return;
				}

				$( this ).closest( '.es-repeater__row' ).remove();
			} );

			$repeater.on( 'input change', '.es-repeater__fields input[type="text"], .es-repeater__fields textarea', function () {
				repeaterTitle( $( this ).closest( '.es-repeater__row' ) );
			} );

			if ( $.fn.sortable ) {
				$rows.sortable( {
					items: '> .es-repeater__row',
					handle: '[data-es-repeater-handle]',
					tolerance: 'pointer',
					axis: 'y',
					placeholder: 'es-repeater__placeholder',
				} );
			}
		} );
	}

	/* ---------------------------------------------------------------------
	 * Destructive actions and dirty state
	 * ------------------------------------------------------------------ */

	function initConfirmations() {
		$( document ).on( 'click', '[data-confirm]', function ( event ) {
			var message = $( this ).attr( 'data-confirm' ) || i18n.confirmReset;

			if ( ! window.confirm( message ) ) {
				event.preventDefault();
				event.stopImmediatePropagation();

				return false;
			}
		} );

		$( 'form.es-tools-form' ).on( 'submit', function ( event ) {
			var $button = $( document.activeElement );
			var message = $button.attr( 'data-confirm' );

			if ( message && ! window.confirm( message ) ) {
				event.preventDefault();

				return false;
			}
		} );
	}

	function initDirtyState() {
		var dirty = false;

		$( '.es-admin__form' ).on( 'change input', ':input', function () {
			dirty = true;
		} );

		$( '.es-admin__form' ).on( 'submit', function () {
			dirty = false;
		} );

		$( window ).on( 'beforeunload', function () {
			if ( ! dirty ) {
				return;
			}

			return 'تغییرات ذخیره‌نشده دارید.';
		} );
	}

	/* ---------------------------------------------------------------------
	 * Tabs: keep the active tab visible on small screens
	 * ------------------------------------------------------------------ */

	function initTabs() {
		var $active = $( '.es-admin__tab.is-active' );

		if ( ! $active.length || ! window.matchMedia( '(max-width: 782px)' ).matches ) {
			return;
		}

		$active[ 0 ].scrollIntoView( { inline: 'center', block: 'nearest' } );
	}

	/* ---------------------------------------------------------------------
	 * Boot
	 * ------------------------------------------------------------------ */

	$( function () {
		initColors();
		initIconPicker();
		initRanges();
		initMedia();
		initGalleries();
		initRepeaters();
		initConfirmations();
		initDirtyState();
		initTabs();
	} );
} )( jQuery );
