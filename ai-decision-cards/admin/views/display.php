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
	// Reuse the legacy render_cards_list method during transition
	if ( class_exists( 'AIDC_Plugin' ) ) {
		$legacy_instance = \AIDC_Plugin::get_instance();
		if ( method_exists( $legacy_instance, 'render_cards_list' ) ) {
			// Use reflection to access private method during transition
			$reflection = new ReflectionClass( $legacy_instance );
			$method = $reflection->getMethod( 'render_cards_list' );
			$method->setAccessible( true );
			$method->invoke( $legacy_instance );
		}
	} else {
		echo '<p>' . esc_html__( 'Cards list functionality will be available after full migration.', 'ai-decision-cards' ) . '</p>';
	}
	?>
</div>