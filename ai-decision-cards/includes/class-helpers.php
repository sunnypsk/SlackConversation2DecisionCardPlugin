<?php
/**
 * Shared utility helpers.
 *
 * This class provides reusable static utility methods used across the plugin.
 * Extracted during Phase 6 modularization to eliminate code duplication.
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
 * Shared utility helpers.
 *
 * This class contains static utility methods that were previously duplicated
 * across multiple classes. Extracted during Phase 6 modularization.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/includes
 * @author     Sunny Poon
 */
class Helpers {

	/**
	 * Redirect with admin notice.
	 *
	 * Redirects to the generate page with a notice message.
	 * Copied from duplicated implementations in AIDC_Plugin and Generator classes.
	 *
	 * @since 1.3.0
	 * @param string $message The notice message to display.
	 * @param string $type    The notice type (success|error|warning|info).
	 */
	public static function redirect_with_notice( $message, $type = 'success' ) {
		$url = add_query_arg(
			array(
				'page'        => 'aidc_generate',
				'aidc_notice' => rawurlencode( $message ),
				'aidc_type'   => $type,
			),
			admin_url( 'edit.php?post_type=decision_card' )
		);
		wp_safe_redirect( $url );
		exit;
	}

	/**
	 * Get escaped option attribute value.
	 *
	 * Retrieves a WordPress option value and escapes it for use in HTML attributes.
	 * Renamed from esc_attr_val() for clarity.
	 *
	 * @since 1.3.0
	 * @param string $option_name The option name to retrieve.
	 * @param string $default     Default value if option doesn't exist.
	 * @return string Escaped attribute value.
	 */
	public static function get_option_attr( $option_name, $default = '' ) {
		return esc_attr( (string) get_option( $option_name, $default ) );
	}

	/**
	 * Build OpenAI-compatible chat completions endpoint URL.
	 *
	 * Handles both standard OpenAI format and services that already include /v1/ in base URL.
	 * Extracted from duplicated logic in AdminAjax and AiClient during Phase 6.
	 *
	 * @since 1.3.0
	 * @param string $api_base The base API URL (with or without trailing slash).
	 * @return string Complete endpoint URL for chat completions.
	 */
	public static function chat_completions_endpoint( $api_base ) {
		$api_base = rtrim( $api_base, '/' ) . '/';
		if ( strpos( $api_base, '/v1/' ) !== false ) {
			// API base already contains v1 (e.g., OpenRouter)
			return $api_base . 'chat/completions';
		} else {
			// Standard OpenAI format
			return $api_base . 'v1/chat/completions';
		}
	}
}