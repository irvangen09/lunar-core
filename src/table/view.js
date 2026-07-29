/**
 * Lokasi: lunar-core/src/table/view.js
 *
 * Menambahkan interaksi sort (klik header kolom) & filter (kotak
 * pencarian) ke Table di frontend. Tanpa file ini, tabel tetap
 * tampil penuh & terbaca — cuma tanpa kemampuan sort/filter.
 *
 * Baris Divider/Section (class "lunar-table__row--divider") tidak
 * ikut diurutkan atau dinilai isinya — baris ini jadi batas antar
 * kelompok. Sort bekerja PER KELOMPOK (baris di antara dua Divider,
 * atau sebelum Divider pertama/setelah Divider terakhir), bukan
 * lintas seluruh tabel — supaya pengelompokan yang sengaja dibuat
 * lewat Divider tidak ikut teracak saat sort diaktifkan.
 *
 * Hanya dimuat di frontend (viewScript di block.json).
 */

( function () {
	function getCellText( row, key ) {
		var cell = row.querySelector( '[data-key="' + key + '"]' );
		return cell ? cell.textContent.trim() : '';
	}

	function isDividerRow( row ) {
		return row.classList.contains( 'lunar-table__row--divider' );
	}

	// Pecah baris tbody jadi beberapa kelompok berdasarkan posisi baris
	// Divider. Setiap kelompok berisi baris data yang berurutan; baris
	// Divider disimpan terpisah sebagai penanda batas, bukan bagian
	// dari kelompok manapun.
	function groupRowsByDivider( rows ) {
		var groups = [];
		var currentGroup = [];

		rows.forEach( function ( row ) {
			if ( isDividerRow( row ) ) {
				groups.push( { divider: null, rows: currentGroup } );
				groups.push( { divider: row, rows: [] } );
				currentGroup = [];
			} else {
				currentGroup.push( row );
			}
		} );

		groups.push( { divider: null, rows: currentGroup } );

		return groups;
	}

	function compareRows( a, b, key, type, direction ) {
		var aText = getCellText( a, key );
		var bText = getCellText( b, key );
		var result;

		if ( 'number' === type ) {
			result = parseFloat( aText || '0' ) - parseFloat( bText || '0' );
		} else {
			result = aText.localeCompare( bText, undefined, { numeric: true, sensitivity: 'base' } );
		}

		return 'desc' === direction ? -result : result;
	}

	function sortRows( tableEl, key, type, direction ) {
		var tbody = tableEl.querySelector( 'tbody' );
		var allRows = Array.prototype.slice.call( tbody.querySelectorAll( 'tr' ) );
		var groups = groupRowsByDivider( allRows );

		groups.forEach( function ( group ) {
			group.rows.sort( function ( a, b ) {
				return compareRows( a, b, key, type, direction );
			} );
		} );

		// Tulis ulang urutan DOM: kelompok data lalu Divider penutupnya,
		// berurutan sesuai posisi asal — Divider tidak pernah pindah.
		groups.forEach( function ( group ) {
			group.rows.forEach( function ( row ) {
				tbody.appendChild( row );
			} );

			if ( group.divider ) {
				tbody.appendChild( group.divider );
			}
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

	function rowMatchesQuery( row, query ) {
		var text = row.textContent.toLowerCase();
		return '' === query || -1 !== text.indexOf( query );
	}

	function applyFilter( tableEl, query ) {
		var tbody = tableEl.querySelector( 'tbody' );
		var allRows = Array.prototype.slice.call( tbody.querySelectorAll( 'tr' ) );
		var groups = groupRowsByDivider( allRows );

		groups.forEach( function ( group ) {
			var groupHasMatch = false;

			group.rows.forEach( function ( row ) {
				var matches = rowMatchesQuery( row, query );
				row.toggleAttribute( 'hidden', ! matches );

				if ( matches ) {
					groupHasMatch = true;
				}
			} );

			if ( group.divider ) {
				// Divider ikut disembunyikan kalau sedang memfilter dan
				// tidak ada satu pun baris di kelompoknya yang cocok —
				// supaya tidak menyisakan judul section kosong tanpa isi.
				group.divider.toggleAttribute( 'hidden', '' !== query && ! groupHasMatch );
			}
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
			applyFilter( tableEl, input.value.trim().toLowerCase() );
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