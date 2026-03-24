<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Hero Image (IMAGE category)
 *
 * @package Racing_Centers
 */
class RC_Tag_Hero_Image extends RC_Tag_Image_Base {
	public function get_name(): string { return 'rc-hero-image'; }
	public function get_title(): string { return __( 'RC – Hero Image', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_hero_image'; }
}
