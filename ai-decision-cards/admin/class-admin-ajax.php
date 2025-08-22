<?php
/**
 * Admin AJAX handler.
 *
 * This class handles AJAX requests in the admin area, particularly the API test functionality.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/admin
 */

namespace AIDC\Admin;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin AJAX handler.
 *
 * This class manages AJAX functionality for the admin area.
 * Extracted from the main AIDC_Plugin class during Phase 5 modularization.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/admin
 * @author     Sunny Poon
 */
class AdminAjax {

	/**
	 * Register hooks for AJAX handling.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		add_action( 'wp_ajax_aidc_test_api', array( $this, 'handle_api_test' ) );
	}

	/**
	 * Handle API test AJAX request.
	 *
	 * Copied from AIDC_Plugin::handle_api_test() during Phase 5 extraction.
	 * Tests API connectivity with OpenAI-compatible endpoints.
	 *
	 * @since 1.3.0
	 */
	public function handle_api_test() {
		// Check permissions and nonce
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Insufficient permissions.', 'ai-decision-cards' ) );
		}
		
		if ( ! isset( $_POST['aidc_test_nonce'] ) || ! wp_verify_nonce( $_POST['aidc_test_nonce'], 'aidc_test_api' ) ) {
			wp_send_json_error( __( 'Security check failed.', 'ai-decision-cards' ) );
		}
		
		// Get form data
		$api_type = isset( $_POST['api_type'] ) ? sanitize_text_field( $_POST['api_type'] ) : 'openai';
		$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( $_POST['api_key'] ) : '';
		$api_base = isset( $_POST['api_base'] ) ? sanitize_text_field( $_POST['api_base'] ) : '';
		$model = isset( $_POST['model'] ) ? sanitize_text_field( $_POST['model'] ) : '';
		
		// Validate inputs
		if ( empty( $api_key ) ) {
			wp_send_json_error( __( 'API key is required.', 'ai-decision-cards' ) );
		}
		
		if ( empty( $api_base ) ) {
			wp_send_json_error( __( 'API base URL is required.', 'ai-decision-cards' ) );
		}
		
		if ( empty( $model ) ) {
			wp_send_json_error( __( 'Model is required.', 'ai-decision-cards' ) );
		}
		
		// Ensure API base ends with slash
		$api_base = rtrim( $api_base, '/' ) . '/';
		
		// Prepare test request body
		$body = array(
			'model' => $model,
			'temperature' => 0,
			'max_tokens' => 10,
			'messages' => array(
				array(
					'role' => 'user',
					'content' => 'Hello'
				)
			)
		);
		
		// Build endpoint and headers for OpenAI Compatible format
		if ( strpos( $api_base, '/v1/' ) !== false ) {
			// API base already contains v1 (e.g., OpenRouter)
			$endpoint = $api_base . 'chat/completions';
		} else {
			// Standard OpenAI format
			$endpoint = $api_base . 'v1/chat/completions';
		}
		$headers = array(
			'Authorization' => 'Bearer ' . $api_key,
			'Content-Type' => 'application/json'
		);
		
		// Make the API request
		$args = array(
			'headers' => $headers,
			'timeout' => 15,
			'body' => wp_json_encode( $body )
		);
		
		$response = wp_remote_post( $endpoint, $args );
		
		// Check for WordPress HTTP errors
		if ( is_wp_error( $response ) ) {
			wp_send_json_error(
				sprintf(
					/* translators: %s: Error message */
					__( 'Connection failed: %s', 'ai-decision-cards' ),
					$response->get_error_message()
				)
			);
		}
		
		// Check HTTP response code
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		
		if ( $response_code < 200 || $response_code >= 300 ) {
			// Try to extract error message from response
			$error_data = json_decode( $response_body, true );
			$error_message = '';
			
			if ( $error_data && isset( $error_data['error']['message'] ) ) {
				$error_message = $error_data['error']['message'];
			} else {
				$error_message = sprintf(
					/* translators: %1$d: HTTP status code, %2$s: Response body */
					__( 'HTTP %1$d: %2$s', 'ai-decision-cards' ),
					$response_code,
					$response_body
				);
			}
			
			// Add debug info for 405 errors
			if ( $response_code === 405 ) {
				$debug_info = sprintf(
					/* translators: %s: API endpoint URL */
					__( ' (Endpoint: %s)', 'ai-decision-cards' ),
					$endpoint
				);
				$error_message .= $debug_info;
			}
			
			wp_send_json_error(
				sprintf(
					/* translators: %s: Error message */
					__( 'API Error: %s', 'ai-decision-cards' ),
					$error_message
				)
			);
		}
		
		// Try to parse the JSON response
		$data = json_decode( $response_body, true );
		if ( ! $data ) {
			wp_send_json_error( __( 'Invalid JSON response from API.', 'ai-decision-cards' ) );
		}
		
		// Check if response has expected structure
		if ( ! isset( $data['choices'] ) || ! isset( $data['choices'][0] ) ) {
			wp_send_json_error( __( 'Unexpected API response format.', 'ai-decision-cards' ) );
		}
		
		// Success!
		$provider_name = 'OpenAI Compatible API';
		wp_send_json_success(
			sprintf(
				/* translators: %s: API provider name */
				__( '✓ API connection successful! Connected to %s.', 'ai-decision-cards' ),
				$provider_name
			)
		);
	}
}