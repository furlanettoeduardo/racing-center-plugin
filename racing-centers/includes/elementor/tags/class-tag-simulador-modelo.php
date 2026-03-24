<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Simulador – Modelo
 *
 * @package Racing_Centers
 */
class RC_Tag_Simulador_Modelo extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-simulador-modelo'; }
	public function get_title(): string { return __( 'RC – Simulador Modelo', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_simulador_modelo'; }
}
