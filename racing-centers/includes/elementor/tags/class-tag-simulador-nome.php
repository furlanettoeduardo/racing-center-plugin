<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Simulador – Nome
 *
 * @package Racing_Centers
 */
class RC_Tag_Simulador_Nome extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-simulador-nome'; }
	public function get_title(): string { return __( 'RC – Simulador Nome', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_simulador_nome'; }
}
