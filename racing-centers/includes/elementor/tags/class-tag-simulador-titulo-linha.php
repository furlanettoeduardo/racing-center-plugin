<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Simulador – Título Linha Superior
 *
 * @package Racing_Centers
 */
class RC_Tag_Simulador_Titulo_Linha extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-simulador-titulo-linha'; }
	public function get_title(): string { return __( 'RC – Simulador Título Linha', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_simulador_titulo_linha'; }
}
