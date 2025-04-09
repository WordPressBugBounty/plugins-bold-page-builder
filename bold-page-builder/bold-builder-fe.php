<?php

class BT_BB_FE {
	public static $elements = array();
	public static $templates = array();
	public static $fe_id = -1;
	public static $content;
	public static $sections_arr_search = array();
	public static $editor_active = false;
}

add_action( 'admin_bar_init', 'bt_bb_fe_init', 9 );

function bt_bb_fe_init() {
	if ( ! bt_bb_active_for_post_type_fe() || ( isset( $_GET['preview'] ) && ! isset( $_GET['bt_bb_fe_preview'] ) ) ) {
		return;
	}
	if ( current_user_can( 'edit_pages' ) ) {
		
		BT_BB_FE::$editor_active = true;
		
		BT_BB_Root::$elements = apply_filters( 'bt_bb_elements', BT_BB_Root::$elements );
		
		BT_BB_FE::$elements = array(
		
			'bt_bb_accordion' => array(
				'edit_box_selector' => '',
				'params' => array(
					'style'        => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'shape'        => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'color_scheme' => array( 'ajax_filter' => array( 'class', 'style' ) ),
				),
			),
			'bt_bb_accordion_item' => array(
				'edit_box_selector' => '',
				'params' => array(
					'title' => array( 'js_handler' => array( 'target_selector' => '.bt_bb_accordion_item_title', 'type' => 'inner_html' ) ),
				),
				'drag_and_drop' => array(
					'target_selector' => '.bt_bb_accordion_item_content'
				)
			),
			'bt_bb_button' => array(
				'edit_box_selector' => '',
				'params' => array(
					'text' 				=> array( 'js_handler'	=> array( 'target_selector' => '.bt_bb_button_text', 'type' => 'inner_html' ) ),
					'icon' 				=> array(),
					'icon_position' 	=> array( 'js_handler'	=> array( 'target_selector' => '', 'type' => 'class' ) ),
					'align' 			=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'url' 				=> array( 'js_handler'	=> array( 'target_selector' => ' > a', 'type' => 'attr', 'attr' => 'href' ) ),
					'target' 			=> array( 'js_handler'	=> array( 'target_selector' => ' > a', 'type' => 'attr', 'attr' => 'target' ) ),
					'size' 				=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'color_scheme' 		=> array( 'ajax_filter' => array( 'class', 'style' ) ),
					'font'              => array( 'js_callback' => array( 'bt_bb_js_google_font' ) ),
					'font_weight' 		=> array( 'js_handler'	=> array( 'target_selector' => '', 'type' => 'class' ) ),
					'text_transform' 	=> array( 'js_handler'	=> array( 'target_selector' => '', 'type' => 'class' ) ),
					'style' 			=> array( 'js_handler'	=> array( 'target_selector' => '', 'type' => 'class' ) ),
					'shape' 			=> array( 'js_handler'	=> array( 'target_selector' => '', 'type' => 'class' ) ),
					'width' 			=> array( 'js_handler'	=> array( 'target_selector' => '', 'type' => 'class' ) ),
				),
			),
			'bt_bb_contact_form_7' => array(
				'edit_box_selector' => '',
				'params' => array(
					'contact_form_id' => array(),
				),
			),
			'bt_bb_column' => array(
				'edit_box_selector' => '',
				'params' => array(
					'align' 					=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'vertical_align' 			=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
					'padding' 					=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'background_image'			=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'background_image' ) ),
					'inner_background_image'	=> array( 'js_handler'  => array( 'target_selector' =>  '.bt_bb_column_content', 'type' => 'background_image' ) ),
					'color_scheme'				=> array( 'ajax_filter' => array( 'class', 'style' ) ),
					'inner_color_scheme'		=> array( 'ajax_filter' => array( 'class', 'style' ) ),
					'background_color'			=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'background_color' ) ),
				),
				'disable_clone' => true,
				'disable_delete' => true,
				'drag_and_drop' => array(
					'disable_as_source' => true,
					'disable_as_target' => false,
					'target_selector' => '.bt_bb_column_content_inner'
				)
			),
			'bt_bb_column_inner' => array(
				'edit_box_selector' => '',
				'params' => array(
					'align' 					=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'vertical_align' 			=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
					'padding' 					=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'background_image'			=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'background_image' ) ),
					'inner_background_image'	=> array( 'js_handler'  => array( 'target_selector' =>  '.bt_bb_column_inner_content', 'type' => 'background_image' ) ),
					'color_scheme'				=> array( 'ajax_filter' => array( 'class', 'style' ) ),
					'inner_color_scheme'		=> array( 'ajax_filter' => array( 'class', 'style' ) ),
					'background_color'			=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'background_color' ) ),
					'inner_background_color'	=> array( 'js_handler'  => array( 'target_selector' => '.bt_bb_column_inner_content', 'type' => 'background_color' ) ),
				),
				'disable_clone' => true,
				'disable_delete' => true,
				'drag_and_drop' => array(
					'disable_as_source' => true,
					'disable_as_target' => false,
					'target_selector' => '.bt_bb_column_inner_content'
				)
			),
			'bt_bb_content_slider' => array(
				'edit_box_selector' => '',
				'ajax_slick' => true,
				'params' => array(
					'height'              => array(),
					'animation'           => array(),
					'direction'           => array(),
					'arrows_size'         => array(),
					'show_dots'           => array(),
					'pause_on_hover'      => array(),
					'slides_to_show'      => array(),
					'additional_settings' => array(),
					'gap'                 => array(),
					'auto_play'           => array(),
				),
			),
			'bt_bb_content_slider_item' => array(
				'edit_box_selector' => '',
				'params' => array(
					'image'              => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'background_image' ) ),
					'background_overlay' => array( 'js_handler'	=> array( 'target_selector' => '', 'type' => 'class' ) ),
				),
				'condition_params' => true,
			),
			'bt_bb_countdown' => array(
				'edit_box_selector' => '',
				'use_ajax_placeholder' => true,
				'ajax_animate_elements' => true,
				'params' => array(
					'datetime' => array( 'js_handler'  => array( 'target_selector' => '.btCountdownHolder', 'type' => 'countdown' ) ),
					'size'     => array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
				),
			),
			'bt_bb_counter' => array(
				'edit_box_selector' => '',
				'ajax_animate_elements' => true,
				'params' => array(
					'number' => array(),
					'size'   => array(),
				),
			),
			'bt_bb_custom_menu' => array( 
				'edit_box_selector' => '',
				'params' => array(
					'menu' => array(),
					'font_weight' => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'direction'   => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
				),
			),
			'bt_bb_google_maps' => array( 
				'edit_box_selector' => '',
				'ajax_callback' => 'bt_bb_init_all_maps',
				'params' => array(
					'api_key'      => array(),
					'zoom'         => array(),
					'height'       => array(),
					'map_id'       => array(),
					'custom_style' => array(),
					'map_type'     => array(),
					'center_map'   => array(),
				),
			),
			'bt_bb_headline' => array(
				'edit_box_selector' => '',
				'ajax_animate_elements' => true,
				'params' => array(
					'ai_prompt'				    	=> array(),
					'superheadline'					=> array( 'js_handler'  => array( 'target_selector' => '.bt_bb_headline_superheadline', 'type' => 'inner_html' ) ),
					'headline'						=> array( 'js_handler'  => array( 'target_selector' => '.bt_bb_headline_content > span', 'type' => 'inner_html_nl2br' ) ),
					'subheadline'					=> array( 'js_handler'  => array( 'target_selector' => '.bt_bb_headline_subheadline', 'type' => 'inner_html_nl2br' ) ),
					'html_tag'						=> array(),
					'size'							=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'align'							=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'dash'							=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
					'color_scheme'					=> array( 'ajax_filter' => array( 'class', 'style' ) ),
					'font'                          => array( 'js_callback' => array( 'bt_bb_js_google_font' ) ),
					'font_weight'					=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
					'text_transform'				=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
					'superheadline_font_weight'		=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
					'superheadline_text_transform'	=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
					'subheadline_font_weight'		=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
					'subheadline_text_transform'	=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
					'url'							=> array( 'js_handler'  => array( 'target_selector' => '.bt_bb_headline_content > span > a', 'type' => 'attr', 'attr' => 'href' ) ),
					'target'						=> array( 'js_handler'  => array( 'target_selector' => '.bt_bb_headline_content > span > a', 'type' => 'attr', 'attr' => 'target' ) ),
				),
			),
			'bt_bb_icon' => array(
				'edit_box_selector' => '',
				'params' => array(
					'icon'			=> array(),
					'colored_icon'	=> array(),
					'text'			=> array( 'js_handler'  => array( 'target_selector' => '.bt_bb_icon_holder > span', 'type' => 'inner_html' ) ),
					'url'			=> array( 'js_handler'  => array( 'target_selector' => 'a.bt_bb_icon_holder', 'type' => 'attr', 'attr' => 'href' ) ),
					'url_title'		=> array( 'js_handler'  => array( 'target_selector' => 'a.bt_bb_icon_holder', 'type' => 'attr', 'attr' => 'title' ) ),
					'target'		=> array( 'js_handler'  => array( 'target_selector' => 'a.bt_bb_icon_holder', 'type' => 'attr', 'attr' => 'target' ) ),
					'align'			=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'size'			=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'color_scheme'	=> array( 'ajax_filter' => array( 'class', 'style' ) ), 
					'style'			=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
					'shape'			=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
				),
			),
			'bt_bb_image' => array(
				'edit_box_selector' => '',
				'use_ajax_placeholder' => true,
				'params' => array(
					'image'				=> array( 'ajax_filter' => array( array( 'exclude' => '.bt_bb_image_content' ) ) ),
					'caption'			=> array( 'ajax_filter' => array( array( 'exclude' => '.bt_bb_image_content' ) ) ),
					'size'				=> array( 'ajax_filter' => array( array( 'exclude' => '.bt_bb_image_content' ) ) ),
					'shape'				=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
					'align'				=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'url'				=> array( 'ajax_filter' => array( array( 'exclude' => '.bt_bb_image_content' ) ) ),
					'target'			=> array( 'ajax_filter' => array( array( 'exclude' => '.bt_bb_image_content' ) ) ),
					'hover_style'		=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
					'content_display'	=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'content_align'		=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
				),
				'drag_and_drop' => array(
					'target_selector' => '.bt_bb_image_content_inner'
				)
			),
			'bt_bb_latest_posts' => array(
				'edit_box_selector' => '',
				'params' => array(
					'gap'         => array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
					'target'      => array( 'js_handler'  => array( 'target_selector' => '.bt_bb_latest_posts_item_image > a, .bt_bb_latest_posts_item_title > a', 'type' => 'attr', 'attr' => 'target' ) ),
					'image_shape' => array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
				),
			),
			'bt_bb_masonry_image_grid' => array(
				'edit_box_selector' => '',
				'ajax_trigger_window_load' => true,
				'params' => array(
					'images' => array(),
					'gap'    => array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'class' ) ),
				),
			),
			'bt_bb_css_image_grid' => array(
				'edit_box_selector' => '',
				'ajax_callback' => 'bt_bb_init_css_image_grid_lightbox',
				'params' => array(
					'images'  => array(),
					'columns' => array(),
					'gap'     => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'shape'   => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'format'  => array(),
				),
			),
			'bt_bb_masonry_post_grid' => array(
				'edit_box_selector' => '',
				'params' => array(
					'gap' => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
				),
			),
			'bt_bb_css_post_grid' => array(
				'edit_box_selector' => '',
				'ajax_callback' => 'bt_bb_css_post_grid_reinit',
				'params' => array(
					'gap'   => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'shape' => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'title_lines' => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'excerpt_lines' => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'hover_style' => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
				),
			),
			'bt_bb_leaflet_map' => array( 
				'edit_box_selector' => '',
				'ajax_callback' => 'bt_bb_leaflet_init_late_all',
				'params' => array(
					'zoom'             => array(),
					'max_zoom'         => array(),
					'height'           => array(),
					'predefined_style' => array(),
					'custom_style'     => array(),
					'center_map'       => array(),
					'scroll_wheel'     => array(),
					'zoom_control'     => array(),
				),
			),		
			'bt_bb_price_list' => array(
				'edit_box_selector' => '',
				'params' => array(
					'title'				=> array( 'js_handler' => array( 'target_selector' => '.bt_bb_price_list_title', 'type' => 'inner_html' ) ),
					'subtitle'			=> array( 'js_handler' => array( 'target_selector' => '.bt_bb_price_list_subtitle', 'type' => 'inner_html' ) ),
					'currency'			=> array( 'js_handler' => array( 'target_selector' => '.bt_bb_price_list_currency', 'type' => 'inner_html' ) ),
					'price'				=> array( 'js_handler' => array( 'target_selector' => '.bt_bb_price_list_amount', 'type' => 'inner_html' ) ),
					'items'				=> array(),
					'color_scheme'		=> array( 'ajax_filter' => array( 'class', 'style' ) ), 
					'currency_position' => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
				),
			),
			'bt_bb_progress_bar' => array(
				'edit_box_selector' => '',
				'params' => array(
					'percentage'		=> array( 'js_handler'  => array( 'target_selector' => '.bt_bb_progress_bar_inner', 'type' => 'attr', 'attr' => 'style', 'preprocess' => 'progress_bar_style' ) ),
					'text'				=> array( 'js_handler'  => array( 'target_selector' => '.bt_bb_progress_bar_text', 'type' => 'inner_html' ) ),
					'color_scheme'		=> array( 'ajax_filter' => array( 'class', 'style' ) ), 
					'align'				=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'size'				=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'style'				=> array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'shape'				=> array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
				),
			),
			'bt_bb_raw_content' => array(
				'edit_box_selector' => '',
				'params' => array(
					'raw_content' => array(),
				),
			),
			'bt_bb_row' => array(
				'edit_box_selector' => '',
				'params' => array(
					// 'column_gap'       => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'column_gap'        => array( 'ajax_filter' => array( 'class', 'style' ) ),
					'row_width'        => array( 'ajax_filter' => array( 'class', 'style' ) ),
					'color_scheme'     => array( 'ajax_filter' => array( 'class', 'style' ) ),
					'background_color' => array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'background_color' ) ),
				),
			),
			'bt_bb_row_inner' => array(
				'edit_box_selector' => '',
				'params' => array(
					// 'column_gap'       => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'column_gap'        => array( 'ajax_filter' => array( 'class', 'style' ) ),
					'row_width'        => array( 'ajax_filter' => array( 'class', 'style' ) ),
				),
			),
			'bt_bb_section' => array(
				'edit_box_selector' => '',
				'params' => array(
					'layout'				=> array( 'ajax_filter' => array( 'class' ) ),
					'top_spacing'			=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'bottom_spacing'		=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'full_screen'			=> array( 'ajax_filter' => array( 'class' ) ), // non-standard class handling in bt_bb_section.php - can not use js_handler
					'vertical_align'		=> array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ), 
					'background_image'		=> array( 'js_handler'  => array( 'target_selector' => '.bt_bb_background_image_holder', 'type' => 'background_image' ) ),
					'parallax'				=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'attr', 'attr' => 'data-parallax' ) ),
					'parallax_offset'		=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'attr', 'attr' => 'data-parallax-offset' ) ),
					'color_scheme'			=> array( 'ajax_filter' => array( 'class', 'style' ) ), 
					'background_overlay'	=> array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'background_color'		=> array( 'js_handler'  => array( 'target_selector' => '', 'type' => 'background_color' ) ),
				),
				'drag_and_drop' => array(
					'disable_as_source' => true,
					'disable_as_target' => false,
				)
			),
			'bt_bb_separator' => array(
				'edit_box_selector' => '',
				'params' => array(
					'top_spacing'		=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'bottom_spacing'	=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'border_style'		=> array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'border_thickness'	=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'color_scheme'		=> array( 'ajax_filter' => array( 'class', 'style' ) ), 
					'icon'				=> array(),
					'icon_size'			=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'text'				=> array(), // js_handler not working very well if we want to remove text (text container is not removed)...
					'text_size'			=> array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
				),
			),
			'bt_bb_service' => array(
				'edit_box_selector' => '',
				'params' => array(
					'ai_prompt'    => array(),
					'icon'         => array(),
					'title'        => array( 'js_handler'  => array( 'target_selector' => '.bt_bb_service_content_title', 'type' => 'inner_html' ) ),
					'html_tag'     => array(),
					'text'         => array( 'js_handler'  => array( 'target_selector' => '.bt_bb_service_content_text', 'type' => 'inner_html_nl2br' ) ),
					'url'          => array( 'js_handler'  => array( 'target_selector' => 'a.bt_bb_icon_holder, .bt_bb_service_content_title a', 'type' => 'attr', 'attr' => 'href' ) ),
					'target'       => array( 'js_handler'  => array( 'target_selector' => 'a.bt_bb_icon_holder, .bt_bb_service_content_title a', 'type' => 'attr', 'attr' => 'target' ) ),
					'size'         => array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
					'color_scheme' => array( 'ajax_filter' => array( 'class', 'style' ) ), 
					'style'        => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'shape'        => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'align'        => array( 'ajax_filter' => array( 'class', 'data-bt-override-class' ) ),
				),
			),
			'bt_bb_shortcode' => array(
				'edit_box_selector' => '',
				'params' => array(
					'shortcode_content' => array(),
				),
			),
			'bt_bb_slider' => array(
				'edit_box_selector' => '',
				'ajax_slick' => true,
				'params' => array(
					'images'              => array(),
					'height'              => array(),
					'size'                => array(),
					'animation'           => array(),
					'show_arrows'         => array(),
					'show_dots'           => array(),
					'slides_to_show'      => array(),
					'additional_settings' => array(),
					'auto_play'           => array(),
					'pause_on_hover'      => array(),
					'use_lightbox'        => array(),
				),
			),
			'bt_bb_tabs' => array(
				'edit_box_selector' => '',
				'params' => array(
					'style'        => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'shape'        => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
					'color_scheme' => array( 'ajax_filter' => array( 'class', 'style' ) ),
				),
			),
			'bt_bb_tab_item' => array(
				'edit_box_selector' => '',
				'params' => array(
					'title' => array( 'js_handler' => array( 'target_selector' => 'span', 'type' => 'inner_html' ) ),
				),
			),
			'bt_bb_text' => array(
				'ajax_mejs' => true,
				'edit_box_selector' => '',
				'params'=> array(),
			),			
			'bt_bb_video' => array(
				'edit_box_selector' => '',
				'ajax_mejs' => true,
				'params' => array(
					'video'            => array(),
					'disable_controls' => array( 'js_handler' => array( 'target_selector' => '', 'type' => 'class' ) ),
				),
			),
		);
		
		$temp_elements = array();

		foreach( BT_BB_Root::$elements as $el_name => $arr ) {
			if ( ! isset( BT_BB_FE::$elements[ $el_name ] ) ) {
				$temp_elements[ $el_name ] = array( 'edit_box_selector' => '', 'params' => array() );
			} else {
				$temp_elements[ $el_name ] = BT_BB_FE::$elements[ $el_name ];
			}
			$temp_elements[ $el_name ]['params'] = array();
			if ( isset( $arr['params'] ) ) {
				foreach( $arr['params'] as $param ) {
					$param_name = $param['param_name'];
					$param_type = $param['type'];
					if ( ! isset( BT_BB_FE::$elements[ $el_name ]['params'][ $param_name ] ) && $param_type != 'hidden' ) {
						$temp_elements[ $el_name ]['params'][ $param_name ] = array();
					} else if ( $param_type != 'hidden' ) {
						$temp_elements[ $el_name ]['params'][ $param_name ] = BT_BB_FE::$elements[ $el_name ]['params'][ $param_name ];
					}
				}
			}
		}
		
		BT_BB_FE::$elements = $temp_elements;
		
		BT_BB_FE::$elements = apply_filters( 'bt_bb_fe_elements', BT_BB_FE::$elements );
		
		BT_BB_FE::$templates = apply_filters( 'bt_bb_fe_templates', array(
			'accordion' => array( // id; id.txt is name of the file in /templates
				'base' => esc_html__( 'bt_bb_accordion', 'bold-builder' ), // base is used to detect if template is allowed at requested position
				'name' => esc_html__( 'Accordion', 'bold-builder' ),
				'description' => esc_html__( 'Accordion container with few items', 'bold-builder' ),
			),
			'button' => array(
				'base' => esc_html__( 'bt_bb_button', 'bold-builder' ),
				'name' => esc_html__( 'Button', 'bold-builder' ),
				'description' => esc_html__( 'Button with custom link', 'bold-builder' ),
			),
			'contact_form_7' => array(
				'base' => esc_html__( 'bt_bb_contact_form_7', 'bold-builder' ),
				'name' => esc_html__( 'Contact Form 7', 'bold-builder' ),
				'description' => esc_html__( 'Choose CF7 form', 'bold-builder' ),
			),
			'countdown' => array(
				'base' => esc_html__( 'bt_bb_countdown', 'bold-builder' ),
				'name' => esc_html__( 'Countdown', 'bold-builder' ),
				'description' => esc_html__( 'Animated countdown', 'bold-builder' ),
			),
			'counter' => array(
				'base' => esc_html__( 'bt_bb_counter', 'bold-builder' ),
				'name' => esc_html__( 'Counter', 'bold-builder' ),
				'description' => esc_html__( 'Animated counter', 'bold-builder' ),
			),
			'custom_menu' => array(
				'base' => esc_html__( 'bt_bb_custom_menu', 'bold-builder' ),
				'name' => esc_html__( 'Custom Menu', 'bold-builder' ),
				'description' => esc_html__( 'Custom WordPress menu', 'bold-builder' ),
			),
			'google_maps' => array(
				'base' => esc_html__( 'bt_bb_google_maps', 'bold-builder' ),
				'name' => esc_html__( 'Google Maps', 'bold-builder' ),
				'description' => esc_html__( 'Google Maps map with custom content', 'bold-builder' ),
			),
			'headline' => array(
				'base' => esc_html__( 'bt_bb_headline', 'bold-builder' ),
				'name' => esc_html__( 'Headline', 'bold-builder' ),
				'description' => esc_html__( 'Headline with custom fonts (and AI help)', 'bold-builder' ),
			),
			'icon' => array(
				'base' => esc_html__( 'bt_bb_icon', 'bold-builder' ),
				'name' => esc_html__( 'Icon', 'bold-builder' ),
				'description' => esc_html__( 'Single icon with link', 'bold-builder' ),
			),
			'image' => array(
				'base' => esc_html__( 'bt_bb_image', 'bold-builder' ),
				'name' => esc_html__( 'Image', 'bold-builder' ),
				'description' => esc_html__( 'Single image', 'bold-builder' ),
			),
			'image_grid' => array(
				'base' => esc_html__( 'bt_bb_css_image_grid', 'bold-builder' ),
				'name' => esc_html__( 'Image Grid', 'bold-builder' ),
				'description' => esc_html__( 'Grid with images', 'bold-builder' ),
			),
			'slider' => array(
				'base' => esc_html__( 'bt_bb_slider', 'bold-builder' ),
				'name' => esc_html__( 'Image Slider', 'bold-builder' ),
				'description' => esc_html__( 'Slider with images', 'bold-builder' ),
			),
			'inner_row_11' => array(
				'base' => esc_html__( 'bt_bb_row_inner', 'bold-builder' ),
				'name' => esc_html__( 'Inner Row (1/1)', 'bold-builder' ),
				'description' => esc_html__( 'Inner Row with 1 column', 'bold-builder' ),
			),
			'inner_row_12+12' => array(
				'base' => esc_html__( 'bt_bb_row_inner', 'bold-builder' ),
				'name' => esc_html__( 'Inner Row (1/2+1/2)', 'bold-builder' ),
				'description' => esc_html__( 'Inner Row with 2 columns', 'bold-builder' ),
			),
			'inner_row_13+13+13' => array(
				'base' => esc_html__( 'bt_bb_row_inner', 'bold-builder' ),
				'name' => esc_html__( 'Inner Row (1/3+1/3+1/3)', 'bold-builder' ),
				'description' => esc_html__( 'Inner Row with 3 columns', 'bold-builder' ),
			),
			'inner_row_23+13' => array(
				'base' => esc_html__( 'bt_bb_row_inner', 'bold-builder' ),
				'name' => esc_html__( 'Inner Row (2/3+1/3)', 'bold-builder' ),
				'description' => esc_html__( 'Inner Row with 2 columns', 'bold-builder' ),
			),
			'inner_row_13+23' => array(
				'base' => esc_html__( 'bt_bb_row_inner', 'bold-builder' ),
				'name' => esc_html__( 'Inner Row (1/3+2/3)', 'bold-builder' ),
				'description' => esc_html__( 'Inner Row with 2 columns', 'bold-builder' ),
			),
			'latest_posts' => array(
				'base' => esc_html__( 'bt_bb_latest_posts', 'bold-builder' ),
				'name' => esc_html__( 'Latest Posts', 'bold-builder' ),
				'description' => esc_html__( 'List of latest posts', 'bold-builder' ),
			),
			'leaflet_map' => array(
				'base' => esc_html__( 'bt_bb_leaflet_map', 'bold-builder' ),
				'name' => esc_html__( 'OpenStreetMap', 'bold-builder' ),
				'description' => esc_html__( 'OpenStreetMap with custom content', 'bold-builder' ),
			),
			'post_grid' => array(
				'base' => esc_html__( 'bt_bb_css_post_grid', 'bold-builder' ),
				'name' => esc_html__( 'Post Grid', 'bold-builder' ),
				'description' => esc_html__( 'Post grid with images', 'bold-builder' ),
			),
			'price_list' => array(
				'base' => esc_html__( 'bt_bb_price_list', 'bold-builder' ),
				'name' => esc_html__( 'Price List', 'bold-builder' ),
				'description' => esc_html__( 'List of items with total price', 'bold-builder' ),
			),
			'progress_bar' => array(
				'base' => esc_html__( 'bt_bb_progress_bar', 'bold-builder' ),
				'name' => esc_html__( 'Progress Bar', 'bold-builder' ),
				'description' => esc_html__( 'Animated progress bar', 'bold-builder' ),
			),
			'raw_content' => array(
				'base' => esc_html__( 'bt_bb_raw_content', 'bold-builder' ),
				'name' => esc_html__( 'Raw Content', 'bold-builder' ),
				'description' => esc_html__( 'Raw HTML/JS content', 'bold-builder' ),
			),
			'separator' => array(
				'base' => esc_html__( 'bt_bb_separator', 'bold-builder' ),
				'name' => esc_html__( 'Separator', 'bold-builder' ),
				'description' => esc_html__( 'Separator line', 'bold-builder' ),
			),
			'service' => array(
				'base' => esc_html__( 'bt_bb_service', 'bold-builder' ),
				'name' => esc_html__( 'Service', 'bold-builder' ),
				'description' => esc_html__( 'Icon with text (and AI help)', 'bold-builder' ),
			),
			'shortcode' => array(
				'base' => esc_html__( 'bt_bb_shortcode', 'bold-builder' ),
				'name' => esc_html__( 'Shortcode', 'bold-builder' ),
				'description' => esc_html__( 'Custom shortcode', 'bold-builder' ),
			),
			'content_slider' => array(
				'base' => esc_html__( 'bt_bb_content_slider', 'bold-builder' ),
				'name' => esc_html__( 'Slider', 'bold-builder' ),
				'description' => esc_html__( 'Slider with custom content', 'bold-builder' ),
			),
			'tabs' => array(
				'base' => esc_html__( 'bt_bb_tabs', 'bold-builder' ),
				'name' => esc_html__( 'Tabs', 'bold-builder' ),
				'description' => esc_html__( 'Tabs container with few items', 'bold-builder' ),
			),
			'text' => array(
				'base' => esc_html__( 'bt_bb_text', 'bold-builder' ),
				'name' => esc_html__( 'Text', 'bold-builder' ),
				'description' => esc_html__( 'Text element (with AI help)', 'bold-builder' ),
			),
			'video' => array(
				'base' => esc_html__( 'bt_bb_video', 'bold-builder' ),
				'name' => esc_html__( 'Video', 'bold-builder' ),
				'description' => esc_html__( 'Video player', 'bold-builder' ),
			),
		) );
		
		add_action( 'wp_head', 'bt_bb_fe_head' );
		add_action( 'wp_head', 'bt_bb_translate' );
		add_action( 'wp_footer', 'bt_bb_fe_footer' );
		
		add_action( 'wp_head', function() {
			wp_enqueue_style( 'bt_bb_framework-leaflet-css', plugin_dir_url( __FILE__ ) . 'css/leafletmap/leaflet.css', array(), BT_BB_VERSION, 'screen' );
			wp_enqueue_style( 'bt_bb_framework-markercluster-css', plugin_dir_url( __FILE__ ) . 'css/leafletmap/MarkerCluster.css', array(), BT_BB_VERSION, 'screen' );
			wp_enqueue_style( 'bt_bb_framework-markercluster-default-css',  plugin_dir_url( __FILE__ ) . 'css/leafletmap/MarkerCluster.Default.css', array(), BT_BB_VERSION, 'screen' );
		});	
		
	}
}

function bt_bb_fe_head() {
	echo '<script>';
		echo 'window.bt_bb_fe_elements = ' . bt_bb_json_encode( BT_BB_FE::$elements ) . ';';
		echo 'window.bt_bb_fe_templates = ' . bt_bb_json_encode( BT_BB_FE::$templates ) . ';';
		BT_BB_Root::$elements = apply_filters( 'bt_bb_elements', BT_BB_Root::$elements );
		$elements = BT_BB_Root::$elements;
		foreach ( $elements as $key => $value ) {
			$params = isset( $value[ 'params' ] ) ? $value[ 'params' ] : null;
			$params1 = array();
			if ( is_array( $params ) ) {
				foreach ( $params as $param ) {
					$params1[ $param['param_name'] ] = $param;
				}
			}
			$elements[ $key ][ 'params' ] = $params1;
		}
		echo 'window.bt_bb_elements = ' . bt_bb_json_encode( $elements ) . ';';
		global $post;
		echo 'window.bt_bb_post_id = ' . $post->ID . ';';
		echo 'window.bt_bb_edit_url = "' . get_edit_post_link( get_the_ID(), '' ) . '";';
		echo 'window.bt_bb_settings = [];';
		$options = get_option( 'bt_bb_settings' );
		$slug_url = array_key_exists( 'slug_url', $options ) ? $options['slug_url'] : '';
		echo 'window.bt_bb_settings.slug_url = "' . esc_js( $slug_url ) . '";';
		echo 'window.bt_bb_ajax_url = "' . esc_js( admin_url( 'admin-ajax.php' ) ) . '";'; // back. compat.
		echo 'window.bt_bb_fa_url = "' . plugins_url( 'css/font-awesome.min.css', __FILE__ ) . '";';
		echo 'window.bt_bb_fe_dialog_content_css_url = "' . plugins_url( 'css/front_end/fe_dialog_content.crush.css', __FILE__ ) . '";';
		
		if ( file_exists( get_parent_theme_file_path( '/admin-style.css' ) ) ) {
			echo 'window.bt_bb_fe_dialog_admin_css = "' . get_parent_theme_file_uri( 'admin-style.css' ) . '";';
		}
		
		echo 'window.bt_bb_fe_dialog_bottom_css_url = "' . plugins_url( 'css/front_end/fe_dialog_bottom.crush.css', __FILE__ ) . '";';
		if ( is_rtl() ) {
			echo 'window.bt_bb_rtl = true;';
		} else {
			echo 'window.bt_bb_rtl = false;';
		}
		if ( function_exists( 'boldthemes_get_icon_fonts_bb_array' ) ) {
			$icon_arr = boldthemes_get_icon_fonts_bb_array();
		} else {
			require_once( dirname(__FILE__) . '/content_elements_misc/fa_icons.php' );
			require_once( dirname(__FILE__) . '/content_elements_misc/fa5_regular_icons.php' );
			require_once( dirname(__FILE__) . '/content_elements_misc/fa5_solid_icons.php' );
			require_once( dirname(__FILE__) . '/content_elements_misc/fa5_brands_icons.php' );
			require_once( dirname(__FILE__) . '/content_elements_misc/s7_icons.php' );
			$icon_arr = array( 'Font Awesome' => bt_bb_fa_icons(), 'Font Awesome 5 Regular' => bt_bb_fa5_regular_icons(), 'Font Awesome 5 Solid' => bt_bb_fa5_solid_icons(), 'Font Awesome 5 Brands' => bt_bb_fa5_brands_icons(), 'S7' => bt_bb_s7_icons() );
		}
		
		echo 'window.bt_bb_ajax_nonce = "' . wp_create_nonce( 'bt_bb_nonce' ) . '";'; // fix nonce issue on local sites with ai.js (not working with wp_localize_script window.bt_bb_ajax.nonce)
		
		echo 'window.bt_bb_icons = JSON.parse(\'' . bt_bb_json_encode( $icon_arr ) . '\')';

	echo '</script>';
}

function bt_bb_fe_footer() {
	echo '<div id="bt_bb_fe_dialog">';
		echo '<div>';
			echo '<div id="bt_bb_fe_dialog_main">';
				echo '<div class="bt_bb_dialog_header">';
					echo '<div class="bt_bb_dialog_header_text"></div>';
					echo '<div id="bt_bb_fe_dialog_close" role="button" class="bt_bb_dialog_close" title="' . esc_html__( 'Close dialog', 'bold-builder' ) . '"></div>';
					echo '<div id="bt_bb_fe_dialog_switch" role="button" title="' . esc_html__( 'Switch side', 'bold-builder' ) . '"><i class="fa fa-exchange"></i></div>';
				echo '</div>';
				echo '<div class="bt_bb_dialog_header_tools"></div>';
				echo '<div id="bt_bb_fe_dialog_content"></div>';
				echo '<div id="bt_bb_fe_dialog_tinymce_container">';
					// https://developer.wordpress.org/reference/classes/_wp_editors/parse_settings/
					wp_editor( '' , 'bt_bb_fe_dialog_tinymce', array( 'media_buttons' => false, 'editor_height' => 200, 'tinymce' => array(
						'toolbar1'      => 'bold,italic,underline,separator,alignleft,aligncenter,alignright,separator',
						'toolbar2'      => '',
						'toolbar3'      => '',
					) ) );
				echo '</div>';
				echo '<div id="bt_bb_fe_dialog_bottom"></div>';
			echo '</div>';
			// echo '<div id="bt_bb_fe_dialog_close" title="Close dialog"><i class="fa fa-close"></i></div>';
			
		echo '</div>';
	echo '</div>';
	
	if ( BT_BB_Root::$has_footer && ! isset( $_GET[ 'bt_bb_edit_footer' ] ) ) {
		echo '<a href="' . add_query_arg( 'bt_bb_edit_footer', '', get_post_permalink( BT_BB_Root::$footer_page_id ) ) . '" target="_blank" class="bt_bb_fe_preview_toggler bt_bb_fe_preview_toggler_footer">' . esc_html__( 'Edit Footer', 'bold-builder' ) . '</a>';
	}
	
	echo '<div class="bt_bb_dd_tip"></div>';
	echo '<div class="bt_bb_desc_tip"></div>';
	
	echo '<div id="bt_bb_fe_init_mouseover"></div>';
}

/**
 * Save post
 */

function bt_bb_fe_save() {
	check_ajax_referer( 'bt_bb_fe_nonce', 'nonce' );
	$post_id = intval( $_POST['post_id'] );
	$post_content = wp_kses_post( $_POST['post_content'] );
	if ( current_user_can( 'edit_post', $post_id ) ) {
		$post = array(
			'ID'           => $post_id,
			'post_content' => $post_content,
		);
		wp_update_post( $post );
		echo 'ok';
	}
	wp_die();
}
add_action( 'wp_ajax_bt_bb_fe_save', 'bt_bb_fe_save' );

/**
 * Get HTML
 */
function bt_bb_fe_get_html() {
	check_ajax_referer( 'bt_bb_fe_nonce', 'nonce' );
	$post_id = intval( $_POST['post_id'] );
	$content = stripslashes( wp_kses_post( $_POST['content'] ) );
	if ( current_user_can( 'edit_post', $post_id ) ) {
		remove_filter( 'the_content', 'wpautop' );
		$html = apply_filters( 'the_content', $content );
		$html = str_ireplace( array( '``', '`{`', '`}`' ), array( '&quot;', '&#91;', '&#93;' ), $html );
		$html = str_ireplace( array( '*`*`*', '*`*{*`*', '*`*}*`*' ), array( '``', '`{`', '`}`' ), $html );
		echo $html;
	}
	wp_die();
}
add_action( 'wp_ajax_bt_bb_fe_get_html', 'bt_bb_fe_get_html' );

/**
 * Get template HTML
 */
function bt_bb_fe_get_template_html() {
	check_ajax_referer( 'bt_bb_fe_nonce', 'nonce' );
	$post_id = intval( $_POST['post_id'] );
	$edit_url = isset( $_POST['edit_url'] ) ? esc_url( $_POST['edit_url'] ) : '';
	$layout = sanitize_text_field( $_POST['layout'] );
	$layout = preg_replace( '/[^a-zA-Z0-9_\-\+]/', '', $layout );
	$type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
	$content = @file_get_contents( get_stylesheet_directory() . '/bold-page-builder/templates/' . $layout . '.txt' );
	if ( ! $content ) {
		$content = @file_get_contents( get_template_directory() . '/bold-page-builder/templates/' . $layout . '.txt' );
	}
	if ( ! $content ) {
		$content = file_get_contents( plugin_dir_path( __FILE__ ) . '/templates/' . $layout . '.txt' );
	}
	$content = trim( $content );
	if ( current_user_can( 'edit_post', $post_id ) ) {
		if ( str_starts_with( $content, '[bt_bb_' ) ) {
			remove_filter( 'the_content', 'wpautop' );
			$content = apply_filters( 'the_content', $content );
			$content = str_ireplace( array( '``', '`{`', '`}`' ), array( '&quot;', '&#91;', '&#93;' ), $content );
			$content = str_ireplace( array( '*`*`*', '*`*{*`*', '*`*}*`*' ), array( '``', '`{`', '`}`' ), $content );
		}
		
		if ( $type == 'section' ) {
			$fe_wrap_open = '<div class="bt_bb_fe_wrap">';
			$fe_wrap_open .= '<span class="bt_bb_fe_count"><span class="bt_bb_fe_count_inner"></span>
			<ul class="bt_bb_element_menu">
				<li><span class="bt_bb_element_menu_edit">' . esc_html__( 'Edit', 'bold-builder' ) . '</span></li>
				<li data-edit_url="' . esc_attr( $edit_url ) . '"><span class="bt_bb_element_menu_edit_be">' . esc_html__( 'Edit in back-end editor', 'bold-builder' ) . '</span><ul><li><span class="bt_bb_element_menu_edit_be_new_tab">' . esc_html__( '(new tab)', 'bold-builder' ) . '</span></li></ul></li>
				<li><span class="bt_bb_element_menu_cut">' . esc_html__( 'Cut', 'bold-builder' ) . '</span></li>
				<li><span class="bt_bb_element_menu_copy">' . esc_html__( 'Copy', 'bold-builder' ) . '</span></li>
				<li><span class="bt_bb_element_menu_paste">' . esc_html__( 'Paste', 'bold-builder' ) . '</span><ul><li><span class="bt_bb_element_menu_paste_above">' . esc_html__( '(above)', 'bold-builder' ) . '</span></li></ul></li>
				<li class="bt_bb_element_menu_delete_parent"><span class="bt_bb_element_menu_delete">' . esc_html__( 'Delete', 'bold-builder' ) . '</span></li>
			</ul>
			</span>';
			$fe_wrap_close = '</div>';
			echo $fe_wrap_open . $content . $fe_wrap_close;
		} else {
			echo $content;
		}
	}
	wp_die();
}
add_action( 'wp_ajax_bt_bb_fe_get_template_html', 'bt_bb_fe_get_template_html' );
