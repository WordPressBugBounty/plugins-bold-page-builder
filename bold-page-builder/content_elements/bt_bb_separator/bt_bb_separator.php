<?php

class bt_bb_separator extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'top_spacing'    		=> 'none',
			'bottom_spacing' 		=> 'none',
			'border_style'   		=> '',
			'border_thickness'   	=> '',
			'color_scheme' 			=> '',
			'icon'         			=> '',
			'icon_size'         	=> 'normal',
			'text'         			=> '',
			'text_size'         	=> 'normal',
		) ), $atts, $this->shortcode ) );
		
		$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
		if ( $text != '' ) $text = '<span class="bt_bb_separator_v2_inner_text">' . wp_kses_post( $text ) . '</span>';
		
		// $class = array( $this->shortcode );
		$class = array( $this->shortcode . '_v2' );
		
		if ( $el_class != '' ) {
			$class[] = $el_class;
		}
		
		$id_attr = '';
		if ( $el_id != '' ) {
			$id_attr = ' ' . 'id="' . esc_attr( $el_id ) . '"';
		}
		
		if ( $border_style != '' ) {
			$class[] = $this->prefix . 'border_style' . '_' . $border_style;
		}
		
		$color_scheme_id = NULL;
		if ( is_numeric ( $color_scheme ) ) {
			$color_scheme_id = $color_scheme;
		} else if ( $color_scheme != '' ) {
			$color_scheme_id = bt_bb_get_color_scheme_id( $color_scheme );
		}
		$color_scheme_colors = bt_bb_get_color_scheme_colors_by_id( $color_scheme_id - 1 );
		if ( $color_scheme_colors ) $el_style .= '; --primary-color:' . $color_scheme_colors[0] . '; --secondary-color:' . $color_scheme_colors[1] . ';';
		if ( $color_scheme != '' ) $class[] = $this->prefix . 'color_scheme_' .  $color_scheme_id;	
		
		$style_attr = '';
		$el_style = apply_filters( $this->shortcode . '_style', $el_style, $atts );
		if ( $el_style != '' ) {
			$style_attr = ' ' . 'style="' . esc_attr( $el_style ) . '"';
		}
		
		$this->responsive_data_override_class(
			$class, $data_override_class,
			array(
				'prefix' => $this->prefix,
				'param' => 'top_spacing',
				'value' => $top_spacing
			)
		);
		
		$this->responsive_data_override_class(
			$class, $data_override_class,
			array(
				'prefix' => $this->prefix,
				'param' => 'bottom_spacing',
				'value' => $bottom_spacing
			)
		);
		
		$this->responsive_data_override_class(
			$class, $data_override_class,
			array(
				'prefix' => $this->prefix,
				'param' => 'border_thickness',
				'value' => $border_thickness
			)
		);

		$this->responsive_data_override_class(
			$class, $data_override_class,
			array(
				'prefix' => $this->prefix,
				'param' => 'icon_size',
				'value' => $icon_size
			)
		);

		$this->responsive_data_override_class(
			$class, $data_override_class,
			array(
				'prefix' => $this->prefix,
				'param' => 'text_size',
				'value' => $text_size
			)
		);
		
		$icon_html = bt_bb_icon::get_html( $icon, '', '', '', '' );
		
		if ( $icon == '' && $text == '' ) $class[] = 'bt_bb_separator_v2_without_content';

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
		
		if ( $el_class != '' ) {
			$class_attr = $class_attr . ' ' . $el_class;
		}
		
		$output = '<div' . $id_attr . ' class="' . esc_attr( $class_attr ) . '"' . $style_attr . ' data-bt-override-class="' . htmlspecialchars( json_encode( $data_override_class, JSON_FORCE_OBJECT ), ENT_QUOTES, 'UTF-8' ) . '"><div class="bt_bb_separator_v2_inner"><span class="bt_bb_separator_v2_inner_before"></span><span class="bt_bb_separator_v2_inner_content">' . $icon_html . $text . '</span><span class="bt_bb_separator_v2_inner_after"></span></div></div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );
		
		return $output;

	}

	function map_shortcode() {
		
		require_once( dirname(__FILE__) . '/../../content_elements_misc/misc.php' );
		$color_scheme_arr = bt_bb_get_color_scheme_param_array();
		
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Separator', 'bold-builder' ), 'description' => esc_html__( 'Separator line', 'bold-builder' ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'top_spacing', 'type' => 'dropdown', 'heading' => esc_html__( 'Top spacing', 'bold-builder' ), 'preview' => true, 'responsive_override' => true,
					'value' => array(
						esc_html__( 'No spacing', 'bold-builder' ) 	=> 'none',
						esc_html__( 'Extra small', 'bold-builder' ) => 'extra_small',
						esc_html__( 'Small', 'bold-builder' ) 		=> 'small',		
						esc_html__( 'Normal', 'bold-builder' ) 		=> 'normal',
						esc_html__( 'Medium', 'bold-builder' ) 		=> 'medium',
						esc_html__( 'Large', 'bold-builder' ) 		=> 'large',
						esc_html__( 'Extra large', 'bold-builder' ) => 'extra_large',
						esc_html__( '5px', 'bold-builder' ) 	=> '5',
						esc_html__( '10px', 'bold-builder' ) 	=> '10',
						esc_html__( '15px', 'bold-builder' ) 	=> '15',
						esc_html__( '20px', 'bold-builder' ) 	=> '20',
						esc_html__( '25px', 'bold-builder' ) 	=> '25',
						esc_html__( '30px', 'bold-builder' ) 	=> '30',
						esc_html__( '35px', 'bold-builder' ) 	=> '35',
						esc_html__( '40px', 'bold-builder' ) 	=> '40',
						esc_html__( '45px', 'bold-builder' ) 	=> '45',
						esc_html__( '50px', 'bold-builder' ) 	=> '50',
						esc_html__( '55px', 'bold-builder' ) 	=> '55',
						esc_html__( '60px', 'bold-builder' ) 	=> '60',
						esc_html__( '65px', 'bold-builder' ) 	=> '65',
						esc_html__( '70px', 'bold-builder' ) 	=> '70',
						esc_html__( '75px', 'bold-builder' ) 	=> '75',
						esc_html__( '80px', 'bold-builder' ) 	=> '80',
						esc_html__( '85px', 'bold-builder' ) 	=> '85',
						esc_html__( '90px', 'bold-builder' ) 	=> '90',
						esc_html__( '95px', 'bold-builder' ) 	=> '95',
						esc_html__( '100px', 'bold-builder' ) 	=> '100'
					)
				),
				array( 'param_name' => 'bottom_spacing', 'type' => 'dropdown', 'heading' => esc_html__( 'Bottom spacing', 'bold-builder' ), 'preview' => true, 'responsive_override' => true,
					'value' => array(
						esc_html__( 'No spacing', 'bold-builder' ) 	=> 'none',
						esc_html__( 'Extra small', 'bold-builder' ) => 'extra_small',
						esc_html__( 'Small', 'bold-builder' ) 		=> 'small',		
						esc_html__( 'Normal', 'bold-builder' ) 		=> 'normal',
						esc_html__( 'Medium', 'bold-builder' ) 		=> 'medium',
						esc_html__( 'Large', 'bold-builder' ) 		=> 'large',
						esc_html__( 'Extra large', 'bold-builder' ) => 'extra_large',
						esc_html__( '5px', 'bold-builder' ) 	=> '5',
						esc_html__( '10px', 'bold-builder' ) 	=> '10',
						esc_html__( '15px', 'bold-builder' ) 	=> '15',
						esc_html__( '20px', 'bold-builder' ) 	=> '20',
						esc_html__( '25px', 'bold-builder' ) 	=> '25',
						esc_html__( '30px', 'bold-builder' ) 	=> '30',
						esc_html__( '35px', 'bold-builder' ) 	=> '35',
						esc_html__( '40px', 'bold-builder' ) 	=> '40',
						esc_html__( '45px', 'bold-builder' ) 	=> '45',
						esc_html__( '50px', 'bold-builder' ) 	=> '50',
						esc_html__( '55px', 'bold-builder' ) 	=> '55',
						esc_html__( '60px', 'bold-builder' ) 	=> '60',
						esc_html__( '65px', 'bold-builder' ) 	=> '65',
						esc_html__( '70px', 'bold-builder' ) 	=> '70',
						esc_html__( '75px', 'bold-builder' ) 	=> '75',
						esc_html__( '80px', 'bold-builder' ) 	=> '80',
						esc_html__( '85px', 'bold-builder' ) 	=> '85',
						esc_html__( '90px', 'bold-builder' ) 	=> '90',
						esc_html__( '95px', 'bold-builder' ) 	=> '95',
						esc_html__( '100px', 'bold-builder' ) 	=> '100'
					)
				),			
				array( 'param_name' => 'border_style', 'type' => 'dropdown', 'heading' => esc_html__( 'Border style', 'bold-builder' ), 'preview' => true,
					'value' => array(
						esc_html__( 'None', 'bold-builder' ) 	=> 'none',
						esc_html__( 'Solid', 'bold-builder' ) 	=> 'solid',
						esc_html__( 'Dotted', 'bold-builder' ) 	=> 'dotted',
						esc_html__( 'Dashed', 'bold-builder' ) 	=> 'dashed'
					)
				),
				array( 'param_name' => 'border_thickness', 'type' => 'dropdown', 'heading' => esc_html__( 'Border tickness', 'bold-builder' ), 'responsive_override' => true,
					'value' => array(
						esc_html__( '1px', 'bold-builder' ) => '1',
						esc_html__( '2px', 'bold-builder' ) => '2',
						esc_html__( '3px', 'bold-builder' ) => '3',
						esc_html__( '4px', 'bold-builder' ) => '4',
						esc_html__( '5px', 'bold-builder' ) => '5',
						esc_html__( '6px', 'bold-builder' ) => '6',
						esc_html__( '7px', 'bold-builder' ) => '7',
						esc_html__( '8px', 'bold-builder' ) => '8',
						esc_html__( '9px', 'bold-builder' ) => '9',
						esc_html__( '10px', 'bold-builder' ) => '10',
						esc_html__( '11px', 'bold-builder' ) => '11',
						esc_html__( '12px', 'bold-builder' ) => '12',
						esc_html__( '13px', 'bold-builder' ) => '13',
						esc_html__( '14px', 'bold-builder' ) => '14',
						esc_html__( '15px', 'bold-builder' ) => '15',
						esc_html__( '16px', 'bold-builder' ) => '16',
						esc_html__( '17px', 'bold-builder' ) => '17',
						esc_html__( '18px', 'bold-builder' ) => '18',
						esc_html__( '19px', 'bold-builder' ) => '19',
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
				// array( 'param_name' => 'border_thickness', 'type' => 'textfield', 'heading' => esc_html__( 'Border width', 'bold-builder' ), 'description' => esc_html__( 'E.g. 5px or 1em', 'bold-builder' ) ),
				array( 'param_name' => 'color_scheme', 'type' => 'dropdown', 'heading' => esc_html__( 'Color scheme', 'bold-builder' ), 'description' => esc_html__( 'Define color schemes in Bold Builder settings or define accent and alternate colors in theme customizer (if avaliable)', 'bold-builder' ), 'value' => $color_scheme_arr ),
				array( 'param_name' => 'icon', 'type' => 'iconpicker', 'heading' => esc_html__( 'Icon', 'bold-builder' ), 'group' => esc_html__( 'Icon & text', 'bold-builder' ), 'preview' => true ),
				array( 'param_name' => 'icon_size', 'type' => 'dropdown', 'heading' => esc_html__( 'Icon size', 'bold-builder' ), 'group' => esc_html__( 'Icon & text', 'bold-builder' ), 'responsive_override' => true, 'default' => 'normal',
					'value' => array(
						esc_html__( 'Extra small', 'bold-builder' ) => 'xsmall',
						esc_html__( 'Small', 'bold-builder' ) 		=> 'small',
						esc_html__( 'Normal', 'bold-builder' ) 		=> 'normal',
						esc_html__( 'Large', 'bold-builder' ) 		=> 'large',
						esc_html__( 'Extra large', 'bold-builder' ) => 'xlarge'
					)
				),
				array( 'param_name' => 'text', 'type' => 'textfield', 'heading' => esc_html__( 'Text', 'bold-builder' ), 'group' => esc_html__( 'Icon & text', 'bold-builder' ), 'placeholder' => esc_html__( 'Add Separator text', 'bold-builder' ), 'preview' => true, 'preview_strong' => true ),
				array( 'param_name' => 'text_size', 'type' => 'dropdown', 'heading' => esc_html__( 'Text size', 'bold-builder' ), 'group' => esc_html__( 'Icon & text', 'bold-builder' ), 'responsive_override' => true, 'default' => 'normal',
					'value' => array(
						esc_html__( 'Extra small', 'bold-builder' ) => 'xsmall',
						esc_html__( 'Small', 'bold-builder' ) 		=> 'small',
						esc_html__( 'Normal', 'bold-builder' ) 		=> 'normal',
						esc_html__( 'Large', 'bold-builder' ) 		=> 'large',
						esc_html__( 'Extra large', 'bold-builder' ) => 'xlarge'
					)
				),
			)
		) );
	}
}