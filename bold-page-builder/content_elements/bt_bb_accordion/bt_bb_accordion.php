<?php

class bt_bb_accordion extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'color_scheme' => '',
			'style'        => '',
			'shape'        => '',
			'closed'       => ''
		) ), $atts, $this->shortcode ) );

		$class = array( $this->shortcode );

		if ( $el_class != '' ) {
			$class[] = $el_class;
		}

		$id_attr = '';
		if ( $el_id != '' ) {
			$id_attr = ' ' . 'id="' . esc_attr( $el_id ) . '"';
		}
		
		$color_scheme_id = NULL;
		if ( is_numeric ( $color_scheme ) ) {
			$color_scheme_id = $color_scheme;
		} else if ( $color_scheme != '' ) {
			$color_scheme_id = bt_bb_get_color_scheme_id( $color_scheme );
		}
		$color_scheme_colors = bt_bb_get_color_scheme_colors_by_id( $color_scheme_id - 1 );
		if ( $color_scheme_colors ) $el_style .= '; --accordion-primary-color:' . $color_scheme_colors[0] . '; --accordion-secondary-color:' . $color_scheme_colors[1] . ';';
		if ( $color_scheme != '' ) $class[] = $this->prefix . 'color_scheme_' .  $color_scheme_id;

		$style_attr = '';
		$el_style = apply_filters( $this->shortcode . '_style', $el_style, $atts );
		if ( $el_style != '' ) {
			$style_attr = ' ' . 'style="' . esc_attr( $el_style ) . '"';
		}

		if ( $style != '' ) {
			$class[] = $this->prefix . 'style' . '_' . $style;
		}		

		if ( $shape != '' ) {
			$class[] = $this->prefix . 'shape' . '_' . $shape;
		}

		$data_attr = '';
		if ( $closed == 'closed' ) {
			$data_attr = ' ' . 'data-closed=closed';
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

		$content = do_shortcode( $content );

		$output = '';

		$output .= '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . $data_attr . '>' . $content . '</div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

	}

	function map_shortcode() {
		
		require_once( dirname(__FILE__) . '/../../content_elements_misc/misc.php' );
		$color_scheme_arr = bt_bb_get_color_scheme_param_array();			
		
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Accordion', 'bold-builder' ), 'description' => esc_html__( 'Accordion container', 'bold-builder' ), 'container' => 'vertical', 'toggle' => true, 'accept' => array( 'bt_bb_accordion_item' => true ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'color_scheme', 'type' => 'dropdown', 'heading' => esc_html__( 'Color scheme', 'bold-builder' ), 'description' => esc_html__( 'Define color schemes in Bold Builder settings or define accent and alternate colors in theme customizer (if avaliable)', 'bold-builder' ), 'value' => $color_scheme_arr, 'preview' => true ),
				array( 'param_name' => 'style', 'type' => 'dropdown', 'heading' => esc_html__( 'Style', 'bold-builder' ), 'preview' => true,
					'value' => array(
						esc_html__( 'Outline', 'bold-builder' ) 	=> 'outline',
						esc_html__( 'Filled', 'bold-builder' ) 		=> 'filled',
						esc_html__( 'Simple', 'bold-builder' ) 		=> 'simple'
					)
				),
				array( 'param_name' => 'shape', 'type' => 'dropdown', 'heading' => esc_html__( 'Shape', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Square', 'bold-builder' ) 			=> 'square',
						esc_html__( 'Soft Rounded', 'bold-builder' ) 	=> 'rounded',
						esc_html__( 'Hard Rounded', 'bold-builder' ) 	=> 'round'
					)
				),
				array( 'param_name' => 'closed', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-builder' ) => 'closed' ), 'heading' => esc_html__( 'All items closed initially', 'bold-builder' ), 'preview' => true )
			)
		) );
	}
}