<?php

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
		if ( str_contains( strtolower( $link ), 'javascript:' ) || str_contains( strtolower( $link ), 'javascript&#58;' ) || str_contains( strtolower( $link ), 'javascript&amp;#58;' ) ) {
			return '#';
		} else if (
			$link != '' && 
			$link != '#' && 
			substr( $link, 0, 5 ) != 'http:' && 
			substr( $link, 0, 6 ) != 'https:' && 
			substr( $link, 0, 7 ) != 'mailto:' && 
			substr( $link, 0, 4 ) != 'tel:' 
		) {
			global $wpdb;
			$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= %s", $link, $post_type ) );
			if ( $page ) {
				return get_permalink( $page );
			}
		}
		return $link;
	}
}

if ( ! function_exists( 'bt_bb_get_color_scheme_param_array' ) ) {
	function bt_bb_get_color_scheme_param_array() {
		$color_scheme_arr = array( esc_html__( 'Inherit', 'bold-builder' ) => '' );

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
		
		/*$content_post = get_post();
		$content = $content_post->post_content;
		
		$bt_bb_content = false;
		if ( strpos( $content, '[bt_bb_' ) === 0 ) {
			$bt_bb_content = true;
		}

		if ( ! $bt_bb_content ) {
			return;
		}
		
		$pattern = '/color_scheme="(.*?)"/';
		preg_match_all( $pattern, $content, $matches );
	
		$color_schemes_to_use = array_unique( $matches[1] );*/

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
					//$scheme_id = $this_scheme;
				}

				//if ( in_array( $this_scheme, $color_schemes_to_use ) ) {

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
					echo '<style data-id="bt_bb_color_schemes">' . $custom_css . '</style>';
				}
				
				//}
				
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
			
			wp_enqueue_style( 'bt_bb_google_fonts', $font_url, array(), null );

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