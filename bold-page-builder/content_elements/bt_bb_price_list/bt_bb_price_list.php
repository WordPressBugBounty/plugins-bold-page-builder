<?php

class bt_bb_price_list extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'title'        			=> '',
			'subtitle'     			=> '',
			'currency'     			=> '',
			'currency_position'     => '',
			'price'        			=> '',			
			'items'        			=> '',
			'color_scheme' 			=> ''
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
		if ( $color_scheme_colors ) $el_style .= '; --primary-color:' . $color_scheme_colors[0] . '; --secondary-color:' . $color_scheme_colors[1] . ';';
		if ( $color_scheme != '' ) $class[] = $this->prefix . 'color_scheme_' .  $color_scheme_id;

		$style_attr = '';
		$el_style = apply_filters( $this->shortcode . '_style', $el_style, $atts );
		if ( $el_style != '' ) {
			$style_attr = ' ' . 'style="' . esc_attr( $el_style ) . '"';
		}
				
		$output = '<div class="' . esc_attr( $this->shortcode ) . '_title">' . $title . '</div>';
		$output .= '<div class="' . esc_attr( $this->shortcode ) . '_subtitle">' . $subtitle . '</div>';
		$output .= '<div class="' . esc_attr( $this->shortcode ) . '_price"><span class="' . esc_attr( $this->shortcode ) . '_currency">' . $currency . '</span><span class="' . esc_attr( $this->shortcode ) . '_amount">' . $price . '</span></div>';

		if ( $items != '' ) {
                    
			if ( base64_encode(base64_decode($items, true)) === $items){
				$items = base64_decode( $items );
			}
                        
			$items_arr = preg_split( '/$\R?^/m', $items );

			$output .= '<ul>';
				foreach ( $items_arr as $item ) {
					$output .= '<li>' . $item . '</li>';
				}
			$output .= '</ul>';
		
		}
		
		if ( $currency_position != '' ) {
			$class[] = $this->prefix . 'currency_position' . '_' . $currency_position ;
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

		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . '>' . $output . '</div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

	}

	function map_shortcode() {
		
		require_once( dirname(__FILE__) . '/../../content_elements_misc/misc.php' );
		$color_scheme_arr = bt_bb_get_color_scheme_param_array();			
		
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Price List', 'bold-builder' ), 'description' => esc_html__( 'List of items with total price', 'bold-builder' ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'title', 'type' => 'textfield', 'heading' => esc_html__( 'Title', 'bold-builder' ), 'placeholder' => esc_html__( 'Add Price List title', 'bold-builder' ), 'preview' => true ),
				array( 'param_name' => 'subtitle', 'type' => 'textfield', 'heading' => esc_html__( 'Subtitle', 'bold-builder' ), 'placeholder' => esc_html__( 'Add Price List subtitle', 'bold-builder' ) ),
				array( 'param_name' => 'currency', 'type' => 'textfield', 'heading' => esc_html__( 'Currency', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. $', 'bold-builder' ) ),
				array( 'param_name' => 'currency_position', 'type' => 'dropdown', 'heading' => esc_html__( 'Currency position', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Left', 'bold-builder' ) 	=> 'left',
						esc_html__( 'Right', 'bold-builder' ) 	=> 'right'
					)
				),
				array( 'param_name' => 'price', 'type' => 'textfield', 'heading' => esc_html__( 'Price', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. 299.99', 'bold-builder' ) ),				
				array( 'param_name' => 'items', 'type' => 'textarea_object', 'heading' => esc_html__( 'Items', 'bold-builder' ), 'description' => esc_html__( 'Add Price List items separated by new line', 'bold-builder' ) ),
				array( 'param_name' => 'color_scheme', 'type' => 'dropdown', 'heading' => esc_html__( 'Color scheme', 'bold-builder' ), 'description' => esc_html__( 'Define color schemes in Bold Builder settings or define accent and alternate colors in theme customizer (if avaliable)', 'bold-builder' ), 'value' => $color_scheme_arr, 'preview' => true ),			
			)
		) );
	}
}