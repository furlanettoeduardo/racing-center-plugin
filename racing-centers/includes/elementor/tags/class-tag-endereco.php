<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Endereço (Informações section)
 *
 * @package Racing_Centers
 */
class RC_Tag_Endereco extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-endereco'; }
	public function get_title(): string { return __( 'RC – Endereço', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_endereco'; }
}
