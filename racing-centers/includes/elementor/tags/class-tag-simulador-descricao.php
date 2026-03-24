<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Simulador – Descrição
 *
 * Multi-line field — preserves line breaks via nl2br.
 *
 * @package Racing_Centers
 */
class RC_Tag_Simulador_Descricao extends RC_Tag_Base {

	public function get_name(): string {
		return 'rc-simulador-descricao';
	}

	public function get_title(): string {
		return __( 'RC – Simulador Descrição', 'racing-centers' );
	}

	public function get_categories(): array {
		return array( \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY );
	}

	public function render(): void {
		$value = $this->rc_meta( 'rc_simulador_descricao' );
		if ( ! $value ) {
			return;
		}
		echo nl2br( esc_html( $value ) );
	}
}
