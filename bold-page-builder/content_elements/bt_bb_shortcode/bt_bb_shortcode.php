<?php

class bt_bb_shortcode extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'shortcode_content' => ''
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
		
		$shortcode_content = str_ireplace( array( '`{`', '`}`', '``' ), array( '[', ']', '"' ), $shortcode_content );
		
		if ( $shortcode_content == '' ) {
			$shortcode_content = '<div>' . esc_html__( 'Please insert shortcode.', 'bold-builder' ) . '</div>';
		}
		
		$output = '<div class="' . esc_attr( implode( ' ', $class ) ) . '">' . do_shortcode( $shortcode_content ) . '</div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );
		
		return $output;
		
	}
	
	function add_params() {
		// removes default params from BT_BB_Element
	}

	function map_shortcode() {
		$desc = '';
		if ( BT_BB_FE::$editor_active ) {
			$desc = esc_html__( 'Save and reload page to make sure shortcode is properly initialized.', 'bold-builder' );
		}		
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Shortcode', 'bold-builder' ), 'description' => esc_html__( 'Custom shortcode', 'bold-builder' ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'shortcode_content', 'type' => 'textfield', 'heading' => esc_html__( 'Shortcode', 'bold-builder' ), 'placeholder' => esc_html__( 'Add your shortcode', 'bold-builder' ), 'description' => $desc )
			)
		) );
	}
}