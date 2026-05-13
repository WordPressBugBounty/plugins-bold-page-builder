<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class bt_bb_image extends BT_BB_Element {

	function handle_shortcode( $atts, $content ) {
		extract( shortcode_atts( apply_filters( 'bt_bb_extract_atts_' . $this->shortcode, array(
			'image'  							=> '',
			'size'   							=> '',
			'shape'  							=> '',
			'lazy_load'  						=> 'no',
			'image_height'  					=> '',
			'align'  							=> '',
			'show_caption'    					=> '',
			'caption'    						=> '',
			'url'    							=> '',
			'target' 							=> '',
			'hover_style'  						=> '',
			'content_display'  					=> '',
			'content_background_color' 			=> '',
			'content_background_opacity'	    => '',
			'content_align'						=> '',
			'ignore_fe_editor'                  => '',
		) ), $atts, $this->shortcode ) );
		
		require_once( dirname(__FILE__) . '/../../content_elements_misc/misc.php' );
		
		$class = array( $this->shortcode );
		$data_override_class = array();
		
		$url = trim( $url );
		
		if ( $el_class != '' ) {
			$class[] = $el_class;
		}
		
		if ( $hover_style == 'scroll' ) {
			$el_id = 'bt_bb_random_id_' . wp_rand();
		}

		if ( $image_height != '' ) {
			$el_style .= 'height:' . $image_height . '; overflow: hidden;';
		}

		$style_attr = '';		
		$el_style = apply_filters( $this->shortcode . '_style', $el_style, $atts );
		if ( $el_style != '' ) {
			$style_attr = ' ' . 'style="' . esc_attr( $el_style ) . '"';
		}	
		
		$id_attr = '';
		if ( $el_id != '' ) {
			$id_attr = ' ' . 'id="' . esc_attr( $el_id ) . '"';
		}
		
		if ( $shape != '' ) {
			$class[] = $this->prefix . 'shape' . '_' . $shape;
		}
		
		if ( $url != '#lightbox' ) {
			$link = bt_bb_get_url( $url );	
		} else {
			$link = $url;
			$target = '_lightbox';
		}
		
		if ( $target != '' ) {
			$class[] = $this->prefix . 'target' . $target;
		}
		
		if ( $target == '_lightbox' ) {
			$class[] = 'bt_bb_use_lightbox';
			$target = '_blank';
			if ( $url == '' ) {
				$link = '#lightbox';
			}
		}
		
		$this->responsive_data_override_class(
			$class, $data_override_class,
			array(
				'prefix' => $this->prefix,
				'param' => 'align',
				'value' => $align
			)
		);
		
		if ( $hover_style != '' ) {
			$class[] = $this->prefix . 'hover_style' . '_' . $hover_style;
		}
		
		/*if ( $content_display != '' ) {
			$class[] = $this->prefix . 'content_display' . '_' . $content_display;
		}*/
		$this->responsive_data_override_class(
			$class, $data_override_class,
			array(
				'prefix' => $this->prefix,
				'param' => 'content_display',
				'value' => $content_display
			)
		);

		if ( $content_align != '' ) {
			$class[] = $this->prefix . 'content_align' . '_' . $content_align;
		}

		$alt = $caption;
		$title = $caption;
		$full_image = $image;
		$image_ = $image;
			
		if ( $image != '' && is_numeric( $image ) ) {
			$post_image = get_post( $image );
			if ( $post_image == '' ) return;
			
			if ( $alt == '' ) {
				$alt = get_post_meta( $post_image->ID, '_wp_attachment_image_alt', true );
			}
			if ( $alt == '' ) {
				$alt = $post_image->post_excerpt;
			}
			if ( $title == '' ) {
				$title = $post_image->post_title;
			}
			
			$image_ = wp_get_attachment_image_src( $image, $size );
			if ( $image_ ) {
				$image_ = $image_[0];
			}
			if ( $alt == '' ) {
				$alt = $image_;
			}
			
			if ( $size == 'full' ) {
				$full_image = $image_;
			} else {
				$full_image = wp_get_attachment_image_src( $image, 'full' );
				if ( $full_image ) {
					$full_image = $full_image[0];
				} else {
					$full_image = '';
				}				
			}
		}
		
		$content = do_shortcode( $content );
		
		if ( $content != '' || BT_BB_FE::$editor_active ) {
			$class[] = $this->prefix . 'content_exists';
		}
		
		$output = '';

		if ( $image != '' ) {
			if ( is_numeric( $image ) ) {
				$attr_arr = array();
				$attr_arr['data-full_image_src'] = esc_url_raw( $full_image );
				$attr_arr['alt'] = esc_attr( $alt );
				if ( $title != '' ) {
					$attr_arr['title'] = esc_attr( $title );
				}
				if ( $lazy_load != 'yes' ) {
					$attr_arr['loading'] = 'eager';
				}
				$output .= wp_get_attachment_image( $image, $size, false, $attr_arr );
			} else {
				$title_attr = '';
				$alt_attr = '';
				if ( $title != '' ) {
					$title_attr = ' ' . 'title="' . esc_attr( $title ) . '"';
				}
				if ( $alt != '' ) {
					$alt_attr = ' ' . 'alt="' . esc_attr( $alt ) . '"';
				}
				if ( $lazy_load == 'yes' ) {
					$output .= '<img src="' . esc_url_raw( $image ) . '"' . $title_attr . $alt_attr . ' loading="lazy">';
				} else {
					$output .= '<img src="' . esc_url_raw( $image ) . '"' . $title_attr . $alt_attr . '>';
				}
			}
		} else if ( ! $ignore_fe_editor ) {
			$output .= '<img src="' . BT_BB_Root::$path . 'img/placeholder.png" alt="' . esc_html__( 'Placeholder image', 'bold-page-builder' ) . '" title="' . esc_html__( 'Placeholder image', 'bold-page-builder' ) . '">';
		}
		
		if ( ! empty( $link ) ) {
			if ( $title != '' ) {
				$output = '<a href="' . esc_url( $link ) . '"  target="' . esc_attr( $target ) . '" title="' . esc_attr( $title ) . '">' . $output . '</a>';
			} else {
				$output = '<a href="' . esc_url( $link ) . '"  target="' . esc_attr( $target ) . '">' . $output . '</a>';
			}
		} else {
			$output = '<span>' . $output . '</span>';
		}
		
		if ( $show_caption == 'yes' ) { 
			$output = '<figure>' . $output . '<figcaption>' . esc_html( $caption ) . '</figcaption></figure>';
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
		
		$output = '<div' . $id_attr . ' class="' . esc_attr( implode( ' ', $class ) ) . '"' . $style_attr . ' data-bt-override-class="' . htmlspecialchars( json_encode( $data_override_class, JSON_FORCE_OBJECT ), ENT_QUOTES, 'UTF-8' ) . '">' . $output ;
		if ( $content != '' || BT_BB_FE::$editor_active ) {
			$content_background_style = '';
			if ( $content_background_color != '' ) {
				if ( strpos( $content_background_color, '#' ) !== false ) {
					$content_background_color = bt_bb_image::hex2rgb( $content_background_color );
					if ( $content_background_opacity == '' ) {
						$content_background_opacity = 1;
					}
					$content_background_style .= ' style="background-color: rgba(' . $content_background_color[0] . ', ' . $content_background_color[1] . ', ' . $content_background_color[2] . ', ' . $content_background_opacity . ');"';
				} else {
					$content_background_style .= 'style="background-color:' . $content_background_color . ';"';
				}
			}
			$output .= '<div class="bt_bb_image_content" ' . $content_background_style . '><div class="bt_bb_image_content_flex"><div class="bt_bb_image_content_inner">' . $content . '</div></div></div>';
		}
		$output .= '</div>';
		
		$output = apply_filters( 'bt_bb_general_output', $output, $atts );
		$output = apply_filters( $this->shortcode . '_output', $output, $atts );
		
		return $output;

	}

	function map_shortcode() {
		bt_bb_map( $this->shortcode, array( 'name' => esc_html__( 'Image', 'bold-page-builder' ), 'description' => esc_html__( 'Single image', 'bold-page-builder' ), 'container' => 'vertical', 'accept' => array( 'bt_bb_button' => true, 'bt_bb_icon' => true, 'bt_bb_text' => true, 'bt_bb_headline' => true, 'bt_bb_separator' => true ), 'icon' => $this->prefix_backend . 'icon' . '_' . $this->shortcode,
			'params' => array(
				array( 'param_name' => 'image', 'type' => 'attach_image', 'heading' => esc_html__( 'Image', 'bold-page-builder' ), 'preview' => true ),
				array( 'param_name' => 'size', 'type' => 'dropdown', 'heading' => esc_html__( 'Size', 'bold-page-builder' ), 'preview' => true,
					'value' => bt_bb_get_image_sizes()
				),
				array( 'param_name' => 'image_height', 'type' => 'textfield', 'heading' => esc_html__( 'Image height', 'bold-page-builder' ), 'placeholder' => esc_html__( 'E.g. 90px', 'bold-page-builder' ) ), 
				array( 'param_name' => 'shape', 'type' => 'dropdown', 'heading' => esc_html__( 'Shape', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Square', 'bold-page-builder' ) 			=> 'square',
						esc_html__( 'Soft Rounded', 'bold-page-builder' ) 	=> 'soft-rounded',
						esc_html__( 'Hard Rounded', 'bold-page-builder' ) 	=> 'hard-rounded'
					)
				),
				array( 'param_name' => 'lazy_load', 'type' => 'dropdown', 'default' => 'yes', 'heading' => esc_html__( 'Lazy load image', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'No', 'bold-page-builder' ) 		=> 'no',
						esc_html__( 'Yes', 'bold-page-builder' ) 	=> 'yes'
					)
				),
				array( 
					'param_name' => 'align', 'type' => 'dropdown', 'heading' => esc_html__( 'Alignment', 'bold-page-builder' ), 'responsive_override' => true,
					'value' => array(
						esc_html__( 'Inherit', 'bold-page-builder' ) 		=> 'inherit',
						esc_html__( 'Left', 'bold-page-builder' ) 			=> 'left',
						esc_html__( 'Center', 'bold-page-builder' ) 			=> 'center',
						esc_html__( 'Right', 'bold-page-builder' ) 			=> 'right'
					)
				),
				array( 
					'param_name' => 'caption', 'type' => 'textfield', 'heading' => esc_html__( 'Caption/Alt/Title override', 'bold-page-builder' ), 'description' => esc_html__( 'Use this field to override the alt, title, and caption values from the media library.', 'bold-page-builder' ) 
				),
				array( 
					'param_name' => 'show_caption', 'type' => 'checkbox', 'value' => array( esc_html__( 'Yes', 'bold-page-builder' ) => 'yes' ), 'default' => '', 'heading' => esc_html__( 'Show HTML caption tag', 'bold-page-builder' ) 
				),
				array( 
					'param_name' => 'url', 'type' => 'link', 'heading' => esc_html__( 'URL', 'bold-page-builder' ), 'description' => esc_html__( 'Enter full or local URL (e.g. https://www.bold-themes.com or /pages/about-us), post slug (e.g. about-us).', 'bold-page-builder' ), 
					'group' => esc_html__( 'URL', 'bold-page-builder' ) 
				),
				array( 'param_name' => 'target', 'type' => 'dropdown', 'heading' => esc_html__( 'Target', 'bold-page-builder' ), 'description' => esc_html__( 'To open current image in full size select Lightbox and leave URL field empty.', 'bold-page-builder' ), 'group' => esc_html__( 'URL', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Self (open in same tab)', 'bold-page-builder' ) 		=> '_self',
						esc_html__( 'Blank (open in new tab)', 'bold-page-builder' ) 		=> '_blank',
						esc_html__( 'Lightbox (open in new layer)', 'bold-page-builder' ) 	=> '_lightbox'
					)
				),
				array( 'param_name' => 'hover_style', 'type' => 'dropdown', 'heading' => esc_html__( 'Hover style', 'bold-page-builder' ), 'group' => esc_html__( 'URL', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Simple', 'bold-page-builder' ) 					=> 'simple',
						esc_html__( 'Flip', 'bold-page-builder' ) 					=> 'flip',
						esc_html__( 'Zoom in', 'bold-page-builder' ) 				=> 'zoom-in',
						esc_html__( 'To grayscale', 'bold-page-builder' ) 			=> 'to-grayscale',
						esc_html__( 'From grayscale', 'bold-page-builder' ) 			=> 'from-grayscale',
						esc_html__( 'Zoom in to grayscale', 'bold-page-builder' ) 	=> 'zoom-in-to-grayscale',
						esc_html__( 'Zoom in from grayscale', 'bold-page-builder' ) 	=> 'zoom-in-from-grayscale',
						esc_html__( 'Scroll', 'bold-page-builder' ) 					=> 'scroll'
					)
				),
				array( 'param_name' => 'content_display', 'type' => 'dropdown', 'heading' => esc_html__( 'Show content', 'bold-page-builder' ), 'description' => esc_html__( 'Add selected elements and show them over the image', 'bold-page-builder' ), 'group' => esc_html__( 'Content', 'bold-page-builder' ), 'responsive_override' => true,
					'value' => array(
						esc_html__( 'Always', 'bold-page-builder' ) 			=> 'always',
						esc_html__( 'Show on hover', 'bold-page-builder' ) 	=> 'show-on-hover',
						esc_html__( 'Hide on hover', 'bold-page-builder' ) 	=> 'hide-on-hover'
					)
				),
				array( 'param_name' => 'content_background_color', 'type' => 'colorpicker', 'heading' => esc_html__( 'Content background color', 'bold-page-builder' ), 'group' => esc_html__( 'Content', 'bold-page-builder' ) ),
				array( 'param_name' => 'content_background_opacity', 'type' => 'textfield', 'heading' => esc_html__( 'Content background opacity (deprecated)', 'bold-page-builder' ), 'group' => esc_html__( 'Content', 'bold-page-builder' ) ),
				array( 'param_name' => 'content_align', 'type' => 'dropdown', 'heading' => esc_html__( 'Content alignment', 'bold-page-builder' ), 'group' => esc_html__( 'Content', 'bold-page-builder' ),
					'value' => array(
						esc_html__( 'Middle', 'bold-page-builder' ) 		=> 'middle',
						esc_html__( 'Top', 'bold-page-builder' ) 		=> 'top',						
						esc_html__( 'Bottom', 'bold-page-builder' ) 		=> 'bottom'
					)
				)
			)
		) );
	}
	
	static function hex2rgb( $hex ) {
		$hex = str_replace( '#', '', $hex );
		if ( strlen( $hex ) == 3 ) {
			$r = hexdec( substr( $hex, 0, 1 ) . substr( $hex, 0, 1 ) );
			$g = hexdec( substr( $hex, 1, 1 ) . substr( $hex, 1, 1 ) );
			$b = hexdec( substr( $hex, 2, 1 ) . substr( $hex, 2, 1 ) );
		} else {
			$r = hexdec( substr( $hex, 0, 2 ) );
			$g = hexdec( substr( $hex, 2, 2 ) );
			$b = hexdec( substr( $hex, 4, 2 ) );
		}
		$rgb = array( $r, $g, $b );
		return $rgb;
	}
}