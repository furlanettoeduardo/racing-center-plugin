<?php
/**
 * Plugin Name:       Racing Centers
 * Plugin URI:        https://example.com/racing-centers
 * Description:       A data-driven system for managing Racing Centers — CPT, meta boxes, and structured admin UI.
 * Version:           2.0.0
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
 * Main plugin bootstrap class.
 *
 * Responsible only for:
 *   - Defining global constants
 *   - Loading class files
 *   - Instantiating feature classes
 *
 * All business logic lives in the dedicated classes inside /includes/.
 */
final class Racing_Centers {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '2.0.0';

	/**
	 * Absolute path to the plugin root directory (no trailing slash).
	 *
	 * @var string
	 */
	const PLUGIN_DIR = __DIR__;

	/**
	 * Absolute URL to the plugin root directory (no trailing slash).
	 *
	 * @var string
	 */
	const PLUGIN_URL = ''; // Populated at runtime – see get_instance().

	/**
	 * Singleton instance.
	 *
	 * @var Racing_Centers|null
	 */
	private static ?Racing_Centers $instance = null;

	/**
	 * Plugin URL resolved at runtime (constant cannot call functions).
	 *
	 * @var string
	 */
	private string $plugin_url;

	// -------------------------------------------------------------------------
	// Bootstrap
	// -------------------------------------------------------------------------

	/**
	 * Returns the singleton instance, creating it on first call.
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
	 * Constructor – loads files and wires up hooks.
	 */
	private function __construct() {
		$this->plugin_url = untrailingslashit( plugin_dir_url( __FILE__ ) );

		$this->load_dependencies();
		$this->init_features();
	}

	// -------------------------------------------------------------------------
	// Dependencies
	// -------------------------------------------------------------------------

	/**
	 * Require all class files.
	 * Order matters: base classes before derived ones.
	 */
	private function load_dependencies(): void {
		$includes = self::PLUGIN_DIR . '/includes/';

		require_once $includes . 'class-cpt.php';
		require_once $includes . 'class-admin-page.php';

		// Admin-only classes.
		if ( is_admin() ) {
			require_once $includes . 'class-meta-boxes.php';
			require_once $includes . 'class-save.php';
		}
	}

	// -------------------------------------------------------------------------
	// Feature initialisation
	// -------------------------------------------------------------------------

	/**
	 * Instantiate each feature class.
	 * Each class registers its own hooks internally.
	 */
	private function init_features(): void {
		new RC_CPT();
		new RC_Admin_Page();

		if ( is_admin() ) {
			new RC_Meta_Boxes( $this->plugin_url );
			new RC_Save();
		}
	}
}

// Kick off the plugin after all plugins are loaded so CPT/meta is available.
add_action( 'plugins_loaded', array( 'Racing_Centers', 'get_instance' ) );
