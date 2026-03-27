<?php
/**
 * Elementor Dynamic Tags loader — Racing Centers
 *
 * Responsible for:
 *   1. Registering the "Racing Centers" custom tag group inside Elementor.
 *   2. Loading every individual tag class file.
 *   3. Registering each tag with Elementor's Dynamic Tags manager.
 *
 * This class is instantiated ONLY from the `elementor/dynamic_tags/register`
 * hook callback, so Elementor's full API is guaranteed to be available here.
 *
 * @package Racing_Centers
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RC_Dynamic_Tags
 *
 * Bootstraps all Racing Center dynamic tags into Elementor.
 */
class RC_Dynamic_Tags {

	/**
	 * Custom group slug used across all tag classes.
	 *
	 * @var string
	 */
	const GROUP = 'racing-centers';

	/**
	 * Map of PHP class name ⟶ file name (relative to the /tags/ subdirectory).
	 *
	 * Add new tags here; the rest is automatic.
	 *
	 * @var array<string, string>
	 */
	private const TAGS = array(
		// Hero.
		'RC_Tag_Cidade'                 => 'class-tag-cidade.php',
		'RC_Tag_Subtitulo'              => 'class-tag-subtitulo.php',
		'RC_Tag_Hero_Image'             => 'class-tag-hero-image.php',

		// Informações.
		'RC_Tag_Endereco'               => 'class-tag-endereco.php',
		'RC_Tag_Horario'                => 'class-tag-horario.php',
		'RC_Tag_Contato'                => 'class-tag-contato.php',
		'RC_Tag_Email'                  => 'class-tag-email.php',

		// Conteúdo.
		'RC_Tag_Video_URL'              => 'class-tag-video-url.php',
		'RC_Tag_Titulo_Sobre'           => 'class-tag-titulo-sobre.php',
		'RC_Tag_Texto_Apresentacao'     => 'class-tag-texto-apresentacao.php',

		// Produtos / Simulador — type selector.
		'RC_Tag_Tipo_Conteudo'          => 'class-tag-tipo-conteudo.php',

		// Simulador.
		'RC_Tag_Simulador_Imagem'       => 'class-tag-simulador-imagem.php',
		'RC_Tag_Simulador_Titulo_Linha' => 'class-tag-simulador-titulo-linha.php',
		'RC_Tag_Simulador_Nome'         => 'class-tag-simulador-nome.php',
		'RC_Tag_Simulador_Modelo'       => 'class-tag-simulador-modelo.php',
		'RC_Tag_Simulador_Descricao'    => 'class-tag-simulador-descricao.php',
		'RC_Tag_DD_Modelo'              => 'class-tag-dd-modelo.php',
		'RC_Tag_Volante_Modelo'         => 'class-tag-volante-modelo.php',
		'RC_Tag_Pedal_Modelo'           => 'class-tag-pedal-modelo.php',
		'RC_Tag_Monitor'                => 'class-tag-monitor.php',
		'RC_Tag_Perifericos_Lista'      => 'class-tag-perifericos-lista.php',
		'RC_Tag_Simulador_Link'         => 'class-tag-simulador-link.php',
		'RC_Tag_Simulador_Produtos_IDs' => 'class-tag-simulador-produtos-ids.php',

		// Produtos.
		'RC_Tag_Produtos_IDs'           => 'class-tag-produtos-ids.php',

		// Galeria.
		'RC_Tag_Galeria'                => 'class-tag-galeria.php',

		// Depoimentos.
		'RC_Tag_Google_Place_ID'        => 'class-tag-google-place-id.php',

		// Localização.
		'RC_Tag_Mapa_Embed'             => 'class-tag-mapa-embed.php',
	);

	// -------------------------------------------------------------------------
	// Constructor
	// -------------------------------------------------------------------------

	/**
	 * Register the group and all tags with Elementor's manager.
	 *
	 * @param \Elementor\Core\DynamicTags\Manager $manager Elementor Dynamic Tags manager instance.
	 */
	public function __construct( $manager ) {
		$this->register_group( $manager );
		$this->load_tag_files();
		$this->register_tags( $manager );
	}

	// -------------------------------------------------------------------------
	// Private helpers
	// -------------------------------------------------------------------------

	/**
	 * Register the "Racing Centers" group in Elementor's tag picker UI.
	 *
	 * @param \Elementor\Core\DynamicTags\Manager $manager
	 */
	private function register_group( $manager ): void {
		$manager->register_group(
			self::GROUP,
			array( 'title' => __( 'Racing Centers', 'racing-centers' ) )
		);
	}

	/**
	 * Require the shared base classes and every individual tag class file.
	 */
	private function load_tag_files(): void {
		// Abstract base classes shared by all tags.
		require_once __DIR__ . '/class-tag-base.php';

		$tags_dir = __DIR__ . '/tags/';
		foreach ( self::TAGS as $file ) {
			require_once $tags_dir . $file;
		}
	}

	/**
	 * Instantiate and register each tag with Elementor.
	 *
	 * @param \Elementor\Core\DynamicTags\Manager $manager
	 */
	private function register_tags( $manager ): void {
		foreach ( array_keys( self::TAGS ) as $class_name ) {
			$manager->register( new $class_name() );
		}
	}
}
