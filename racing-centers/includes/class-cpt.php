<?php
/**
 * Custom Post Type: racing_center
 *
 * Registers the "Racing Center" CPT and flushes rewrite rules on
 * plugin activation / deactivation via static helpers called from
 * the main plugin file.
 *
 * @package Racing_Centers
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RC_CPT
 *
 * Handles registration of the `racing_center` custom post type.
 */
class RC_CPT {

	// -------------------------------------------------------------------------
	// Constructor
	// -------------------------------------------------------------------------

	/**
	 * Wire up WordPress hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register' ) );

		// Flush rewrite rules once after the CPT is registered on activation.
		register_activation_hook(
			Racing_Centers::PLUGIN_DIR . '/racing-centers.php',
			array( __CLASS__, 'flush_on_activation' )
		);
	}

	// -------------------------------------------------------------------------
	// CPT Registration
	// -------------------------------------------------------------------------

	/**
	 * Register the `racing_center` post type.
	 *
	 * Hooked to: init
	 */
	public function register(): void {
		$labels = array(
			'name'                  => _x( 'Racing Centers', 'post type general name', 'racing-centers' ),
			'singular_name'         => _x( 'Racing Center', 'post type singular name', 'racing-centers' ),
			'menu_name'             => _x( 'Racing Centers', 'admin menu', 'racing-centers' ),
			'name_admin_bar'        => _x( 'Racing Center', 'add new on admin bar', 'racing-centers' ),
			'add_new'               => __( 'Add New', 'racing-centers' ),
			'add_new_item'          => __( 'Add New Racing Center', 'racing-centers' ),
			'new_item'              => __( 'New Racing Center', 'racing-centers' ),
			'edit_item'             => __( 'Edit Racing Center', 'racing-centers' ),
			'view_item'             => __( 'View Racing Center', 'racing-centers' ),
			'all_items'             => __( 'All Racing Centers', 'racing-centers' ),
			'search_items'          => __( 'Search Racing Centers', 'racing-centers' ),
			'parent_item_colon'     => __( 'Parent Racing Centers:', 'racing-centers' ),
			'not_found'             => __( 'No racing centers found.', 'racing-centers' ),
			'not_found_in_trash'    => __( 'No racing centers found in Trash.', 'racing-centers' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,   // Appears as its own top-level menu entry.
			'show_in_rest'       => true,   // Block editor + REST API support.
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'racing-center' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 5,      // Below Posts.
			'menu_icon'          => 'dashicons-location-alt',
			'supports'           => array( 'title', 'thumbnail' ),
		);

		register_post_type( 'racing_center', $args );
	}

	// -------------------------------------------------------------------------
	// Activation helper
	// -------------------------------------------------------------------------

	/**
	 * Flush rewrite rules so the CPT archive URL works immediately after activation.
	 *
	 * Called via register_activation_hook().
	 */
	public static function flush_on_activation(): void {
		// The CPT must be registered before flushing, so we call register() directly.
		( new self() )->register();
		flush_rewrite_rules();
	}
}
