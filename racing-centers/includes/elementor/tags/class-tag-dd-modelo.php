<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Direct Drive – Modelo
 *
 * @package Racing_Centers
 */
class RC_Tag_DD_Modelo extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-dd-modelo'; }
	public function get_title(): string { return __( 'RC – Direct Drive Modelo', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_dd_modelo'; }
}
