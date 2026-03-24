<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Monitor
 *
 * @package Racing_Centers
 */
class RC_Tag_Monitor extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-monitor'; }
	public function get_title(): string { return __( 'RC – Monitor', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_monitor'; }
}
