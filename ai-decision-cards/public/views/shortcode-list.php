<?php
/**
 * Shortcode List View Template.
 *
 * This template renders the [decision-cards-list] shortcode output.
 * Contains only presentation logic - no business logic.
 *
 * Available variables:
 * @var bool $show_filters Whether to show filter form.
 * @var string $search Current search term.
 * @var string $status Current status filter.
 * @var string $owner Current owner filter.
 * @var WP_Query $query The query object with decision cards.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/public/views
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
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
?>