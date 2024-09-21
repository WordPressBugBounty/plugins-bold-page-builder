<?php

class bt_bb_contact_form_7 extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'contact_form_id' => ''
		) ), $atts, $this->shortcode ) );

		$class = array( $this->shortcode );
		
		if ( $el_class != '' ) {
			$class[] = $el_class;
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

		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . '>';
			if ( shortcode_exists( 'contact-form-7' ) ) {
				if ( $contact_form_id == 0 ) {
					$output .= '<p>' . esc_html__( 'Please select contact form.', 'bold-builder' ) . '</p>';
				} else {
					$output .= do_shortcode( '[contact-form-7 id="' . $contact_form_id . '"]' );
				}
			} else {
				$output .= '<p>' . esc_html__( 'Please install and activate Contact Form 7 plugin.', 'bold-builder' ) . '</p>';
			}
		$output .= '</div>';

		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

	}

	function map_shortcode() {
		
		$args = array( 'post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1 );
		$forms_data = array();
		if ( $data = get_posts( $args ) ) {
			$forms_data[ '' ] = 0; // required to support adding and editing element on FE
			foreach( $data as $key ) {
				$forms_data[ $key->post_title ] = $key->ID;
			}
		} else {
			$forms_data[ esc_html__( 'No contact form found', 'bold-builder' ) ] = 0;
		}

		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Contact Form 7', 'bold-builder' ), 'description' => esc_html__( 'Choose CF7 form', 'bold-builder' ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'contact_form_id', 'type' => 'dropdown', 'heading' => esc_html__( 'Contact Form 7', 'bold-builder' ), 'description' => esc_html__( 'Add new contact form on your Dashboard > Contact (Contact Form 7 plugin is required)', 'bold-builder' ), 'preview' => true,
					'value' => $forms_data )
			) )
		);
	}
}