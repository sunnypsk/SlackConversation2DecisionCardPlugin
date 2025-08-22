<?php
/**
 * Plugin Name: AI Decision Cards
 * Plugin URI: https://github.com/sunnypsk/SlackConversaton2DecisionCardPlugin
 * Description: Convert Slack-style conversations into AI-generated Decision Cards with summaries and action items using OpenAI-compatible APIs.
 * Version: 1.3.0
 * Author: Sunny Poon
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: ai-decision-cards
 * Domain Path: /languages
 * Requires at least: 6.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 *
 * @package AIDecisionCards
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'AIDC_VERSION', '1.3.0' );
define( 'AIDC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AIDC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'AIDC_PLUGIN_FILE', __FILE__ );

/**
 * Main plugin initialization function.
 *
 * Loads the new modular plugin architecture.
 * Replaces the legacy AIDC_Plugin system as of v1.3.0.
 *
 * @since 1.3.0
 */
function aidc_init() {
	// Load the main plugin orchestrator
	require_once AIDC_PLUGIN_DIR . 'includes/class-plugin.php';
	
	// Boot the plugin
	\AIDC\Includes\Plugin::instance()->boot();
}
add_action( 'plugins_loaded', 'aidc_init' );

/**
 * Plugin activation handler.
 *
 * @since 1.3.0
 */
function aidc_on_activate() {
	require_once AIDC_PLUGIN_DIR . 'includes/class-activator.php';
	\AIDC\Includes\Activator::activate();
}
register_activation_hook( __FILE__, 'aidc_on_activate' );

/**
 * Plugin deactivation handler.
 *
 * @since 1.3.0
 */
function aidc_on_deactivate() {
	require_once AIDC_PLUGIN_DIR . 'includes/class-deactivator.php';
	\AIDC\Includes\Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'aidc_on_deactivate' );
