<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Dynamic Tag: Mapa Embed (Localização section)
 *
 * The value is stored as a sanitised <iframe> string (via wp_kses).
 * On render, it is passed through wp_kses() again so only the
 * safe iframe attributes are allowed through — never arbitrary HTML.
 *
 * Because the output contains raw HTML, this tag is categorised as TEXT
 * so it can be used inside Elementor's HTML widget or raw text containers.
 *
 * @package Racing_Centers
 */
class RC_Tag_Mapa_Embed extends RC_Tag_Base {

	/**
	 * Allowed HTML for the map embed output.
	 * Mirrors the allowlist used during save in class-save.php.
	 *
	 * @var array<string, array<string, bool>>
	 */
	private const ALLOWED_HTML = array(
		'iframe' => array(
			'src'             => true,
			'width'           => true,
			'height'          => true,
			'frameborder'     => true,
			'allowfullscreen' => true,
			'loading'         => true,
			'referrerpolicy'  => true,
			'style'           => true,
			'title'           => true,
		),
	);

	public function get_name(): string {
		return 'rc-mapa-embed';
	}

	public function get_title(): string {
		return __( 'RC – Mapa Embed', 'racing-centers' );
	}

	public function get_categories(): array {
		return array( \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY );
	}

	/**
	 * Output the safe iframe HTML.
	 */
	public function render(): void {
		$raw = $this->rc_meta( 'rc_mapa_embed' );
		if ( ! $raw ) {
			return;
		}
		// Second-pass sanitization — defence in depth.
		echo wp_kses( $raw, self::ALLOWED_HTML );
	}
}
