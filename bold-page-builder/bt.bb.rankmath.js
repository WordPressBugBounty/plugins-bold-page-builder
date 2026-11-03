/**
 * Rank Math SEO compatibility - feeds rendered builder HTML to Rank Math's
 * content analysis via its Content Analysis API:
 * https://rankmath.com/kb/content-analysis-api/
 *
 * Rendering and change detection live in bt.bb.seo.js.
 */
( function() {
	'use strict';

	if ( ! window.wp || ! wp.hooks || ! window.bt_bb_seo ) {
		return;
	}

	var html = null; // null = let Rank Math use its own content

	wp.hooks.addFilter( 'rank_math_content', 'bold-page-builder', function( content ) {
		return html !== null ? html : content;
	} );

	window.bt_bb_seo.watch( function( new_html ) {
		if ( new_html === html ) {
			return;
		}
		html = new_html;
		if ( window.rankMathEditor && typeof window.rankMathEditor.refresh === 'function' ) {
			window.rankMathEditor.refresh( 'content' );
		}
	} );

} )();
