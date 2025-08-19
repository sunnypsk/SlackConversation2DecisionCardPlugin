<?php
/**
 * Plugin Name: AI Decision Cards
 * Plugin URI: https://github.com/sunnypoon/SlackConversation2DecisionCardPlugin
 * Description: Convert Slack-style conversations into AI-generated Decision Cards with summaries and action items using OpenAI-compatible APIs.
 * Version: 1.0.0
 * Author: Sunny Poon
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-decision-cards
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 *
 * @package AIDecisionCards
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'AIDC_VERSION', '1.0.0' );
define( 'AIDC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AIDC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'AIDC_PLUGIN_FILE', __FILE__ );

/**
 * Main plugin class.
 *
 * @since 1.0.0
 */
class AIDC_Plugin {

	/**
	 * Plugin option names.
	 */
	const OPTION_API_KEY  = 'aidc_openai_api_key';
	const OPTION_API_BASE = 'aidc_openai_api_base';
	const OPTION_MODEL    = 'aidc_openai_model';

	/**
	 * Single instance of the plugin.
	 *
	 * @var AIDC_Plugin
	 */
	private static $instance = null;

	/**
	 * Get single instance of the plugin.
	 *
	 * @return AIDC_Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks.
	 *
	 * @since 1.0.0
	 */
	private function init_hooks() {
		add_action( 'init', array( $this, 'register_cpt' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_pages' ) );
		add_action( 'admin_post_aidc_generate', array( $this, 'handle_generate' ) );
		register_activation_hook( AIDC_PLUGIN_FILE, array( $this, 'on_activate' ) );
		register_deactivation_hook( AIDC_PLUGIN_FILE, array( $this, 'on_deactivate' ) );
	}

	/**
	 * Plugin activation handler.
	 *
	 * @since 1.0.0
	 */
	public function on_activate() {
		// Set default options.
		if ( ! get_option( self::OPTION_API_BASE ) ) {
			update_option( self::OPTION_API_BASE, 'https://api.openai.com/' );
		}
		if ( ! get_option( self::OPTION_MODEL ) ) {
			update_option( self::OPTION_MODEL, 'gpt-3.5-turbo' );
		}

		// Register custom post type and flush rewrite rules.
		$this->register_cpt();
		flush_rewrite_rules( false );
	}

	/**
	 * Plugin deactivation handler.
	 *
	 * @since 1.0.0
	 */
	public function on_deactivate() {
		// Flush rewrite rules to clean up.
		flush_rewrite_rules( false );
	}

	/**
	 * Register the Decision Card custom post type.
	 *
	 * @since 1.0.0
	 */
	public function register_cpt() {
		$labels = array(
			'name'                  => _x( 'Decision Cards', 'Post Type General Name', 'ai-decision-cards' ),
			'singular_name'         => _x( 'Decision Card', 'Post Type Singular Name', 'ai-decision-cards' ),
			'menu_name'             => __( 'Decision Cards', 'ai-decision-cards' ),
			'name_admin_bar'        => __( 'Decision Card', 'ai-decision-cards' ),
			'archives'              => __( 'Decision Archives', 'ai-decision-cards' ),
			'attributes'            => __( 'Decision Attributes', 'ai-decision-cards' ),
			'parent_item_colon'     => __( 'Parent Decision:', 'ai-decision-cards' ),
			'all_items'             => __( 'All Decisions', 'ai-decision-cards' ),
			'add_new_item'          => __( 'Add New Decision Card', 'ai-decision-cards' ),
			'add_new'               => __( 'Add New', 'ai-decision-cards' ),
			'new_item'              => __( 'New Decision Card', 'ai-decision-cards' ),
			'edit_item'             => __( 'Edit Decision Card', 'ai-decision-cards' ),
			'update_item'           => __( 'Update Decision Card', 'ai-decision-cards' ),
			'view_item'             => __( 'View Decision Card', 'ai-decision-cards' ),
			'view_items'            => __( 'View Decision Cards', 'ai-decision-cards' ),
			'search_items'          => __( 'Search Decision Cards', 'ai-decision-cards' ),
			'not_found'             => __( 'No Decision Cards found', 'ai-decision-cards' ),
			'not_found_in_trash'    => __( 'No Decision Cards found in Trash', 'ai-decision-cards' ),
			'featured_image'        => __( 'Featured Image', 'ai-decision-cards' ),
			'set_featured_image'    => __( 'Set featured image', 'ai-decision-cards' ),
			'remove_featured_image' => __( 'Remove featured image', 'ai-decision-cards' ),
			'use_featured_image'    => __( 'Use as featured image', 'ai-decision-cards' ),
			'insert_into_item'      => __( 'Insert into Decision Card', 'ai-decision-cards' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Decision Card', 'ai-decision-cards' ),
			'items_list'            => __( 'Decision Cards list', 'ai-decision-cards' ),
			'items_list_navigation' => __( 'Decision Cards list navigation', 'ai-decision-cards' ),
			'filter_items_list'     => __( 'Filter Decision Cards list', 'ai-decision-cards' ),
		);

		$args = array(
			'label'                 => __( 'Decision Card', 'ai-decision-cards' ),
			'description'           => __( 'AI-generated decision summaries from conversations', 'ai-decision-cards' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'custom-fields' ),
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 25,
			'menu_icon'             => 'dashicons-yes-alt',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => false,
			'capability_type'       => 'post',
			'map_meta_cap'          => true,
			'show_in_rest'          => false,
		);

		register_post_type( 'decision_card', $args );

		// Register custom fields for meta data.
		$this->register_meta_fields();
	}

	/**
	 * Register meta fields for decision cards.
	 *
	 * @since 1.0.0
	 */
	private function register_meta_fields() {
		register_post_meta(
			'decision_card',
			'_aidc_status',
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => false,
				'default'      => 'Proposed',
			)
		);

		register_post_meta(
			'decision_card',
			'_aidc_owner',
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => false,
				'default'      => '',
			)
		);

		register_post_meta(
			'decision_card',
			'_aidc_due',
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => false,
				'default'      => '',
			)
		);
	}

	/**
	 * Register admin pages and menus.
	 *
	 * @since 1.0.0
	 */
	public function register_admin_pages() {
		// Add Generate page as submenu under the Decision Cards post type menu
		add_submenu_page(
			'edit.php?post_type=decision_card',
			__( 'Generate from Conversation', 'ai-decision-cards' ),
			__( 'Generate from Conversation', 'ai-decision-cards' ),
			'edit_posts',
			'aidc_generate',
			array( $this, 'render_generate_page' )
		);

		// Add Settings page as submenu under the Decision Cards post type menu
		add_submenu_page(
			'edit.php?post_type=decision_card',
			__( 'Settings', 'ai-decision-cards' ),
			__( 'Settings', 'ai-decision-cards' ),
			'manage_options',
			'aidc_settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Get escaped attribute value for an option.
	 *
	 * @since 1.0.0
	 * @param string $opt_name The option name.
	 * @param string $default  The default value.
	 * @return string Escaped attribute value.
	 */
	private function esc_attr_val( $opt_name, $default = '' ) {
		return esc_attr( (string) get_option( $opt_name, $default ) );
	}

	/**
	 * Render the settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$saved = false;
		if ( isset( $_POST['aidc_settings_nonce'] ) && wp_verify_nonce( $_POST['aidc_settings_nonce'], 'aidc_save_settings' ) ) {
			if ( isset( $_POST['aidc_api_key'] ) ) {
				update_option( self::OPTION_API_KEY, sanitize_text_field( wp_unslash( $_POST['aidc_api_key'] ) ) );
			}
			if ( isset( $_POST['aidc_api_base'] ) ) {
				$base = trim( sanitize_text_field( wp_unslash( $_POST['aidc_api_base'] ) ) );
				if ( '' !== $base && '/' !== substr( $base, -1 ) ) {
					$base .= '/';
				}
				update_option( self::OPTION_API_BASE, $base );
			}
			if ( isset( $_POST['aidc_model'] ) ) {
				update_option( self::OPTION_MODEL, sanitize_text_field( wp_unslash( $_POST['aidc_model'] ) ) );
			}
			$saved = true;
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
							<label for="aidc_api_key"><?php esc_html_e( 'OpenAI-compatible API Key', 'ai-decision-cards' ); ?></label>
						</th>
						<td>
							<input type="password" id="aidc_api_key" name="aidc_api_key" 
								   value="<?php echo $this->esc_attr_val( self::OPTION_API_KEY ); ?>" 
								   class="regular-text" />
							<p class="description">
								<?php esc_html_e( 'Your API key for OpenAI or OpenRouter.', 'ai-decision-cards' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="aidc_api_base"><?php esc_html_e( 'API Base URL', 'ai-decision-cards' ); ?></label>
						</th>
						<td>
							<input type="text" id="aidc_api_base" name="aidc_api_base" 
								   value="<?php echo $this->esc_attr_val( self::OPTION_API_BASE, 'https://api.openai.com/' ); ?>" 
								   class="regular-text" />
							<p class="description">
								<?php esc_html_e( 'Example: https://api.openai.com/ or your OpenRouter-compatible base.', 'ai-decision-cards' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="aidc_model"><?php esc_html_e( 'Model', 'ai-decision-cards' ); ?></label>
						</th>
						<td>
							<input type="text" id="aidc_model" name="aidc_model" 
								   value="<?php echo $this->esc_attr_val( self::OPTION_MODEL, 'gpt-3.5-turbo' ); ?>" 
								   class="regular-text" />
							<p class="description">
								<?php esc_html_e( 'Default: gpt-3.5-turbo. Any OpenAI-compatible chat model is fine.', 'ai-decision-cards' ); ?>
							</p>
						</td>
					</tr>
				</table>
				<?php submit_button( __( 'Save Settings', 'ai-decision-cards' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the generate page.
	 *
	 * @since 1.0.0
	 */
	public function render_generate_page() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		$api_key_exists = (bool) get_option( self::OPTION_API_KEY );
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
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('aidc_generate_nonce', 'aidc_generate_nonce_field'); ?>
                <input type="hidden" name="action" value="aidc_generate" />
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="aidc_conversation">Conversation Transcript</label></th>
                        <td>
                            <textarea id="aidc_conversation" name="aidc_conversation" class="large-text code" rows="14" placeholder="[10:02] Alice: Should we launch Beta next week?\n[10:05] Bob: Needs QA signoff...\n..."></textarea>
                            <p class="description">Plain text copied from Slack or similar. The AI will summarize it.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="aidc_status">Status</label></th>
                        <td>
                            <select id="aidc_status" name="aidc_status">
                                <option value="Proposed">Proposed</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="aidc_owner">Owner</label></th>
                        <td><input type="text" id="aidc_owner" name="aidc_owner" class="regular-text" placeholder="e.g., Sunny" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="aidc_due">Due Date</label></th>
                        <td><input type="date" id="aidc_due" name="aidc_due" class="regular-text" /></td>
                    </tr>
                </table>
                <?php submit_button('Generate Summary & Create Draft', 'primary', 'submit', true, ['onclick' => 'this.disabled=true; this.form.submit();']); ?>
            </form>
        </div>
        <?php
    }

    public function handle_generate() {
        if (!current_user_can('edit_posts')) {
            wp_die('Insufficient permissions.');
        }
        if (!isset($_POST['aidc_generate_nonce_field']) || !wp_verify_nonce($_POST['aidc_generate_nonce_field'], 'aidc_generate_nonce')) {
            wp_die('Nonce verification failed.');
        }

        $conversation = isset($_POST['aidc_conversation']) ? trim(wp_unslash($_POST['aidc_conversation'])) : '';
        $status = isset($_POST['aidc_status']) ? sanitize_text_field($_POST['aidc_status']) : 'Proposed';
        $owner  = isset($_POST['aidc_owner']) ? sanitize_text_field($_POST['aidc_owner']) : '';
        $due    = isset($_POST['aidc_due']) ? sanitize_text_field($_POST['aidc_due']) : '';

        if ($conversation === '') {
            $this->redirect_with_notice('Please paste a conversation before generating.', 'error');
        }

        $api_key  = get_option(self::OPTION_API_KEY);
        $api_base = rtrim((string) get_option(self::OPTION_API_BASE, 'https://api.openai.com/'), '/') . '/';
        $model    = (string) get_option(self::OPTION_MODEL, 'gpt-3.5-turbo');

        if (!$api_key) {
            $this->redirect_with_notice('Missing API key. Set it in Settings.', 'error');
        }

        $prompt_system = "You are an assistant that converts Slack-like conversations into a concise Decision Card. 
- First, write a short **Summary** (3–6 sentences) focusing on the decision, key arguments, and rationale.
- Then write **Action Items** as a bullet list with who/what/when if present.
- Keep a neutral, professional tone.
- Only use facts found in the conversation. If uncertain, say so.";

        $body = [
            'model' => $model,
            'temperature' => 0.2,
            'max_tokens' => 500,
            'messages' => [
                ['role' => 'system', 'content' => $prompt_system],
                ['role' => 'user', 'content' => "Conversation:\n" . $conversation . "\n\nPlease output in the following structure:\nSummary:\n- ...\n- ...\n\nAction Items:\n- ...\n- ..."]
            ]
        ];

        $endpoint = $api_base . 'v1/chat/completions';
        $args = [
            'headers' => [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type'  => 'application/json'
            ],
            'timeout' => 30,
            'body' => wp_json_encode($body)
        ];

        $resp = wp_remote_post($endpoint, $args);
        if (is_wp_error($resp)) {
            $this->redirect_with_notice('API request failed: ' . $resp->get_error_message(), 'error');
        }
        $code = wp_remote_retrieve_response_code($resp);
        $raw  = wp_remote_retrieve_body($resp);
        if ($code < 200 || $code >= 300) {
            $this->redirect_with_notice('API error (' . $code . '): ' . esc_html($raw), 'error');
        }
        $data = json_decode($raw, true);
        if (!$data || empty($data['choices'][0]['message']['content'])) {
            $this->redirect_with_notice('Empty response from AI. Try again with a shorter conversation.', 'error');
        }
        $ai = $data['choices'][0]['message']['content'];

        $allowed = [
            'p' => [], 'ul' => [], 'ol' => [], 'li' => [], 'strong' => [], 'em' => [], 'br' => [], 'b' => [], 'i' => [], 'h2' => [], 'h3' => [], 'code'=>[]
        ];
        $safe_ai = wp_kses($ai, $allowed);

        $content = "<h2>Summary</h2>\n";
        if (stripos($safe_ai, 'Action Items') !== false) {
            $parts = preg_split('/\\bAction Items\\b[:]?/i', $safe_ai, 2);
            $summary_html = trim($parts[0]);
            $actions_html = isset($parts[1]) ? trim($parts[1]) : '';
            $content .= wpautop($summary_html);
            if ($actions_html !== '') {
                $content .= "\n<h2>Action Items</h2>\n" . wpautop($actions_html);
            }
        } else {
            $content .= wpautop($safe_ai);
        }

        $postarr = [
            'post_type'   => 'decision_card',
            'post_status' => 'draft',
            'post_title'  => 'Decision Card – ' . current_time('Y-m-d H:i'),
            'post_content'=> $content,
        ];
        $post_id = wp_insert_post($postarr, true);
        if (is_wp_error($post_id)) {
            $this->redirect_with_notice('Failed to create post: ' . $post_id->get_error_message(), 'error');
        }
        update_post_meta($post_id, '_aidc_status', $status);
        update_post_meta($post_id, '_aidc_owner', $owner);
        update_post_meta($post_id, '_aidc_due',   $due);

        $edit_url = get_edit_post_link($post_id, '');
        if ($edit_url) {
            wp_redirect($edit_url);
            exit;
        }
        $this->redirect_with_notice('Draft created (ID ' . intval($post_id) . ').', 'success');
    }

	/**
	 * Redirect with notice message.
	 *
	 * @since 1.0.0
	 * @param string $msg  The notice message.
	 * @param string $type The notice type (success, error, warning, info).
	 */
	private function redirect_with_notice( $msg, $type = 'success' ) {
		$url = add_query_arg(
			array(
				'page'        => 'aidc_generate',
				'aidc_notice' => rawurlencode( $msg ),
				'aidc_type'   => $type,
			),
			admin_url( 'edit.php?post_type=decision_card' )
		);
		wp_redirect( $url );
		exit;
	}
}

/**
 * Initialize the plugin.
 *
 * @since 1.0.0
 */
function aidc_init() {
	AIDC_Plugin::get_instance();
}
add_action( 'plugins_loaded', 'aidc_init' );

/**
 * Display admin notices for the plugin.
 *
 * @since 1.0.0
 */
function aidc_admin_notices() {
	if ( ! is_admin() || ! isset( $_GET['aidc_notice'] ) ) {
		return;
	}

	$type = isset( $_GET['aidc_type'] ) ? sanitize_text_field( wp_unslash( $_GET['aidc_type'] ) ) : 'info';
	$class = 'notice';
	
	switch ( $type ) {
		case 'success':
			$class .= ' notice-success';
			break;
		case 'error':
			$class .= ' notice-error';
			break;
		case 'warning':
			$class .= ' notice-warning';
			break;
		default:
			$class .= ' notice-info';
			break;
	}

	$message = isset( $_GET['aidc_notice'] ) ? rawurldecode( sanitize_text_field( wp_unslash( $_GET['aidc_notice'] ) ) ) : '';
	
	printf(
		'<div class="%s"><p>%s</p></div>',
		esc_attr( $class ),
		esc_html( $message )
	);
}
add_action( 'admin_notices', 'aidc_admin_notices' );
