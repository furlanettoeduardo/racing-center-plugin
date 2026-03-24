<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Título da Seção Sobre (Conteúdo section)
 *
 * @package Racing_Centers
 */
class RC_Tag_Titulo_Sobre extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-titulo-sobre'; }
	public function get_title(): string { return __( 'RC – Título Sobre', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_titulo_sobre'; }
}
