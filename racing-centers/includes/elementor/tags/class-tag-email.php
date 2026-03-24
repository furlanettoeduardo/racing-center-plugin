<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: E-mail (Informações section)
 *
 * @package Racing_Centers
 */
class RC_Tag_Email extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-email'; }
	public function get_title(): string { return __( 'RC – E-mail', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_email'; }
}
