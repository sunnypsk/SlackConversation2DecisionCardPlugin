<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
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
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 * Currently a stub for Phase 0 - no functionality implemented yet.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/includes
 * @author     Sunny Poon
 */
class Activator {

	/**
	 * Plugin activation handler.
	 *
	 * This method will handle plugin activation tasks.
	 * Currently empty for Phase 0 - no behavior changes.
	 *
	 * @since 1.3.0
	 */
	public static function activate() {
		// Empty for now - Phase 0 introduces structure only
		// Future implementation will handle:
		// - Setting default options
		// - CPT registration
		// - Rewrite rules flush
	}
}