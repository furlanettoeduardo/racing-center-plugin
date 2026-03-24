<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Cidade (Hero section)
 *
 * @package Racing_Centers
 */
class RC_Tag_Cidade extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-cidade'; }
	public function get_title(): string { return __( 'RC – Cidade', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_cidade'; }
}
