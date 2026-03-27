<?php
/**
 * Plugin Name:       Racing Centers
 * Plugin URI:        https://example.com/racing-centers
 * Description:       A data-driven system for managing Racing Centers — CPT, meta boxes, admin UI, and Elementor Dynamic Tags.
 * Version:           2.1.0
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
	const VERSION = '2.1.0';

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

		// Elementor Dynamic Tags — hook fires only when Elementor is active.
		add_action( 'elementor/dynamic_tags/register', array( $this, 'register_elementor_tags' ) );
	}

	// -------------------------------------------------------------------------
	// Elementor integration
	// -------------------------------------------------------------------------

	/**
	 * Load and initialise the Elementor Dynamic Tags module.
	 *
	 * Hooked to: elementor/dynamic_tags/register
	 * This action is fired by Elementor — it never fires if Elementor is absent,
	 * so the code below is 100% safe without a class_exists() guard.
	 *
	 * @param \Elementor\Core\DynamicTags\Manager $dynamic_tags Elementor's Dynamic Tags manager.
	 */
	public function register_elementor_tags( $dynamic_tags ): void {
		// Extra safety: confirm Elementor base tag class is available.
		if ( ! class_exists( '\Elementor\Core\DynamicTags\Tag' ) ) {
			return;
		}

		require_once self::PLUGIN_DIR . '/includes/elementor/class-dynamic-tags.php';
		new RC_Dynamic_Tags( $dynamic_tags );
	}
}

// Kick off the plugin after all plugins are loaded so CPT/meta is available.
add_action( 'plugins_loaded', array( 'Racing_Centers', 'get_instance' ) );

// ---------------------------------------------------------------------------
// Auto-updater via GitHub releases (plugin-update-checker).
//
// HOW TO RELEASE AN UPDATE:
//   1. Bump `Version:` in this header AND the Racing_Centers::VERSION constant.
//   2. Commit and push all changes.
//   3. Push a Git tag that matches the version, e.g.:
//          git tag v2.2.0 && git push origin v2.2.0
//   4. The GitHub Actions workflow (.github/workflows/release.yml) will
//      automatically build and attach a proper plugin zip to the release.
//   5. WordPress will detect the new version and offer the update in the
//      Plugins › Updates screen — just click "Update Now".
// ---------------------------------------------------------------------------
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';

	$rc_updater = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
		'https://github.com/YOUR-USERNAME/racing-center-plugin', // ← altere para sua URL do GitHub
		__FILE__,
		'racing-centers'
	);

	// Usa o zip anexado ao GitHub Release (gerado pelo Actions).
	$rc_updater->getVcsApi()->enableReleaseAssets();
}
