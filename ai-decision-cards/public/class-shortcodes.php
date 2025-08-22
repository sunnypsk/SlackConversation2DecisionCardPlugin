<?php
/**
 * Shortcodes handler.
 *
 * This class handles all shortcode functionality for Decision Cards.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/public
 */

namespace AIDC\PublicUi;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shortcodes handler.
 *
 * This class manages all shortcode registration and rendering functionality.
 * Extracted from the main AIDC_Plugin class during Phase 2 modularization.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/public
 * @author     Sunny Poon
 */
class Shortcodes {

	/**
	 * Current search term for custom search filter.
	 *
	 * @since 1.3.0
	 * @var string
	 */
	private $current_search_term = '';

	/**
	 * Register hooks for shortcode functionality.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_shortcodes' ) );
	}

	/**
	 * Register the Decision Cards shortcodes.
	 *
	 * Copied from AIDC_Plugin::register_shortcodes() during Phase 2 extraction.
	 *
	 * @since 1.3.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'decision-cards-list', array( $this, 'shortcode_decision_cards_list' ) );
		add_shortcode( 'decision-card', array( $this, 'shortcode_single_decision_card' ) );
	}

	/**
	 * Handle [decision-cards-list] shortcode.
	 *
	 * Copied from AIDC_Plugin::shortcode_decision_cards_list() during Phase 2 extraction.
	 *
	 * @since 1.3.0
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

		// Apply filters from query string when present
		if ( isset( $_GET['aidc_search'] ) ) {
			$search = sanitize_text_field( wp_unslash( $_GET['aidc_search'] ) );
		}
		if ( isset( $_GET['aidc_status'] ) ) {
			$status = sanitize_text_field( wp_unslash( $_GET['aidc_status'] ) );
		}
		if ( isset( $_GET['aidc_owner'] ) ) {
			$owner = sanitize_text_field( wp_unslash( $_GET['aidc_owner'] ) );
		}

		// Build query
		$query_args = array(
			'post_type'      => 'decision_card',
			'post_status'    => 'publish',
			'posts_per_page' => $limit,
		);

		// Add search
		if ( ! empty( $search ) ) {
			// Use custom search to include both title and content
			add_filter( 'posts_where', array( $this, 'custom_search_where' ), 10, 2 );
			// Store search term for the filter
			$this->current_search_term = $search;
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

		$query = new \WP_Query( $query_args );
		
		// Remove custom search filter after query
		if ( ! empty( $search ) ) {
			remove_filter( 'posts_where', array( $this, 'custom_search_where' ), 10 );
		}

		// Prepare data for view
		$view_data = array(
			'show_filters' => $show_filters,
			'search' => $search,
			'status' => $status,
			'owner' => $owner,
			'query' => $query,
		);

		// Load view template
		return $this->render_list_view( $view_data );
	}

	/**
	 * Handle [decision-card] shortcode for single decision card.
	 *
	 * Copied from AIDC_Plugin::shortcode_single_decision_card() during Phase 2 extraction.
	 *
	 * @since 1.3.0
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

		// Prepare data for view
		$view_data = array(
			'post' => $post,
			'post_id' => $post_id,
			'show_meta' => $show_meta,
			'excerpt_only' => $excerpt_only,
			'status' => $status,
			'owner' => $owner,
			'due' => $due,
		);

		// Load view template
		return $this->render_single_view( $view_data );
	}

	/**
	 * Custom search WHERE clause for full-text search.
	 *
	 * Copied from AIDC_Plugin::custom_search_where() during Phase 2 extraction.
	 *
	 * @since 1.3.0
	 * @param string $where Original WHERE clause.
	 * @param \WP_Query $query WP_Query object.
	 * @return string Modified WHERE clause.
	 */
	public function custom_search_where( $where, $query ) {
		global $wpdb;

		if ( empty( $this->current_search_term ) ) {
			return $where;
		}

		// Only apply to decision_card post type queries
		if ( ! isset( $query->query_vars['post_type'] ) || 'decision_card' !== $query->query_vars['post_type'] ) {
			return $where;
		}

		$search_term = esc_sql( $wpdb->esc_like( $this->current_search_term ) );
		
		// Create custom search condition for title and content
		$custom_where = $wpdb->prepare(
			"AND (({$wpdb->posts}.post_title LIKE %s) OR ({$wpdb->posts}.post_content LIKE %s))",
			'%' . $search_term . '%',
			'%' . $search_term . '%'
		);

		return $where . ' ' . $custom_where;
	}

	/**
	 * Render the list shortcode view.
	 *
	 * @since 1.3.0
	 * @param array $data View data.
	 * @return string Rendered HTML.
	 */
	private function render_list_view( $data ) {
		ob_start();
		
		// Extract variables for view scope
		$show_filters = $data['show_filters'];
		$search = $data['search'];
		$status = $data['status'];
		$owner = $data['owner'];
		$query = $data['query'];
		
		// Include the view file
		$view_file = dirname( __FILE__ ) . '/views/shortcode-list.php';
		if ( file_exists( $view_file ) ) {
			include $view_file;
		} else {
			// Fallback if view file doesn't exist yet
			return $this->render_list_fallback( $data );
		}
		
		return ob_get_clean();
	}

	/**
	 * Render the single shortcode view.
	 *
	 * @since 1.3.0
	 * @param array $data View data.
	 * @return string Rendered HTML.
	 */
	private function render_single_view( $data ) {
		ob_start();
		
		// Extract variables for view scope
		$post = $data['post'];
		$post_id = $data['post_id'];
		$show_meta = $data['show_meta'];
		$excerpt_only = $data['excerpt_only'];
		$status = $data['status'];
		$owner = $data['owner'];
		$due = $data['due'];
		
		// Include the view file
		$view_file = dirname( __FILE__ ) . '/views/shortcode-single.php';
		if ( file_exists( $view_file ) ) {
			include $view_file;
		} else {
			// Fallback if view file doesn't exist yet
			return $this->render_single_fallback( $data );
		}
		
		return ob_get_clean();
	}

	/**
	 * Fallback rendering for list shortcode (temporary until views are created).
	 *
	 * @since 1.3.0
	 * @param array $data View data.
	 * @return string Rendered HTML.
	 */
	private function render_list_fallback( $data ) {
		$show_filters = $data['show_filters'];
		$search = $data['search'];
		$status = $data['status'];
		$owner = $data['owner'];
		$query = $data['query'];

		ob_start();
		?>
		<div class="aidc-shortcode-wrapper">
			<?php if ( $show_filters ) : ?>
				<div class="aidc-shortcode-filters">
					<?php $aidc_action_url = esc_url( get_permalink( get_queried_object_id() ) ); ?>
					<form method="get" action="<?php echo $aidc_action_url; ?>" class="aidc-shortcode-filter-form">
						<?php
						// Preserve non-AIDC query parameters (e.g., page_id) on submit
						if ( ! empty( $_GET ) ) {
							foreach ( $_GET as $aidc_qk => $aidc_qv ) {
								if ( in_array( $aidc_qk, array( 'aidc_search', 'aidc_status', 'aidc_owner' ), true ) ) {
									continue;
								}
								if ( is_array( $aidc_qv ) ) {
									foreach ( $aidc_qv as $aidc_qv_item ) {
										echo '<input type="hidden" name="' . esc_attr( $aidc_qk ) . '[]" value="' . esc_attr( wp_unslash( $aidc_qv_item ) ) . '" />';
									}
								} else {
									echo '<input type="hidden" name="' . esc_attr( $aidc_qk ) . '" value="' . esc_attr( wp_unslash( $aidc_qv ) ) . '" />';
								}
							}
						}
						?>
						<input type="text" name="aidc_search" value="<?php echo esc_attr( $_GET['aidc_search'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Search decision cards...', 'ai-decision-cards' ); ?>">
						<select name="aidc_status">
							<option value=""><?php esc_html_e( 'All Statuses', 'ai-decision-cards' ); ?></option>
							<option value="Proposed" <?php selected( $_GET['aidc_status'] ?? '', 'Proposed' ); ?>><?php esc_html_e( 'Proposed', 'ai-decision-cards' ); ?></option>
							<option value="Approved" <?php selected( $_GET['aidc_status'] ?? '', 'Approved' ); ?>><?php esc_html_e( 'Approved', 'ai-decision-cards' ); ?></option>
							<option value="Rejected" <?php selected( $_GET['aidc_status'] ?? '', 'Rejected' ); ?>><?php esc_html_e( 'Rejected', 'ai-decision-cards' ); ?></option>
						</select>
						<input type="text" name="aidc_owner" value="<?php echo esc_attr( $_GET['aidc_owner'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Filter by owner...', 'ai-decision-cards' ); ?>">
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
							$card_status = get_post_meta( get_the_ID(), '_aidc_status', true );
							$card_owner = get_post_meta( get_the_ID(), '_aidc_owner', true );
							$card_due = get_post_meta( get_the_ID(), '_aidc_due', true );
							?>
							
							<div class="aidc-shortcode-meta">
								<span class="aidc-shortcode-status aidc-status-<?php echo esc_attr( strtolower( $card_status ) ); ?>">
									<?php echo $card_status ? esc_html( $card_status ) : 'TBD'; ?>
								</span>
								<?php if ( $card_owner ) : ?>
									<span class="aidc-shortcode-owner"><?php esc_html_e( 'Owner:', 'ai-decision-cards' ); ?> <?php echo esc_html( $card_owner ); ?></span>
								<?php endif; ?>
								<?php if ( $card_due ) : ?>
									<span class="aidc-shortcode-due"><?php esc_html_e( 'Due:', 'ai-decision-cards' ); ?> <?php echo esc_html( $card_due ); ?></span>
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

			<!-- Styles moved to assets/css/public.css -->
		</div>
		<?php
		wp_reset_postdata();
		return ob_get_clean();
	}

	/**
	 * Fallback rendering for single shortcode (temporary until views are created).
	 *
	 * @since 1.3.0
	 * @param array $data View data.
	 * @return string Rendered HTML.
	 */
	private function render_single_fallback( $data ) {
		$post = $data['post'];
		$post_id = $data['post_id'];
		$show_meta = $data['show_meta'];
		$excerpt_only = $data['excerpt_only'];
		$status = $data['status'];
		$owner = $data['owner'];
		$due = $data['due'];

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

			<!-- Styles moved to assets/css/public.css -->
		</div>
		<?php
		return ob_get_clean();
	}
}