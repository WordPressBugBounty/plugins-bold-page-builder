<?php

class bt_bb_google_maps extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'api_key'      	=> '',
			'map_id'   		=> '',
			'zoom'         	=> '',
			'height'       	=> '',
			'custom_style' 	=> '',
			'map_type'   	=> 'interactive',
			'center_map'   	=> ''
		) ), $atts, $this->shortcode ) );
		
		$class_master = 'bt_bb_map';
		
		$class = array( $this->shortcode, $class_master );
		$data_override_class = array();
		
		if ( $el_class != '' ) {
			$class[] = $el_class;
		}

		if ( $center_map == 'yes_no_overlay' ) {
			$class[] = $this->shortcode . '_no_overlay';
			$class[] = $class_master . '_no_overlay';
		}

		if ( $map_type != '' ) {
			$class[] = $this->prefix . 'map_type' . '_' . $map_type;
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

		wp_deregister_script( 'gmaps_api' );
		wp_dequeue_script( 'gmaps_api' ); // dequeue one from map_shortcode
		if ( $api_key != '' ) {
			wp_enqueue_script( 
				'gmaps_api',
				'https://maps.googleapis.com/maps/api/js?key=' . $api_key . '&loading=async&callback=window.bt_bb_init_all_maps&libraries=marker',
				array(),
				false,
				array(
					'strategy'  => 'async',
					'in_footer' => true,
				)
			);
		} else {
			wp_enqueue_script( 
				'gmaps_api',
				'https://maps.googleapis.com/maps/api/js?v=&sensor=false&loading=async&callback=window.bt_bb_init_all_maps&libraries=marker',
				array(),
				false,
				array(
					'strategy'  => 'async',
					'in_footer' => true,
				)
			);
		}
		// wp_script_add_data( 'gmaps_api', 'strategy', 'async' );
		
		
		if ( $zoom == '' ) {
			$zoom = 14;
		}

		$style_height = '';
		if ( $height != '' && $map_type == 'interactive' ) {
			$style_height = ' ' . 'style="height:' . esc_attr( $height ) . '"';
		}
		
		$map_id = $map_id == '' ? uniqid( 'map_canvas' ) : $map_id; // 'map_canvas' used in JS, do not modify

		$content_html = do_shortcode( $content );

		$locations = substr_count( $content_html, 'class="bt_bb_google_maps_location ' ); // fe editor data-base contains the same value
		$locations_without_content = substr_count( $content_html, 'bt_bb_google_maps_location_without_content' );
	
		if ( $content != '' && $locations != $locations_without_content ) {
			$content = '<span class="' . esc_attr( $this->shortcode ) . '_content_toggler ' . esc_attr( $class_master ) . '_content_toggler"></span>'; 
			$content .= '<div class="' . esc_attr( $this->shortcode ) . '_content ' . esc_attr( $class_master ) . '_content">';
				$content .= '<div class="' . esc_attr( $this->shortcode ) . '_content_wrapper ' . esc_attr( $class_master ) . '_content_wrapper">' ;
				$content .= $content_html ;
				$content .= '</div>';
			$content .= '</div>';
			$class[] = $this->shortcode . '_with_content';
			$class[] = $class_master . '_with_content';
			$style_height = '';
		} else {
		   $content = $content_html;
		}
		
		$data_override_class = [];

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

		$output = '<div class="' . esc_attr( $this->shortcode ) . '_map ' . esc_attr( $class_master ) . '_map" id="' . esc_attr( $map_id ) . '" data-height="' . esc_attr( intval( $height ) ) . '" data-api-key="' . esc_attr( $api_key ) . '" data-map-type="' . esc_attr( $map_type ) . '" data-zoom="' . esc_attr( intval( $zoom ) ) . '" data-init-finished="false" data-custom-style="' . esc_attr( $custom_style ) . '"' . $style_height . '">';
		$output .= '</div>';
		$output .= $content;
		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . ' data-center="' . esc_attr( $center_map ) . '" data-bt-override-class="' . htmlspecialchars( json_encode( $data_override_class, JSON_FORCE_OBJECT ), ENT_QUOTES, 'UTF-8' ) . '">' . $output . '</div>';

		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

	}

	function map_shortcode() {
		
		if ( BT_BB_FE::$editor_active ) {
			wp_enqueue_script( // used when adding new Google Maps elements via drag and drop (no existing Google Maps elements on the page)
				'gmaps_api',
				'https://maps.googleapis.com/maps/api/js?v=&sensor=false&loading=async&callback=window.bt_bb_init_all_maps',
				array(),
				false,
				array(
					'strategy'  => 'async',
					'in_footer' => true,
				)
			);
		}
		
		if ( BT_BB_FE::$editor_active ) {
			$api_key_param = array( 'param_name' => 'api_key', 'type' => 'textfield', 'heading' => esc_html__( 'API key', 'bold-builder' ), 'description' => __( 'Google Maps require an API key for site domains.<br><a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Get your API key here</a>. Save and reload page to make sure API is initialized.', 'bold-builder' ) );
			$center_map_desc = esc_html__( 'You can edit location(s) on back end.', 'bold-builder' );
		} else {
			$api_key_param = array( 'param_name' => 'api_key', 'type' => 'textfield', 'heading' => esc_html__( 'API key', 'bold-builder' ), 'description' => __( 'Google Maps require an API key for site domains. <a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Get your API key here</a>.', 'bold-builder' ) );
			$center_map_desc = '';
		}
		
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Google Maps', 'bold-builder' ), 'description' => esc_html__( 'Google Maps map with custom content', 'bold-builder' ), 'container' => 'vertical', 'accept' => array( 'bt_bb_google_maps_location' => true ), 'toggle' => true, 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				$api_key_param,
				array( 'param_name' => 'zoom', 'type' => 'textfield', 'heading' => esc_html__( 'Zoom', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. 14', 'bold-builder' ) ),
				array( 'param_name' => 'height', 'type' => 'textfield', 'heading' => esc_html__( 'Height', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. 250px', 'bold-builder' ), 'description' => esc_html__( 'Used for static map or for interactive map without content; static image map width is 1280px ', 'bold-builder' ) ),
				array( 'param_name' => 'map_id', 'type' => 'textfield', 'heading' => esc_html__( 'Map ID (Google API)', 'bold-builder' ), 'description' => __( 'The Google Maps Platform offers <a href="https://developers.google.com/maps/documentation/cloud-customization" target="_blank">cloud-based maps styling</a> in the Google Cloud Console. Copy Map ID there and copy here.', 'bold-builder' ) ),
				array( 'param_name' => 'custom_style', 'type' => 'textarea_object', 'heading' => esc_html__( 'Custom map style array (interactive map) or querystring (static image map)', 'bold-builder' ), 'description' => __( 'Find more custom styles on <a href="https://snazzymaps.com/" target="_blank">Snazzy Maps</a> or <a href="https://mapstyle.withgoogle.com/" target="_blank">Map Style (for static maps)</a>. ', 'bold-builder' ) ),
				array( 'param_name' => 'map_type', 'type' => 'dropdown', 'default' => 'interactive', 'heading' => esc_html__( 'Map type', 'bold-builder' ),
					'value' => array(
						esc_html__( 'Interactive (JavaScript API)', 'bold-builder' ) 		=> 'interactive',
						esc_html__( 'Static image (Maps Static API)', 'bold-builder' ) 		=> 'static'
					)
				),
				array( 'param_name' => 'center_map', 'type' => 'dropdown', 'heading' => esc_html__( 'Center map', 'bold-builder' ), 'description' => $center_map_desc,
					'value' => array(
						esc_html__( 'No (use first location as center)', 'bold-builder' ) 	=> 'no',
						esc_html__( 'Yes', 'bold-builder' ) 								=> 'yes',
						esc_html__( 'Yes (without overlay initially)', 'bold-builder' ) 	=> 'yes_no_overlay'
					)
				),
			)
		) );
	}
}