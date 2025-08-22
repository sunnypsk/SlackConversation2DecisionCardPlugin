<?php
/**
 * Settings Page Template.
 *
 * This template renders the Settings admin page.
 * Contains only presentation logic - no business logic.
 *
 * Available variables:
 * @var bool $saved Whether settings were just saved.
 * @var string $api_type Current API type setting.
 * @var string $api_key Current API key setting.
 * @var string $api_base Current API base URL setting.
 * @var string $model Current model setting.
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
	<h1><?php esc_html_e( 'AI Decision Cards — Settings', 'ai-decision-cards' ); ?></h1>
	<?php if ( $saved ) : ?>
		<div class="notice notice-success">
			<p><?php esc_html_e( 'Settings saved.', 'ai-decision-cards' ); ?></p>
		</div>
	<?php endif; ?>
	<form method="post">
		<?php wp_nonce_field( 'aidc_save_settings', 'aidc_settings_nonce' ); ?>
		<table class="form-table" role="presentation">
			<tr>
				<th scope="row">
					<label for="aidc_api_type"><?php esc_html_e( 'API Type', 'ai-decision-cards' ); ?></label>
				</th>
				<td>
					<select id="aidc_api_type" name="aidc_api_type" onchange="toggleApiFields()">
						<option value="openai" <?php selected( $api_type, 'openai' ); ?>>
							<?php esc_html_e( 'OpenAI Compatible', 'ai-decision-cards' ); ?>
						</option>
					</select>
					<p class="description">
						<?php esc_html_e( 'Choose your AI service provider.', 'ai-decision-cards' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="aidc_api_key"><span id="api_key_label"><?php esc_html_e( 'API Key', 'ai-decision-cards' ); ?></span></label>
				</th>
				<td>
					<input type="password" id="aidc_api_key" name="aidc_api_key" 
						   value="<?php echo esc_attr( $api_key ); ?>" 
						   class="regular-text" />
					<p class="description" id="api_key_desc">
						<?php esc_html_e( 'Your API key for OpenAI, OpenRouter, or other compatible services.', 'ai-decision-cards' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="aidc_api_base"><span id="api_base_label"><?php esc_html_e( 'API Base URL', 'ai-decision-cards' ); ?></span></label>
				</th>
				<td>
					<input type="text" id="aidc_api_base" name="aidc_api_base" 
						   value="<?php echo esc_attr( $api_base ); ?>" 
						   class="regular-text" />
					<p class="description" id="api_base_desc">
						<?php esc_html_e( 'Example: https://api.openai.com/ or https://openrouter.ai/api/v1/', 'ai-decision-cards' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="aidc_model"><span id="model_label"><?php esc_html_e( 'Model', 'ai-decision-cards' ); ?></span></label>
				</th>
				<td>
					<input type="text" id="aidc_model" name="aidc_model" 
						   value="<?php echo esc_attr( $model ); ?>" 
						   class="regular-text" />
					<p class="description" id="model_desc">
						<?php esc_html_e( 'Example: gpt-3.5-turbo, gpt-4, claude-3-haiku, etc.', 'ai-decision-cards' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<p class="submit">
			<?php submit_button( __( 'Save Settings', 'ai-decision-cards' ), 'primary', 'submit', false ); ?>
			<button type="button" id="aidc_test_api" class="button button-secondary" style="margin-left: 10px;">
				<?php esc_html_e( 'Test API Key', 'ai-decision-cards' ); ?>
			</button>
		</p>
	</form>
	<div id="aidc_test_result" style="margin-top: 15px;"></div>
</div>