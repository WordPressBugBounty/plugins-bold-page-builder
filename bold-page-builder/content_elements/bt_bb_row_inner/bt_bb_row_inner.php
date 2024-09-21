<?php

class bt_bb_row_inner extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'column_gap'     	=> '',
			'row_width'     	=> ''
		) ), $atts, $this->shortcode ) );

		$class = array( 'bt_bb_row_inner' );
		$outer_class = array( '' );
		$data_override_class = array();

		if ( $el_class != '' ) {
			$class[] = $el_class;
		}

		$id_attr = '';
		if ( $el_id != '' ) {
			$id_attr = 'id="' . esc_attr( $el_id ) . '"';
		}
		
		$el_style = apply_filters( $this->shortcode . '_style', $el_style, $atts );
		$el_inner_style = apply_filters( $this->shortcode . '_fix_inner_style', '', $atts );

		$style_attr = '';
		if ( $el_style != '' ) {
			$style_attr = 'style="' . esc_attr( $el_style ) . '"';
		}
		
		$style_inner_attr = '';
		if ( $el_inner_style != '' ) {
			$style_inner_attr = 'style="' . esc_attr( $el_inner_style ) . '"';
		}

		if ( $column_gap != '' ) {
			$class[] = $this->prefix . 'column_inner_gap' . '_' . $column_gap;
		}

		/*if ( $row_width != '' && $row_width != 'default' ) {
			$outer_class_1200_base = $this->prefix . 'row_width' . '_' . 'boxed_1200';
			if ( $row_width == 'boxed_1200' ) { $outer_class[] = $outer_class_1200_base; }
			$outer_class_1400_base = $this->prefix . 'row_width' . '_' . 'boxed_1400';
			if ( $row_width == 'boxed_1400' ) { $outer_class[] = $outer_class_1400_base; }
		}*/
		
		if ( $row_width != '' && $row_width != 'default' ) {
			$class[] = $this->prefix . 'row_width' . '_' . $row_width;
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

		$output = '<div class="' . esc_attr( $outer_class_attr ) . ' ' . esc_attr( $class_attr ) . '" ' . $style_attr . ' ' . $id_attr . ' data-bt-override-class="' . htmlspecialchars( json_encode( $data_override_class, JSON_FORCE_OBJECT ), ENT_QUOTES, 'UTF-8' ) . '">';	
			$output .= '<div class="bt_bb_row_inner_holder" ' . $style_inner_attr . ' >';
				$output .= do_shortcode( $content );
			$output .= '</div>';
		$output .= '</div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

	}
	function map_shortcode() {		
		
			bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Inner Row', 'bold-builder' ), 'description' => esc_html__( 'Inner Row element', 'bold-builder' ), 'container' => 'horizontal', 
				'accept' => array( 'bt_bb_column_inner' => true ), 'toggle' => true,  'show_settings_on_create' => false, 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
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
					array( 'param_name' => 'row_width', 'type' => 'dropdown', 'heading' => esc_html__( 'Row width', 'bold-builder' ), 'description' => esc_html__( 'For the best experience set section width to wide.', 'bold-builder' ), 'preview' => true,
						'value' => array(
							esc_html__( 'Wide', 'bold-builder' ) => 'default',
							esc_html__( 'Boxed 1200px', 'bold-builder' ) => 'boxed_1200',
							esc_html__( 'Boxed 1400px', 'bold-builder' ) => 'boxed_1400',
						)
					)
				)
			) 
		);
	}

}