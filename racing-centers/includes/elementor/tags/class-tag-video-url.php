<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: URL do Vídeo (Conteúdo section — URL category)
 *
 * @package Racing_Centers
 */
class RC_Tag_Video_URL extends RC_Tag_URL_Base {
	public function get_name(): string { return 'rc-video-url'; }
	public function get_title(): string { return __( 'RC – URL do Vídeo', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_video_url'; }
}
