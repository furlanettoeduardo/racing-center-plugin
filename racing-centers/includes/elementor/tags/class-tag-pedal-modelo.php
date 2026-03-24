<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Pedais – Modelo
 *
 * @package Racing_Centers
 */
class RC_Tag_Pedal_Modelo extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-pedal-modelo'; }
	public function get_title(): string { return __( 'RC – Pedal Modelo', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_pedal_modelo'; }
}
