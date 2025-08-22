<?php
/**
 * Generate Page Template.
 *
 * This template renders the Generate from Conversation admin page.
 * Contains only presentation logic - no business logic.
 *
 * Available variables:
 * @var bool $api_key_exists Whether API key is configured.
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
	<h1><?php esc_html_e( 'Generate Decision Card from Conversation', 'ai-decision-cards' ); ?></h1>
	<p><?php esc_html_e( 'Paste a Slack-like conversation transcript. The plugin will create a Decision Card draft with an AI-generated summary and action items.', 'ai-decision-cards' ); ?></p>
	<?php if ( ! $api_key_exists ) : ?>
		<div class="notice notice-warning">
			<p>
				<strong><?php esc_html_e( 'API key missing.', 'ai-decision-cards' ); ?></strong>
				<?php
				printf(
					/* translators: %s: Settings page URL */
					esc_html__( 'Please set your OpenAI-compatible API key in %s.', 'ai-decision-cards' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=aidc_settings' ) ) . '">' . esc_html__( 'Settings', 'ai-decision-cards' ) . '</a>'
				);
				?>
			</p>
		</div>
	<?php endif; ?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php wp_nonce_field( 'aidc_generate_nonce', 'aidc_generate_nonce_field' ); ?>
		<input type="hidden" name="action" value="aidc_generate" />
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row"><label for="aidc_conversation"><?php esc_html_e( 'Conversation Transcript', 'ai-decision-cards' ); ?></label></th>
				<td>
					<textarea id="aidc_conversation" name="aidc_conversation" class="large-text code" rows="14" placeholder="[10:02] Alice: Should we launch Beta next week?&#10;[10:05] Bob: Needs QA signoff...&#10;..."></textarea>
					<p class="description"><?php esc_html_e( 'Plain text copied from Slack or similar. The AI will summarize it.', 'ai-decision-cards' ); ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="aidc_status"><?php esc_html_e( 'Status', 'ai-decision-cards' ); ?></label></th>
				<td>
					<select id="aidc_status" name="aidc_status">
						<option value="Proposed"><?php esc_html_e( 'Proposed', 'ai-decision-cards' ); ?></option>
						<option value="Approved"><?php esc_html_e( 'Approved', 'ai-decision-cards' ); ?></option>
						<option value="Rejected"><?php esc_html_e( 'Rejected', 'ai-decision-cards' ); ?></option>
					</select>
				</td>
			</tr>
			<tr>
				<th scope="row"><label for="aidc_owner"><?php esc_html_e( 'Owner', 'ai-decision-cards' ); ?></label></th>
				<td><input type="text" id="aidc_owner" name="aidc_owner" class="regular-text" placeholder="<?php esc_attr_e( 'e.g., Sunny', 'ai-decision-cards' ); ?>" /></td>
			</tr>
			<tr>
				<th scope="row"><label for="aidc_due"><?php esc_html_e( 'Due Date', 'ai-decision-cards' ); ?></label></th>
				<td><input type="date" id="aidc_due" name="aidc_due" class="regular-text" /></td>
			</tr>
		</table>
		<?php submit_button( __( 'Generate Summary & Create Draft', 'ai-decision-cards' ), 'primary', 'submit', true, array( 'id' => 'aidc_generate_btn' ) ); ?>
		<div id="aidc_generate_status" style="margin-top: 15px; display: none;"></div>
	</form>
</div>