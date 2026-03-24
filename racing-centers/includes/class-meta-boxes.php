<?php
/**
 * Meta Boxes — Racing Center CPT
 *
 * Registers all meta boxes for the `racing_center` post type and renders
 * their HTML. Each meta box has its own nonce for security.
 *
 * Sections:
 *   1. Hero
 *   2. Informações
 *   3. Conteúdo
 *   4. Produtos / Simulador (with conditional JS toggling)
 *   5. Depoimentos
 *   6. Localização
 *
 * @package Racing_Centers
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RC_Meta_Boxes
 *
 * Registers and renders all meta boxes for the racing_center CPT.
 */
class RC_Meta_Boxes {

	/**
	 * Absolute URL to the plugin root (used for enqueuing assets).
	 *
	 * @var string
	 */
	private string $plugin_url;

	// -------------------------------------------------------------------------
	// Constructor
	// -------------------------------------------------------------------------

	/**
	 * @param string $plugin_url Absolute URL to the plugin directory (no trailing slash).
	 */
	public function __construct( string $plugin_url ) {
		$this->plugin_url = $plugin_url;

		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	// -------------------------------------------------------------------------
	// Asset enqueuing
	// -------------------------------------------------------------------------

	/**
	 * Enqueue admin CSS, JS, and the WP media uploader on the racing_center edit screen.
	 *
	 * Hooked to: admin_enqueue_scripts
	 *
	 * @param string $hook Current admin page hook.
	 */
	public function enqueue_assets( string $hook ): void {
		// Only load on post create/edit screens.
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen || 'racing_center' !== $screen->post_type ) {
			return;
		}

		// WordPress media uploader.
		wp_enqueue_media();

		// Plugin admin stylesheet.
		wp_enqueue_style(
			'rc-admin-css',
			$this->plugin_url . '/assets/css/admin.css',
			array(),
			Racing_Centers::VERSION
		);

		// Plugin admin JS (conditional show/hide + media uploader wiring).
		wp_enqueue_script(
			'rc-admin-js',
			$this->plugin_url . '/assets/js/admin.js',
			array( 'jquery' ),
			Racing_Centers::VERSION,
			true // Load in footer.
		);
	}

	// -------------------------------------------------------------------------
	// Meta box registration
	// -------------------------------------------------------------------------

	/**
	 * Register all meta boxes.
	 *
	 * Hooked to: add_meta_boxes
	 */
	public function register_meta_boxes(): void {
		$boxes = array(
			array(
				'id'       => 'rc_hero',
				'title'    => __( '🖼️ Hero Section', 'racing-centers' ),
				'callback' => 'render_hero',
			),
			array(
				'id'       => 'rc_informacoes',
				'title'    => __( 'ℹ️ Informações', 'racing-centers' ),
				'callback' => 'render_informacoes',
			),
			array(
				'id'       => 'rc_conteudo',
				'title'    => __( '📄 Conteúdo', 'racing-centers' ),
				'callback' => 'render_conteudo',
			),
			array(
				'id'       => 'rc_produtos_simulador',
				'title'    => __( '🎮 Produtos / Simulador', 'racing-centers' ),
				'callback' => 'render_produtos_simulador',
			),
			array(
				'id'       => 'rc_depoimentos',
				'title'    => __( '⭐ Depoimentos', 'racing-centers' ),
				'callback' => 'render_depoimentos',
			),
			array(
				'id'       => 'rc_localizacao',
				'title'    => __( '📍 Localização', 'racing-centers' ),
				'callback' => 'render_localizacao',
			),
		);

		foreach ( $boxes as $box ) {
			add_meta_box(
				$box['id'],
				$box['title'],
				array( $this, $box['callback'] ),
				'racing_center',
				'normal',
				'high'
			);
		}
	}

	// -------------------------------------------------------------------------
	// Render helpers
	// -------------------------------------------------------------------------

	/**
	 * Retrieve a saved meta value, returning an empty string if not set.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key.
	 * @return string
	 */
	private function get_meta( int $post_id, string $key ): string {
		return (string) get_post_meta( $post_id, $key, true );
	}

	/**
	 * Output a text input row.
	 *
	 * @param int    $post_id     Post ID.
	 * @param string $key         Meta key / input name.
	 * @param string $label       Field label.
	 * @param string $description Optional helper text.
	 * @param string $type        Input type (text, email, url).
	 */
	private function text_field(
		int $post_id,
		string $key,
		string $label,
		string $description = '',
		string $type = 'text'
	): void {
		$value = esc_attr( $this->get_meta( $post_id, $key ) );
		?>
		<div class="rc-field-row">
			<label class="rc-field-label" for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
			<input
				type="<?php echo esc_attr( $type ); ?>"
				id="<?php echo esc_attr( $key ); ?>"
				name="<?php echo esc_attr( $key ); ?>"
				value="<?php echo $value; ?>"
				class="rc-text-input"
			/>
			<?php if ( $description ) : ?>
				<p class="rc-field-desc"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Output a textarea row.
	 *
	 * @param int    $post_id     Post ID.
	 * @param string $key         Meta key / textarea name.
	 * @param string $label       Field label.
	 * @param string $description Optional helper text.
	 */
	private function textarea_field(
		int $post_id,
		string $key,
		string $label,
		string $description = ''
	): void {
		$value = esc_textarea( $this->get_meta( $post_id, $key ) );
		?>
		<div class="rc-field-row">
			<label class="rc-field-label" for="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></label>
			<textarea
				id="<?php echo esc_attr( $key ); ?>"
				name="<?php echo esc_attr( $key ); ?>"
				class="rc-textarea"
				rows="4"
			><?php echo $value; ?></textarea>
			<?php if ( $description ) : ?>
				<p class="rc-field-desc"><?php echo esc_html( $description ); ?></p>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Output a single-image media uploader row.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key storing the attachment ID.
	 * @param string $label   Field label.
	 */
	private function media_field( int $post_id, string $key, string $label ): void {
		$attachment_id = (int) $this->get_meta( $post_id, $key );
		$thumb_url     = $attachment_id
			? wp_get_attachment_image_url( $attachment_id, 'thumbnail' )
			: '';
		?>
		<div class="rc-field-row rc-media-field" data-key="<?php echo esc_attr( $key ); ?>">
			<label class="rc-field-label"><?php echo esc_html( $label ); ?></label>
			<div class="rc-media-preview">
				<?php if ( $thumb_url ) : ?>
					<img src="<?php echo esc_url( $thumb_url ); ?>" alt="" />
				<?php endif; ?>
			</div>
			<input type="hidden" name="<?php echo esc_attr( $key ); ?>" value="<?php echo esc_attr( $attachment_id ?: '' ); ?>" class="rc-media-id" />
			<button type="button" class="button rc-media-upload"><?php esc_html_e( 'Select Image', 'racing-centers' ); ?></button>
			<button type="button" class="button rc-media-remove<?php echo $thumb_url ? '' : ' rc-hidden'; ?>"><?php esc_html_e( 'Remove', 'racing-centers' ); ?></button>
		</div>
		<?php
	}

	// -------------------------------------------------------------------------
	// Meta box renderers
	// -------------------------------------------------------------------------

	/**
	 * Hero Section meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_hero( WP_Post $post ): void {
		wp_nonce_field( 'rc_hero_nonce_action', 'rc_hero_nonce' );
		echo '<div class="rc-meta-box">';
		$this->media_field( $post->ID, 'rc_hero_image', __( 'Hero Image', 'racing-centers' ) );
		$this->text_field( $post->ID, 'rc_cidade', __( 'Cidade', 'racing-centers' ), __( 'City name displayed in the hero area.', 'racing-centers' ) );
		$this->text_field( $post->ID, 'rc_subtitulo', __( 'Subtítulo', 'racing-centers' ), __( 'Short subtitle shown below the city name.', 'racing-centers' ) );
		echo '</div>';
	}

	/**
	 * Informações meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_informacoes( WP_Post $post ): void {
		wp_nonce_field( 'rc_informacoes_nonce_action', 'rc_informacoes_nonce' );
		echo '<div class="rc-meta-box">';
		$this->text_field( $post->ID, 'rc_endereco', __( 'Endereço', 'racing-centers' ) );
		$this->text_field( $post->ID, 'rc_horario', __( 'Horário de Funcionamento', 'racing-centers' ), __( 'E.g. Seg–Sex: 09h–18h', 'racing-centers' ) );
		$this->text_field( $post->ID, 'rc_contato', __( 'Contato / Telefone', 'racing-centers' ) );
		$this->text_field( $post->ID, 'rc_email', __( 'E-mail', 'racing-centers' ), '', 'email' );
		echo '</div>';
	}

	/**
	 * Conteúdo meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_conteudo( WP_Post $post ): void {
		wp_nonce_field( 'rc_conteudo_nonce_action', 'rc_conteudo_nonce' );

		// Gallery: stored as comma-separated attachment IDs.
		$gallery_ids = $this->get_meta( $post->ID, 'rc_galeria' );
		$ids_array   = array_filter( array_map( 'intval', explode( ',', $gallery_ids ) ) );

		echo '<div class="rc-meta-box">';
		$this->text_field( $post->ID, 'rc_video_url', __( 'URL do Vídeo', 'racing-centers' ), __( 'YouTube or Vimeo embed URL.', 'racing-centers' ), 'url' );

		// Gallery uploader.
		?>
		<div class="rc-field-row">
			<label class="rc-field-label"><?php esc_html_e( 'Galeria de Imagens', 'racing-centers' ); ?></label>
			<div class="rc-gallery-preview" id="rc-gallery-preview">
				<?php foreach ( $ids_array as $id ) : ?>
					<?php $url = wp_get_attachment_image_url( $id, 'thumbnail' ); ?>
					<?php if ( $url ) : ?>
						<div class="rc-gallery-item" data-id="<?php echo esc_attr( $id ); ?>">
							<img src="<?php echo esc_url( $url ); ?>" alt="" />
							<button type="button" class="rc-gallery-remove" title="<?php esc_attr_e( 'Remove', 'racing-centers' ); ?>">✕</button>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<input type="hidden" id="rc_galeria" name="rc_galeria" value="<?php echo esc_attr( $gallery_ids ); ?>" />
			<button type="button" class="button" id="rc-gallery-add"><?php esc_html_e( 'Add Images', 'racing-centers' ); ?></button>
			<p class="rc-field-desc"><?php esc_html_e( 'Select multiple images from the media library.', 'racing-centers' ); ?></p>
		</div>
		<?php

		$this->text_field( $post->ID, 'rc_titulo_sobre', __( 'Título da Seção Sobre', 'racing-centers' ) );
		$this->textarea_field( $post->ID, 'rc_texto_apresentacao', __( 'Texto de Apresentação', 'racing-centers' ), __( 'Main description paragraph.', 'racing-centers' ) );
		echo '</div>';
	}

	/**
	 * Produtos / Simulador meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_produtos_simulador( WP_Post $post ): void {
		wp_nonce_field( 'rc_produtos_simulador_nonce_action', 'rc_produtos_simulador_nonce' );

		$tipo = $this->get_meta( $post->ID, 'rc_tipo_conteudo' ) ?: 'produtos';
		echo '<div class="rc-meta-box">';
		?>

		<!-- Type selector -->
		<div class="rc-field-row">
			<label class="rc-field-label"><?php esc_html_e( 'Tipo de Conteúdo', 'racing-centers' ); ?></label>
			<div class="rc-radio-group">
				<label>
					<input type="radio" name="rc_tipo_conteudo" value="produtos" <?php checked( $tipo, 'produtos' ); ?> id="rc-tipo-produtos" />
					<?php esc_html_e( 'Produtos', 'racing-centers' ); ?>
				</label>
				<label>
					<input type="radio" name="rc_tipo_conteudo" value="simulador" <?php checked( $tipo, 'simulador' ); ?> id="rc-tipo-simulador" />
					<?php esc_html_e( 'Simulador', 'racing-centers' ); ?>
				</label>
			</div>
		</div>

		<!-- ===== PRODUTOS ===== -->
		<div id="rc-section-produtos" class="rc-conditional-section<?php echo 'produtos' === $tipo ? '' : ' rc-hidden'; ?>">
			<h4 class="rc-section-heading"><?php esc_html_e( 'Produtos', 'racing-centers' ); ?></h4>
			<?php $this->text_field( $post->ID, 'rc_produtos_ids', __( 'IDs dos Produtos', 'racing-centers' ), __( 'Comma-separated product IDs.', 'racing-centers' ) ); ?>
		</div>

		<!-- ===== SIMULADOR ===== -->
		<div id="rc-section-simulador" class="rc-conditional-section<?php echo 'simulador' === $tipo ? '' : ' rc-hidden'; ?>">
			<h4 class="rc-section-heading"><?php esc_html_e( 'Simulador', 'racing-centers' ); ?></h4>
			<?php
			$this->media_field( $post->ID, 'rc_simulador_imagem', __( 'Imagem do Simulador', 'racing-centers' ) );
			$this->text_field( $post->ID, 'rc_simulador_titulo_linha', __( 'Título (linha superior)', 'racing-centers' ) );
			$this->text_field( $post->ID, 'rc_simulador_nome', __( 'Nome do Simulador', 'racing-centers' ) );
			$this->text_field( $post->ID, 'rc_simulador_modelo', __( 'Modelo', 'racing-centers' ) );
			$this->textarea_field( $post->ID, 'rc_simulador_descricao', __( 'Descrição', 'racing-centers' ) );
			?>
			<h4 class="rc-section-heading rc-section-heading--sub"><?php esc_html_e( 'Detalhes de Hardware', 'racing-centers' ); ?></h4>
			<?php
			$this->text_field( $post->ID, 'rc_dd_modelo', __( 'Direct Drive – Modelo', 'racing-centers' ) );
			$this->text_field( $post->ID, 'rc_volante_modelo', __( 'Volante – Modelo', 'racing-centers' ) );
			$this->text_field( $post->ID, 'rc_pedal_modelo', __( 'Pedais – Modelo', 'racing-centers' ) );
			$this->text_field( $post->ID, 'rc_monitor', __( 'Monitor', 'racing-centers' ) );
			$this->textarea_field( $post->ID, 'rc_perifericos_lista', __( 'Periféricos (um por linha)', 'racing-centers' ), __( 'Each line becomes a list item.', 'racing-centers' ) );
			$this->text_field( $post->ID, 'rc_simulador_link', __( 'Link do Simulador', 'racing-centers' ), '', 'url' );
			$this->text_field( $post->ID, 'rc_simulador_produtos_ids', __( 'IDs de Produtos do Simulador', 'racing-centers' ), __( 'Comma-separated product IDs linked to this simulator.', 'racing-centers' ) );
			?>
		</div>

		<?php
		echo '</div>';
	}

	/**
	 * Depoimentos meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_depoimentos( WP_Post $post ): void {
		wp_nonce_field( 'rc_depoimentos_nonce_action', 'rc_depoimentos_nonce' );
		echo '<div class="rc-meta-box">';
		$this->text_field(
			$post->ID,
			'rc_google_place_id',
			__( 'Google Place ID', 'racing-centers' ),
			__( 'Used to fetch reviews via the Google Places API (future integration).', 'racing-centers' )
		);
		echo '</div>';
	}

	/**
	 * Localização meta box.
	 *
	 * @param WP_Post $post Current post object.
	 */
	public function render_localizacao( WP_Post $post ): void {
		wp_nonce_field( 'rc_localizacao_nonce_action', 'rc_localizacao_nonce' );
		echo '<div class="rc-meta-box">';
		$this->textarea_field(
			$post->ID,
			'rc_mapa_embed',
			__( 'Embed do Mapa (iframe)', 'racing-centers' ),
			__( 'Paste the full <iframe> embed code from Google Maps.', 'racing-centers' )
		);
		echo '</div>';
	}
}
