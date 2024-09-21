<?php

class bt_bb_latest_posts extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'rows'            	=> '',
			'columns'         	=> '',
			'gap'             	=> '',
			'category'        	=> '',
			'target'          	=> '',
			'image_shape'     	=> '',
			'show_category'   	=> '',
			'show_date'       	=> '',
			'show_author'     	=> '',
			'show_comments'   	=> '',
			'show_excerpt'    	=> '',
			'lazy_load'  		=> 'no'
		) ), $atts, $this->shortcode ) );
		
		$class = array( $this->shortcode );
		
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
		
		if ( $image_shape != '' ) {
			$class[] = $this->prefix . 'image_shape' . '_' . $image_shape;
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
		
		$number = (int)$rows * (int)$columns;
		
		$posts = bt_bb_get_posts( $number, 0, $category );
		
		$output = '';
		
		if ( count( $posts ) == 0 ) {
			$output = esc_html__( 'No posts found.', 'bold-builder' );
		}

		foreach( $posts as $post_item ) {

			$output .= '<div class="' . esc_attr( $this->shortcode ) . '_item">';
				$post_thumbnail_id = get_post_thumbnail_id( $post_item['ID'] );

				if ( $post_thumbnail_id != '' ) {
					$img = wp_get_attachment_image_src( $post_thumbnail_id, $size = 'medium' );
					if ( $img ) {
						$img_src = $img[0];
					} else {
						$img_src = BT_BB_Root::$path . 'img/blank.gif';
					}
					$data_img = '';

					if ( $lazy_load == 'yes' ) {
						$data_img = ' loading="lazy"';
					}
					
					$output .= '<div class="' . esc_attr( $this->shortcode ) . '_item_image"><a href="' . esc_url_raw( $post_item['permalink'] ) . '" target="' . esc_attr( $target ) . '"><img src="' . esc_url_raw( $img_src ) . '" alt="' . esc_attr( $post_item['title'] ) . '" title="' . esc_attr( $post_item['title'] ) . '" ' . $data_img .  '"></a></div>';
				}

				$output .= '<div class="' . esc_attr( $this->shortcode ) . '_item_content">';
				
					if ( $show_category == 'show_category' ) {
						$output .= '<div class="' . esc_attr( $this->shortcode ) . '_item_category">';
							$output .= $post_item['category_list'];
						$output .= '</div>';
					}

					if ( $show_date == 'show_date' || $show_author == 'show_author' || $show_author == 'show_comments' ) {
				
						$meta_output = '<div class="' . esc_attr( $this->shortcode ) . '_item_meta">';

							if ( $show_date == 'show_date' ) {
								$meta_output .= '<span class="' . esc_attr( $this->shortcode ) . '_item_date">';
									$meta_output .= get_the_date( '', $post_item['ID'] );
								$meta_output .= '</span>';
							}

							if ( $show_author == 'show_author' ) {
								$meta_output .= '<span class="' . esc_attr( $this->shortcode ) . '_item_author">';
									$meta_output .= esc_html__( 'by', 'bold-builder' ) . ' ' . $post_item['author'];
								$meta_output .= '</span>';
							}

							if ( $show_comments == 'show_comments' && $post_item['comments'] != '' ) {
								$meta_output .= '<span class="' . esc_attr( $this->shortcode ) . '_item_comments">';
									$meta_output .= $post_item['comments'];
								$meta_output .= '</span>';
							}
				
						$meta_output .= '</div>';
		
						$output .= $meta_output;
		
					}

					$output .= '<h5 class="' . esc_attr( $this->shortcode ) . '_item_title">';
						$output .= '<a href="' . esc_url_raw( $post_item['permalink'] ) . '" target="' . esc_attr( $target ) . '">' . $post_item['title'] . '</a>';
					$output .= '</h5>';
					
					if ( $show_excerpt == 'show_excerpt' ) {
						$output .= '<div class="' . esc_attr( $this->shortcode ) . '_item_excerpt">';
							$output .= $post_item['excerpt'];
						$output .= '</div>';
					}
				$output .= '</div>';
				
			$output .= '</div>';
		}

		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . '>' . $output . '</div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

	}

	function map_shortcode() {

		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Latest Posts', 'bold-builder' ), 'description' => esc_html__( 'List of latest posts', 'bold-builder' ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'rows', 'type' => 'textfield', 'value' => '1', 'heading' => esc_html__( 'Rows', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. 2', 'bold-builder' ), 'preview' => true ),
				array( 'param_name' => 'columns', 'type' => 'dropdown', 'value' => '3', 'heading' => esc_html__( 'Columns', 'bold-builder' ), 'preview' => true,
					'value' => array(
						esc_html__( '1', 'bold-builder' ) => '1',
						esc_html__( '2', 'bold-builder' ) => '2',
						esc_html__( '3', 'bold-builder' ) => '3',
						esc_html__( '4', 'bold-builder' ) => '4',
						esc_html__( '6', 'bold-builder' ) => '6'
					)
				),
				array( 'param_name' => 'gap', 'type' => 'dropdown', 'heading' => esc_html__( 'Gap', 'bold-builder' ), 'preview' => true,
					'value' => array(
						esc_html__( 'No gap', 'bold-builder' ) 	=> 'no_gap',
						esc_html__( 'Normal', 'bold-builder' ) 	=> 'normal',
						esc_html__( 'Small', 'bold-builder' ) 	=> 'small',
						esc_html__( 'Large', 'bold-builder' ) 	=> 'large'
					)
				),
				array( 'param_name' => 'category', 'type' => 'textfield', 'heading' => esc_html__( 'Category', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. music', 'bold-builder' ), 'description' => esc_html__( 'Add category slug or leave empty to show all', 'bold-builder' ), 'preview' => true ),
				array( 'param_name' => 'target', 'type' => 'dropdown', 'heading' => esc_html__( 'Target', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Self (open in same tab)', 'bold-builder' ) 	=> '_self',
						esc_html__( 'Blank (open in new tab)', 'bold-builder' ) 	=> '_blank',
					)
				),
				array( 'param_name' => 'image_shape', 'type' => 'dropdown', 'heading' => esc_html__( 'Image shape', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Square', 'bold-builder' ) 			=> 'square',
						esc_html__( 'Soft Rounded', 'bold-builder' ) 	=> 'rounded',
						esc_html__( 'Hard Rounded', 'bold-builder' ) 	=> 'round'
					)
				),
				array( 'param_name' => 'show_category', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-builder' ) => 'show_category' ), 'heading' => esc_html__( 'Show category', 'bold-builder' ), 'preview' => true
				),
				array( 'param_name' => 'show_date', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-builder' ) => 'show_date' ), 'heading' => esc_html__( 'Show date', 'bold-builder' ), 'preview' => true
				),
				array( 'param_name' => 'show_author', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-builder' ) => 'show_author' ), 'heading' => esc_html__( 'Show author', 'bold-builder' ), 'preview' => true
				),
				array( 'param_name' => 'show_comments', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-builder' ) => 'show_comments' ), 'heading' => esc_html__( 'Show number of comments', 'bold-builder' ), 'preview' => true
				),
				array( 'param_name' => 'show_excerpt', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-builder' ) => 'show_excerpt' ), 'heading' => esc_html__( 'Show excerpt', 'bold-builder' ), 'preview' => true
				),
				array( 'param_name' => 'lazy_load', 'type' => 'dropdown', 'default' => 'yes', 'heading' => esc_html__( 'Lazy load images', 'bold-builder' ),
					'value' => array(
						esc_html__( 'No', 'bold-builder' ) 		=> 'no',
						esc_html__( 'Yes', 'bold-builder' ) 	=> 'yes'
					)
				)
			)
		) );
	} 
}