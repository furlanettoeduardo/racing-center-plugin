<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Google Place ID (Depoimentos section)
 *
 * @package Racing_Centers
 */
class RC_Tag_Google_Place_ID extends RC_Tag_Text_Base {
	public function get_name(): string { return 'rc-google-place-id'; }
	public function get_title(): string { return __( 'RC – Google Place ID', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_google_place_id'; }
}
