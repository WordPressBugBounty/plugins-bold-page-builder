<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Get AI response
 */

class BT_BB_AI {
	public static $_content_system_prompt;
}

BT_BB_AI::$_content_system_prompt = 'You are a copywriter and your goal is to help users generate website content based on the user prompt.';

function bt_bb_ai() {
	check_ajax_referer( 'bt_bb_nonce', 'nonce' );

	$keywords = isset( $_POST['keywords'] ) ? wp_kses_post( wp_unslash( $_POST['keywords'] ) ) : '';
	$tone     = isset( $_POST['tone'] ) ? wp_kses_post( wp_unslash( $_POST['tone'] ) ) : '';
	$mode     = isset( $_POST['mode'] ) ? wp_kses_post( wp_unslash( $_POST['mode'] ) ) : '';
	$language = isset( $_POST['language'] ) ? wp_kses_post( wp_unslash( $_POST['language'] ) ) : '';
	$target   = isset( $_POST['target'] ) ? json_decode( wp_kses_post( wp_unslash( $_POST['target'] ) ), true ) : '';
	$modify   = isset( $_POST['modify'] ) && $_POST['modify'] === 'true';
	$content  = isset( $_POST['content'] ) ? wp_kses_post( wp_unslash( $_POST['content'] ) ) : '';

	if ( $target == '_content' ) {
		$system_prompt_base = BT_BB_AI::$_content_system_prompt;
	} else {
		$system_prompt_base = isset( $_POST['system_prompt'] ) ? wp_kses_post( wp_unslash( $_POST['system_prompt'] ) ) : '';
	}

	$length = isset( $_POST['length'] ) ? json_decode( wp_kses_post( wp_unslash( $_POST['length'] ) ), true ) : array();
	
	$options = get_option( 'bt_bb_settings' );
	if ( is_array( $options ) ) {
		$api_key = isset( $options['openai_api_key'] ) ? $options['openai_api_key'] : '';
		$api_model = ( isset( $options['openai_model'] ) && $options['openai_model'] != '' ) ? $options['openai_model'] : 'gpt-4';
	}
	
	$url = 'https://api.openai.com/v1/chat/completions';
	
	if ( is_array( $target ) ) { // not _content
		$properties = array();
		
		foreach( $target as $k => $v ) {
			$properties[ $k ] = array( 'type' => 'string', 'description' => $v['alias'] );
		}

		$schema = array(
			'name' => 'get_content',
			'parameters' => [
				'type' => 'object',
				'properties' => $properties
			]
		);
	}
	
	$tone_prompt = '';
	if ( $tone != '' ) {
		$tone_prompt = ' The tone of the content must be ' . $tone . '.';
	}
	
	$language_prompt = '';
	if ( $language == '' ) {
		$language = 'English';
	}
	$language_prompt = ' Content must be in ' . $language . '.';
		
	$length_prompt = '';
	
	if ( is_array( $target ) ) {
		$i = 0;
		foreach( $target as $k => $v ) {
			if ( $length[ $i ] != '' && abs( intval( $length[ $i ] ) ) > 0 ) {
				$w_or_c = 'words.';
				if ( str_contains( $length[ $i ], 'c' ) ) {
					$w_or_c = 'characters.';
				}
				$length_prompt .= ' ' . ucfirst( $v[ 'alias' ] ) . ' ' . 'must have' . ' ' . abs( intval( $length[ $i ] ) ) . ' ' . $w_or_c;
			}
			$i++;
		}
	} else { // _content
		if ( $length[ 0 ] != '' && abs( intval( $length[ 0 ] ) ) > 0 ) {
			$w_or_c = 'words.';
			if ( str_contains( $length[ 0 ], 'c' ) ) {
				$w_or_c = 'characters.';
			}
			$length_prompt .= ' Content must have' . ' ' . abs( intval( $length[ 0 ] ) ) . ' ' . $w_or_c;
		}
	}
	
	$user_prompt = $keywords;
	
	if ( $modify ) {
		
		$_content = false;
		$content_obj = json_decode( $content ); // $content already unslashed above
		if ( property_exists( $content_obj, '_content' ) ) {
			$content = '"""' . $content_obj->_content . '"""';
			$_content = true;
		}

		$user_prompt = $content;
		
		if ( $mode == 'translate' ) {
			$system_prompt = 'Translate content to ' . $language . '. If you are unable to do that, return given content.';
		} else if ( $mode == 'rephrase' ) {
			$system_prompt = 'Rephrase content.';
			if ( $tone != '' ) {
				$system_prompt .= ' The tone of the content must be ' . $tone . '.';
			}
		} else if ( $mode == 'correct' ) {
			$system_prompt = 'Correct content grammar. If content is already grammatically correct, return given content.';
		}
		
		if ( is_array( $target ) ) {
			$system_prompt .= ' Preserve JSON structure.';
		}
		
	} else {
		$system_prompt = $system_prompt_base;
		$system_prompt .= $tone_prompt;
		$system_prompt .= $length_prompt;
		$system_prompt .= $language_prompt;
	}

	if ( is_array( $target ) ) {
		$data = array(
			'model' => $api_model,
			'messages' => [array(
				'role' => 'user',
				'content' => $user_prompt
			),array(
				'role' => 'system',
				'content' => $system_prompt
			)]
		);
		if ( ! $modify ) {
			$data['tools'] = [array(
				'type' => 'function',
				'function' => $schema
			)];
			$data['tool_choice'] = [
				'type' => 'function',
				'function' => [
					'name' => 'get_content'
				]
			];
		}
	} else { // _content
		$data = array(
			'model' => $api_model,
			'messages' => [array(
				'role' => 'user',
				'content' => $user_prompt
			),array(
				'role' => 'system',
				'content' => $system_prompt
			)]
		);
	}

	$response = wp_remote_post( $url, array(
		'timeout' => 30,
		'headers' => array(
			'Content-Type'  => 'application/json',
			'Authorization' => 'Bearer ' . $api_key,
		),
		'body'    => wp_json_encode( $data ),
	) );

	$result = is_wp_error( $response ) ? null : json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $result ) {
		if ( is_array( $result ) ) {
			if ( isset( $result['error'] ) ) {
				echo esc_html( $result['error']['message'] );
			} else {
				if ( is_array( $target ) ) {
					if ( $modify ) {
						echo esc_html( str_ireplace( '\\\\', '\\', $result['choices'][0]['message']['content'] ) );
					} else {
						echo esc_html( $result['choices'][0]['message']['tool_calls'][0]['function']['arguments'] );
					}
				} else { // _content
					echo json_encode( array( '_content' => trim( $result['choices'][0]['message']['content'], '"' ) ) );
				}
			}
		}
	}
	
	wp_die();
}
add_action( 'wp_ajax_bt_bb_ai', 'bt_bb_ai' );