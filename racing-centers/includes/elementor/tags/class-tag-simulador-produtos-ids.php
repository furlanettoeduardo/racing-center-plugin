<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: IDs de Produtos do Simulador (comma-separated)
 *
 * @package Racing_Centers
 */
class RC_Tag_Simulador_Produtos_IDs extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-simulador-produtos-ids'; }
	public function get_title(): string { return __( 'RC – Simulador Produtos IDs', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_simulador_produtos_ids'; }
}
