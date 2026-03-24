<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Imagem do Simulador (IMAGE category)
 *
 * @package Racing_Centers
 */
class RC_Tag_Simulador_Imagem extends RC_Tag_Image_Base {
	public function get_name(): string { return 'rc-simulador-imagem'; }
	public function get_title(): string { return __( 'RC – Simulador Imagem', 'racing-centers' ); }
	protected function get_meta_key(): string { return 'rc_simulador_imagem'; }
}
