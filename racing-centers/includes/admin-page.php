<?php
/**
 * Admin page renderer for the Racing Centers plugin.
 *
 * This file is loaded only in the WordPress admin area.
 * It provides the callback function used by add_menu_page() to render
 * the "Racing Centers" admin page.
 */

// Prevent direct access to this file.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the Racing Centers admin page.
 *
 * The function name uses the `rc_` prefix to avoid collisions with other
 * plugins or themes that might define similarly named functions.
 */
function rc_render_admin_page(): void {
	// Only users with the correct capability should reach this page.
	// WordPress already enforces the capability check via add_menu_page(),
	// but an explicit guard here follows the principle of defense-in-depth.
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'racing-centers' ) );
	}
	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<p><?php esc_html_e( 'Racing Centers Plugin is active and working.', 'racing-centers' ); ?></p>
	</div>
	<?php
}
