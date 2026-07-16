/**
 * Lokasi: lunar-core/src/tabs/view.js
 *
 * Mengubah markup polos Tabs (semua panel terlihat) jadi widget tab
 * interaktif dengan ARIA lengkap — mengikuti pola WAI-ARIA APG "Tabs".
 *
 * Kalau file ini gagal dimuat, markup asli (semua panel terlihat
 * sekaligus, label sebagai teks biasa) tetap sepenuhnya bisa dibaca —
 * cuma tanpa kemampuan klik pindah tab.
 *
 * Hanya dimuat di frontend (viewScript di block.json).
 */

( function () {
	function initTabs( tabsEl, tabsIndex ) {
		var items = Array.prototype.slice.call(
			tabsEl.querySelectorAll( ':scope > .lunar-tabs-item' )
		);

		if ( items.length === 0 ) {
			return;
		}

		var tablist = document.createElement( 'div' );
		tablist.className = 'lunar-tabs__list';
		tablist.setAttribute( 'role', 'tablist' );

		items.forEach( function ( item, itemIndex ) {
			var label = item.querySelector( ':scope > .lunar-tabs-item__label' );
			var panel = item.querySelector( ':scope > .lunar-tabs-item__content' );

			if ( ! label || ! panel ) {
				return;
			}

			var tabId = 'lunar-tabs-' + tabsIndex + '-tab-' + itemIndex;
			var panelId = 'lunar-tabs-' + tabsIndex + '-panel-' + itemIndex;
			var isActive = 0 === itemIndex;

			label.classList.add( 'lunar-tabs__tab' );
			label.setAttribute( 'role', 'tab' );
			label.setAttribute( 'id', tabId );
			label.setAttribute( 'aria-controls', panelId );
			label.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
			label.setAttribute( 'tabindex', isActive ? '0' : '-1' );

			panel.classList.add( 'lunar-tabs__panel' );
			panel.setAttribute( 'role', 'tabpanel' );
			panel.setAttribute( 'id', panelId );
			panel.setAttribute( 'aria-labelledby', tabId );

			if ( ! isActive ) {
				panel.setAttribute( 'hidden', '' );
			}

			// Pindahkan label dari posisi asalnya (di atas panel masing-masing)
			// ke strip tablist baru — inilah inti transformasi progressive
			// enhancement yang dijelaskan sebelum Tahap 3.1.
			tablist.appendChild( label );
		} );

		tabsEl.insertBefore( tablist, tabsEl.firstChild );
		tabsEl.classList.add( 'lunar-tabs--enhanced' );

		var tabs = Array.prototype.slice.call( tablist.querySelectorAll( '.lunar-tabs__tab' ) );

		function activate( tab ) {
			tabs.forEach( function ( candidate ) {
				var panel = document.getElementById( candidate.getAttribute( 'aria-controls' ) );
				var isSelected = candidate === tab;

				candidate.setAttribute( 'aria-selected', isSelected ? 'true' : 'false' );
				candidate.setAttribute( 'tabindex', isSelected ? '0' : '-1' );

				if ( panel ) {
					if ( isSelected ) {
						panel.removeAttribute( 'hidden' );
					} else {
						panel.setAttribute( 'hidden', '' );
					}
				}
			} );

			tab.focus();
		}

		tabs.forEach( function ( tab, index ) {
			tab.addEventListener( 'click', function () {
				activate( tab );
			} );

			// Navigasi panah kiri/kanan (+ Home/End) antar tab, sesuai
			// pola WAI-ARIA APG — pindah fokus otomatis mengaktifkan tab.
			tab.addEventListener( 'keydown', function ( event ) {
				var newIndex = null;

				if ( 'ArrowRight' === event.key ) {
					newIndex = ( index + 1 ) % tabs.length;
				} else if ( 'ArrowLeft' === event.key ) {
					newIndex = ( index - 1 + tabs.length ) % tabs.length;
				} else if ( 'Home' === event.key ) {
					newIndex = 0;
				} else if ( 'End' === event.key ) {
					newIndex = tabs.length - 1;
				}

				if ( null !== newIndex ) {
					event.preventDefault();
					activate( tabs[ newIndex ] );
				}
			} );
		} );
	}

	document.querySelectorAll( '.lunar-tabs' ).forEach( function ( tabsEl, index ) {
		initTabs( tabsEl, index );
	} );
} )();
