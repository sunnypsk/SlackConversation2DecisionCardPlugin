<?php
/**
 * Shortcode Meta Box Template.
 *
 * This template renders the shortcode meta box in the decision card edit screen.
 * Contains only presentation logic - no business logic.
 *
 * Available variables:
 * @var WP_Post $post The current post object.
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
<div class="aidc-shortcode-meta-box">
	<p><?php esc_html_e( 'Use these shortcodes to embed this Decision Card in pages or posts:', 'ai-decision-cards' ); ?></p>
	
	<div class="aidc-form-field">
		<label class="aidc-form-label">
			<?php esc_html_e( 'Full Decision Card:', 'ai-decision-cards' ); ?>
		</label>
		<input type="text" 
			   value="[decision-card id=&quot;<?php echo esc_attr( $post->ID ); ?>&quot;]" 
			   readonly 
			   class="aidc-form-textarea aidc-shortcode-input">
		<p class="description aidc-form-description">
			<?php esc_html_e( 'Displays the complete Decision Card with status banner', 'ai-decision-cards' ); ?>
		</p>
	</div>
	
	<div class="aidc-form-field">
		<label class="aidc-form-label">
			<?php esc_html_e( 'Summary Only:', 'ai-decision-cards' ); ?>
		</label>
		<input type="text" 
			   value="[decision-card id=&quot;<?php echo esc_attr( $post->ID ); ?>&quot; excerpt_only=&quot;yes&quot;]" 
			   readonly 
			   class="aidc-form-textarea aidc-shortcode-input">
		<p class="description aidc-form-description">
			<?php esc_html_e( 'Shows only the summary section for quick reference', 'ai-decision-cards' ); ?>
		</p>
	</div>
	
	<div class="aidc-form-field">
		<label class="aidc-form-label">
			<?php esc_html_e( 'Without Status Banner:', 'ai-decision-cards' ); ?>
		</label>
		<input type="text" 
			   value="[decision-card id=&quot;<?php echo esc_attr( $post->ID ); ?>&quot; show_meta=&quot;no&quot;]" 
			   readonly 
			   class="aidc-form-textarea aidc-shortcode-input">
		<p class="description aidc-form-description">
			<?php esc_html_e( 'Hides the status banner for cleaner display', 'ai-decision-cards' ); ?>
		</p>
	</div>
	
	<div class="aidc-user-guide">
		<p>
			<strong><?php esc_html_e( 'How to use:', 'ai-decision-cards' ); ?></strong><br>
			<?php esc_html_e( 'Click any shortcode above to select it, then copy (Ctrl+C) and paste into your page editor.', 'ai-decision-cards' ); ?>
		</p>
	</div>
	
	<div class="aidc-shortcode-link">
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=aidc_shortcodes#shortcode-guide' ) ); ?>" target="_blank">
			<?php esc_html_e( 'View complete shortcode documentation →', 'ai-decision-cards' ); ?>
		</a>
	</div>
</div>