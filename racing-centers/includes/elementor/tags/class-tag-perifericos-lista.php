<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Periféricos Lista
 *
 * Stored one item per line. Outputs as a plain-text, newline-separated
 * string so Elementor text widgets can display it; line breaks are
 * preserved via nl2br for HTML contexts.
 *
 * @package Racing_Centers
 */
class RC_Tag_Perifericos_Lista extends RC_Tag_Base {

	public function get_name(): string {
		return 'rc-perifericos-lista';
	}

	public function get_title(): string {
		return __( 'RC – Periféricos Lista', 'racing-centers' );
	}

	public function get_categories(): array {
		return array( \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY );
	}

	public function render(): void {
		$value = $this->rc_meta( 'rc_perifericos_lista' );
		if ( ! $value ) {
			return;
		}
		echo nl2br( esc_html( $value ) );
	}
}
