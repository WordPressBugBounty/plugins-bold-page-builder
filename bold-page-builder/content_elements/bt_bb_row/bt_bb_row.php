<?php

class bt_bb_row extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {

		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'column_gap'  			=> '',
			'row_width'  			=> '',
			'color_scheme' 			=> '',
			'background_color' 		=> '',
			'opacity' 				=> ''
		) ), $atts, $this->shortcode ) );

		$class = array( '' );
		$outer_class = array( 'bt_bb_row' );
		$data_override_class = array();

		if ( $el_class != '' ) {
			$class[] = $el_class;
		}

		$id_attr = '';
		if ( $el_id != '' ) {
			$id_attr = 'id="' . esc_attr( $el_id ) . '"';
		}

		$el_inner_style = apply_filters( $this->shortcode . '_fix_inner_style', '', $atts );
		$el_style = apply_filters( $this->shortcode . '_style', $el_style, $atts );
		
		if ( $background_color != '' ) {
			if ( strpos( $background_color, '#' ) !== false ) {
				$background_color = bt_bb_hex2rgb( $background_color );
				if ( $opacity == '' ) {
					$opacity = 1;
				}
				$el_style .= ' background-color: rgba(' . $background_color[0] . ', ' . $background_color[1] . ', ' . $background_color[2] . ', ' . $opacity . ');';
			} else {
				$el_style .= 'background-color:' . $background_color . ';';
			}
		}
		
		$color_scheme_id = NULL;
		if ( is_numeric ( $color_scheme ) ) {
			$color_scheme_id = $color_scheme;
		} else if ( $color_scheme != '' ) {
			$color_scheme_id = bt_bb_get_color_scheme_id( $color_scheme );
		}
		$color_scheme_colors = bt_bb_get_color_scheme_colors_by_id( $color_scheme_id - 1 );
		if ( $color_scheme_colors ) $el_style .= '; --row-primary-color:' . $color_scheme_colors[0] . '; --row-secondary-color:' . $color_scheme_colors[1] . ';';
		if ( $color_scheme != '' ) $class[] = $this->prefix . 'color_scheme_' .  $color_scheme_id;

		if ( $column_gap != '' ) {
			$class[] = $this->prefix . 'column_gap' . '_' . $column_gap;
			if ( is_numeric( $column_gap ) ) $el_style .= '; --column-gap:' . $column_gap . 'px;'; // Values for small, normal etc are in css
		}

		if ( $row_width != '' && $row_width != 'default' ) {
			$outer_class_1200_base = $this->prefix . 'row_width' . '_' . 'boxed_1200 ' . $this->prefix . 'row_width' . '_' . 'boxed';
			$outer_class_1400_base = $this->prefix . 'row_width' . '_' . 'boxed_1400 ' . $this->prefix . 'row_width' . '_' . 'boxed';
			
			// TODO: srediti $el_style .= '; --row-width: 1200px;';
			
			if ( $row_width == 'boxed_1200' ) { $outer_class[] = $outer_class_1200_base; }
			else if ( $row_width == 'boxed_1200_left' ) { $outer_class[] = 'bt_bb_row_push_left'; $outer_class[] = $outer_class_1200_base; }
			else if ( $row_width == 'boxed_1200_left_content_wide' ) { $outer_class[] = 'bt_bb_row_push_left'; $outer_class[] = 'bt_bb_content_wide'; $outer_class[] = $outer_class_1200_base; }
			else if ( $row_width == 'boxed_1200_right' ) { $outer_class[] = 'bt_bb_row_push_right'; $outer_class[] = $outer_class_1200_base; }
			else if ( $row_width == 'boxed_1200_right_content_wide' ) { $outer_class[] = 'bt_bb_row_push_right'; $outer_class[] = 'bt_bb_content_wide'; $outer_class[] = $outer_class_1200_base; }
			else if ( $row_width == 'boxed_1200_left_right' ) { $outer_class[] = 'bt_bb_row_push_right'; $outer_class[] = 'bt_bb_row_push_left'; $outer_class[] = $outer_class_1200_base; }
			else if ( $row_width == 'boxed_1200_left_right_content_wide' ) { $outer_class[] = 'bt_bb_row_push_right'; $outer_class[] = 'bt_bb_row_push_left'; $outer_class[] = 'bt_bb_content_wide'; $outer_class[] = $outer_class_1200_base; }
			
			else if ( $row_width == 'boxed_1400' ) { $outer_class[] = $outer_class_1400_base; }
			else if ( $row_width == 'boxed_1400_left' ) { $outer_class[] = 'bt_bb_row_push_left'; $outer_class[] = $outer_class_1400_base; }
			else if ( $row_width == 'boxed_1400_left_content_wide' ) { $outer_class[] = 'bt_bb_row_push_left'; $outer_class[] = 'bt_bb_content_wide'; $outer_class[] = $outer_class_1400_base; }
			else if ( $row_width == 'boxed_1400_right' ) { $outer_class[] = 'bt_bb_row_push_right'; $outer_class[] = $outer_class_1400_base; }
			else if ( $row_width == 'boxed_1400_right_content_wide' ) { $outer_class[] = 'bt_bb_row_push_right'; $outer_class[] = 'bt_bb_content_wide'; $outer_class[] = $outer_class_1400_base; }
			else if ( $row_width == 'boxed_1400_left_right' ) { $outer_class[] = 'bt_bb_row_push_right'; $outer_class[] = 'bt_bb_row_push_left'; $outer_class[] = $outer_class_1400_base; }
			else if ( $row_width == 'boxed_1400_left_right_content_wide' ) { $outer_class[] = 'bt_bb_row_push_right'; $outer_class[] = 'bt_bb_row_push_left'; $outer_class[] = 'bt_bb_content_wide'; $outer_class[] = $outer_class_1400_base; }
			else { $outer_class[] = $this->prefix . 'row_width' . '_' . $row_width; /* fix for old custom classes */ }
		}

		$style_attr = '';
		if ( $el_style != '' ) {
			$style_attr = 'style="' . esc_attr( $el_style ) . '"';
		}
		
		$style_inner_attr = '';
		if ( $el_inner_style != '' ) {
			$style_inner_attr = 'style="' . esc_attr( $el_inner_style ) . '"';
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
		$class_attr = implode( ' ', $class );
		$outer_class_attr = implode( ' ', $outer_class );

		$output = '<div class="' . esc_attr( $outer_class_attr ) . ' ' . esc_attr( $class_attr ) . '" ' . $style_attr . ' data-bt-override-class="' . htmlspecialchars( json_encode( $data_override_class, JSON_FORCE_OBJECT ), ENT_QUOTES, 'UTF-8' ) . '"' . $id_attr . '>';
			$output .= '<div class="bt_bb_row_holder" ' . $style_inner_attr . '>';
				$output .= do_shortcode( $content );
			$output .= '</div>';
		$output .= '</div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

	}

	function map_shortcode() {
		
		require_once( dirname(__FILE__) . '/../../content_elements_misc/misc.php' );
		$color_scheme_arr = bt_bb_get_color_scheme_param_array();
		
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Row', 'bold-builder' ), 'description' => esc_html__( 'Row element', 'bold-builder' ), 'container' => 'horizontal', 'accept' => array( 'bt_bb_column' => true ), 'toggle' => true, 'auto_add' => 'bt_bb_column', 'show_settings_on_create' => false,
			'params' => array(
				array( 'param_name' => 'column_gap', 'type' => 'dropdown', 'heading' => esc_html__( 'Column gap', 'bold-builder' ), 'preview' => true,
					'value' => array(
						esc_html__( 'Default', 'bold-builder' ) => '',
						esc_html__( 'Extra small', 'bold-builder' ) => 'extra_small',
						esc_html__( 'Small', 'bold-builder' ) => 'small',		
						esc_html__( 'Normal', 'bold-builder' ) => 'normal',
						esc_html__( 'Medium', 'bold-builder' ) => 'medium',
						esc_html__( 'Large', 'bold-builder' ) => 'large',
						esc_html__( '0px', 'bold-builder' ) => '0',
						esc_html__( '5px', 'bold-builder' ) => '5',
						esc_html__( '10px', 'bold-builder' ) => '10',
						esc_html__( '15px', 'bold-builder' ) => '15',
						esc_html__( '20px', 'bold-builder' ) => '20',
						esc_html__( '25px', 'bold-builder' ) => '25',
						esc_html__( '30px', 'bold-builder' ) => '30',
						esc_html__( '35px', 'bold-builder' ) => '35',
						esc_html__( '40px', 'bold-builder' ) => '40',
						esc_html__( '45px', 'bold-builder' ) => '45',
						esc_html__( '50px', 'bold-builder' ) => '50',
						esc_html__( '55px', 'bold-builder' ) => '55',
						esc_html__( '60px', 'bold-builder' ) => '60',
						esc_html__( '65px', 'bold-builder' ) => '65',
						esc_html__( '70px', 'bold-builder' ) => '70',
						esc_html__( '75px', 'bold-builder' ) => '75',
						esc_html__( '80px', 'bold-builder' ) => '80',
						esc_html__( '85px', 'bold-builder' ) => '85',
						esc_html__( '90px', 'bold-builder' ) => '90',
						esc_html__( '95px', 'bold-builder' ) => '95',
						esc_html__( '100px', 'bold-builder' ) => '100'
					)
				),
				array( 'param_name' => 'row_width', 'type' => 'radio', 'heading' => esc_html__( 'Columns layout', 'bold-builder' ), 'default' => 'default', 'description' => __( 'For the best experience set Section Layout to Wide. Read more in our <a href="https://documentation.bold-themes.com/bold-builder/row-layouts/" target="_blank">documentation</a>.', 'bold-builder' ), 'preview' => true,
					'value' => array(
						esc_html__( 'Default', 'bold-builder' ) 										=> 'default',
						
						esc_html__( 'Row width 1200px', 'bold-builder' )                                => '__text',
						
						esc_html__( 'First and last are boxed', 'bold-builder' ) 						=> 'boxed_1200',
						esc_html__( 'First is wide (boxed content)', 'bold-builder' ) 					=> 'boxed_1200_left',
						esc_html__( 'First is wide', 'bold-builder' ) 									=> 'boxed_1200_left_content_wide',
						esc_html__( 'Last is wide (boxed content)', 'bold-builder' ) 					=> 'boxed_1200_right',
						esc_html__( 'Last is wide', 'bold-builder' ) 									=> 'boxed_1200_right_content_wide',
						esc_html__( 'First and last are wide (boxed content)', 'bold-builder' ) 		=> 'boxed_1200_left_right',
						esc_html__( 'First and last are wide', 'bold-builder' ) 						=> 'boxed_1200_left_right_content_wide',
						
						esc_html__( 'Row width 1400px', 'bold-builder' )                                => '__text',
						
						esc_html__( 'First and last are boxed ', 'bold-builder' ) 						=> 'boxed_1400',
						esc_html__( 'First is wide (boxed content) ', 'bold-builder' ) 					=> 'boxed_1400_left',
						esc_html__( 'First is wide ', 'bold-builder' ) 									=> 'boxed_1400_left_content_wide',
						esc_html__( 'Last is wide (boxed content) ', 'bold-builder' ) 					=> 'boxed_1400_right',
						esc_html__( 'Last is wide ', 'bold-builder' ) 									=> 'boxed_1400_right_content_wide',
						esc_html__( 'First and last are wide (boxed content) ', 'bold-builder' ) 		=> 'boxed_1400_left_right',
						esc_html__( 'First and last are wide ', 'bold-builder' ) 						=> 'boxed_1400_left_right_content_wide',
					)
				),
				array( 'param_name' => 'color_scheme', 'type' => 'dropdown', 'heading' => esc_html__( 'Color scheme', 'bold-builder' ), 'description' => esc_html__( 'Define color schemes in Bold Builder settings or define accent and alternate colors in theme customizer (if avaliable)', 'bold-builder' ), 'value' => $color_scheme_arr, 'preview' => true, 'group' => esc_html__( 'Design', 'bold-builder' )  ),
				array( 'param_name' => 'background_color', 'type' => 'colorpicker', 'heading' => esc_html__( 'Background color', 'bold-builder' ), 'preview' => true, 'group' => esc_html__( 'Design', 'bold-builder' ) ),
				array( 'param_name' => 'opacity', 'type' => 'textfield', 'heading' => esc_html__( 'Background color opacity (deprecated)', 'bold-builder' ), 'group' => esc_html__( 'Design', 'bold-builder' ) )			
			)
		) );
	}

}