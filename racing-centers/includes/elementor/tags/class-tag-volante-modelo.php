<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Volante – Modelo
 *
 * @package Racing_Centers
 */
class RC_Tag_Volante_Modelo extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-volante-modelo'; }
	public function get_title(): string { return __( 'RC – Volante Modelo', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_volante_modelo'; }
}
