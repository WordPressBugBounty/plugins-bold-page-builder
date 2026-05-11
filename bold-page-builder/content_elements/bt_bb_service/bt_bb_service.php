<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class bt_bb_service extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'ai_prompt'    => '',
			'icon'         => '',
			'title'        => '',
			'html_tag'     => 'div',
			'text'         => '',
			'url'          => '',
			'target'       => '',
			'color_scheme' => '',
			'style'        => '',
			'size'         => '',
			'shape'        => '',
			'align'        => ''
		) ), $atts, $this->shortcode ) );
		
		$title = html_entity_decode( $title, ENT_QUOTES, 'UTF-8' );
		$text  = html_entity_decode( $text,  ENT_QUOTES, 'UTF-8' );

		$title = str_ireplace( array( '``' ), array( '"' ), $title );
		$text  = str_ireplace( array( '``' ), array( '"' ), $text );

		$title = wp_kses_post( $title );
		$text  = wp_kses_post( $text );

		$class = array( $this->shortcode );
		$data_override_class = array();
		
		$html_tag = str_replace( [ ' ', '=', '&', 'script' ], '', $html_tag );

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

		if ( $style != '' ) {
			$class[] = $this->prefix . 'style' . '_' . $style;
		}

		$this->responsive_data_override_class(
			$class, $data_override_class,
			array(
				'prefix' => $this->prefix,
				'param' => 'size',
				'value' => $size
			)
		);

		if ( $shape != '' ) {
			$class[] = $this->prefix . 'shape' . '_' . $shape;
		}
		
		if ( $target != '' ) {
			$class[] = $this->prefix . 'target' . $target;
		}
		
		if ( $target == '_lightbox' ) {
			$class[] = 'bt_bb_use_lightbox';
			$target = '_blank';
		}

		$this->responsive_data_override_class(
			$class, $data_override_class,
			array(
				'prefix' => $this->prefix,
				'param' => 'align',
				'value' => $align
			)
		);
		
		$link = bt_bb_get_url( $url );

		$icon_title = wp_strip_all_tags($title);
		
		$icon = bt_bb_icon::get_html( $icon, '', $link, $icon_title, $target );

		if ( $link != '' ) {
			if ( $title != '' ) $title = '<a href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '" title="' . esc_attr( $title ) . '">' . $title . '</a>';
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

		$output = $icon;

		$output .= '<div class="' . esc_attr( $this->shortcode ) . '_content">';
			if ( $title != '' ) $output .= '<'. $html_tag . ' class="' . esc_attr( $this->shortcode ) . '_content_title">' . $title . '</'. $html_tag . '>';
			if ( $text != '' ) $output .= '<div class="' . esc_attr( $this->shortcode ) . '_content_text">' . nl2br( $text ) . '</div>';
		$output .= '</div>';

		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . ' data-bt-override-class="' . htmlspecialchars( json_encode( $data_override_class, JSON_FORCE_OBJECT ), ENT_QUOTES, 'UTF-8' ) . '">' . $output . '</div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

	}

	function map_shortcode() {

		require_once( dirname(__FILE__) . '/../../content_elements_misc/misc.php' );
		$color_scheme_arr = bt_bb_get_color_scheme_param_array();

		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Service', 'bold-page-builder' ), 'description' => esc_html__( 'Service with text (and AI help)', 'bold-page-builder' ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array(
					'param_name' => 'ai_prompt',
					'type' => 'ai_prompt',
					'target' =>
						array(
							'title' => array( 'alias' => 'title', 'title' => esc_html__( 'Title', 'bold-page-builder' ) ),
							'text' => array( 'alias' => 'text', 'title' => esc_html__( 'Text', 'bold-page-builder' ) ),
						),
					'system_prompt' => 'You are a copywriter and your GOAL is to help users generate website content. Based on the user prompt generate title and text for the website page.',
				),
				array( 'param_name' => 'icon', 'type' => 'iconpicker', 'heading' => esc_html__( 'Icon', 'bold-page-builder' ), 'preview' => true ),
				array( 'param_name' => 'title', 'type' => 'textfield', 'heading' => esc_html__( 'Title', 'bold-page-builder' ), 'placeholder' => esc_html__( 'Add Service title', 'bold-page-builder' ), 'preview' => true ),
				array( 'param_name' => 'html_tag', 'type' => 'dropdown', 'heading' => esc_html__( 'Title HTML tag', 'bold-page-builder' ), 'preview' => true, 'default' => 'div',
					'value' => array(
						esc_html__( 'div', 'bold-page-builder' ) 	=> 'div',
						esc_html__( 'h1', 'bold-page-builder' ) 		=> 'h1',
						esc_html__( 'h2', 'bold-page-builder' ) 		=> 'h2',
						esc_html__( 'h3', 'bold-page-builder' ) 		=> 'h3',
						esc_html__( 'h4', 'bold-page-builder' ) 		=> 'h4',
						esc_html__( 'h5', 'bold-page-builder' ) 		=> 'h5',
						esc_html__( 'h6', 'bold-page-builder' ) 		=> 'h6',
						esc_html__( 'span', 'bold-page-builder' ) 	=> 'span',
						esc_html__( 'p', 'bold-page-builder' ) 		=> 'p'
				) ),
				array( 'param_name' => 'text', 'type' => 'textarea', 'heading' => esc_html__( 'Text', 'bold-page-builder' ), 'placeholder' => esc_html__( 'Add Service text', 'bold-page-builder' ) ),
				array( 'param_name' => 'align', 'type' => 'dropdown', 'heading' => esc_html__( 'Icon position', 'bold-page-builder' ), 'responsive_override' => true,
					'value' => array(
						esc_html__( 'Inherit', 'bold-page-builder' ) => 'inherit',
						esc_html__( 'Left', 'bold-page-builder' ) 	=> 'left',
						esc_html__( 'Center', 'bold-page-builder' ) 	=> 'center',
						esc_html__( 'Right', 'bold-page-builder' ) 	=> 'right'
					)
				),
				array( 'param_name' => 'url', 'type' => 'link', 'heading' => esc_html__( 'URL', 'bold-page-builder' ), 'group' => esc_html__( 'URL', 'bold-page-builder' ), 'description' => esc_html__( 'Enter full or local URL (e.g. https://www.bold-themes.com or /pages/about-us) or post slug (e.g. about-us) or search for existing content.', 'bold-page-builder' ) ),
				array( 'param_name' => 'target', 'type' => 'dropdown', 'heading' => esc_html__( 'Target', 'bold-page-builder' ), 'group' => esc_html__( 'URL', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Self (open in same tab)', 'bold-page-builder' ) 		=> '_self',
						esc_html__( 'Blank (open in new tab)', 'bold-page-builder' ) 		=> '_blank',
						esc_html__( 'Lightbox (open in new layer)', 'bold-page-builder' ) 	=> '_lightbox',
					)
				),
				array( 'param_name' => 'size', 'type' => 'dropdown', 'heading' => esc_html__( 'Icon size', 'bold-page-builder' ), 'responsive_override' => true, 'preview' => true, 'group' => esc_html__( 'Design', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Extra small', 'bold-page-builder' ) => 'xsmall',
						esc_html__( 'Small', 'bold-page-builder' ) 		=> 'small',
						esc_html__( 'Normal', 'bold-page-builder' ) 		=> 'normal',
						esc_html__( 'Large', 'bold-page-builder' ) 		=> 'large',
						esc_html__( 'Extra large', 'bold-page-builder' ) => 'xlarge'
					)
				),
				array( 'param_name' => 'style', 'type' => 'dropdown', 'heading' => esc_html__( 'Icon style', 'bold-page-builder' ), 'preview' => true, 'group' => esc_html__( 'Design', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Outline', 'bold-page-builder' ) 		=> 'outline',
						esc_html__( 'Filled', 'bold-page-builder' ) 			=> 'filled',
						esc_html__( 'Borderless', 'bold-page-builder' ) 		=> 'borderless'
					)
				),
				array( 'param_name' => 'shape', 'type' => 'dropdown', 'heading' => esc_html__( 'Icon shape', 'bold-page-builder' ), 'group' => esc_html__( 'Design', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Circle', 'bold-page-builder' ) 			=> 'circle',
						esc_html__( 'Square', 'bold-page-builder' ) 			=> 'square',
						esc_html__( 'Rounded Square', 'bold-page-builder' ) 	=> 'round'
					)
				),
				array( 'param_name' => 'color_scheme', 'type' => 'dropdown', 'heading' => esc_html__( 'Color scheme', 'bold-page-builder' ), 'description' => esc_html__( 'Define color schemes in Bold Builder settings or define accent and alternate colors in theme customizer (if avaliable)', 'bold-page-builder' ), 'value' => $color_scheme_arr, 'preview' => true, 'group' => esc_html__( 'Design', 'bold-page-builder' ) ),
			)
		) );
	}
}