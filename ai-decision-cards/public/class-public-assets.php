<?php
/**
 * Public Assets handler.
 *
 * This class handles enqueuing of public CSS and JavaScript assets.
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
 * Public Assets handler.
 *
 * This class manages the enqueuing of CSS and JavaScript assets for the frontend.
 * Extracted from the main AIDC_Plugin class during Phase 2 modularization.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/public
 * @author     Sunny Poon
 */
class PublicAssets {

	/**
	 * Register hooks for asset enqueuing.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_assets' ) );
	}

	/**
	 * Enqueue public assets (CSS and JavaScript).
	 *
	 * Copied from AIDC_Plugin::enqueue_public_assets() during Phase 2 extraction.
	 * Always loads public CSS since shortcodes may appear on any page.
	 *
	 * @since 1.3.0
	 */
	public function enqueue_public_assets() {
		// Always load public CSS since shortcodes may appear on any page
		wp_enqueue_style(
			'aidc-public',
			plugins_url( 'assets/css/public.css', dirname( __FILE__ ) ),
			array(),
			AIDC_VERSION
		);

		wp_enqueue_script(
			'aidc-public',
			plugins_url( 'assets/js/public.js', dirname( __FILE__ ) ),
			array(),
			AIDC_VERSION,
			true
		);
	}
}