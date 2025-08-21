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
	 * Currently empty for Phase 0 - no behavior changes.
	 *
	 * @since 1.3.0
	 */
	public function boot() {
		// Empty for now - Phase 0 introduces structure only
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