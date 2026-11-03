/**
 * Yoast SEO compatibility - feeds rendered builder HTML to Yoast's content
 * analysis via its JS analysis plugin API (registerPlugin / pluginReady /
 * registerModification / pluginReloaded).
 *
 * Rendering and change detection live in bt.bb.seo.js.
 */
( function( $ ) {
	'use strict';

	if ( ! window.bt_bb_seo ) {
		return;
	}

	var NAME = 'BTBBYoastCompatibilityPlugin';
	var html = null; // null = let Yoast use its own content
	var started = false;
	var ready = false;

	function modify_content( content ) {
		return html !== null ? html : content;
	}

	function start() {
		if ( started || ! window.YoastSEO || ! YoastSEO.app || typeof YoastSEO.app.registerPlugin !== 'function' ) {
			return;
		}
		started = true;

		// 'loading' makes Yoast hold its first analysis until pluginReady(),
		// so it starts from rendered HTML rather than raw shortcodes.
		YoastSEO.app.registerPlugin( NAME, { status: 'loading' } );

		window.bt_bb_seo.watch( function( new_html ) {
			var changed = new_html !== html;
			html = new_html;
			if ( ! ready ) {
				ready = true;
				YoastSEO.app.pluginReady( NAME );
				YoastSEO.app.registerModification( 'content', modify_content, NAME, 5 );
			} else if ( changed ) {
				YoastSEO.app.pluginReloaded( NAME );
			}
		} );
	}

	$( window ).on( 'YoastSEO:ready', start );
	start(); // in case Yoast was ready before this script ran

} )( jQuery );
