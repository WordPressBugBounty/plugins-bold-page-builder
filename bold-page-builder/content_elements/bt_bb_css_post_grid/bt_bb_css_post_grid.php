<?php

class bt_bb_css_post_grid extends BT_BB_Element {

	function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_bt_bb_get_css_grid', array( __CLASS__, 'bt_bb_get_css_grid_callback' ) );
		add_action( 'wp_ajax_nopriv_bt_bb_get_css_grid', array( __CLASS__, 'bt_bb_get_css_grid_callback' ) );
	}
	
	static function bt_bb_get_css_grid_callback() {
		if ( $_POST['ignore-nonce'] != 'ignore_nonce' ) check_ajax_referer( 'bt-bb-css-post-grid-nonce', 'bt-bb-css-post-grid-nonce' );
		bt_bb_css_post_grid::dump_grid( 
				intval( $_POST['number'] ),
				intval( $_POST['offset'] ), 
				sanitize_text_field( urldecode( $_POST['category'] ) ),
				$_POST['show'],
				$_POST['show_superheadline'],
				$_POST['show_subheadline'],
				$_POST['post-type'],
				$_POST['format'],
				$_POST['title_html_tag'],
				$_POST['img_base_size'],
				$_POST['ignore-nonce']
			);
		die();
	}	

	static function dump_grid( $number, $offset, $category, $show, $show_superheadline, $show_subheadline, $post_type, $format, $title_html_tag, $img_base_size ) {
		
		$show				= json_decode( urldecode( $show ), true );
		$show_superheadline = json_decode( urldecode( $show_superheadline ), true );
		$show_subheadline	= json_decode( urldecode( $show_subheadline ), true );

		$title_html_tag		= $title_html_tag != '' ? $title_html_tag : 'h5';
		
		$format_arr = explode( ',', $format );

		$output = '';

		$posts = bt_bb_get_posts( $number, $offset, $category, $post_type );
		
		$n = 0;

		foreach( $posts as $item ) {
			$post_thumbnail_id = get_post_thumbnail_id( $item['ID'] ); 
			$img = wp_get_attachment_image_src( $post_thumbnail_id, $img_base_size );
			$img_src = isset( $img[0] ) ? $img[0] : '';
			$hw = 0;
			if ( $img_src != '' ) {
				if ( isset($img[1]) && isset($img[2]) ){
					$hw = $img[2] / $img[1];
				}
			}
			$alt = get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true );
			$alt = $alt != '' ? $alt : $item['title'];

			if ( isset( $format_arr[ $n ] ) ) {
				$tile_format = 'bt_bb_tile_format';
				if ( $format_arr[ $n ] != '' ) {
					$tile_format .= '_' . esc_attr( trim( $format_arr[ $n ] ) );
				} else {
					$tile_format .= '_11';
				}
			}

			$output .= '<div class="bt_bb_grid_item ' . $tile_format . '" data-hw="' . esc_attr( $hw ) . '" data-src="' . esc_url_raw( $img_src ) . '" data-alt="' . esc_attr( $alt ) . '" data-post-format="' . esc_attr( $item['format'] ) . '"><div class="bt_bb_grid_item_inner">';
				$output .= '<div class="bt_bb_grid_item_post_thumbnail"><a href="' . esc_url_raw( $item['permalink'] ) . '" title="' . esc_attr( $item['title'] ) . '"></a></div>';
				$output .= '<div class="bt_bb_grid_item_post_content">';

					if ( $show_superheadline['category'] || $show_superheadline['date'] || $show_superheadline['author'] || $show_superheadline['comments'] ) {

						$meta_output = '<div class="bt_bb_grid_item_meta bt_bb_grid_item_meta_superheadline">';

							if ( $show_superheadline['category'] && $item['category_list'] != '' ) {
								$meta_output .=  '<div class="bt_bb_grid_item_category bt_bb_grid_item_category_superheadline">';
									$meta_output .=  $item['category_list'];
								$meta_output .=  '</div>';
							}

							if ( $show_superheadline['date'] && $item['date'] != '' ) {
								$meta_output .= '<span class="bt_bb_grid_item_date">';
									$meta_output .= $item['date'];
								$meta_output .= '</span>';
							}

							if ( $show_superheadline['author'] && $item['author'] != '' ) {
								$meta_output .= '<span class="bt_bb_grid_item_item_author">';
									$meta_output .= esc_html__( 'by', 'bold-builder' ) . ' ' . $item['author'];
								$meta_output .= '</span>';
							}

							if ( $show_superheadline['comments'] && $item['comments'] != '' ) {
								$meta_output .= '<span class="bt_bb_grid_item_item_comments">';
									$meta_output .= $item['comments'];
								$meta_output .= '</span>';
							}

						$meta_output .= '</div>';

						$output .= $meta_output;

					}

					$output .= '<' . $title_html_tag . ' class="bt_bb_grid_item_post_title"><a href="' . esc_url_raw( $item['permalink'] ) . '" title="' . esc_attr( $item['title'] ) . '">' . $item['title'] . '</a></' . $title_html_tag . '>';

					if ( $show['excerpt'] ) {
						$output .= '<div class="bt_bb_grid_item_post_excerpt">' . $item['excerpt'] . '</div>';
					}

					if ( $show_subheadline['category'] || $show_subheadline['date'] || $show_subheadline['author'] || $show_subheadline['comments'] ) {
				
						$meta_output = '<div class="bt_bb_grid_item_meta bt_bb_grid_item_meta_subheadline">';

							if ( $show_subheadline['category'] && $item['category_list'] != '' ) {
								$meta_output .=  '<div class="bt_bb_grid_item_category bt_bb_grid_item_category_subheadline">';
									$meta_output .= $item['category_list'];
								$meta_output .=  '</div>';
							}

							if ( $show_subheadline['date'] && $item['date'] != '' ) {
								$meta_output .= '<span class="bt_bb_grid_item_date">';
									$meta_output .= $item['date'];
								$meta_output .= '</span>';
							}

							if ( $show_subheadline['author'] && $item['author'] != '' ) {
								$meta_output .= '<span class="bt_bb_grid_item_item_author">';
									$meta_output .= esc_html__( 'by', 'bold-builder' ) . ' ' . $item['author'];
								$meta_output .= '</span>';
							}

							if ( $show_subheadline['comments'] && $item['comments'] != '' ) {
								$meta_output .= '<span class="bt_bb_grid_item_item_comments">';
									$meta_output .= $item['comments'];
								$meta_output .= '</span>';
							}

						$meta_output .= '</div>';

						$output .= $meta_output;

					}

					if ( $show['share'] ) {
						$output .= '<div class="bt_bb_grid_item_post_share">' . $item['share'] . '</div>';
					}

				$output .= '</div></div>';
			$output .= '</div>';
			$n++;
		}
		
		$allowed = array(
			'a' => array(
				'class'       => true,
				'data-ico-fontawesome5regular' => true,
				'data-ico-fontawesome6brands' => true,
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
				'data-post-format' => true,
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
				'class' => true,
			),
			'h2' => array(
				'class' => true,
			),
			'h3' => array(
				'class' => true,
			),
			'h4' => array(
				'class' => true,
			),
			'h5' => array(
				'class' => true,
			),
			'h6' => array(
				'class' => true,
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
			'post_type'					=> 'post',
			'initial_items_number'		=> '6',
			'auto_loading'				=> '',
			'columns'     				=> '3',
			'format'      				=> '',
			'gap'         				=> '',
			'shape'						=> 'inherit',
			'img_base_size'				=> 'large',
			'category'					=> '',
			'category_filter'			=> '',
			'title_html_tag'			=> '',
			'show_in_superheadline'		=> '',
			'show_in_subheadline'		=> '',
			'show_excerpt'				=> '',
			'show_share'				=> '',
			'title_lines'				=> '',
			'excerpt_lines'				=> '',
			'hover_style'				=> '',
			'ignore_nonce'				=> ''
		) ), $atts, $this->shortcode ) );

		wp_enqueue_script( 'jquery-masonry' );

		wp_enqueue_script( 
			'bt_bb_css_post_grid',
			plugin_dir_url( __FILE__ ) . 'bt_bb_css_post_grid.js',
			array( 'jquery' ),
			BT_BB_VERSION
		);

		wp_localize_script( 'bt_bb_css_post_grid', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

		$class = array( $this->shortcode, 'bt_bb_grid_container' );
		$data_override_class = array();


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

		if ( $auto_loading != '' ) {
			$class[] = $this->prefix . 'auto_loading' . '_' . $auto_loading;
		}
		
		if ( $gap != '' ) {
			$class[] = $this->prefix . 'gap' . '_' . $gap;
		}

		if ( $title_lines != '' ) {
			$class[] = $this->prefix . 'title_lines' . '_' . $title_lines;
		}

		if ( $excerpt_lines != '' ) {
			$class[] = $this->prefix . 'excerpt_lines' . '_' . $excerpt_lines;
		}

		if ( $hover_style != '' ) {
			$class[] = $this->prefix . 'hover_style' . '_' . $hover_style;
		}

		if ( $shape != '' ) {
			$class[] = $this->prefix . 'shape' . '_' . $shape;
		}

		if ( $initial_items_number > 1000 || $initial_items_number == '' ) {
			$initial_items_number = 1000;
		} else if ( $initial_items_number < 1 ) {
			$initial_items_number = 1;
		}

		$category = str_replace( ' ', '', $category );
		
		$show_in_superheadline = explode( ' ', $show_in_superheadline );
		$show_superheadline = array( 'category' => false, 'date' => false, 'author' => false, 'comments' => false );
		foreach ( $show_in_superheadline as $show_item ){
			$show_superheadline[$show_item] = true;
		}

		$show_in_subheadline = explode( ' ', $show_in_subheadline );
		$show_subheadline = array( 'category' => false, 'date' => false, 'author' => false, 'comments' => false );
		foreach ( $show_in_subheadline as $show_item ){
			$show_subheadline[$show_item] = true;
		}

		$show = array( 'excerpt' => false, 'share' => false );
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
					$output .= '<div class="bt_bb_post_grid_filter bt_bb_css_post_grid_filter">';
						$output .= '<span class="bt_bb_post_grid_filter_item bt_bb_css_post_grid_filter_item active" data-category="' . esc_attr( $category ) . '">' . esc_html__( 'All', 'bold-builder' ) . '</span>';
							foreach ( $cats as $cat ) {
								$output .= '<span class="bt_bb_post_grid_filter_item bt_bb_css_post_grid_filter_item" data-category="' . esc_attr( $cat->slug ) . '">' . $cat->name . '</span>';
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
		
		$bt_bb_css_post_grid_nonce = wp_create_nonce( 'bt-bb-css-post-grid-nonce' );
		
		$output .= '<div class="bt_bb_css_post_grid_content bt_bb_grid_hide" data-bt-bb-css-post-grid-nonce="' . esc_attr( $bt_bb_css_post_grid_nonce ) . '" data-number="' . esc_attr( $initial_items_number ) . '" data-category="' . esc_attr( $category ) . '" data-show="' . esc_attr( urlencode( json_encode( $show ) ) ) . '" data-show-superheadline="' . esc_attr( urlencode( json_encode( $show_superheadline ) ) ) . '" 
		data-title-html-tag="' . esc_attr( $title_html_tag ) . '" 
		data-img-base-size="' . esc_attr( $img_base_size ) . '" data-show-subheadline="' . esc_attr( urlencode( json_encode( $show_subheadline ) ) ) . '" data-show-subheadline="' . esc_attr( urlencode( json_encode( $show_subheadline ) ) ) . '"  data-format="' . esc_attr( $format ) . '" data-post-type="' . esc_attr( $post_type ) . '"  data-ignore-nonce="' . esc_attr( $ignore_nonce ) . '" data-auto-loading="' . esc_attr( $auto_loading ) . '" data-bt-override-class="' . htmlspecialchars( json_encode( $data_override_class, JSON_FORCE_OBJECT ), ENT_QUOTES, 'UTF-8' ) . '" data-no-posts-text="' . esc_html__( 'No posts found.', 'bold-builder' ) . '">
		</div>';

		$output .= '<div class="bt_bb_post_grid_loader"></div>';

		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . ' data-columns="' . esc_attr( $columns ) . '" data-offset="0">' . $output . '<div  class="bt_bb_css_post_grid_button_holder"><button type="button" class="bt_bb_css_post_grid_button">'. esc_html__( 'Load more', 'bold-builder' ). '</button></div></div>';

		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;
	}

	function map_shortcode() {
		
		if ( BT_BB_FE::$editor_active ) {
			wp_enqueue_script( 'jquery-masonry' );

			wp_enqueue_script( 
				'bt_bb_css_post_grid',
				plugin_dir_url( __FILE__ ) . 'bt_bb_css_post_grid.js',
				array( 'jquery' ),
				BT_BB_VERSION
			);

			wp_localize_script( 'bt_bb_css_post_grid', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
		}

		$array = array();
		
		if ( post_type_exists( 'portfolio' ) ) {
			$array = array( array( 'param_name' => 'post_type', 'type' => 'dropdown', 'heading' => esc_html__( 'Post Type', 'bold-builder' ), 'preview' => true,
				'value' => array(
					esc_html__( 'Post', 'bold-builder' ) 		=> 'post',
					esc_html__( 'Portfolio', 'bold-builder' ) 	=> 'portfolio',
				)
			) );
		}

		$array = array_merge( $array, array(
			array( 'param_name' => 'initial_items_number', 'type' => 'textfield', 'heading' => esc_html__( 'Inital number of items', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. 6', 'bold-builder' ), 'description' => esc_html__( 'Enter initial number of items or leave empty to show all (up to 1000)', 'bold-builder' ), 'default' => '6', 'preview' => true ),
			array( 'param_name' => 'auto_loading', 'type' => 'dropdown', 'heading' => esc_html__( 'Navigation', 'bold-builder' ), 'preview' => true, 
				'value' => array( 
					esc_html__( 'No navigation', 'bold-builder' ) => '',
					esc_html__( 'Auto load more on scroll', 'bold-builder' ) => 'auto_loading',
					esc_html__( 'Load more on button click', 'bold-builder' ) => 'load_more_loading',
				)
			),
			array( 'param_name' => 'columns', 'type' => 'dropdown', 'heading' => esc_html__( 'Columns', 'bold-builder' ), 'default' => '3', 'preview' => true,
				'value' => array(
					esc_html__( '1', 'bold-builder' ) 	=> '1',
					esc_html__( '2', 'bold-builder' ) 	=> '2',
					esc_html__( '3', 'bold-builder' ) 	=> '3',
					esc_html__( '4', 'bold-builder' ) 	=> '4',
					esc_html__( '5', 'bold-builder' ) 	=> '5',
					esc_html__( '6', 'bold-builder' ) 	=> '6'
				)
			),
			array( 'param_name' => 'gap', 'type' => 'dropdown', 'heading' => esc_html__( 'Gap', 'bold-builder' ),
				'value' => array(
					esc_html__( 'No gap', 'bold-builder' )	 	=> 'no_gap',
					esc_html__( 'Small', 'bold-builder' ) 		=> 'small',
					esc_html__( 'Normal', 'bold-builder' ) 		=> 'normal',
					esc_html__( 'Large', 'bold-builder' ) 		=> 'large'
				)
			),
			array( 'param_name' => 'shape', 'type' => 'dropdown', 'heading' => esc_html__( 'Shape', 'bold-builder' ), 
				'value' => array(
					esc_html__( 'Inherit', 'bold-builder' ) 		=> 'inherit',
					esc_html__( 'Square', 'bold-builder' ) 			=> 'square',
					esc_html__( 'Soft Rounded', 'bold-builder' ) 	=> 'rounded',
					esc_html__( 'Hard Rounded', 'bold-builder' ) 	=> 'round'
				)
			),
			array( 'param_name' => 'img_base_size', 'type' => 'dropdown', 'default' => 'large', 'heading' => esc_html__( 'Base image size', 'bold-builder' ),
				'value' => bt_bb_get_image_sizes()
			),
			array( 'param_name' => 'format', 'type' => 'textfield', 'preview' => true, 'heading' => esc_html__( 'Tiles format', 'bold-builder' ), 'placeholder' => esc_html__( '21, 11, 11', 'bold-builder' ), 'description' => esc_html__( 'E.g. 21, 11, 11', 'bold-builder' ) ),
			array( 'param_name' => 'category', 'type' => 'textfield', 'heading' => esc_html__( 'Category', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. music', 'bold-builder' ), 'description' => esc_html__( 'Enter category slug or leave empty to show all', 'bold-builder' ), 'preview' => true ),
			array( 'param_name' => 'category_filter', 'type' => 'dropdown', 'heading' => esc_html__( 'Show category filter', 'bold-builder' ),
				'value' => array(
					esc_html__( 'No', 'bold-builder' ) 			=> 'no',
					esc_html__( 'Yes', 'bold-builder' ) 		=> 'yes'
				)
			),
			array( 'param_name' => 'title_html_tag', 'type' => 'dropdown', 'heading' => esc_html__( 'Title HTML tag', 'bold-builder' ), 'preview' => true,
				'value' => array(
					esc_html__( 'h1', 'bold-builder' ) 	=> 'h1',
					esc_html__( 'h2', 'bold-builder' ) 	=> 'h2',
					esc_html__( 'h3', 'bold-builder' ) 	=> 'h3',
					esc_html__( 'h4', 'bold-builder' ) 	=> 'h4',
					esc_html__( 'h5', 'bold-builder' ) 	=> 'h5',
					esc_html__( 'h6', 'bold-builder' ) 	=> 'h6'
			) ),
			array( 'param_name' => 'show_in_superheadline', 'type' => 'checkbox_group', 'heading' => esc_html__( 'Show in superheadline', 'bold-builder' ),  'preview' => true,
				'value' => array(
					esc_html__( 'Category', 'bold-builder' ) 	=> 'category',
					esc_html__( 'Date', 'bold-builder' ) 		=> 'date',
					esc_html__( 'Author', 'bold-builder' ) 		=> 'author',
					esc_html__( 'Comments', 'bold-builder' ) 	=> 'comments'
				)
			),
			array( 'param_name' => 'show_in_subheadline', 'type' => 'checkbox_group', 'heading' => esc_html__( 'Show in subheadline', 'bold-builder' ),  'preview' => true,
				'value' => array(
					esc_html__( 'Category', 'bold-builder' ) 	=> 'category',
					esc_html__( 'Date', 'bold-builder' ) 		=> 'date',
					esc_html__( 'Author', 'bold-builder' ) 		=> 'author',
					esc_html__( 'Comments', 'bold-builder' ) 	=> 'comments'
				)
			),
			array( 'param_name' => 'show_excerpt', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-builder' ) => 'show_excerpt' ), 'heading' => esc_html__( 'Show excerpt', 'bold-builder' ), 'preview' => true
			),
			array( 'param_name' => 'show_share', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-builder' ) => 'show_share' ), 'heading' => esc_html__( 'Show share icons', 'bold-builder' ), 'preview' => true 
			),
			array( 'param_name' => 'title_lines', 'type' => 'dropdown', 'heading' => esc_html__( 'Title lines', 'bold-builder' ),  'preview' => true, 
				'value' => array(
					esc_html__( 'All', 'bold-builder' )		=> '',
					esc_html__( '4', 'bold-builder' )		=> '4',
					esc_html__( '3', 'bold-builder' )		=> '3',
					esc_html__( '2', 'bold-builder' )		=> '2',
					esc_html__( '1', 'bold-builder' )		=> '1'
				)
			),
			array( 'param_name' => 'excerpt_lines', 'type' => 'dropdown', 'heading' => esc_html__( 'Excerpt lines', 'bold-builder' ),  'preview' => true, 
				'value' => array(
					esc_html__( 'All', 'bold-builder' )		=> '',
					esc_html__( '4', 'bold-builder' )		=> '4',
					esc_html__( '3', 'bold-builder' )		=> '3',
					esc_html__( '2', 'bold-builder' )		=> '2',
					esc_html__( '1', 'bold-builder' )		=> '1'
				)
			),
			array( 'param_name' => 'hover_style', 'type' => 'dropdown', 'heading' => esc_html__( 'Hover style', 'bold-builder' ), 
				'value' => array(
					esc_html__( 'None', 'bold-builder' )						=> '',
					esc_html__( 'Zoom image', 'bold-builder' )					=> 'zoom',
					esc_html__( 'Zoom image & arrow icon', 'bold-builder' )		=> 'arrow'
				)
			),
			array( 'param_name' => 'ignore_nonce', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-builder' ) => 'ignore_nonce' ), 'heading' => esc_html__( 'Skip nonce verification', 'bold-builder' ), 'description' => esc_html__( 'If using long-term caching, either skip nonce verification (less secure) or reduce cache TTL to prevent failures.', 'bold-builder' ), 'preview' => true )
		) );

		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Post Grid', 'bold-builder' ), 'description' => esc_html__( 'Post grid with images', 'bold-builder' ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => $array
		) );
	}
}