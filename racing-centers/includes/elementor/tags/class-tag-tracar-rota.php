<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: URL – Traçar Rota (URL category)
 *
 * Returns the "Traçar rota" button URL stored in `rc_tracar_rota_url`.
 *
 * @package Racing_Centers
 */
class RC_Tag_Tracar_Rota extends RC_Tag_URL_Base {
	public function get_name(): string  { return 'rc-tracar-rota'; }
	public function get_title(): string { return __( 'RC – Traçar Rota (URL)', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_tracar_rota_url'; }
}
