<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Texto de Apresentação (Conteúdo section)
 *
 * Multi-line text field — value is stored without HTML (sanitize_textarea_field).
 * Uses nl2br so line breaks are preserved when the tag is used in HTML contexts.
 *
 * @package Racing_Centers
 */
class RC_Tag_Texto_Apresentacao extends RC_Tag_Base {

	public function get_name(): string {
		return 'rc-texto-apresentacao';
	}

	public function get_title(): string {
		return __( 'RC – Texto de Apresentação', 'racing-centers' );
	}

	public function get_categories(): array {
		return array( \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY );
	}

	public function render(): void {
		$value = $this->rc_meta( 'rc_texto_apresentacao' );
		if ( ! $value ) {
			return;
		}
		// Preserve line breaks for multi-line display in text widgets.
		echo nl2br( esc_html( $value ) );
	}
}
