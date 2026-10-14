/**
 * Converge-Reveal — Scroll Reveal Collection (vanilla JS, no dependencies).
 *
 * Writes the scroll progress (0..1) of each widget instance to the CSS custom
 * property --mdc-p on the stage. All movement/opacity maths lives in reveal.css.
 */
( function () {
	'use strict';

	var WRAP = '.mdc-reveal-wrap--animated';
	var STAGE = '.mdc-reveal';
	var instances = new WeakMap();
	var isEditor = function () {
		return !! ( document.body && document.body.classList.contains( 'elementor-editor-active' ) );
	};

	function clamp( value ) {
		return Math.min( 1, Math.max( 0, value ) );
	}

	function create( wrap ) {
		if ( instances.has( wrap ) ) {
			return;
		}

		var stage = wrap.querySelector( STAGE );
		if ( ! stage ) {
			return;
		}

		// Static in the editor unless "Preview animation in editor" is enabled.
		var frozen = isEditor() && ! wrap.classList.contains( 'mdc-reveal-wrap--editor-anim' );
		var desktop = window.matchMedia( '(min-width: 768px)' );
		var ticking = false;
		var active = false;
		var lastP = -1;
		var observer = null;

		function progress() {
			var range = wrap.offsetHeight - stage.offsetHeight;
			if ( range <= 0 ) {
				return 0;
			}
			return clamp( -wrap.getBoundingClientRect().top / range );
		}

		function update() {
			ticking = false;

			if ( ! wrap.isConnected ) {
				destroy();
				return;
			}

			// Desktop/tablet always animate; mobile only with the converge effect.
			var enabled = desktop.matches || wrap.classList.contains( 'mdc-reveal-wrap--m-fx' );
			var p = ( enabled && ! frozen ) ? progress() : 0;
			if ( p !== lastP ) {
				lastP = p;
				stage.style.setProperty( '--mdc-p', p.toFixed( 4 ) );
			}
		}

		function request() {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( update );
			}
		}

		function setActive( on ) {
			if ( on === active ) {
				return;
			}
			active = on;

			if ( on ) {
				window.addEventListener( 'scroll', request, { passive: true } );
				request();
			} else {
				window.removeEventListener( 'scroll', request );
				update(); // settle on 0 or 1 when the widget leaves the viewport
			}
		}

		function destroy() {
			window.removeEventListener( 'scroll', request );
			window.removeEventListener( 'resize', request );
			if ( desktop.removeEventListener ) {
				desktop.removeEventListener( 'change', request );
			}
			if ( observer ) {
				observer.disconnect();
			}
			instances.delete( wrap );
		}

		window.addEventListener( 'resize', request, { passive: true } );
		if ( desktop.addEventListener ) {
			desktop.addEventListener( 'change', request );
		}

		if ( 'IntersectionObserver' in window ) {
			observer = new IntersectionObserver( function ( entries ) {
				entries.forEach( function ( entry ) {
					setActive( entry.isIntersecting );
				} );
			}, { rootMargin: '100px 0px' } );
			observer.observe( wrap );
		} else {
			setActive( true );
		}

		instances.set( wrap, destroy );
		request();
	}

	function initIn( root ) {
		if ( ! root ) {
			return;
		}
		if ( root.matches && root.matches( WRAP ) ) {
			create( root );
		}
		if ( root.querySelectorAll ) {
			root.querySelectorAll( WRAP ).forEach( create );
		}
	}

	function onElementReady( scope ) {
		// Elementor passes a jQuery object (or an element in newer versions).
		initIn( scope && scope.jquery ? scope[ 0 ] : scope );
	}

	function registerElementorHook() {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction( 'frontend/element_ready/mdc-reveal.default', onElementReady );
			return true;
		}
		return false;
	}

	function boot() {
		initIn( document );

		if ( ! registerElementorHook() ) {
			window.addEventListener( 'elementor/frontend/init', registerElementorHook );
		}

		// Editor safety net: widgets are re-rendered on every change.
		if ( isEditor() && 'MutationObserver' in window ) {
			new MutationObserver( function ( mutations ) {
				mutations.forEach( function ( mutation ) {
					mutation.addedNodes.forEach( function ( node ) {
						if ( node.nodeType === 1 ) {
							initIn( node );
						}
					} );
				} );
			} ).observe( document.body, { childList: true, subtree: true } );
		}
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', boot );
	} else {
		boot();
	}
}() );
