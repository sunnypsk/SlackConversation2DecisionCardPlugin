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
	 * Flushes rewrite rules to clean up.
	 * Logic moved from AIDC_Plugin::on_deactivate() during Phase 8.
	 *
	 * @since 1.3.0
	 */
	public static function deactivate() {
		// Flush rewrite rules to clean up (copied from legacy AIDC_Plugin::on_deactivate)
		flush_rewrite_rules( false );
	}
}