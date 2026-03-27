<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dynamic Tag: Galeria de Imagens (HTML category)
 *
 * Renders the gallery stored in `rc_galeria` as:
 *   - 1 full-width main image (top)
 *   - Scrollable thumbnail strip / carousel (bottom)
 *
 * Clicking a thumbnail swaps the main image.
 * Uses Swiper.js when available (Elementor already loads it on pages that
 * include slider/carousel widgets), otherwise falls back to a simple
 * scrollable flex strip with vanilla JS click handlers.
 *
 * @package Racing_Centers
 */
class RC_Tag_Galeria extends RC_Tag_Base {

	public function get_name(): string {
		return 'rc-galeria';
	}

	public function get_title(): string {
		return __( 'RC – Galeria', 'racing-centers' );
	}

	public function get_categories(): array {
		return array( \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY );
	}

	public function render(): void {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return;
		}

		$raw_ids = (string) get_post_meta( $post_id, 'rc_galeria', true );
		$ids     = array_values(
			array_filter( array_map( 'absint', explode( ',', $raw_ids ) ) )
		);

		if ( empty( $ids ) ) {
			return;
		}

		// Build image data array.
		$images = array();
		foreach ( $ids as $id ) {
			$full  = wp_get_attachment_image_url( $id, 'full' );
			$thumb = wp_get_attachment_image_url( $id, 'medium' );
			$alt   = (string) get_post_meta( $id, '_wp_attachment_image_alt', true );

			if ( $full ) {
				$images[] = array(
					'full'  => $full,
					'thumb' => $thumb ?: $full,
					'alt'   => $alt,
				);
			}
		}

		if ( empty( $images ) ) {
			return;
		}

		// Unique ID to scope JS/CSS to this instance.
		$uid = 'rc-gal-' . absint( $post_id );
		?>

		<div class="rc-gallery-wrap" id="<?php echo esc_attr( $uid ); ?>">

			<?php /* ── Main image ──────────────────────────────────────── */ ?>
			<div class="rc-gallery-main">
				<img
					class="rc-gallery-main__img"
					src="<?php echo esc_url( $images[0]['full'] ); ?>"
					alt="<?php echo esc_attr( $images[0]['alt'] ); ?>"
				/>
			</div>

			<?php if ( count( $images ) > 1 ) : ?>
			<?php /* ── Thumbnail carousel ──────────────────────────────── */ ?>
			<div class="rc-gallery-strip">
				<button type="button" class="rc-gallery-strip__arrow rc-gallery-strip__arrow--prev" aria-label="<?php esc_attr_e( 'Anterior', 'racing-centers' ); ?>">&#8249;</button>

				<div class="rc-gallery-strip__viewport">
					<div class="rc-gallery-strip__track">
						<?php foreach ( $images as $i => $img ) : ?>
						<button
							type="button"
							class="rc-gallery-strip__thumb<?php echo 0 === $i ? ' is-active' : ''; ?>"
							data-full="<?php echo esc_url( $img['full'] ); ?>"
							data-alt="<?php echo esc_attr( $img['alt'] ); ?>"
						>
							<img src="<?php echo esc_url( $img['thumb'] ); ?>" alt="<?php echo esc_attr( $img['alt'] ); ?>" loading="lazy" />
						</button>
						<?php endforeach; ?>
					</div>
				</div>

				<button type="button" class="rc-gallery-strip__arrow rc-gallery-strip__arrow--next" aria-label="<?php esc_attr_e( 'Próximo', 'racing-centers' ); ?>">&#8250;</button>
			</div>
			<?php endif; ?>

		</div><!-- /.rc-gallery-wrap -->

		<?php /* ── Scoped styles ─────────────────────────────────────── */ ?>
		<style>
		#<?php echo esc_attr( $uid ); ?> {
			--rc-gal-thumb-size: 60px;
			--rc-gal-gap:        5px;
			--rc-gal-accent:     #e30000;
		}
		@media (min-width: 480px) {
			#<?php echo esc_attr( $uid ); ?> {
				--rc-gal-thumb-size: 72px;
				--rc-gal-gap:        6px;
			}
		}
		@media (min-width: 768px) {
			#<?php echo esc_attr( $uid ); ?> {
				--rc-gal-thumb-size: 90px;
				--rc-gal-gap:        8px;
			}
		}
		#<?php echo esc_attr( $uid ); ?> .rc-gallery-main {
			width: 100%;
			aspect-ratio: 16 / 9;
			overflow: hidden;
			background: #000;
			line-height: 0;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-gallery-main__img {
			display: block;
			width: 100%;
			height: 100%;
			object-fit: cover;
			object-position: center;
			transition: opacity .25s ease;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-gallery-strip {
			display: flex;
			align-items: center;
			gap: var(--rc-gal-gap);
			margin-top: var(--rc-gal-gap);
		}
		#<?php echo esc_attr( $uid ); ?> .rc-gallery-strip__viewport {
			flex: 1;
			overflow: hidden;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-gallery-strip__track {
			display: flex;
			gap: var(--rc-gal-gap);
			transition: transform .3s ease;
			will-change: transform;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-gallery-strip__thumb {
			flex: 0 0 var(--rc-gal-thumb-size);
			width: var(--rc-gal-thumb-size);
			height: var(--rc-gal-thumb-size);
			padding: 0;
			border: 2px solid transparent;
			border-radius: 3px;
			background: none;
			cursor: pointer;
			overflow: hidden;
			transition: border-color .2s;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-gallery-strip__thumb img {
			display: block;
			width: 100%;
			height: 100%;
			object-fit: cover;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-gallery-strip__thumb.is-active,
		#<?php echo esc_attr( $uid ); ?> .rc-gallery-strip__thumb:hover {
			border-color: var(--rc-gal-accent);
		}
		#<?php echo esc_attr( $uid ); ?> .rc-gallery-strip__arrow {
			flex: 0 0 auto;
			width: 32px;
			height: 32px;
			border: none;
			border-radius: 3px;
			background: rgba(0,0,0,.55);
			color: #fff;
			font-size: 22px;
			line-height: 1;
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
			transition: background .2s;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-gallery-strip__arrow:hover {
			background: var(--rc-gal-accent);
		}
		</style>

		<?php /* ── Gallery initialisation script ──────────────────────── */ ?>
		<script>
		(function () {
			var uid      = <?php echo wp_json_encode( $uid ); ?>;
			var offset   = 0;

			function getThumbW(wrap) {
				var first = wrap.querySelector('.rc-gallery-strip__thumb');
				return first ? first.offsetWidth : 80;
			}

			function getGap(track) {
				if (!track) return 6;
				var style = window.getComputedStyle(track);
				return parseFloat(style.columnGap || style.gap) || 6;
			}

			function init() {
				var wrap     = document.getElementById(uid);
				if (!wrap) return;

				var mainImg  = wrap.querySelector('.rc-gallery-main__img');
				var track    = wrap.querySelector('.rc-gallery-strip__track');
				var viewport = wrap.querySelector('.rc-gallery-strip__viewport');
				var btnPrev  = wrap.querySelector('.rc-gallery-strip__arrow--prev');
				var btnNext  = wrap.querySelector('.rc-gallery-strip__arrow--next');
				var thumbs   = wrap.querySelectorAll('.rc-gallery-strip__thumb');

				if (!mainImg || !thumbs.length) return;

				// ── Thumbnail click: swap main image ────────────────────
				thumbs.forEach(function (btn) {
					btn.addEventListener('click', function () {
						mainImg.style.opacity = '0';
						setTimeout(function () {
							mainImg.src = btn.dataset.full;
							mainImg.alt = btn.dataset.alt;
							mainImg.style.opacity = '1';
						}, 150);
						thumbs.forEach(function (b) { b.classList.remove('is-active'); });
						btn.classList.add('is-active');
						scrollThumbIntoView(btn);
					});
				});

				// ── Arrow navigation ────────────────────────────────────
				if (btnPrev && btnNext && track && viewport) {
					btnPrev.addEventListener('click', function () {
						var step = (getThumbW(wrap) + getGap(track)) * 3;
						offset = Math.max(0, offset - step);
						track.style.transform = 'translateX(-' + offset + 'px)';
					});

					btnNext.addEventListener('click', function () {
						var maxOffset = track.scrollWidth - viewport.offsetWidth;
						if (maxOffset <= 0) return;
						var step = (getThumbW(wrap) + getGap(track)) * 3;
						offset = Math.min(maxOffset, offset + step);
						track.style.transform = 'translateX(-' + offset + 'px)';
					});
				}

				function scrollThumbIntoView(btn) {
					if (!track || !viewport) return;
					var tw     = getThumbW(wrap);
					var gap    = getGap(track);
					var btnLeft  = btn.offsetLeft;
					var vpWidth  = viewport.offsetWidth;
					var maxOff   = track.scrollWidth - vpWidth;
					if (btnLeft < offset) {
						offset = Math.max(0, btnLeft - gap);
					} else if (btnLeft + tw > offset + vpWidth) {
						offset = Math.min(maxOff, btnLeft + tw - vpWidth + gap);
					}
					track.style.transform = 'translateX(-' + offset + 'px)';
				}
			}

			// Init after DOM + Elementor frontend are both ready.
			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', init);
			} else {
				init();
			}

			// Re-init after Elementor frontend is ready (editor preview).
			if (window.elementorFrontend) {
				window.elementorFrontend.hooks.addAction('frontend/element_ready/global', init);
			}
		})();
		</script>

		<?php
	}
}
