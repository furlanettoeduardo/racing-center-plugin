<?php
/**
 * Data saving — Racing Center CPT
 *
 * Handles the save_post hook for `racing_center` posts.
 *
 * Security model:
 *   - One nonce per meta box group (matches the nonces printed in class-meta-boxes.php).
 *   - Capability check: current user must be able to edit the specific post.
 *   - Autosave guard: skips all processing during autosaves.
 *   - Every value is sanitized before being persisted.
 *
 * @package Racing_Centers
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RC_Save
 *
 * Listens to save_post and persists meta values for the racing_center CPT.
 */
class RC_Save {

	// -------------------------------------------------------------------------
	// Nonce definitions
	// -------------------------------------------------------------------------

	/**
	 * Map of nonce field name => nonce action.
	 * Must match the pairs passed to wp_nonce_field() in class-meta-boxes.php.
	 *
	 * @var array<string, string>
	 */
	private const NONCES = array(
		'rc_hero_nonce'               => 'rc_hero_nonce_action',
		'rc_informacoes_nonce'        => 'rc_informacoes_nonce_action',
		'rc_conteudo_nonce'           => 'rc_conteudo_nonce_action',
		'rc_produtos_simulador_nonce' => 'rc_produtos_simulador_nonce_action',
		'rc_depoimentos_nonce'        => 'rc_depoimentos_nonce_action',
		'rc_localizacao_nonce'        => 'rc_localizacao_nonce_action',
	);

	// -------------------------------------------------------------------------
	// Constructor
	// -------------------------------------------------------------------------

	/**
	 * Register the save_post hook.
	 */
	public function __construct() {
		add_action( 'save_post_racing_center', array( $this, 'save' ), 10, 2 );
	}

	// -------------------------------------------------------------------------
	// Main save handler
	// -------------------------------------------------------------------------

	/**
	 * Validate the request and persist meta values.
	 *
	 * Hooked to: save_post_racing_center
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function save( int $post_id, WP_Post $post ): void {
		// 1. Bail on autosave.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// 2. Bail on revisions.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// 3. Capability check — user must be allowed to edit this specific post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// 4. Verify at least one of our nonces is present and valid.
		//    We verify each nonce individually so partial saves (e.g., Quick Edit)
		//    still work if only one meta box submits data.
		$nonce_verified = false;
		foreach ( self::NONCES as $field => $action ) {
			$raw_nonce = isset( $_POST[ $field ] ) ? sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) : '';
			if ( $raw_nonce && wp_verify_nonce( $raw_nonce, $action ) ) {
				$nonce_verified = true;
				break;
			}
		}

		if ( ! $nonce_verified ) {
			return;
		}

		// 5. Save each field group.
		$this->save_hero( $post_id );
		$this->save_informacoes( $post_id );
		$this->save_conteudo( $post_id );
		$this->save_produtos_simulador( $post_id );
		$this->save_depoimentos( $post_id );
		$this->save_localizacao( $post_id );
	}

	// -------------------------------------------------------------------------
	// Field group savers
	// -------------------------------------------------------------------------

	/**
	 * Persist Hero fields.
	 *
	 * @param int $post_id Post ID.
	 */
	private function save_hero( int $post_id ): void {
		$this->save_int( $post_id, 'rc_hero_image' );
		$this->save_text( $post_id, 'rc_cidade' );
		$this->save_text( $post_id, 'rc_subtitulo' );
	}

	/**
	 * Persist Informações fields.
	 *
	 * @param int $post_id Post ID.
	 */
	private function save_informacoes( int $post_id ): void {
		$this->save_text( $post_id, 'rc_endereco' );
		$this->save_text( $post_id, 'rc_horario' );
		$this->save_text( $post_id, 'rc_contato' );
		$this->save_email( $post_id, 'rc_email' );
	}

	/**
	 * Persist Conteúdo fields.
	 *
	 * @param int $post_id Post ID.
	 */
	private function save_conteudo( int $post_id ): void {
		$this->save_url( $post_id, 'rc_video_url' );
		$this->save_gallery( $post_id, 'rc_galeria' );
		$this->save_text( $post_id, 'rc_titulo_sobre' );
		$this->save_textarea( $post_id, 'rc_texto_apresentacao' );
	}

	/**
	 * Persist Produtos / Simulador fields.
	 *
	 * @param int $post_id Post ID.
	 */
	private function save_produtos_simulador( int $post_id ): void {
		// Type selector — only allow known values.
		$allowed = array( 'produtos', 'simulador' );
		$tipo    = isset( $_POST['rc_tipo_conteudo'] )
			? sanitize_text_field( wp_unslash( $_POST['rc_tipo_conteudo'] ) )
			: 'produtos';

		if ( ! in_array( $tipo, $allowed, true ) ) {
			$tipo = 'produtos';
		}
		update_post_meta( $post_id, 'rc_tipo_conteudo', $tipo );

		// Produtos fields.
		$this->save_text( $post_id, 'rc_produtos_ids' );

		// Multi-simulator repeater — store as JSON.
		//
		// The JS serializes all sim data as base64-encoded JSON into a single
		// hidden field (rc_simuladores_data) to avoid WAF false-positives on
		// characters like | and " that appear in equipment names.
		$rc_sim_source = null;

		// DEBUG — log raw POST keys received. Remove after diagnosing.
		$debug_has_data    = isset( $_POST['rc_simuladores_data'] ) ? 'YES (len=' . strlen( $_POST['rc_simuladores_data'] ) . ')' : 'NO'; // phpcs:ignore
		$debug_has_rc_sim  = isset( $_POST['rc_sim'] ) ? 'YES' : 'NO'; // phpcs:ignore
		error_log( '[RC_Save] post_id=' . $post_id . ' | rc_simuladores_data=' . $debug_has_data . ' | rc_sim[]=' . $debug_has_rc_sim ); // phpcs:ignore

		if ( ! empty( $_POST['rc_simuladores_data'] ) ) {
			// Strip any non-base64 characters before decoding.
			$b64 = preg_replace( '/[^A-Za-z0-9+\/=]/', '', wp_unslash( $_POST['rc_simuladores_data'] ) );
			error_log( '[RC_Save] b64 (first 100 chars): ' . substr( $b64, 0, 100 ) ); // phpcs:ignore
			if ( $b64 ) {
				$json_str = base64_decode( $b64 );
				if ( false !== $json_str ) {
					$decoded = json_decode( $json_str, true );
					if ( is_array( $decoded ) ) {
						$rc_sim_source = $decoded;
						error_log( '[RC_Save] decoded ' . count( $decoded ) . ' simulators from base64' ); // phpcs:ignore
					} else {
						error_log( '[RC_Save] json_decode failed. json_str (first 200): ' . substr( $json_str, 0, 200 ) ); // phpcs:ignore
					}
				} else {
					error_log( '[RC_Save] base64_decode returned false' ); // phpcs:ignore
				}
			}
		}

		// Fallback: direct rc_sim[] array (for environments without the JS layer).
		if ( null === $rc_sim_source && isset( $_POST['rc_sim'] ) && is_array( $_POST['rc_sim'] ) ) {
			$rc_sim_source = array_values( $_POST['rc_sim'] );
			error_log( '[RC_Save] using fallback rc_sim[] — count=' . count( $rc_sim_source ) ); // phpcs:ignore
		}

		if ( null === $rc_sim_source ) {
			error_log( '[RC_Save] rc_sim_source is NULL — nothing will be saved' ); // phpcs:ignore
		}

		if ( null !== $rc_sim_source ) {
			$clean = array();
			foreach ( $rc_sim_source as $sim ) {
				if ( ! is_array( $sim ) ) {
					continue;
				}
				$clean[] = array(
					'imagem'            => absint( $sim['imagem'] ?? 0 ),
					'linha'             => sanitize_text_field( wp_unslash( $sim['linha'] ?? '' ) ),
					'titulo'            => sanitize_text_field( wp_unslash( $sim['titulo'] ?? '' ) ),
					'subtitulo'         => sanitize_text_field( wp_unslash( $sim['subtitulo'] ?? '' ) ),
					'descricao'         => sanitize_textarea_field( wp_unslash( $sim['descricao'] ?? '' ) ),
					'dd_modelo'         => sanitize_text_field( wp_unslash( $sim['dd_modelo'] ?? '' ) ),
					'volante_modelo'    => sanitize_text_field( wp_unslash( $sim['volante_modelo'] ?? '' ) ),
					'pedal_modelo'      => sanitize_text_field( wp_unslash( $sim['pedal_modelo'] ?? '' ) ),
					'monitor'           => sanitize_text_field( wp_unslash( $sim['monitor'] ?? '' ) ),
					'perifericos_lista' => sanitize_textarea_field( wp_unslash( $sim['perifericos_lista'] ?? '' ) ),
					'link'              => esc_url_raw( wp_unslash( $sim['link'] ?? '' ) ),
				);
			}
			$json_to_save = wp_json_encode( $clean );
			error_log( '[RC_Save] saving ' . count( $clean ) . ' simulators. JSON (first 300): ' . substr( $json_to_save, 0, 300 ) ); // phpcs:ignore
			$result = update_post_meta( $post_id, 'rc_simuladores', $json_to_save );
			error_log( '[RC_Save] update_post_meta result: ' . ( false === $result ? 'FALSE (insert ok or no change)' : 'updated (new meta_id=' . $result . ')' ) ); // phpcs:ignore
		}
	}

	/**
	 * Persist Depoimentos fields.
	 *
	 * @param int $post_id Post ID.
	 */
	private function save_depoimentos( int $post_id ): void {
		$this->save_text( $post_id, 'rc_google_place_id' );
	}

	/**
	 * Persist Localização fields.
	 *
	 * @param int $post_id Post ID.
	 */
	private function save_localizacao( int $post_id ): void {
		// Mapa embed is an iframe; we allow safe HTML via wp_kses.
		if ( isset( $_POST['rc_mapa_embed'] ) ) {
			$allowed_html = array(
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
			$value = wp_kses(
				wp_unslash( $_POST['rc_mapa_embed'] ),
				$allowed_html
			);
			update_post_meta( $post_id, 'rc_mapa_embed', $value );
		}
	}

	// -------------------------------------------------------------------------
	// Sanitize-and-save helpers
	// -------------------------------------------------------------------------

	/**
	 * Save a plain text field.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key and POST field name.
	 */
	private function save_text( int $post_id, string $key ): void {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta(
				$post_id,
				$key,
				sanitize_text_field( wp_unslash( $_POST[ $key ] ) )
			);
		}
	}

	/**
	 * Save a textarea field (preserves newlines, strips tags).
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key and POST field name.
	 */
	private function save_textarea( int $post_id, string $key ): void {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta(
				$post_id,
				$key,
				sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) )
			);
		}
	}

	/**
	 * Save a URL field.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key and POST field name.
	 */
	private function save_url( int $post_id, string $key ): void {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta(
				$post_id,
				$key,
				esc_url_raw( wp_unslash( $_POST[ $key ] ) )
			);
		}
	}

	/**
	 * Save an email field.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key and POST field name.
	 */
	private function save_email( int $post_id, string $key ): void {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta(
				$post_id,
				$key,
				sanitize_email( wp_unslash( $_POST[ $key ] ) )
			);
		}
	}

	/**
	 * Save an integer field (e.g., attachment IDs).
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key and POST field name.
	 */
	private function save_int( int $post_id, string $key ): void {
		if ( isset( $_POST[ $key ] ) ) {
			update_post_meta(
				$post_id,
				$key,
				absint( $_POST[ $key ] )
			);
		}
	}

	/**
	 * Save a gallery field (comma-separated attachment IDs).
	 *
	 * Each ID is cast to an integer and zeros are removed before storage.
	 *
	 * @param int    $post_id Post ID.
	 * @param string $key     Meta key and POST field name.
	 */
	private function save_gallery( int $post_id, string $key ): void {
		if ( isset( $_POST[ $key ] ) ) {
			$raw  = sanitize_text_field( wp_unslash( $_POST[ $key ] ) );
			$ids  = array_filter( array_map( 'absint', explode( ',', $raw ) ) );
			update_post_meta( $post_id, $key, implode( ',', $ids ) );
		}
	}
}
