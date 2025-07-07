<?php

class bt_bb_tab_item extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {	
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'title'  => ''
		) ), $atts, $this->shortcode ) );
		
		$class = array( $this->shortcode );

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
		
		$output1 = '<li><span>' . wp_kses_post( $title ) . '</span></li>';
		
		if ( strpos( $content, 'bt_bb_image' ) ) {
			$class[] = 'has_bt_bb_image';
		}

		$output2 = '<div class="' . esc_attr( implode( ' ', $class ) ) . '">
			<div class="bt_bb_tab_content">' . do_shortcode( $content ) . '</div>
		</div>';
		
		$output .= $output1 . '%$%' . $output2 . '%$%';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );
		
		return $output;

	}

	function add_params() {
		// removes default params from BT_BB_Element
	}

	function map_shortcode() {
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Tab', 'bold-builder' ), 'description' => esc_html__( 'Tab item', 'bold-builder' ), 'container' => 'vertical', 'toggle' => true, 'accept' => array( 'bt_bb_section' => false, 'bt_bb_row' => false, 'bt_bb_column' => false, 'bt_bb_column_inner' => false, 'bt_bb_tabs' => false, 'bt_bb_tab_item' => false, 'bt_bb_accordion' => false, 'bt_bb_accordion_item' => false, 'bt_bb_cost_calculator_item' => false, 'bt_cc_group' => false, 'bt_cc_multiply' => false, 'bt_cc_item' => false, 'bt_bb_content_slider_item' => false, 'bt_bb_google_maps_location' => false, '_content' => false ), 'accept_all' => true, 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'title', 'type' => 'textfield', 'heading' => esc_html__( 'Title', 'bold-builder' ), 'placeholder' => esc_html__( 'Add Tab Item title', 'bold-builder' ), 'preview' => true )			
			)
		) );
	}
}