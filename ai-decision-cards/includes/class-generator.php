<?php
/**
 * Generator class for handling Decision Card generation from conversations.
 *
 * This class handles the admin-post processing for generating Decision Cards
 * from Slack-like conversation transcripts using AI.
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
 * Generator class.
 *
 * Handles the generation of Decision Cards from conversation transcripts
 * via admin-post actions.
 *
 * @since 1.3.0
 */
class Generator {

	/**
	 * Option keys for plugin settings.
	 * Must match legacy AIDC_Plugin option names for compatibility.
	 */
	const OPTION_API_TYPE = 'aidc_api_type';
	const OPTION_API_KEY  = 'aidc_openai_api_key';
	const OPTION_API_BASE = 'aidc_openai_api_base';
	const OPTION_MODEL    = 'aidc_openai_model';

	/**
	 * AI Client instance.
	 *
	 * @var AiClient
	 */
	private $ai_client;

	/**
	 * Constructor.
	 *
	 * @since 1.3.0
	 */
	public function __construct() {
		$this->ai_client = new AiClient();
	}

	/**
	 * Register hooks for the generator.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		add_action( 'admin_post_aidc_generate', array( $this, 'handle_generate' ) );
	}

	/**
	 * Handle the generation of Decision Cards from conversation transcripts.
	 *
	 * Processes admin-post form submission to generate AI-powered Decision Cards.
	 *
	 * @since 1.3.0
	 */
	public function handle_generate() {
		// Check user permissions
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_die( esc_html__( 'Insufficient permissions.', 'ai-decision-cards' ) );
		}

		// Verify nonce
		if ( ! isset( $_POST['aidc_generate_nonce_field'] ) || ! wp_verify_nonce( $_POST['aidc_generate_nonce_field'], 'aidc_generate_nonce' ) ) {
			wp_die( esc_html__( 'Nonce verification failed.', 'ai-decision-cards' ) );
		}

		// Sanitize and validate form inputs
		$conversation = isset( $_POST['aidc_conversation'] ) ? trim( wp_unslash( $_POST['aidc_conversation'] ) ) : '';
		$status       = isset( $_POST['aidc_status'] ) ? sanitize_text_field( $_POST['aidc_status'] ) : 'Proposed';
		$owner        = isset( $_POST['aidc_owner'] ) ? sanitize_text_field( $_POST['aidc_owner'] ) : '';
		$due          = isset( $_POST['aidc_due'] ) ? sanitize_text_field( $_POST['aidc_due'] ) : '';

		// Validate required conversation input
		if ( $conversation === '' ) {
			\AIDC\Includes\Helpers::redirect_with_notice( __( 'Please paste a conversation before generating.', 'ai-decision-cards' ), 'error' );
			return;
		}

		// Get API configuration
		$api_type = get_option( self::OPTION_API_TYPE, 'openai' );
		$api_key  = get_option( self::OPTION_API_KEY );
		$api_base = rtrim( (string) get_option( self::OPTION_API_BASE, 'https://api.openai.com/' ), '/' ) . '/';
		$model    = (string) get_option( self::OPTION_MODEL, 'gpt-3.5-turbo' );

		// Validate API key is configured
		if ( ! $api_key ) {
			\AIDC\Includes\Helpers::redirect_with_notice( __( 'Missing API key. Set it in Settings.', 'ai-decision-cards' ), 'error' );
			return;
		}

		// Build system prompt for AI
		$prompt_system = $this->get_system_prompt();

		// Prepare messages for AI
		$messages = array(
			array( 'role' => 'system', 'content' => $prompt_system ),
			array( 'role' => 'user', 'content' => "Conversation:\n" . $conversation ),
		);

		// Make API request
		$response = $this->ai_client->complete_chat( $api_base, $api_key, $model, $messages );

		// Handle API errors
		if ( is_wp_error( $response ) ) {
			\AIDC\Includes\Helpers::redirect_with_notice( $response->get_error_message(), 'error' );
			return;
		}

		// Extract AI-generated content
		$ai_content = $response['choices'][0]['message']['content'];

		// Sanitize AI content with allowed HTML tags
		$allowed_tags = array(
			'h2'         => array(),
			'h3'         => array(),
			'p'          => array(),
			'ul'         => array(),
			'ol'         => array(),
			'li'         => array(),
			'strong'     => array(),
			'em'         => array(),
			'code'       => array(),
			'blockquote' => array(),
			'br'         => array(),
		);
		$content = wp_kses( $ai_content, $allowed_tags );

		// Create the Decision Card post
		$post_data = array(
			'post_type'    => 'decision_card',
			'post_status'  => 'draft',
			'post_title'   => 'Decision Card – ' . current_time( 'Y-m-d H:i' ),
			'post_content' => $content,
		);

		$post_id = wp_insert_post( $post_data, true );

		// Handle post creation errors
		if ( is_wp_error( $post_id ) ) {
			\AIDC\Includes\Helpers::redirect_with_notice( __( 'Failed to create post: ', 'ai-decision-cards' ) . $post_id->get_error_message(), 'error' );
			return;
		}

		// Update post meta fields
		update_post_meta( $post_id, '_aidc_status', $status );
		update_post_meta( $post_id, '_aidc_owner', $owner );
		update_post_meta( $post_id, '_aidc_due', $due );

		// Redirect to edit screen for the newly created post
		$edit_url = get_edit_post_link( $post_id, '' );
		if ( $edit_url ) {
			wp_safe_redirect( $edit_url );
			exit;
		}

		// Fallback success message
		\AIDC\Includes\Helpers::redirect_with_notice( sprintf( __( 'Draft created (ID %d).', 'ai-decision-cards' ), intval( $post_id ) ), 'success' );
	}

	/**
	 * Get the system prompt for AI Decision Card generation.
	 *
	 * @since 1.3.0
	 * @return string The system prompt.
	 */
	private function get_system_prompt() {
		return "You convert Slack-like conversations into a Decision Card in strict HTML with these sections, in this exact order:

<h2>Decision</h2>
<p>(One sentence. What was decided.)</p>

<h2>Summary</h2>
<ul>
<li>(Exactly 3 concise bullets. Why/what changed, key rationale. Use only facts from the conversation.)</li>
</ul>

<h2>Action Items</h2>
<ul>
<li><strong>Owner</strong> — task. Include \"Due: <YYYY-MM-DD>\" if an exact date is present in the conversation; otherwise \"Due: TBD\".
If the conversation uses relative time (e.g., \"next week\", \"the week after\"), KEEP the phrase and ADD a follow-up item like:
\"<strong>Alice</strong> — set exact date for '<relative phrase>' (Due: TBD)\".</li>
</ul>

<h2>Sources</h2>
<blockquote>
<p>(Quote 2–3 short lines from the conversation that directly support the decision, with the original timestamps/names.)</p>
</blockquote>

<h2>Risks / Assumptions</h2>
<ul>
<li>(1–2 bullets on risks, unknowns, or assumptions mentioned or clearly implied in the conversation. If none, output \"None\".)</li>
</ul>

Rules:
- Use only facts from the conversation. If uncertain, say \"TBD\" rather than inventing details.
- Keep neutral, professional tone.
- Output HTML only. No extra preface or epilogue.
- Use proper HTML tags: h2 for sections, ul/li for lists, p for paragraphs, strong for emphasis, blockquote for quotes.";
	}

	// Note: redirect_with_notice() method moved to AIDC\Includes\Helpers in Phase 6
}