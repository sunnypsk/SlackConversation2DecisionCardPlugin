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

	<h2>1.3.0</h2>
	<ul>
		<li><?php esc_html_e( 'Major: Complete plugin architecture refactor for better maintainability and performance.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'New: Modular class-based design with proper namespace organization.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'New: Improved CSS organization with all inline styles moved to dedicated files.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Improved: Enhanced JavaScript event handling with modern best practices.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Improved: Better separation of concerns between business logic and presentation.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Developer: Reduced main plugin file from 1600+ to 67 lines of code.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Developer: Enhanced code maintainability with shared utility classes.', 'ai-decision-cards' ); ?></li>
	</ul>

	<h2>1.2.1</h2>
	<ul>
		<li><?php esc_html_e( 'New: Translation-ready with a template so you can add your language.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'New: One-click public Decision Cards page from the admin menu.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Improved: Simpler filters with a quick Reset for faster browsing.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Improved: Clearer messages and guidance during generation.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Stability: General reliability and security improvements.', 'ai-decision-cards' ); ?></li>
	</ul>

	<h2>1.2.0</h2>
	<ul>
		<li><?php esc_html_e( 'New: Easy shortcodes to list and embed cards anywhere.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'New: Search across titles and full card content.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'New: Filter by status and owner to find decisions quickly.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'New: Public display option to share decisions with stakeholders.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Improved: Copy-and-paste shortcodes and responsive layouts.', 'ai-decision-cards' ); ?></li>
	</ul>

	<h2>1.1.0</h2>
	<ul>
		<li><?php esc_html_e( 'New: Standard 5-section layout for consistent Decision Cards.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'New: Status banner showing Status | Owner | Target.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Improved: Smarter date handling and better previews.', 'ai-decision-cards' ); ?></li>
		<li><?php esc_html_e( 'Improved: Higher-quality AI output and cleaner rendering.', 'ai-decision-cards' ); ?></li>
	</ul>

	<h2>1.0.0</h2>
	<ul>
		<li><?php esc_html_e( 'First release with AI‑generated Decision Cards and simple embedding.', 'ai-decision-cards' ); ?></li>
	</ul>
</div>