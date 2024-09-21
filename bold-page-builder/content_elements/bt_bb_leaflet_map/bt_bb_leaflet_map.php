<?php

class bt_bb_leaflet_map extends BT_BB_Element {
	
	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'zoom'                  => '9',
			'max_zoom'              => '15',
			'height'                => '',
			'center_map'            => '',
			'predefined_style'      => '1',
			'custom_style'          => '',
			'scroll_wheel'          => '',
			'zoom_control'          => ''
		) ), $atts, $this->shortcode ) );

		$zoom		= $zoom == '' ? '9' : $zoom;
		$max_zoom	= $max_zoom == '' ? '15' : $max_zoom;
		
		$max_zoom = $max_zoom > $zoom ? $max_zoom : $zoom;
		$scroll_wheel = ( $scroll_wheel == 'scroll_wheel' || $scroll_wheel == 'yes' ) ? 1 : 0;
		$zoom_control = ( $zoom_control == 'zoom_control' || $zoom_control == 'yes' ) ? 1 : 0;

		// enqueue leaflet framework js and css 
		$leaflet_framework_path = plugin_dir_url( __FILE__ ) . '/';
		require_once( 'enqueue_lib.php' );
		// wp_enqueue_script( 'bt_bb_leaflet_map_js', plugin_dir_url( __FILE__ ) . '/bt_bb_leaflet_map.js' ); 

		$class_master = 'bt_bb_map';
		
		$class = array( $this->shortcode, $class_master );
		
		if ( $el_class != '' ) {
			$class[] = $el_class;
		}

		if ( $center_map == 'yes_no_overlay' ) {
			$class[] = $this->shortcode . '_no_overlay';
			$class[] = $class_master . '_no_overlay';
		}

		$id_attr = '';
		if ( $el_id != '' ) {
			$id_attr = ' ' . 'id="' . esc_attr($el_id) . '"';
		}

		$style_attr = '';
		$el_style = apply_filters( $this->shortcode . '_style', $el_style, $atts );
		if ( $el_style != '' ) {
			$style_attr = ' ' . 'style="' . esc_attr($el_style) . '"';
		}

		$style_height = '';
		if ( $height != '' ) {
			$style_height = ' ' . 'style="height:' . $height . '"';
		}
	
		$map_id = uniqid( 'map_canvas' );
		$content_html = do_shortcode( $content );
		$locations = substr_count( $content_html, 'class="bt_bb_leaflet_map_location ' );
		$locations_without_content = substr_count( $content_html, 'bt_bb_leaflet_map_location_without_content' );

		if ( $content != '' && $locations != $locations_without_content ) {
			$content = '<span class="' . esc_attr( $this->shortcode ) . '_content_toggler ' . esc_attr( $class_master ) . '_content_toggler"></span>'; 
			$content .= '<div class="' . esc_attr( $this->shortcode ) . '_content ' . esc_attr($class_master) . '_content">';
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

		$output = '<div class="' . esc_attr( $this->shortcode ) . '_map ' . esc_attr( $class_master ) . '_map" id="' . esc_attr( $map_id ) . '"' . $style_height . ' data-zoom="' . esc_attr( $zoom ) . '" data-max_zoom="' . esc_attr( $max_zoom ) . '" data-predefined_style="' . esc_attr( $predefined_style ) . '" data-custom_style="' . esc_attr( $custom_style ) . '" data-scroll_wheel="' . esc_attr( $scroll_wheel ) . '" data-zoom_control="' . esc_attr( $zoom_control ) . '"></div>';
		$output .= $content;
		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . ' data-center="' . esc_attr( $center_map ) . '">' . $output . '</div>';
		
		$output_script = 'var bt_bb_leaflet_' . $map_id . '_init_finished = false; ';
		$output_script .= ' document.addEventListener("readystatechange", function() { ';
			$output_script .= ' if ( ! bt_bb_leaflet_' . $map_id . '_init_finished && ( document.readyState === "interactive" || document.readyState === "complete" ) ) { ';
				$output_script .= ' if ( typeof( bt_bb_leaflet_init ) !== typeof(Function) ) { return false; } ';
				$output_script .= ' bt_bb_leaflet_init( "' . $map_id . '", ' . $zoom . ', ' . $max_zoom . ', ' . $predefined_style . ', "' . $custom_style . '", ' . $scroll_wheel . ', ' . $zoom_control . ' );';
				$output_script .= ' bt_bb_leaflet_' . $map_id . '_init_finished = true; ';
			$output_script .= '};';
		$output_script .= '}, false);';
		
		wp_register_script( 'boldthemes-script-bt-bb-leaflet-maps-js-init', '' );
		wp_enqueue_script( 'boldthemes-script-bt-bb-leaflet-maps-js-init' );
		wp_add_inline_script( 'boldthemes-script-bt-bb-leaflet-maps-js-init', $output_script );
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

		}

	function map_shortcode() {
		
		$center_map_desc = '';
		if ( BT_BB_FE::$editor_active ) {
			$center_map_desc = esc_html__( 'You can edit location(s) on back end.', 'bold-builder' );
			require_once( 'enqueue_lib.php' );
		}		
		
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'OpenStreetMap', 'boldthemes_framework_textdomain' ), 'description' => esc_html__( 'OpenStreetMap with custom content', 'boldthemes_framework_textdomain' ), 'toggle' => true, 'container' => 'vertical', 'accept' => array( 'bt_bb_leaflet_map_location' => true ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'zoom', 'type' => 'textfield', 'heading' => esc_html__( 'Zoom', 'boldthemes_framework_textdomain' ), 'placeholder' => esc_html__( 'E.g. 9', 'bold-builder' ), 'default' => '9', 'preview' => true ),
				array( 'param_name' => 'max_zoom', 'type' => 'textfield', 'heading' => esc_html__( 'Max zoom', 'boldthemes_framework_textdomain' ), 'placeholder' => esc_html__( 'E.g. 15', 'bold-builder' ), 'default' => '15', 'preview' => true ),
				array( 'param_name' => 'height', 'type' => 'textfield', 'heading' => esc_html__( 'Height', 'boldthemes_framework_textdomain' ), 'placeholder' => esc_html__( 'E.g. 250px', 'bold-builder' ), 'description' => esc_html__( 'Used when there is no content', 'boldthemes_framework_textdomain' ) ),
				array( 'param_name' => 'predefined_style', 'type' => 'dropdown', 'default' => '1', 'heading' => esc_html__( 'Predefined (base) map layer', 'boldthemes_framework_textdomain' ), 'preview' => true, 
					'value' => array(
						esc_html__( 'No base layer (use only additional map layers)', 'boldthemes_framework_textdomain' ) 	=> '0',
						esc_html__( 'Mapnik OSM', 'boldthemes_framework_textdomain' ) 										=> '1',
						//__( 'Wikimedia', 'boldthemes_framework_textdomain' ) 										=> '2',
						esc_html__( 'OSM Hot', 'boldthemes_framework_textdomain' ) 											=> '3',
						esc_html__( 'Stamen Watercolor', 'boldthemes_framework_textdomain' ) 								=> '4',
						esc_html__( 'Stamen Terrain', 'boldthemes_framework_textdomain' ) 									=> '5',
						esc_html__( 'Stamen Toner', 'boldthemes_framework_textdomain' ) 									=> '6',
						esc_html__( 'Carto Dark', 'boldthemes_framework_textdomain' )  										=> '7',
						esc_html__( 'Carto Light', 'boldthemes_framework_textdomain' )  									=> '8'
					)
				),
				array( 'param_name' => 'custom_style', 'type' => 'textarea_object', 'heading' => esc_html__( 'Additional map layers', 'boldthemes_framework_textdomain' ), 'description' => esc_html__( 'Add Map tiles URL. E.g. https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png, Attribution text separated by new line', 'boldthemes_framework_textdomain' ) ),
				array( 'param_name' => 'center_map', 'type' => 'dropdown', 'heading' => esc_html__( 'Center map', 'boldthemes_framework_textdomain' ), 'description' => $center_map_desc,
					'value' => array(
						esc_html__( 'No (use first location as center)', 'boldthemes_framework_textdomain' ) 	=> 'no',
						esc_html__( 'Yes', 'boldthemes_framework_textdomain' ) 									=> 'yes',
						esc_html__( 'Yes (without overlay initially)', 'boldthemes_framework_textdomain' ) 		=> 'yes_no_overlay'
					)
				),
				array( 'param_name' => 'scroll_wheel',  'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'boldthemes_framework_textdomain' ) => 'yes' ), 'default' => 'yes', 'heading' => esc_html__( 'Enable scroll wheel zoom on map', 'boldthemes_framework_textdomain' ), 'preview' => true
				),
				array( 'param_name' => 'zoom_control',  'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'boldthemes_framework_textdomain' ) => 'yes' ), 'default' => 'yes', 'heading' => esc_html__( 'Enable zoom control on map', 'boldthemes_framework_textdomain' ), 'preview' => true
				),
			)
		) );
	}
}

