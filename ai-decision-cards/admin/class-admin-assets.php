<?php
/**
 * Admin Assets handler.
 *
 * This class handles enqueuing of admin CSS and JavaScript assets with localization.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/admin
 */

namespace AIDC\Admin;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin Assets handler.
 *
 * This class manages the enqueuing of CSS and JavaScript assets for the admin area.
 * Extracted from the main AIDC_Plugin class during Phase 3 modularization.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/admin
 * @author     Sunny Poon
 */
class AdminAssets {

	/**
	 * Register hooks for admin asset enqueuing.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Enqueue admin assets for plugin pages.
	 *
	 * Copied from AIDC_Plugin::enqueue_admin_assets() during Phase 3 extraction.
	 * Only loads on plugin admin pages with proper screen detection.
	 *
	 * @since 1.3.0
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_admin_assets( $hook ) {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}
		
		// Only load on our plugin pages (settings, generate, display, shortcodes, changelog)
		$target_pages = array(
			'decision_card_page_aidc_settings',
			'decision_card_page_aidc_generate',
			'decision_card_page_aidc_display',
			'decision_card_page_aidc_shortcodes',
			'decision_card_page_aidc_changelog',
			'toplevel_page_decision-cards-display'
		);
		
		if ( in_array( $screen->id, $target_pages, true ) ) {
			// Public styles are also needed for previewing display inside admin
			wp_enqueue_style(
				'aidc-public',
				plugin_dir_url( AIDC_PLUGIN_FILE ) . 'assets/css/public.css',
				array(),
				AIDC_VERSION
			);
			wp_enqueue_style(
				'aidc-admin',
				plugin_dir_url( AIDC_PLUGIN_FILE ) . 'assets/css/admin.css',
				array(),
				AIDC_VERSION
			);
			wp_enqueue_script(
				'aidc-admin',
				plugin_dir_url( AIDC_PLUGIN_FILE ) . 'assets/js/admin.js',
				array( 'jquery' ),
				AIDC_VERSION,
				true
			);

			// Public JS is needed for the display toggle inside admin preview pages
			if ( in_array( $screen->id, array( 'decision_card_page_aidc_display', 'toplevel_page_decision-cards-display' ), true ) ) {
				wp_enqueue_script(
					'aidc-public',
					plugin_dir_url( AIDC_PLUGIN_FILE ) . 'assets/js/public.js',
					array(),
					AIDC_VERSION,
					true
				);
			}
			
			wp_localize_script(
				'aidc-admin',
				'aidcAdmin',
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'aidc_test_api' ),
					'i18n' => array(
						'pleaseEnterKey' => __( 'Please enter an API key first.', 'ai-decision-cards' ),
						'testing' => __( 'Testing...', 'ai-decision-cards' ),
						'testingConnection' => __( 'Testing API connection...', 'ai-decision-cards' ),
						'testApiKey' => __( 'Test API Key', 'ai-decision-cards' ),
						'genPleaseEnterConversation' => __( 'Please enter a conversation before generating.', 'ai-decision-cards' ),
						'genGenerating' => __( 'Generating... Please wait', 'ai-decision-cards' ),
						'genAnalyzing' => __( 'AI is analyzing your conversation and creating a decision card... This may take 10-30 seconds.', 'ai-decision-cards' ),
						'genLongerThanExpected' => __( 'Taking longer than expected. Please check your API settings or try again.', 'ai-decision-cards' ),
						'genGenerateButton' => __( 'Generate Summary & Create Draft', 'ai-decision-cards' ),
					),
				)
			);
		}
	}
}