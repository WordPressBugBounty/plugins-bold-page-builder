<?php

class bt_bb_css_image_grid extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'images'      				=> '',
			'columns'     				=> '',
			'format'      				=> '',
			'gap'         				=> '',
			'shape'						=> 'inherit',
			'img_base_size'     		=> 'large',
			'lightbox_img_base_size'    => 'full',
			'no_lightbox' 				=> ''
		) ), $atts, $this->shortcode ) );

		wp_enqueue_script( 'jquery-masonry' );

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
		
		if ( $shape != '' ) {
			$class[] = $this->prefix . 'shape' . '_' . $shape;
		}

		if ( $no_lightbox == 'no_lightbox' ) {
			$class[] = $this->prefix . 'no_lightbox';
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

		$output = '';

		$images_arr = explode( ',', $images );
		$format_arr = explode( ',', $format );
			
		$n = 0;
		
		if ( $images == '' ) {
			$images_arr = array();
			for ( $i = 0; $i < $columns; $i++ ) {
				$images_arr[] = -1;
			}
		}

		foreach( $images_arr as $id ) {
			$data_title = '';
			if ( is_numeric( $id ) ) {
				if ( $id == -1 ) {
					$image_post = '';
					$img_src = BT_BB_Root::$path . 'img/placeholder2.png';
					$img_src_full = BT_BB_Root::$path . 'img/placeholder2.png';
				} else {
					$img = wp_get_attachment_image_src( $id, $img_base_size );
					$img_src = isset( $img[0] ) ? $img[0] : '';
					$img_full = wp_get_attachment_image_src( $id, $lightbox_img_base_size );
					$img_src_full = isset( $img_full[0] ) ? $img_full[0] : '';
					$image_post = get_post( $id );
				}
				if ( isset( $format_arr[ $n ] ) ) {
					$tile_format = 'bt_bb_tile_format';
					if ( $format_arr[ $n ] != '' ) {
						$tile_format .= '_' . esc_attr( trim( $format_arr[ $n ] ));
					} else {
						$tile_format .= '_11';
					}
				}

				if ( is_object( $image_post ) ) {
					$data_title = $image_post->post_title;
				}
				$output .= '<div class="' . $this->shortcode . '_item ' . $tile_format . '" data-src-full="' . esc_url_raw( $img_src_full ) . '" title="' . esc_attr( $data_title ) . '"><div class="' . $this->shortcode . '_item_inner"><div class="' . $this->shortcode . '_item_inner_image"><img src="' . esc_url_raw( $img_src ) . '" data-src-full="' . esc_url_raw( $img_src_full ) . '" title="' . esc_attr( $data_title ) . '" alt="' . esc_attr( $data_title ) . '"/></div><div class="' . $this->shortcode . '_item_inner_content"></div></div></div>';
				$n++;
			} else {
				$output .= '<div class="' . $this->shortcode . '_item" data-hw=".75" data-src-full="' . $id . '" data-title="" title="' . esc_attr( $data_title ) . '"><div class="' . $this->shortcode . '_item_inner" data-hw=".75" ><div class="' . $this->shortcode . '_item_inner_image"><img src="' . $id . '" data-src-full="' . $id . '" title="' . esc_attr( $data_title ) . '" alt="' . esc_attr( $data_title ) . '"/></div><div class="' . $this->shortcode . '_item_inner_content"></div></div></div>';
			}
		}

		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . ' data-columns="' . esc_attr( $columns ) . '"><div class="' . $this->shortcode . '_content">' . $output . '</div></div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

	}

	function map_shortcode() {

		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Image Grid', 'bold-builder' ), 'description' => esc_html__( 'Grid with images', 'bold-builder' ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'images', 'type' => 'attach_images', 'heading' => esc_html__( 'Images', 'bold-builder' ) ),
				array( 'param_name' => 'columns', 'type' => 'dropdown', 'heading' => esc_html__( 'Columns', 'bold-builder' ), 'preview' => true,
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
						esc_html__( 'No gap', 'bold-builder' ) 			=> 'no_gap',
						esc_html__( 'Extra small', 'bold-builder' ) 	=> 'extrasmall',
						esc_html__( 'Small', 'bold-builder' ) 			=> 'small',
						esc_html__( 'Normal', 'bold-builder' ) 			=> 'normal',
						esc_html__( 'Large', 'bold-builder' ) 			=> 'large'
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
				array( 'param_name' => 'format', 'type' => 'textfield', 'preview' => true, 'heading' => esc_html__( 'Tiles format', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. 21, 11, 11', 'bold-builder' ), 'description' => esc_html__( 'E.g. 21, 11, 11', 'bold-builder' ) ),
				array( 'param_name' => 'lightbox_img_base_size', 'type' => 'dropdown', 'default' => 'full', 'heading' => esc_html__( 'Lightbox image size', 'bold-builder' ),
					'value' => bt_bb_get_image_sizes()
				),
				array( 'param_name' => 'no_lightbox', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-builder' ) => 'no_lightbox', esc_html__( 'No', 'bold-builder' ) => 'hide_share' ), 'heading' => esc_html__( 'Disable lightbox', 'bold-builder' ) )
			)
		) );
	} 
}