<?php
 
/**
 * Plugin Name: Bold Builder
 * Description: WordPress page builder.
 * Version: 5.4.0
 * Author: BoldThemes
 * Author URI: https://www.bold-themes.com
 * Text Domain: bold-builder
 */
 
defined( 'ABSPATH' ) || exit;

// VERSION --------------------------------------------------------- \\
define( 'BT_BB_VERSION', '5.4.0' );
// VERSION --------------------------------------------------------- \\
 
define( 'BT_BB_FEATURE_ADD_ELEMENTS', true );
 
/**
 * Enqueue scripts and styles
 */

if ( file_exists( plugin_dir_path( __FILE__ ) . 'css-crush/CssCrush.php' ) && strpos( $_SERVER['SERVER_NAME'], '-dev' ) ) {
	require_once( 'css-crush/CssCrush.php' );
}

if ( file_exists( get_template_directory() . '/bt_bb_config.php' ) ) {
	require_once( get_template_directory() . '/bt_bb_config.php' );
}

add_filter( 'the_content', 'bt_bb_parse_content', 20 );
function bt_bb_parse_content( $content ) {
	
	if ( ! ( is_singular() && in_the_loop() && is_main_query() ) ) {
		return $content;
	}
	
	global $wp_query;
	global $post;
	if ( $post && $wp_query && $wp_query->queried_object && $post->ID != $wp_query->queried_object->ID ) { // latest posts fix (The Loop & bt_bb_active_for_post_type_fe())
		return $content;
	}
	
	if ( BT_BB_Root::$fe_wrap_count > 1 ) {
		return $content;
	}

	if ( bt_bb_active_for_post_type_fe() ) {
		
		BT_BB_Root::$fe_wrap_count++;
		
		$content = str_ireplace( array( '``', '`{`', '`}`' ), array( '&quot;', '&#91;', '&#93;' ), $content );
		$content = str_ireplace( array( '*`*`*', '*`*{*`*', '*`*}*`*' ), array( '``', '`{`', '`}`' ), $content );

		if ( is_singular() ) {
			
			$data_templates_time = '';
			if ( current_user_can( 'edit_pages' ) ) {
				$files1 = glob( get_stylesheet_directory() . '/bold-page-builder/templates/*.txt' );
				$files2 = glob( get_template_directory() . '/bold-page-builder/templates/*.txt' );
				$files3 = glob( plugin_dir_path( __FILE__ ) . 'templates/*.txt' );
				if ( count( $files1 ) > 0 ) {
					$data_templates_time = max( array_map( 'filemtime', $files1 ) );
				}
				if ( count( $files2 ) > 0 ) {
					$data_templates_time .= max( array_map( 'filemtime', $files2 ) );
				}
				if ( count( $files3 ) > 0 ) {
					$data_templates_time .= max( array_map( 'filemtime', $files3 ) );
				}
			}
			
			$data_templates_time = ' ' . 'data-templates-time="' . $data_templates_time . '"';
			
			$_content = $content;
			$content = '<div class="bt_bb_wrapper"' . $data_templates_time . '>' . $content;
			if ( current_user_can( 'edit_pages' ) && ( strpos( $_content, 'bt_bb_fe_wrap' ) || $_content == '' ) ) {
				if ( ! class_exists( 'BoldThemes_BB_Settings' ) || ! BoldThemes_BB_Settings::$custom_content_elements ) { // only with native BB elements
					if ( ! ( isset( $_GET['bt_bb_fe_preview'] ) && $_GET['bt_bb_fe_preview'] == 'true' ) ) {
						if ( BT_BB_FEATURE_ADD_ELEMENTS ) {
							$content .= '<div class="bt_bb_fe_wrap">';
							$content .= '<div class="bt_bb_fe_add_template">';
							$content .= '<h6>' . esc_html__( 'Add Section/Row/Column(s)', 'bold-builder' ) . '</h6>';
							$content .= '<div class="bt_bb_fe_add_template_list">';
								$content .= '<div data-layout-id="11" title="1/1"></div>';
								$content .= '<div data-layout-id="12+12" title="1/2+1/2"></div>';
								$content .= '<div data-layout-id="13+13+13" title="1/3+1/3+1/3"></div>';
								$content .= '<div data-layout-id="14+14+14+14" title="1/4+1/4+1/4+1/4"></div>';
								$content .= '<div data-layout-id="15+15+15+15+15" title="1/5+1/5+1/5+1/5+1/5"></div>';
								$content .= '<div data-layout-id="23+13" title="2/3+1/3"></div>';
								$content .= '<div data-layout-id="13+23" title="1/3+2/3"></div>';
								$content .= '<div data-layout-id="34+14" title="3/4+1/4"></div>';
								$content .= '<div data-layout-id="14+34" title="1/4+3/4"></div>';
								$content .= '<div data-layout-id="14+24+14" title="1/4+2/4+1/4"></div>';
							$content .= '</div>';
							$content .= '</div>';
							$content .= '</div>';
						}
					}
				}
			}
			$content .= '</div>';
			$content .= '<span id="bt_bb_fe_preview_toggler" class="bt_bb_fe_preview_toggler" title="' . esc_html__( 'Edit/Preview', 'bold-builder' ) . '"></span>';
			if ( ! class_exists( 'BoldThemes_BB_Settings' ) || ! BoldThemes_BB_Settings::$custom_content_elements ) { // only with native BB elements
				if ( current_user_can( 'edit_pages' ) && ( strpos( $_content, 'bt_bb_fe_wrap' ) || $_content == '' ) ) {
					if ( ! ( isset( $_GET['bt_bb_fe_preview'] ) && $_GET['bt_bb_fe_preview'] == 'true' ) ) {
						if ( BT_BB_FEATURE_ADD_ELEMENTS ) {
							$content .= '<span id="bt_bb_fe_add_elements" title="' . esc_html__( 'Add Elements', 'bold-builder' ) . '"></span>';
						}
						$content .= '<span id="bt_bb_fe_undo" title="' . esc_attr__( 'Undo', 'bold-builder' ) . '" disabled></span>';
						$content .= '<span id="bt_bb_fe_redo" title="' . esc_attr__( 'Redo', 'bold-builder' ) . '" disabled></span>';
						$content .= '<span id="bt_bb_fe_save" title="' . esc_attr__( 'Save changes', 'bold-builder' ) . '"></span>';
					}
				}
			}
		}
	}
	return $content;
}

add_filter( 'body_class', 'bt_bb_body_class' );
function bt_bb_body_class( $classes ) {
	$extra_classes = array();
	$extra_classes[] = 'bt_bb_plugin_active';
	$extra_classes[] = 'bt_bb_fe_preview_toggle';
	if ( isset( $_GET[ 'bt_bb_edit_footer' ] ) ) {
		$extra_classes[] = 'bt_bb_edit_footer';
	}	
	return array_merge( $classes, $extra_classes );
}

// WP 5

function bt_bb_disable_gutenberg( $is_enabled, $post_type ) {
	$options = get_option( 'bt_bb_settings' );
	if ( ! $options || ( ! array_key_exists( $post_type, $options ) || ( array_key_exists( $post_type, $options ) && $options[ $post_type ] == '1' ) ) ) {
		return false;
	}
	return $is_enabled;
}
add_filter( 'use_block_editor_for_post_type', 'bt_bb_disable_gutenberg', 10, 2 );

function bt_bb_parse_content_admin( $content ) {
	
	if ( ! ( is_singular() && in_the_loop() && is_main_query() ) ) {
		return $content;
	}
	
	global $wp_query;
	global $post;
	if ( $post && $wp_query && $wp_query->queried_object && $post->ID != $wp_query->queried_object->ID ) { // latest posts fix
		return $content;
	}
	
	if ( BT_BB_Root::$fe_wrap_count > 0 ) {
		return $content;
	}
	
	BT_BB_Root::$fe_wrap_count++;

	$bt_bb_content = false;
	if ( current_user_can( 'edit_pages' ) && ( ( strpos( trim( $content, "\t\n"), '[bt_bb' ) === 0 || strpos( trim( $content, "\t\n" ), '[bt_section' ) === 0 ) || $content == '' ) ) {
		$bt_bb_content = true;
	}
	
	if ( ! $bt_bb_content ) {
		return $content;
	}

	global $bt_bb_map;
	
	foreach( BT_BB_Root::$elements as $base => $params ) {
		$proxy = new BT_BB_FE_Map_Proxy( $base, $params );
		$proxy->js_map();
	}
	
	global $bt_bb_array;
	$bt_bb_array = array();

	bt_bb_do_shortcode( $content );

	global $bt_bb_fe_array;
	$bt_bb_fe_array = array();
	
	global $bt_bb_fe_array_depth;
	$bt_bb_fe_array_depth = -1;
	
	bt_bb_fe_do_shortcode( $bt_bb_array );

	$wrap_arr = array();
	
	$count = 0;

	foreach( $bt_bb_fe_array as $item ) {
		
		$fe_wrap_open = '';
		$fe_wrap_close = '';

		if ( isset( $bt_bb_map[ $item['base'] ] ) && isset( $bt_bb_map[ $item['base'] ]['root'] ) && $bt_bb_map[ $item['base'] ]['root'] === true && $item['depth'] == 0 ) {
			$count++;
			//if ( ! $bt_footer ) {
				if ( ! class_exists( 'BoldThemes_BB_Settings' ) || ! BoldThemes_BB_Settings::$custom_content_elements ) { // only with native BB elements
					if ( isset( $_GET['bt_bb_fe_preview'] ) && $_GET['bt_bb_fe_preview'] == 'true' ) {
						$fe_wrap_open = '<div class="bt_bb_fe_wrap"><span class="bt_bb_fe_count" data-edit_url="' . get_edit_post_link( get_the_ID(), '' ) . '" title="' . esc_html__( 'Find Section', 'bold-builder' ) . '"><span class="bt_bb_fe_count_inner">' . $count . '</span></span>';
					} else {
						$fe_wrap_open = '<div class="bt_bb_fe_wrap">';
						$fe_wrap_open .= '<span class="bt_bb_fe_count"><span class="bt_bb_fe_count_inner">' . $count . '</span>
						<ul class="bt_bb_element_menu">
							<li><span class="bt_bb_element_menu_edit">' . esc_html__( 'Edit', 'bold-builder' ) . '</span></li>
							<li data-edit_url="' . get_edit_post_link( get_the_ID(), '' ) . '"><span class="bt_bb_element_menu_edit_be">' . esc_html__( 'Edit in back-end editor', 'bold-builder' ) . '</span><ul><li><span class="bt_bb_element_menu_edit_be_new_tab">' . esc_html__( '(new tab)', 'bold-builder' ) . '</span></li></ul></li>
							<li><span class="bt_bb_element_menu_cut">' . esc_html__( 'Cut', 'bold-builder' ) . '</span></li>
							<li><span class="bt_bb_element_menu_copy">' . esc_html__( 'Copy', 'bold-builder' ) . '</span></li>
							<li><span class="bt_bb_element_menu_paste">' . esc_html__( 'Paste', 'bold-builder' ) . '</span><ul><li><span class="bt_bb_element_menu_paste_above">' . esc_html__( '(above)', 'bold-builder' ) . '</span></li></ul></li>
							<li class="bt_bb_element_menu_delete_parent"><span class="bt_bb_element_menu_delete">' . esc_html__( 'Delete', 'bold-builder' ) . '</span></li>
						</ul>
						</span>';
					}
					$fe_wrap_close = '</div>';
				} else {
					$fe_wrap_open = '<div class="bt_bb_fe_wrap"><span class="bt_bb_fe_count bt_bb_element_menu_edit_be_new_tab" title="' . esc_html__( 'Edit', 'bold-builder' ) . '"><span class="bt_bb_fe_count_inner" data-edit_url="' . get_edit_post_link( get_the_ID(), '' ) . '">' . $count . '</span></span>';
					$fe_wrap_close = '</div>';
				}
			//}
		}

		$depth = $item['depth'];

		if ( isset( $wrap_arr[ 'd' . ( $depth + 1 ) ] ) ) {
			if ( isset( $wrap_arr[ 'd' . $depth  ] ) ) {
				$wrap_arr[ 'd' . $depth  ] = $wrap_arr[ 'd' . $depth  ] . $fe_wrap_open . $item['sc'] . $wrap_arr[ 'd' . ( $depth + 1 ) ] . $item['sc_close'] . $fe_wrap_close;
			} else {
				$wrap_arr[ 'd' . $depth  ] = $fe_wrap_open . $item['sc'] . $wrap_arr[ 'd' . ( $depth + 1 ) ] . $item['sc_close'] . $fe_wrap_close;
			}
			unset( $wrap_arr[ 'd' . ( $depth + 1 ) ] );
		} else {
			if ( isset( $wrap_arr[ 'd' . $depth  ] ) ) {
				$wrap_arr[ 'd' . $depth  ] = $wrap_arr[ 'd' . $depth  ] . $fe_wrap_open . $item['sc'] . $item['sc_close'] . $fe_wrap_close;
			} else {
				$wrap_arr[ 'd' . $depth  ] = $fe_wrap_open . $item['sc'] . $item['sc_close'] . $fe_wrap_close;
			}
		}

	}

	if ( isset( $wrap_arr['d0'] ) && $wrap_arr['d0'] != '' ) {
		return $wrap_arr['d0'];
	} else {
		return $content;
	}
	
}

function bt_bb_fe_do_shortcode( $bt_bb_array ) {
	
	global $bt_bb_fe_array;
	global $bt_bb_fe_array_depth;
	
	$bt_bb_fe_array_depth++;
	
	foreach( $bt_bb_array as $item ) {
		
		$sc = '';
		
		if ( $item['base'] == '_content' ) {
			
			$sc = $item['content'];
			
			$sc_close = '';
			
		} else {
		
			$sc = '[';
			
			$sc .= $item['base'];
			
			if ( isset( $item['attr'] ) ) {
				$attr_obj = bt_bb_json_decode( $item['attr'] );
				foreach( $attr_obj as $key => $value ) {
					$sc .= ' ' . $key . '="' . $value . '"';
				}
			}
			
			$sc .= ']';
			
			if ( isset( $item['children'] ) ) {
				bt_bb_fe_do_shortcode( $item['children'] );
				$bt_bb_fe_array_depth--;
			}
			
			$sc_close = '[/' . $item['base'] . ']';
		
		}
		
		$bt_bb_fe_array[] = array( 
			'base' => $item['base'],
			'sc' => $sc,
			'sc_close' => $sc_close,
			'container' => count( $item['children'] ) > 0 ? 'yes' : 'no',
			'depth' => $bt_bb_fe_array_depth
		);
		
	}
}
	
class BT_BB_Root {
	public static $elements = array();
	public static $path;
	public static $init_wpautop;
	public static $font_arr = array();
	public static $fe_wrap_count;
	public static $deprecated = array();
	public static $has_footer;
	public static $footer_page_id;
}

BT_BB_Root::$path = plugin_dir_url( __FILE__ );
BT_BB_Root::$init_wpautop = null;

BT_BB_Root::$fe_wrap_count = 0;

BT_BB_Root::$has_footer = false;

add_action( 'wp_head', 'bt_bb_has_footer' );
function bt_bb_has_footer() {
	if ( function_exists( 'boldthemes_get_option' ) && function_exists( 'boldthemes_get_id_by_slug' ) && class_exists( 'BoldThemes_Customize_Default' ) && isset( BoldThemes_Customize_Default::$data['footer_page_slug'] ) ) {
		$page_slug = boldthemes_get_option( 'footer_page_slug' );
		if ( $page_slug != '' ) {
			$page_id = boldthemes_get_id_by_slug( $page_slug );
			if ( ! is_null( $page_id ) ) {
				BT_BB_Root::$has_footer = true;
				BT_BB_Root::$footer_page_id = $page_id;
			}
		}
	}
}
 
function bt_bb_enqueue() {
	
	$screen = get_current_screen();
	if ( $screen->base != 'post' && $screen->base != 'widgets' && $screen->base != 'nav-menus' && $screen->base != 'customize' ) {
		return;
	}
	
	$options = get_option( 'bt_bb_settings' );
	$post_types = get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ) );
	$active_pt = array();
	foreach( $post_types as $pt ) {
		if ( ! $options || ( ! array_key_exists( $pt, $options ) || ( array_key_exists( $pt, $options ) && $options[ $pt ] == '1' ) ) ) {
			$active_pt[] = $pt;
		}
	}
	$screens = $active_pt;
	if ( ! in_array( $screen->post_type, $screens ) && $screen->base == 'post' ) {
		return;
	}

	if ( function_exists( 'csscrush_file' ) ) {
		csscrush_file( plugin_dir_path( __FILE__ ) . 'css/style.css', array( 'source_map' => true, 'minify' => false, 'output_file' => 'style.crush', 'formatter' => 'block', 'boilerplate' => false, 'plugins' => array( 'ease' ) ) );
		csscrush_file( plugin_dir_path( __FILE__ ) . 'css/front_end/fe_dialog_content.css', array( 'source_map' => true, 'minify' => false, 'output_file' => 'fe_dialog_content.crush', 'formatter' => 'block', 'boilerplate' => false, 'plugins' => array( 'ease' ) ) );
	}

	wp_enqueue_style( 'bt_bb_font-awesome.min', plugins_url( 'css/font-awesome.min.css', __FILE__ ), array(), BT_BB_VERSION );
	wp_enqueue_style( 'bt_bb', plugins_url( 'css/style.crush.css', __FILE__ ), array(), BT_BB_VERSION );

	wp_enqueue_script( 'bt_bb_react', plugins_url( 'react.min.js', __FILE__ ), array(), BT_BB_VERSION );
	//wp_enqueue_script( 'bt_bb', plugins_url( 'script.min.js', __FILE__ ), array( 'jquery' ), BT_BB_VERSION );
	wp_enqueue_script( 'bt_bb_jsx', plugins_url( 'build/bundle.js', __FILE__ ), array( 'jquery' ), BT_BB_VERSION );
	wp_localize_script( 'bt_bb_jsx', 'bt_bb_ajax', array(
		'url' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'bt_bb_nonce' )
	));
	wp_enqueue_script( 'bt_bb_autosize', plugins_url( 'autosize.min.js', __FILE__ ), array(), BT_BB_VERSION );
	wp_enqueue_script( 'bt_bb_imagesloaded', plugins_url( 'imagesloaded.pkgd.min.js', __FILE__ ), array( 'jquery' ), BT_BB_VERSION );
	wp_enqueue_script( 'wp-color-picker' );
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_script( 'wp-color-picker-alpha', plugins_url( 'wp-color-picker-alpha.min.js', __FILE__ ), array( 'wp-color-picker' ), BT_BB_VERSION );
	
	wp_enqueue_script( 'bt_bb_ai', plugins_url( 'ai/ai.js', __FILE__ ), array( 'jquery' ), BT_BB_VERSION );
	
	wp_enqueue_script( 'bt_bb_yoast_compatibility', plugins_url( 'bt.bb.yoast.js', __FILE__ ), array(), BT_BB_VERSION, true );
	wp_localize_script( 'bt_bb_yoast_compatibility', 'bt_bb_ajax', array(
		'url' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'bt_bb_yoast_compatibility' )
	));

}
add_action( 'admin_enqueue_scripts', 'bt_bb_enqueue' );

function bt_bb_enqueue_fe_always() {
	
	if ( is_customize_preview() ) {
		return;
	}
	
	if ( current_user_can( 'edit_pages' ) ) {
		add_filter( 'user_can_richedit', function( $wp_rich_edit ) { return true; } ); // necessary in some cases even for super admins (tinyMCE JS error)
		if ( ! class_exists( 'BoldThemes_BB_Settings' ) || ! BoldThemes_BB_Settings::$custom_content_elements ) { // only with native BB elements
			wp_enqueue_media();
			wp_enqueue_editor();
			wp_enqueue_style( 'bt_bb_font-awesome.min', plugins_url( 'css/font-awesome.min.css', __FILE__ ), array(), BT_BB_VERSION );
		}
		
		$options = get_option( 'bt_bb_settings' );
		
		$post_types = get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ) );
		$active_pt = array();
		foreach( $post_types as $pt ) {
			if ( ! $options || ( ! array_key_exists( $pt, $options ) || ( array_key_exists( $pt, $options ) && $options[ $pt ] == '1' ) ) ) {
				$active_pt[] = $pt;
			}
		}
		$screens = $active_pt;
		if ( ! in_array( get_post_type(), $screens ) ) {
			return;
		}
		
		wp_enqueue_script( 'bt_bb_fe', plugins_url( 'build/bundle_fe.js', __FILE__ ), array( 'jquery' ), BT_BB_VERSION );
		wp_localize_script( 'bt_bb_fe', 'bt_bb_ajax', array(
			'url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'bt_bb_nonce' )
		));
		wp_localize_script( 'bt_bb_fe', 'bt_bb_fe_ajax', array( // back. compat.
			'url' => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'bt_bb_fe_nonce' )
		));
		
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script(
			'iris',
			admin_url( 'js/iris.min.js' ),
			array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' )
		);
		wp_enqueue_script(
			'wp-color-picker',
			admin_url( 'js/color-picker.min.js' ),
			array( 'iris', 'wp-i18n' )
		);
		$colorpicker_l10n = array(
			'clear' => __( 'Clear' ),
			'defaultString' => __( 'Default' ),
			'pick' => __( 'Select Color' ),
			'current' => __( 'Current Color' ),
		);
		wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n );
		wp_enqueue_script( 'wp-color-picker-alpha', plugins_url( 'wp-color-picker-alpha.min.js', __FILE__ ), array( 'wp-color-picker' ), BT_BB_VERSION );

		wp_enqueue_script( 'bt_bb_ai', plugins_url( 'ai/ai.js', __FILE__ ), array( 'jquery' ), BT_BB_VERSION );
	}
}

function bt_bb_wp_head() {
	?>
	<script>
		var bt_bb_update_res = function() {
			var width = Math.max( document.documentElement.clientWidth, window.innerWidth || 0 );
			window.bt_bb_res = 'xxl';
			if ( width <= 1400 ) window.bt_bb_res = 'xl';
			if ( width <= 1200 ) window.bt_bb_res = 'lg';
			if ( width <= 992) window.bt_bb_res = 'md';
			if ( width <= 768 ) window.bt_bb_res = 'sm';
			if ( width <= 480 ) window.bt_bb_res = 'xs';
			document.documentElement.setAttribute( 'data-bt_bb_screen_resolution', window.bt_bb_res ); // used in CSS
		}
		bt_bb_update_res();
		var bt_bb_observer = new MutationObserver(function( mutations ) {
			for ( var i = 0; i < mutations.length; i++ ) {
				var nodes = mutations[ i ].addedNodes;
				for ( var j = 0; j < nodes.length; j++ ) {
					var node = nodes[ j ];
					// Only process element nodes
					if ( 1 === node.nodeType ) {
						// Check if element or its children have override classes
						if ( ( node.hasAttribute && node.hasAttribute( 'data-bt-override-class' ) ) || ( node.querySelector && node.querySelector( '[data-bt-override-class]' ) ) ) {
							
							[ ...node.querySelectorAll( '[data-bt-override-class]' ),
							...( node.matches( '[data-bt-override-class]' ) ? [ node ] : [] ) ].forEach(function( element ) {
								// Get the attribute value
								let override_classes = JSON.parse( element.getAttribute( 'data-bt-override-class' ) );
								
								for ( let prefix in override_classes ) {
									let new_class;
									if ( override_classes[ prefix ][ window.bt_bb_res ] !== undefined ) {
										new_class = prefix + override_classes[ prefix ][ window.bt_bb_res ];
									} else {
										new_class = prefix + override_classes[ prefix ]['def'];
									}
									
									// Remove the current class
									element.classList.remove( ...override_classes[ prefix ]['current_class'].split( ' ' ) );
									
									// Add the new class
									element.classList.add( ...new_class.split( ' ' ) );
			
									// Update the current_class
									override_classes[ prefix ]['current_class'] = new_class;
								}
								
								// Store the updated data back to the attribute
								element.setAttribute( 'data-bt-override-class', JSON.stringify( override_classes ) );
							} );
							
						}
					}
				}
			}
		} );
		
		// Start observing
		bt_bb_observer.observe( document.documentElement, {
			childList: true,
			subtree: true
		} );
		
		// Cancel observer when ready
		var bt_bb_cancel_observer = function() {
			if ( 'interactive' === document.readyState || 'complete' === document.readyState ) {
				bt_bb_observer.disconnect();
				document.removeEventListener( 'readystatechange', bt_bb_cancel_observer );
			}
		};
		
		document.addEventListener( 'readystatechange', bt_bb_cancel_observer );
	</script>
	<?php
	
	$post_id = get_the_ID();
	$opt_arr = get_option( 'bt_bb_custom_css' );
	if ( isset( $opt_arr[ $post_id ] ) ) {
		echo '<style>' . stripslashes( wp_strip_all_tags( $opt_arr[ $post_id ] ) ) . '</style>';
	}
	if ( isset( $_GET['preview'] ) && $_GET['preview'] == 'true' ) {
		echo '<script>window.bt_bb_preview = true</script>';
	} else {
		echo '<script>window.bt_bb_preview = false</script>';
	}
	if ( ( isset( $_GET['bt_bb_fe_preview'] ) && $_GET['bt_bb_fe_preview'] == 'true' ) ) {
		echo '<script>window.bt_bb_fe_preview = true</script>';
	} else {
		echo '<script>window.bt_bb_fe_preview = false</script>';
	}
	if ( function_exists( 'bt_bb_add_color_schemes' ) ) {
		bt_bb_add_color_schemes();
	}
	if ( ! class_exists( 'BoldThemes_BB_Settings' ) || ! BoldThemes_BB_Settings::$custom_content_elements ) { // only with native BB elements
		echo '<script>window.bt_bb_custom_elements = false;</script>';
	} else {
		echo '<script>window.bt_bb_custom_elements = true;</script>';
	}
}
add_action( 'wp_head', 'bt_bb_wp_head', 200 );

function bt_bb_enqueue_fe() {
	if ( function_exists( 'csscrush_file' ) ) {
		csscrush_file( plugin_dir_path( __FILE__ ) . 'css/front_end/style.css', array( 'source_map' => true, 'minify' => false, 'output_file' => 'style.crush', 'formatter' => 'block', 'boilerplate' => false, 'plugins' => array( 'ease' ) ) );
	}
	wp_enqueue_style( 'bt_bb', plugins_url( 'css/front_end/style.crush.css', __FILE__ ), array(), BT_BB_VERSION );
}

function bt_bb_enqueue_content_elements() {
	if ( function_exists( 'csscrush_file' ) ) {
		csscrush_file( plugin_dir_path( __FILE__ ) . 'css/front_end/content_elements.css', array( 'source_map' => true, 'minify' => false, 'output_file' => 'content_elements.crush', 'formatter' => 'block', 'boilerplate' => false, 'plugins' => array( 'ease' ) ) );
	}
	wp_enqueue_style( 'bt_bb_content_elements', plugins_url( 'css/front_end/content_elements.crush.css', __FILE__ ), array(), BT_BB_VERSION );
	
	wp_enqueue_script( 
		'bt_bb_slick',
		plugins_url( 'slick/slick.min.js', __FILE__ ),
		array( 'jquery' ),
		BT_BB_VERSION
	);
	wp_enqueue_style( 'bt_bb_slick', plugins_url( 'slick/slick.css', __FILE__ ), array(), BT_BB_VERSION );

	wp_enqueue_script(
		'bt_bb_magnific',
		plugins_url( 'content_elements_misc/js/jquery.magnific-popup.min.js', __FILE__ ),
		array( 'jquery' ),
		BT_BB_VERSION
	);

	wp_enqueue_script( 'bt_bb', plugins_url( 'content_elements_misc/js/content_elements.js', __FILE__ ), array( 'jquery' ), BT_BB_VERSION );
	
	$post_id = get_the_ID();

}

function bt_bb_activate() {
	$options = get_option( 'bt_bb_settings' );
	if ( ! $options ) {
		update_option( 'bt_bb_settings', array( 'tag_as_name' => '0', 'color_schemes' => "Black/White;#000;#fff\r\nWhite/Black;#fff;#000" ) );
	}
}
register_activation_hook( __FILE__, 'bt_bb_activate' );

function bt_bb_deactivate() {
	//delete_option( 'bt_bb_settings' );
}
register_deactivation_hook( __FILE__, 'bt_bb_deactivate' );

function bt_bb_admin_init() {
    register_setting( 'bt_bb_settings', 'bt_bb_settings' );
}
add_action( 'admin_init', 'bt_bb_admin_init' );

function bt_bb_load_plugin_textdomain() {

	$domain = 'bold-builder';
	$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

}
add_action( 'init', 'bt_bb_load_plugin_textdomain' );

/**
 * Save custom css
 */

function bt_bb_save_custom_css() {
	check_ajax_referer( 'bt-bb-custom-css-nonce', 'bt-bb-custom-css-nonce' );
	$post_id = intval( $_POST['post_id'] );
	if ( current_user_can( 'edit_post', $post_id ) ) {
		$css = wp_strip_all_tags( $_POST['css'] );
		$opt_arr = get_option( 'bt_bb_custom_css' );
		if ( ! is_array( $opt_arr ) ) {
			$opt_arr = array();
		}
		if ( $css != '' ) {
			$opt_arr[ $post_id ] = $css;
		} else {
			unset( $opt_arr[ $post_id ] );
		}
		update_option( 'bt_bb_custom_css', $opt_arr );
		echo 'ok';
	}
	wp_die();
}
add_action( 'wp_ajax_bt_bb_save_custom_css', 'bt_bb_save_custom_css' );

/**
 * Get custom css
 */
 
function bt_bb_get_custom_css() {
	$post_id = intval( $_POST['post_id'] );
	if ( current_user_can( 'edit_post', $post_id ) ) {
		$opt_arr = get_option( 'bt_bb_custom_css' );
		if ( isset( $opt_arr[ $post_id ] ) ) {
			echo stripslashes( wp_strip_all_tags( $opt_arr[ $post_id ] ) );
		}
	}
	die();
}
add_action( 'wp_ajax_bt_bb_get_custom_css', 'bt_bb_get_custom_css' );

/**
 * Get links
 */
 
function bt_bb_search_links() {
	$search = sanitize_text_field( $_POST['search'] );
	$page = intval( $_POST['page'] );

    $pts = get_post_types( array( 'public' => true ), 'objects' );
    $pt_names = array_keys( $pts );
 
    $query = array(
        'post_type'              => $pt_names,
        'suppress_filters'       => true,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
        'post_status'            => 'publish',
        'posts_per_page'         => 20,
    );
	
	if ( $search != '' ) {
		$query['s'] = $search;
	}
	
	$query['offset'] = $page > 1 ? $query['posts_per_page'] * ( $page - 1 ) : 0;
	
    // Do main query.
    $get_posts = new WP_Query;
    $posts = $get_posts->query( $query );
 
    // Build results.
    $results = array();
    foreach ( $posts as $post ) {
        if ( 'post' === $post->post_type ) {
            $info = mysql2date( __( 'Y/m/d' ), $post->post_date );
        } else {
            $info = $pts[ $post->post_type ]->labels->singular_name;
        }
 
        $results[] = array(
            'ID'        => $post->ID,
            'title'     => trim( esc_html( strip_tags( get_the_title( $post ) ) ) ),
            'permalink' => get_permalink( $post->ID ),
			'slug'      => $post->post_name,
            'info'      => $info,
        );
    }
	
    if ( ! isset( $results ) ) {
        wp_die( 0 );
    }
 
    echo wp_json_encode( $results );
    echo "\n";
 
    die();
}
add_action( 'wp_ajax_bt_bb_search_links', 'bt_bb_search_links' );

/**
 * Get page HTML, used in Yoast compatibility plugin
 */
function bt_bb_get_html() {
	check_ajax_referer( 'bt_bb_yoast_compatibility', 'nonce' );
	$post_id = intval( $_POST['post_id'] );
	$content = stripslashes( wp_kses_post( $_POST['content'] ) );
	if ( current_user_can( 'edit_post', $post_id ) ) {
		remove_filter( 'the_content', 'wpautop' );
		$html = apply_filters( 'the_content', $content );
		/**/
		$html = str_ireplace( array( '``', '`{`', '`}`' ), array( '&quot;', '&#91;', '&#93;' ), $html );
		$html = str_ireplace( array( '*`*`*', '*`*{*`*', '*`*}*`*' ), array( '``', '`{`', '`}`' ), $html );
		
		echo $html;
	}
	wp_die();
}
add_action( 'wp_ajax_bt_bb_get_html', 'bt_bb_get_html' );

/**
 * Settings menu
 */

function bt_bb_menu() {
	add_options_page( esc_html__( 'Bold Builder Settings', 'bold-builder' ), esc_html__( 'Bold Builder', 'bold-builder' ), 'manage_options', 'bt_bb_settings', 'bt_bb_settings' );
}
add_action( 'admin_menu', 'bt_bb_menu' );

/**
 * Settings page
 */
function bt_bb_settings() {
	
	$options = get_option( 'bt_bb_settings' );
	if ( is_array( $options ) ) {
		$tag_as_name = isset( $options['tag_as_name'] ) ? $options['tag_as_name'] : '0';
		$color_schemes = isset( $options['color_schemes'] ) ? $options['color_schemes'] : '';
		$openai_api_key = isset( $options['openai_api_key'] ) ? $options['openai_api_key'] : '';
		$openai_model = isset( $options['openai_model'] ) ? $options['openai_model'] : '';
	}
	
	$post_types = get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ) );

	?>
		<div class="wrap">
			<h2><?php _e( 'Bold Builder Settings', 'bold-builder' ); ?></h2>
			<p class="description bt_bb_wrap_documentation dashicons-editor-help dashicons-before"><?php printf(
				   _x( 'For additional help check out <a href="%1$s" title="%2$s" target="_blank">Online Documentation</a>.', 'bold-builder' ), 'https://documentation.bold-themes.com/bold-builder', _x( 'Bold Builder documentation', 'bold-builder' )
			 ); ?>
			</p>
			<form method="post" action="options.php">
				<?php settings_fields( 'bt_bb_settings' ); ?>
				<table class="form-table">
					<tbody>
					<tr>
						<th scope="row"><?php _e( 'Show shortcode tag instead of mapped name', 'bold-builder' ); ?></th>
						<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Show shortcode tags instead of mapped names', 'bold-builder' ); ?></span></legend>
						<p><label><input name="bt_bb_settings[tag_as_name]" type="radio" value="0" <?php echo $tag_as_name != '1' ? 'checked="checked"' : ''; ?>> <?php _e( 'No', 'bold-builder' ); ?></label><br>
						<label><input name="bt_bb_settings[tag_as_name]" type="radio" value="1" <?php echo $tag_as_name == '1' ? 'checked="checked"' : ''; ?>> <?php _e( 'Yes', 'bold-builder' ); ?></label></p>
						</fieldset></td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Post types', 'bold-builder' ); ?></th>
						<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Post Types', 'bold-builder' ); ?></span></legend>
						<p>
						<?php 
						$n = 0;
						foreach( $post_types as $pt ) {
							$n++;
							$checked = '';
							if ( ! $options || ( ! array_key_exists( $pt, $options ) || ( array_key_exists( $pt, $options ) && $options[ $pt ] == '1' ) ) ) {
								$checked = ' ' . 'checked="checked"';
							}
							echo '<input type="hidden" name="bt_bb_settings[' . $pt . ']" value="0">';
							echo '<label><input name="bt_bb_settings[' . $pt . ']" type="checkbox" value="1"' . $checked . '> ' . $pt . '</label>';
							if ( $n < count( $post_types ) ) echo '<br>';
						} ?>
						</p>
						</fieldset></td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Color schemes', 'bold-builder' ); ?></th>
						<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'Color Schemes', 'bold-builder' ); ?></span></legend>
						<p>
						<textarea name="bt_bb_settings[color_schemes]" rows="10" cols="50" placeholder="Black/White;#000;#fff"><?php echo sanitize_textarea_field( $color_schemes ); ?></textarea>
						</p>
						<small>Add each Color Scheme separated by new line. E.g. Black/White;#000;#fff</small>
						</fieldset></td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Tips', 'bold-builder' ); ?></th>
						<td>
						<p><?php
						$checked = '';
						if ( ! $options || ( ! array_key_exists( 'tips', $options ) || ( array_key_exists( 'tips', $options ) && $options['tips'] == '1' ) ) ) {
							$checked = ' ' . 'checked="checked"';
						}
						echo '<input type="hidden" name="bt_bb_settings[tips]" value="0">';
						echo '<label><input name="bt_bb_settings[tips]" type="checkbox" value="1"' . $checked . '> ' . esc_html__( 'Show Tips', 'bold-builder' ) . '</label>';
						?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Slug in URL', 'bold-builder' ); ?></th>
						<td>
						<p><?php
						$checked = '';
						if ( $options && array_key_exists( 'slug_url', $options ) && $options['slug_url'] == '1' ) {
							$checked = ' ' . 'checked="checked"';
						}
						echo '<input type="hidden" name="bt_bb_settings[slug_url]" value="0">';
						echo '<label><input name="bt_bb_settings[slug_url]" type="checkbox" value="1"' . $checked . '> ' . esc_html__( 'Use post slug in URL input (when selecting existing content search result)', 'bold-builder' ) . '</label>';
						?></p>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'OpenAI API key', 'bold-builder' ); ?></th>
						<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'OpenAI API key', 'bold-builder' ); ?></span></legend>
						<p>
						<input type="text" name="bt_bb_settings[openai_api_key]" value="<?php echo esc_attr( $openai_api_key ); ?>">
						</p>
						</fieldset></td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'OpenAI Model', 'bold-builder' ); ?></th>
						<td><fieldset><legend class="screen-reader-text"><span><?php _e( 'OpenAI Model', 'bold-builder' ); ?></span></legend>
						<p>
						<input type="text" name="bt_bb_settings[openai_model]" value="<?php echo esc_attr( $openai_model ); ?>" placeholder="gpt-4">
						</p>
						</fieldset></td>
					</tr>
					</tbody>
				</table>

				<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save', 'bold-builder' ); ?>"></p>
			</form>
		</div>
	<?php

}

function bt_bb_customize_script() {
	echo '<script>';
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
		echo 'window.bt_bb_icons = JSON.parse(\'' . bt_bb_json_encode( $icon_arr ) . '\')';
	echo '</script>';
}
add_action( 'customize_controls_print_scripts', 'bt_bb_customize_script' );

/**
 * Settings
 */

function bt_bb_js_settings() {
	
	$screen = get_current_screen();
	if ( $screen->base != 'post' && $screen->base != 'widgets' && $screen->base != 'nav-menus' ) {
        return;
    }
	echo '<script>';
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
		echo 'window.bt_bb_icons = JSON.parse(\'' . bt_bb_json_encode( $icon_arr ) . '\')';
	echo '</script>';
	
	$options = get_option( 'bt_bb_settings' );
	
	$post_types = get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ) );
	$active_pt = array();
	foreach( $post_types as $pt ) {
		if ( ! $options || ( ! array_key_exists( $pt, $options ) || ( array_key_exists( $pt, $options ) && $options[ $pt ] == '1' ) ) ) {
			$active_pt[] = $pt;
		}
	}
	$screens = $active_pt;
	if ( ! in_array( $screen->post_type, $screens ) && $screen->base == 'post' ) {
		return;
	}
	
	$tag_as_name = $options['tag_as_name'];
	$slug_url = array_key_exists( 'slug_url', $options ) ? $options['slug_url'] : '';
	
	echo '<script>';
		echo 'window.bt_bb_plugins_url = "' . plugins_url() . '";';
		echo 'window.bt_bb_loading_gif_url = "' . plugins_url( 'img/ajax-loader.gif', __FILE__ ) . '";';
		echo 'window.bt_bb_settings = [];';
		echo 'window.bt_bb_settings.tag_as_name = "' . esc_js( $tag_as_name ) . '";';
		echo 'window.bt_bb_settings.slug_url = "' . esc_js( $slug_url ) . '";';
		
		echo 'window.bt_bb_ajax_url = "' . esc_js( admin_url( 'admin-ajax.php' ) ) . '";'; // back. compat.
		
		echo 'window.bt_bb_ajax_nonce = "' . wp_create_nonce( 'bt_bb_nonce' ) . '";'; // fix nonce issue on local sites with ai.js (not working with wp_localize_script window.bt_bb_ajax.nonce)
		
		global $shortcode_tags;
		$all_sc = $shortcode_tags;
		ksort( $all_sc );
		
		echo 'window.bt_bb.all_sc = ' . bt_bb_json_encode( array_keys( $all_sc ) ) . ';';

		global $bt_bb_is_bb_content;
		if ( $bt_bb_is_bb_content === 'true' || $bt_bb_is_bb_content === 'false' ) {
			echo 'window.bt_bb.is_bb_content = ' . $bt_bb_is_bb_content . ';';
		}
		$ajax_nonce = wp_create_nonce( 'bt-bb-custom-css-nonce' );
		?>
			jQuery( '#bt_bb_dialog' ).on( 'click', '.bt_bb_button_save_custom_css', function( e ) {

				if ( wp.codeEditor !== undefined ) {
					window.bt_bb_ce.codemirror.save();
				}				

				var css = jQuery( '.bt_bb_custom_css_content' ).val();
				
				window.bt_bb_custom_css = window.bt_bb_b64EncodeUnicode( css );

				window.bt_bb_dialog.hide();
				
				if ( css != '' ) {
					jQuery( '.bt_bb_custom_css' ).addClass( 'button-primary' );
				} else {
					jQuery( '.bt_bb_custom_css' ).removeClass( 'button-primary' );
				}

				var data = {
					'action': 'bt_bb_save_custom_css',
					'post_id': jQuery( '#post_ID' ).val(),
					'css': css,
					'bt-bb-custom-css-nonce': '<?php echo $ajax_nonce; ?>'
				};				

				jQuery.ajax({
					type: 'POST',
					url: window.bt_bb_ajax_url,
					data: data,
					async: true,
					success: function( response ) {
						
					}
				});
			});
		<?php
		
	echo '</script>';
}
add_action( 'admin_footer', 'bt_bb_js_settings' );

/**
 * Translate
 */

function bt_bb_translate() {
	echo '<script>';
	echo 'window.bt_bb_text = [];';
	echo 'window.bt_bb_text.toggle = "' . esc_html__( 'Toggle', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.add = "' . esc_html__( 'Add', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.edit = "' . esc_html__( 'Edit', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.edit_content = "' . esc_html__( 'Edit Content', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.content = "' . esc_html__( 'Content', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.select_parent = "' . esc_html__( 'Select parent', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.clone = "' . esc_html__( 'Clone', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.delete = "' . esc_html__( 'Delete', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.delete_section_confirm = "' . esc_html__( 'Do you really want to delete this Section?', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.layout_error = "' . esc_html__( 'Layout error!', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.number_columns = "' . esc_html__( 'Number of columns not equal for all breakpoints!', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.add_element = "' . esc_html__( 'Add Element', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.add_elements_dd = "' . esc_html__( 'Add elements (drag & drop)', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.select_layout = "' . esc_html__( 'Select Layout', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.edit_layout = "' . esc_html__( 'Edit Layout', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.select = "' . esc_html__( 'Select', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.select_images = "' . esc_html__( 'Select Images', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.submit = "' . esc_html__( 'Submit', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.search = "' . esc_html__( 'Search', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.search_content = "' . esc_html__( 'Search for existing content', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.copy = "' . esc_html__( 'Copy', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.copy_plus = "' . esc_html__( 'Copy+', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.paste = "' . esc_html__( 'Paste', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.export = "' . esc_html__( 'Export', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.import = "' . esc_html__( 'Import', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.not_allowed = "' . esc_html__( 'Not allowed!', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.manage_cb = "' . esc_html__( 'Manage Clipboard', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.filter = "' . esc_html__( 'Filter...', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.sc_mapper = "' . esc_html__( 'Shortcode Mapper', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.insert_mapping = "' . esc_html__( 'Insert Mapping', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.save = "' . esc_html__( 'Save', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.switch_editor = "' . esc_html__( 'Switch Editor', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.custom_css = "' . esc_html__( 'Custom CSS', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.switch_editor_confirm = "' . esc_html__( 'Are you sure you want to switch editor?', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.general = "' . esc_html__( 'General', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.preview = "' . esc_html__( 'Preview', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.no_results = "' . esc_html__( 'No results found.', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.edit_be = "' . esc_html__( 'Edit in BE editor', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.parent = "' . esc_html__( 'parent', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.or_enter_image_url = "' . esc_html__( 'Or enter image URL...', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.save_content_first = "' . esc_html__( 'Content not saved!', 'bold-builder' ) . '";';
	
	echo 'window.bt_bb_text.prev_tip = "' . esc_html__( 'Previous Tip', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.next_tip = "' . esc_html__( 'Next Tip', 'bold-builder' ) . '";';
	
	echo 'window.bt_bb_text._tips = [];';
	
	//if ( ! class_exists( 'BoldThemes_BB_Settings' ) || ! BoldThemes_BB_Settings::$custom_content_elements ) { // only with native BB elements
	echo 'window.bt_bb_text._tips.push( "' . esc_html__( 'Bold Builder tip: Hold CTRL key while moving (dragging) element to quickly clone it to another location.', 'bold-builder' ) . '" );';
	echo 'window.bt_bb_text._tips.push( "' . esc_html__( 'Bold Builder tip: You can edit Bold Builder settings in Settings > Bold Builder.', 'bold-builder' ) . '" );';
	echo 'window.bt_bb_text._tips.push( "' . esc_html__( 'Bold Builder tip: Did you know that you can move columns between rows?', 'bold-builder' ) . '" );';
	echo 'window.bt_bb_text._tips.push( "' . esc_html__( 'Bold Builder tip: You can use CTRL key with Copy/Copy+ to cut the element.', 'bold-builder' ) . '" );';
	echo 'window.bt_bb_text._tips.push( "' . esc_html__( 'Bold Builder tip: Use Copy+ to copy multiple elements to clipboard.', 'bold-builder' ) . '" );';
	echo 'window.bt_bb_text._tips.push( "' . esc_html__( 'Bold Builder tip: Hold CTRL key and click Paste on content element toolbar to paste before the element.', 'bold-builder' ) . '" );';
	
	// AI
	
	echo 'window.bt_bb_text.ai = "' . esc_html__( 'AI', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.ai_content_generator = "' . esc_html__( 'AI Content Generator', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.generate = "' . esc_html__( 'Generate', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.translate = "' . esc_html__( 'Translate', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.rephrase = "' . esc_html__( 'Rephrase', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.correct = "' . esc_html__( 'Correct', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.go = "' . esc_html__( 'Go', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.keywords = "' . esc_html__( 'Keywords', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.system_prompt = "' . esc_html__( 'System prompt', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.temperature = "' . esc_html__( 'Randomness (temperature)', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.desired_length = "' . esc_html__( 'Desired length (in words)', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.language = "' . esc_html__( 'Language', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.english = "' . esc_html__( 'English', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.tone = "' . esc_html__( 'Tone', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text._tone = {};';
	echo 'window.bt_bb_text._tone.default = "' . esc_html__( 'Default / Same', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text._tone.bold = "' . esc_html__( 'Bold', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text._tone.conversational = "' . esc_html__( 'Conversational', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text._tone.passionate = "' . esc_html__( 'Passionate', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text._tone.professional = "' . esc_html__( 'Professional', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text._tone.witty = "' . esc_html__( 'Witty', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.help = "' . esc_html__( 'Help', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.leave_empty = "' . esc_html__( '(leave empty to use length of current content)', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.ai_error = "' . sprintf(
		esc_html__( 'Error. Please check OpenAI settings %1$shere%2$s.', 'bold-builder' ),
		'<a href=\"' . esc_url_raw( get_admin_url( null, 'options-general.php?page=bt_bb_settings' ) ) . '\" target=\"_blank\">',
		'</a>' 
	) . '";';
	echo 'window.bt_bb_text.no_content = "' . esc_html__( 'No content!', 'bold-builder' ) . '";';
	
	echo 'window.bt_bb_text.dd = {};';
	echo 'window.bt_bb_text.dd.move = "' . esc_html__( 'Move', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.dd.copy = "' . esc_html__( 'Copy', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.dd.before = "' . esc_html__( 'before', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.dd.after = "' . esc_html__( 'after', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.dd.to = "' . esc_html__( 'to', 'bold-builder' ) . '";';
	
	echo 'window.bt_bb_text.documentation = "' . esc_html__( 'Need help?', 'bold-builder' ) . '";';
	echo 'window.bt_bb_text.read_documentation = "' . esc_html__( 'Read Documentation', 'bold-builder' ) . '";';
	
	echo 'window.bt_bb_text.responsive_override = "' . esc_html__( 'Responsive Override', 'bold-builder' ) . '";';
	
	echo '</script>';
}
add_action( 'admin_footer', 'bt_bb_translate', 20 );
add_action( 'customize_controls_head', 'bt_bb_translate' );
 
/**
 * Map shortcodes
 */
 
global $bt_bb_map;
$bt_bb_map = array();

function bt_bb_map_js() {
	
	if ( is_admin() ) { // back end
		$screen = get_current_screen();
		if ( ! is_object( $screen ) || $screen->base != 'post' ) {
			return;
		}
		
		$options = get_option( 'bt_bb_settings' );
		$post_types = get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ) );
		$active_pt = array();
		foreach( $post_types as $pt ) {
			if ( ! $options || ( ! array_key_exists( $pt, $options ) || ( array_key_exists( $pt, $options ) && $options[ $pt ] == '1' ) ) ) {
				$active_pt[] = $pt;
			}
		}
		$screens = $active_pt;
		if ( ! in_array( $screen->post_type, $screens ) && $screen->base == 'post' ) {
			return;
		}		
	}
	
	global $bt_bb_map;
	echo '<script>';
		BT_BB_Root::$elements = apply_filters( 'bt_bb_elements', BT_BB_Root::$elements );
		foreach( BT_BB_Root::$elements as $base => $params ) {
			$proxy = new BT_BB_Map_Proxy( $base, $params );
			$proxy->js_map();
		}
	echo '</script>';
	echo '<script>';
		$opt_arr = get_option( 'bt_bb_mapping_secondary' );
		if ( is_array( $opt_arr ) ) {
			foreach( $opt_arr as $k => $v ) {
				if ( shortcode_exists( $k ) ) {
					echo 'window.bt_bb_map["' . $k . '"] = ' . stripslashes( $v ) . ';';
					$bt_bb_map[ $k ] = json_decode( stripslashes( $v ), true );
					echo 'window.bt_bb_map_secondary["' . $k . '"] = true;';
				}
			}
		}
	echo '</script>';
}
add_action( 'admin_head', 'bt_bb_map_js' );

function bt_bb_fe_action() {
	if ( ! bt_bb_active_for_post_type_fe() || ( isset( $_GET['preview'] ) && ! isset( $_GET['bt_bb_fe_preview'] ) ) ) {
		return;
	}
	add_action( 'wp_enqueue_scripts', 'bt_bb_enqueue_fe' );
	add_filter( 'the_content', 'bt_bb_parse_content_admin' );
}
add_action( 'admin_bar_init', 'bt_bb_fe_action' );

if ( ! class_exists( 'BoldThemes_BB_Settings' ) || ! BoldThemes_BB_Settings::$custom_content_elements ) {
	add_action( 'wp_enqueue_scripts', 'bt_bb_enqueue_content_elements' );
}

add_action( 'wp_enqueue_scripts', 'bt_bb_enqueue_fe_always', 50 );

/**
 * Map shortcode elements
 */
//function bt_bb_map( $base, $params, $priority = 10 ) {
function bt_bb_map( $base, $params ) {
	$i = 0;
	if ( isset( $params['params'] ) ) {
		foreach( $params['params'] as $param ) {
			if ( ! isset( $param['weight'] ) ) {
				$params['params'][ $i ]['weight'] = $i;
			}
			$i++;
		}
	}
	BT_BB_Root::$elements[ $base ] = $params;
}

add_action( 'plugins_loaded', 'bt_rc_map_plugins_loaded', 5 );
function bt_rc_map_plugins_loaded() {
	if ( ! function_exists( 'bt_rc_map' ) ) {
		function bt_rc_map( $base, $params ) {
			bt_bb_map( $base, $params );
		}
	}
}

/* Deactivate RC */
register_activation_hook( __FILE__, 'bt_deactivate_rc' );
function bt_deactivate_rc () {
	deactivate_plugins( 'rapid_composer/rapid_composer.php' );
}

/**
 * Modify map
 */
function bt_bb_map_modify( $base, $params ) {
	$i = 0;
	if ( isset( $params['params'] ) ) {
		foreach( $params['params'] as $param ) {
			if ( ! isset( $param['weight'] ) ) {
				$params['params'][ $i ]['weight'] = $i;
			}
			$i++;
		}
	}
	foreach( $params as $key => $value ) {
		BT_BB_Root::$elements[ $base ][ $key ] = $value;
	}
}

/**
 * Add element param(s)
 */
function bt_bb_add_params( $base, $params ) {
	$proxy = new BT_BB_Add_Params_Proxy( $base, $params );
	add_filter( 'bt_bb_elements', array( $proxy, 'add_params' ) );
}

/**
 * Remove element param(s)
 */
function bt_bb_remove_params( $base, $params ) {
	$proxy = new BT_BB_Remove_Params_Proxy( $base, $params );
	add_filter( 'bt_bb_elements', array( $proxy, 'remove_params' ) );
}

global $bt_bb_root_base;
$bt_bb_root_base = array();

class BT_BB_Map_Proxy {
	public $base;
	public $params;	
	function __construct( $base, $params ) {
		$this->base = $base;
		$params['base'] = $base;
		$this->params = $params;
	}

	public function js_map() {
		global $bt_bb_map;
		if ( shortcode_exists( $this->base ) ) {
			if ( isset( $this->params['admin_enqueue_css'] ) ) {
				foreach( $this->params['admin_enqueue_css'] as $item ) {
					wp_enqueue_style( 'bt_bb_admin_' . uniqid(), $item );
				}
			}
			echo 'window.bt_bb_map["' . $this->base . '"] = window.bt_bb_map_primary.' . $this->base . ' = ' . bt_bb_json_encode( $this->params ) . ';';
			$bt_bb_map[ $this->base ] = $this->params;
		}
	}
}

class BT_BB_FE_Map_Proxy {
	public $base;
	public $params;
	function __construct( $base, $params ) {
		$this->base = $base;
		$params['base'] = $base;
		$this->params = $params;

		global $bt_bb_root_base;
		if ( isset( $params['root'] ) && $params['root'] === true && isset( $params['base'] ) ) {
			$bt_bb_root_base[] = $params['base'];
		}
	}

	public function js_map() {
		global $bt_bb_map;
		if ( shortcode_exists( $this->base ) ) {
			$bt_bb_map[ $this->base ] = $this->params;
		}
	}
}

class BT_BB_Add_Params_Proxy {
	public $base;
	public $params;
	function __construct( $base, $params ) {
		$this->base = $base;
		$this->params = $params;
	}
	public function add_params( $elements ) {
		if ( isset( $elements[ $this->base ] ) ) {
			foreach( $this->params as $param ) {
				$last = end( $elements[ $this->base ]['params'] );
				if ( ! is_array( $param ) ) {
					if ( ! isset( $this->params['weight'] ) ) {
						if ( isset( $last['weight'] ) ) {
							$this->params['weight'] = $last['weight'] + 1;
						} else {
							$this->params['weight'] = 0;
						}
					}
					$elements[ $this->base ]['params'][] = $this->params;
					break;
				} else {
					if ( ! isset( $param['weight'] ) ) {
						if ( isset( $last['weight'] ) ) {
							$param['weight'] = $last['weight'] + 1;
						} else {
							$param['weight'] = 0;
						}
					}
					$elements[ $this->base ]['params'][] = $param;
				}
			}
			usort( $elements[ $this->base ]['params'], array( $this, 'sort_by_weight' ) );
		}
		
		return $elements;
	}
	private function sort_by_weight( $a, $b ) {
		if ( $a['weight'] == $b['weight'] ) {
			return 0;
		}
		return ( $a['weight'] < $b['weight'] ) ? -1 : 1;
	}
}

class BT_BB_Remove_Params_Proxy {
	public $base;
	public $params;	
	function __construct( $base, $params ) {
		$this->base = $base;
		$this->params = $params;
	}
	public function remove_params( $elements ) {
		if ( is_array( $this->params ) ) {
			foreach( $this->params as $param ) {
				$i = 0;
				foreach( $elements[ $this->base ]['params'] as $element_param ) {
					if ( $element_param['param_name'] == $param ) {
						unset( $elements[ $this->base ]['params'][ $i ] );
					}
					$i++;
				}
				$elements[ $this->base ]['params'] = array_values( $elements[ $this->base ]['params'] );
			}
		} else {
			$i = 0;
			foreach( $elements[ $this->base ]['params'] as $element_param ) {
				if ( $element_param['param_name'] == $this->params ) {
					unset( $elements[ $this->base ]['params'][ $i ] );
				}
				$i++;
			}
			$elements[ $this->base ]['params'] = array_values( $elements[ $this->base ]['params'] );
		}
		return $elements;
	}
}

/**
 * Remove wpautop
 */
function bt_bb_wpautop( $content ) {
	
	if ( ! ( is_singular() && in_the_loop() && is_main_query() ) ) {
		return $content;
	}
	
	global $wp_query;
	global $post;
	if ( $post && $wp_query && $wp_query->queried_object && $post->ID != $wp_query->queried_object->ID ) {
		return $content;
	}	
	
	if ( BT_BB_Root::$init_wpautop === null ) {
		BT_BB_Root::$init_wpautop = has_filter( 'the_content', 'wpautop' );
	}
	if ( ! bt_bb_active_for_post_type_fe() ) {
		return $content;
	}
	if ( strpos( trim( $content ), '[bt_' ) === 0 ) {
		remove_filter( 'the_content', 'wpautop' );
	} else if ( BT_BB_Root::$init_wpautop !== false ) {
		add_filter( 'the_content', 'wpautop' );
	}
	return $content;
}
add_filter( 'the_content', 'bt_bb_wpautop', 1 );

// force visual editor
add_action( 'current_screen', 'bt_bb_current_screen' );
function bt_bb_current_screen( $current_screen ) {
	$options = get_option( 'bt_bb_settings' );
	$post_types = get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ) );
	$active_pt = array();
	foreach( $post_types as $pt ) {
		if ( ! $options || ( ! array_key_exists( $pt, $options ) || ( array_key_exists( $pt, $options ) && $options[ $pt ] == '1' ) ) ) {
			$active_pt[] = $pt;
		}
	}
	$screens = $active_pt;
	if ( in_array( $current_screen->post_type, $screens ) ) {
		add_filter( 'user_can_richedit' , '__return_true', 50 );
		if ( ! class_exists( 'BoldThemes_BB_Settings' ) || ! BoldThemes_BB_Settings::$custom_content_elements ) {
			add_action( 'admin_footer', function() {
					echo '<script>window.bt_bb_custom_elements = false;</script>';
					if ( isset( BT_BB_Root::$elements['bt_bb_column']['responsive_override'] ) && BT_BB_Root::$elements['bt_bb_column']['responsive_override'] && isset( BT_BB_Root::$elements['bt_bb_column_inner']['responsive_override'] ) && BT_BB_Root::$elements['bt_bb_column_inner']['responsive_override'] ) {
						echo '<script>window.bt_bb_responsive_override_layout = true;</script>';
					} else {
						echo '<script>window.bt_bb_responsive_override_layout = false;</script>';
					}
				}
			);
		} else {
			add_action( 'admin_footer', function() { echo '<script>window.bt_bb_custom_elements = true;</script>'; } );
		}
	}
}

add_filter( 'preview_post_link', 'bt_bb_preview_post_link' );
function bt_bb_preview_post_link( $preview_link ) {
	if ( isset( $_POST['bt_bb_fe_preview'] ) && $_POST['bt_bb_fe_preview'] == 'true' ) {
		return $preview_link . '&bt_bb_fe_preview=true';
	} else {
		return $preview_link;
	}
}

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function bt_bb_add_meta_box() {
	$options = get_option( 'bt_bb_settings' );
	$post_types = get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ) );
	$active_pt = array();
	foreach( $post_types as $pt ) {
		if ( ! $options || ( ! array_key_exists( $pt, $options ) || ( array_key_exists( $pt, $options ) && $options[ $pt ] == '1' ) ) ) {
			$active_pt[] = $pt;
		}
	}

	$screens = $active_pt;

	foreach( $screens as $screen ) {
		add_meta_box(
			'bt_bb_sectionid',
			esc_html__( 'Bold Builder', 'bold-builder' ),
			'bt_bb_meta_box_callback',
			$screen,
			'normal',
			'high',
			array(
				'__back_compat_meta_box' => true,
				/*'__block_editor_compatible_meta_box' => true,*/
				/*'__block_editor_compatible_meta_box' => false,*/
			)
		);
	}
}
add_action( 'add_meta_boxes', 'bt_bb_add_meta_box' );

/**
 * Get size information for all currently-registered image sizes.
 *
 * @global $_wp_additional_image_sizes
 * @uses   get_intermediate_image_sizes()
 * @return array $sizes Data for all currently-registered image sizes.
 */
function bt_bb_get_image_sizes() {
	global $_wp_additional_image_sizes;

	foreach ( get_intermediate_image_sizes() as $_size ) {
		$sizes[ $_size ] = $_size;
		/*if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {
			$sizes[ $_size ]['width']  = get_option( "{$_size}_size_w" );
			$sizes[ $_size ]['height'] = get_option( "{$_size}_size_h" );
			$sizes[ $_size ]['crop']   = (bool) get_option( "{$_size}_crop" );
		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
			$sizes[ $_size ] = array(
				'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
				'height' => $_wp_additional_image_sizes[ $_size ]['height'],
				'crop'   => $_wp_additional_image_sizes[ $_size ]['crop'],
			);
		}*/
	}

	$sizes[ 'full' ] = 'full';

	return array_reverse( $sizes );
}

/**
 * Initial data.
 */
class BT_BB_Data_Proxy {
	public $data;
	function __construct( $data ) {
		$this->data = $data;
	}

	public function js() {
		echo '<script>
			var bt_bb_data = {	
				title: "_root",
				base: "_root",
				key: "' . uniqid( 'bt_bb_' ) . '",
				children: ' . $this->data . '
			}
		</script>';
	}
}

function bt_bb_force_tinymce() {
    return 'tinymce';
}
add_filter( 'wp_default_editor', 'bt_bb_force_tinymce' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function bt_bb_meta_box_callback( $post ) {

	// Add an nonce field so we can check for it later.
	wp_nonce_field( 'bt_bb_meta_box', 'bt_bb_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$value = get_post_meta( $post->ID, '_my_meta_value_key', true );

	$content = $post->post_content;
	
	if ( function_exists( 'mb_convert_encoding' ) ) {
		$content = mb_convert_encoding( $content, 'UTF-8' );
	}
	
	global $bt_bb_array;
	$bt_bb_array = array();

	bt_bb_do_shortcode( $content );

	$json_content = bt_bb_json_encode( $bt_bb_array );
	
	$screen = get_current_screen();
	
	global $bt_bb_is_bb_content;
	if ( $json_content == '[]' ) {
		$bt_bb_is_bb_content = 'false';
	} else {
		$bt_bb_is_bb_content = 'true';
	}
	
	if ( $screen->post_type == 'page' && $screen->action == 'add' ) {
		$bt_bb_is_bb_content = 'true';
	}
	
	if ( function_exists( 'wp_enqueue_code_editor' ) ) {
		wp_enqueue_code_editor( array( 'type' => 'text/css' ) );
	}	
	
	echo '<div id="bt_bb"></div><div id="bt_bb_add_root"><i></i></div>';
	
	bt_bb_edit_dialog();
	
	echo '<div id="bt_bb_front_end_preview_container"><div id="bt_bb_front_end_preview" class="bt_bb_dialog">';
		echo '<div class="bt_bb_dialog_header">';
			echo '<div class="bt_bb_front_end_preview_resize bt_bb_front_end_preview_xxl bt_bb_front_end_preview_selected" data-preview-size="xxl"></div>';
			echo '<div class="bt_bb_front_end_preview_resize bt_bb_front_end_preview_xl" data-preview-size="xl"></div>';
			echo '<div class="bt_bb_front_end_preview_resize bt_bb_front_end_preview_lg" data-preview-size="lg"></div>';
			echo '<div class="bt_bb_front_end_preview_resize bt_bb_front_end_preview_md" data-preview-size="md"></div>';
			echo '<div class="bt_bb_front_end_preview_resize bt_bb_front_end_preview_sm" data-preview-size="sm"></div>';
			echo '<div class="bt_bb_front_end_preview_resize bt_bb_front_end_preview_xs" data-preview-size="xs"></div>';
			echo '<div class="bt_bb_front_end_preview_close bt_bb_dialog_close"></div>';
			echo '<span>' . esc_html__( 'Preview', 'bold-builder' ) . '</span>';
		echo '</div>';
		echo '<iframe name="bt_bb_front_end_preview_iframe"></iframe>';
	echo '</div></div>';
	
	echo '<div id="bt_bb_main_toolbar">';
	echo '<i class="bt_bb_undo" title="' . esc_html__( 'Undo', 'bold-builder' ) . '"></i>';
	echo '<i class="bt_bb_redo" title="' . esc_html__( 'Redo', 'bold-builder' ) . '"></i>';
		echo '<span class="bt_bb_separator">|</span>';
	echo '<i class="bt_bb_paste_root" title="' . esc_html__( 'Paste', 'bold-builder' ) . '"></i>';
	echo '<span class="bt_bb_cb_items"></span>';
	echo '<i class="bt_bb_manage_clipboard" title="' . esc_html__( 'Clipboard Manager', 'bold-builder' ) . '"></i>';
		echo '<span class="bt_bb_separator">|</span>';
	echo '<i class="bt_bb_preview" title="' . esc_html__( 'Preview', 'bold-builder' ) . '"></i>';
	echo '<i class="bt_bb_save" title="' . esc_html__( 'Save', 'bold-builder' ) . '"></i>';
	echo '</div>';

	add_action( 'admin_footer', array( new BT_BB_Data_Proxy( $json_content ), 'js' ) );

}

function bt_bb_do_shortcode( $content ) {
	global $shortcode_tags;
	if ( ! ( ( empty( $shortcode_tags ) || ! is_array( $shortcode_tags ) ) ) ) {
		$pattern = get_shortcode_regex();
		
		global $bt_bb_array;
		
		$callback = new BT_BB_Callback( $bt_bb_array );
		
		$preg_cb = preg_replace_callback( "/$pattern/s", array( $callback, 'bt_bb_do_shortcode_tag' ), $content );
	}
}

class BT_BB_Callback {

	private $bt_bb_array;

    function __construct( &$bt_bb_array ) {
        $this->bt_bb_array = &$bt_bb_array;
    }

	function bt_bb_do_shortcode_tag( $m ) {
		global $shortcode_tags;
		
		global $bt_bb_map;

		// allow [[foo]] syntax for escaping a tag
		if ( $m[1] == '[' && $m[6] == ']' ) {
			return $m[0];
		}

		$tag = $m[2];
		$attr = shortcode_parse_atts( $m[3] );

		if ( is_array( $attr ) ) {
			$this->bt_bb_array[] = array( 'title' => $tag, 'base' => $tag, 'key' => str_replace( '.', '', uniqid( 'bt_bb_', true ) ), 'attr' => bt_bb_json_encode( $attr ), 'children' => array() );
		} else {
			$this->bt_bb_array[] = array( 'title' => $tag, 'base' => $tag, 'key' => str_replace( '.', '', uniqid( 'bt_bb_', true ) ), 'children' => array() );
		}

		if ( isset( $m[5] ) && $m[5] != '' ) {
			// enclosing tag - extra parameter
			$pattern = get_shortcode_regex();
			
			if ( isset( $bt_bb_map[ $m[2] ]['accept']['_content'] ) && $bt_bb_map[ $m[2] ]['accept']['_content'] ) {
				$r = $m[5];
			} else {
				$callback = new BT_BB_Callback( $this->bt_bb_array[ count( $this->bt_bb_array ) - 1 ]['children'] );
				$r = preg_replace_callback( "/$pattern/s", array( $callback, 'bt_bb_do_shortcode_tag' ), $m[5] );
				$r = trim( $r );
			}
		
			if ( $r != '' ) {
				$this->bt_bb_array[ count( $this->bt_bb_array ) - 1 ]['children'][0] = array( 'title' => '_content', 'base' => '_content', 'content' => $r, 'children' => array() );
			}
		}
	}
}

function bt_bb_edit_dialog() {
	echo '<div id="bt_bb_dialog" class="bt_bb_dialog">';
		echo '<div class="bt_bb_dialog_header"><div class="bt_bb_dialog_close"></div><span></span></div>';
		echo '<div class="bt_bb_dialog_header_tools"></div>';
		echo '<div class="bt_bb_dialog_content">';
		echo '</div>';
		echo '<div class="bt_bb_dialog_tinymce">';
			echo '<div class="bt_bb_dialog_tinymce_editor_container">';
				wp_editor( '' , 'bt_bb_tinymce', array( 'textarea_rows' => 12 ) );
			echo '</div>';
			echo '<input type="button" class="bt_bb_dialog_button bt_bb_edit button button-small" value="' . esc_html__( 'Submit', 'bold-builder' ) . '">';
		echo '</div>';
	echo '</div>';
}

function bt_bb_active_for_post_type_fe() {
	$options = get_option( 'bt_bb_settings' );
	$post_types = get_post_types( array( 'public' => true, 'show_in_nav_menus' => true ) );
	$active_pt = array();
	foreach( $post_types as $pt ) {
		if ( ! $options || ( ! array_key_exists( $pt, $options ) || ( array_key_exists( $pt, $options ) && $options[ $pt ] == '1' ) ) ) {
			$active_pt[ $pt ] = $pt;
		}
	}
	$post_type = get_post_type();
	if ( $post_type ) {
		return array_key_exists( $post_type, $active_pt );
	}
	return false;
}

function bt_bb_json_encode( $arg ) {
	return json_encode($arg);
}

function bt_bb_json_decode( $arg ) {
	return json_decode($arg);
}

/* General publish date filter */

function bt_bb_publish_hide_callback( $output, $atts ) {
	$now = date("Y-m-d H:m", time());
	if ( !isset( $atts['publish_datetime'] ) || $atts['publish_datetime'] == '' ) {
		$publish_datetime = date("Y-m-d H:m", time() - 60 * 60 * 24);
	} else  {
		$publish_datetime =  str_replace( "T", " ", $atts['publish_datetime']);
	}
	if ( !isset( $atts['expiry_datetime'] ) || $atts['expiry_datetime'] == '' ) {
		$expiry_datetime = date("Y-m-d H:m", time() + 60 * 60 * 24);
	} else {
		$expiry_datetime = str_replace( "T", " ", $atts['expiry_datetime']);
	}
	
	if ( $now >= $publish_datetime && $now <= $expiry_datetime ) {
		return $output;	
	} else {
		return "";
	}
}

add_filter( 'bt_bb_general_output', 'bt_bb_publish_hide_callback', 10, 2 );

class BT_BB_Basic_Element {
	
	protected $shortcode;
	protected $prefix;
	protected $prefix_backend;
	
	public $extra_responsive_data_override_param;
	
	function __construct() {
		$this->init();
	}

	function init() {
		
		require_once( dirname(__FILE__) . '/content_elements_misc/misc.php' );
		
		$this->prefix = 'bt_bb_';
		$this->prefix_backend = 'bt_bb_';
		$this->shortcode = get_class( $this );
		$this->extra_responsive_data_override_param = [];
		add_shortcode( $this->shortcode, array( $this, 'handle_shortcode' ) );
		
		add_action( 'admin_bar_init', array( $this, 'map_shortcode' ), 8 ); // priority 9 in admin_bar_init in bold-builder-fe.php
		
		if ( ! is_admin() ) {
			add_action( 'admin_bar_init', array( $this, 'enqueue' ) );
		}

		$this->add_params();
		add_filter( 'bt_bb_extract_atts_' . $this->shortcode, array( $this, 'atts_callback' ) );
		add_filter( 'bt_bb_extract_atts', array( $this, 'atts_callback' ) ); // older themes override content elements
		add_filter( $this->shortcode . '_class', array( $this, 'responsive_hide_callback' ), 10, 2 );
		add_filter( $this->shortcode . '_extra_responsive_data_override_param', function( $params ) { 
			$p = 'animation';
			if ( ! in_array( $p, $params ) ) {
				$params[] = $p;
			}
			return $params;
		});	
		add_action( $this->shortcode . '_before_extra_responsive_param', array( $this, 'extra_responsive_param' ) );
		add_filter( $this->shortcode . '_output', array( $this, 'data_shortcode_fe_editor' ), 10, 2 );
	}
	
	function enqueue() {
		
	}
	
	function add_params() {
		
	}
	
	function atts_callback( $atts ) {
		
	}

	function responsive_hide_callback( $class, $atts ) {
		if ( isset( $atts['responsive'] ) && $atts['responsive'] != '' ) {
			$class = array_merge( $class, preg_filter('/^/', $this->prefix, explode( ' ', $atts['responsive'] ) ) );
		}
		return $class;
	}
	
	function extra_responsive_param() {
		$this->extra_responsive_data_override_param = apply_filters( $this->shortcode . '_extra_responsive_data_override_param', $this->extra_responsive_data_override_param );
	}
	
	function data_shortcode_fe_editor( $output, $atts ) {
		if ( is_array( $atts ) && array_key_exists( 'ignore_fe_editor', $atts ) ) { // skip if used with do_shortcode in other element
			return $output;
		}
		if ( current_user_can( 'edit_pages' ) ) {
			BT_BB_FE::$fe_id++;
			$atts1 = $atts;
			if ( is_array( $atts ) ) {
				$atts1 = array();
				foreach( $atts as $k => $v ) {
					$atts1[ $k ] = str_ireplace( array( '``', '`{`', '`}`' ), array( '*`*`*', '*`*{*`*', '*`*}*`*' ), $v );
				}
			}
			$json_atts = htmlspecialchars( json_encode( $atts1, JSON_FORCE_OBJECT ) );
			return preg_replace( '/(<\w+)/', '$1 data-base="' . $this->shortcode . '" data-bt-bb-fe-atts="' . preg_replace( '/\$([0-9]+)/', '\\\$$1', $json_atts ) . '" data-fe-id="' . BT_BB_FE::$fe_id . '"', $output, 1 );
		} else {
			return $output;
		}
	}

	function to_uc( $str ) {
		$str = str_replace( '_', ' ', $str );
		$str = ucwords( $str );
		$str = str_replace( ' ', '', $str );
		return $str;
	}		
	
	function to_camel( $str ) {
		$str = $this->to_uc( $str );
		$str = lcfirst( $str );
		return $str;
	}

}

class BT_BB_Element extends BT_BB_Basic_Element {

	function add_params() {
		
		$bt_bb_skip_responsive_arr = [ 'bt_bb_content_slider_item', 'bt_bb_google_maps_location', 'bt_bb_leaflet_map_location' ];
		
		$params = array();
		
		if ( ! in_array( $this->shortcode, $bt_bb_skip_responsive_arr ) ) {
			$params[] = array( 'param_name' => 'responsive', 'type' => 'checkbox_group', 'heading' => esc_html__( 'Hide element on screens', 'bold-builder' ), 'group' => esc_html__( 'Responsive', 'bold-builder' ), 'preview' => true,
				'value' => array(
					esc_html__( '≤480px', 'bold-builder' ) => 'hidden_xs',
					esc_html__( '481-768px', 'bold-builder' ) => 'hidden_ms',
					esc_html__( '769-992px', 'bold-builder' ) => 'hidden_sm',
					esc_html__( '993-1200px', 'bold-builder' ) => 'hidden_md',
					esc_html__( '1201-1400px', 'bold-builder' ) => 'hidden_lg',
					esc_html__( '>1400px', 'bold-builder' ) => 'hidden_xl',
				)
			);
		}
		
		$params[] = array( 'param_name' => 'publish_datetime', 'type' => 'datetime-local', 'heading' => esc_html__( 'Publish date', 'bold-builder' ), 'description' => esc_html__( 'Fill both the date and time', 'bold-builder' ), 'group' => esc_html__( 'Custom', 'bold-builder' ), 'weight' => 990 );
		$params[] = array( 'param_name' => 'expiry_datetime', 'type' => 'datetime-local', 'heading' => esc_html__( 'Expiry date', 'bold-builder' ), 'description' => esc_html__( 'Fill both the date and time', 'bold-builder' ), 'group' => esc_html__( 'Custom', 'bold-builder' ), 'weight' => 995 );
		$params[] = array( 'param_name' => 'el_id', 'preview' => true, 'type' => 'textfield', 'heading' => esc_html__( 'Element ID', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. #myid', 'bold-builder' ), 'group' => esc_html__( 'Custom', 'bold-builder' ), 'weight' => 1000 );
		$params[] = array( 'param_name' => 'el_class', 'type' => 'textfield', 'heading' => esc_html__( 'Extra class name(s)', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. .myclass', 'bold-builder' ), 'group' => esc_html__( 'Custom', 'bold-builder' ), 'weight' => 1005 );
		$params[] = array( 'param_name' => 'el_style', 'type' => 'textfield', 'heading' => esc_html__( 'Inline CSS style', 'bold-builder' ), 'placeholder' => esc_html__( 'E.g. opacity: 0.5;', 'bold-builder' ), 'group' => esc_html__( 'Custom', 'bold-builder' ), 'weight' => 1010 );
		
		$params[] = array( 'param_name' => 'bb_version', 'type' => 'hidden', 'value' => BT_BB_VERSION );
		
		$bt_bb_skip_animation_arr = [ 'bt_bb_slider', 'bt_bb_content_slider', 'bt_bb_content_slider_item', 'bt_bb_accordion_item', 'bt_bb_tab_item', 'bt_bb_google_maps_location', 'bt_bb_leaflet_map_location' ];
		
		if ( ! in_array( $this->shortcode, $bt_bb_skip_animation_arr ) ) {
			$params[] = array( 'param_name' => 'animation', 'type' => 'dropdown', 'heading' => esc_html__( 'Animation', 'bold-builder' ), 'group' => esc_html__( 'Custom', 'bold-builder' ), 'responsive_override' => true, 'preview' => true, 'weight' => 997,
				'value' => array(
					esc_html__( 'No Animation', 'bold-builder' ) => 'no_animation',
					esc_html__( 'Fade In', 'bold-builder' ) => 'fade_in',
					esc_html__( 'Move Up', 'bold-builder' ) => 'move_up',
					esc_html__( 'Move Left', 'bold-builder' ) => 'move_left',
					esc_html__( 'Move Right', 'bold-builder' ) => 'move_right',
					esc_html__( 'Move Down', 'bold-builder' ) => 'move_down',
					esc_html__( 'Zoom In', 'bold-builder' ) => 'zoom_in',
					esc_html__( 'Zoom Out', 'bold-builder' ) => 'zoom_out',
					esc_html__( 'Fade In / Move Up', 'bold-builder' ) => 'fade_in move_up',
					esc_html__( 'Fade In / Move Left', 'bold-builder' ) => 'fade_in move_left',
					esc_html__( 'Fade In / Move Right', 'bold-builder' ) => 'fade_in move_right',
					esc_html__( 'Fade In / Move Down', 'bold-builder' ) => 'fade_in move_down',
					esc_html__( 'Fade In / Zoom In', 'bold-builder' ) => 'fade_in zoom_in',
					esc_html__( 'Fade In / Zoom Out', 'bold-builder' ) => 'fade_in zoom_out'
				)
			);
		}
		
		bt_bb_add_params( $this->shortcode, $params );

		/*if ( $this->shortcode == 'bt_bb_content_slider' ) {      
			bt_bb_remove_params( $this->shortcode, 'animation' );
		}*/
	}
	
	function atts_callback( $atts ) {
		return array(
			'responsive' => '',
			'animation'  => '',
			'el_id'      => '',
			'el_class'   => '',
			'el_style'   => '',
			'bb_version' => '',
		) + $atts;
	}
	
	function responsive_override_class( &$class, $arr ) {
		if ( $arr['value'] != '' ) {
			if ( strpos( $arr['value'], '%$%' ) !== false ) {
				$value_arr = explode( '%$%', $arr['value'] );
			} else {
				$value_arr = explode( ',;,', $arr['value'] );
			}
			if ( isset( $arr['suffix'] ) ) {
				$suffix = $arr['suffix'];
			} else {
				$suffix = '_';
			}
			$main = $arr['prefix'] . $arr['param'] . $suffix . $value_arr[0];
			if ( ! ( isset( $arr['ignore'] ) && $arr['ignore'] == $value_arr[0] ) ) {
				$class[] = $main;
			}
			if ( count( $value_arr ) == 5 ) {
				if ( $value_arr[1] != '' ) {
					//$class[] = $arr['prefix'] . $arr['param'] . '_xxl' . $suffix . $value_arr[1];
					$class[] = $arr['prefix'] . $arr['param'] . '_xl' . $suffix . $value_arr[1];
					$class[] = $arr['prefix'] . $arr['param'] . '_lg' . $suffix . $value_arr[1];
				}
				if ( $value_arr[2] != '' ) {
					$class[] = $arr['prefix'] . $arr['param'] . '_md' . $suffix . $value_arr[2];
				}
				if ( $value_arr[3] != '' ) {
					$class[] = $arr['prefix'] . $arr['param'] . '_sm' . $suffix . $value_arr[3];
				}
				if ( $value_arr[4] != '' ) {
					$class[] = $arr['prefix'] . $arr['param'] . '_xs' . $suffix . $value_arr[4];
				}
			} else if ( count( $value_arr ) == 6 ) {
				if ( $value_arr[1] != '' ) {
					//$class[] = $arr['prefix'] . $arr['param'] . '_xxl' . $suffix . $value_arr[1];
					$class[] = $arr['prefix'] . $arr['param'] . '_xl' . $suffix . $value_arr[1];
				}
				if ( $value_arr[2] != '' ) {
					$class[] = $arr['prefix'] . $arr['param'] . '_lg' . $suffix . $value_arr[2];
				}
				if ( $value_arr[3] != '' ) {
					$class[] = $arr['prefix'] . $arr['param'] . '_md' . $suffix . $value_arr[3];
				}
				if ( $value_arr[4] != '' ) {
					$class[] = $arr['prefix'] . $arr['param'] . '_sm' . $suffix . $value_arr[4];
				}
				if ( $value_arr[5] != '' ) {
					$class[] = $arr['prefix'] . $arr['param'] . '_xs' . $suffix . $value_arr[5];
				}
			} else if ( count( $value_arr ) == 7 ) {
				if ( $value_arr[1] != '' ) {
					$class[] = $arr['prefix'] . $arr['param'] . '_xxl' . $suffix . $value_arr[1];
				}
				if ( $value_arr[2] != '' ) {
					$class[] = $arr['prefix'] . $arr['param'] . '_xl' . $suffix . $value_arr[2];
				}
				if ( $value_arr[3] != '' ) {
					$class[] = $arr['prefix'] . $arr['param'] . '_lg' . $suffix . $value_arr[3];
				}
				if ( $value_arr[4] != '' ) {
					$class[] = $arr['prefix'] . $arr['param'] . '_md' . $suffix . $value_arr[4];
				}
				if ( $value_arr[5] != '' ) {
					$class[] = $arr['prefix'] . $arr['param'] . '_sm' . $suffix . $value_arr[5];
				}
				if ( $value_arr[6] != '' ) {
					$class[] = $arr['prefix'] . $arr['param'] . '_xs' . $suffix . $value_arr[6];
				}
			}
		}
	}
	
	function responsive_data_override_class( &$class, &$data_override_class, $arr ) {
		if ( $arr['value'] != '' ) {
			if ( strpos( $arr['value'], '%$%' ) !== false ) {
				$value_arr = explode( '%$%', $arr['value'] );
			} else {
				$value_arr = explode( ',;,', $arr['value'] );
			}
			if ( isset( $arr['suffix'] ) ) {
				$suffix = $arr['suffix'];
			} else {
				$suffix = '_';
			}
			
			$main = $arr['prefix'] . $arr['param'] . $suffix . $value_arr[0];
			
			$animate = false;
			if ( $arr['param'] == 'animation' ) { // animation is a special case
				if ( implode( '', $value_arr ) != 'no_animation' ) {
					$animate = true;
				}
				if ( $value_arr[0] != 'no_animation' && implode( '', array_slice( $value_arr, 1 ) ) == '' ) { 
					$class[] = $main;
					$class[] = 'animate';
				}
			} else {
				$class[] = $main;
			}
			
			if ( count( $value_arr ) == 5 ) {
				$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'current_class' ] = $main . ( $animate ? ' ' . 'animate' : '' );
				$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'def' ] = $value_arr[0] . ( $animate ? ' ' . 'animate' : '' );
				$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'xl' ] = $value_arr[0] . ( $animate ? ' ' . 'animate' : '' );

				if ( $value_arr[1] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'lg' ] = $value_arr[1] . ( $animate ? ' ' . 'animate' : '' );
				}
				if ( $value_arr[2] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'md' ] = $value_arr[2] . ( $animate ? ' ' . 'animate' : '' );
				}
				if ( $value_arr[3] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'sm' ] = $value_arr[3] . ( $animate ? ' ' . 'animate' : '' );
				}
				if ( $value_arr[4] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'xs' ] = $value_arr[4] . ( $animate ? ' ' . 'animate' : '' );
				}
			} else if ( count( $value_arr ) == 6 ) {
				$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'current_class' ] = $main . ( $animate ? ' ' . 'animate' : '' );
				$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'def' ] = $value_arr[0] . ( $animate ? ' ' . 'animate' : '' );
				if ( $value_arr[1] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'xl' ] = $value_arr[1] . ( $animate ? ' ' . 'animate' : '' );
				}
				if ( $value_arr[2] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'lg' ] = $value_arr[2] . ( $animate ? ' ' . 'animate' : '' );
				}
				if ( $value_arr[3] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'md' ] = $value_arr[3] . ( $animate ? ' ' . 'animate' : '' );
				}
				if ( $value_arr[4] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'sm' ] = $value_arr[4] . ( $animate ? ' ' . 'animate' : '' );
				}
				if ( $value_arr[5] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'xs' ] = $value_arr[5] . ( $animate ? ' ' . 'animate' : '' );
				}
			} else if ( count( $value_arr ) == 7 ) {
				$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'current_class' ] = $main . ( $animate ? ' ' . 'animate' : '' );
				$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'def' ] = $value_arr[0] . ( $animate ? ' ' . 'animate' : '' );
				if ( $value_arr[1] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'xxl' ] = $value_arr[1] . ( $animate ? ' ' . 'animate' : '' );
				}
				if ( $value_arr[2] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'xl' ] = $value_arr[2] . ( $animate ? ' ' . 'animate' : '' );
				}
				if ( $value_arr[3] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'lg' ] = $value_arr[3] . ( $animate ? ' ' . 'animate' : '' );
				}
				if ( $value_arr[4] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'md' ] = $value_arr[4] . ( $animate ? ' ' . 'animate' : '' );
				}
				if ( $value_arr[5] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'sm' ] = $value_arr[5] . ( $animate ? ' ' . 'animate' : '' );
				}
				if ( $value_arr[6] != '' ) {
					$data_override_class[ $arr['prefix'] . $arr['param'] . $suffix ][ 'xs' ] = $value_arr[6] . ( $animate ? ' ' . 'animate' : '' );
				}
			}
		}
	}
	
}

// BB version
function bt_bb_version( $output, $atts ) {
	if ( isset( $atts['bb_version'] ) ) {
		$pattern = '/([<a-zA-Z0-9]+)\s(.+)/s';
		$replacement = '$1 data-bb-version="' . $atts['bb_version'] . '" $2';
		return preg_replace( $pattern, $replacement, $output );
	}
	return $output;
}
add_action( 'bt_bb_general_output', 'bt_bb_version', 10, 2 );

// WP 6 menu issue
function bt_bb_wp6_import_fix() {
	$terms = get_terms( array(
		'taxonomy' => 'nav_menu',
		'hide_empty' => false
	) );
	foreach ( $terms as $term ) {
		wp_update_term_count_now( array( $term->term_id ), 'nav_menu' );
	}
}
add_action( 'import_end', 'bt_bb_wp6_import_fix' );

$elements = array();

if ( ! class_exists( 'BoldThemes_BB_Settings' ) || ! BoldThemes_BB_Settings::$custom_content_elements ) {
	$glob_match = glob( plugin_dir_path( __FILE__ ) . 'content_elements/*/*.php' );
	$glob_match = apply_filters( 'bt_bb_content_elements', $glob_match );
	if ( $glob_match ) {
		foreach( $glob_match as $file ) {
			if ( preg_match( '/(\w+)\/\1.php$/', $file, $match ) ) {
				$elements[ $match[1] ] = $file;
			}
		}
	}
	
	$theme = wp_get_theme( get_template() );
	$template = $theme->template;
	//$author = $theme->author; // triggers this: Notice: Function _load_textdomain_just_in_time was called incorrectly.

	require_once plugin_dir_path( __FILE__ ) . 'content_elements/_deprecated.php';
	
	if ( /*strpos( $author, 'BoldThemes' ) !== false &&*/ in_array( $template, BT_BB_Root::$deprecated['bt_bb_separator'] ) ) {
		$elements[ 'bt_bb_separator' ] = plugin_dir_path( __FILE__ ) . 'content_elements/_deprecated/bt_bb_separator/bt_bb_separator.php';
	}

	$glob_match = glob( WP_PLUGIN_DIR . '/' . get_template() . '/bold-page-builder/content_elements/*/*.php' );
	if ( $glob_match ) {
		foreach( $glob_match as $file ) {
			if ( preg_match( '/(\w+)\/\1.php$/', $file, $match ) ) {
				$elements[ $match[1] ] = $file;
			}
		}
	}
}

$glob_match = glob( get_template_directory() . '/bold-page-builder/content_elements/*/*.php' );
if ( $glob_match ) {
	foreach( $glob_match as $file ) {
		if ( preg_match( '/(\w+)\/\1.php$/', $file, $match ) ) {
			$elements[ $match[1] ] = $file;
		}
	}
}

$glob_match = glob( get_stylesheet_directory() . '/bold-page-builder/content_elements/*/*.php' );
if ( $glob_match ) {
	foreach( $glob_match as $file ) {
		if ( preg_match( '/(\w+)\/\1.php$/', $file, $match ) ) {
			$elements[ $match[1] ] = $file;
		}
	}
}

add_action( 'init', function() use ( $elements ) {
	foreach( $elements as $key => $value ) {
		require( $value );
		new $key();
	}
} );

add_filter( 'wp_get_attachment_url', 'bt_bb_honor_ssl_for_attachments' );
function bt_bb_honor_ssl_for_attachments( $url ) {
	$http = site_url( FALSE, 'http' );
	$https = site_url( FALSE, 'https' );
	return ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ? str_replace( $http, $https, $url ) : $url;
}

add_action( 'content_save_pre', 'bt_bb_save_pre' );
function bt_bb_save_pre( $content ) {
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		if ( str_contains( $content, '[bt_bb_price_list' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to save Price List element.', 'bold-builder' ) );
		}
		if ( str_contains( $content, '[bt_bb_raw_content' ) ) {
			wp_die( esc_html__( 'Sorry, you are not allowed to save Raw Content element.', 'bold-builder' ) );
		}
	}
	return $content;
}

/* Widgets */

if ( ! class_exists( 'BoldThemes_BB_Settings' ) || ! BoldThemes_BB_Settings::$custom_content_elements ) { // only with native BB elements
	require_once( plugin_dir_path( __FILE__ ) . 'widgets/init.php' );
	//include 'admin-notice.php'; // ### Instagram ###
	include 'admin-notice-rating.php';
}

$options = get_option( 'bt_bb_settings' );
if ( ! $options || ( ! array_key_exists( 'tips', $options ) || ( array_key_exists( 'tips', $options ) && $options['tips'] == '1' ) ) ) {
	include 'tips.php';
}

require_once 'bold-builder-fe.php';
require_once 'ai/ai.php';
