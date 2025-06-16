<?php

class bt_bb_slider extends BT_BB_Element {
	
	public $auto_play = '';

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'images'    			=> '',
			'height'    			=> '',
			'size'     				=> 'full',
			'show_arrows'     		=> '',
			'show_dots'     		=> '',
			'animation' 			=> '',
			'slides_to_show' 		=> '',
			'additional_settings'	=> '',
			'auto_play' 			=> '',
			'pause_on_hover'     	=> '',
			'use_lightbox' 			=> ''
		) ), $atts, $this->shortcode ) );
		
		$slider_class = array( 'slick-slider' );
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

		if ( $height != '' ) {
			$class[] = $this->prefix . 'height' . '_' . $height;
		}	
		
		$data_slick = ' ' . 'data-slick=\'{ "lazyLoad": "progressive", "cssEase": "ease-out", "speed": "300"';
		
		if ( $animation == 'fade' ) {
			$data_slick .= ', "fade": true';
			$slider_class[] = 'fade';
			$slides_to_show = 1;
		}
		
		if ( $height != 'keep-height' ) {
			$data_slick .= ', "adaptiveHeight": true';
		}
		
		if ( $show_dots != 'hide' ) {
			$data_slick .= ', "dots": true' ;
			$class[] = $this->prefix . 'show_dots' . '_' . $show_dots;
		}

		if ( $show_arrows != 'hide' ) {
			$data_slick  .= ', "prevArrow": "&lt;button type=\"button\" class=\"slick-prev\" aria-label=\"' . esc_html__( 'Previous', 'bold-builder' ) . '\" tabindex=\"0\" role=\"button\"&gt;&lt;/button&gt;", "nextArrow": "&lt;button type=\"button\" class=\"slick-next\" aria-label=\"' . esc_html__( 'Next', 'bold-builder' ) . '\" tabindex=\"0\" role=\"button\"&gt;&lt;/button&gt;"';
		} else {
			$data_slick  .= ', "prevArrow": "", "nextArrow": ""';
		}
		
		if ( $slides_to_show > 1 ) {
			$data_slick .= ',"slidesToShow": ' . intval( $slides_to_show );
			$class[] = $this->prefix . 'multiple_slides';
		}
		
		if ( $auto_play != '' ) {
			$data_slick .= ', "autoplay": true, "autoplaySpeed": ' . intval( $auto_play );
		}
		
		if ( $pause_on_hover == 'no' ) {
			$data_slick .= ', "pauseOnHover": false';
		}
		
		if ( is_rtl() ) {
			$data_slick .= ', "rtl": true' ;
		}
		
		$slides_to_show_arr = explode( ',;,', $slides_to_show );
		
		if ( count( $slides_to_show_arr ) == 1 ) {
			$slides_to_show_arr = array( $slides_to_show_arr[0], '', '', '', '', '', '' );
		}
		
		if ( count( $slides_to_show_arr ) == 6 ) {
			array_splice( $slides_to_show_arr, 0, 0, array( $slides_to_show_arr[0] ) );
		}
		
		if ( $slides_to_show_arr[2] == '' ) { $slides_to_show_arr[2] = $slides_to_show_arr[0]; } // 1400
		if ( $slides_to_show_arr[3] == '' ) { $slides_to_show_arr[3] = $slides_to_show_arr[0]; } // 1200
		if ( $slides_to_show_arr[4] == '' ) { $slides_to_show_arr[4] = ( intval( $slides_to_show_arr[0] ) > 3 ) ? '3' : $slides_to_show_arr[0]; } // 992
		if ( $slides_to_show_arr[5] == '' ) { $slides_to_show_arr[5] = ( intval( $slides_to_show_arr[0] ) > 2 ) ? '2' : $slides_to_show_arr[0]; } // 768
		if ( $slides_to_show_arr[6] == '' ) { $slides_to_show_arr[6] = ( intval( $slides_to_show_arr[0] ) > 1 ) ? '1' : $slides_to_show_arr[0];	} // 480
		
		$sts = intval( $slides_to_show_arr[0] );
		if ( isset( $slides_to_show_arr[1] ) && $slides_to_show_arr[1] !== '' ) {
			$sts = intval( $slides_to_show_arr[1] );
		}
		
		if ( $sts > 1 ) {
			$data_slick .= ',"slidesToShow": ' . $sts;
			$class[] = $this->prefix . 'multiple_slides';
			$data_slick .= ', "responsive": [';
				$data_slick .= '{ "breakpoint": 481, "settings": { "slidesToShow": ' . $slides_to_show_arr[6] . ', "slidesToScroll": ' . $slides_to_show_arr[6] . ' } }';	
				$data_slick .= ',{ "breakpoint": 769, "settings": { "slidesToShow": ' . $slides_to_show_arr[5] . ', "slidesToScroll": ' . $slides_to_show_arr[5] . ' } }';	
				$data_slick .= ',{ "breakpoint": 993, "settings": { "slidesToShow": ' . $slides_to_show_arr[4] . ', "slidesToScroll": ' . $slides_to_show_arr[4] . ' } }';	
				$data_slick .= ',{ "breakpoint": 1201, "settings": { "slidesToShow": ' . $slides_to_show_arr[3] . ', "slidesToScroll": ' . $slides_to_show_arr[3] . ' } }';	
				$data_slick .= ',{ "breakpoint": 1401, "settings": { "slidesToShow": ' . $slides_to_show_arr[2] . ', "slidesToScroll": ' . $slides_to_show_arr[2] . ' } }';		
			$data_slick .= ']';
		}
		
		if ( $additional_settings != '' ) {
			$data_slick .= ', ' . $additional_settings;
		}
		
		$data_slick = $data_slick . '}\' ';

		if ( $use_lightbox == 'use_lightbox' ) {
			$class[] = $this->prefix . 'use_lightbox';
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
		
		if ( $images == '' ) {
			$image_array = array();
			$image_array[] = BT_BB_Root::$path . 'img/placeholder3.png';
			$image_array[] = BT_BB_Root::$path . 'img/placeholder3.png';
			$image_array[] = BT_BB_Root::$path . 'img/placeholder3.png';
		} else {
			$image_array = explode( ',', $images );
		}
		
		foreach( $image_array as $image ) {
			$alt = '';
			$title = '';
			$image_thumb_src = BT_BB_Root::$path . 'img/placeholder3.png';
			$image_full_src = BT_BB_Root::$path . 'img/placeholder3.png';
			if ( is_numeric( $image ) ) {
				$post_image = get_post( $image );
				if ( is_object( $post_image ) ) {
					$alt = get_post_meta( $post_image->ID, '_wp_attachment_image_alt', true );
					if ( $alt == '' ) {
						$alt = $post_image->post_excerpt;
					} 
					if ( $alt == '' ) {
						$alt = $post_image->post_title;
					}
					$title = $post_image->post_title;
					if ( $title != '' ) {
						$title = ' title="' . esc_attr( $title ) . '"';	
					}
					$image_thumb = wp_get_attachment_image_src( $image, $size );
					$image_full = wp_get_attachment_image_src( $image, 'full' );
					$image_thumb_src = $image_thumb[0];
					$image_full_src = $image_full[0];
				}
			}
			if ( $height == 'auto' || $height == 'keep-height' ) {
				$output .= '<div class="bt_bb_slider_item" data-src-full="' . esc_url_raw( $image_full_src ) . '"><img src="' . esc_url_raw( $image_thumb_src ) . '" alt="' . esc_attr( $alt ) . '" ' . ( $title ) . '></div>';
			} else {
				$output .= '<div class="bt_bb_slider_item" style="background-image:url(\'' . esc_url_raw( $image_thumb_src ) . '\')" data-src-full="' . esc_url_raw( $image_full_src ) . '"></div>';
			}
			
		}

		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . '><div class="' . esc_attr( implode( ' ', $slider_class ) ) . '" ' . $data_slick . '>' . $output . '</div></div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );
		
		return $output;

	}

	function map_shortcode() {
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Image Slider', 'bold-builder' ), 'description' => esc_html__( 'Slider with images', 'bold-builder' ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'images', 'type' => 'attach_images', 'heading' => esc_html__( 'Images', 'bold-builder' ) ),
				array( 'param_name' => 'height', 'type' => 'dropdown', 'heading' => esc_html__( 'Height', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Auto', 'bold-builder' ) => 'auto',
						esc_html__( 'Keep height', 'bold-builder' ) => 'keep-height',
						esc_html__( 'Half screen', 'bold-builder' ) => 'half_screen',
						esc_html__( 'Full screen', 'bold-builder' ) => 'full_screen'
					)
				),
				array( 'param_name' => 'size', 'type' => 'dropdown', 'heading' => esc_html__( 'Images size', 'bold-builder' ), 'preview' => true,
					'value' => bt_bb_get_image_sizes()
				),
				array( 'param_name' => 'animation', 'type' => 'dropdown', 'heading' => esc_html__( 'Animation', 'bold-builder' ), 'description' => esc_html__( 'If fade is selected, number of slides to show will be 1', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Default', 'bold-builder' ) => 'slide',
						esc_html__( 'Fade', 'bold-builder' ) 	=> 'fade'
					)
				),
				array( 'param_name' => 'show_arrows', 'default' => 'show', 'type' => 'dropdown', 'heading' => esc_html__( 'Navigation arrows', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Show', 'bold-builder' ) => 'show',
						esc_html__( 'Hide', 'bold-builder' ) => 'hide'
					)
				),
				array( 'param_name' => 'show_dots', 'type' => 'dropdown', 'heading' => esc_html__( 'Dots navigation', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Bottom', 'bold-builder' ) 	=> 'bottom',
						esc_html__( 'Below', 'bold-builder' ) 	=> 'below',
						esc_html__( 'Hide', 'bold-builder' ) 	=> 'hide'
					)
				),
				array( 'param_name' => 'slides_to_show', 'type' => 'dropdown', 'preview' => true, 'heading' => esc_html__( 'Number of slides to show', 'bold-builder' ), 'description' => esc_html__( 'E.g. 1. If fade animation is selected, number will  be 1 anyway', 'bold-builder' ), 'responsive_override' => true,
					'value' => array(
						esc_html__( '1', 'bold-builder' ) 		=> '1',
						esc_html__( '2', 'bold-builder' ) 		=> '2',
						esc_html__( '3', 'bold-builder' ) 		=> '3',
						esc_html__( '4', 'bold-builder' ) 		=> '4',
						esc_html__( '5', 'bold-builder' ) 		=> '5',
						esc_html__( '6', 'bold-builder' ) 		=> '6',
						esc_html__( '7', 'bold-builder' ) 		=> '7',
						esc_html__( '8', 'bold-builder' ) 		=> '8',
						esc_html__( '9', 'bold-builder' ) 		=> '9',
						esc_html__( '10', 'bold-builder' ) 		=> '10',
						esc_html__( '11', 'bold-builder' ) 		=> '11',
						esc_html__( '12', 'bold-builder' ) 		=> '12'
					) 
				),
				
				array( 'param_name' => 'additional_settings', 'type' => 'textfield', 'heading' => esc_html__( 'Additional settings', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. "slidesToScroll": 3, "infinite": false, "centerMode": true', 'bold-builder' ), 'description' => __( 'E.g. "slidesToScroll": 3, "infinite": false, "centerMode": true, "centerPadding": "60px" (<a href="https://kenwheeler.github.io/slick/" target="_blank">view here for more</a>)', 'bold-builder' ) ),

				array( 'param_name' => 'auto_play', 'type' => 'textfield', 'heading' => esc_html__( 'Autoplay interval (ms)', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. 2000', 'bold-builder' ) ), 
					
				array( 'param_name' => 'pause_on_hover', 'default' => 'yes', 'type' => 'dropdown', 'heading' => esc_html__( 'Pause slideshow on hover', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Yes', 'bold-builder' ) 	=> 'yes',
						esc_html__( 'No', 'bold-builder' ) 		=> 'no'
					)
				),
				array( 'param_name' => 'use_lightbox', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-builder' ) => 'use_lightbox', esc_html__( 'No', 'bold-builder' ) => 'dont_use_lightbox' ), 'heading' => esc_html__( 'Use lightbox (opens image in full size on click)', 'bold-builder' ) )
			)
		) );
	}
}