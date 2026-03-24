<?php
/**
 * Abstract base tag classes — Racing Centers Dynamic Tags
 *
 * Defines three abstract base classes:
 *
 *   RC_Tag_Base         – Root: get_group() + rc_meta() helper.
 *   RC_Tag_Text_Base    – Text category, renders esc_html().
 *   RC_Tag_URL_Base     – URL  category, renders esc_url().
 *   RC_Tag_Image_Base   – Image category, resolves attachment URL.
 *
 * Each concrete tag only needs to declare:
 *   get_name(), get_title(), and (for typed bases) get_meta_key().
 *
 * @package Racing_Centers
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// =============================================================================
// Root base
// =============================================================================

/**
 * RC_Tag_Base
 *
 * Shared foundation for every Racing Center dynamic tag.
 * Provides the group name and a convenience meta-retrieval helper.
 */
abstract class RC_Tag_Base extends \Elementor\Core\DynamicTags\Tag {

	/**
	 * Attach all Racing Center tags to the "racing-centers" Elementor group.
	 *
	 * @return string
	 */
	public function get_group(): string {
		return RC_Dynamic_Tags::GROUP;
	}

	/**
	 * Retrieve a post-meta value for the currently rendered post.
	 *
	 * Returns an empty string gracefully when no post context is available.
	 *
	 * @param string $key Meta key, e.g. 'rc_cidade'.
	 * @return string
	 */
	protected function rc_meta( string $key ): string {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return '';
		}
		return (string) get_post_meta( $post_id, $key, true );
	}
}

// =============================================================================
// Text base
// =============================================================================

/**
 * RC_Tag_Text_Base
 *
 * Use for any field that should appear in the TEXT category and is output
 * as HTML-escaped plain text.
 */
abstract class RC_Tag_Text_Base extends RC_Tag_Base {

	/**
	 * Return the post_meta key this tag reads.
	 *
	 * @return string
	 */
	abstract protected function get_meta_key(): string;

	/**
	 * @return array<string>
	 */
	public function get_categories(): array {
		return array( \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY );
	}

	/**
	 * Echo the sanitised value.
	 */
	public function render(): void {
		echo esc_html( $this->rc_meta( $this->get_meta_key() ) );
	}
}

// =============================================================================
// URL base
// =============================================================================

/**
 * RC_Tag_URL_Base
 *
 * Use for URL fields (video links, external links, etc.).
 */
abstract class RC_Tag_URL_Base extends RC_Tag_Base {

	/**
	 * @return string
	 */
	abstract protected function get_meta_key(): string;

	/**
	 * @return array<string>
	 */
	public function get_categories(): array {
		return array( \Elementor\Modules\DynamicTags\Module::URL_CATEGORY );
	}

	/**
	 * Echo the URL-escaped value.
	 */
	public function render(): void {
		$url = $this->rc_meta( $this->get_meta_key() );
		if ( $url ) {
			echo esc_url( $url );
		}
	}
}

// =============================================================================
// Image base
// =============================================================================

/**
 * RC_Tag_Image_Base
 *
 * Use for fields that store a WP attachment ID and belong to the
 * IMAGE category (Elementor Image widget / background image source).
 *
 * render() outputs the full-size attachment URL — the format Elementor
 * expects for IMAGE_CATEGORY dynamic tags.
 */
abstract class RC_Tag_Image_Base extends RC_Tag_Base {

	/**
	 * @return string
	 */
	abstract protected function get_meta_key(): string;

	/**
	 * @return array<string>
	 */
	public function get_categories(): array {
		return array( \Elementor\Modules\DynamicTags\Module::IMAGE_CATEGORY );
	}

	/**
	 * Echo the attachment URL, or nothing if the attachment does not exist.
	 */
	public function render(): void {
		$attachment_id = absint( $this->rc_meta( $this->get_meta_key() ) );
		if ( ! $attachment_id ) {
			return;
		}

		$url = wp_get_attachment_image_url( $attachment_id, 'full' );
		if ( $url ) {
			echo esc_url( $url );
		}
	}
}
