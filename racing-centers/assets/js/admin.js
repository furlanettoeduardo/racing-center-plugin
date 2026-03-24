/* global jQuery, wp */
/**
 * Racing Centers — Admin JavaScript
 *
 * Responsibilities:
 *   1. Conditional show/hide of "Produtos" vs "Simulador" field sections.
 *   2. Single-image media uploader (hero image, simulador image).
 *   3. Multi-image gallery uploader (Conteúdo gallery).
 */
( function ( $ ) {
	'use strict';

	// =========================================================================
	// 1. Conditional Produtos / Simulador toggle
	// =========================================================================

	/**
	 * Show the section matching the currently selected radio value,
	 * hide the other one.
	 */
	function rcToggleTipoConteudo() {
		var selected = $( 'input[name="rc_tipo_conteudo"]:checked' ).val();

		if ( selected === 'simulador' ) {
			$( '#rc-section-simulador' ).removeClass( 'rc-hidden' );
			$( '#rc-section-produtos' ).addClass( 'rc-hidden' );
		} else {
			$( '#rc-section-produtos' ).removeClass( 'rc-hidden' );
			$( '#rc-section-simulador' ).addClass( 'rc-hidden' );
		}
	}

	// Run on page load to reflect saved value.
	rcToggleTipoConteudo();

	// Re-run whenever the radio selection changes.
	$( document ).on( 'change', 'input[name="rc_tipo_conteudo"]', rcToggleTipoConteudo );

	// =========================================================================
	// 2. Single-image media uploader
	// =========================================================================

	/**
	 * Open the WP media library frame for a single-image field.
	 *
	 * @param {jQuery} $wrapper  The `.rc-media-field` container element.
	 */
	function rcOpenMediaUploader( $wrapper ) {
		var frame = wp.media( {
			title:    'Select Image',
			button:   { text: 'Use this image' },
			multiple: false,
			library:  { type: 'image' },
		} );

		frame.on( 'select', function () {
			var attachment = frame.state().get( 'selection' ).first().toJSON();

			// Store the attachment ID in the hidden input.
			$wrapper.find( '.rc-media-id' ).val( attachment.id );

			// Show the thumbnail preview.
			var thumbUrl = attachment.sizes && attachment.sizes.thumbnail
				? attachment.sizes.thumbnail.url
				: attachment.url;

			var $preview = $wrapper.find( '.rc-media-preview' );
			$preview.html( '<img src="' + thumbUrl + '" alt="" />' );

			// Show the Remove button.
			$wrapper.find( '.rc-media-remove' ).removeClass( 'rc-hidden' );
		} );

		frame.open();
	}

	// Delegate click on "Select Image" buttons.
	$( document ).on( 'click', '.rc-media-upload', function ( e ) {
		e.preventDefault();
		rcOpenMediaUploader( $( this ).closest( '.rc-media-field' ) );
	} );

	// Delegate click on "Remove" buttons.
	$( document ).on( 'click', '.rc-media-remove', function ( e ) {
		e.preventDefault();
		var $wrapper = $( this ).closest( '.rc-media-field' );
		$wrapper.find( '.rc-media-id' ).val( '' );
		$wrapper.find( '.rc-media-preview' ).html( '' );
		$( this ).addClass( 'rc-hidden' );
	} );

	// =========================================================================
	// 3. Multi-image gallery uploader
	// =========================================================================

	var galleryFrame = null;

	/**
	 * Build a thumbnail element for a gallery item.
	 *
	 * @param  {number} id  Attachment ID.
	 * @param  {string} url Thumbnail URL.
	 * @return {jQuery}
	 */
	function rcBuildGalleryItem( id, url ) {
		return $( '<div class="rc-gallery-item" data-id="' + id + '">' +
			'<img src="' + url + '" alt="" />' +
			'<button type="button" class="rc-gallery-remove" title="Remove">✕</button>' +
			'</div>' );
	}

	/**
	 * Rebuild the hidden input value from the current gallery item IDs.
	 */
	function rcSyncGalleryInput() {
		var ids = [];
		$( '#rc-gallery-preview .rc-gallery-item' ).each( function () {
			ids.push( $( this ).data( 'id' ) );
		} );
		$( '#rc_galeria' ).val( ids.join( ',' ) );
	}

	// Open the gallery frame.
	$( document ).on( 'click', '#rc-gallery-add', function ( e ) {
		e.preventDefault();

		if ( galleryFrame ) {
			galleryFrame.open();
			return;
		}

		galleryFrame = wp.media( {
			title:    'Select Gallery Images',
			button:   { text: 'Add to gallery' },
			multiple: 'add',
			library:  { type: 'image' },
		} );

		galleryFrame.on( 'select', function () {
			var selection = galleryFrame.state().get( 'selection' );
			var $preview  = $( '#rc-gallery-preview' );

			selection.each( function ( attachment ) {
				var data     = attachment.toJSON();
				var thumbUrl = data.sizes && data.sizes.thumbnail
					? data.sizes.thumbnail.url
					: data.url;

				// Avoid duplicates.
				if ( $preview.find( '[data-id="' + data.id + '"]' ).length === 0 ) {
					$preview.append( rcBuildGalleryItem( data.id, thumbUrl ) );
				}
			} );

			rcSyncGalleryInput();
		} );

		galleryFrame.open();
	} );

	// Remove a single gallery item.
	$( document ).on( 'click', '.rc-gallery-remove', function ( e ) {
		e.preventDefault();
		$( this ).closest( '.rc-gallery-item' ).remove();
		rcSyncGalleryInput();
	} );

}( jQuery ) );
