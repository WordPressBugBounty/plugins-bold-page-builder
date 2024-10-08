<?php

class bt_bb_content_slider extends BT_BB_Element {
	
	public $auto_play = '';

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'height'    			=> '',
			'show_dots' 			=> '',
			'animation' 			=> 'slide',
			'direction' 			=> 'default',
			'gap' 					=> '',
			'arrows_size' 			=> '',
			'pause_on_hover'     	=> '',
			'slides_to_show' 		=> '',
			'additional_settings' 	=> '',
			'auto_play' 			=> ''
		) ), $atts, $this->shortcode ) );
		
		$class = array( $this->shortcode );
		$slider_class = array( 'slick-slider' );
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
		
		if ( $gap != '' ) {
			$class[] = $this->prefix . 'gap' . '_' . $gap;
		}
		
		if ( $arrows_size != '' ) {
			$class[] = $this->prefix . 'arrows_size' . '_' . $arrows_size;
		}
		
		if ( $show_dots != '' ) {
			$class[] = $this->prefix . 'show_dots_' . $show_dots;
		}
		
		if ( $height != '' ) {
			$class[] = $this->prefix . 'height' . '_' . $height;
		}
		
		if ( $animation != '' ) {
			$class[] = $this->prefix . 'animation' . '_' . $animation;
		}
		
		$data_slick  = ' data-slick=\'{ "lazyLoad": "progressive", "cssEase": "ease-out", "speed": "600", "accessibility": false';
		
		if ( $animation == 'fade' ) {
			$data_slick .= ', "fade": true';
			$slider_class[] = 'fade';
			$slides_to_show = 1;
		}
		
		if ( $arrows_size != 'no_arrows' ) {
			$data_slick  .= ', "prevArrow": "&lt;button type=\"button\" class=\"slick-prev\" aria-label=\"' . esc_html__( 'Previous', 'bold-builder' ) . '\" tabindex=\"0\" role=\"button\"&gt;&lt;/button&gt;", "nextArrow": "&lt;button type=\"button\" class=\"slick-next\" aria-label=\"' . esc_html__( 'Next', 'bold-builder' ) . '\" tabindex=\"0\" role=\"button\"&gt;&lt;/button&gt;"';
		} else {
			$data_slick .= ', "arrows": false';
		}
		
		if ( $height != 'keep-height' ) {
			$data_slick .= ', "adaptiveHeight": true';
		}
		
		if ( $show_dots != 'hide' ) {
			$data_slick .= ', "dots": true' ;
		}
		
		if ( $auto_play != '' ) {
			$data_slick .= ',"autoplay": true, "autoplaySpeed": ' . intval( $auto_play );
		}
		
		if ( $pause_on_hover == 'no' ) {
			$data_slick .= ',"pauseOnHover": false';
		}
		
		$dir_attr = "";

		if ( is_rtl() && ( !in_array( $direction, array( 'rtl', 'ltr') ) ) ) {
			$data_slick .= ', "rtl": true' ;
		} else if( $direction == 'rtl' ) {
			$data_slick .= ', "rtl": true' ;
			$dir_attr = " dir='rtl'";
		} else if( $direction == 'ltr' ) {
			$data_slick .= ', "rtl": false' ;
			$dir_attr = " dir='ltr'";
		}
				
		$slides_to_show_arr =  explode( ',;,', $slides_to_show );
		
		
		if ( count( $slides_to_show_arr ) == 1 || count( array_count_values( $slides_to_show_arr ) ) < 3 ) {

			// old format used, just saved in new format ( $slides_to_show and '' are the only values )
			if ( count( array_count_values( $slides_to_show_arr ) ) == 2 ) {
				$slides_to_show = $slides_to_show_arr[0];
			}
			// old format
			$slides_to_show_arr = array( 6 );
			
			// switch to new format
			$slides_to_show_arr[0] = $slides_to_show;
			$slides_to_show_arr[1] = $slides_to_show;
			$slides_to_show_arr[2] = $slides_to_show;
			$slides_to_show_arr[3] = ( intval( $slides_to_show ) > 3 ) ? '3' : $slides_to_show;
			$slides_to_show_arr[4] = ( intval( $slides_to_show ) > 2 ) ? '2' : $slides_to_show;
			$slides_to_show_arr[5] = ( intval( $slides_to_show ) > 1 ) ? '1' : $slides_to_show;
		}
		
		// new format
		if ( $slides_to_show_arr[1] == '' ) { $slides_to_show_arr[1] = $slides_to_show_arr[0]; } // 1400
		if ( $slides_to_show_arr[2] == '' ) { $slides_to_show_arr[2] = $slides_to_show_arr[1]; } // 1200
		if ( $slides_to_show_arr[3] == '' ) { $slides_to_show_arr[3] = $slides_to_show_arr[2]; } // 992
		if ( $slides_to_show_arr[4] == '' ) { $slides_to_show_arr[4] = $slides_to_show_arr[3]; } // 768
		if ( $slides_to_show_arr[5] == '' ) { $slides_to_show_arr[5] = $slides_to_show_arr[4]; } // 480
		
		if ( intval( $slides_to_show_arr[0] ) > 1 ) {
			$data_slick .= ',"slidesToShow": ' . intval( $slides_to_show_arr[0] );
			$class[] = $this->prefix . 'multiple_slides';
			$data_slick .= ', "responsive": [';
				$data_slick .= '{ "breakpoint": 480, "settings": { "slidesToShow": ' . $slides_to_show_arr[5] . ', "slidesToScroll": ' . $slides_to_show_arr[5] . ' } }';	
				$data_slick .= ',{ "breakpoint": 768, "settings": { "slidesToShow": ' . $slides_to_show_arr[4] . ', "slidesToScroll": ' . $slides_to_show_arr[4] . ' } }';	
				$data_slick .= ',{ "breakpoint": 992, "settings": { "slidesToShow": ' . $slides_to_show_arr[3] . ', "slidesToScroll": ' . $slides_to_show_arr[3] . ' } }';	
				$data_slick .= ',{ "breakpoint": 1200, "settings": { "slidesToShow": ' . $slides_to_show_arr[2] . ', "slidesToScroll": ' . $slides_to_show_arr[2] . ' } }';	
				$data_slick .= ',{ "breakpoint": 1400, "settings": { "slidesToShow": ' . $slides_to_show_arr[1] . ', "slidesToScroll": ' . $slides_to_show_arr[1] . ' } }';		
			$data_slick .= ']';
		}
		
		// var_dump( $content );
			
		// turn of loop for dynamic elements (ghost slide does not work)
		if ( 
			str_contains( $content, "bt_bb_leaflet_map" ) || 
			str_contains( $content, "bt_bb_google_maps" ) || 
			str_contains( $content, "bt_bb_progress_bar" ) || 
			str_contains( $content, "bt_bb_countdown" ) || 
			str_contains( $content, "bt_bb_counter" ) 
		) {
			$data_slick .= ', "infinite": false';
		}
		
		if ( $additional_settings != '' ) {
			$data_slick .= ', ' . $additional_settings;
		}
		
		$data_slick = $data_slick . '}\' ';

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

		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . '' . $dir_attr . ' data-bt-override-class="' . htmlspecialchars( json_encode( $data_override_class, JSON_FORCE_OBJECT ), ENT_QUOTES, 'UTF-8' ) . '"><div class="' . implode( ' ', $slider_class ) . '" ' . $data_slick .  '>' . do_shortcode( $content ) . '</div></div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );
		
		return $output;

	}

	function map_shortcode() {
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Slider', 'bold-builder' ), 'description' => esc_html__( 'Slider with custom content', 'bold-builder' ), 'container' => 'vertical', 'accept' => array( 'bt_bb_content_slider_item' => true ), 'toggle' => true, 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'height', 'type' => 'dropdown', 'preview' => true, 'heading' => esc_html__( 'Size', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Auto', 'bold-builder' ) 			=> 'auto',
						esc_html__( 'Keep height', 'bold-builder' ) 	=> 'keep-height',
						esc_html__( 'Half screen', 'bold-builder' ) 	=> 'half_screen',
						esc_html__( 'Full screen', 'bold-builder' ) 	=> 'full_screen'
					)
				),
				array( 'param_name' => 'animation', 'preview' => true, 'default' => 'slide', 'type' => 'dropdown', 'heading' => esc_html__( 'Animation', 'bold-builder' ), 'description' => esc_html__( 'If fade is selected, number of slides to show will be 1', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Default (slide)', 'bold-builder' ) => 'slide',
						esc_html__( 'Fade', 'bold-builder' ) 			=> 'fade'
					)
				),
				array( 'param_name' => 'direction', 'preview' => true, 'default' => 'default', 'type' => 'dropdown', 'heading' => esc_html__( 'Direction', 'bold-builder' ), 'description' => esc_html__( 'Default option follows Wordpress language settings.', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Default (switch rtl / ltr)', 'bold-builder' ) 	=> 'default',
						esc_html__( 'Left to right', 'bold-builder' ) 				=> 'ltr',
						esc_html__( 'Right to left', 'bold-builder' ) 				=> 'rtl'
					)
				),
				array( 'param_name' => 'pause_on_hover', 'default' => 'yes', 'type' => 'dropdown', 'heading' => esc_html__( 'Pause slideshow on hover', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Yes', 'bold-builder' ) 			=> 'yes',
						esc_html__( 'No', 'bold-builder' ) 				=> 'no'
					)
				),
				array( 'param_name' => 'slides_to_show', 'type' => 'dropdown', 'preview' => true, 'heading' => esc_html__( 'Number of slides to show', 'bold-builder' ), 'description' => esc_html__( 'If fade animation is selected, number will be 1 anyway', 'bold-builder' ), 'responsive_override' => true,
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
				array( 'param_name' => 'gap', 'type' => 'dropdown', 'heading' => esc_html__( 'Gap', 'bold-builder' ),
					'value' => array(
						esc_html__( 'No gap', 'bold-builder' ) 			=> 'no_gap',
						esc_html__( 'Small', 'bold-builder' ) 			=> 'small',
						esc_html__( 'Normal', 'bold-builder' ) 			=> 'normal',
						esc_html__( 'Large', 'bold-builder' ) 			=> 'large'
					)
				),
				array( 'param_name' => 'auto_play', 'type' => 'textfield', 'heading' => esc_html__( 'Autoplay interval (ms)', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. 2000', 'bold-builder' ) ),


				array( 'param_name' => 'arrows_size', 'type' => 'dropdown', 'preview' => true, 'default' => 'normal', 'heading' => esc_html__( 'Navigation arrows size', 'bold-builder' ), 'group' => esc_html__( 'Navigation', 'bold-builder' ),
					'value' => array(
						esc_html__( 'No arrows', 'bold-builder' ) 		=> 'no_arrows',
						esc_html__( 'Small', 'bold-builder' ) 			=> 'small',
						esc_html__( 'Normal', 'bold-builder' ) 			=> 'normal',
						esc_html__( 'Large', 'bold-builder' ) 			=> 'large'
					)
				),
				array( 'param_name' => 'show_dots', 'type' => 'dropdown', 'heading' => esc_html__( 'Dots navigation', 'bold-builder' ), 'group' => esc_html__( 'Navigation', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Bottom', 'bold-builder' ) 			=> 'bottom',
						esc_html__( 'Below', 'bold-builder' ) 			=> 'below',
						esc_html__( 'Hide', 'bold-builder' ) 			=> 'hide'
					)
				),
			)
		) );
	}
}