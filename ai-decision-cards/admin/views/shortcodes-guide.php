<?php
/**
 * Shortcodes Guide Page Template.
 *
 * This template renders the Shortcode Usage Guide admin page.
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
<div class="wrap" id="shortcode-guide">
	<h1><?php esc_html_e( 'Shortcode Usage Guide', 'ai-decision-cards' ); ?></h1>
	<p><?php esc_html_e( 'Use these shortcodes to display Decision Cards on your website pages and posts.', 'ai-decision-cards' ); ?></p>

	<div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
		<h2><?php esc_html_e( 'Display Decision Cards List', 'ai-decision-cards' ); ?></h2>
		<p><?php esc_html_e( 'Show a grid of Decision Cards with optional filtering:', 'ai-decision-cards' ); ?></p>

		<div style="background: white; padding: 15px; border-radius: 4px; font-family: monospace; margin: 10px 0;">
			<div style="margin-bottom: 8px;"><code>[decision-cards-list]</code> <span style="color: #666;"><?php esc_html_e( '— Display all Decision Cards', 'ai-decision-cards' ); ?></span></div>
			<div style="margin-bottom: 8px;"><code>[decision-cards-list limit=&quot;5&quot;]</code> <span style="color: #666;"><?php esc_html_e( '— Show only 5 cards', 'ai-decision-cards' ); ?></span></div>
			<div style="margin-bottom: 8px;"><code>[decision-cards-list status=&quot;Approved&quot;]</code> <span style="color: #666;"><?php esc_html_e( '— Show only approved cards', 'ai-decision-cards' ); ?></span></div>
			<div style="margin-bottom: 8px;"><code>[decision-cards-list owner=&quot;John&quot;]</code> <span style="color: #666;"><?php esc_html_e( '— Filter by owner', 'ai-decision-cards' ); ?></span></div>
			<div><code>[decision-cards-list show_filters=&quot;no&quot;]</code> <span style="color: #666;"><?php esc_html_e( '— Hide search filters', 'ai-decision-cards' ); ?></span></div>
		</div>

		<h3><?php esc_html_e( 'Available Parameters:', 'ai-decision-cards' ); ?></h3>
		<ul>
			<li><strong>limit</strong>: <?php esc_html_e( 'Number of cards to display (1-50, default: 10)', 'ai-decision-cards' ); ?></li>
			<li><strong>status</strong>: <?php esc_html_e( 'Filter by status (Proposed, Approved, Rejected)', 'ai-decision-cards' ); ?></li>
			<li><strong>owner</strong>: <?php esc_html_e( 'Filter by owner name', 'ai-decision-cards' ); ?></li>
			<li><strong>search</strong>: <?php esc_html_e( 'Search in card titles and content', 'ai-decision-cards' ); ?></li>
			<li><strong>show_filters</strong>: <?php esc_html_e( 'Show/hide filter form (yes/no, default: yes)', 'ai-decision-cards' ); ?></li>
		</ul>
	</div>

	<div style="background: #f0f8ff; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
		<h2><?php esc_html_e( 'Display Single Decision Card', 'ai-decision-cards' ); ?></h2>
		<p><?php esc_html_e( 'Embed a specific Decision Card by its ID:', 'ai-decision-cards' ); ?></p>

		<div style="background: white; padding: 15px; border-radius: 4px; font-family: monospace; margin: 10px 0;">
			<div style="margin-bottom: 8px;"><code>[decision-card id=&quot;123&quot;]</code> <span style="color: #666;"><?php esc_html_e( '— Display full Decision Card', 'ai-decision-cards' ); ?></span></div>
			<div style="margin-bottom: 8px;"><code>[decision-card id=&quot;123&quot; excerpt_only=&quot;yes&quot;]</code> <span style="color: #666;"><?php esc_html_e( '— Show only summary', 'ai-decision-cards' ); ?></span></div>
			<div><code>[decision-card id=&quot;123&quot; show_meta=&quot;no&quot;]</code> <span style="color: #666;"><?php esc_html_e( '— Hide status banner', 'ai-decision-cards' ); ?></span></div>
		</div>

		<h3><?php esc_html_e( 'Available Parameters:', 'ai-decision-cards' ); ?></h3>
		<ul>
			<li><strong>id</strong>: <?php esc_html_e( 'Decision Card ID (required - find this in the URL when editing)', 'ai-decision-cards' ); ?></li>
			<li><strong>show_meta</strong>: <?php esc_html_e( 'Show status banner (yes/no, default: yes)', 'ai-decision-cards' ); ?></li>
			<li><strong>excerpt_only</strong>: <?php esc_html_e( 'Show only summary instead of full content (yes/no, default: no)', 'ai-decision-cards' ); ?></li>
		</ul>
	</div>

	<div style="background: #fff2cc; padding: 15px; border-radius: 8px; border-left: 4px solid #ffcc00;">
		<h3><?php esc_html_e( 'Quick Copy', 'ai-decision-cards' ); ?></h3>
		<p><?php esc_html_e( 'You can copy these shortcodes and paste them directly into any page or post editor.', 'ai-decision-cards' ); ?></p>
		<p><?php esc_html_e( 'To find a Decision Card ID, edit the card and look at the URL: ...&post=123 (where 123 is the ID)', 'ai-decision-cards' ); ?></p>
	</div>
</div>