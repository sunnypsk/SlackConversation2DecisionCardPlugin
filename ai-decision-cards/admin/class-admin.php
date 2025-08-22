<?php
/**
 * Admin functionality handler.
 *
 * This class handles admin menus, pages, and notices for the plugin.
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
 * Admin functionality handler.
 *
 * This class manages admin menu registration, page rendering via views,
 * and admin notice display. Extracted from the main AIDC_Plugin class 
 * during Phase 3 modularization.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/admin
 * @author     Sunny Poon
 */
class Admin {

	/**
	 * Plugin option names.
	 */
	const OPTION_API_TYPE = 'aidc_api_type';
	const OPTION_API_KEY  = 'aidc_openai_api_key';
	const OPTION_API_BASE = 'aidc_openai_api_base';
	const OPTION_MODEL    = 'aidc_openai_model';
	const OPTION_PUBLIC_PAGE_ID = 'aidc_public_page_id';

	/**
	 * Register hooks for admin functionality.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		add_action( 'admin_menu', array( $this, 'register_admin_pages' ) );
		add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_shortcode_meta_box' ) );
	}

	/**
	 * Register admin pages and menus.
	 *
	 * Copied from AIDC_Plugin::register_admin_pages() during Phase 3 extraction.
	 * Creates all admin menu items and their callback handlers.
	 *
	 * @since 1.3.0
	 */
	public function register_admin_pages() {
		// Add Generate page as submenu under the Decision Cards post type menu
		add_submenu_page(
			'edit.php?post_type=decision_card',
			__( 'Generate from Conversation', 'ai-decision-cards' ),
			__( 'Generate from Conversation', 'ai-decision-cards' ),
			'edit_posts',
			'aidc_generate',
			array( $this, 'render_generate_page' )
		);

		// Add Settings page as submenu under the Decision Cards post type menu
		add_submenu_page(
			'edit.php?post_type=decision_card',
			__( 'Settings', 'ai-decision-cards' ),
			__( 'Settings', 'ai-decision-cards' ),
			'manage_options',
			'aidc_settings',
			array( $this, 'render_settings_page' )
		);

		// Add Shortcode Usage Guide page under the Decision Cards post type menu
		add_submenu_page(
			'edit.php?post_type=decision_card',
			__( 'Shortcode Usage Guide', 'ai-decision-cards' ),
			__( 'Shortcode Usage Guide', 'ai-decision-cards' ),
			'read',
			'aidc_shortcodes',
			array( $this, 'render_shortcodes_page' )
		);

		// Add Changelog page under the Decision Cards post type menu
		add_submenu_page(
			'edit.php?post_type=decision_card',
			__( 'Changelog', 'ai-decision-cards' ),
			__( 'Changelog', 'ai-decision-cards' ),
			'read',
			'aidc_changelog',
			array( $this, 'render_changelog_page' )
		);

		// Add public Decision Cards Display page (accessible to everyone)
		add_submenu_page(
			'edit.php?post_type=decision_card',
			__( 'View Public Display', 'ai-decision-cards' ),
			__( 'View Public Display', 'ai-decision-cards' ),
			'read',
			'aidc_display',
			array( $this, 'render_display_page' )
		);

		// Add public accessible page that doesn't require admin access
		add_menu_page(
			__( 'Decision Cards Display', 'ai-decision-cards' ),
			__( 'Decision Cards Display', 'ai-decision-cards' ),
			'read',
			'decision-cards-display',
			array( $this, 'render_public_display_page' ),
			'dashicons-yes-alt',
			30
		);
	}

	/**
	 * Render the settings page.
	 *
	 * @since 1.3.0
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Prepare data for the view
		$view_data = array(
			'saved' => false,
			'api_type' => get_option( self::OPTION_API_TYPE, 'openai' ),
			'api_key' => get_option( self::OPTION_API_KEY, '' ),
			'api_base' => get_option( self::OPTION_API_BASE, 'https://api.openai.com/' ),
			'model' => get_option( self::OPTION_MODEL, 'gpt-3.5-turbo' ),
		);

		// Handle form submission
		if ( isset( $_POST['aidc_settings_nonce'] ) && wp_verify_nonce( $_POST['aidc_settings_nonce'], 'aidc_save_settings' ) ) {
			if ( isset( $_POST['aidc_api_type'] ) ) {
				update_option( self::OPTION_API_TYPE, sanitize_text_field( wp_unslash( $_POST['aidc_api_type'] ) ) );
			}
			if ( isset( $_POST['aidc_api_key'] ) ) {
				update_option( self::OPTION_API_KEY, sanitize_text_field( wp_unslash( $_POST['aidc_api_key'] ) ) );
			}
			if ( isset( $_POST['aidc_api_base'] ) ) {
				$base = trim( sanitize_text_field( wp_unslash( $_POST['aidc_api_base'] ) ) );
				if ( '' !== $base && '/' !== substr( $base, -1 ) ) {
					$base .= '/';
				}
				update_option( self::OPTION_API_BASE, $base );
			}
			if ( isset( $_POST['aidc_model'] ) ) {
				update_option( self::OPTION_MODEL, sanitize_text_field( wp_unslash( $_POST['aidc_model'] ) ) );
			}
			
			$view_data['saved'] = true;
			// Refresh data after save
			$view_data['api_type'] = get_option( self::OPTION_API_TYPE, 'openai' );
			$view_data['api_key'] = get_option( self::OPTION_API_KEY, '' );
			$view_data['api_base'] = get_option( self::OPTION_API_BASE, 'https://api.openai.com/' );
			$view_data['model'] = get_option( self::OPTION_MODEL, 'gpt-3.5-turbo' );
		}

		// Render view
		$this->render_view( 'settings', $view_data );
	}

	/**
	 * Render the generate page.
	 *
	 * @since 1.3.0
	 */
	public function render_generate_page() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		// Prepare data for the view
		$view_data = array(
			'api_key_exists' => (bool) get_option( self::OPTION_API_KEY ),
		);

		// Render view
		$this->render_view( 'generate', $view_data );
	}

	/**
	 * Render the display page (admin area).
	 *
	 * @since 1.3.0
	 */
	public function render_display_page() {
		// Prepare data for the view
		$view_data = array(
			'public_page_url' => $this->get_or_create_public_page_url(),
		);

		// Render view
		$this->render_view( 'display', $view_data );
	}

	/**
	 * Render Shortcode Usage Guide page.
	 *
	 * @since 1.3.0
	 */
	public function render_shortcodes_page() {
		if ( ! current_user_can( 'read' ) ) {
			return;
		}

		// Render view (no data needed for static content)
		$this->render_view( 'shortcodes-guide', array() );
	}

	/**
	 * Render Changelog page.
	 *
	 * @since 1.3.0
	 */
	public function render_changelog_page() {
		if ( ! current_user_can( 'read' ) ) {
			return;
		}

		// Render view (no data needed for static content)
		$this->render_view( 'changelog', array() );
	}

	/**
	 * Render public display page.
	 *
	 * @since 1.3.0
	 */
	public function render_public_display_page() {
		// This page will reuse public functionality for consistency
		// For now, delegate to legacy method during transition
		if ( class_exists( 'AIDC_Plugin' ) ) {
			$legacy_instance = \AIDC_Plugin::get_instance();
			if ( method_exists( $legacy_instance, 'render_public_display_page' ) ) {
				$legacy_instance->render_public_display_page();
				return;
			}
		}

		// Fallback if legacy method not available
		echo '<div class="wrap"><h1>' . esc_html__( 'Decision Cards Display', 'ai-decision-cards' ) . '</h1><p>' . esc_html__( 'Public display functionality will be available after full migration.', 'ai-decision-cards' ) . '</p></div>';
	}

	/**
	 * Add shortcode meta box to decision card edit screen.
	 *
	 * @since 1.3.0
	 */
	public function add_shortcode_meta_box() {
		add_meta_box(
			'aidc_shortcode_info',
			__( 'Embed This Decision Card', 'ai-decision-cards' ),
			array( $this, 'render_shortcode_meta_box' ),
			'decision_card',
			'side',
			'high'
		);
	}

	/**
	 * Render shortcode meta box content.
	 *
	 * @since 1.3.0
	 * @param WP_Post $post The current post object.
	 */
	public function render_shortcode_meta_box( $post ) {
		// Delegate to legacy method during transition
		if ( class_exists( 'AIDC_Plugin' ) ) {
			$legacy_instance = \AIDC_Plugin::get_instance();
			if ( method_exists( $legacy_instance, 'render_shortcode_meta_box' ) ) {
				$legacy_instance->render_shortcode_meta_box( $post );
				return;
			}
		}

		// Fallback if legacy method not available
		echo '<p>' . esc_html__( 'Shortcode functionality will be available after full migration.', 'ai-decision-cards' ) . '</p>';
	}

	/**
	 * Display admin notices.
	 *
	 * Copied from aidc_admin_notices() function during Phase 3 extraction.
	 *
	 * @since 1.3.0
	 */
	public function display_admin_notices() {
		if ( ! is_admin() || ! isset( $_GET['aidc_notice'] ) ) {
			return;
		}

		$type = isset( $_GET['aidc_type'] ) ? sanitize_text_field( wp_unslash( $_GET['aidc_type'] ) ) : 'info';
		$class = 'notice';
		
		switch ( $type ) {
			case 'success':
				$class .= ' notice-success';
				break;
			case 'error':
				$class .= ' notice-error';
				break;
			case 'warning':
				$class .= ' notice-warning';
				break;
			default:
				$class .= ' notice-info';
				break;
		}

		$message = isset( $_GET['aidc_notice'] ) ? rawurldecode( sanitize_text_field( wp_unslash( $_GET['aidc_notice'] ) ) ) : '';
		
		printf(
			'<div class="%s"><p>%s</p></div>',
			esc_attr( $class ),
			esc_html( $message )
		);
	}

	/**
	 * Render a view template.
	 *
	 * @since 1.3.0
	 * @param string $view_name Name of the view file (without .php extension).
	 * @param array  $data      Data to pass to the view.
	 */
	private function render_view( $view_name, $data = array() ) {
		$view_file = AIDC_PLUGIN_DIR . 'admin/views/' . $view_name . '.php';
		
		if ( file_exists( $view_file ) ) {
			// Extract data for use in view
			extract( $data );
			include $view_file;
		} else {
			echo '<div class="wrap"><h1>' . esc_html__( 'Error', 'ai-decision-cards' ) . '</h1><p>' . sprintf( esc_html__( 'View file not found: %s', 'ai-decision-cards' ), esc_html( $view_name ) ) . '</p></div>';
		}
	}

	/**
	 * Get escaped attribute value for an option.
	 *
	 * @since 1.3.0
	 * @param string $opt_name The option name.
	 * @param string $default  The default value.
	 * @return string Escaped attribute value.
	 */
	private function esc_attr_val( $opt_name, $default = '' ) {
		return esc_attr( (string) get_option( $opt_name, $default ) );
	}

	/**
	 * Ensure a public page exists for Decision Cards Display and return its permalink.
	 *
	 * @since 1.3.0
	 * @return string Permalink URL of the public display page.
	 */
	private function get_or_create_public_page_url() {
		$page_id = intval( get_option( self::OPTION_PUBLIC_PAGE_ID ) );
		if ( $page_id && get_post_status( $page_id ) ) {
			return get_permalink( $page_id );
		}

		// Create a new page to host the public display
		$page_id = wp_insert_post( array(
			'post_title'   => __( 'Decision Cards', 'ai-decision-cards' ),
			'post_name'    => 'decision-cards',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '[decision-cards-list]'
		) );

		if ( ! is_wp_error( $page_id ) && $page_id ) {
			update_option( self::OPTION_PUBLIC_PAGE_ID, $page_id );
			return get_permalink( $page_id );
		}

		// Fallback to home if creation failed
		return home_url( '/' );
	}
}