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
	 * This method will register the hooks needed for plugin textdomain loading.
	 * Currently empty for Phase 0 - no behavior changes.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		// Empty for now - Phase 0 introduces structure only
		// Future implementation will hook load_plugin_textdomain
	}
}