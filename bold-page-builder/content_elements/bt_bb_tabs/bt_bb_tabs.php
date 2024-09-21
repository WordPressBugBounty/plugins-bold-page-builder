<?php

class bt_bb_tabs extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'color_scheme' => '',
			'style'        => '',
			'shape'        => ''
		) ), $atts, $this->shortcode ) );

		$class = array( $this->shortcode );
		$data_override_class = array();
		
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
		if ( $color_scheme_colors ) $el_style .= '; --tabs-primary-color:' . $color_scheme_colors[0] . '; --tabs-secondary-color:' . $color_scheme_colors[1] . ';';
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
		$content = explode( '%$%', $content );

		$output = '';

		$output .='<ul class="bt_bb_tabs_header">';
			for ( $i = 0; $i < count( $content ); $i = $i + 2 ) {
				$output .= $content[ $i ];
			}
		$output .='</ul>';
		$output .='<div class="bt_bb_tabs_tabs">';
			for ( $i = 1; $i < count( $content ); $i = $i + 2 ) {
				$output .= $content[ $i ];
			}
		$output .='</div>';

		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . ' data-bt-override-class="' . htmlspecialchars( json_encode( $data_override_class, JSON_FORCE_OBJECT ), ENT_QUOTES, 'UTF-8' ) . '">' . $output . '</div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

	}

	function map_shortcode() {
		
		require_once( dirname(__FILE__) . '/../../content_elements_misc/misc.php' );
		$color_scheme_arr = bt_bb_get_color_scheme_param_array();		
		
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Tabs', 'bold-builder' ), 'description' => esc_html__( 'Tabs container', 'bold-builder' ), 'container' => 'vertical', 'toggle' => true, 'accept' => array( 'bt_bb_tab_item' => true ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
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
				array( 'param_name' => 'color_scheme', 'type' => 'dropdown', 'heading' => esc_html__( 'Color scheme', 'bold-builder' ), 'description' => esc_html__( 'Define color schemes in Bold Builder settings or define accent and alternate colors in theme customizer (if avaliable)', 'bold-builder' ), 'value' => $color_scheme_arr, 'preview' => true )				
			)
		) );
	}
}