<?php
/**
 * The core plugin class.
 *
 * This is used to define the main plugin orchestrator with singleton pattern.
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
 * The core plugin class.
 *
 * This class will act as the main orchestrator for all plugin functionality.
 * Currently a stub for Phase 0 - no functionality implemented yet.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/includes
 * @author     Sunny Poon
 */
class Plugin {

	/**
	 * The single instance of the class.
	 *
	 * @since 1.3.0
	 * @var   Plugin|null
	 */
	protected static $instance = null;

	/**
	 * Main Plugin Instance.
	 *
	 * Ensures only one instance of Plugin is loaded or can be loaded.
	 *
	 * @since  1.3.0
	 * @static
	 * @return Plugin - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor - Private to enforce singleton pattern.
	 *
	 * @since 1.3.0
	 */
	private function __construct() {
		// Private constructor to prevent direct instantiation
	}

	/**
	 * Boot the plugin.
	 *
	 * This method will be used to initialize all plugin functionality.
	 * Phase 1 adds CPT registration.
	 * Phase 2 adds Public layer (assets, content filter, shortcodes).
	 * Phase 3 adds Admin layer (menus, pages, assets, notices).
	 * Phase 4 adds Generator (admin-post) + AI client.
	 *
	 * @since 1.3.0
	 */
	public function boot() {
		// Phase 8: Register internationalization
		require_once AIDC_PLUGIN_DIR . 'includes/class-i18n.php';
		( new \AIDC\Includes\I18n() )->register();

		// Phase 6: Load shared utility helpers
		require_once AIDC_PLUGIN_DIR . 'includes/class-helpers.php';

		// Phase 1: Register Custom Post Type and Meta Fields
		require_once AIDC_PLUGIN_DIR . 'includes/class-cpt.php';
		( new \AIDC\Includes\Cpt() )->register();

		// Phase 2: Register Public Layer Components
		require_once AIDC_PLUGIN_DIR . 'public/class-public-assets.php';
		( new \AIDC\PublicUi\PublicAssets() )->register();

		require_once AIDC_PLUGIN_DIR . 'public/class-public.php';
		( new \AIDC\PublicUi\PublicHandler() )->register();

		require_once AIDC_PLUGIN_DIR . 'public/class-shortcodes.php';
		( new \AIDC\PublicUi\Shortcodes() )->register();

		// Phase 3: Register Admin Layer Components (only when in admin)
		if ( is_admin() ) {
			require_once AIDC_PLUGIN_DIR . 'admin/class-admin-assets.php';
			( new \AIDC\Admin\AdminAssets() )->register();

			require_once AIDC_PLUGIN_DIR . 'admin/class-admin.php';
			( new \AIDC\Admin\Admin() )->register();

			// Phase 5: Register Admin AJAX Handler
			require_once AIDC_PLUGIN_DIR . 'admin/class-admin-ajax.php';
			( new \AIDC\Admin\AdminAjax() )->register();
		}

		// Phase 4: Register Generator (admin-post runs in admin context)
		require_once AIDC_PLUGIN_DIR . 'includes/class-ai-client.php';
		require_once AIDC_PLUGIN_DIR . 'includes/class-generator.php';
		( new \AIDC\Includes\Generator() )->register();
	}

	/**
	 * Prevent cloning.
	 *
	 * @since 1.3.0
	 */
	private function __clone() {
		// Prevent cloning
	}

	/**
	 * Prevent unserialization.
	 *
	 * @since 1.3.0
	 */
	public function __wakeup() {
		// Prevent unserialization
	}
}