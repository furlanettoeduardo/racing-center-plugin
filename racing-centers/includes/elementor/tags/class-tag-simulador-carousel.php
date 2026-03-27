<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dynamic Tag: Simuladores Carousel (TEXT / HTML category)
 *
 * Reads the JSON stored in `rc_simuladores` and renders the full
 * "O QUE VOCÊ VAI ENCONTRAR:" section with:
 *   - Section header (fixed text) + prev/next arrows
 *   - One slide per simulator: image + badge + title + subtitle +
 *     description + 4 spec boxes + equipment list + CTA button
 *   - Dot navigation
 *
 * Empty fields are automatically hidden; arrows/dots hidden when
 * there is only one simulator.
 *
 * @package Racing_Centers
 */
class RC_Tag_Simulador_Carousel extends RC_Tag_Base {

	public function get_name(): string {
		return 'rc-simulador-carousel';
	}

	public function get_title(): string {
		return __( 'RC – Simuladores (Carousel)', 'racing-centers' );
	}

	public function get_categories(): array {
		return array( \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY );
	}

	public function render(): void {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return;
		}

		$raw  = (string) get_post_meta( $post_id, 'rc_simuladores', true );
		$sims = array();
		if ( $raw ) {
			$decoded = json_decode( $raw, true );
			if ( is_array( $decoded ) ) {
				$sims = $decoded;
			}
		}
		if ( empty( $sims ) ) {
			return;
		}

		$uid        = 'rc-sims-' . absint( $post_id );
		$has_many   = count( $sims ) > 1;
		?>

		<div class="rc-sims" id="<?php echo esc_attr( $uid ); ?>">

			<?php /* ── Section header ──────────────────────────────────── */ ?>
			<div class="rc-sims__head">
				<div class="rc-sims__head-left">
					<p class="rc-sims__eyebrow"><span class="rc-sims__eyebrow-line"></span>SIMULADORES</p>
					<h2 class="rc-sims__section-title">O QUE VOCÊ VAI ENCONTRAR:</h2>
				</div>
				<?php if ( $has_many ) : ?>
				<div class="rc-sims__nav" aria-label="<?php esc_attr_e( 'Navegação dos simuladores', 'racing-centers' ); ?>">
					<button type="button" class="rc-sims__arrow rc-sims__arrow--prev" aria-label="<?php esc_attr_e( 'Anterior', 'racing-centers' ); ?>">&#8249;</button>
					<button type="button" class="rc-sims__arrow rc-sims__arrow--next" aria-label="<?php esc_attr_e( 'Próximo', 'racing-centers' ); ?>">&#8250;</button>
				</div>
				<?php endif; ?>
			</div>

			<?php /* ── Slides ───────────────────────────────────────────── */ ?>
			<div class="rc-sims__slides">
				<?php foreach ( $sims as $i => $sim ) :
					$imagem     = absint( $sim['imagem'] ?? 0 );
					$img_url    = $imagem ? wp_get_attachment_image_url( $imagem, 'large' ) : '';
					$img_alt    = $imagem ? (string) get_post_meta( $imagem, '_wp_attachment_image_alt', true ) : '';
					$linha      = sanitize_text_field( $sim['linha'] ?? '' );
					$titulo     = sanitize_text_field( $sim['titulo'] ?? '' );
					$subtitulo  = sanitize_text_field( $sim['subtitulo'] ?? '' );
					$descricao  = sanitize_textarea_field( $sim['descricao'] ?? '' );
					$dd         = sanitize_text_field( $sim['dd_modelo'] ?? '' );
					$volante    = sanitize_text_field( $sim['volante_modelo'] ?? '' );
					$pedal      = sanitize_text_field( $sim['pedal_modelo'] ?? '' );
					$monitor    = sanitize_text_field( $sim['monitor'] ?? '' );
					$perifericos = array_filter( array_map( 'trim', explode( ';', $sim['perifericos_lista'] ?? '' ) ) );
					$link       = esc_url( $sim['link'] ?? '' );

					$specs = array_filter( array(
						__( 'Direct Drive', 'racing-centers' ) => $dd,
						__( 'Volante', 'racing-centers' )      => $volante,
						__( 'Pedais', 'racing-centers' )       => $pedal,
						__( 'Monitor', 'racing-centers' )      => $monitor,
					) );
				?>
				<div class="rc-sims__slide<?php echo 0 === $i ? ' is-active' : ''; ?>" role="group" aria-label="<?php echo esc_attr( sprintf( __( 'Simulador %d de %d', 'racing-centers' ), $i + 1, count( $sims ) ) ); ?>">
					<div class="rc-sims__card">

						<?php /* Image column */ ?>
						<div class="rc-sims__img-col">
							<div class="rc-sims__img-box">
								<?php if ( $linha ) : ?>
									<span class="rc-sims__badge"><?php echo esc_html( $linha ); ?></span>
								<?php endif; ?>
								<?php if ( $img_url ) : ?>
									<img class="rc-sims__img" src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ?: $titulo ); ?>" loading="lazy" />
								<?php endif; ?>
							</div>
						</div>

						<?php /* Content column */ ?>
						<div class="rc-sims__content-col">

							<?php if ( $titulo ) : ?>
								<h3 class="rc-sims__nome"><?php echo esc_html( $titulo ); ?></h3>
							<?php endif; ?>

							<?php if ( $subtitulo ) : ?>
								<p class="rc-sims__subtitulo"><?php echo esc_html( $subtitulo ); ?></p>
							<?php endif; ?>

							<?php if ( $descricao ) : ?>
								<p class="rc-sims__descricao"><?php echo esc_html( $descricao ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $specs ) ) : ?>
								<div class="rc-sims__specs">
									<?php foreach ( $specs as $label => $value ) : ?>
										<div class="rc-sims__spec">
											<span class="rc-sims__spec-label"><?php echo esc_html( $label ); ?></span>
											<strong class="rc-sims__spec-value"><?php echo esc_html( $value ); ?></strong>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>

							<?php if ( ! empty( $perifericos ) ) : ?>
								<p class="rc-sims__perf-heading"><?php esc_html_e( 'Equipamentos e Periféricos', 'racing-centers' ); ?></p>
								<ul class="rc-sims__perf-list">
									<?php foreach ( $perifericos as $item ) : ?>
										<li><?php echo esc_html( $item ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<?php if ( $link ) : ?>
								<a href="<?php echo esc_url( $link ); ?>" class="rc-sims__cta" target="_blank" rel="noopener noreferrer">
									<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
									<?php esc_html_e( 'Ver na Loja', 'racing-centers' ); ?>
								</a>
							<?php endif; ?>

						</div><?php /* /.rc-sims__content-col */ ?>
					</div><?php /* /.rc-sims__card */ ?>
				</div><?php /* /.rc-sims__slide */ ?>
				<?php endforeach; ?>
			</div><?php /* /.rc-sims__slides */ ?>

			<?php if ( $has_many ) : ?>
			<div class="rc-sims__dots" role="tablist" aria-label="<?php esc_attr_e( 'Selecionar simulador', 'racing-centers' ); ?>">
				<?php foreach ( $sims as $i => $sim ) : ?>
					<button
						type="button"
						class="rc-sims__dot<?php echo 0 === $i ? ' is-active' : ''; ?>"
						data-index="<?php echo esc_attr( $i ); ?>"
						role="tab"
						aria-label="<?php echo esc_attr( sprintf( __( 'Simulador %d', 'racing-centers' ), $i + 1 ) ); ?>"
					></button>
				<?php endforeach; ?>
			</div>
			<?php endif; ?>

		</div><?php /* /.rc-sims */ ?>

		<?php /* ── Scoped styles ─────────────────────────────────────── */ ?>
		<style>
		@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Oswald:wght@400;600;700;900&display=swap');

		#<?php echo esc_attr( $uid ); ?> {
			--rc-sim-accent:   #EC1313;
			--rc-sim-txt:      #1a1a1a;
			--rc-sim-muted:    #666;
			--rc-sim-border:   #e0e0e0;
			--rc-sim-radius:   10px;
		}

		/* Head */
		#<?php echo esc_attr( $uid ); ?> .rc-sims__head {
			display: flex;
			align-items: flex-end;
			justify-content: space-between;
			margin-bottom: 28px;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__head-left {
			flex: 1;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__eyebrow {
			display: flex;
			align-items: center;
			gap: 10px;
			font-family: 'Oswald', sans-serif;
			font-size: 14px;
			font-weight: 400;
			letter-spacing: 0.35em;
			text-transform: uppercase;
			color: var(--rc-sim-accent);
			margin: 0 0 8px;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__eyebrow-line {
			display: block;
			width: 40px;
			height: 2px;
			background: currentColor;
			flex-shrink: 0;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__section-title {
			font-family: 'Oswald', sans-serif;
			font-size: clamp(24px, 3vw, 36px);
			font-weight: 900;
			text-transform: uppercase;
			color: var(--rc-sim-txt);
			line-height: 1.1;
			margin: 0;
			white-space: nowrap;
		}

		/* Arrows */
		#<?php echo esc_attr( $uid ); ?> .rc-sims__nav {
			display: flex;
			gap: 8px;
			padding-bottom: 4px;
			flex-shrink: 0;
			margin-left: 16px;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__arrow {
			width: 38px;
			height: 38px;
			border: 1px solid var(--rc-sim-border);
			border-radius: 50%;
			background: #fff;
			font-size: 20px;
			line-height: 1;
			cursor: pointer;
			display: flex;
			align-items: center;
			justify-content: center;
			color: var(--rc-sim-txt);
			transition: background .2s, border-color .2s;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__arrow:hover {
			background: var(--rc-sim-accent);
			border-color: var(--rc-sim-accent);
			color: #fff;
		}

		/* Slides */
		@keyframes rc-sims-from-right {
			from { opacity: 0; transform: translateX(32px); }
			to   { opacity: 1; transform: translateX(0); }
		}
		@keyframes rc-sims-from-left {
			from { opacity: 0; transform: translateX(-32px); }
			to   { opacity: 1; transform: translateX(0); }
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__slide {
			display: none;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__slide.is-active {
			display: block;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__slide.is-active.slide-from-right {
			animation: rc-sims-from-right .35s ease forwards;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__slide.is-active.slide-from-left {
			animation: rc-sims-from-left .35s ease forwards;
		}

		/* Card layout */
		#<?php echo esc_attr( $uid ); ?> .rc-sims__card {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 40px;
			align-items: start;
		}
		@media (max-width: 767px) {
			#<?php echo esc_attr( $uid ); ?> .rc-sims__card {
				grid-template-columns: 1fr;
				gap: 24px;
			}
		}

		/* Image column */
		#<?php echo esc_attr( $uid ); ?> .rc-sims__img-box {
			position: relative;
			background: #fff;
			border-radius: var(--rc-sim-radius);
			padding: 24px;
			box-shadow: 0 2px 16px rgba(0,0,0,.07);
			line-height: 0;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__img {
			display: block;
			width: 100%;
			height: auto;
			border-radius: 6px;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__badge {
			position: absolute;
			top: 16px;
			left: 16px;
			background: var(--rc-sim-accent);
			color: #fff;
			font-family: 'Oswald', sans-serif;
			font-size: 10px;
			font-weight: 600;
			letter-spacing: .1em;
			text-transform: uppercase;
			padding: 4px 10px;
			border-radius: 3px;
			line-height: 1.4;
		}

		/* Content column */
		#<?php echo esc_attr( $uid ); ?> .rc-sims__nome {
			font-family: 'Oswald', sans-serif;
			font-size: clamp(18px, 2vw, 24px);
			font-weight: 900;
			text-transform: uppercase;
			color: var(--rc-sim-txt);
			margin: 0 0 6px;
			line-height: 1.2;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__subtitulo {
			font-family: 'Oswald', sans-serif;
			font-size: 15px;
			color: var(--rc-sim-accent);
			font-weight: 600;
			margin: 0 0 14px;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__descricao {
			font-family: 'Inter', sans-serif;
			font-size: 14px;
			color: var(--rc-sim-muted);
			line-height: 1.65;
			margin: 0 0 20px;
		}

		/* Spec boxes */
		#<?php echo esc_attr( $uid ); ?> .rc-sims__specs {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(90px, 1fr));
			gap: 8px;
			margin-bottom: 20px;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__spec {
			border: 1px solid var(--rc-sim-border);
			border-radius: 6px;
			padding: 8px 10px;
			display: flex;
			flex-direction: column;
			gap: 3px;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__spec-label {
			font-family: 'Inter', sans-serif;
			font-size: 10px;
			font-weight: 600;
			color: var(--rc-sim-muted);
			text-transform: uppercase;
			letter-spacing: .06em;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__spec-value {
			font-family: 'Inter', sans-serif;
			font-size: 13px;
			font-weight: 700;
			color: var(--rc-sim-txt);
		}

		/* Periféricos */
		#<?php echo esc_attr( $uid ); ?> .rc-sims__perf-heading {
			font-family: 'Inter', sans-serif;
			font-size: 11px;
			font-weight: 700;
			letter-spacing: .1em;
			text-transform: uppercase;
			color: var(--rc-sim-muted);
			margin: 0 0 10px;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__perf-list {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 0 20px;
			list-style: none;
			padding: 0;
			margin: 0 0 22px;
		}
		@media (max-width: 480px) {
			#<?php echo esc_attr( $uid ); ?> .rc-sims__perf-list {
				grid-template-columns: 1fr;
			}
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__perf-list li {
			font-family: 'Inter', sans-serif;
			font-size: 13px;
			color: var(--rc-sim-txt);
			padding: 3px 0 3px 16px;
			position: relative;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__perf-list li::before {
			content: '';
			display: block;
			position: absolute;
			left: 0;
			top: 50%;
			transform: translateY(-50%);
			width: 7px;
			height: 7px;
			border-radius: 50%;
			background: var(--rc-sim-accent);
		}

		/* CTA button */
		#<?php echo esc_attr( $uid ); ?> .rc-sims__cta {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			background: var(--rc-sim-accent);
			color: #fff;
			font-family: 'Inter', sans-serif;
			font-size: 13px;
			font-weight: 700;
			text-transform: uppercase;
			letter-spacing: .06em;
			padding: 12px 24px;
			border-radius: 6px;
			text-decoration: none;
			transition: background .2s, transform .15s;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__cta:hover {
			background: #c51010;
			transform: translateY(-1px);
			color: #fff;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__cta svg {
			flex-shrink: 0;
		}

		/* Dots */
		#<?php echo esc_attr( $uid ); ?> .rc-sims__dots {
			display: flex;
			justify-content: center;
			gap: 8px;
			margin-top: 28px;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__dot {
			width: 10px;
			height: 10px;
			border-radius: 50%;
			border: none;
			background: var(--rc-sim-border);
			cursor: pointer;
			padding: 0;
			transition: background .2s, transform .2s;
		}
		#<?php echo esc_attr( $uid ); ?> .rc-sims__dot.is-active {
			background: var(--rc-sim-accent);
			transform: scale(1.3);
		}
		</style>

		<?php /* ── Carousel JS ──────────────────────────────────────────── */ ?>
		<script>
		(function () {
			var uid     = <?php echo wp_json_encode( $uid ); ?>;
			var current = 0;

			function init() {
				var wrap   = document.getElementById(uid);
				if (!wrap) return;

				var slides  = wrap.querySelectorAll('.rc-sims__slide');
				var dots    = wrap.querySelectorAll('.rc-sims__dot');
				var btnPrev = wrap.querySelector('.rc-sims__arrow--prev');
				var btnNext = wrap.querySelector('.rc-sims__arrow--next');

				if (slides.length <= 1) return;

				function goTo(n, dir) {
					slides[current].classList.remove('is-active', 'slide-from-right', 'slide-from-left');
					if (dots[current]) dots[current].classList.remove('is-active');
					current = ((n % slides.length) + slides.length) % slides.length;
					var s = slides[current];
					s.classList.remove('slide-from-right', 'slide-from-left');
					// Force reflow so the animation retriggers when same direction is used twice.
					void s.offsetWidth;
					s.classList.add(dir === 'prev' ? 'slide-from-left' : 'slide-from-right');
					s.classList.add('is-active');
					if (dots[current]) dots[current].classList.add('is-active');
				}

				if (btnPrev) btnPrev.addEventListener('click', function () { goTo(current - 1, 'prev'); });
				if (btnNext) btnNext.addEventListener('click', function () { goTo(current + 1, 'next'); });

				dots.forEach(function (dot, i) {
					dot.addEventListener('click', function () {
						goTo(i, i >= current ? 'next' : 'prev');
					});
				});
			}

			if (document.readyState === 'loading') {
				document.addEventListener('DOMContentLoaded', init);
			} else {
				init();
			}
			if (window.elementorFrontend) {
				window.elementorFrontend.hooks.addAction('frontend/element_ready/global', init);
			}
		})();
		</script>

		<?php
	}
}
