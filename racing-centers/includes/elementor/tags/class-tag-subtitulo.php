<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Subtítulo (Hero section)
 *
 * @package Racing_Centers
 */
class RC_Tag_Subtitulo extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-subtitulo'; }
	public function get_title(): string { return __( 'RC – Subtítulo', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_subtitulo'; }
}
