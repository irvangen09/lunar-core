/**
 * Lokasi: lunar-core/assets/admin/game-tile-meta.js
 *
 * Interaksi picker Media Library untuk field "Media Game Tile" di layar
 * Edit Game (lihat Game_Tile_Meta::render_edit_fields()). Memakai
 * wp.media bawaan WordPress — tidak menulis modal upload sendiri
 * (ENGINEERING_PRINCIPLES.md §9, WordPress Native).
 */

( function () {
	'use strict';

	document.addEventListener( 'DOMContentLoaded', function () {
		var selectButton = document.getElementById( 'lunar-core-game-tile-select' );
		var removeButton = document.getElementById( 'lunar-core-game-tile-remove' );
		var preview      = document.getElementById( 'lunar-core-game-tile-preview' );
		var hiddenInput  = document.getElementById( 'lunar-core-game-tile-image-id' );

		if ( ! selectButton || ! hiddenInput || ! preview || typeof wp === 'undefined' || ! wp.media ) {
			return;
		}

		var frame = null;

		selectButton.addEventListener( 'click', function ( event ) {
			event.preventDefault();

			if ( frame ) {
				frame.open();
				return;
			}

			frame = wp.media( {
				title: selectButton.getAttribute( 'data-title' ) || 'Pilih Gambar',
				button: { text: 'Gunakan Gambar Ini' },
				library: { type: 'image' },
				multiple: false,
			} );

			frame.on( 'select', function () {
				var attachment = frame.state().get( 'selection' ).first().toJSON();
				var imageUrl   = attachment.sizes && attachment.sizes.medium
					? attachment.sizes.medium.url
					: attachment.url;

				hiddenInput.value = attachment.id;
				preview.src = imageUrl;
				preview.style.display = 'block';

				if ( removeButton ) {
					removeButton.style.display = '';
				}
			} );

			frame.open();
		} );

		if ( removeButton ) {
			removeButton.addEventListener( 'click', function ( event ) {
				event.preventDefault();

				hiddenInput.value = '';
				preview.src = '';
				preview.style.display = 'none';
				removeButton.style.display = 'none';
			} );
		}
	} );
} )();