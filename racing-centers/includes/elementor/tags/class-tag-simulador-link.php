<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Link do Simulador (URL category)
 *
 * @package Racing_Centers
 */
class RC_Tag_Simulador_Link extends RC_Tag_URL_Base {
	public function get_name(): string { return 'rc-simulador-link'; }
	public function get_title(): string { return __( 'RC – Simulador Link', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_simulador_link'; }
}
