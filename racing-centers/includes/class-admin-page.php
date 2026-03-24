<?php
/**
 * Admin page — Racing Centers dashboard
 *
 * Registers a top-level admin menu entry that acts as the plugin's
 * overview / dashboard page. The CPT list is accessible via its own
 * automatically generated menu entry produced by the CPT registration.
 *
 * @package Racing_Centers
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RC_Admin_Page
 *
 * Adds a "Racing Centers" item to the WordPress admin sidebar and renders
 * the corresponding overview page.
 */
class RC_Admin_Page {

	// -------------------------------------------------------------------------
	// Constructor
	// -------------------------------------------------------------------------

	/**
	 * Register the admin_menu hook.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
	}

	// -------------------------------------------------------------------------
	// Menu registration
	// -------------------------------------------------------------------------

	/**
	 * Add the top-level "Racing Centers" menu page.
	 *
	 * Hooked to: admin_menu
	 */
	public function register_menu(): void {
		add_menu_page(
			__( 'Racing Centers', 'racing-centers' ),  // Browser <title>.
			__( 'Racing Centers', 'racing-centers' ),  // Sidebar label.
			'manage_options',                           // Required capability.
			'racing-centers-dashboard',                 // Unique menu slug.
			array( $this, 'render' ),                   // Page render callback.
			'dashicons-location-alt',                   // Icon.
			4                                           // Position — above Posts.
		);
	}

	// -------------------------------------------------------------------------
	// Page renderer
	// -------------------------------------------------------------------------

	/**
	 * Render the admin dashboard page.
	 */
	public function render(): void {
		// Capability guard (defence-in-depth; WP already enforces via add_menu_page).
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'racing-centers' ) );
		}

		$cpt_url   = admin_url( 'edit.php?post_type=racing_center' );
		$new_url   = admin_url( 'post-new.php?post_type=racing_center' );
		$count_obj = wp_count_posts( 'racing_center' );
		$published = isset( $count_obj->publish ) ? (int) $count_obj->publish : 0;
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p><?php esc_html_e( 'Racing Centers Plugin is active and working.', 'racing-centers' ); ?></p>

			<div class="rc-dashboard-cards" style="display:flex;gap:16px;margin-top:24px;flex-wrap:wrap;">

				<div class="rc-card" style="background:#fff;border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;min-width:200px;">
					<h2 style="margin-top:0;font-size:14px;text-transform:uppercase;color:#646970;">
						<?php esc_html_e( 'Published Centers', 'racing-centers' ); ?>
					</h2>
					<p style="font-size:36px;font-weight:700;margin:4px 0;"><?php echo esc_html( $published ); ?></p>
					<a href="<?php echo esc_url( $cpt_url ); ?>"><?php esc_html_e( 'View all →', 'racing-centers' ); ?></a>
				</div>

				<div class="rc-card" style="background:#fff;border:1px solid #c3c4c7;border-radius:4px;padding:20px 24px;min-width:200px;">
					<h2 style="margin-top:0;font-size:14px;text-transform:uppercase;color:#646970;">
						<?php esc_html_e( 'Quick Actions', 'racing-centers' ); ?>
					</h2>
					<a href="<?php echo esc_url( $new_url ); ?>" class="button button-primary" style="margin-top:8px;">
						<?php esc_html_e( '+ Add New Racing Center', 'racing-centers' ); ?>
					</a>
				</div>

			</div>
		</div>
		<?php
	}
}
