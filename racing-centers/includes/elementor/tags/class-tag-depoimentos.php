<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dynamic Tag: Depoimentos (Trustindex Reviews) — TEXT / HTML category
 *
 * Renders the Trustindex widget shortcode stored per Racing Center in
 * `rc_depoimentos_shortcode`, wrapped in scoped CSS that overrides
 * Trustindex's default styles to match the Racing Center design:
 *
 *   • Dark cards (#2a2a2a) with rounded corners
 *   • Large red opening-quote decoration (::before)
 *   • Yellow star rating
 *   • White review text
 *   • Bold white reviewer name + grey review date as subtitle
 *
 * The section header (eyebrow text, title, background) should be built
 * directly in the Elementor template — only the cards grid is output here.
 *
 * @package Racing_Centers
 */
class RC_Tag_Depoimentos extends RC_Tag_Base {

	public function get_name(): string {
		return 'rc-depoimentos';
	}

	public function get_title(): string {
		return __( 'RC – Depoimentos (Reviews)', 'racing-centers' );
	}

	public function get_categories(): array {
		return array( \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY );
	}

	public function render(): void {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return;
		}

		$shortcode = get_post_meta( $post_id, 'rc_depoimentos_shortcode', true );
		if ( ! $shortcode ) {
			return;
		}

		// Unique ID used to scope both CSS and JS to this instance.
		$uid = 'rc-dep-' . absint( $post_id );

		// ── Scoped CSS ────────────────────────────────────────────────────────
		// Targets Trustindex class names documented in the Trustindex plugin.
		// Using .rc-dep-wrap as the scoping ancestor so we never leak styles.
		?>
		<style>
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap * {
			box-sizing: border-box;
		}

		/* ── Strip Trustindex container backgrounds ── */
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-widget,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-widget-loader,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-reviews-container {
			background: transparent !important;
			border: none !important;
			box-shadow: none !important;
		}

		/* ── Individual review card ── */
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-review-item {
			background: #2a2a2a !important;
			border: none !important;
			border-radius: 12px !important;
			padding: 52px 24px 24px 24px !important;
			position: relative !important;
			box-shadow: none !important;
			overflow: hidden !important;
		}

		/* ── Red opening-quote decoration ── */
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-review-item::before {
			content: '\201C';
			color: #EC1313;
			font-size: 80px;
			font-family: Georgia, 'Times New Roman', serif;
			line-height: 1;
			position: absolute;
			top: 8px;
			left: 20px;
		}

		/* ── Yellow stars ── */
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-stars,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-stars *,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-rating,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap [class*="ti-star"] {
			color: #F5B300 !important;
			fill: #F5B300 !important;
		}
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-stars svg path {
			fill: #F5B300 !important;
		}

		/* ── Review text ── */
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-review-text,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-review-text * {
			color: #ffffff !important;
			font-size: 14px !important;
			line-height: 1.65 !important;
		}

		/* ── Read-more link ── */
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap a.ti-read-more,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-read-more {
			color: #EC1313 !important;
			font-size: 13px !important;
		}

		/* ── Review footer (name + date row) ── */
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-review-footer,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-reviewer {
			background: transparent !important;
			border-top: 1px solid rgba(255,255,255,0.12) !important;
			margin-top: 16px !important;
			padding-top: 14px !important;
			display: flex !important;
			align-items: center !important;
			gap: 10px !important;
		}

		/* ── Reviewer name ── */
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-name,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-name a,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-reviewer-name {
			color: #ffffff !important;
			font-weight: 700 !important;
			font-size: 15px !important;
			text-decoration: none !important;
		}

		/* ── Review date (used as subtitle / role) ── */
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-review-date,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-date {
			color: #a0a0a0 !important;
			font-size: 12px !important;
		}

		/* ── Hide profile photo ── */
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-profile-photo,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-reviewer-photo,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-reviewer img {
			display: none !important;
		}

		/* ── Hide Google / platform icon ── */
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-review-platform,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-platform-icon,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap [class*="ti-google"],
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-widget-footer {
			display: none !important;
		}

		/* ── "Powered by" bar ── */
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-widget-header,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-powered-by {
			display: none !important;
		}

		/* ── Navigation arrows (slider variant) ── */
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-left-arrow,
		#<?php echo esc_attr( $uid ); ?>.rc-dep-wrap .ti-right-arrow {
			background: #EC1313 !important;
			border-radius: 50% !important;
			color: #fff !important;
		}
		</style>

		<div class="rc-dep-wrap" id="<?php echo esc_attr( $uid ); ?>">
			<?php echo do_shortcode( $shortcode ); ?>
		</div>
		<?php
	}
}
