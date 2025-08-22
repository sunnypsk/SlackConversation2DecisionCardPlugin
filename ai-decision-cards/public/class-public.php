<?php
/**
 * Public functionality handler.
 *
 * This class handles public-facing functionality including content filters
 * and public page creation.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/public
 */

namespace AIDC\PublicUi;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Public functionality handler.
 *
 * This class manages the content filter for meta banner display and
 * public page creation functionality.
 * Extracted from the main AIDC_Plugin class during Phase 2 modularization.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/public
 * @author     Sunny Poon
 */
class PublicHandler {

	/**
	 * Option name for storing public page ID.
	 *
	 * @since 1.3.0
	 * @var string
	 */
	const OPTION_PUBLIC_PAGE_ID = 'aidc_public_page_id';

	/**
	 * Register hooks for public functionality.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		add_filter( 'the_content', array( $this, 'prepend_meta_banner' ), 5 );
	}

	/**
	 * Prepend meta banner to decision card content.
	 *
	 * Copied from AIDC_Plugin::prepend_meta_banner() during Phase 2 extraction.
	 * Displays Status | Owner | Target information at the top of decision cards.
	 *
	 * @since 1.3.0
	 * @param string $content Post content.
	 * @return string Modified content with meta banner prepended.
	 */
	public function prepend_meta_banner( $content ) {
		// Only apply to decision_card post type
		if ( get_post_type() !== 'decision_card' ) {
			return $content;
		}

		// Skip if we're in the admin area (except for preview)
		if ( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return $content;
		}

		// Get meta values
		$status = get_post_meta( get_the_ID(), '_aidc_status', true );
		$owner = get_post_meta( get_the_ID(), '_aidc_owner', true );
		$due = get_post_meta( get_the_ID(), '_aidc_due', true );

		// Set defaults for empty values
		$status = $status ? esc_html( $status ) : 'TBD';
		$owner = $owner ? esc_html( $owner ) : 'TBD';
		$due = $due ? esc_html( $due ) : 'TBD';

		// Create banner HTML with inline styles for accessibility
		$banner = sprintf(
			'<div class="aidc-meta-banner" style="background-color: #f9f9f9; border: 1px solid #ddd; padding: 12px 16px; margin-bottom: 20px; font-size: 14px; line-height: 1.4; color: #333;">%s</div>',
			sprintf(
				/* translators: %1$s: status, %2$s: owner, %3$s: due date */
				__( 'Status: %1$s | Owner: %2$s | Target: %3$s', 'ai-decision-cards' ),
				'<strong>' . $status . '</strong>',
				'<strong>' . $owner . '</strong>',
				'<strong>' . $due . '</strong>'
			)
		);

		return $banner . $content;
	}

	/**
	 * Ensure a public page exists for Decision Cards Display and return its permalink.
	 *
	 * Copied from AIDC_Plugin::get_or_create_public_page_url() during Phase 2 extraction.
	 * Creates the page on first use and stores its ID in an option.
	 *
	 * @since 1.3.0
	 * @return string Permalink URL of the public display page.
	 */
	public function get_or_create_public_page_url() {
		$page_id = intval( get_option( self::OPTION_PUBLIC_PAGE_ID ) );
		if ( $page_id && get_post_status( $page_id ) ) {
			return get_permalink( $page_id );
		}

		// Create a new page to host the public display
		$page_id = wp_insert_post( array(
			'post_title'   => __( 'Decision Cards', 'ai-decision-cards' ),
			'post_name'    => 'decision-cards',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '[decision-cards-list]'
		) );

		if ( ! is_wp_error( $page_id ) && $page_id ) {
			update_option( self::OPTION_PUBLIC_PAGE_ID, $page_id );
			return get_permalink( $page_id );
		}

		// Fallback to home if creation failed
		return home_url( '/' );
	}
}