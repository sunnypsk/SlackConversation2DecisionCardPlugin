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
	const OPTION_API_TYPE = 'aidc_api_type';
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
		add_action( 'init', array( $this, 'register_shortcodes' ) );
		add_action( 'admin_menu', array( $this, 'register_admin_pages' ) );
		add_action( 'admin_post_aidc_generate', array( $this, 'handle_generate' ) );
		add_action( 'wp_ajax_aidc_test_api', array( $this, 'handle_api_test' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_shortcode_meta_box' ) );
		add_filter( 'the_content', array( $this, 'prepend_meta_banner' ), 5 );
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
		if ( ! get_option( self::OPTION_API_TYPE ) ) {
			update_option( self::OPTION_API_TYPE, 'openai' );
		}
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
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 25,
			'menu_icon'             => 'dashicons-yes-alt',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
			'map_meta_cap'          => true,
			'show_in_rest'          => false,
		);

		register_post_type( 'decision_card', $args );

		// Register custom fields for meta data.
		$this->register_meta_fields();
	}

	/**
	 * Register shortcodes.
	 *
	 * @since 1.0.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'decision-cards-list', array( $this, 'shortcode_decision_cards_list' ) );
		add_shortcode( 'decision-card', array( $this, 'shortcode_single_decision_card' ) );
	}

	/**
	 * Handle [decision-cards-list] shortcode.
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function shortcode_decision_cards_list( $atts ) {
		$atts = shortcode_atts(
			array(
				'limit'  => 10,
				'status' => '',
				'owner'  => '',
				'search' => '',
				'show_filters' => 'yes',
			),
			$atts,
			'decision-cards-list'
		);

		// Sanitize attributes
		$limit = max( 1, min( 50, intval( $atts['limit'] ) ) );
		$status = sanitize_text_field( $atts['status'] );
		$owner = sanitize_text_field( $atts['owner'] );
		$search = sanitize_text_field( $atts['search'] );
		$show_filters = ( 'yes' === $atts['show_filters'] || 'true' === $atts['show_filters'] );

		// Build query
		$query_args = array(
			'post_type'      => 'decision_card',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
		);

		// Add search
		if ( ! empty( $search ) ) {
			$query_args['s'] = $search;
		}

		// Add meta query for filters
		$meta_query = array();
		if ( ! empty( $status ) ) {
			$meta_query[] = array(
				'key'     => '_aidc_status',
				'value'   => $status,
				'compare' => '=',
			);
		}
		if ( ! empty( $owner ) ) {
			$meta_query[] = array(
				'key'     => '_aidc_owner',
				'value'   => $owner,
				'compare' => 'LIKE',
			);
		}
		if ( ! empty( $meta_query ) ) {
			$query_args['meta_query'] = $meta_query;
		}

		$query = new WP_Query( $query_args );

		ob_start();
		?>
		<div class="aidc-shortcode-wrapper">
			<?php if ( $show_filters ) : ?>
				<div class="aidc-shortcode-filters">
					<form method="get" class="aidc-shortcode-filter-form">
						<input type="text" name="aidc_search" value="<?php echo esc_attr( $_GET['aidc_search'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Search decision cards...', 'ai-decision-cards' ); ?>">
						<select name="aidc_status">
							<option value=""><?php esc_html_e( 'All Statuses', 'ai-decision-cards' ); ?></option>
							<option value="Proposed" <?php selected( $_GET['aidc_status'] ?? '', 'Proposed' ); ?>><?php esc_html_e( 'Proposed', 'ai-decision-cards' ); ?></option>
							<option value="Approved" <?php selected( $_GET['aidc_status'] ?? '', 'Approved' ); ?>><?php esc_html_e( 'Approved', 'ai-decision-cards' ); ?></option>
							<option value="Rejected" <?php selected( $_GET['aidc_status'] ?? '', 'Rejected' ); ?>><?php esc_html_e( 'Rejected', 'ai-decision-cards' ); ?></option>
						</select>
						<button type="submit"><?php esc_html_e( 'Filter', 'ai-decision-cards' ); ?></button>
					</form>
				</div>
			<?php endif; ?>

			<?php if ( $query->have_posts() ) : ?>
				<div class="aidc-shortcode-grid">
					<?php while ( $query->have_posts() ) : $query->the_post(); ?>
						<div class="aidc-shortcode-card">
							<h3><a href="<?php echo esc_url( get_permalink() ); ?>"><?php the_title(); ?></a></h3>
							
							<?php
							$status = get_post_meta( get_the_ID(), '_aidc_status', true );
							$owner = get_post_meta( get_the_ID(), '_aidc_owner', true );
							$due = get_post_meta( get_the_ID(), '_aidc_due', true );
							?>
							
							<div class="aidc-shortcode-meta">
								<span class="aidc-shortcode-status aidc-status-<?php echo esc_attr( strtolower( $status ) ); ?>">
									<?php echo $status ? esc_html( $status ) : 'TBD'; ?>
								</span>
								<?php if ( $owner ) : ?>
									<span class="aidc-shortcode-owner"><?php esc_html_e( 'Owner:', 'ai-decision-cards' ); ?> <?php echo esc_html( $owner ); ?></span>
								<?php endif; ?>
								<?php if ( $due ) : ?>
									<span class="aidc-shortcode-due"><?php esc_html_e( 'Due:', 'ai-decision-cards' ); ?> <?php echo esc_html( $due ); ?></span>
								<?php endif; ?>
							</div>
							
							<div class="aidc-shortcode-excerpt">
								<?php
								$content = get_the_content();
								// Extract summary or first paragraph
								if ( preg_match( '/<h2>Summary<\/h2>\s*<ul>(.*?)<\/ul>/s', $content, $matches ) ) {
									echo wp_kses_post( $matches[1] );
								} else {
									echo wp_trim_words( wp_strip_all_tags( $content ), 15 );
								}
								?>
							</div>
							
							<div class="aidc-shortcode-date">
								<?php echo get_the_date(); ?>
							</div>
						</div>
					<?php endwhile; ?>
				</div>
			<?php else : ?>
				<p><?php esc_html_e( 'No Decision Cards found.', 'ai-decision-cards' ); ?></p>
			<?php endif; ?>

			<style>
				.aidc-shortcode-wrapper {
					margin: 20px 0;
				}
				.aidc-shortcode-filters {
					background: #f9f9f9;
					padding: 15px;
					border-radius: 5px;
					margin-bottom: 20px;
				}
				.aidc-shortcode-filter-form {
					display: flex;
					gap: 10px;
					flex-wrap: wrap;
					align-items: center;
				}
				.aidc-shortcode-filter-form input,
				.aidc-shortcode-filter-form select,
				.aidc-shortcode-filter-form button {
					padding: 8px 12px;
					border: 1px solid #ddd;
					border-radius: 4px;
				}
				.aidc-shortcode-filter-form button {
					background: #0073aa;
					color: white;
					cursor: pointer;
				}
				.aidc-shortcode-grid {
					display: grid;
					grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
					gap: 20px;
				}
				.aidc-shortcode-card {
					background: white;
					padding: 15px;
					border: 1px solid #ddd;
					border-radius: 5px;
					box-shadow: 0 2px 4px rgba(0,0,0,0.1);
				}
				.aidc-shortcode-card h3 {
					margin: 0 0 10px 0;
				}
				.aidc-shortcode-card h3 a {
					color: #333;
					text-decoration: none;
				}
				.aidc-shortcode-card h3 a:hover {
					color: #0073aa;
				}
				.aidc-shortcode-meta {
					margin-bottom: 10px;
					display: flex;
					flex-wrap: wrap;
					gap: 8px;
					align-items: center;
				}
				.aidc-shortcode-status {
					padding: 3px 6px;
					border-radius: 3px;
					font-size: 11px;
					font-weight: bold;
					text-transform: uppercase;
				}
				.aidc-status-proposed {
					background: #fff3cd;
					color: #856404;
				}
				.aidc-status-approved {
					background: #d4edda;
					color: #155724;
				}
				.aidc-status-rejected {
					background: #f8d7da;
					color: #721c24;
				}
				.aidc-shortcode-owner,
				.aidc-shortcode-due {
					font-size: 11px;
					color: #666;
				}
				.aidc-shortcode-excerpt {
					color: #555;
					margin-bottom: 10px;
				}
				.aidc-shortcode-date {
					font-size: 11px;
					color: #999;
					text-align: right;
				}
				@media (max-width: 600px) {
					.aidc-shortcode-grid {
						grid-template-columns: 1fr;
					}
					.aidc-shortcode-filter-form {
						flex-direction: column;
						align-items: stretch;
					}
				}
			</style>
		</div>
		<?php
		wp_reset_postdata();
		return ob_get_clean();
	}

	/**
	 * Handle [decision-card] shortcode for single decision card.
	 *
	 * @since 1.0.0
	 * @param array $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function shortcode_single_decision_card( $atts ) {
		$atts = shortcode_atts(
			array(
				'id'          => '',
				'show_meta'   => 'yes',
				'excerpt_only' => 'no',
			),
			$atts,
			'decision-card'
		);

		// Validate ID
		$post_id = intval( $atts['id'] );
		if ( ! $post_id ) {
			return '<p>' . esc_html__( 'Error: Decision Card ID is required.', 'ai-decision-cards' ) . '</p>';
		}

		// Get the post
		$post = get_post( $post_id );
		if ( ! $post || 'decision_card' !== $post->post_type || 'publish' !== $post->post_status ) {
			return '<p>' . esc_html__( 'Error: Decision Card not found or not published.', 'ai-decision-cards' ) . '</p>';
		}

		// Sanitize attributes
		$show_meta = ( 'yes' === $atts['show_meta'] || 'true' === $atts['show_meta'] );
		$excerpt_only = ( 'yes' === $atts['excerpt_only'] || 'true' === $atts['excerpt_only'] );

		// Get meta data
		$status = get_post_meta( $post_id, '_aidc_status', true );
		$owner = get_post_meta( $post_id, '_aidc_owner', true );
		$due = get_post_meta( $post_id, '_aidc_due', true );

		ob_start();
		?>
		<div class="aidc-single-card-wrapper">
			<?php if ( $show_meta ) : ?>
				<div class="aidc-single-meta-banner">
					<?php
					printf(
						/* translators: %1$s: status, %2$s: owner, %3$s: due date */
						__( 'Status: %1$s | Owner: %2$s | Target: %3$s', 'ai-decision-cards' ),
						'<strong>' . ( $status ? esc_html( $status ) : 'TBD' ) . '</strong>',
						'<strong>' . ( $owner ? esc_html( $owner ) : 'TBD' ) . '</strong>',
						'<strong>' . ( $due ? esc_html( $due ) : 'TBD' ) . '</strong>'
					);
					?>
				</div>
			<?php endif; ?>

			<div class="aidc-single-card-content">
				<h3 class="aidc-single-card-title">
					<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" target="_blank">
						<?php echo esc_html( $post->post_title ); ?>
					</a>
				</h3>

				<?php if ( $excerpt_only ) : ?>
					<div class="aidc-single-card-excerpt">
						<?php
						$content = $post->post_content;
						// Extract summary or first paragraph
						if ( preg_match( '/<h2>Summary<\/h2>\s*<ul>(.*?)<\/ul>/s', $content, $matches ) ) {
							echo wp_kses_post( $matches[1] );
						} else {
							echo wp_trim_words( wp_strip_all_tags( $content ), 30 );
						}
						?>
					</div>
				<?php else : ?>
					<div class="aidc-single-card-full">
						<?php echo wp_kses_post( $post->post_content ); ?>
					</div>
				<?php endif; ?>

				<div class="aidc-single-card-footer">
					<span class="aidc-single-card-date"><?php echo get_the_date( '', $post_id ); ?></span>
					<a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" target="_blank" class="aidc-single-card-link">
						<?php esc_html_e( 'View Full Decision Card', 'ai-decision-cards' ); ?>
					</a>
				</div>
			</div>

			<style>
				.aidc-single-card-wrapper {
					border: 1px solid #ddd;
					border-radius: 8px;
					padding: 20px;
					margin: 20px 0;
					background: white;
					box-shadow: 0 2px 4px rgba(0,0,0,0.1);
				}
				
				.aidc-single-meta-banner {
					background-color: #f9f9f9;
					border: 1px solid #ddd;
					padding: 12px 16px;
					margin-bottom: 20px;
					font-size: 14px;
					line-height: 1.4;
					color: #333;
					border-radius: 4px;
				}
				
				.aidc-single-card-title {
					margin: 0 0 15px 0;
					font-size: 1.3em;
					line-height: 1.3;
				}
				
				.aidc-single-card-title a {
					color: #2c3e50;
					text-decoration: none;
				}
				
				.aidc-single-card-title a:hover {
					color: #0073aa;
				}
				
				.aidc-single-card-content {
					line-height: 1.6;
				}
				
				.aidc-single-card-excerpt {
					color: #555;
					margin-bottom: 15px;
				}
				
				.aidc-single-card-excerpt ul {
					margin: 0;
					padding-left: 20px;
				}
				
				.aidc-single-card-full h2 {
					color: #2c3e50;
					border-bottom: 2px solid #f0f0f0;
					padding-bottom: 5px;
					margin-top: 25px;
					margin-bottom: 15px;
				}
				
				.aidc-single-card-full h2:first-child {
					margin-top: 0;
				}
				
				.aidc-single-card-full ul {
					margin: 10px 0;
					padding-left: 25px;
				}
				
				.aidc-single-card-full blockquote {
					background: #f9f9f9;
					border-left: 4px solid #0073aa;
					margin: 15px 0;
					padding: 10px 15px;
					font-style: italic;
					color: #666;
				}
				
				.aidc-single-card-footer {
					display: flex;
					justify-content: space-between;
					align-items: center;
					margin-top: 20px;
					padding-top: 15px;
					border-top: 1px solid #f0f0f0;
					font-size: 14px;
				}
				
				.aidc-single-card-date {
					color: #999;
				}
				
				.aidc-single-card-link {
					color: #0073aa;
					text-decoration: none;
					font-weight: 500;
				}
				
				.aidc-single-card-link:hover {
					text-decoration: underline;
				}
				
				@media (max-width: 600px) {
					.aidc-single-card-wrapper {
						padding: 15px;
					}
					
					.aidc-single-card-footer {
						flex-direction: column;
						align-items: flex-start;
						gap: 10px;
					}
				}
			</style>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Add shortcode meta box to decision card edit screen.
	 *
	 * @since 1.0.0
	 */
	public function add_shortcode_meta_box() {
		add_meta_box(
			'aidc_shortcode_info',
			__( 'Embed This Decision Card', 'ai-decision-cards' ),
			array( $this, 'render_shortcode_meta_box' ),
			'decision_card',
			'side',
			'high'
		);
	}

	/**
	 * Render shortcode meta box content.
	 *
	 * @since 1.0.0
	 * @param WP_Post $post The current post object.
	 */
	public function render_shortcode_meta_box( $post ) {
		?>
		<div class="aidc-shortcode-meta-box">
			<p><?php esc_html_e( 'Use these shortcodes to embed this Decision Card in pages or posts:', 'ai-decision-cards' ); ?></p>
			
			<div style="margin-bottom: 15px;">
				<label style="font-weight: bold; display: block; margin-bottom: 5px;">
					<?php esc_html_e( 'Full Decision Card:', 'ai-decision-cards' ); ?>
				</label>
				<input type="text" 
					   value="[decision-card id=&quot;<?php echo esc_attr( $post->ID ); ?>&quot;]" 
					   readonly 
					   onclick="this.select()" 
					   style="width: 100%; font-family: monospace; background: #f9f9f9; padding: 5px; border: 1px solid #ddd; border-radius: 3px;">
				<p class="description" style="margin-top: 5px;">
					<?php esc_html_e( 'Displays the complete Decision Card with status banner', 'ai-decision-cards' ); ?>
				</p>
			</div>
			
			<div style="margin-bottom: 15px;">
				<label style="font-weight: bold; display: block; margin-bottom: 5px;">
					<?php esc_html_e( 'Summary Only:', 'ai-decision-cards' ); ?>
				</label>
				<input type="text" 
					   value="[decision-card id=&quot;<?php echo esc_attr( $post->ID ); ?>&quot; excerpt_only=&quot;yes&quot;]" 
					   readonly 
					   onclick="this.select()" 
					   style="width: 100%; font-family: monospace; background: #f9f9f9; padding: 5px; border: 1px solid #ddd; border-radius: 3px;">
				<p class="description" style="margin-top: 5px;">
					<?php esc_html_e( 'Shows only the summary section for quick reference', 'ai-decision-cards' ); ?>
				</p>
			</div>
			
			<div style="margin-bottom: 15px;">
				<label style="font-weight: bold; display: block; margin-bottom: 5px;">
					<?php esc_html_e( 'Without Status Banner:', 'ai-decision-cards' ); ?>
				</label>
				<input type="text" 
					   value="[decision-card id=&quot;<?php echo esc_attr( $post->ID ); ?>&quot; show_meta=&quot;no&quot;]" 
					   readonly 
					   onclick="this.select()" 
					   style="width: 100%; font-family: monospace; background: #f9f9f9; padding: 5px; border: 1px solid #ddd; border-radius: 3px;">
				<p class="description" style="margin-top: 5px;">
					<?php esc_html_e( 'Hides the status banner for cleaner display', 'ai-decision-cards' ); ?>
				</p>
			</div>
			
			<div style="background: #e7f3ff; padding: 10px; border-radius: 4px; margin-top: 15px;">
				<p style="margin: 0; font-size: 12px;">
					<strong><?php esc_html_e( 'How to use:', 'ai-decision-cards' ); ?></strong><br>
					<?php esc_html_e( 'Click any shortcode above to select it, then copy (Ctrl+C) and paste into your page editor.', 'ai-decision-cards' ); ?>
				</p>
			</div>
			
			<div style="margin-top: 15px;">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=aidc_settings#shortcode-guide' ) ); ?>" target="_blank" style="text-decoration: none;">
					<?php esc_html_e( 'View Complete Shortcode Guide →', 'ai-decision-cards' ); ?>
				</a>
			</div>
		</div>
		
		<style>
			.aidc-shortcode-meta-box input[readonly] {
				cursor: pointer;
			}
			.aidc-shortcode-meta-box input[readonly]:focus {
				box-shadow: 0 0 5px rgba(0,123,255,0.3);
				border-color: #007cba;
			}
		</style>
		<?php
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

		// Add public Decision Cards Display page (accessible to everyone)
		add_submenu_page(
			'edit.php?post_type=decision_card',
			__( 'View Public Display', 'ai-decision-cards' ),
			__( 'View Public Display', 'ai-decision-cards' ),
			'read',
			'aidc_display',
			array( $this, 'render_display_page' )
		);

		// Add public accessible page that doesn't require admin access
		add_menu_page(
			__( 'Decision Cards Display', 'ai-decision-cards' ),
			__( 'Decision Cards Display', 'ai-decision-cards' ),
			'read',
			'decision-cards-display',
			array( $this, 'render_public_display_page' ),
			'dashicons-yes-alt',
			30
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
			if ( isset( $_POST['aidc_api_type'] ) ) {
				update_option( self::OPTION_API_TYPE, sanitize_text_field( wp_unslash( $_POST['aidc_api_type'] ) ) );
			}
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
							<label for="aidc_api_type"><?php esc_html_e( 'API Type', 'ai-decision-cards' ); ?></label>
						</th>
						<td>
							<select id="aidc_api_type" name="aidc_api_type" onchange="toggleApiFields()">
								<option value="openai" <?php selected( $this->esc_attr_val( self::OPTION_API_TYPE, 'openai' ), 'openai' ); ?>>
									<?php esc_html_e( 'OpenAI Compatible', 'ai-decision-cards' ); ?>
								</option>
								<option value="azure" <?php selected( $this->esc_attr_val( self::OPTION_API_TYPE, 'openai' ), 'azure' ); ?>>
									<?php esc_html_e( 'Azure OpenAI', 'ai-decision-cards' ); ?>
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
								   value="<?php echo $this->esc_attr_val( self::OPTION_API_KEY ); ?>" 
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
								   value="<?php echo $this->esc_attr_val( self::OPTION_API_BASE, 'https://api.openai.com/' ); ?>" 
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
								   value="<?php echo $this->esc_attr_val( self::OPTION_MODEL, 'gpt-3.5-turbo' ); ?>" 
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
		<script type="text/javascript">
		function toggleApiFields() {
			const apiType = document.getElementById('aidc_api_type').value;
			const apiKeyLabel = document.getElementById('api_key_label');
			const apiKeyDesc = document.getElementById('api_key_desc');
			const apiBaseLabel = document.getElementById('api_base_label');
			const apiBaseDesc = document.getElementById('api_base_desc');
			const modelLabel = document.getElementById('model_label');
			const modelDesc = document.getElementById('model_desc');
			
			if (apiType === 'azure') {
				apiKeyLabel.textContent = '<?php esc_attr_e( "Azure OpenAI API Key", "ai-decision-cards" ); ?>';
				apiKeyDesc.textContent = '<?php esc_attr_e( "Your Azure OpenAI API key from Azure portal.", "ai-decision-cards" ); ?>';
				apiBaseLabel.textContent = '<?php esc_attr_e( "Azure Endpoint", "ai-decision-cards" ); ?>';
				apiBaseDesc.textContent = '<?php esc_attr_e( "Example: https://your-resource.openai.azure.com/", "ai-decision-cards" ); ?>';
				modelLabel.textContent = '<?php esc_attr_e( "Deployment Name", "ai-decision-cards" ); ?>';
				modelDesc.textContent = '<?php esc_attr_e( "Your Azure OpenAI deployment name (e.g. gpt-35-turbo).", "ai-decision-cards" ); ?>';
			} else {
				apiKeyLabel.textContent = '<?php esc_attr_e( "API Key", "ai-decision-cards" ); ?>';
				apiKeyDesc.textContent = '<?php esc_attr_e( "Your API key for OpenAI, OpenRouter, or other compatible services.", "ai-decision-cards" ); ?>';
				apiBaseLabel.textContent = '<?php esc_attr_e( "API Base URL", "ai-decision-cards" ); ?>';
				apiBaseDesc.textContent = '<?php esc_attr_e( "Example: https://api.openai.com/ or https://openrouter.ai/api/v1/", "ai-decision-cards" ); ?>';
				modelLabel.textContent = '<?php esc_attr_e( "Model", "ai-decision-cards" ); ?>';
				modelDesc.textContent = '<?php esc_attr_e( "Example: gpt-3.5-turbo, gpt-4, claude-3-haiku, etc.", "ai-decision-cards" ); ?>';
			}
		}
		
		// Initialize on page load
		document.addEventListener('DOMContentLoaded', function() {
			toggleApiFields();
		});
		
		// Handle API test button click
		document.getElementById('aidc_test_api').addEventListener('click', function() {
			const button = this;
			const resultDiv = document.getElementById('aidc_test_result');
			
			// Get current form values
			const apiType = document.getElementById('aidc_api_type').value;
			const apiKey = document.getElementById('aidc_api_key').value;
			const apiBase = document.getElementById('aidc_api_base').value;
			const model = document.getElementById('aidc_model').value;
			
			// Basic validation
			if (!apiKey.trim()) {
				resultDiv.innerHTML = '<div class="notice notice-error"><p><?php esc_html_e( "Please enter an API key first.", "ai-decision-cards" ); ?></p></div>';
				return;
			}
			
			// Show loading state
			button.disabled = true;
			button.textContent = '<?php esc_attr_e( "Testing...", "ai-decision-cards" ); ?>';
			resultDiv.innerHTML = '<div class="notice notice-info"><p><?php esc_html_e( "Testing API connection...", "ai-decision-cards" ); ?></p></div>';
			
			// Prepare AJAX request
			const formData = new FormData();
			formData.append('action', 'aidc_test_api');
			formData.append('aidc_test_nonce', '<?php echo esc_js( wp_create_nonce( "aidc_test_api" ) ); ?>');
			formData.append('api_type', apiType);
			formData.append('api_key', apiKey);
			formData.append('api_base', apiBase);
			formData.append('model', model);
			
			// Send AJAX request
			fetch('<?php echo esc_url( admin_url( "admin-ajax.php" ) ); ?>', {
				method: 'POST',
				body: formData
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					resultDiv.innerHTML = '<div class="notice notice-success"><p>' + data.data + '</p></div>';
				} else {
					resultDiv.innerHTML = '<div class="notice notice-error"><p>' + data.data + '</p></div>';
				}
			})
			.catch(error => {
				resultDiv.innerHTML = '<div class="notice notice-error"><p><?php esc_html_e( "Network error occurred while testing API.", "ai-decision-cards" ); ?></p></div>';
			})
			.finally(() => {
				button.disabled = false;
				button.textContent = '<?php esc_attr_e( "Test API Key", "ai-decision-cards" ); ?>';
			});
		});
		</script>

		<!-- Shortcode Usage Guide -->
		<div style="margin-top: 40px;">
			<h2><?php esc_html_e( 'Shortcode Usage Guide', 'ai-decision-cards' ); ?></h2>
			<p><?php esc_html_e( 'Use these shortcodes to display Decision Cards on your website pages and posts.', 'ai-decision-cards' ); ?></p>
			
			<div style="background: #f9f9f9; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
				<h3><?php esc_html_e( 'Display Decision Cards List', 'ai-decision-cards' ); ?></h3>
				<p><?php esc_html_e( 'Show a grid of Decision Cards with optional filtering:', 'ai-decision-cards' ); ?></p>
				
				<div style="background: white; padding: 15px; border-radius: 4px; font-family: monospace; margin: 10px 0;">
					<div style="margin-bottom: 8px;"><code>[decision-cards-list]</code> <span style="color: #666;"><?php esc_html_e( '— Display all Decision Cards', 'ai-decision-cards' ); ?></span></div>
					<div style="margin-bottom: 8px;"><code>[decision-cards-list limit="5"]</code> <span style="color: #666;"><?php esc_html_e( '— Show only 5 cards', 'ai-decision-cards' ); ?></span></div>
					<div style="margin-bottom: 8px;"><code>[decision-cards-list status="Approved"]</code> <span style="color: #666;"><?php esc_html_e( '— Show only approved cards', 'ai-decision-cards' ); ?></span></div>
					<div style="margin-bottom: 8px;"><code>[decision-cards-list owner="John"]</code> <span style="color: #666;"><?php esc_html_e( '— Filter by owner', 'ai-decision-cards' ); ?></span></div>
					<div><code>[decision-cards-list show_filters="no"]</code> <span style="color: #666;"><?php esc_html_e( '— Hide search filters', 'ai-decision-cards' ); ?></span></div>
				</div>
				
				<h4><?php esc_html_e( 'Available Parameters:', 'ai-decision-cards' ); ?></h4>
				<ul>
					<li><strong>limit</strong>: <?php esc_html_e( 'Number of cards to display (1-50, default: 10)', 'ai-decision-cards' ); ?></li>
					<li><strong>status</strong>: <?php esc_html_e( 'Filter by status (Proposed, Approved, Rejected)', 'ai-decision-cards' ); ?></li>
					<li><strong>owner</strong>: <?php esc_html_e( 'Filter by owner name', 'ai-decision-cards' ); ?></li>
					<li><strong>search</strong>: <?php esc_html_e( 'Search in card titles and content', 'ai-decision-cards' ); ?></li>
					<li><strong>show_filters</strong>: <?php esc_html_e( 'Show/hide filter form (yes/no, default: yes)', 'ai-decision-cards' ); ?></li>
				</ul>
			</div>
			
			<div style="background: #f0f8ff; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
				<h3><?php esc_html_e( 'Display Single Decision Card', 'ai-decision-cards' ); ?></h3>
				<p><?php esc_html_e( 'Embed a specific Decision Card by its ID:', 'ai-decision-cards' ); ?></p>
				
				<div style="background: white; padding: 15px; border-radius: 4px; font-family: monospace; margin: 10px 0;">
					<div style="margin-bottom: 8px;"><code>[decision-card id="123"]</code> <span style="color: #666;"><?php esc_html_e( '— Display full Decision Card', 'ai-decision-cards' ); ?></span></div>
					<div style="margin-bottom: 8px;"><code>[decision-card id="123" excerpt_only="yes"]</code> <span style="color: #666;"><?php esc_html_e( '— Show only summary', 'ai-decision-cards' ); ?></span></div>
					<div><code>[decision-card id="123" show_meta="no"]</code> <span style="color: #666;"><?php esc_html_e( '— Hide status banner', 'ai-decision-cards' ); ?></span></div>
				</div>
				
				<h4><?php esc_html_e( 'Available Parameters:', 'ai-decision-cards' ); ?></h4>
				<ul>
					<li><strong>id</strong>: <?php esc_html_e( 'Decision Card ID (required - find this in the URL when editing)', 'ai-decision-cards' ); ?></li>
					<li><strong>show_meta</strong>: <?php esc_html_e( 'Show status banner (yes/no, default: yes)', 'ai-decision-cards' ); ?></li>
					<li><strong>excerpt_only</strong>: <?php esc_html_e( 'Show only summary instead of full content (yes/no, default: no)', 'ai-decision-cards' ); ?></li>
				</ul>
			</div>
			
			<div style="background: #fff2cc; padding: 15px; border-radius: 8px; border-left: 4px solid #ffcc00;">
				<h4><?php esc_html_e( 'Quick Copy', 'ai-decision-cards' ); ?></h4>
				<p><?php esc_html_e( 'You can copy these shortcodes and paste them directly into any page or post editor.', 'ai-decision-cards' ); ?></p>
				<p><?php esc_html_e( 'To find a Decision Card ID, edit the card and look at the URL: ...&post=123 (where 123 is the ID)', 'ai-decision-cards' ); ?></p>
			</div>
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
                <?php submit_button('Generate Summary & Create Draft', 'primary', 'submit', true, ['id' => 'aidc_generate_btn']); ?>
                <div id="aidc_generate_status" style="margin-top: 15px; display: none;"></div>
            </form>
        </div>
        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action*="admin-post.php"]');
            const button = document.getElementById('aidc_generate_btn');
            const statusDiv = document.getElementById('aidc_generate_status');
            
            if (form && button && statusDiv) {
                form.addEventListener('submit', function(e) {
                    // Basic validation
                    const conversation = document.getElementById('aidc_conversation').value.trim();
                    if (!conversation) {
                        statusDiv.style.display = 'block';
                        statusDiv.innerHTML = '<div class="notice notice-error"><p><?php esc_html_e( "Please enter a conversation before generating.", "ai-decision-cards" ); ?></p></div>';
                        e.preventDefault();
                        return false;
                    }
                    
                    // Show loading state
                    button.disabled = true;
                    button.value = '<?php esc_attr_e( "Generating... Please wait", "ai-decision-cards" ); ?>';
                    statusDiv.style.display = 'block';
                    statusDiv.innerHTML = '<div class="notice notice-info"><p><?php esc_html_e( "🤖 AI is analyzing your conversation and creating a decision card... This may take 10-30 seconds.", "ai-decision-cards" ); ?></p></div>';
                    
                    // Set a timeout to re-enable the button in case something goes wrong
                    setTimeout(function() {
                        if (button.disabled) {
                            button.disabled = false;
                            button.value = '<?php esc_attr_e( "Generate Summary & Create Draft", "ai-decision-cards" ); ?>';
                            statusDiv.innerHTML = '<div class="notice notice-warning"><p><?php esc_html_e( "Taking longer than expected. Please check your API settings or try again.", "ai-decision-cards" ); ?></p></div>';
                        }
                    }, 45000); // 45 seconds timeout
                    
                    return true;
                });
            }
        });
        </script>
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

        $api_type = get_option(self::OPTION_API_TYPE, 'openai');
        $api_key  = get_option(self::OPTION_API_KEY);
        $api_base = rtrim((string) get_option(self::OPTION_API_BASE, 'https://api.openai.com/'), '/') . '/';
        $model    = (string) get_option(self::OPTION_MODEL, 'gpt-3.5-turbo');

        if (!$api_key) {
            $this->redirect_with_notice('Missing API key. Set it in Settings.', 'error');
        }

        $prompt_system = "You convert Slack-like conversations into a Decision Card in strict HTML with these sections, in this exact order:

<h2>Decision</h2>
<p>(One sentence. What was decided.)</p>

<h2>Summary</h2>
<ul>
<li>(Exactly 3 concise bullets. Why/what changed, key rationale. Use only facts from the conversation.)</li>
</ul>

<h2>Action Items</h2>
<ul>
<li><strong>Owner</strong> — task. Include \"Due: <YYYY-MM-DD>\" if an exact date is present in the conversation; otherwise \"Due: TBD\".
If the conversation uses relative time (e.g., \"next week\", \"the week after\"), KEEP the phrase and ADD a follow-up item like:
\"<strong>Alice</strong> — set exact date for '<relative phrase>' (Due: TBD)\".</li>
</ul>

<h2>Sources</h2>
<blockquote>
<p>(Quote 2–3 short lines from the conversation that directly support the decision, with the original timestamps/names.)</p>
</blockquote>

<h2>Risks / Assumptions</h2>
<ul>
<li>(1–2 bullets on risks, unknowns, or assumptions mentioned or clearly implied in the conversation. If none, output \"None\".)</li>
</ul>

Rules:
- Use only facts from the conversation. If uncertain, say \"TBD\" rather than inventing details.
- Keep neutral, professional tone.
- Output HTML only. No extra preface or epilogue.
- Use proper HTML tags: h2 for sections, ul/li for lists, p for paragraphs, strong for emphasis, blockquote for quotes.";

        $body = [
            'model' => $model,
            'temperature' => 0.2,
            'max_tokens' => 600,
            'messages' => [
                ['role' => 'system', 'content' => $prompt_system],
                ['role' => 'user', 'content' => "Conversation:\n" . $conversation]
            ]
        ];

        // Build endpoint and headers based on API type
        if ($api_type === 'azure') {
            // Azure OpenAI format: https://your-resource.openai.azure.com/openai/deployments/your-deployment/chat/completions?api-version=2024-02-01
            $endpoint = $api_base . 'openai/deployments/' . $model . '/chat/completions?api-version=2024-02-01';
            $headers = [
                'api-key' => $api_key,
                'Content-Type' => 'application/json'
            ];
            // Remove model from body for Azure
            unset($body['model']);
        } else {
            // OpenAI Compatible format - check if API base already contains v1
            if (strpos($api_base, '/v1/') !== false) {
                // API base already contains v1 (e.g., OpenRouter)
                $endpoint = $api_base . 'chat/completions';
            } else {
                // Standard OpenAI format
                $endpoint = $api_base . 'v1/chat/completions';
            }
            $headers = [
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ];
        }

        $args = [
            'headers' => $headers,
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
            'h2' => [], 'h3' => [], 'p' => [], 'ul' => [], 'ol' => [], 'li' => [], 'strong' => [], 'em' => [], 'code' => [], 'blockquote' => [], 'br' => []
        ];
        $content = wp_kses($ai, $allowed);

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
	 * Handle API test AJAX request.
	 *
	 * @since 1.0.0
	 */
	public function handle_api_test() {
		// Check permissions and nonce
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Insufficient permissions.', 'ai-decision-cards' ) );
		}
		
		if ( ! isset( $_POST['aidc_test_nonce'] ) || ! wp_verify_nonce( $_POST['aidc_test_nonce'], 'aidc_test_api' ) ) {
			wp_send_json_error( __( 'Security check failed.', 'ai-decision-cards' ) );
		}
		
		// Get form data
		$api_type = isset( $_POST['api_type'] ) ? sanitize_text_field( $_POST['api_type'] ) : 'openai';
		$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( $_POST['api_key'] ) : '';
		$api_base = isset( $_POST['api_base'] ) ? sanitize_text_field( $_POST['api_base'] ) : '';
		$model = isset( $_POST['model'] ) ? sanitize_text_field( $_POST['model'] ) : '';
		
		// Validate inputs
		if ( empty( $api_key ) ) {
			wp_send_json_error( __( 'API key is required.', 'ai-decision-cards' ) );
		}
		
		if ( empty( $api_base ) ) {
			wp_send_json_error( __( 'API base URL is required.', 'ai-decision-cards' ) );
		}
		
		if ( empty( $model ) ) {
			wp_send_json_error( __( 'Model is required.', 'ai-decision-cards' ) );
		}
		
		// Ensure API base ends with slash
		$api_base = rtrim( $api_base, '/' ) . '/';
		
		// Prepare test request body
		$body = array(
			'model' => $model,
			'temperature' => 0,
			'max_tokens' => 10,
			'messages' => array(
				array(
					'role' => 'user',
					'content' => 'Hello'
				)
			)
		);
		
		// Build endpoint and headers based on API type
		if ( $api_type === 'azure' ) {
			// Azure OpenAI format
			$endpoint = $api_base . 'openai/deployments/' . $model . '/chat/completions?api-version=2024-02-01';
			$headers = array(
				'api-key' => $api_key,
				'Content-Type' => 'application/json'
			);
			// Remove model from body for Azure
			unset( $body['model'] );
		} else {
			// OpenAI Compatible format - check if API base already contains v1
			if ( strpos( $api_base, '/v1/' ) !== false ) {
				// API base already contains v1 (e.g., OpenRouter)
				$endpoint = $api_base . 'chat/completions';
			} else {
				// Standard OpenAI format
				$endpoint = $api_base . 'v1/chat/completions';
			}
			$headers = array(
				'Authorization' => 'Bearer ' . $api_key,
				'Content-Type' => 'application/json'
			);
		}
		
		// Make the API request
		$args = array(
			'headers' => $headers,
			'timeout' => 15,
			'body' => wp_json_encode( $body )
		);
		
		$response = wp_remote_post( $endpoint, $args );
		
		// Check for WordPress HTTP errors
		if ( is_wp_error( $response ) ) {
			wp_send_json_error(
				sprintf(
					/* translators: %s: Error message */
					__( 'Connection failed: %s', 'ai-decision-cards' ),
					$response->get_error_message()
				)
			);
		}
		
		// Check HTTP response code
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );
		
		if ( $response_code < 200 || $response_code >= 300 ) {
			// Try to extract error message from response
			$error_data = json_decode( $response_body, true );
			$error_message = '';
			
			if ( $error_data && isset( $error_data['error']['message'] ) ) {
				$error_message = $error_data['error']['message'];
			} else {
				$error_message = sprintf(
					/* translators: %1$d: HTTP status code, %2$s: Response body */
					__( 'HTTP %1$d: %2$s', 'ai-decision-cards' ),
					$response_code,
					$response_body
				);
			}
			
			// Add debug info for 405 errors
			if ( $response_code === 405 ) {
				$debug_info = sprintf(
					/* translators: %s: API endpoint URL */
					__( ' (Endpoint: %s)', 'ai-decision-cards' ),
					$endpoint
				);
				$error_message .= $debug_info;
			}
			
			wp_send_json_error(
				sprintf(
					/* translators: %s: Error message */
					__( 'API Error: %s', 'ai-decision-cards' ),
					$error_message
				)
			);
		}
		
		// Try to parse the JSON response
		$data = json_decode( $response_body, true );
		if ( ! $data ) {
			wp_send_json_error( __( 'Invalid JSON response from API.', 'ai-decision-cards' ) );
		}
		
		// Check if response has expected structure
		if ( ! isset( $data['choices'] ) || ! isset( $data['choices'][0] ) ) {
			wp_send_json_error( __( 'Unexpected API response format.', 'ai-decision-cards' ) );
		}
		
		// Success!
		$provider_name = ( $api_type === 'azure' ) ? 'Azure OpenAI' : 'OpenAI Compatible API';
		wp_send_json_success(
			sprintf(
				/* translators: %s: API provider name */
				__( '✓ API connection successful! Connected to %s.', 'ai-decision-cards' ),
				$provider_name
			)
		);
	}

	/**
	 * Prepend meta banner to Decision Card content.
	 *
	 * @since 1.0.0
	 * @param string $content The post content.
	 * @return string Modified content with meta banner.
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
	 * Render display page (admin area).
	 *
	 * @since 1.0.0
	 */
	public function render_display_page() {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Decision Cards Display', 'ai-decision-cards' ); ?></h1>
			<p><?php esc_html_e( 'Preview how Decision Cards appear to website visitors.', 'ai-decision-cards' ); ?></p>
			<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=decision-cards-display' ) ); ?>" target="_blank" class="button button-secondary"><?php esc_html_e( 'View Public Display Page', 'ai-decision-cards' ); ?></a></p>
			<?php $this->render_cards_list(); ?>
		</div>
		<?php
	}

	/**
	 * Render public display page.
	 *
	 * @since 1.0.0
	 */
	public function render_public_display_page() {
		?>
		<!DOCTYPE html>
		<html <?php language_attributes(); ?>>
		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<title><?php esc_html_e( 'Decision Cards Display', 'ai-decision-cards' ); ?> - <?php bloginfo( 'name' ); ?></title>
			<?php $this->render_display_styles(); ?>
		</head>
		<body class="aidc-public-display">
			<div class="aidc-container">
				<header class="aidc-header">
					<h1><?php esc_html_e( 'Decision Cards', 'ai-decision-cards' ); ?></h1>
					<p><?php esc_html_e( 'AI-generated decision summaries from team conversations', 'ai-decision-cards' ); ?></p>
				</header>
				
				<div class="aidc-embed-info">
					<div class="aidc-embed-toggle">
						<button type="button" onclick="toggleEmbedInfo()" class="aidc-embed-button">
							<span class="dashicons dashicons-admin-page"></span>
							<?php esc_html_e( 'How to Embed These Cards', 'ai-decision-cards' ); ?>
						</button>
					</div>
					<div id="aidc-embed-content" class="aidc-embed-content" style="display: none;">
						<h3><?php esc_html_e( 'Embed Decision Cards on Your Website', 'ai-decision-cards' ); ?></h3>
						<p><?php esc_html_e( 'You can easily embed these Decision Cards in your pages and posts using shortcodes:', 'ai-decision-cards' ); ?></p>
						
						<div class="aidc-embed-examples">
							<div class="aidc-embed-example">
								<h4><?php esc_html_e( 'Display All Cards:', 'ai-decision-cards' ); ?></h4>
								<code>[decision-cards-list]</code>
								<p><?php esc_html_e( 'Shows a grid of all Decision Cards with filtering options', 'ai-decision-cards' ); ?></p>
							</div>
							
							<div class="aidc-embed-example">
								<h4><?php esc_html_e( 'Display Specific Card:', 'ai-decision-cards' ); ?></h4>
								<code>[decision-card id="123"]</code>
								<p><?php esc_html_e( 'Shows a single Decision Card (replace 123 with the actual card ID)', 'ai-decision-cards' ); ?></p>
							</div>
							
							<div class="aidc-embed-example">
								<h4><?php esc_html_e( 'Filter by Status:', 'ai-decision-cards' ); ?></h4>
								<code>[decision-cards-list status="Approved"]</code>
								<p><?php esc_html_e( 'Shows only approved Decision Cards', 'ai-decision-cards' ); ?></p>
							</div>
						</div>
						
						<p>
							<a href="<?php echo esc_url( admin_url( 'admin.php?page=aidc_settings#shortcode-guide' ) ); ?>" target="_blank" class="aidc-guide-link">
								<?php esc_html_e( 'View Complete Shortcode Guide →', 'ai-decision-cards' ); ?>
							</a>
						</p>
					</div>
				</div>
				
				<?php $this->render_search_filters(); ?>
				<?php $this->render_cards_list(); ?>
			</div>
		</body>
		</html>
		<?php
	}

	/**
	 * Render cards list with pagination.
	 *
	 * @since 1.0.0
	 */
	private function render_cards_list() {
		// Get current page and search parameters
		$paged = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
		$search = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
		$status_filter = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';
		$owner_filter = isset( $_GET['owner'] ) ? sanitize_text_field( $_GET['owner'] ) : '';

		// Build query arguments
		$args = array(
			'post_type'      => 'decision_card',
			'post_status'    => 'publish',
			'posts_per_page' => 10,
			'paged'          => $paged,
		);

		// Add search
		if ( ! empty( $search ) ) {
			$args['s'] = $search;
		}

		// Add meta query for filters
		$meta_query = array();
		if ( ! empty( $status_filter ) ) {
			$meta_query[] = array(
				'key'     => '_aidc_status',
				'value'   => $status_filter,
				'compare' => '=',
			);
		}
		if ( ! empty( $owner_filter ) ) {
			$meta_query[] = array(
				'key'     => '_aidc_owner',
				'value'   => $owner_filter,
				'compare' => 'LIKE',
			);
		}
		if ( ! empty( $meta_query ) ) {
			$args['meta_query'] = $meta_query;
		}

		$query = new WP_Query( $args );

		if ( $query->have_posts() ) : ?>
			<div class="aidc-cards-grid">
				<?php while ( $query->have_posts() ) : $query->the_post(); ?>
					<div class="aidc-card">
						<h3 class="aidc-card-title">
							<a href="<?php echo esc_url( get_permalink() ); ?>" target="_blank">
								<?php the_title(); ?>
							</a>
						</h3>
						
						<?php
						$status = get_post_meta( get_the_ID(), '_aidc_status', true );
						$owner = get_post_meta( get_the_ID(), '_aidc_owner', true );
						$due = get_post_meta( get_the_ID(), '_aidc_due', true );
						?>
						
						<div class="aidc-card-meta">
							<span class="aidc-status aidc-status-<?php echo esc_attr( strtolower( $status ) ); ?>">
								<?php echo $status ? esc_html( $status ) : 'TBD'; ?>
							</span>
							<?php if ( $owner ) : ?>
								<span class="aidc-owner"><?php esc_html_e( 'Owner:', 'ai-decision-cards' ); ?> <?php echo esc_html( $owner ); ?></span>
							<?php endif; ?>
							<?php if ( $due ) : ?>
								<span class="aidc-due"><?php esc_html_e( 'Due:', 'ai-decision-cards' ); ?> <?php echo esc_html( $due ); ?></span>
							<?php endif; ?>
						</div>
						
						<div class="aidc-card-excerpt">
							<?php
							$content = get_the_content();
							// Extract summary or first paragraph
							if ( preg_match( '/<h2>Summary<\/h2>\s*<ul>(.*?)<\/ul>/s', $content, $matches ) ) {
								echo wp_kses_post( $matches[1] );
							} else {
								echo wp_trim_words( wp_strip_all_tags( $content ), 20 );
							}
							?>
						</div>
						
						<div class="aidc-card-date">
							<?php echo get_the_date(); ?>
						</div>
					</div>
				<?php endwhile; ?>
			</div>

			<?php
			// Pagination
			if ( $query->max_num_pages > 1 ) :
				$current_url = remove_query_arg( 'paged' );
				?>
				<div class="aidc-pagination">
					<?php for ( $i = 1; $i <= $query->max_num_pages; $i++ ) : ?>
						<?php if ( $i == $paged ) : ?>
							<span class="current"><?php echo $i; ?></span>
						<?php else : ?>
							<a href="<?php echo esc_url( add_query_arg( 'paged', $i, $current_url ) ); ?>"><?php echo $i; ?></a>
						<?php endif; ?>
					<?php endfor; ?>
				</div>
			<?php endif; ?>

		<?php else : ?>
			<div class="aidc-no-cards">
				<p><?php esc_html_e( 'No Decision Cards found.', 'ai-decision-cards' ); ?></p>
				<p><a href="<?php echo esc_url( admin_url( 'edit.php?post_type=decision_card&page=aidc_generate' ) ); ?>"><?php esc_html_e( 'Generate your first Decision Card', 'ai-decision-cards' ); ?></a></p>
			</div>
		<?php endif;

		wp_reset_postdata();
	}

	/**
	 * Render search and filter form.
	 *
	 * @since 1.0.0
	 */
	private function render_search_filters() {
		$search = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
		$status_filter = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '';
		$owner_filter = isset( $_GET['owner'] ) ? sanitize_text_field( $_GET['owner'] ) : '';
		?>
		<div class="aidc-filters">
			<form method="get" class="aidc-filter-form">
				<input type="hidden" name="page" value="decision-cards-display">
				
				<div class="aidc-filter-group">
					<input type="text" 
						   name="search" 
						   value="<?php echo esc_attr( $search ); ?>" 
						   placeholder="<?php esc_attr_e( 'Search decision cards...', 'ai-decision-cards' ); ?>"
						   class="aidc-search-input">
				</div>
				
				<div class="aidc-filter-group">
					<select name="status" class="aidc-filter-select">
						<option value=""><?php esc_html_e( 'All Statuses', 'ai-decision-cards' ); ?></option>
						<option value="Proposed" <?php selected( $status_filter, 'Proposed' ); ?>><?php esc_html_e( 'Proposed', 'ai-decision-cards' ); ?></option>
						<option value="Approved" <?php selected( $status_filter, 'Approved' ); ?>><?php esc_html_e( 'Approved', 'ai-decision-cards' ); ?></option>
						<option value="Rejected" <?php selected( $status_filter, 'Rejected' ); ?>><?php esc_html_e( 'Rejected', 'ai-decision-cards' ); ?></option>
					</select>
				</div>
				
				<div class="aidc-filter-group">
					<input type="text" 
						   name="owner" 
						   value="<?php echo esc_attr( $owner_filter ); ?>" 
						   placeholder="<?php esc_attr_e( 'Filter by owner...', 'ai-decision-cards' ); ?>"
						   class="aidc-owner-input">
				</div>
				
				<div class="aidc-filter-group">
					<button type="submit" class="aidc-filter-button"><?php esc_html_e( 'Filter', 'ai-decision-cards' ); ?></button>
					<a href="?page=decision-cards-display" class="aidc-clear-button"><?php esc_html_e( 'Clear', 'ai-decision-cards' ); ?></a>
				</div>
			</form>
		</div>
		<?php
	}

	/**
	 * Render CSS styles for display page.
	 *
	 * @since 1.0.0
	 */
	private function render_display_styles() {
		?>
		<style>
			.aidc-public-display {
				font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;
				line-height: 1.6;
				color: #333;
				background-color: #f9f9f9;
				margin: 0;
				padding: 0;
			}
			
			.aidc-container {
				max-width: 1200px;
				margin: 0 auto;
				padding: 20px;
			}
			
			.aidc-header {
				text-align: center;
				margin-bottom: 30px;
				padding: 20px;
				background: white;
				border-radius: 8px;
				box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			}
			
			.aidc-header h1 {
				margin: 0 0 10px 0;
				color: #2c3e50;
				font-size: 2.5em;
			}
			
			.aidc-filters {
				background: white;
				padding: 20px;
				border-radius: 8px;
				box-shadow: 0 2px 4px rgba(0,0,0,0.1);
				margin-bottom: 20px;
			}
			
			.aidc-filter-form {
				display: flex;
				gap: 15px;
				flex-wrap: wrap;
				align-items: center;
			}
			
			.aidc-filter-group {
				display: flex;
				align-items: center;
			}
			
			.aidc-search-input,
			.aidc-owner-input,
			.aidc-filter-select {
				padding: 8px 12px;
				border: 1px solid #ddd;
				border-radius: 4px;
				font-size: 14px;
			}
			
			.aidc-search-input {
				width: 250px;
			}
			
			.aidc-owner-input {
				width: 150px;
			}
			
			.aidc-filter-button {
				background: #0073aa;
				color: white;
				padding: 8px 16px;
				border: none;
				border-radius: 4px;
				cursor: pointer;
				font-size: 14px;
			}
			
			.aidc-filter-button:hover {
				background: #005a87;
			}
			
			.aidc-clear-button {
				color: #666;
				text-decoration: none;
				margin-left: 10px;
				padding: 8px 16px;
			}
			
			.aidc-clear-button:hover {
				color: #000;
			}
			
			.aidc-cards-grid {
				display: grid;
				grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
				gap: 20px;
				margin-bottom: 30px;
			}
			
			.aidc-card {
				background: white;
				padding: 20px;
				border-radius: 8px;
				box-shadow: 0 2px 4px rgba(0,0,0,0.1);
				transition: box-shadow 0.2s ease;
			}
			
			.aidc-card:hover {
				box-shadow: 0 4px 8px rgba(0,0,0,0.15);
			}
			
			.aidc-card-title {
				margin: 0 0 15px 0;
				font-size: 1.2em;
			}
			
			.aidc-card-title a {
				color: #2c3e50;
				text-decoration: none;
			}
			
			.aidc-card-title a:hover {
				color: #0073aa;
			}
			
			.aidc-card-meta {
				margin-bottom: 15px;
				display: flex;
				flex-wrap: wrap;
				gap: 10px;
				align-items: center;
			}
			
			.aidc-status {
				padding: 4px 8px;
				border-radius: 4px;
				font-size: 12px;
				font-weight: bold;
				text-transform: uppercase;
			}
			
			.aidc-status-proposed {
				background: #fff3cd;
				color: #856404;
			}
			
			.aidc-status-approved {
				background: #d4edda;
				color: #155724;
			}
			
			.aidc-status-rejected {
				background: #f8d7da;
				color: #721c24;
			}
			
			.aidc-owner,
			.aidc-due {
				font-size: 12px;
				color: #666;
			}
			
			.aidc-card-excerpt {
				color: #555;
				margin-bottom: 15px;
				line-height: 1.5;
			}
			
			.aidc-card-excerpt ul {
				margin: 0;
				padding-left: 20px;
			}
			
			.aidc-card-date {
				font-size: 12px;
				color: #999;
				text-align: right;
			}
			
			.aidc-pagination {
				display: flex;
				justify-content: center;
				gap: 10px;
				padding: 20px 0;
			}
			
			.aidc-pagination a,
			.aidc-pagination .current {
				padding: 8px 12px;
				border: 1px solid #ddd;
				text-decoration: none;
				color: #0073aa;
				border-radius: 4px;
			}
			
			.aidc-pagination .current {
				background: #0073aa;
				color: white;
				border-color: #0073aa;
			}
			
			.aidc-pagination a:hover {
				background: #f0f0f0;
			}
			
			.aidc-no-cards {
				text-align: center;
				padding: 40px;
				background: white;
				border-radius: 8px;
				box-shadow: 0 2px 4px rgba(0,0,0,0.1);
			}
			
			.aidc-no-cards a {
				color: #0073aa;
				text-decoration: none;
			}
			
			.aidc-no-cards a:hover {
				text-decoration: underline;
			}
			
			.aidc-embed-info {
				background: #fff;
				border-radius: 8px;
				box-shadow: 0 2px 4px rgba(0,0,0,0.1);
				margin-bottom: 20px;
				overflow: hidden;
			}
			
			.aidc-embed-toggle {
				background: #f0f8ff;
				border-bottom: 1px solid #e0e0e0;
			}
			
			.aidc-embed-button {
				width: 100%;
				padding: 15px 20px;
				background: none;
				border: none;
				text-align: left;
				cursor: pointer;
				font-size: 14px;
				font-weight: 500;
				color: #2c3e50;
				display: flex;
				align-items: center;
				gap: 8px;
				transition: background-color 0.2s ease;
			}
			
			.aidc-embed-button:hover {
				background: rgba(0,123,255,0.1);
			}
			
			.aidc-embed-button .dashicons {
				font-size: 16px;
				width: 16px;
				height: 16px;
			}
			
			.aidc-embed-content {
				padding: 20px;
			}
			
			.aidc-embed-content h3 {
				margin: 0 0 15px 0;
				color: #2c3e50;
			}
			
			.aidc-embed-examples {
				display: grid;
				grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
				gap: 15px;
				margin: 20px 0;
			}
			
			.aidc-embed-example {
				background: #f9f9f9;
				padding: 15px;
				border-radius: 6px;
				border-left: 4px solid #0073aa;
			}
			
			.aidc-embed-example h4 {
				margin: 0 0 8px 0;
				font-size: 14px;
				color: #2c3e50;
			}
			
			.aidc-embed-example code {
				display: block;
				background: #333;
				color: #fff;
				padding: 8px 12px;
				border-radius: 4px;
				font-family: 'Courier New', monospace;
				font-size: 13px;
				margin: 8px 0;
				word-break: break-all;
			}
			
			.aidc-embed-example p {
				margin: 8px 0 0 0;
				font-size: 12px;
				color: #666;
			}
			
			.aidc-guide-link {
				color: #0073aa;
				text-decoration: none;
				font-weight: 500;
			}
			
			.aidc-guide-link:hover {
				text-decoration: underline;
			}
			
			@media (max-width: 768px) {
				.aidc-cards-grid {
					grid-template-columns: 1fr;
				}
				
				.aidc-filter-form {
					flex-direction: column;
					align-items: stretch;
				}
				
				.aidc-filter-group {
					justify-content: center;
				}
				
				.aidc-search-input,
				.aidc-owner-input {
					width: 100%;
					max-width: 300px;
				}
				
				.aidc-embed-examples {
					grid-template-columns: 1fr;
				}
			}
		</style>
		
		<script>
			function toggleEmbedInfo() {
				const content = document.getElementById('aidc-embed-content');
				const button = document.querySelector('.aidc-embed-button');
				
				if (content.style.display === 'none') {
					content.style.display = 'block';
					button.setAttribute('aria-expanded', 'true');
				} else {
					content.style.display = 'none';
					button.setAttribute('aria-expanded', 'false');
				}
			}
		</script>
		<?php
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
