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
	 * Sets default options and flushes rewrite rules.
	 * Logic moved from AIDC_Plugin::on_activate() during Phase 8.
	 *
	 * @since 1.3.0
	 */
	public static function activate() {
		// Set default options (copied from legacy AIDC_Plugin::on_activate)
		if ( ! get_option( 'aidc_api_type' ) ) {
			update_option( 'aidc_api_type', 'openai' );
		}
		if ( ! get_option( 'aidc_openai_api_base' ) ) {
			update_option( 'aidc_openai_api_base', 'https://api.openai.com/' );
		}
		if ( ! get_option( 'aidc_openai_model' ) ) {
			update_option( 'aidc_openai_model', 'gpt-3.5-turbo' );
		}

		// Register CPT and flush rewrite rules
		// We need to load CPT class to register the post type before flushing
		require_once AIDC_PLUGIN_DIR . 'includes/class-cpt.php';
		( new \AIDC\Includes\Cpt() )->register_cpt();
		( new \AIDC\Includes\Cpt() )->register_meta_fields();
		
		// Flush rewrite rules to ensure CPT permalinks work
		flush_rewrite_rules( false );
	}
}