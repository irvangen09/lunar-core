/**
 * Lokasi: lunar-core/src/gallery/view.js
 * Lunar Gallery — Lightbox
 *
 * Progressive enhancement di atas fallback <a href="{imageUrl}">.
 * Setiap instance block Gallery diperlakukan sebagai grup navigasi
 * terpisah — tombol ←→ tidak berpindah lintas Gallery block yang berbeda.
 */

const LIGHTBOX_SELECTOR = '[data-lunar-lightbox="gallery-item"]';

let overlay = null;
let overlayImage = null;
let overlayCaption = null;
let currentGroup = [];
let currentIndex = -1;
let lastFocusedTrigger = null;

function buildOverlay() {
	if ( overlay ) {
		return;
	}

	overlay = document.createElement( 'div' );
	overlay.className = 'lunar-gallery-lightbox';
	overlay.setAttribute( 'role', 'dialog' );
	overlay.setAttribute( 'aria-modal', 'true' );
	overlay.setAttribute( 'aria-label', 'Pratinjau gambar' );
	overlay.hidden = true;

	overlay.innerHTML = `
		<button type="button" class="lunar-gallery-lightbox__close" aria-label="Tutup">&times;</button>
		<button type="button" class="lunar-gallery-lightbox__prev" aria-label="Sebelumnya">&#8592;</button>
		<figure class="lunar-gallery-lightbox__figure">
			<img class="lunar-gallery-lightbox__image" alt="" />
			<figcaption class="lunar-gallery-lightbox__caption"></figcaption>
		</figure>
		<button type="button" class="lunar-gallery-lightbox__next" aria-label="Berikutnya">&#8594;</button>
	`;

	document.body.appendChild( overlay );

	overlayImage = overlay.querySelector( '.lunar-gallery-lightbox__image' );
	overlayCaption = overlay.querySelector(
		'.lunar-gallery-lightbox__caption'
	);

	overlay
		.querySelector( '.lunar-gallery-lightbox__close' )
		.addEventListener( 'click', closeLightbox );
	overlay
		.querySelector( '.lunar-gallery-lightbox__prev' )
		.addEventListener( 'click', () => showIndex( currentIndex - 1 ) );
	overlay
		.querySelector( '.lunar-gallery-lightbox__next' )
		.addEventListener( 'click', () => showIndex( currentIndex + 1 ) );

	// Klik di luar figure (area backdrop) menutup lightbox.
	overlay.addEventListener( 'click', ( event ) => {
		if ( event.target === overlay ) {
			closeLightbox();
		}
	} );
}

function showIndex( index ) {
	if ( ! currentGroup.length ) {
		return;
	}

	// Wrap-around: geser dari gambar terakhir kembali ke pertama, dan sebaliknya.
	const nextIndex =
		( index + currentGroup.length ) % currentGroup.length;
	const trigger = currentGroup[ nextIndex ];
	const img = trigger.querySelector( 'img' );
	const figure = trigger.closest( '.lunar-gallery-item' );
	const captionEl = figure
		? figure.querySelector( '.lunar-gallery-item__caption' )
		: null;

	currentIndex = nextIndex;
	overlayImage.src = trigger.getAttribute( 'href' );
	overlayImage.alt = img ? img.getAttribute( 'alt' ) || '' : '';

	if ( captionEl && captionEl.textContent.trim() ) {
		overlayCaption.textContent = captionEl.textContent;
		overlayCaption.hidden = false;
	} else {
		overlayCaption.textContent = '';
		overlayCaption.hidden = true;
	}

	const hasMultiple = currentGroup.length > 1;
	overlay.querySelector( '.lunar-gallery-lightbox__prev' ).hidden =
		! hasMultiple;
	overlay.querySelector( '.lunar-gallery-lightbox__next' ).hidden =
		! hasMultiple;
}

function openLightbox( trigger ) {
	buildOverlay();

	const galleryRoot = trigger.closest( '.lunar-gallery' );
	currentGroup = galleryRoot
		? Array.from( galleryRoot.querySelectorAll( LIGHTBOX_SELECTOR ) )
		: [ trigger ];

	lastFocusedTrigger = trigger;

	const index = currentGroup.indexOf( trigger );
	showIndex( index === -1 ? 0 : index );

	overlay.hidden = false;
	document.body.classList.add( 'lunar-gallery-lightbox-open' );
	overlay.querySelector( '.lunar-gallery-lightbox__close' ).focus();

	document.addEventListener( 'keydown', onKeyDown );
}

function closeLightbox() {
	if ( ! overlay || overlay.hidden ) {
		return;
	}

	overlay.hidden = true;
	document.body.classList.remove( 'lunar-gallery-lightbox-open' );
	document.removeEventListener( 'keydown', onKeyDown );

	if ( lastFocusedTrigger ) {
		lastFocusedTrigger.focus();
	}
}

function onKeyDown( event ) {
	switch ( event.key ) {
		case 'Escape':
			closeLightbox();
			break;
		case 'ArrowLeft':
			showIndex( currentIndex - 1 );
			break;
		case 'ArrowRight':
			showIndex( currentIndex + 1 );
			break;
	}
}

document.addEventListener( 'click', ( event ) => {
	const trigger = event.target.closest( LIGHTBOX_SELECTOR );

	if ( ! trigger ) {
		return;
	}

	event.preventDefault();
	openLightbox( trigger );
} );
