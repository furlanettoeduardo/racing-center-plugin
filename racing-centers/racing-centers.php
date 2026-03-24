<?php
/**
 * Plugin Name:       Racing Centers
 * Plugin URI:        https://example.com/racing-centers
 * Description:       A base plugin for managing Racing Centers data in WordPress.
 * Version:           1.0.0
 * Requires at least: 6.0
 * Requires PHP:      8.2
 * Author:            Your Name
 * Author URI:        https://example.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       racing-centers
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 *
 * Encapsulates all plugin bootstrap logic to avoid polluting the global namespace.
 */
final class Racing_Centers {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '1.0.0';

	/**
	 * Absolute path to the plugin directory (with trailing slash).
	 *
	 * @var string
	 */
	const PLUGIN_DIR = __DIR__ . DIRECTORY_SEPARATOR;

	/**
	 * The single instance of this class (singleton).
	 *
	 * @var Racing_Centers|null
	 */
	private static ?Racing_Centers $instance = null;

	/**
	 * Returns (and creates, if needed) the singleton instance.
	 *
	 * @return Racing_Centers
	 */
	public static function get_instance(): Racing_Centers {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Private constructor – use get_instance() instead.
	 *
	 * Loads dependencies and registers WordPress hooks.
	 */
	private function __construct() {
		$this->load_dependencies();
		$this->register_hooks();
	}

	/**
	 * Load all required include files.
	 */
	private function load_dependencies(): void {
		// Admin page renderer (only needed in the admin area).
		if ( is_admin() ) {
			require_once self::PLUGIN_DIR . 'includes/admin-page.php';
		}
	}

	/**
	 * Register WordPress action / filter hooks.
	 */
	private function register_hooks(): void {
		// Register the admin menu item.
		add_action( 'admin_menu', array( $this, 'register_admin_menu' ) );
	}

	/**
	 * Add the "Racing Centers" top-level menu entry to the WordPress admin sidebar.
	 *
	 * Hooked to: admin_menu
	 */
	public function register_admin_menu(): void {
		add_menu_page(
			__( 'Racing Centers', 'racing-centers' ), // Page <title> tag.
			__( 'Racing Centers', 'racing-centers' ), // Sidebar label.
			'manage_options',                          // Required capability.
			'racing-centers',                          // Menu slug (unique identifier).
			'rc_render_admin_page',                    // Callback that renders the page.
			'dashicons-location-alt',                  // Dashicon class.
			5                                          // Position – just below Posts (default 5).
		);
	}
}

// Bootstrap the plugin.
Racing_Centers::get_instance();
