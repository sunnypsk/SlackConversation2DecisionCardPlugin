<?php
/**
 * AI Client class for OpenAI-compatible API communication.
 *
 * This class handles API requests to OpenAI and OpenAI-compatible services
 * for AI chat completion functionality.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/includes
 */

namespace AIDC\Includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AI Client class.
 *
 * Handles communication with OpenAI-compatible API endpoints for chat completions.
 *
 * @since 1.3.0
 */
class AiClient {

	/**
	 * Complete a chat request using OpenAI-compatible API.
	 *
	 * @since 1.3.0
	 *
	 * @param string $endpoint_base The base URL for the API (e.g., 'https://api.openai.com/').
	 * @param string $api_key       The API key for authentication.
	 * @param string $model         The model to use (e.g., 'gpt-3.5-turbo').
	 * @param array  $messages      Array of message objects with 'role' and 'content'.
	 * @param array  $options       Optional parameters (temperature, max_tokens, etc.).
	 * @return array                Decoded response array.
	 * @throws \WP_Error            On API errors or invalid responses.
	 */
	public function complete_chat( $endpoint_base, $api_key, $model, array $messages, array $options = array() ) {
		// Validate required parameters
		if ( empty( $endpoint_base ) || empty( $api_key ) || empty( $model ) || empty( $messages ) ) {
			return new \WP_Error( 'invalid_parameters', __( 'Missing required parameters for API request.', 'ai-decision-cards' ) );
		}

		// Set default options
		$defaults = array(
			'temperature' => 0.2,
			'max_tokens'  => 600,
		);
		$options = wp_parse_args( $options, $defaults );

		// Build request body
		$body = array(
			'model'       => $model,
			'temperature' => $options['temperature'],
			'max_tokens'  => $options['max_tokens'],
			'messages'    => $messages,
		);

		// Build endpoint using shared helper
		$endpoint = \AIDC\Includes\Helpers::chat_completions_endpoint( $endpoint_base );

		// Build headers
		$headers = array(
			'Authorization' => 'Bearer ' . $api_key,
			'Content-Type'  => 'application/json',
		);

		// Prepare request arguments
		$args = array(
			'headers' => $headers,
			'timeout' => 30,
			'body'    => wp_json_encode( $body ),
		);

		// Make the API request
		$response = wp_remote_post( $endpoint, $args );

		// Check for network errors
		if ( is_wp_error( $response ) ) {
			return new \WP_Error( 'api_request_failed', __( 'API request failed: ', 'ai-decision-cards' ) . $response->get_error_message() );
		}

		// Check response code
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		if ( $response_code < 200 || $response_code >= 300 ) {
			return new \WP_Error( 'api_error', sprintf( __( 'API error (%d): %s', 'ai-decision-cards' ), $response_code, esc_html( $response_body ) ) );
		}

		// Decode response
		$data = json_decode( $response_body, true );
		if ( ! $data ) {
			return new \WP_Error( 'invalid_response', __( 'Invalid JSON response from API.', 'ai-decision-cards' ) );
		}

		// Validate response structure
		if ( empty( $data['choices'][0]['message']['content'] ) ) {
			return new \WP_Error( 'empty_response', __( 'Empty response from AI. Try again with a shorter conversation.', 'ai-decision-cards' ) );
		}

		return $data;
	}
}