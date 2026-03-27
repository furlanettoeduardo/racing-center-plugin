<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Dynamic Tag: Simulador Produtos em Destaque (Cards) — TEXT / HTML category
 *
 * Reads `produtos_ids` from the first simulator in `rc_simuladores` (or falls
 * back to the flat `rc_produtos_ids` meta field), fetches each WooCommerce
 * product and renders styled clickable cards:
 *
 *   • Strict 4-column grid (always inline, one card per product)
 *   • Fixed-size image area (220 × 220 px) with #fafafa background, centred
 *   • Sale % badge (red) — top-right of image area; no ESGOTADO badge
 *   • Info section below image: name (bold uppercase), original price
 *     (grey strikethrough, only when on sale), current price (large bold),
 *     Pix price in red (if available — % via 'rc_pix_discount_percent' filter)
 *
 * @package Racing_Centers
 */
class RC_Tag_Simulador_Produtos_Cards extends RC_Tag_Base {

	public function get_name(): string {
		return 'rc-simulador-produtos-cards';
	}

	public function get_title(): string {
		return __( 'RC – Simulador Produtos (Cards)', 'racing-centers' );
	}

	public function get_categories(): array {
		return array( \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY );
	}

	public function render(): void {
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return;
		}

		if ( ! function_exists( 'wc_get_product' ) ) {
			return;
		}

		$ids_raw = $this->get_produtos_ids( $post_id );
		if ( ! $ids_raw ) {
			return;
		}

		$ids = array_values( array_filter( array_map( 'absint', explode( ',', $ids_raw ) ) ) );
		if ( empty( $ids ) ) {
			return;
		}

		/**
		 * Percentage discount applied for Pix payment.
		 * Override via: add_filter( 'rc_pix_discount_percent', fn() => 5 );
		 */
		$pix_pct = (float) apply_filters( 'rc_pix_discount_percent', 3 );

		$this->print_styles();
		?>

		<div class="rc-produtos-cards">
			<?php foreach ( $ids as $product_id ) :
				$product = wc_get_product( $product_id );
				if ( ! $product || ! $product->is_visible() ) {
					continue;
				}

				$is_on_sale   = $product->is_on_sale();
				$regular      = (float) $product->get_regular_price();
				$current      = (float) $product->get_price();

				$discount_pct = ( $is_on_sale && $regular > 0 )
					? (int) round( ( $regular - $current ) / $regular * 100 )
					: 0;

				$pix_price = ( $current > 0 && $pix_pct > 0 )
					? $current * ( 1 - $pix_pct / 100 )
					: 0.0;

				$img_id  = $product->get_image_id();
				$img_url = $img_id
					? wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' )
					: wc_placeholder_img_src( 'woocommerce_thumbnail' );
				$img_alt = $img_id
					? (string) get_post_meta( $img_id, '_wp_attachment_image_alt', true )
					: $product->get_name();
				?>

				<a href="<?php echo esc_url( $product->get_permalink() ); ?>"
				   class="rc-produto-card"
				   aria-label="<?php echo esc_attr( $product->get_name() ); ?>">

					<div class="rc-produto-card__img">
						<?php if ( $is_on_sale && $discount_pct > 0 ) : ?>
							<span class="rc-produto-card__badge">
								-<?php echo esc_html( $discount_pct ); ?>%
							</span>
						<?php endif; ?>
						<img src="<?php echo esc_url( $img_url ); ?>"
						     alt="<?php echo esc_attr( $img_alt ); ?>"
						     loading="lazy" />
					</div>

					<div class="rc-produto-card__info">
						<h4 class="rc-produto-card__name"><?php echo esc_html( $product->get_name() ); ?></h4>

						<div class="rc-produto-card__price">
							<?php if ( $is_on_sale && $regular > 0 ) : ?>
								<p class="rc-produto-card__regular-price">
									<?php echo wp_kses_post( wc_price( $regular ) ); ?>
								</p>
							<?php endif; ?>
							<?php if ( $current > 0 ) : ?>
								<p class="rc-produto-card__current-price">
									<?php echo wp_kses_post( wc_price( $current ) ); ?>
								</p>
								<?php if ( $pix_price > 0 ) : ?>
									<p class="rc-produto-card__pix-price">
										<?php echo wp_kses_post( wc_price( $pix_price ) ); ?> <?php esc_html_e( 'no Pix', 'racing-centers' ); ?>
									</p>
								<?php endif; ?>
							<?php endif; ?>
						</div>
					</div>

				</a>

			<?php endforeach; ?>
		</div>

		<?php
	}

	// -------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------

	/**
	 * Resolve the comma-separated products IDs string for the given post.
	 *
	 * Priority:
	 *   1. `produtos_ids` on the first simulator in the `rc_simuladores` repeater.
	 *   2. Flat `rc_produtos_ids` meta field (legacy / fallback).
	 */
	private function get_produtos_ids( int $post_id ): string {
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

		if ( ! empty( $sims[0]['produtos_ids'] ) ) {
			return $sims[0]['produtos_ids'];
		}

		return (string) get_post_meta( $post_id, 'rc_produtos_ids', true );
	}

	/**
	 * Output the shared CSS once per page, regardless of how many instances
	 * of the tag are rendered.
	 */
	private function print_styles(): void {
		static $printed = false;
		if ( $printed ) {
			return;
		}
		$printed = true;
		?>
		<style id="rc-produtos-cards-styles">
		/* ── Grid: always 4 columns, no wrapping ── */
		.rc-produtos-cards {
			display: grid;
			grid-template-columns: repeat(4, 1fr);
			gap: 16px;
			width: 100%;
		}
		@media (max-width: 900px) {
			.rc-produtos-cards { grid-template-columns: repeat(2, 1fr); }
		}
		@media (max-width: 500px) {
			.rc-produtos-cards { grid-template-columns: 1fr 1fr; }
		}

		/* ── Card wrapper ── */
		a.rc-produto-card {
			display: flex;
			flex-direction: column;
			text-decoration: none;
			color: inherit;
			border: 1px solid #e2e2e2;
			border-radius: 8px;
			overflow: hidden;
			background: #ffffff;
			transition: box-shadow 0.2s ease, transform 0.2s ease;
		}
		a.rc-produto-card:hover {
			box-shadow: 0 8px 24px rgba(0, 0, 0, 0.10);
			transform: translateY(-3px);
			text-decoration: none;
		}

		/* ── Image area ── */
		.rc-produto-card__img {
			position: relative;
			width: 100%;
			height: 220px;
			background: #fafafa;
			display: flex;
			align-items: center;
			justify-content: center;
			overflow: hidden;
		}
		.rc-produto-card__img img {
			width: 180px;
			height: 180px;
			object-fit: contain;
			display: block;
		}

		/* ── Sale badge (inside image area) ── */
		.rc-produto-card__badge {
			position: absolute;
			top: 10px;
			right: 10px;
			background: #EC1313;
			color: #fff;
			font-size: 11px;
			font-weight: 700;
			line-height: 1;
			padding: 4px 8px;
			border-radius: 4px;
			z-index: 1;
		}

		/* ── Info section ── */
		.rc-produto-card__info {
			padding: 14px 16px 16px;
			display: flex;
			flex-direction: column;
			flex: 1;
		}

		/* ── Product name ── */
		.rc-produto-card__name {
			font-size: 13px;
			font-weight: 700;
			color: #1a1a1a;
			text-transform: uppercase;
			line-height: 1.3;
			margin: 0 0 10px;
			flex: 1;
		}

		/* ── Price block ── */
		.rc-produto-card__price {
			margin-top: auto;
			display: flex;
			flex-direction: column;
			gap: 2px;
		}

		.rc-produto-card__regular-price,
		.rc-produto-card__regular-price .woocommerce-Price-amount {
			font-size: 12px;
			color: #9e9e9e;
			text-decoration: line-through;
			margin: 0;
		}

		.rc-produto-card__current-price,
		.rc-produto-card__current-price .woocommerce-Price-amount {
			font-size: 16px;
			font-weight: 700;
			color: #1a1a1a;
			margin: 0;
		}

		.rc-produto-card__pix-price,
		.rc-produto-card__pix-price .woocommerce-Price-amount {
			font-size: 12px;
			color: #EC1313;
			margin: 0;
		}
		</style>
		<?php
	}
}
