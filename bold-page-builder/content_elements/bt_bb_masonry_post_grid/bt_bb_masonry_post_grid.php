<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class bt_bb_masonry_post_grid extends BT_BB_Element {

	function __construct() {
		parent::__construct();
		add_action( 'wp_ajax_bt_bb_get_grid', array( __CLASS__, 'bt_bb_get_grid_callback' ) );
		add_action( 'wp_ajax_nopriv_bt_bb_get_grid', array( __CLASS__, 'bt_bb_get_grid_callback' ) );
	}

	static function bt_bb_get_grid_callback() {
		// phpcs:disable WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended -- Public read-only endpoint (wp_ajax_nopriv): only echoes already-escaped published-post markup. The nonce is verified for the editor/logged-in path; the 'ignore_nonce' escape disables it in contexts where a fresh per-request nonce can't be embedded in the markup. All values below are sanitized before use.
		$ignore_nonce = isset( $_POST['ignore-nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['ignore-nonce'] ) ) : '';
		if ( 'ignore_nonce' !== $ignore_nonce ) {
			check_ajax_referer( 'bt-bb-masonry-post-grid-nonce', 'bt-bb-masonry-post-grid-nonce' );
		}
		$number    = isset( $_POST['number'] ) ? intval( $_POST['number'] ) : 0;
		$offset    = isset( $_POST['offset'] ) ? intval( $_POST['offset'] ) : 0;
		$category  = isset( $_POST['category'] ) ? sanitize_text_field( urldecode( wp_unslash( $_POST['category'] ) ) ) : ''; // urldecode first (a pre-decode sanitize would strip %XX octets), then sanitize the decoded slug
		// show is URL-encoded JSON; sanitize_text_field() would strip the percent-encoded octets and destroy the JSON, so decode + cast to a boolean whitelist instead (see bt_bb_decode_show_flags).
		$show      = isset( $_POST['show'] ) ? bt_bb_decode_show_flags( wp_unslash( $_POST['show'] ), array( 'category', 'date', 'author', 'comments', 'excerpt', 'share' ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$post_type = isset( $_POST['post-type'] ) ? sanitize_text_field( wp_unslash( $_POST['post-type'] ) ) : '';
		// phpcs:enable WordPress.Security.NonceVerification.Missing, WordPress.Security.NonceVerification.Recommended
		bt_bb_masonry_post_grid::dump_grid( $number, $offset, $category, $show, $post_type );
		die();
	}
	
	static function dump_grid( $number, $offset, $category, $show, $post_type ) {

		// $show arrives as a decoded boolean array (see bt_bb_decode_show_flags).

		$output = '';

		$posts = bt_bb_get_posts( $number, $offset, $category, $post_type );

		foreach( $posts as $item ) {
			$post_thumbnail_id = get_post_thumbnail_id( $item['ID'] ); 
			$img = wp_get_attachment_image_src( $post_thumbnail_id, 'large' );
			$img_src = isset( $img[0] ) ? $img[0] : '';
			$hw = 0;
			if ( $img_src != '' ) {
				$hw = $img[2] / $img[1];
			}
			$alt = get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true );
			$alt = $alt != '' ? $alt : $item['title'];
			$output .= '<div class="bt_bb_grid_item" data-hw="' . esc_attr( $hw ) . '" data-src="' . esc_url_raw( $img_src ) . '" data-alt="' . esc_attr( $alt ) . '"><div class="bt_bb_grid_item_inner">';
				$output .= '<div class="bt_bb_grid_item_post_thumbnail"><a href="' . esc_url_raw( $item['permalink'] ) . '" title="' . esc_attr( $item['title'] ) . '"></a></div>';
				$output .= '<div class="bt_bb_grid_item_post_content">';

					if ( $show['category'] ) {
						$output .= '<div class="bt_bb_grid_item_category">';
							$output .= $item['category_list'];
						$output .= '</div>';
					}

					if ( $show['date'] || $show['author'] || $show['comments'] ) {
				
						$meta_output = '<div class="bt_bb_grid_item_meta">';

							if ( $show['date'] ) {
								$meta_output .= '<span class="bt_bb_grid_item_date">';
									$meta_output .= $item['date'];
								$meta_output .= '</span>';
							}

							if ( $show['author'] ) {
								$meta_output .= '<span class="bt_bb_grid_item_item_author">';
									$meta_output .= esc_html__( 'by', 'bold-page-builder' ) . ' ' . $item['author'];
								$meta_output .= '</span>';
							}

							if ( $show['comments'] && $item['comments'] != '' ) {
								$meta_output .= '<span class="bt_bb_grid_item_item_comments">';
									$meta_output .= $item['comments'];
								$meta_output .= '</span>';
							}
				
						$meta_output .= '</div>';
		
						$output .= $meta_output;
		
					}

					$output .= '<h5 class="bt_bb_grid_item_post_title"><a href="' . esc_url_raw( $item['permalink'] ) . '" title="' . esc_attr( $item['title'] ) . '">' . wp_kses_post( $item['title'] ) . '</a></h5>';

					if ( $show['excerpt'] ) {
						$output .= '<div class="bt_bb_grid_item_post_excerpt">' . wp_kses_post( $item['excerpt'] ) . '</div>';
					}

					if ( $show['share'] ) {
						$output .= '<div class="bt_bb_grid_item_post_share">' . wp_kses_post( $item['share'] ) . '</div>';
					}

				$output .= '</div></div>';
			$output .= '</div>';
		}
		
		$allowed = array(
			'a' => array(
				'class'       => true,
				'data-ico-fa' => true,
				'href'        => true,
				'rel'         => true,
				'title'       => true,
				'target'      => true,
			),
			'div' => array(
				'class'    => true,
				'data-hw'  => true,
				'data-src' => true,
				'data-alt' => true,
				'style'    => true,
			),
			'span' => array(
				'class' => true,
			),
			'img' => array(
				'src' => true,
				'alt' => true,
			),
			'h1' => array(
				
			),
			'h2' => array(
				
			),
			'h3' => array(
				
			),
			'h4' => array(
				
			),
			'h5' => array(
				'class' => true,
			),
			'h6' => array(
				
			),
			'ul' => array(
				'class' => true,
			),
			'li' => array(
				
			)
		);
		
		echo wp_kses( $output, $allowed );
	}

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'post_type'       => 'post',
			'number'          => '',
			'auto_loading'    => '',
			'columns'         => '',
			'gap'             => '',
			'category'        => '',
			'category_filter' => '',
			'show_category'   => '',
			'show_date'       => '',
			'show_author'     => '',
			'show_comments'   => '',
			'show_excerpt'    => '',
			'ignore_nonce'	  => '',
			'show_share'      => ''
		) ), $atts, $this->shortcode ) );

		wp_enqueue_script( 'jquery-masonry' );

		wp_enqueue_script( 
			'bt_bb_post_grid',
			plugin_dir_url( __FILE__ ) . 'bt_bb_post_grid.js',
			array( 'jquery' ),
			BT_BB_VERSION
		);
		
		wp_localize_script( 'bt_bb_post_grid', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		$class = array( $this->shortcode, 'bt_bb_grid_container' );

		if ( $el_class != '' ) {
			$class[] = $el_class;
		}	

		$id_attr = '';
		if ( $el_id != '' ) {
			$id_attr = ' ' . 'id="' . esc_attr( $el_id ) . '"';
		}

		$style_attr = '';
		$el_style = apply_filters( $this->shortcode . '_style', $el_style, $atts );
		if ( $el_style != '' ) {
			$style_attr = ' ' . 'style="' . esc_attr( $el_style ) . '"';
		}

		if ( $columns != '' ) {
			$class[] = $this->prefix . 'columns' . '_' . $columns;
		}
		
		if ( $gap != '' ) {
			$class[] = $this->prefix . 'gap' . '_' . $gap;
		}
		
		if ( $number > 1000 || $number == '' ) {
			$number = 1000;
		} else if ( $number < 1 ) {
			$number = 1;
		}
		
		$category = str_replace( ' ', '', $category );

		$show = array( 'category' => false, 'date' => false, 'author' => false, 'comments' => false, 'excerpt' => false, 'share' => false );
		if ( $show_category == 'show_category' ) {
			$show['category'] = true;
		}
		if ( $show_date == 'show_date' ) {
			$show['date'] = true;
		}
		if ( $show_author == 'show_author' ) {
			$show['author'] = true;
		}
		if ( $show_comments == 'show_comments' ) {
			$show['comments'] = true;
		}
		if ( $show_excerpt == 'show_excerpt' ) {
			$show['excerpt'] = true;
		}
		if ( $show_share == 'show_share' ) {
			$show['share'] = true;
		}

		$output = '';
		
		if ( $category_filter == 'yes' ) {
			if ( $post_type == 'post' ) {
				$cat_arr = get_categories();
				$cats = array();
				if ( $category != '' ) {
					$cat_slug_arr = explode( ',', $category );
					
					$cat_id_arr = get_terms( array( 'taxonomy' => 'category',  'fields' => 'ids' , 'slug' => $cat_slug_arr)  );
					foreach ( $cat_arr as $cat ) {
						if ( in_array( $cat->slug, $cat_slug_arr ) || in_array( $cat->parent, $cat_id_arr ) ) {
							$cats[] = $cat;
						}
					}
				} else {
					$cats = $cat_arr;
				}
			} else if ( $post_type == 'portfolio' ) {
				$cat_arr = get_terms( 'portfolio_category' );
				$cats = array();
				if ( ! is_wp_error( $cat_arr ) ) {
					if ( $category != '' ) {
						$cat_slug_arr = explode( ',', $category );
						foreach ( $cat_arr as $cat ) {
							if ( in_array( $cat->slug, $cat_slug_arr ) ) {
								$cats[] = $cat;
							}
						}
					} else {
						$cats = $cat_arr;
					}
				} else {
					$output .= $cat_arr->get_error_message();
				}
			}

			if ( ! is_wp_error( $cats ) ) {
				if ( count( $cats ) > 0 ) {
					$output .= '<div class="bt_bb_post_grid_filter">';
						$output .= '<span class="bt_bb_post_grid_filter_item active" data-category="' . $category . '">' . esc_html__( 'All', 'bold-page-builder' ) . '</span>';
							foreach ( $cats as $cat ) {
								$output .= '<span class="bt_bb_post_grid_filter_item" data-category="' . esc_attr( $cat->slug ) . '">' . $cat->name . '</span>';
							}
					$output .= '</div>';
				}
			} else {
				$output .= $cats->get_error_message();
			}
		}

		do_action( $this->shortcode . '_before_extra_responsive_param' );
		foreach ( $this->extra_responsive_data_override_param as $p ) {
			if ( ! is_array( $atts ) || ! array_key_exists( $p, $atts ) ) continue;
			$this->responsive_data_override_class(
				$class, $data_override_class,
				apply_filters( $this->shortcode . '_responsive_data_override', array(
					'prefix' => $this->prefix,
					'param' => $p,
					'value' => $atts[ $p ],
				) )
			);
		}
		
		$class = apply_filters( $this->shortcode . '_class', $class, $atts );

		$output .= '<div class="bt_bb_masonry_post_grid_content bt_bb_grid_hide" data-bt-bb-masonry-post-grid-nonce="' . esc_attr( wp_create_nonce( 'bt-bb-masonry-post-grid-nonce' ) ) . '" data-number="' . esc_attr( $number ) . '" data-category="' . esc_attr( $category ) . '" data-show="' . esc_attr( urlencode( json_encode( $show ) ) ) . '" data-auto-loading="' . esc_attr( $auto_loading ) . '" data-post-type="' . esc_attr( $post_type ) . '" data-ignore-nonce="' . esc_attr( $ignore_nonce ) . '"><div class="bt_bb_grid_sizer"></div></div>';

		$output .= '<div class="bt_bb_post_grid_loader"></div>';

		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . ' data-columns="' . esc_attr( $columns ) . '">' . $output . '</div>';

		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

	}

	function map_shortcode() {
		
		$array = array();
		
		if ( post_type_exists( 'portfolio' ) ) {
			$array = array( array( 'param_name' => 'post_type', 'type' => 'dropdown', 'heading' => esc_html__( 'Post Type', 'bold-page-builder' ), 'description' => esc_html__( 'Masonry post grid (deprecated, use css post grid element)', 'bold-page-builder' ), 'preview' => true,
				'value' => array(
					esc_html__( 'Post', 'bold-page-builder' ) => 'post',
					esc_html__( 'Portfolio', 'bold-page-builder' ) => 'portfolio',
				)
			) );
		}
		
		$array = array_merge( $array, array(
			array( 'param_name' => 'number', 'type' => 'textfield', 'heading' => esc_html__( 'Number of items', 'bold-page-builder' ), 'description' => esc_html__( 'Enter number of items or leave empty to show all (up to 1000)', 'bold-page-builder' ), 'preview' => true ),
			array( 'param_name' => 'auto_loading', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-page-builder' ) => 'auto_loading' ), 'heading' => esc_html__( 'Load more items on scroll', 'bold-page-builder' ), 'preview' => true
			),
			array( 'param_name' => 'columns', 'type' => 'dropdown', 'heading' => esc_html__( 'Columns', 'bold-page-builder' ), 'preview' => true,
				'value' => array(
					esc_html__( '1', 'bold-page-builder' ) => '1',
					esc_html__( '2', 'bold-page-builder' ) => '2',
					esc_html__( '3', 'bold-page-builder' ) => '3',
					esc_html__( '4', 'bold-page-builder' ) => '4',
					esc_html__( '5', 'bold-page-builder' ) => '5',
					esc_html__( '6', 'bold-page-builder' ) => '6'
				)
			),
			array( 'param_name' => 'gap', 'type' => 'dropdown', 'heading' => esc_html__( 'Gap', 'bold-page-builder' ),
				'value' => array(
					esc_html__( 'No gap', 'bold-page-builder' ) => 'no_gap',
					esc_html__( 'Small', 'bold-page-builder' ) => 'small',
					esc_html__( 'Normal', 'bold-page-builder' ) => 'normal',
					esc_html__( 'Large', 'bold-page-builder' ) => 'large'
				)
			),
			array( 'param_name' => 'category', 'type' => 'textfield', 'heading' => esc_html__( 'Category', 'bold-page-builder' ), 'description' => esc_html__( 'Enter category slug or leave empty to show all', 'bold-page-builder' ), 'preview' => true ),
			array( 'param_name' => 'category_filter', 'type' => 'dropdown', 'heading' => esc_html__( 'Category filter', 'bold-page-builder' ),
				'value' => array(
					esc_html__( 'No', 'bold-page-builder' ) => 'no',
					esc_html__( 'Yes', 'bold-page-builder' ) => 'yes'
				)
			),
			array( 'param_name' => 'show_category', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-page-builder' ) => 'show_category' ), 'heading' => esc_html__( 'Show category', 'bold-page-builder' ), 'preview' => true
			),
			array( 'param_name' => 'show_date', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-page-builder' ) => 'show_date' ), 'heading' => esc_html__( 'Show date', 'bold-page-builder' ), 'preview' => true
			),
			array( 'param_name' => 'show_author', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-page-builder' ) => 'show_author' ), 'heading' => esc_html__( 'Show author', 'bold-page-builder' ), 'preview' => true
			),
			array( 'param_name' => 'show_comments', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-page-builder' ) => 'show_comments' ), 'heading' => esc_html__( 'Show number of comments', 'bold-page-builder' ), 'preview' => true
			),
			array( 'param_name' => 'show_excerpt', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-page-builder' ) => 'show_excerpt' ), 'heading' => esc_html__( 'Show excerpt', 'bold-page-builder' ), 'preview' => true
			),
			array( 'param_name' => 'show_share', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-page-builder' ) => 'show_share' ), 'heading' => esc_html__( 'Show share icons', 'bold-page-builder' ), 'preview' => true 
			),
			array( 'param_name' => 'ignore_nonce', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-page-builder' ) => 'ignore_nonce' ), 'heading' => esc_html__( 'Skip nonce verification', 'bold-page-builder' ), 'description' => esc_html__( 'If using long-term caching, either skip nonce verification (less secure) or reduce cache TTL to prevent failures.', 'bold-page-builder' ), 'preview' => true )
		) );

		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Masonry Post Grid (deprecated)', 'bold-page-builder' ), 'description' => esc_html__( 'Use Post Grid element', 'bold-page-builder' ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => $array
		) );
	} 
}