<?php
/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
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
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 * Currently a stub for Phase 0 - no functionality implemented yet.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/includes
 * @author     Sunny Poon
 */
class Deactivator {

	/**
	 * Plugin deactivation handler.
	 *
	 * This method will handle plugin deactivation tasks.
	 * Currently empty for Phase 0 - no behavior changes.
	 *
	 * @since 1.3.0
	 */
	public static function deactivate() {
		// Empty for now - Phase 0 introduces structure only
		// Future implementation will handle:
		// - Rewrite rules flush
		// - Cleanup tasks
	}
}