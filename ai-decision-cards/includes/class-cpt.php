<?php
/**
 * Custom Post Type and Meta Fields handler.
 *
 * This class defines the Decision Card custom post type and its associated meta fields.
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
 * Custom Post Type and Meta Fields handler.
 *
 * This class registers the Decision Card custom post type and its meta fields.
 * Extracted from the main AIDC_Plugin class during Phase 1 modularization.
 *
 * @since      1.3.0
 * @package    AIDecisionCards
 * @subpackage AIDecisionCards/includes
 * @author     Sunny Poon
 */
class Cpt {

	/**
	 * Register hooks for CPT and meta fields.
	 *
	 * @since 1.3.0
	 */
	public function register() {
		add_action( 'init', array( $this, 'register_cpt' ) );
	}

	/**
	 * Register the Decision Card custom post type.
	 *
	 * Copied from AIDC_Plugin::register_cpt() during Phase 1 extraction.
	 *
	 * @since 1.3.0
	 */
	public function register_cpt() {
		$labels = array(
			'name'                  => _x( 'Decision Cards', 'Post Type General Name', 'ai-decision-cards' ),
			'singular_name'         => _x( 'Decision Card', 'Post Type Singular Name', 'ai-decision-cards' ),
			'menu_name'             => __( 'Decision Cards', 'ai-decision-cards' ),
			'name_admin_bar'        => __( 'Decision Card', 'ai-decision-cards' ),
			'archives'              => __( 'Decision Archives', 'ai-decision-cards' ),
			'attributes'            => __( 'Decision Attributes', 'ai-decision-cards' ),
			'parent_item_colon'     => __( 'Parent Decision:', 'ai-decision-cards' ),
			'all_items'             => __( 'All Decisions', 'ai-decision-cards' ),
			'add_new_item'          => __( 'Add New Decision Card', 'ai-decision-cards' ),
			'add_new'               => __( 'Add New', 'ai-decision-cards' ),
			'new_item'              => __( 'New Decision Card', 'ai-decision-cards' ),
			'edit_item'             => __( 'Edit Decision Card', 'ai-decision-cards' ),
			'update_item'           => __( 'Update Decision Card', 'ai-decision-cards' ),
			'view_item'             => __( 'View Decision Card', 'ai-decision-cards' ),
			'view_items'            => __( 'View Decision Cards', 'ai-decision-cards' ),
			'search_items'          => __( 'Search Decision Cards', 'ai-decision-cards' ),
			'not_found'             => __( 'No Decision Cards found', 'ai-decision-cards' ),
			'not_found_in_trash'    => __( 'No Decision Cards found in Trash', 'ai-decision-cards' ),
			'featured_image'        => __( 'Featured Image', 'ai-decision-cards' ),
			'set_featured_image'    => __( 'Set featured image', 'ai-decision-cards' ),
			'remove_featured_image' => __( 'Remove featured image', 'ai-decision-cards' ),
			'use_featured_image'    => __( 'Use as featured image', 'ai-decision-cards' ),
			'insert_into_item'      => __( 'Insert into Decision Card', 'ai-decision-cards' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Decision Card', 'ai-decision-cards' ),
			'items_list'            => __( 'Decision Cards list', 'ai-decision-cards' ),
			'items_list_navigation' => __( 'Decision Cards list navigation', 'ai-decision-cards' ),
			'filter_items_list'     => __( 'Filter Decision Cards list', 'ai-decision-cards' ),
		);

		$args = array(
			'label'                 => __( 'Decision Card', 'ai-decision-cards' ),
			'description'           => __( 'AI-generated decision summaries from conversations', 'ai-decision-cards' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'custom-fields' ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 25,
			'menu_icon'             => 'dashicons-yes-alt',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => true,
			'capability_type'       => 'post',
			'map_meta_cap'          => true,
			'show_in_rest'          => false,
		);

		register_post_type( 'decision_card', $args );

		// Register custom fields for meta data.
		$this->register_meta_fields();
	}

	/**
	 * Register meta fields for Decision Cards.
	 *
	 * Copied from AIDC_Plugin::register_meta_fields() during Phase 1 extraction.
	 *
	 * @since 1.3.0
	 */
	private function register_meta_fields() {
		register_post_meta(
			'decision_card',
			'_aidc_status',
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => false,
				'default'      => 'Proposed',
			)
		);

		register_post_meta(
			'decision_card',
			'_aidc_owner',
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => false,
				'default'      => '',
			)
		);

		register_post_meta(
			'decision_card',
			'_aidc_due',
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => false,
				'default'      => '',
			)
		);
	}
}