<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: IDs de Produtos do Simulador (comma-separated)
 *
 * @package Racing_Centers
 */
class RC_Tag_Simulador_Produtos_IDs extends RC_Tag_Base {

	public function get_name(): string  { return 'rc-simulador-produtos-ids'; }
	public function get_title(): string { return __( 'RC – Simulador Produtos IDs', 'racing-centers' ); }

	public function get_categories(): array {
		return array( \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY );
	}

	/**
	 * Returns comma-separated product IDs from the first (or only) simulator
	 * in the `rc_simuladores` repeater stored for the current post.
	 */
	public function render(): void {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return;
		}

		$stored = get_post_meta( $post_id, 'rc_simuladores', true );
		$sims   = array();
		if ( is_array( $stored ) ) {
			$sims = $stored;
		} elseif ( is_string( $stored ) && $stored ) {
			$decoded = json_decode( $stored, true );
			if ( is_array( $decoded ) ) {
				$sims = $decoded;
			}
		}

		$ids = ! empty( $sims[0]['produtos_ids'] ) ? $sims[0]['produtos_ids'] : '';
		echo esc_html( $ids );
	}
}
