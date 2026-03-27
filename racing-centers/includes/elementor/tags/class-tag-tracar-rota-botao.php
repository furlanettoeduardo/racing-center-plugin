<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Botão Traçar Rota (TEXT / HTML category)
 *
 * Renders a full styled button linking to `rc_tracar_rota_url`.
 * Returns nothing when the URL is not set.
 *
 * @package Racing_Centers
 */
class RC_Tag_Tracar_Rota_Botao extends RC_Tag_Base {

	public function get_name(): string {
		return 'rc-tracar-rota-botao';
	}

	public function get_title(): string {
		return __( 'RC – Traçar Rota (Botão)', 'racing-centers' );
	}

	public function get_categories(): array {
		return array( \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY );
	}

	public function render(): void {
		$url = esc_url( $this->rc_meta( 'rc_tracar_rota_url' ) );
		if ( ! $url ) {
			return;
		}
		?>
		<a href="<?php echo $url; ?>"
		   class="rc-tracar-rota-btn"
		   target="_blank"
		   rel="noopener noreferrer">
			<svg class="rc-tracar-rota-btn__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
				<polygon points="3 11 22 2 13 21 11 13 3 11"/>
			</svg>
			<?php esc_html_e( 'TRAÇAR ROTA', 'racing-centers' ); ?>
		</a>
		<?php
	}
}
