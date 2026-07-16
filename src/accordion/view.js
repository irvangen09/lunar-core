/**
 * Lokasi: lunar-core/src/accordion/view.js
 *
 * Mengatur atribut "open" pada tiap Accordion Item sesuai lebar layar.
 * INI SATU-SATUNYA CARA YANG BENAR — trik CSS murni (display/
 * content-visibility override) TIDAK bisa diandalkan di browser modern,
 * karena browser sengaja mengunci status buka/tutup <details> demi
 * konsistensi aksesibilitas (mencegah visual "terbuka" tapi status ARIA
 * tetap "tertutup" bagi pengguna screen reader).
 *
 * Hanya dimuat di frontend (viewScript di block.json), tidak di editor.
 */

( function () {
	var DESKTOP_QUERY = '(min-width: 768px)';

	function syncAccordionState( isDesktop ) {
		var items = document.querySelectorAll( '.lunar-accordion-item' );

		items.forEach( function ( item ) {
			if ( isDesktop ) {
				item.setAttribute( 'open', '' );
			} else {
				item.removeAttribute( 'open' );
			}
		} );
	}

	var mql = window.matchMedia( DESKTOP_QUERY );

	// Set status awal begitu halaman dimuat.
	syncAccordionState( mql.matches );

	// Sinkron ulang HANYA saat lebar layar benar-benar melewati breakpoint
	// (bukan tiap kali resize) — supaya tidak mengganggu status buka/tutup
	// manual pengguna selama masih di rentang mobile yang sama.
	mql.addEventListener( 'change', function ( event ) {
		syncAccordionState( event.matches );
	} );
} )();