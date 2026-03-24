<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Tipo de Conteúdo – "produtos" or "simulador" (Produtos/Simulador section)
 *
 * @package Racing_Centers
 */
class RC_Tag_Tipo_Conteudo extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-tipo-conteudo'; }
	public function get_title(): string { return __( 'RC – Tipo de Conteúdo', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_tipo_conteudo'; }
}
