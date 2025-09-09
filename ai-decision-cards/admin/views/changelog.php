<?php
/**
 * Changelog Page Template.
 *
 * This template renders the Changelog admin page.
 * Contains only presentation logic - no business logic.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/admin/views
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap" id="aidc-changelog">
	<h1><?php esc_html_e( 'Changelog', 'ai-decision-cards' ); ?></h1>
	<p><?php esc_html_e( 'What\'s new and improved in AI Decision Cards.', 'ai-decision-cards' ); ?></p>

	<h2>1.3.1</h2>
	<ul>
		<li><?php esc_html_e( 'Safer settings: Your API key stays hidden on the page. Leave the field blank to keep your current key.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Easier testing: “Test API Key” works even if the field is blank (it uses your saved key).', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Smoother updates: Admin screens refresh their assets more reliably after updates.', 'ai-decision-cards' ); ?></li>
	</ul>
	<p><em><?php echo wp_kses_post( sprintf( __( 'Want the technical details? See the %s.', 'ai-decision-cards' ), '<a href="https://github.com/sunnypsk/SlackConversation2DecisionCardPlugin/releases" target="_blank" rel="noopener noreferrer">' . esc_html__( 'release notes on GitHub', 'ai-decision-cards' ) . '</a>' ) ); ?></em></p>

	<h2>1.3.0</h2>
	<ul>
		<li><?php esc_html_e( 'Under‑the‑hood refresh for a faster, more reliable experience.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Cleaner screens and smoother interactions across admin and public pages.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'More consistent styling and scripts to reduce quirks.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Stronger foundation to support future features and updates.', 'ai-decision-cards' ); ?></li>
	</ul>

	<h2>1.2.1</h2>
	<ul>
		<li><?php esc_html_e( 'Now supports translations — add your language easily.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Added a one‑click public Decision Cards page from the menu.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Simpler filters with a quick Reset for faster browsing.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Clearer messages and guidance while generating.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'General stability and security improvements.', 'ai-decision-cards' ); ?></li>
	</ul>

	<h2>1.2.0</h2>
	<ul>
		<li><?php esc_html_e( 'New shortcodes to list and embed Decision Cards anywhere.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Search across titles and full card content.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Filter by status and owner to find decisions quickly.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Public display to share decisions with stakeholders.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Copy‑and‑paste shortcodes and responsive layouts.', 'ai-decision-cards' ); ?></li>
	</ul>

	<h2>1.1.0</h2>
	<ul>
		<li><?php esc_html_e( 'Consistent 5‑section Decision Card layout.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Status banner shows Status | Owner | Target.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Smarter date handling and better previews.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Cleaner output for easier reading.', 'ai-decision-cards' ); ?></li>
	</ul>

	<h2>1.0.0</h2>
	<ul>
		<li><?php esc_html_e( 'First release with AI‑generated Decision Cards and simple embedding.', 'ai-decision-cards' ); ?></li>
	</ul>
</div>
