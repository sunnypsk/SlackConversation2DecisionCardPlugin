<?php
/**
 * Shortcode Single View Template.
 *
 * This template renders the [decision-card] shortcode output for a single card.
 * Contains only presentation logic - no business logic.
 *
 * Available variables:
 * @var WP_Post $post The decision card post object.
 * @var int $post_id The post ID.
 * @var bool $show_meta Whether to show meta banner.
 * @var bool $excerpt_only Whether to show excerpt only.
 * @var string $status Decision card status.
 * @var string $owner Decision card owner.
 * @var string $due Decision card due date.
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