<?php

class bt_bb_leaflet_map_location extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'latitude'  => '0',
			'longitude' => '0',
			'icon'      => ''
		) ), $atts, $this->shortcode ) );
		
        $class_master = 'bt_bb_map_location'; 
		
		$class = array( $this->shortcode, $class_master );
		
		if ( $latitude == '' ) {
			$latitude = 0;
		}
		
		if ( $longitude == '' ) {
			$longitude = 0;
		}
                
        if ( $el_class != '' ) {
			$class[] = $el_class;
		}
		
		if ( $content == '' ) {
			$class[] = $this->shortcode . '_without_content';
            $class[] = $class_master . '_without_content';
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

		if ( $icon != '' ) {
			if ( is_numeric( $icon ) ) {
				$icon = wp_get_attachment_image_src( $icon, 'full' );
				if ( $icon ) {
					$icon = $icon[0];
				} else {
					$icon = '';
				}			
			} else {
				$icon = esc_url_raw( $icon );
			}
		} else {
			$icon = BT_BB_Root::$path . 'content_elements/bt_bb_leaflet_map/leafletmap/img/marker-icon.png';
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

		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . '  data-lat="' . esc_attr( $latitude ) . '" data-lng="' . esc_attr( $longitude ) . '" data-icon="' . esc_attr($icon) . '">' . do_shortcode( $content ) . '</div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

	}

	function map_shortcode() {
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Leaflet Maps Location', 'boldthemes_framework_textdomain' ), 'description' => esc_html__( 'OpenSteet Map Location', 'boldthemes_framework_textdomain' ), 'container' => 'vertical', 'as_child' => array( 'only' => 'bt_bb_leaflet_map' ), 'accept' => array( 'bt_bb_headline' => true, 'bt_bb_text' => true, 'bt_bb_button' => true, 'bt_bb_icon' => true, 'bt_bb_service_icon' => true, 'bt_bb_service' => true, 'bt_bb_image' => true, 'bt_bb_separator' => true ), 'toggle' => true, 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'latitude', 'type' => 'textfield', 'heading' => esc_html__( 'Latitude', 'boldthemes_framework_textdomain' ), 'placeholder' => esc_html__( 'E.g. 40.000000', 'bold-builder' ), 'preview' => true ),
				array( 'param_name' => 'longitude', 'type' => 'textfield', 'heading' => esc_html__( 'Longitude', 'boldthemes_framework_textdomain' ), 'placeholder' => esc_html__( 'E.g. 40.000000', 'bold-builder' ), 'preview' => true ),
				array( 'param_name' => 'icon', 'type' => 'attach_image', 'heading' => esc_html__( 'Icon', 'boldthemes_framework_textdomain' ), 'preview' => true )
			)
		) );
	}
}

