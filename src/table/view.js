/**
 * Lokasi: lunar-core/src/table/view.js
 *
 * Menambahkan interaksi sort (klik header kolom) & filter (kotak
 * pencarian) ke Table di frontend. Tanpa file ini, tabel tetap
 * tampil penuh & terbaca — cuma tanpa kemampuan sort/filter.
 *
 * Hanya dimuat di frontend (viewScript di block.json).
 */

( function () {
	function getCellText( row, key ) {
		var cell = row.querySelector( '[data-key="' + key + '"]' );
		return cell ? cell.textContent.trim() : '';
	}

	function sortRows( tableEl, key, type, direction ) {
		var tbody = tableEl.querySelector( 'tbody' );
		var rows = Array.prototype.slice.call( tbody.querySelectorAll( 'tr' ) );

		rows.sort( function ( a, b ) {
			var aText = getCellText( a, key );
			var bText = getCellText( b, key );
			var result;

			if ( 'number' === type ) {
				result = parseFloat( aText || '0' ) - parseFloat( bText || '0' );
			} else {
				result = aText.localeCompare( bText, undefined, { numeric: true, sensitivity: 'base' } );
			}

			return 'desc' === direction ? -result : result;
		} );

		rows.forEach( function ( row ) {
			tbody.appendChild( row );
		} );
	}

	function initSort( tableEl ) {
		var headers = Array.prototype.slice.call( tableEl.querySelectorAll( 'thead th' ) );

		headers.forEach( function ( th ) {
			var direction = null;

			th.classList.add( 'lunar-table__sortable' );
			th.setAttribute( 'role', 'button' );
			th.setAttribute( 'tabindex', '0' );

			function activateSort() {
				headers.forEach( function ( other ) {
					if ( other !== th ) {
						other.removeAttribute( 'data-sort-direction' );
					}
				} );

				direction = 'asc' === direction ? 'desc' : 'asc';
				th.setAttribute( 'data-sort-direction', direction );

				sortRows( tableEl, th.getAttribute( 'data-key' ), th.getAttribute( 'data-type' ), direction );
			}

			th.addEventListener( 'click', activateSort );

			th.addEventListener( 'keydown', function ( event ) {
				if ( 'Enter' === event.key || ' ' === event.key ) {
					event.preventDefault();
					activateSort();
				}
			} );
		} );
	}

	function initFilter( wrapperEl, tableEl ) {
		var searchWrap = document.createElement( 'div' );
		searchWrap.className = 'lunar-table__filter';

		var input = document.createElement( 'input' );
		input.type = 'search';
		input.className = 'lunar-table__filter-input';
		input.setAttribute( 'placeholder', 'Cari di tabel ini…' );
		input.setAttribute( 'aria-label', 'Cari di dalam tabel' );

		searchWrap.appendChild( input );
		wrapperEl.insertBefore( searchWrap, tableEl );

		input.addEventListener( 'input', function () {
			var query = input.value.trim().toLowerCase();
			var rows = tableEl.querySelectorAll( 'tbody tr' );

			rows.forEach( function ( row ) {
				var text = row.textContent.toLowerCase();

				if ( '' === query || -1 !== text.indexOf( query ) ) {
					row.removeAttribute( 'hidden' );
				} else {
					row.setAttribute( 'hidden', '' );
				}
			} );
		} );
	}

	document.querySelectorAll( '.lunar-table' ).forEach( function ( wrapperEl ) {
		var tableEl = wrapperEl.querySelector( '.lunar-table__table' );

		if ( ! tableEl ) {
			return;
		}

		if ( 'true' === wrapperEl.getAttribute( 'data-sort' ) ) {
			initSort( tableEl );
		}

		if ( 'true' === wrapperEl.getAttribute( 'data-filter' ) ) {
			initFilter( wrapperEl, tableEl );
		}
	} );
} )();
