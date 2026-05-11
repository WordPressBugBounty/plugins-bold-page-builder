<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

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
		
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Separator', 'bold-page-builder' ), 'description' => esc_html__( 'Separator line', 'bold-page-builder' ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'top_spacing', 'type' => 'dropdown', 'heading' => esc_html__( 'Top spacing', 'bold-page-builder' ), 'preview' => true, 'responsive_override' => true,
					'value' => array(
						esc_html__( 'No spacing', 'bold-page-builder' ) 	=> 'none',
						esc_html__( 'Extra small', 'bold-page-builder' ) => 'extra_small',
						esc_html__( 'Small', 'bold-page-builder' ) 		=> 'small',		
						esc_html__( 'Normal', 'bold-page-builder' ) 		=> 'normal',
						esc_html__( 'Medium', 'bold-page-builder' ) 		=> 'medium',
						esc_html__( 'Large', 'bold-page-builder' ) 		=> 'large',
						esc_html__( 'Extra large', 'bold-page-builder' ) => 'extra_large',
						esc_html__( '5px', 'bold-page-builder' ) 	=> '5',
						esc_html__( '10px', 'bold-page-builder' ) 	=> '10',
						esc_html__( '15px', 'bold-page-builder' ) 	=> '15',
						esc_html__( '20px', 'bold-page-builder' ) 	=> '20',
						esc_html__( '25px', 'bold-page-builder' ) 	=> '25',
						esc_html__( '30px', 'bold-page-builder' ) 	=> '30',
						esc_html__( '35px', 'bold-page-builder' ) 	=> '35',
						esc_html__( '40px', 'bold-page-builder' ) 	=> '40',
						esc_html__( '45px', 'bold-page-builder' ) 	=> '45',
						esc_html__( '50px', 'bold-page-builder' ) 	=> '50',
						esc_html__( '55px', 'bold-page-builder' ) 	=> '55',
						esc_html__( '60px', 'bold-page-builder' ) 	=> '60',
						esc_html__( '65px', 'bold-page-builder' ) 	=> '65',
						esc_html__( '70px', 'bold-page-builder' ) 	=> '70',
						esc_html__( '75px', 'bold-page-builder' ) 	=> '75',
						esc_html__( '80px', 'bold-page-builder' ) 	=> '80',
						esc_html__( '85px', 'bold-page-builder' ) 	=> '85',
						esc_html__( '90px', 'bold-page-builder' ) 	=> '90',
						esc_html__( '95px', 'bold-page-builder' ) 	=> '95',
						esc_html__( '100px', 'bold-page-builder' ) 	=> '100'
					)
				),
				array( 'param_name' => 'bottom_spacing', 'type' => 'dropdown', 'heading' => esc_html__( 'Bottom spacing', 'bold-page-builder' ), 'preview' => true, 'responsive_override' => true,
					'value' => array(
						esc_html__( 'No spacing', 'bold-page-builder' ) 	=> 'none',
						esc_html__( 'Extra small', 'bold-page-builder' ) => 'extra_small',
						esc_html__( 'Small', 'bold-page-builder' ) 		=> 'small',		
						esc_html__( 'Normal', 'bold-page-builder' ) 		=> 'normal',
						esc_html__( 'Medium', 'bold-page-builder' ) 		=> 'medium',
						esc_html__( 'Large', 'bold-page-builder' ) 		=> 'large',
						esc_html__( 'Extra large', 'bold-page-builder' ) => 'extra_large',
						esc_html__( '5px', 'bold-page-builder' ) 	=> '5',
						esc_html__( '10px', 'bold-page-builder' ) 	=> '10',
						esc_html__( '15px', 'bold-page-builder' ) 	=> '15',
						esc_html__( '20px', 'bold-page-builder' ) 	=> '20',
						esc_html__( '25px', 'bold-page-builder' ) 	=> '25',
						esc_html__( '30px', 'bold-page-builder' ) 	=> '30',
						esc_html__( '35px', 'bold-page-builder' ) 	=> '35',
						esc_html__( '40px', 'bold-page-builder' ) 	=> '40',
						esc_html__( '45px', 'bold-page-builder' ) 	=> '45',
						esc_html__( '50px', 'bold-page-builder' ) 	=> '50',
						esc_html__( '55px', 'bold-page-builder' ) 	=> '55',
						esc_html__( '60px', 'bold-page-builder' ) 	=> '60',
						esc_html__( '65px', 'bold-page-builder' ) 	=> '65',
						esc_html__( '70px', 'bold-page-builder' ) 	=> '70',
						esc_html__( '75px', 'bold-page-builder' ) 	=> '75',
						esc_html__( '80px', 'bold-page-builder' ) 	=> '80',
						esc_html__( '85px', 'bold-page-builder' ) 	=> '85',
						esc_html__( '90px', 'bold-page-builder' ) 	=> '90',
						esc_html__( '95px', 'bold-page-builder' ) 	=> '95',
						esc_html__( '100px', 'bold-page-builder' ) 	=> '100'
					)
				),			
				array( 'param_name' => 'border_style', 'type' => 'dropdown', 'heading' => esc_html__( 'Border style', 'bold-page-builder' ), 'preview' => true,
					'value' => array(
						esc_html__( 'None', 'bold-page-builder' ) 	=> 'none',
						esc_html__( 'Solid', 'bold-page-builder' ) 	=> 'solid',
						esc_html__( 'Dotted', 'bold-page-builder' ) 	=> 'dotted',
						esc_html__( 'Dashed', 'bold-page-builder' ) 	=> 'dashed'
					)
				),
				array( 'param_name' => 'border_thickness', 'type' => 'dropdown', 'heading' => esc_html__( 'Border tickness', 'bold-page-builder' ), 'responsive_override' => true,
					'value' => array(
						esc_html__( '1px', 'bold-page-builder' ) => '1',
						esc_html__( '2px', 'bold-page-builder' ) => '2',
						esc_html__( '3px', 'bold-page-builder' ) => '3',
						esc_html__( '4px', 'bold-page-builder' ) => '4',
						esc_html__( '5px', 'bold-page-builder' ) => '5',
						esc_html__( '6px', 'bold-page-builder' ) => '6',
						esc_html__( '7px', 'bold-page-builder' ) => '7',
						esc_html__( '8px', 'bold-page-builder' ) => '8',
						esc_html__( '9px', 'bold-page-builder' ) => '9',
						esc_html__( '10px', 'bold-page-builder' ) => '10',
						esc_html__( '11px', 'bold-page-builder' ) => '11',
						esc_html__( '12px', 'bold-page-builder' ) => '12',
						esc_html__( '13px', 'bold-page-builder' ) => '13',
						esc_html__( '14px', 'bold-page-builder' ) => '14',
						esc_html__( '15px', 'bold-page-builder' ) => '15',
						esc_html__( '16px', 'bold-page-builder' ) => '16',
						esc_html__( '17px', 'bold-page-builder' ) => '17',
						esc_html__( '18px', 'bold-page-builder' ) => '18',
						esc_html__( '19px', 'bold-page-builder' ) => '19',
						esc_html__( '20px', 'bold-page-builder' ) => '20',
						esc_html__( '25px', 'bold-page-builder' ) => '25',
						esc_html__( '30px', 'bold-page-builder' ) => '30',
						esc_html__( '35px', 'bold-page-builder' ) => '35',
						esc_html__( '40px', 'bold-page-builder' ) => '40',
						esc_html__( '45px', 'bold-page-builder' ) => '45',
						esc_html__( '50px', 'bold-page-builder' ) => '50',
						esc_html__( '55px', 'bold-page-builder' ) => '55',
						esc_html__( '60px', 'bold-page-builder' ) => '60',
						esc_html__( '65px', 'bold-page-builder' ) => '65',
						esc_html__( '70px', 'bold-page-builder' ) => '70',
						esc_html__( '75px', 'bold-page-builder' ) => '75',
						esc_html__( '80px', 'bold-page-builder' ) => '80',
						esc_html__( '85px', 'bold-page-builder' ) => '85',
						esc_html__( '90px', 'bold-page-builder' ) => '90',
						esc_html__( '95px', 'bold-page-builder' ) => '95',
						esc_html__( '100px', 'bold-page-builder' ) => '100'
					)
				),
				// array( 'param_name' => 'border_thickness', 'type' => 'textfield', 'heading' => esc_html__( 'Border width', 'bold-page-builder' ), 'description' => esc_html__( 'E.g. 5px or 1em', 'bold-page-builder' ) ),
				array( 'param_name' => 'color_scheme', 'type' => 'dropdown', 'heading' => esc_html__( 'Color scheme', 'bold-page-builder' ), 'description' => esc_html__( 'Define color schemes in Bold Builder settings or define accent and alternate colors in theme customizer (if avaliable)', 'bold-page-builder' ), 'value' => $color_scheme_arr ),
				array( 'param_name' => 'icon', 'type' => 'iconpicker', 'heading' => esc_html__( 'Icon', 'bold-page-builder' ), 'group' => esc_html__( 'Icon & text', 'bold-page-builder' ), 'preview' => true ),
				array( 'param_name' => 'icon_size', 'type' => 'dropdown', 'heading' => esc_html__( 'Icon size', 'bold-page-builder' ), 'group' => esc_html__( 'Icon & text', 'bold-page-builder' ), 'responsive_override' => true, 'default' => 'normal',
					'value' => array(
						esc_html__( 'Extra small', 'bold-page-builder' ) => 'xsmall',
						esc_html__( 'Small', 'bold-page-builder' ) 		=> 'small',
						esc_html__( 'Normal', 'bold-page-builder' ) 		=> 'normal',
						esc_html__( 'Large', 'bold-page-builder' ) 		=> 'large',
						esc_html__( 'Extra large', 'bold-page-builder' ) => 'xlarge'
					)
				),
				array( 'param_name' => 'text', 'type' => 'textfield', 'heading' => esc_html__( 'Text', 'bold-page-builder' ), 'group' => esc_html__( 'Icon & text', 'bold-page-builder' ), 'placeholder' => esc_html__( 'Add Separator text', 'bold-page-builder' ), 'preview' => true, 'preview_strong' => true ),
				array( 'param_name' => 'text_size', 'type' => 'dropdown', 'heading' => esc_html__( 'Text size', 'bold-page-builder' ), 'group' => esc_html__( 'Icon & text', 'bold-page-builder' ), 'responsive_override' => true, 'default' => 'normal',
					'value' => array(
						esc_html__( 'Extra small', 'bold-page-builder' ) => 'xsmall',
						esc_html__( 'Small', 'bold-page-builder' ) 		=> 'small',
						esc_html__( 'Normal', 'bold-page-builder' ) 		=> 'normal',
						esc_html__( 'Large', 'bold-page-builder' ) 		=> 'large',
						esc_html__( 'Extra large', 'bold-page-builder' ) => 'xlarge'
					)
				),
			)
		) );
	}
}