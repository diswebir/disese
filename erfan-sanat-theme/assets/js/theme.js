/**
 * Erfan Sanat — front-end behaviour.
 *
 * Vanilla JavaScript only: no jQuery, no external runtime library, no CDN.
 * Every module is opt-in: it exits immediately when its markup is absent, so
 * the same bundle can serve the whole site without selector churn.
 *
 * Progressive enhancement is the rule: all navigation works without JS
 * (the search overlay is a plain GET form, the drawer is a normal menu list,
 * the lightbox links open the full-size image, the services list is readable
 * markup). JavaScript only adds the interaction layer.
 *
 * Hooks implemented here (see templates):
 *   [data-es-header] [data-es-sticky] [data-es-progress] [data-es-progress-bar]
 *   [data-es-menu-open] [data-es-menu-close] [data-es-drawer]
 *   [data-es-search-open] [data-es-search-panel] [data-es-search-close]
 *   [data-es-search-input] [data-es-search-results]
 *   [data-es-to-top] [data-es-float] [data-es-services] [data-es-service]
 *   [data-es-service-toggle] [data-es-service-preview] [data-es-service-current]
 *   [data-es-lightbox] [data-es-lightbox-item] [data-es-copy] [data-es-toc]
 *   [data-es-entry] [data-es-cart-count]
 *
 * @package ErfanSanat
 * @since   1.0.0
 */

( function () {
	'use strict';

	var settings = window.esTheme || {};
	var i18n = settings.i18n || {};
	var root = document.documentElement;
	var body = document.body;

	/* ---------------------------------------------------------------------
	 * Utilities
	 * ------------------------------------------------------------------ */

	function qs( selector, context ) {
		return ( context || document ).querySelector( selector );
	}

	function qsa( selector, context ) {
		return Array.prototype.slice.call( ( context || document ).querySelectorAll( selector ) );
	}

	function on( target, type, handler, options ) {
		if ( target ) {
			target.addEventListener( type, handler, options || false );
		}
	}

	function rafThrottle( fn ) {
		var queued = false;

		return function () {
			var args = arguments;
			var self = this;

			if ( queued ) {
				return;
			}

			queued = true;

			window.requestAnimationFrame( function () {
				queued = false;
				fn.apply( self, args );
			} );
		};
	}

	function debounce( fn, wait ) {
		var timer = null;

		return function () {
			var args = arguments;
			var self = this;

			window.clearTimeout( timer );
			timer = window.setTimeout( function () {
				fn.apply( self, args );
			}, wait );
		};
	}

	function reducedMotion() {
		return !!( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches );
	}

	function scrollTo( top ) {
		try {
			window.scrollTo( { top: top, behavior: reducedMotion() ? 'auto' : 'smooth' } );
		} catch ( e ) {
			window.scrollTo( 0, top );
		}
	}

	function focusables( container ) {
		return qsa(
			'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])',
			container
		).filter( function ( el ) {
			return ! el.hasAttribute( 'hidden' ) && el.offsetParent !== null;
		} );
	}

	function trapTab( container, event ) {
		var items = focusables( container );

		if ( ! items.length ) {
			return;
		}

		var first = items[ 0 ];
		var last = items[ items.length - 1 ];

		if ( event.shiftKey && document.activeElement === first ) {
			event.preventDefault();
			last.focus();
		} else if ( ! event.shiftKey && document.activeElement === last ) {
			event.preventDefault();
			first.focus();
		}
	}

	/* A single polite live region is reused for every announcement. */
	var liveRegion = null;

	function announce( message ) {
		if ( ! message ) {
			return;
		}

		if ( ! liveRegion ) {
			liveRegion = document.createElement( 'div' );
			liveRegion.className = 'screen-reader-text';
			liveRegion.setAttribute( 'role', 'status' );
			liveRegion.setAttribute( 'aria-live', 'polite' );
			body.appendChild( liveRegion );
		}

		liveRegion.textContent = message;
	}

	var persianDigits = [ '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ];

	function toPersian( value ) {
		return String( value ).replace( /[0-9]/g, function ( digit ) {
			return persianDigits[ parseInt( digit, 10 ) ];
		} );
	}

	/* ---------------------------------------------------------------------
	 * Header: sticky state, measured offset, scroll progress
	 * ------------------------------------------------------------------ */

	function initHeader() {
		var header = qs( '[data-es-header]' );

		if ( ! header ) {
			return;
		}

		var sticky = header.hasAttribute( 'data-es-sticky' );
		var lastScroll = window.pageYOffset;

		var measure = rafThrottle( function () {
			root.style.setProperty( '--es-header-height', header.offsetHeight + 'px' );
		} );

		measure();
		on( window, 'resize', measure, { passive: true } );

		if ( ! sticky ) {
			return;
		}

		on(
			window,
			'scroll',
			rafThrottle( function () {
				var y = window.pageYOffset;

				header.classList.toggle( 'is-stuck', y > 8 );

				// Compact, then reveal again as soon as the visitor scrolls up.
				if ( y > 320 && y > lastScroll ) {
					header.classList.add( 'is-hidden' );
				} else {
					header.classList.remove( 'is-hidden' );
				}

				lastScroll = y;
			} ),
			{ passive: true }
		);
	}

	function initProgress() {
		var progress = qs( '[data-es-progress]' );
		var bar = qs( '[data-es-progress-bar]' );

		if ( ! progress || ! bar ) {
			return;
		}

		var update = rafThrottle( function () {
			var scrollable = document.documentElement.scrollHeight - window.innerHeight;
			var ratio = scrollable > 0 ? window.pageYOffset / scrollable : 0;

			ratio = Math.max( 0, Math.min( 1, ratio ) );
			bar.style.transform = 'scaleX(' + ratio + ')';
			progress.setAttribute( 'aria-hidden', ratio <= 0.01 ? 'true' : 'false' );
		} );

		update();
		on( window, 'scroll', update, { passive: true } );
		on( window, 'resize', update, { passive: true } );
	}

	/* ---------------------------------------------------------------------
	 * Drawer / mobile menu
	 * ------------------------------------------------------------------ */

	function initDrawer() {
		var drawer = qs( '[data-es-drawer]' );
		var openers = qsa( '[data-es-menu-open]' );
		var closers = qsa( '[data-es-menu-close]' );

		if ( ! drawer ) {
			return;
		}

		var panel = qs( '.es-drawer__panel', drawer ) || drawer;
		var lastFocus = null;

		function isOpen() {
			return drawer.classList.contains( 'is-open' );
		}

		function open() {
			if ( isOpen() ) {
				return;
			}

			lastFocus = document.activeElement;
			drawer.hidden = false;
			body.classList.add( 'es-no-scroll' );

			window.requestAnimationFrame( function () {
				drawer.classList.add( 'is-open' );
			} );

			openers.forEach( function ( opener ) {
				opener.setAttribute( 'aria-expanded', 'true' );
			} );

			var first = qs( '[data-es-menu-close]', panel );

			if ( first ) {
				first.focus();
			}

			document.addEventListener( 'keydown', onKeydown );
			window.addEventListener( 'resize', onResize );
		}

		function close() {
			if ( ! isOpen() ) {
				return;
			}

			drawer.classList.remove( 'is-open' );
			body.classList.remove( 'es-no-scroll' );

			openers.forEach( function ( opener ) {
				opener.setAttribute( 'aria-expanded', 'false' );
			} );

			document.removeEventListener( 'keydown', onKeydown );
			window.removeEventListener( 'resize', onResize );

			window.setTimeout( function () {
				drawer.hidden = true;
			}, reducedMotion() ? 0 : 260 );

			if ( lastFocus && typeof lastFocus.focus === 'function' ) {
				lastFocus.focus();
			}
		}

		function onKeydown( event ) {
			if ( event.key === 'Escape' ) {
				event.preventDefault();
				close();

				return;
			}

			if ( event.key === 'Tab' ) {
				trapTab( panel, event );
			}
		}

		function onResize() {
			if ( window.innerWidth > ( settings.breakpoint || 992 ) ) {
				close();
			}
		}

		openers.forEach( function ( opener ) {
			on( opener, 'click', function ( event ) {
				event.preventDefault();
				open();
			} );
		} );

		closers.forEach( function ( closer ) {
			on( closer, 'click', function ( event ) {
				event.preventDefault();
				close();
			} );
		} );

		// Accordion behaviour for nested items inside the drawer only.
		qsa( '.es-drawer__list li.menu-item-has-children', drawer ).forEach( function ( item ) {
			var link = qs( ':scope > a', item );
			var submenu = qs( ':scope > .sub-menu', item );

			if ( ! link || ! submenu ) {
				return;
			}

			submenu.hidden = true;
			submenu.id = submenu.id || 'es-submenu-' + Math.random().toString( 36 ).slice( 2, 8 );

			var toggle = document.createElement( 'button' );

			toggle.type = 'button';
			toggle.className = 'es-drawer__toggle';
			toggle.setAttribute( 'aria-expanded', 'false' );
			toggle.setAttribute( 'aria-controls', submenu.id );
			toggle.setAttribute(
				'aria-label',
				( i18n.openMenu || 'باز کردن منو' ) + ' — ' + ( link.textContent || '' ).trim()
			);
			toggle.innerHTML = '<span class="es-drawer__toggle-icon" aria-hidden="true"></span>';

			on( toggle, 'click', function () {
				var expanded = toggle.getAttribute( 'aria-expanded' ) === 'true';

				toggle.setAttribute( 'aria-expanded', expanded ? 'false' : 'true' );
				submenu.hidden = expanded;
				item.classList.toggle( 'is-expanded', ! expanded );
			} );

			link.parentNode.insertBefore( toggle, link.nextSibling );
		} );
	}

	/* ---------------------------------------------------------------------
	 * Desktop navigation keyboard support
	 * ------------------------------------------------------------------ */

	function initNavKeyboard() {
		qsa( '.es-nav__list > li.menu-item-has-children' ).forEach( function ( item ) {
			var link = qs( ':scope > a', item );
			var submenu = qs( ':scope > .sub-menu', item );

			if ( ! link || ! submenu ) {
				return;
			}

			link.setAttribute( 'aria-haspopup', 'true' );
			link.setAttribute( 'aria-expanded', 'false' );

			function setExpanded( expanded ) {
				link.setAttribute( 'aria-expanded', expanded ? 'true' : 'false' );
				item.classList.toggle( 'is-open', expanded );
			}

			on( item, 'mouseenter', function () {
				setExpanded( true );
			} );

			on( item, 'mouseleave', function () {
				setExpanded( false );
			} );

			on( item, 'focusin', function () {
				setExpanded( true );
			} );

			on( item, 'focusout', function ( event ) {
				if ( ! item.contains( event.relatedTarget ) ) {
					setExpanded( false );
				}
			} );

			on( link, 'keydown', function ( event ) {
				if ( event.key === 'ArrowDown' ) {
					event.preventDefault();
					setExpanded( true );

					var first = qs( 'a', submenu );

					if ( first ) {
						first.focus();
					}
				}

				if ( event.key === 'Escape' ) {
					setExpanded( false );
					link.focus();
				}
			} );

			on( submenu, 'keydown', function ( event ) {
				if ( event.key === 'Escape' ) {
					setExpanded( false );
					link.focus();
				}
			} );
		} );
	}

	/* ---------------------------------------------------------------------
	 * Search overlay with live REST suggestions
	 * ------------------------------------------------------------------ */

	function initSearch() {
		var panel = qs( '[data-es-search-panel]' );

		if ( ! panel ) {
			return;
		}

		var openers = qsa( '[data-es-search-open]' );
		var closers = qsa( '[data-es-search-close]', panel );
		var input = qs( '[data-es-search-input]', panel );
		var results = qs( '[data-es-search-results]', panel );
		var lastFocus = null;
		var controller = null;
		var activeIndex = -1;

		function isOpen() {
			return panel.classList.contains( 'is-open' );
		}

		function clearResults() {
			activeIndex = -1;

			if ( results ) {
				results.innerHTML = '';
				results.classList.remove( 'has-results' );
			}
		}

		function open() {
			if ( isOpen() ) {
				return;
			}

			lastFocus = document.activeElement;
			panel.hidden = false;

			window.requestAnimationFrame( function () {
				panel.classList.add( 'is-open' );
			} );

			openers.forEach( function ( opener ) {
				opener.setAttribute( 'aria-expanded', 'true' );
			} );

			if ( input ) {
				window.setTimeout( function () {
					input.focus();
					input.select();
				}, reducedMotion() ? 0 : 60 );
			}

			document.addEventListener( 'keydown', onKeydown );
		}

		function close() {
			if ( ! isOpen() ) {
				return;
			}

			panel.classList.remove( 'is-open' );
			clearResults();

			if ( controller ) {
				controller.abort();
				controller = null;
			}

			openers.forEach( function ( opener ) {
				opener.setAttribute( 'aria-expanded', 'false' );
			} );

			document.removeEventListener( 'keydown', onKeydown );

			window.setTimeout( function () {
				panel.hidden = true;
			}, reducedMotion() ? 0 : 260 );

			if ( lastFocus && typeof lastFocus.focus === 'function' ) {
				lastFocus.focus();
			}
		}

		function items() {
			return results ? qsa( 'a', results ) : [];
		}

		function highlight( next ) {
			var anchors = items();

			if ( ! anchors.length ) {
				return;
			}

			anchors.forEach( function ( anchor ) {
				anchor.classList.remove( 'is-active' );
			} );

			activeIndex = ( next + anchors.length ) % anchors.length;

			var current = anchors[ activeIndex ];

			current.classList.add( 'is-active' );
			current.scrollIntoView( { block: 'nearest' } );
		}

		function onKeydown( event ) {
			if ( event.key === 'Escape' ) {
				event.preventDefault();
				close();

				return;
			}

			if ( event.key === 'ArrowDown' ) {
				event.preventDefault();
				highlight( activeIndex + 1 );

				return;
			}

			if ( event.key === 'ArrowUp' ) {
				event.preventDefault();
				highlight( activeIndex - 1 );

				return;
			}

			if ( event.key === 'Enter' && activeIndex > -1 ) {
				var anchor = items()[ activeIndex ];

				if ( anchor ) {
					event.preventDefault();
					window.location.href = anchor.href;
				}

				return;
			}

			if ( event.key === 'Tab' ) {
				var dialog = qs( '.es-search__dialog', panel );

				if ( dialog ) {
					trapTab( dialog, event );
				}
			}
		}

		function render( posts ) {
			if ( ! results ) {
				return;
			}

			results.innerHTML = '';
			activeIndex = -1;

			if ( ! posts.length ) {
				var empty = document.createElement( 'p' );

				empty.className = 'es-search__empty';
				empty.textContent = i18n.error || 'نتیجه‌ای یافت نشد.';
				results.appendChild( empty );
				results.classList.remove( 'has-results' );

				return;
			}

			var list = document.createElement( 'ul' );

			list.className = 'es-search__list';

			posts.forEach( function ( post ) {
				var item = document.createElement( 'li' );
				var anchor = document.createElement( 'a' );
				var title = document.createElement( 'span' );
				var type = document.createElement( 'span' );
				var thumb = post.thumbnail;
				var label = post.subtype || post.type;

				anchor.className = 'es-search__result';
				anchor.href = post.url;
				anchor.setAttribute( 'role', 'option' );

				title.className = 'es-search__result-title';
				title.textContent = post.title || post.url;

				type.className = 'es-search__result-type';
				type.textContent = label;

				if ( thumb ) {
					var img = document.createElement( 'img' );

					img.className = 'es-search__result-thumb';
					img.src = thumb;
					img.alt = '';
					img.loading = 'lazy';
					img.width = 48;
					img.height = 48;
					anchor.appendChild( img );
				}

				anchor.appendChild( title );
				anchor.appendChild( type );
				item.appendChild( anchor );
				list.appendChild( item );
			} );

			results.appendChild( list );
			results.classList.add( 'has-results' );
		}

		var request = debounce( function () {
			var term = input ? input.value.trim() : '';

			if ( term.length < 2 || ! settings.restUrl ) {
				clearResults();

				return;
			}

			if ( controller ) {
				controller.abort();
			}

			controller = typeof AbortController === 'function' ? new AbortController() : null;

			var url =
				settings.restUrl +
				'search?search=' +
				encodeURIComponent( term ) +
				'&per_page=6&_fields=id,title,url,subtype,type,thumbnail';

			results.classList.add( 'is-loading' );

			window
				.fetch( url, {
					credentials: 'same-origin',
					headers: { Accept: 'application/json' },
					signal: controller ? controller.signal : undefined,
				} )
				.then( function ( response ) {
					if ( ! response.ok ) {
						throw new Error( 'HTTP ' + response.status );
					}

					return response.json();
				} )
				.then( function ( data ) {
					results.classList.remove( 'is-loading' );
					render( Array.isArray( data ) ? data : [] );
				} )
				.catch( function ( error ) {
					results.classList.remove( 'is-loading' );

					if ( error && error.name === 'AbortError' ) {
						return;
					}

					render( [] );
				} );
		}, 260 );

		openers.forEach( function ( opener ) {
			on( opener, 'click', function ( event ) {
				event.preventDefault();
				open();
			} );
		} );

		closers.forEach( function ( closer ) {
			on( closer, 'click', function ( event ) {
				event.preventDefault();
				close();
			} );
		} );

		on( input, 'input', request );
		on( input, 'keydown', function ( event ) {
			if ( event.key === 'ArrowDown' || event.key === 'ArrowUp' ) {
				event.preventDefault();
				highlight( activeIndex + ( event.key === 'ArrowDown' ? 1 : -1 ) );
			}
		} );
	}

	/* ---------------------------------------------------------------------
	 * Back to top
	 * ------------------------------------------------------------------ */

	function initToTop() {
		var button = qs( '[data-es-to-top]' );

		if ( ! button ) {
			return;
		}

		function sync() {
			if ( window.pageYOffset > 600 ) {
				button.hidden = false;
			} else {
				button.hidden = true;
			}
		}

		sync();
		on( window, 'scroll', rafThrottle( sync ), { passive: true } );

		on( button, 'click', function () {
			scrollTo( 0 );
		} );
	}

	/* ---------------------------------------------------------------------
	 * Reveal on scroll
	 * ------------------------------------------------------------------ */

	function initReveal() {
		if ( reducedMotion() || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}

		var targets = qsa( '[data-es-reveal], .es-card, .es-stat, .es-feature-card, .es-section__head' ).slice( 0, 120 );

		if ( ! targets.length ) {
			return;
		}

		// Never fade content the visitor can already see: only the blocks that
		// start below the fold get the entrance transition.
		var belowFold = targets.filter( function ( target ) {
			return target.getBoundingClientRect().top > window.innerHeight * 0.85;
		} );

		if ( ! belowFold.length ) {
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( entry.isIntersecting ) {
						entry.target.classList.add( 'is-visible' );
						observer.unobserve( entry.target );
					}
				} );
			},
			{ rootMargin: '0px 0px -6% 0px', threshold: 0.08 }
		);

		belowFold.forEach( function ( target, index ) {
			target.classList.add( 'es-reveal' );
			target.style.transitionDelay = ( index % 3 ) * 45 + 'ms';
			observer.observe( target );
		} );

		// Safety net: if anything is still hidden after four seconds (broken
		// observer, zero height, print), show it rather than losing content.
		window.setTimeout( function () {
			belowFold.forEach( function ( target ) {
				target.classList.add( 'is-visible' );
			} );
		}, 4000 );
	}

	/* ---------------------------------------------------------------------
	 * Interactive services list
	 * ------------------------------------------------------------------ */

	function initServices() {
		var container = qs( '[data-es-services]' );

		if ( ! container ) {
			return;
		}

		var items = qsa( '[data-es-service]', container );
		var preview = qs( '[data-es-service-preview]', container );
		var label = qs( '[data-es-service-current]', container );

		if ( ! items.length ) {
			return;
		}

		function activate( item, focus ) {
			items.forEach( function ( other ) {
				var toggle = qs( '[data-es-service-toggle]', other );
				var panel = qs( '.es-service__body', other );
				var isActive = other === item;

				other.classList.toggle( 'is-active', isActive );

				if ( toggle ) {
					toggle.setAttribute( 'aria-expanded', isActive ? 'true' : 'false' );
				}

				if ( panel ) {
					panel.hidden = ! isActive;
				}
			} );

			var position = items.indexOf( item );

			if ( label ) {
				label.textContent = toPersian( position + 1 );
			}

			var panel = qs( '.es-service__body', item );
			var image = panel ? panel.getAttribute( 'data-image' ) : '';

			if ( preview && image && preview.getAttribute( 'src' ) !== image ) {
				preview.classList.add( 'is-swapping' );

				var loader = new Image();

				loader.onload = function () {
					preview.src = image;
					preview.classList.remove( 'is-swapping' );
				};

				loader.onerror = function () {
					preview.classList.remove( 'is-swapping' );
				};

				loader.src = image;
			}

			if ( focus ) {
				var toggle = qs( '[data-es-service-toggle]', item );

				if ( toggle ) {
					toggle.focus();
				}
			}
		}

		items.forEach( function ( item, index ) {
			var toggle = qs( '[data-es-service-toggle]', item );

			if ( ! toggle ) {
				return;
			}

			on( toggle, 'click', function () {
				activate( item );
			} );

			on( toggle, 'keydown', function ( event ) {
				var next = null;

				if ( event.key === 'ArrowDown' ) {
					next = items[ ( index + 1 ) % items.length ];
				} else if ( event.key === 'ArrowUp' ) {
					next = items[ ( index - 1 + items.length ) % items.length ];
				} else if ( event.key === 'Home' ) {
					next = items[ 0 ];
				} else if ( event.key === 'End' ) {
					next = items[ items.length - 1 ];
				}

				if ( next ) {
					event.preventDefault();
					activate( next, true );
				}
			} );

			if ( window.matchMedia && window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches ) {
				on( item, 'mouseenter', function () {
					if ( ! item.classList.contains( 'is-active' ) ) {
						activate( item );
					}
				} );
			}
		} );
	}

	/* ---------------------------------------------------------------------
	 * Project gallery lightbox
	 * ------------------------------------------------------------------ */

	function initLightbox() {
		var gallery = qs( '[data-es-lightbox]' );
		var items = qsa( '[data-es-lightbox-item]' );

		if ( ! gallery || ! items.length ) {
			return;
		}

		var overlay = null;
		var imageEl = null;
		var captionEl = null;
		var counterEl = null;
		var current = 0;
		var lastFocus = null;
		var touchStart = null;

		function build() {
			overlay = document.createElement( 'div' );
			overlay.className = 'es-lightbox';
			overlay.setAttribute( 'role', 'dialog' );
			overlay.setAttribute( 'aria-modal', 'true' );
			overlay.setAttribute( 'aria-label', 'گالری تصاویر پروژه' );
			overlay.hidden = true;
			overlay.innerHTML =
				'<div class="es-lightbox__stage">' +
				'<img class="es-lightbox__image" alt="" src="">' +
				'</div>' +
				'<p class="es-lightbox__caption"></p>' +
				'<span class="es-lightbox__counter" aria-hidden="true"></span>' +
				'<button type="button" class="es-lightbox__close" aria-label="' +
				( i18n.close || 'بستن' ) +
				'">&times;</button>' +
				'<button type="button" class="es-lightbox__nav es-lightbox__nav--prev" aria-label="قبلی">&#8250;</button>' +
				'<button type="button" class="es-lightbox__nav es-lightbox__nav--next" aria-label="بعدی">&#8249;</button>';

			body.appendChild( overlay );

			imageEl = qs( '.es-lightbox__image', overlay );
			captionEl = qs( '.es-lightbox__caption', overlay );
			counterEl = qs( '.es-lightbox__counter', overlay );

			on( qs( '.es-lightbox__close', overlay ), 'click', close );
			on( qs( '.es-lightbox__nav--prev', overlay ), 'click', function () {
				step( -1 );
			} );
			on( qs( '.es-lightbox__nav--next', overlay ), 'click', function () {
				step( 1 );
			} );
			on( overlay, 'click', function ( event ) {
				if ( event.target === overlay ) {
					close();
				}
			} );

			on( overlay, 'touchstart', function ( event ) {
				touchStart = event.changedTouches[ 0 ].clientX;
			}, { passive: true } );

			on( overlay, 'touchend', function ( event ) {
				if ( touchStart === null ) {
					return;
				}

				var delta = event.changedTouches[ 0 ].clientX - touchStart;

				touchStart = null;

				if ( Math.abs( delta ) > 45 ) {
					step( delta > 0 ? -1 : 1 );
				}
			}, { passive: true } );
		}

		function show( index ) {
			current = ( index + items.length ) % items.length;

			var item = items[ current ];
			var thumb = qs( 'img', item );

			imageEl.src = item.getAttribute( 'href' );
			imageEl.alt = thumb ? thumb.getAttribute( 'alt' ) || '' : '';
			captionEl.textContent = thumb ? thumb.getAttribute( 'alt' ) || '' : '';
			counterEl.textContent = toPersian( current + 1 ) + ' / ' + toPersian( items.length );
		}

		function step( delta ) {
			show( current + delta );
		}

		function onKeydown( event ) {
			if ( event.key === 'Escape' ) {
				close();
			} else if ( event.key === 'ArrowRight' ) {
				step( -1 );
			} else if ( event.key === 'ArrowLeft' ) {
				step( 1 );
			} else if ( event.key === 'Tab' ) {
				trapTab( overlay, event );
			}
		}

		function open( index ) {
			if ( ! overlay ) {
				build();
			}

			lastFocus = document.activeElement;
			overlay.hidden = false;
			show( index );

			window.requestAnimationFrame( function () {
				overlay.classList.add( 'is-open' );
			} );

			body.classList.add( 'es-no-scroll' );
			document.addEventListener( 'keydown', onKeydown );
			qs( '.es-lightbox__close', overlay ).focus();
		}

		function close() {
			if ( ! overlay || overlay.hidden ) {
				return;
			}

			overlay.classList.remove( 'is-open' );
			body.classList.remove( 'es-no-scroll' );
			document.removeEventListener( 'keydown', onKeydown );

			window.setTimeout(
				function () {
					overlay.hidden = true;
				},
				reducedMotion() ? 0 : 220
			);

			if ( items[ current ] ) {
				items[ current ].focus();
			}
		}

		items.forEach( function ( item, index ) {
			on( item, 'click', function ( event ) {
				event.preventDefault();
				open( index );
			} );
		} );
	}

	/* ---------------------------------------------------------------------
	 * Copy to clipboard
	 * ------------------------------------------------------------------ */

	function initCopy() {
		var buttons = qsa( '[data-es-copy]' );

		if ( ! buttons.length ) {
			return;
		}

		function legacyCopy( text ) {
			var field = document.createElement( 'textarea' );

			field.value = text;
			field.setAttribute( 'readonly', 'readonly' );
			field.style.position = 'fixed';
			field.style.top = '-1000px';
			body.appendChild( field );
			field.select();

			var ok = false;

			try {
				ok = document.execCommand( 'copy' );
			} catch ( e ) {
				ok = false;
			}

			body.removeChild( field );

			return ok;
		}

		function done( button ) {
			button.classList.add( 'es-copy-done' );
			announce( i18n.copied || 'کپی شد' );

			window.setTimeout( function () {
				button.classList.remove( 'es-copy-done' );
			}, 1600 );
		}

		buttons.forEach( function ( button ) {
			on( button, 'click', function () {
				var text = button.getAttribute( 'data-es-copy' ) || window.location.href;

				if ( navigator.clipboard && window.isSecureContext ) {
					navigator.clipboard.writeText( text ).then(
						function () {
							done( button );
						},
						function () {
							if ( legacyCopy( text ) ) {
								done( button );
							}
						}
					);

					return;
				}

				if ( legacyCopy( text ) ) {
					done( button );
				}
			} );
		} );
	}

	/* ---------------------------------------------------------------------
	 * Table of contents scroll spy
	 * ------------------------------------------------------------------ */

	function initToc() {
		var toc = qs( '[data-es-toc]' );

		if ( ! toc ) {
			return;
		}

		var links = qsa( 'a[href^="#"]', toc );
		var sections = links
			.map( function ( link ) {
				return document.getElementById( link.getAttribute( 'href' ).slice( 1 ) );
			} )
			.filter( Boolean );

		links.forEach( function ( link ) {
			on( link, 'click', function ( event ) {
				var target = document.getElementById( link.getAttribute( 'href' ).slice( 1 ) );

				if ( ! target ) {
					return;
				}

				event.preventDefault();

				var offset = parseInt( root.style.getPropertyValue( '--es-header-height' ), 10 ) || 84;
				var top = target.getBoundingClientRect().top + window.pageYOffset - offset - 16;

				scrollTo( top );

				if ( window.history && window.history.pushState ) {
					window.history.pushState( null, '', link.getAttribute( 'href' ) );
				}

				link.focus( { preventScroll: true } );
			} );
		} );

		if ( ! sections.length || ! ( 'IntersectionObserver' in window ) ) {
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( ! entry.isIntersecting ) {
						return;
					}

					links.forEach( function ( link ) {
						link.classList.toggle( 'is-active', link.getAttribute( 'href' ) === '#' + entry.target.id );
					} );
				} );
			},
			{ rootMargin: '-84px 0px -70% 0px', threshold: 0 }
		);

		sections.forEach( function ( section ) {
			observer.observe( section );
		} );
	}

	/* ---------------------------------------------------------------------
	 * Content helpers: tables and external links
	 * ------------------------------------------------------------------ */

	function initTables() {
		qsa( '.es-entry table, .woocommerce-Tabs-panel table' ).forEach( function ( table ) {
			if ( table.parentNode && table.parentNode.classList.contains( 'es-table-wrap' ) ) {
				return;
			}

			var wrap = document.createElement( 'div' );

			wrap.className = 'es-table-wrap';
			wrap.setAttribute( 'tabindex', '0' );
			wrap.setAttribute( 'role', 'region' );
			wrap.setAttribute( 'aria-label', 'جدول قابل اسکرول' );
			table.parentNode.insertBefore( wrap, table );
			wrap.appendChild( table );
		} );
	}

	function initExternalLinks() {
		qsa( 'a[target="_blank"]' ).forEach( function ( link ) {
			var rel = link.getAttribute( 'rel' ) || '';

			if ( rel.indexOf( 'noopener' ) === -1 ) {
				link.setAttribute( 'rel', ( rel + ' noopener noreferrer' ).trim() );
			}
		} );
	}

	/* ---------------------------------------------------------------------
	 * WooCommerce cart counter (fragments)
	 * ------------------------------------------------------------------ */

	function initCart() {
		var link = qs( '[data-es-cart-count]' );

		if ( ! link || ! settings.ajaxUrl ) {
			return;
		}

		var pending = false;
		var timer = null;

		function refresh() {
			if ( pending ) {
				return;
			}

			pending = true;

			window
				.fetch( settings.ajaxUrl + '?wc-ajax=get_refreshed_fragments', {
					credentials: 'same-origin',
					headers: { 'X-Requested-With': 'XMLHttpRequest' },
				} )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( data ) {
					pending = false;

					if ( ! data || ! data.fragments ) {
						return;
					}

					Object.keys( data.fragments ).forEach( function ( selector ) {
						var html = data.fragments[ selector ];

						if ( ! html ) {
							return;
						}

						qsa( selector ).forEach( function ( element ) {
							if ( element.classList.contains( 'es-header-action--cart' ) ) {
								element.outerHTML = html;
							}
						} );
					} );
				} )
				.catch( function () {
					pending = false;
				} );
		}

		function refreshSoon() {
			window.clearTimeout( timer );
			timer = window.setTimeout( refresh, 900 );
		}

		// WooCommerce marks its AJAX add-to-cart links with `.added` once the
		// request finished; watch that instead of polling blindly.
		qsa( '.ajax_add_to_cart, .single_add_to_cart_button' ).forEach( function ( button ) {
			on( button, 'click', refreshSoon );

			if ( 'MutationObserver' in window && button.classList.contains( 'ajax_add_to_cart' ) ) {
				var observer = new MutationObserver( function () {
					if ( button.classList.contains( 'added' ) ) {
						refresh();
					}
				} );

				observer.observe( button, { attributes: true, attributeFilter: [ 'class' ] } );
			}
		} );

		on( document.body, 'submit', function ( event ) {
			if ( event.target && event.target.matches && event.target.matches( 'form.cart' ) ) {
				refreshSoon();
			}
		} );
	}

	/* ---------------------------------------------------------------------
	 * Boot
	 * ------------------------------------------------------------------ */

	function init() {
		initHeader();
		initProgress();
		initDrawer();
		initNavKeyboard();
		initSearch();
		initToTop();
		initServices();
		initLightbox();
		initCopy();
		initToc();
		initTables();
		initExternalLinks();
		initCart();
		initReveal();
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', init );
	} else {
		init();
	}
} )();
