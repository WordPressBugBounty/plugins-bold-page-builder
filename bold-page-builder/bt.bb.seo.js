/**
 * Shared core for SEO plugin integrations (bt.bb.yoast.js, bt.bb.rankmath.js).
 *
 * SEO plugins analyse the raw editor content, which for Bold Builder pages is a
 * tree of [bt_bb_*] shortcodes - headings, images, links and word count are
 * invisible to them. This renders the live builder content to HTML server-side
 * (bt_bb_get_html) and reports it to the integration on every change.
 *
 * Usage: window.bt_bb_seo.watch( function( html ) { ... } )
 *   html - rendered HTML, or null when the content has no builder shortcodes
 *          (or rendering failed) and the SEO plugin should use its own content.
 *   Called once on start and again whenever the rendered result may differ.
 *
 * All integrations share one watcher, so each change is rendered once no matter
 * how many SEO plugins are active. A late subscriber (Yoast subscribes on
 * YoastSEO:ready) immediately receives the latest result.
 */
( function( $ ) {
	'use strict';

	if ( ! window.bt_bb_seo_config ) {
		return;
	}

	var cfg = window.bt_bb_seo_config;

	// While the builder is active, #content is only synced on save/preview/editor
	// switch, so serialize the live builder state instead (same serializer the
	// builder uses for #content; it only writes the bt_bb_parse_data_str global).
	function get_source() {
		if ( window.bt_bb && window.bt_bb.editor === 'bb' && window.bt_bb_state_current && typeof window.bt_bb_parse_data_helper === 'function' ) {
			var saved = window.bt_bb_parse_data_str;
			var src = null;
			try {
				window.bt_bb_parse_data_str = '';
				window.bt_bb_parse_data_helper( window.bt_bb_state_current );
				src = window.bt_bb_parse_data_str;
			} catch ( e ) {
				src = null;
			}
			window.bt_bb_parse_data_str = saved;
			if ( src !== null ) {
				return src;
			}
		}
		var ta = document.getElementById( 'content' );
		return ta ? ta.value : '';
	}

	function has_builder_content( src ) {
		return src.indexOf( '[bt_bb_' ) !== -1 || src.indexOf( '[bt_section' ) !== -1;
	}

	var subscribers = [];
	var started = false;
	var has_result = false;
	var current = null;   // latest result passed to subscribers
	var last_src = null;  // shortcode source the latest request was made for
	var xhr = null;
	var timer = null;

	function publish( html ) {
		has_result = true;
		current = html;
		subscribers.slice().forEach( function( callback ) {
			try {
				callback( html );
			} catch ( e ) {
				if ( window.console ) {
					window.console.error( e ); // one failing integration must not block the others
				}
			}
		} );
	}

	function update() {
		var src = get_source();
		if ( src === last_src ) {
			return;
		}
		last_src = src;

		if ( xhr ) {
			xhr.abort();
			xhr = null;
		}

		if ( ! has_builder_content( src ) ) {
			publish( null );
			return;
		}

		var req = xhr = $.ajax( {
			method: 'POST',
			url: cfg.url,
			data: {
				action: 'bt_bb_get_html',
				nonce: cfg.nonce,
				post_id: $( '#post_ID' ).val(),
				content: src
			}
		} );
		req.done( function( response ) {
			if ( req === xhr ) {
				publish( typeof response === 'string' ? response : null );
			}
		} ).fail( function( jqxhr, status ) {
			if ( req === xhr && status !== 'abort' ) {
				publish( null );
			}
		} ).always( function() {
			if ( req === xhr ) {
				xhr = null;
			}
		} );
	}

	function schedule() {
		clearTimeout( timer );
		timer = setTimeout( update, 500 );
	}

	function start() {
		if ( started ) {
			return;
		}
		started = true;

		// Every builder change (add/edit/move/delete/undo/redo) goes through a
		// non-bubbling 'bt_bb_event' on .bt_bb_item_list - a capture listener on
		// document still sees it. The debounce lets the builder update its state first.
		document.addEventListener( 'bt_bb_event', schedule, true );

		// Manual edits in the WP editor (Text tab) after "Switch editor".
		$( document ).on( 'input', '#content', schedule );

		$( update );
	}

	function watch( callback ) {
		subscribers.push( callback );
		if ( has_result ) {
			// Subscribed after the first render - hand over the latest result now
			// (async, like every other delivery).
			setTimeout( function() {
				callback( current );
			}, 0 );
		}
		start();
	}

	window.bt_bb_seo = {
		watch: watch,
		get_source: get_source
	};

} )( jQuery );
