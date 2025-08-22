<?php
/**
 * Display Page Template.
 *
 * This template renders the admin Display page for previewing Decision Cards.
 * Contains only presentation logic - no business logic.
 *
 * Available variables:
 * @var string $public_page_url URL to the public display page.
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
<div class="wrap">
	<h1><?php esc_html_e( 'Decision Cards Display', 'ai-decision-cards' ); ?></h1>
	<p><?php esc_html_e( 'Preview how Decision Cards appear to website visitors.', 'ai-decision-cards' ); ?></p>
	<p><a href="<?php echo esc_url( $public_page_url ); ?>" target="_blank" class="button button-secondary"><?php esc_html_e( 'View Public Display Page', 'ai-decision-cards' ); ?></a></p>
	
	<?php
	// Render a live preview using the shortcode output.
	echo do_shortcode( '[decision-cards-list]' );
	?>
</div>
