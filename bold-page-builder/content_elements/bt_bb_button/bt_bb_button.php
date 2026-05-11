<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class bt_bb_button extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'text'						=> '',
			'icon'						=> '',
			'icon_position'				=> '',
			'url'						=> '',
			'target'					=> '',
			'color_scheme'  			=> '',
			'font'          			=> '',
			'font_subset'   			=> '',
			'font_load_extension'   	=> '',
			'font_weight'   			=> '',
			'text_transform'   			=> '',
			'style'						=> '',
			'size'						=> '',
			'width'						=> '',
			'shape'						=> '',
			'align'						=> 'inherit'
		) ), $atts, $this->shortcode ) );
		
		$class = array( $this->shortcode );
		$data_override_class = array();
		
		$font = sanitize_text_field( $font );

		if ( $font != '' && $font != 'inherit' ) {
			require_once( dirname(__FILE__) . '/../../content_elements_misc/misc.php' );
			bt_bb_enqueue_google_font( $font, $font_subset, $font_load_extension );
		}		
		
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
		
		if ( $icon_position != '' ) {
			$class[] = $this->prefix . 'icon_position' . '_' . $icon_position;
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

		$this->responsive_data_override_class(
			$class, $data_override_class,
			array(
				'prefix' => $this->prefix,
				'param' => 'width',
				'value' => $width
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
		
		if ( $font_weight != '' ) {
			$class[] = $this->prefix . 'font_weight' . '_' . $font_weight;
		}
		
		if ( $text_transform != '' ) {
			$class[] = $this->prefix . 'text_transform' . '_' . $text_transform;
		}
		
		$this->responsive_data_override_class(
			$class, $data_override_class,
			array(
				'prefix' => $this->prefix,
				'param' => 'align',
				'value' => $align
			)
		);

		if ( $target == '' ) {
			$target = '_self';
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
		
		$output = $this->get_html( $icon, $text, $font, $url, $target );
		
		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . ' data-bt-override-class="' . htmlspecialchars( json_encode( $data_override_class, JSON_FORCE_OBJECT ), ENT_QUOTES, 'UTF-8' ) . '">' . $output . '</div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );

		return $output;

	}
	
	function get_html( $icon, $text, $font, $url, $target ) {
		
		require_once( dirname(__FILE__) . '/../../content_elements_misc/misc.php' );

		if ( $url == '' ) {
			$url = '#';
		}
		
		$font_attr = '';

		if ( $font != '' && $font != 'inherit' ) {
			$font_attr = ' style="' . esc_attr( 'font-family:\'' . urldecode( $font ) . '\'' ) . '"';
		}
		
		$text_output = '';

		if ( $text != '' ) {
			$text_output = '<span class="bt_bb_button_text" ' . $font_attr . '>' . wp_kses_post( $text ) . '</span>';
		}

		$link = bt_bb_get_url( $url );

		// IMPORTANT: esc_attr (not esc_url) is intentional here.
		// Authors are allowed to put javascript: / mailto: / tel: / non-standard schemes
		// in the button URL. esc_url would strip them. Do not "fix" this to esc_url —
		// it has been flagged by automated security audits before; it is by design.
		$output = '<a href="' . esc_attr( $link ) . '" target="' . esc_attr( $target ) . '" class="' . esc_attr( $this->prefix ) . 'link" title="' . esc_attr( $text ) . '">';
			if ( $icon == '' || $icon == 'no_icon' ) {
				$output .= $text_output;
			} else {
				$output .= $text_output . bt_bb_icon::get_html( $icon, '', '', '' );
			}
		$output .= '</a>';
		
		return $output;
	}

	function map_shortcode() {

		require_once( dirname(__FILE__) . '/../../content_elements_misc/misc.php' );
		$color_scheme_arr = bt_bb_get_color_scheme_param_array();
		
		require( dirname(__FILE__) . '/../../content_elements_misc/fonts1.php' );

		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Button', 'bold-page-builder' ), 'description' => esc_html__( 'Button with custom link', 'bold-page-builder' ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'text', 'type' => 'textfield', 'heading' => esc_html__( 'Text', 'bold-page-builder' ), 'placeholder' => esc_html__( 'Add Button Text', 'bold-page-builder' ), 'preview' => true ),
				array( 'param_name' => 'icon', 'type' => 'iconpicker', 'heading' => esc_html__( 'Icon', 'bold-page-builder' ), 'preview' => true ),
				array( 'param_name' => 'icon_position', 'type' => 'dropdown', 'heading' => esc_html__( 'Icon position', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Left', 'bold-page-builder' ) 	=> 'left',
						esc_html__( 'Right', 'bold-page-builder' ) 	=> 'right'
					)
				),
				array( 'param_name' => 'align', 'type' => 'dropdown', 'heading' => esc_html__( 'Alignment', 'bold-page-builder' ), 'description' => esc_html__( 'Please note that it is not possible to show multiple buttons inline if any of them are using Center option.', 'bold-page-builder' ), 'responsive_override' => true,
					'value' => array(
						esc_html__( 'Inherit', 'bold-page-builder' ) => 'inherit',
						esc_html__( 'Left', 'bold-page-builder' ) 	=> 'left',
						esc_html__( 'Center', 'bold-page-builder' ) 	=> 'center',
						esc_html__( 'Right', 'bold-page-builder' ) 	=> 'right'
					)
				),
				array( 'param_name' => 'url', 'type' => 'link', 'heading' => esc_html__( 'URL', 'bold-page-builder' ), 'description' => esc_html__( 'Enter full or local URL (e.g. https://www.bold-themes.com or /pages/about-us) or post slug (e.g. about-us) or search for existing content.', 'bold-page-builder' ), 'group' => esc_html__( 'URL', 'bold-page-builder' ), 'preview' => true ),
				array( 'param_name' => 'target', 'type' => 'dropdown', 'heading' => esc_html__( 'Target', 'bold-page-builder' ), 'group' => esc_html__( 'URL', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Self (open in same tab)', 'bold-page-builder' ) 		=> '_self',
						esc_html__( 'Blank (open in new tab)', 'bold-page-builder' ) 		=> '_blank',
						esc_html__( 'Lightbox (open in new layer)', 'bold-page-builder' ) 	=> '_lightbox',
					)
				),
				array( 'param_name' => 'size', 'type' => 'dropdown', 'heading' => esc_html__( 'Size', 'bold-page-builder' ), 'preview' => true, 'group' => esc_html__( 'Design', 'bold-page-builder' ), 'responsive_override' => true,
					'value' => array(
						esc_html__( 'Small', 'bold-page-builder' ) 		=> 'small',
						esc_html__( 'Medium', 'bold-page-builder' ) 		=> 'medium',
						esc_html__( 'Normal', 'bold-page-builder' ) 		=> 'normal',
						esc_html__( 'Large', 'bold-page-builder' ) 		=> 'large'
					)
				),
				array( 'param_name' => 'style', 'type' => 'dropdown', 'heading' => esc_html__( 'Style', 'bold-page-builder' ), 'preview' => true, 'group' => esc_html__( 'Design', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Outline', 'bold-page-builder' ) 	=> 'outline',
						esc_html__( 'Filled', 'bold-page-builder' ) 		=> 'filled',
						esc_html__( 'Clean', 'bold-page-builder' ) 		=> 'clean'
					)
				),
				array( 'param_name' => 'shape', 'type' => 'dropdown', 'heading' => esc_html__( 'Shape', 'bold-page-builder' ), 'group' => esc_html__( 'Design', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Inherit', 'bold-page-builder' ) 		=> 'inherit',
						esc_html__( 'Square', 'bold-page-builder' ) 			=> 'square',
						esc_html__( 'Soft Rounded', 'bold-page-builder' ) 	=> 'rounded',
						esc_html__( 'Hard Rounded', 'bold-page-builder' ) 	=> 'round'
					)
				),
				array( 'param_name' => 'width', 'type' => 'dropdown', 'heading' => esc_html__( 'Width', 'bold-page-builder' ), 'group' => esc_html__( 'Design', 'bold-page-builder' ), 'responsive_override' => true,
					'value' => array(
						esc_html__( 'Inline', 'bold-page-builder' ) 			=> 'inline',
						esc_html__( 'Full', 'bold-page-builder' ) 			=> 'full'
					)
				),
				array( 'param_name' => 'color_scheme', 'type' => 'dropdown', 'heading' => esc_html__( 'Color scheme', 'bold-page-builder' ), 'description' => esc_html__( 'Define color schemes in Bold Builder settings or define accent and alternate colors in theme customizer (if avaliable)', 'bold-page-builder' ), 'value' => $color_scheme_arr, 'preview' => true, 'group' => esc_html__( 'Design', 'bold-page-builder' ) ),
				
				array( 'param_name' => 'font', 'type' => 'dropdown', 'heading' => esc_html__( 'Font', 'bold-page-builder' ), 'group' => esc_html__( 'Font', 'bold-page-builder' ), 'preview' => true,
					'value' => array( esc_html__( 'Inherit', 'bold-page-builder' ) => 'inherit' ) + BT_BB_Root::$font_arr
				),
				array( 'param_name' => 'font_subset', 'type' => 'textfield', 'heading' => esc_html__( 'Google font subset', 'bold-page-builder' ), 'group' => esc_html__( 'Font', 'bold-page-builder' ), 'value' => 'latin,latin-ext', 'description' => esc_html__( 'E.g. latin,latin-ext,cyrillic,cyrillic-ext', 'bold-page-builder' ) ),
				array( 'param_name' => 'font_load_extension', 'type' => 'textfield', 'heading' => esc_html__( 'Google variable font style specification', 'bold-page-builder' ), 'group' => esc_html__( 'Font', 'bold-page-builder' ), 'value' => '', 'description' => __( 'Define Google variable font style specification. For more details, <a href="https://fonts.google.com/knowledge/glossary/variable_fonts" target="_blank">read here</a>. Leave empty to load default font settings.', 'bold-page-builder' ), 'placeholder' => esc_html__( 'E.g. :ital,wght@0,200;1,700', 'bold-page-builder' ) ),
				array( 'param_name' => 'font_weight', 'type' => 'dropdown', 'heading' => esc_html__( 'Font weight', 'bold-page-builder' ), 'group' => esc_html__( 'Font', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Default', 'bold-page-builder' ) 	=> '',
						esc_html__( 'Normal', 'bold-page-builder' ) 		=> 'normal',
						esc_html__( 'Bold', 'bold-page-builder' ) 		=> 'bold',
						esc_html__( 'Bolder', 'bold-page-builder' ) 		=> 'bolder',
						esc_html__( 'Lighter', 'bold-page-builder' ) 	=> 'lighter',
						esc_html__( 'Light', 'bold-page-builder' ) 		=> 'light',
						esc_html__( '100', 'bold-page-builder' ) 		=> '100',
						esc_html__( '200', 'bold-page-builder' ) 		=> '200',
						esc_html__( '300', 'bold-page-builder' ) 		=> '300',
						esc_html__( '400', 'bold-page-builder' ) 		=> '400',
						esc_html__( '500', 'bold-page-builder' ) 		=> '500',
						esc_html__( '600', 'bold-page-builder' ) 		=> '600',
						esc_html__( '700', 'bold-page-builder' ) 		=> '700',
						esc_html__( '800', 'bold-page-builder' ) 		=> '800',
						esc_html__( '900', 'bold-page-builder' ) 		=> '900'
					)
				),
				array( 'param_name' => 'text_transform', 'type' => 'dropdown', 'heading' => esc_html__( 'Text transform', 'bold-page-builder' ), 'group' => esc_html__( 'Font', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Inherit', 'bold-page-builder' ) 	=> 'inherit',
						esc_html__( 'None', 'bold-page-builder' ) 		=> 'none',
						esc_html__( 'UPPERCASE', 'bold-page-builder' ) 	=> 'uppercase',
						esc_html__( 'Lowercase', 'bold-page-builder' ) 	=> 'lowercase',
						esc_html__( 'Capitalize', 'bold-page-builder' ) 	=> 'capitalize'
					)
				),
				
				
				
			)
		) );
	} 
}