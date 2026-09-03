<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class BT_BB_State {
	static $fonts_added = array();
	static $font_subsets_added = array();
}

if ( ! function_exists( 'bt_bb_hex2rgb' ) ) {
	function bt_bb_hex2rgb( $hex ) {
		if ( strpos( $hex, '#' ) !== false ) {
			$hex = str_replace( '#', '', $hex );
			if ( strlen( $hex ) == 3 ) {
				$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
				$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
				$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
			} else {
				$r = hexdec( substr( $hex, 0, 2 ) );
				$g = hexdec( substr( $hex, 2, 2 ) );
				$b = hexdec( substr( $hex, 4, 2 ) );
			}
			$rgb = array( $r, $g, $b );
		} else {
			$hex = str_replace( 'rgba(', '', $hex );
			$hex = str_replace( 'rgb(', '', $hex );
			$hex = str_replace( ')', '', $hex );
			$hex = str_replace( ' ', '', $hex );
			$arr = explode( ',', $hex );
			return array( $arr[0], $arr[1], $arr[2] );
		}
		return $rgb;
	}
}

if ( ! function_exists( 'bt_bb_get_allowed_url_schemes' ) ) {
	/**
	 * Schemes an element link is allowed to use.
	 *
	 * Deliberately an allow list, not a block list: a block list only ever knows
	 * the spellings someone already thought of, and browsers accept many more.
	 * 'javascript' is not on this list and must not be added back -- see the
	 * note in bt_bb_get_permalink_by_slug().
	 *
	 * A site that genuinely needs another scheme can add it in code:
	 *
	 *     add_filter( 'bt_bb_allowed_url_schemes', function( $schemes ) {
	 *         $schemes[] = 'callto';
	 *         return $schemes;
	 *     } );
	 */
	function bt_bb_get_allowed_url_schemes( $link = '' ) {
		return apply_filters( 'bt_bb_allowed_url_schemes', array( 'http', 'https', 'mailto', 'tel', 'ftp', 'ftps', 'sms', 'skype', 'whatsapp' ), $link );
	}
}

if ( ! function_exists( 'bt_bb_url_scheme_allowed' ) ) {
	/**
	 * Whether $link carries a scheme the browser would be allowed to execute.
	 *
	 * The test is run against a normalized *copy*: the caller keeps the value the
	 * author typed, because bt_bb_get_url() has to hand a bare page slug back
	 * unchanged. Normalization mirrors what a browser does before it decides what
	 * a URL's scheme is -- resolve HTML entity and percent encodings, then drop
	 * the characters it ignores outright (C0 controls, DEL, space). Without that
	 * last step "java&#9;script:alert(1)" reads as an unknown scheme here and as
	 * javascript: in the browser, which is exactly how the old str_contains()
	 * block list was bypassed.
	 */
	function bt_bb_url_scheme_allowed( $link ) {
		$test = (string) $link;

		// Decoding can expose another layer of encoding ( '&amp;#58;', '%26colon%3B' ),
		// so peel a few times rather than once. ENT_HTML5 matters: '&colon;' is not
		// in the HTML 4.01 entity table PHP uses by default.
		for ( $i = 0; $i < 3; $i++ ) {
			$decoded = rawurldecode( html_entity_decode( $test, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
			if ( $decoded === $test ) {
				break;
			}
			$test = $decoded;
		}

		$test = preg_replace( '/[\x00-\x20\x7f]/', '', $test );

		if ( $test === '' || $test === null ) {
			return true;
		}

		$colon = strpos( $test, ':' );
		if ( $colon === false ) {
			return true; // relative path, anchor or query only -- no scheme to vet.
		}

		// A ':' that comes after the first '/', '?' or '#' is part of the path or
		// query, not a scheme delimiter ( 'about/me:you', '?t=1:2' ).
		foreach ( array( '/', '?', '#' ) as $delimiter ) {
			$position = strpos( $test, $delimiter );
			if ( $position !== false && $position < $colon ) {
				return true;
			}
		}

		$scheme = strtolower( substr( $test, 0, $colon ) );

		// Not a syntactically valid scheme (RFC 3986), so the browser will not
		// treat it as one either -- it is a relative URL with a colon in it.
		if ( ! preg_match( '/^[a-z][a-z0-9+.\-]*$/', $scheme ) ) {
			return true;
		}

		return in_array( $scheme, bt_bb_get_allowed_url_schemes( $link ), true );
	}
}

if ( ! function_exists( 'bt_bb_sanitize_slick_settings' ) ) {
	/**
	 * Make the slider "Additional settings" field safe to write into data-slick.
	 *
	 * esc_attr() is NOT enough here, and never was. It is _wp_specialchars() with
	 * $double_encode = false, so an entity already present in the stored value is
	 * passed through untouched: an author saves "&lt;img src=x onerror=alert(1)&gt;",
	 * the HTML parser decodes the attribute once when it reads it, slick hands the
	 * value to jQuery, and jQuery builds it as markup. That is how the 5.9.8 fix was
	 * bypassed (reported by WPScan). It applies to every setting slick passes on that
	 * way -- prevArrow, nextArrow, appendArrows, appendDots -- not to one of them.
	 *
	 * Three steps, and all three are load bearing:
	 *
	 *   1. peel every layer of entity encoding, so what we inspect is what the
	 *      browser will hand to slick after it decodes the attribute value;
	 *   2. drop '<' and '>', so nothing in the field can become markup;
	 *   3. escape with double encoding ON, so the browser's single decode reproduces
	 *      step 2 byte for byte and no encoding can smuggle a '<' back in.
	 *
	 * A site that genuinely needs custom arrow markup can take it back in code. The
	 * filter is handed the escaped string and the raw one, and a callback that
	 * overrides it owns the escaping from that point on:
	 *
	 *     add_filter( 'bt_bb_slick_additional_settings', function( $safe, $raw ) {
	 *         return $safe;
	 *     }, 10, 2 );
	 */
	function bt_bb_sanitize_slick_settings( $settings ) {
		$raw = $settings;
		$settings = (string) $settings;

		// Decoding can expose another layer of encoding ( '&amp;lt;' ), so peel a few
		// times rather than once. ENT_HTML5 matters here for the same reason it does
		// in bt_bb_url_scheme_allowed() above.
		for ( $i = 0; $i < 5; $i++ ) {
			$decoded = html_entity_decode( $settings, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
			if ( $decoded === $settings ) {
				break;
			}
			$settings = $decoded;
		}

		$settings = str_replace( array( '<', '>' ), '', $settings );

		// htmlspecialchars(), not esc_attr(): the fourth argument is the whole point.
		$settings = htmlspecialchars( $settings, ENT_QUOTES, 'UTF-8', true );

		return apply_filters( 'bt_bb_slick_additional_settings', $settings, $raw );
	}
}

if ( ! function_exists( 'bt_bb_get_url' ) ) {
	function bt_bb_get_url( $link, $post_type = 'page' ) {
		if ( substr( $link, 0, 4 ) == 'www.' ) {
			return 'http://' . $link;
		}
		return bt_bb_get_permalink_by_slug( $link, $post_type );
	}
}

if ( ! function_exists( 'bt_bb_get_permalink_by_slug' ) ) {
	function bt_bb_get_permalink_by_slug( $link, $post_type = 'page' ) {
		// Scheme allow list. This replaced a str_contains() block list on three
		// literal spellings of 'javascript:', which a control character inside the
		// scheme walked straight past ( "java\tscript:alert(1)" ): the value reached
		// the href intact through esc_attr, and the browser stripped the tab and ran
		// it. Note the callers print this with esc_attr and NOT esc_url, on purpose
		// -- see the comments in bt_bb_button.php / bt_bb_headline.php -- so this
		// function is the only thing standing between an author and a live
		// javascript: URL.
		if ( ! bt_bb_url_scheme_allowed( $link ) ) {
			return '#';
		} else if (
			// Distinct question from the one above: these prefixes decide whether the
			// value is already a URL or is a bare page slug to look up. Do not merge
			// the two lists -- widening this one silently widens what is treated as a
			// URL, and narrowing it breaks slug resolution.
			$link != '' &&
			$link != '#' &&
			substr( $link, 0, 5 ) != 'http:' &&
			substr( $link, 0, 6 ) != 'https:' &&
			substr( $link, 0, 7 ) != 'mailto:' &&
			substr( $link, 0, 4 ) != 'tel:'
		) {
			$page = get_page_by_path( $link, OBJECT, $post_type ); // object-cached; replaces a direct $wpdb query (PCP DirectDatabaseQuery / NoCaching)
			if ( $page ) {
				return get_permalink( $page );
			}
		}
		return $link;
	}
}

if ( ! function_exists( 'bt_bb_get_color_scheme_param_array' ) ) {
	function bt_bb_get_color_scheme_param_array() {
		$color_scheme_arr = array( esc_html__( 'Inherit', 'bold-page-builder' ) => '' );

		$color_scheme_arr_temp = bt_bb_get_color_scheme_array();

		if ( isset( $color_scheme_arr_temp[0] ) && $color_scheme_arr_temp[0] != '' ) {
			foreach( $color_scheme_arr_temp as $item ) {
				if ( $item != '' ) {
					$item_arr = explode( ';', $item, 4 );
					if ( count( $item_arr ) == 4 ) {
						$color_scheme_arr[ $item_arr[1] ] = $item_arr[0];
					} else {
						$color_scheme_arr[ $item_arr[0] ] = $item_arr[0];
					}
				}
			}
		}
		return $color_scheme_arr;
	}
}

if ( ! function_exists( 'bt_bb_add_color_schemes' ) ) {
	function bt_bb_add_color_schemes() {

		$color_scheme_arr = bt_bb_get_color_scheme_array();

		if ( $color_scheme_arr[0] != '' ) {
			$scheme_id = 1;
			$i = 0;
			foreach( $color_scheme_arr as $item ) {
	
				$scheme_id = $i + 1;

				$color_scheme = explode( ';', $color_scheme_arr[ $i ] );
				
				$this_scheme = $color_scheme[0];
				
				if ( count( $color_scheme ) == 4 ) {
					array_shift( $color_scheme );
				}

				require( 'color_scheme_template.php' );

				if ( file_exists( get_stylesheet_directory() . '/bold-page-builder/content_elements_misc/color_scheme_template.php' ) ) {
					$temp_css = $custom_css;
					require( get_stylesheet_directory() . '/bold-page-builder/content_elements_misc/color_scheme_template.php' );
					$custom_css = $temp_css . $custom_css;
				} else if ( file_exists( get_template_directory() . '/bold-page-builder/content_elements_misc/color_scheme_template.php' ) ) {
					$temp_css = $custom_css;
					require( get_template_directory() . '/bold-page-builder/content_elements_misc/color_scheme_template.php' );
					$custom_css = $temp_css . $custom_css;
				}
				
				$custom_css = apply_filters( 'bt_bb_color_schemes', $custom_css );

				if ( $custom_css != '' ) {
					$custom_css = str_replace( ': ', ':', $custom_css );
					$custom_css = str_replace( array( "\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $custom_css);
					$custom_css = preg_replace( '/\/\*.*?\*\//', ' ', $custom_css );
					wp_add_inline_style( 'bt_bb_content_elements', $custom_css );
				}
				
				$i++;
			}
		}
	}
}

if ( ! function_exists( 'bt_bb_get_color_scheme_id' ) ) {
	function bt_bb_get_color_scheme_id( $scheme_name ) {

		$color_scheme_arr = bt_bb_get_color_scheme_array();

		$scheme_id = 1;
		$i = 0;
		foreach( $color_scheme_arr as $item ) {
			$i++;
			$item_arr = explode( ';', $item, 4 );
			if ( $item_arr[0] == $scheme_name ) {
				$scheme_id = $i;
				break;
			}
		}
		return $scheme_id;
	}
}

if ( ! function_exists( 'bt_bb_get_color_scheme_colors_by_id' ) ) {
	function bt_bb_get_color_scheme_colors_by_id( $scheme_id ) {
		if ( !is_numeric( $scheme_id ) || intval( $scheme_id ) < 0 ) return false;
		$color_scheme_arr = bt_bb_get_color_scheme_array();
		if ( !isset( $color_scheme_arr[ $scheme_id ] ) ) return false;
		$color_scheme = explode( ';', $color_scheme_arr[ $scheme_id ] );
		// var_dump( array_slice( $color_scheme, -2, 2 ) );
		$color_scheme = array_map( 'trim', array_slice( $color_scheme, -2, 2 ) );
		return $color_scheme;
	}
}

if ( ! function_exists( 'bt_bb_get_color_scheme_array' ) ) {
	function bt_bb_get_color_scheme_array() {

		$options = get_option( 'bt_bb_settings' );
		if ( ! $options ) {
			$color_scheme_arr = array();
		} else {
			$color_schemes = $options['color_schemes'];
			$color_scheme_arr = preg_split( '/(\r\n|\n|\r)/', $color_schemes );
		}
		
		// Remove rows without ';'
		$color_scheme_arr = array_filter( $color_scheme_arr, function ( $x ) { return strpos( $x, ';' ) !== false; });

		$color_scheme_arr = apply_filters( 'bt_bb_color_scheme_arr', $color_scheme_arr );

		return $color_scheme_arr;
	}
}

if ( ! function_exists( 'bt_bb_enqueue_google_font' ) ) {
	function bt_bb_enqueue_google_font( $font, $subset, $font_load_extension = "" ) {
		
		if ( property_exists( 'BoldThemesFramework', 'custom_fonts' ) && property_exists( 'BoldThemesFramework', 'custom_fonts_enqueue' ) ) {
			if ( array_key_exists( $font, BoldThemesFramework::$custom_fonts ) ) {
				BoldThemesFramework::$custom_fonts_enqueue[ $font ] = $font;
				return; // do not enqueue as google font
			}
		}

		if ( ! in_array( $font, BT_BB_State::$fonts_added ) ) {
			
			$default_load_font_extension = ':ital,wght@0,100;0,200;0,300;0,400;0,500;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,700;1,800;1,900';
			// $default_load_font_extension = ':ital,opsz,wght@0,9..144,100;0,9..144,200;0,9..144,300;0,9..144,400;1,9..144,100;1,9..144,200;1,9..144,300;1,9..144,400';
			
			$font_load_extension = $font_load_extension != '' ? $font_load_extension : $default_load_font_extension;

			BT_BB_State::$fonts_added[ $font ] = $font . $font_load_extension;

			$subset = preg_replace( '/\s+/', '', $subset );
			$subset_arr = explode( ',', $subset );

			BT_BB_State::$font_subsets_added = BT_BB_State::$font_subsets_added + $subset_arr;

			add_action( 'wp_footer', 'bt_bb_enqueue_google_fonts' );

		}
	}
}

if ( ! function_exists( 'bt_bb_enqueue_google_fonts' ) ) {
	function bt_bb_enqueue_google_fonts() {

		if ( count( BT_BB_State::$fonts_added ) > 0 ) {

			$font_families = array();

			foreach( BT_BB_State::$fonts_added as $item ) {
				// $font_families[] = urldecode( $item ) . ':100,200,300,400,500,600,700,800,900,100italic,200italic,300italic,400italic,500italic,600italic,700italic,800italic,900italic';
				// $font_families[] = urldecode( $item ) . $default_load_font_extension;
				$font_families[] = urldecode( $item );
			}

			$query_args = array(
				'family' => implode( '&family=', $font_families ),
				'subset' => implode( ',', BT_BB_State::$font_subsets_added ),
				'display' => ( 'swap' ),
			);

			$font_url = add_query_arg( $query_args, 'https://fonts.googleapis.com/css2' );
			
			wp_enqueue_style( 'bt_bb_google_fonts', $font_url, array(), BT_BB_VERSION );

		}
	}
}

/**
 * Generate pagination html used in grid layout with sticky posts
 *
 * @param int $_page
 * @param int $number
 * @param string $post_type
 * @param string $cat_slug Category slug
 * @param string $ajax loading or not
 * @param string $url
 * @return html for grid pagination
 */
if ( ! function_exists( 'bt_bb_get_grid_pagination' ) ) {
	function bt_bb_get_grid_pagination( $_page, $number, $post_type, $category, $ajax = true, $url = '', $class = '' ) {
		$_page		= intval($_page);
		// $class is concatenated into class="..." further down. No core element calls
		// this helper -- themes do -- so escape once here rather than at each sink.
		$class		= esc_attr( $class );
		$wp_query	= array();

		$cat_slug_portfolio	= array();
		if ( $post_type == 'portfolio' && $category != '' ) {
			if ( ! is_array( $category ) ) {
				$cat_slug_portfolio = str_replace( ' ', '', $category );
				$cat_slug_portfolio = explode( ',', $cat_slug_portfolio );
			}
		}
		
		if ( $post_type == 'portfolio' ) {
			if ( is_array( $cat_slug_portfolio ) && !empty( $cat_slug_portfolio ) ) {				
				$wp_query = new WP_Query( array( 'post_type' => 'portfolio', 'tax_query' => array( array( 'taxonomy' => 'portfolio_category', 'field' => 'slug', 'terms' => $cat_slug_portfolio ) ), 'post_status' => 'publish', 'posts_per_page' => $number, 'paged'	=> $_page ) );
			} else {
				$wp_query = new WP_Query( array( 'post_type' => 'portfolio', 'post_status' => 'publish', 'posts_per_page' => $number, 'paged' => $_page ) );
			}
		} else {
			if ( $category != '' ) {		
				$wp_query = new WP_Query( array( 'category_name' => $category, 'post_status' => 'publish', 'post_type' => 'post', 'posts_per_page' => $number, 'paged'	=> $_page ) );
			} else {
				$wp_query = new WP_Query( array( 'post_status' => 'publish', 'post_type' => 'post', 'posts_per_page' => $number, 'paged' => $_page ) );
			}
		}
		
		$big = 999999999;
		$link = get_pagenum_link($big);
		if ( $ajax ){
			$link = $url == "" ? get_pagenum_link($big) : $url . 'page/' . $big;
		}

		$pages = paginate_links(array(
			'base'			=> str_replace($big, '%#%', esc_url( $link )),
			'format'		=> '?paged=%#%',
			'current'		=> max(1, $_page),
			'total'			=> $wp_query->max_num_pages,
			'type'			=> 'array',
			'aria_current'	=> 'page',
			'show_all '		=> false,
			'prev_next'		=> true,
			'prev_text'		=> is_rtl() ? '&rarr;' : '&larr;',
			'next_text'		=> is_rtl() ? '&larr;' : '&rarr;',
			'end_size'		=> 1,
			'mid_size'		=> 2, 
		));

		$output = '';
		
		if (is_array($pages)) {
			$output .= '<nav><ul class="page-numbers">';
				foreach ($pages as $i => $page) {					
					
					if ($_page == 0 && $i == 0) {
						$page =  '<a class="page-numbers ' . $class . '" data-page="1" href="'.esc_url( get_pagenum_link( 1 ) ).'">1</a>';
						$output .= "<li class='active'>$page</li>";
					} else {

						$page_number = bt_bb_get_grid_pagenum($page, '">', '</a>');

						if ( $page_number == '&rarr;' || $page_number == '&larr;' ){
							$_page = intval($_page) == 0 ? intval($_page)+1 : $_page;
							if ( is_rtl() ) {
								$page_number = $page_number == '&rarr;' ? intval($_page)-1 : $page_number;
								$page_number = $page_number == '&larr;' ? intval($_page)+1 : $page_number;
							}else{
								$page_number = $page_number == '&rarr;' ? intval($_page)+1 : $page_number;
								$page_number = $page_number == '&larr;' ? intval($_page)-1 : $page_number;
							}
						}	
						
						$_page_number = $page_number == '' ? -1 : intval($page_number);

						if ( intval($_page) == intval($_page_number) ){
							$page = str_replace('page-numbers"', 'page-numbers ' . $class . '" data-page="'.$_page_number.'"', $page);
							$output .= "<li class='active'>$page</li>";
						}else{
							$page = str_replace('page-numbers"', 'page-numbers ' . $class . '" data-page="'.$_page_number.'"', $page);
							$output .= "<li>$page</li>";
						}
					}	
				}
			$output .= '</ul></nav>';				
		}

		return $output;

	}
}

if ( ! function_exists( 'bt_bb_get_grid_pagenum' ) ) {
	function bt_bb_get_grid_pagenum($string, $start, $end){
		$string = ' ' . $string;
		$ini = strpos($string, $start);
		if ($ini == 0) return '';
		$ini += strlen($start);
		$len = strpos($string, $end, $ini) - $ini;
		return substr($string, $ini, $len);
	}
}

/**
 * Get array of data for a range of posts, used in grid layout with sticky posts
 *
 * @param int $number
 * @param int $offset
 * @param string $cat_slug Category slug
 * @param string $post_type
 * @param string $related
 * @param string $sticky_in_grid
 * @return array Array of data for a range of posts
 */
 if ( ! function_exists( 'bt_bb_get_posts_with_sticky_posts' ) ) {
	function bt_bb_get_posts_with_sticky_posts( $number, $offset, $category, $post_type = 'post' ) {
		$posts_data = array();

		$sticky			= true;
		$sticky_array	= get_option( 'sticky_posts' );

		$cat_slug_portfolio		= array();
		if ( $post_type == 'portfolio' && $category != '' ) {
			if ( ! is_array( $category ) ) {
				$cat_slug_portfolio = str_replace( ' ', '', $category );
				$cat_slug_portfolio = explode( ',', $cat_slug_portfolio );
			}
		}
		
		// Get all posts by post type and category
		$recent_posts_q = array();
		if ( $post_type == 'portfolio' ) {
			if ( is_array( $cat_slug_portfolio ) && !empty( $cat_slug_portfolio ) ) {	
				$recent_posts_q = new WP_Query( array( 'post_type' => 'portfolio', 'tax_query' => array( array( 'taxonomy' => 'portfolio_category', 'field' => 'slug', 'terms' => $cat_slug_portfolio ) ), 
					'post_status' => 'publish', 'posts_per_page' => -1 ) );
			} else {
				$recent_posts_q = new WP_Query( array( 'post_type' => 'portfolio', 'post_status' => 'publish', 'posts_per_page' => -1 ) );
			}
		} else {
			if ( $category != '' ) {
				$recent_posts_q = new WP_Query( array( 'category_name' => $category, 'post_status' => 'publish', 'post_type' => 'post', 'posts_per_page' => -1 ) );
			} else {
				$recent_posts_q = new WP_Query( array( 'post_status' => 'publish', 'post_type' => 'post', 'posts_per_page' => -1 ) );
			}
		}
		
		if ( isset( $recent_posts_q ) && $recent_posts_q->have_posts() ) {
			while ( $recent_posts_q->have_posts() ) {
				$recent_posts_q->the_post();
				$post = get_post();
				$post_author = $post->post_author;
				$post_id = get_the_ID();
				$posts_data[] = bt_bb_get_posts_array_item( $post_type, $post_id, $post_author );						
			}
			wp_reset_postdata();			
		}

		// Order data posts by added sticky post field, sticky posts first
		foreach($posts_data as $key => &$value){
			$is_sticky = in_array( $value['ID'], $sticky_array ) ? 1 : 0;
			$value["sticky"] = $is_sticky;
		}
		unset($value);
		
		usort($posts_data, function ($item1, $item2) {
			return $item2['sticky'] <=> $item1['sticky'];
		});	
		
		// Get part of data posts by offset and number, reset array keys
		$posts_data_return = array_slice( $posts_data, $offset, $number, false ); 
		
		return $posts_data_return;
	}
}

/**
 * Decode a post-grid "show flags" AJAX param into a whitelisted boolean array.
 *
 * The grid elements pass these flags as URL-encoded JSON (e.g. %7B%22date%22%3Atrue%7D).
 * sanitize_text_field() cannot be used on the raw value — it strips every percent-encoded
 * octet and destroys the JSON — so we urldecode, json_decode, and cast each known key to a
 * bool. The result is only ever read in boolean context (never echoed), so the whitelist
 * cast is the correct, complete sanitizer for this input.
 *
 * @param string $raw  Raw (unslashed) URL-encoded JSON value from $_POST.
 * @param array  $keys Allowed flag keys to extract.
 * @return array Map of $keys => bool.
 */
if ( ! function_exists( 'bt_bb_decode_show_flags' ) ) {
	function bt_bb_decode_show_flags( $raw, $keys ) {
		$decoded = json_decode( urldecode( (string) $raw ), true );
		if ( ! is_array( $decoded ) ) {
			$decoded = array();
		}
		$flags = array();
		foreach ( $keys as $key ) {
			$flags[ $key ] = ! empty( $decoded[ $key ] );
		}
		return $flags;
	}
}

/**
 * Get array of data for a range of posts, used in grid layout
 *
 * @param int $number
 * @param int $offset
 * @param string $cat_slug Category slug
 * @param string $post_type
 * @param string $related
 * @param string $sticky_in_grid
 * @return array Array of data for a range of posts
 */
if ( ! function_exists( 'bt_bb_get_posts' ) ) {
	function bt_bb_get_posts( $number, $offset, $cat_slug, $post_type = 'post' ) {

		$posts_data1 = array();
		$posts_data2 = array();
		$posts_data = array();

		$sticky = true;
		$sticky_array = get_option( 'sticky_posts' );
		
		if ( $post_type == 'portfolio' && $cat_slug != '' ) {
			if ( ! is_array( $cat_slug ) ) {
				$cat_slug = str_replace( ' ', '', $cat_slug );
				$cat_slug = explode( ',', $cat_slug );
			}
		}
		
		/* Get only sticky posts */

		if ( $offset == 0 && $sticky && count( $sticky_array ) > 0 && $post_type == 'post' ) {
			if ( $cat_slug != '' ) {
				if ( $post_type == 'portfolio' ) {
					$recent_posts_q_sticky = new WP_Query( array( 'post__in' => $sticky_array, 'posts_per_page' => $number, 'tax_query' => array( array( 'taxonomy' => 'portfolio_category', 'field' => 'slug', 'terms' => $cat_slug ) ), 'post_status' => 'publish', 'ignore_sticky_posts' => 1 ) );
				} else {
					$recent_posts_q_sticky = new WP_Query( array( 'post__in' => $sticky_array, 'posts_per_page' => $number, 'category_name' => $cat_slug, 'post_status' => 'publish', 'ignore_sticky_posts' => 1 ) );
				}
			} else {
				$recent_posts_q_sticky = new WP_Query( array( 'post__in' => $sticky_array, 'posts_per_page' => $number, 'post_status' => 'publish', 'ignore_sticky_posts' => 1 ) );	
			}
			
			$posts_data1 = bt_bb_get_posts_array( $recent_posts_q_sticky, $post_type, array() );
		}
		
		/* Get non sticky posts */

		if ( $number > 0 ) {
			$recent_posts_q = array();
			if ( $post_type == 'portfolio' ) {
				if ( $cat_slug != '' ) {
					$recent_posts_q = new WP_Query( array( 'post_type' => 'portfolio', 'posts_per_page' => $number, 'offset' => $offset, 'tax_query' => array( array( 'taxonomy' => 'portfolio_category', 'field' => 'slug', 'terms' => $cat_slug ) ), 'post_status' => 'publish' ) );
				} else {
					$recent_posts_q = new WP_Query( array( 'post_type' => 'portfolio', 'posts_per_page' => $number, 'offset' => $offset, 'post_status' => 'publish' ) );
				}
			} else {
				if ( $cat_slug != '' ) {
					$recent_posts_q = new WP_Query( array( 'posts_per_page' => $number, 'offset' => $offset, 'category_name' => $cat_slug, 'post_status' => 'publish' ) );
				} else {
					$recent_posts_q = new WP_Query( array( 'posts_per_page' => $number, 'offset' => $offset, 'post_status' => 'publish' ) );
				}
			}
			if ( $sticky ) {
				$posts_data2 = bt_bb_get_posts_array( $recent_posts_q, $post_type, $sticky_array );
			} else {
				$posts_data2 = bt_bb_get_posts_array( $recent_posts_q, $post_type, array() );
			}
		}
		
		$posts_data = array_merge( $posts_data1, $posts_data2 );
		array_splice( $posts_data, $number );
		
		return $posts_data;
	}
}

/**
 * bt_bb_get_posts_data helper function
 *
 * @param object
 * @param array 
 * @return array 
 */
if ( ! function_exists( 'bt_bb_get_posts_array' ) ) {
	function bt_bb_get_posts_array( $recent_posts_q, $post_type, $sticky_arr ) {
		$posts_data = array();
		if ( isset( $recent_posts_q ) && $recent_posts_q->have_posts() ) {
			while ( $recent_posts_q->have_posts() ) {
				$recent_posts_q->the_post();
				$post = get_post();
				$post_author = $post->post_author;
				$post_id = get_the_ID();
				if ( in_array( $post_id, $sticky_arr ) ) {
					continue;
				}
				$posts_data[] = bt_bb_get_posts_array_item( $post_type, $post_id, $post_author );
			}
		}
		wp_reset_postdata();
		
		return $posts_data;
	}
}

/**
 * boldthemes_get_posts_array helper function
 *
 * @return array
 */
if ( ! function_exists( 'bt_bb_get_posts_array_item' ) ) {
	function bt_bb_get_posts_array_item( $post_type, $post_id, $post_author ) {

		$post_data = array();
		$post_data['permalink'] = get_permalink( $post_id );
		$post_data['format'] = get_post_format( $post_id );
		$post_data['title'] = get_the_title( $post_id );

		$post_data['excerpt'] = get_the_excerpt( $post_id );

		$post_data['date'] = date_i18n( get_option( 'date_format' ), strtotime( get_the_time( 'Y-m-d', $post_id ) ) );

		$user_data = get_userdata( $post_author );
		if ( $user_data ) {
			$author = $user_data->data->display_name;
			$author_url = get_author_posts_url( $post_author );
			$post_data['author'] = '<a href="' . esc_url_raw( $author_url ) . '">' . esc_html( $author ) . '</a>';
		} else {
			$post_data['author'] = '';
		}

		if ( $post_type == 'portfolio' ) {
			$post_data['category'] = wp_get_post_terms( $post_id, 'portfolio_category' );
		} else {
			$post_data['category'] = get_the_category( $post_id );
		}

		if ( $post_type == 'portfolio' ) {
			$post_data['category_list'] = get_the_term_list( $post_id, 'portfolio_category' );
		} else {
			$post_data['category_list'] = get_the_category_list( '', '', $post_id );
		}

		$comments_open = comments_open( $post_id );
		$comments_number = get_comments_number( $post_id );
		if ( ! $comments_open && $comments_number == 0 ) {
			$comments_number = false;
		}			

		$post_data['comments'] = $comments_number;
		$post_data['ID'] = $post_id;

		$post_data['share'] = bt_bb_get_share_html( $post_data['permalink'], $post_type );
		
		return $post_data;
	}
}

/**
 * Returns share icons HTML
 *
 * @return string
 */
if ( ! function_exists( 'bt_bb_get_share_html' ) ) {
	function bt_bb_get_share_html( $permalink, $type = 'blog' ) {
		
		$share_html = '';

		// New framework
		if ( function_exists( 'boldthemes_get_option' ) && function_exists( 'boldthemes_share_html' ) ) {
			if ( $type !== '' ) {				
				if (  $type == 'post' ) { $type = 'blog'; }
				if (  $type == 'portfolio' ) { $type = 'pf'; }
				
				$share_slug = is_single() || is_page() || $type == 'page' ? $type . '_single_share' : $type . '_list_share';				
				
				$size			= boldthemes_get_option( $share_slug . '_size' );
				$style			= boldthemes_get_option( $share_slug . '_style' );
				$shape			= boldthemes_get_option( $share_slug . '_shape' );
				$color_scheme	= boldthemes_get_option( $share_slug . '_color_scheme' );				
				$args   = array( 'size' => $size, 'style' => $style, 'shape' => $shape, 'color_scheme' => $color_scheme);
				
				$share_html .= boldthemes_share_html( boldthemes_get_option( $share_slug ), $args );
			}
			return $share_html;
		}
		
		// Old framework
		if ( function_exists( 'boldthemes_get_option' ) && class_exists( 'BoldThemes_Customize_Default' ) ) {
			if (  $type == 'post' ) { $type = 'blog'; }
			if (  $type == 'portfolio' ) { $type = 'pf'; }

			$share_facebook = isset( BoldThemes_Customize_Default::$data[ $type . '_share_facebook' ] ) ? boldthemes_get_option( $type . '_share_facebook' ) : false;
			$share_twitter = isset( BoldThemes_Customize_Default::$data[ $type . '_share_twitter' ] ) ? boldthemes_get_option( $type . '_share_twitter' ) : false;
			$share_linkedin = isset( BoldThemes_Customize_Default::$data[ $type . '_share_linkedin' ] ) ? boldthemes_get_option( $type . '_share_linkedin' ) : false;
			$share_vk = isset( BoldThemes_Customize_Default::$data[ $type . '_share_vk' ] ) ? boldthemes_get_option( $type . '_share_vk' ) : false;
			$share_whatsapp = isset( BoldThemes_Customize_Default::$data[ $type . '_share_whatsapp' ] ) ? boldthemes_get_option( $type . '_share_whatsapp' ) : false;
		} else {
			$share_facebook = true;
			$share_twitter = true;
			$share_linkedin = true;
			$share_vk = true;
			$share_whatsapp = true;
		}
		

		if ( $share_facebook || $share_twitter || $share_linkedin || $share_vk || $share_whatsapp ) {

			if ( $share_facebook ) {
				if ( function_exists( 'boldthemes_get_option' ) ) {
					$share_html .= boldthemes_get_icon_html( array( 'icon' => 'fa_f09a', 'url' => boldthemes_get_share_link( 'facebook', $permalink ), 'el_class' => 'bt_facebook' ) );
				} else {
					$share_html .= do_shortcode( '[bt_bb_icon icon="fa_f09a" url="' . bt_bb_get_share_link( 'facebook', $permalink ) . '"]' );
				}
			}
			if ( $share_twitter ) {
				if ( function_exists( 'boldthemes_get_option' ) ) {
					$share_html .= boldthemes_get_icon_html( array( 'icon' => 'fa_f099', 'url' => boldthemes_get_share_link( 'twitter', $permalink ), 'el_class' => 'bt_twitter' ) );
				} else {
					$share_html .= do_shortcode( '[bt_bb_icon icon="fa_f099" url="' . bt_bb_get_share_link( 'twitter', $permalink ) . '"]' );
				}
			}
			if ( $share_linkedin ) {
				if ( function_exists( 'boldthemes_get_option' ) ) {
					$share_html .= boldthemes_get_icon_html( array( 'icon' => 'fa_f0e1', 'url' => boldthemes_get_share_link( 'linkedin', $permalink ), 'el_class' => 'bt_linkedin' ) );
				} else {
					$share_html .= do_shortcode( '[bt_bb_icon icon="fa_f0e1" url="' . bt_bb_get_share_link( 'linkedin', $permalink ) . '"]' );
				}
			}
			if ( $share_vk ) {
				if ( function_exists( 'boldthemes_get_option' ) ) {
					$share_html .= boldthemes_get_icon_html( array( 'icon' => 'fa_f189', 'url' => boldthemes_get_share_link( 'vk', $permalink ), 'el_class' => 'bt_vk' ) );
				} else {
					$share_html .= do_shortcode( '[bt_bb_icon icon="fa_f189" url="' . bt_bb_get_share_link( 'vk', $permalink ) . '"]' );
				}
			}
			if ( $share_whatsapp ) {
				if ( function_exists( 'boldthemes_get_option' ) ) {
					$share_html .= boldthemes_get_icon_html( array( 'icon' => 'fa_f232', 'url' => boldthemes_get_share_link( 'whatsapp', $permalink ), 'el_class' => 'bt_whatsapp' ) );
				} else {
					$share_html .= do_shortcode( '[bt_bb_icon icon="fa_f232" url="' . bt_bb_get_share_link( 'whatsapp', $permalink ) . '"]' );
				}
			}
		}
		return $share_html;
	}
}

/**
 * Share links
 */
if ( ! function_exists( 'bt_bb_get_share_link' ) ) {
	function bt_bb_get_share_link( $service, $url ) {
		if ( $service == 'facebook' ) {
			return 'https://www.facebook.com/sharer/sharer.php?u=' . $url;
		} else if ( $service == 'twitter' ) {
			return 'https://twitter.com/home?status=' . $url;
		} else if ( $service == 'linkedin' ) {
			return 'https://www.linkedin.com/shareArticle?url=' . $url;
		} else if ( $service == 'vk' ) {
			return 'http://vkontakte.ru/share.php?url=' . $url;
		} else if ( $service == 'whatsapp' ) {
			return 'https://api.whatsapp.com/send?text=' . $url;		
		} else {
			return '#';
		}
	}
}