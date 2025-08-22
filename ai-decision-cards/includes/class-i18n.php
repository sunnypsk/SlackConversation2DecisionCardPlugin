<?php
/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/includes
 */

namespace AIDC\Includes;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 * Currently a stub for Phase 0 - no functionality implemented yet.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/includes
 * @author     Sunny Poon
 */
class I18n {

	/**
	 * Register internationalization hooks.
	 *
	 * Hooks load_plugin_textdomain to plugins_loaded action.
	 * Logic moved from aidc_load_textdomain() during Phase 8.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		// Register on 'init' so registration works even when called during 'plugins_loaded'.
		add_action( 'init', array( $this, 'load_textdomain' ) );
	}

	/**
	 * Load plugin textdomain for translations.
	 *
	 * Moved from aidc_load_textdomain() function during Phase 8.
	 *
	 * @since 1.3.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'ai-decision-cards', false, dirname( plugin_basename( AIDC_PLUGIN_FILE ) ) . '/languages' );
	}
}
