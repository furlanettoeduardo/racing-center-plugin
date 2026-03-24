<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Contato / Telefone (Informações section)
 *
 * @package Racing_Centers
 */
class RC_Tag_Contato extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-contato'; }
	public function get_title(): string { return __( 'RC – Contato', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_contato'; }
}
