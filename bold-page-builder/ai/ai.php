<?php

/**
 * Get AI response
 */

class BT_BB_AI {
	public static $_content_system_prompt;
}

BT_BB_AI::$_content_system_prompt = 'You are a copywriter and your goal is to help users generate website content based on the user prompt.';

function bt_bb_ai() {
	check_ajax_referer( 'bt_bb_nonce', 'nonce' );
	
	$keywords = wp_kses_post( $_POST['keywords'] );
	$tone = wp_kses_post( $_POST['tone'] );
	$mode = wp_kses_post( $_POST['mode'] );
	$language = wp_kses_post( $_POST['language'] );
	$target = json_decode( stripslashes( wp_kses_post( $_POST['target'] ) ), true );
	$modify = boolval( $_POST['modify'] === 'true' );
	$content = wp_kses_post( $_POST['content'] );

	if ( $target == '_content' ) {
		$system_prompt_base = BT_BB_AI::$_content_system_prompt;
	} else {
		$system_prompt_base = wp_kses_post( $_POST['system_prompt'] );
	}

	$length = json_decode( stripslashes( wp_kses_post( $_POST['length'] ) ), true );
	
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
		$content_obj = json_decode( stripslashes( $content ) );
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

	$ch = curl_init( $url );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_POST, true );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $data ) );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Authorization: Bearer ' . $api_key
	));

	$response = curl_exec( $ch );
	curl_close( $ch );

	$result = json_decode( $response, true );

	if ( $result ) {
		if ( is_array( $result ) ) {
			if ( isset( $result['error'] ) ) {
				echo $result['error']['message'];
			} else {
				if ( is_array( $target ) ) {
					if ( $modify ) {
						echo str_ireplace( '\\\\', '\\', $result['choices'][0]['message']['content'] );
					} else {
						echo $result['choices'][0]['message']['tool_calls'][0]['function']['arguments'];
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