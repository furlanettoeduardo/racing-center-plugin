<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Horário de Funcionamento (Informações section)
 *
 * @package Racing_Centers
 */
class RC_Tag_Horario extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-horario'; }
	public function get_title(): string { return __( 'RC – Horário', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_horario'; }
}
