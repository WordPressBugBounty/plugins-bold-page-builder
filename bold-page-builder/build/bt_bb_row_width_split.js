'use strict';

/*
 * Custom dialog control for the Row element's "Columns layout" (row_width) param.
 *
 * The stored value stays a single combined token, exactly as before:
 *   default | boxed_{W} | boxed_{W}_{structure}
 *     W         = 1200 | 1300 | 1400 | 1500 | 1600
 *     structure = left | left_content_wide | right | right_content_wide
 *                 | left_right | left_right_content_wide
 *
 * The editor just presents it as two dropdowns (Width + Structure): we decompose
 * the saved value on open and recompose it on submit/change. Render path and CSS
 * classes are untouched, so existing content is fully backward compatible.
 */

( function() {

	var WIDTH_RE = /^boxed_(1200|1300|1400|1500|1600)(?:_(.+))?$/;

	function decompose( v ) {
		if ( v === undefined || v === null || v === '' || v === 'default' ) {
			return { width: 'default', structure: '', raw: '' };
		}
		var m = WIDTH_RE.exec( v );
		if ( m ) {
			return { width: m[ 1 ], structure: m[ 2 ] ? m[ 2 ] : '', raw: '' };
		}
		// Unknown / custom legacy value — preserve it verbatim.
		return { width: 'default', structure: '', raw: v };
	}

	function compose( width, structure ) {
		if ( ! width || width === 'default' ) {
			return 'default';
		}
		return 'boxed_' + width + ( structure ? '_' + structure : '' );
	}

	function esc_attr( s ) {
		return String( s === undefined || s === null ? '' : s )
			.replace( /&/g, '&amp;' ).replace( /"/g, '&quot;' )
			.replace( /</g, '&lt;' ).replace( />/g, '&gt;' );
	}

	function build_options( map, selected ) {
		var html = '';
		if ( map ) {
			for ( var label in map ) {
				if ( ! Object.prototype.hasOwnProperty.call( map, label ) ) continue;
				var val = map[ label ];
				html += '<option value="' + esc_attr( val ) + '"' + ( val === selected ? ' selected' : '' ) + '>' + label + '</option>';
			}
		}
		return html;
	}

	var uid = 0;

	function build_radios( map, selected, name, disabled ) {
		var html = '';
		if ( map ) {
			for ( var label in map ) {
				if ( ! Object.prototype.hasOwnProperty.call( map, label ) ) continue;
				var val = map[ label ];
				html += '<label class="bt_bb_rws_structure_item">'
					+ '<input type="radio" class="bt_bb_rws_structure" name="' + name + '" value="' + esc_attr( val ) + '"'
						+ ( val === selected ? ' checked' : '' ) + ( disabled ? ' disabled' : '' ) + '>'
					+ '<span>' + label + '</span>'
				+ '</label>';
			}
		}
		return html;
	}

	// expose helpers (used by the inline onchange below and reusable elsewhere)
	window.bt_bb_row_width_split_decompose = decompose;
	window.bt_bb_row_width_split_compose = compose;

	// keep the Structure select disabled while Width = Default (no box to push against)
	window.bt_bb_row_width_split_sync = function( el ) {
		var item = el.closest( '.bt_bb_dialog_item' );
		if ( ! item ) return;
		var disabled = ( el.value === 'default' );
		var radios = item.querySelectorAll( '.bt_bb_rws_structure' );
		for ( var i = 0; i < radios.length; i++ ) radios[ i ].disabled = disabled;
		var sc = item.querySelector( '.bt_bb_row_width_split_structure' );
		if ( sc ) sc.classList.toggle( 'bt_bb_rws_disabled', disabled );
	};

	// render the dialog item content (called in BE via jsx.js, in FE via bt_bb_get_dialog_param)
	window.bt_bb_cf_row_width_split_content = function( arg ) {
		var pv         = arg.param_value || {};
		var widths     = pv.widths || {};
		var structures = pv.structures || {};
		var d          = decompose( arg.val );

		var html = '';
		html += '<b>' + arg.param_heading + '</b>';
		html += '<div class="bt_bb_row_width_split">';
			html += '<div class="bt_bb_row_width_split_col">';
				if ( pv.width_label ) html += '<label class="bt_bb_rws_label">' + pv.width_label + '</label>';
				html += '<select class="bt_bb_rws_width" onchange="bt_bb_row_width_split_sync(this)">' + build_options( widths, d.width ) + '</select>';
			html += '</div>';
			html += '<div class="bt_bb_row_width_split_col bt_bb_row_width_split_structure' + ( d.width === 'default' ? ' bt_bb_rws_disabled' : '' ) + '">';
				if ( pv.structure_label ) html += '<label class="bt_bb_rws_label">' + pv.structure_label + '</label>';
				html += '<div class="bt_bb_rws_structure_options">' + build_radios( structures, d.structure, 'bt_bb_rws_structure_' + ( ++uid ), d.width === 'default' ) + '</div>';
			html += '</div>';
		html += '</div>';
		// preserve an unknown legacy value so on_submit can return it unchanged if untouched
		html += '<input type="hidden" class="bt_bb_rws_raw" value="' + esc_attr( d.raw ) + '">';
		if ( pv.description ) html += '<i class="bt_bb_param_desc">' + pv.description + '</i>';
		return html;
	};

	// read value on submit (BE) / change (FE) — shared via bt_bb_get_edit_item_value
	window.bt_bb_cf_row_width_split_on_submit = function( $item ) {
		var width     = $item.find( '.bt_bb_rws_width' ).val();
		var structure = $item.find( '.bt_bb_rws_structure:checked' ).val();
		if ( structure === undefined ) structure = '';
		var raw       = $item.find( '.bt_bb_rws_raw' ).val();
		if ( raw && width === 'default' && ! structure ) {
			// unknown legacy value, left untouched — keep it verbatim
			return raw;
		}
		return compose( width, structure );
	};

	// short human label for the BE element toolbar preview (preview => true)
	window.bt_bb_cf_row_width_split_param_value_preview = function( val ) {
		var d = decompose( val );
		if ( d.raw ) return d.raw;
		if ( d.width === 'default' ) return 'Default';
		var map = {
			''                        : 'All boxed',
			'left'                    : 'First wide (boxed content)',
			'left_content_wide'       : 'First wide',
			'right'                   : 'Last wide (boxed content)',
			'right_content_wide'      : 'Last wide',
			'left_right'              : 'First & last wide (boxed content)',
			'left_right_content_wide' : 'First & last wide'
		};
		var s = ( map[ d.structure ] !== undefined ) ? map[ d.structure ] : d.structure;
		return d.width + 'px' + ( s ? ' · ' + s : '' );
	};

} )();
