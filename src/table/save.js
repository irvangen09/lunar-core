/**
 * Lokasi: lunar-core/src/table/save.js
 * Markup statis Table — <table> polos dengan data-label per sel
 * (untuk tampilan card di mobile) dan data-key/data-type per kolom
 * (dipakai view.js untuk sort/filter).
 *
 * Catatan: <th> memakai scope="col" (accessibility, BLOCK_DEVELOPMENT_
 * GUIDE.md §10). Ini mengubah markup tersimpan — Table block yang
 * sudah dipublikasikan sebelum perubahan ini akan menampilkan Block
 * Validation Error saat dibuka di editor (bukan di frontend), perlu
 * "Attempt Block Recovery" atau resave sekali per artikel yang memakainya.
 *
 * Baris Divider/Section (row.isDivider) dirender sebagai satu <td>
 * yang melebar penuh (colSpan) tanpa data-key — sengaja tidak ikut
 * skema data-label/data-key kolom biasa, karena bukan sel data.
 * view.js men-tandai baris ini lewat class "lunar-table__row--divider"
 * agar sort & filter bisa memperlakukannya sebagai batas kelompok,
 * bukan baris data biasa.
 */

import { useBlockProps } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { columns, rows, enableSort, enableFilter } = attributes;

	if ( ! columns.length ) {
		return null;
	}

	const blockProps = useBlockProps.save( {
		className: 'lunar-table',
		'data-sort': enableSort ? 'true' : 'false',
		'data-filter': enableFilter ? 'true' : 'false',
	} );

	return (
		<div { ...blockProps }>
			<table className="lunar-table__table">
				<thead>
					<tr>
						{ columns.map( ( col ) => (
							<th key={ col.key } scope="col" data-key={ col.key } data-type={ col.type }>
								{ col.label }
							</th>
						) ) }
					</tr>
				</thead>
				<tbody>
					{ rows.map( ( row, index ) => {
						if ( row.isDivider ) {
							return (
								<tr key={ index } className="lunar-table__row--divider">
									<td colSpan={ columns.length } className="lunar-table__divider-cell">
										{ row.dividerLabel ?? '' }
									</td>
								</tr>
							);
						}

						return (
							<tr key={ index }>
								{ columns.map( ( col ) => {
									if ( 'image' === col.type ) {
										const image = row[ col.key ];

										return (
											<td key={ col.key } data-label={ col.label } data-key={ col.key }>
												{ image?.url && (
													<img
														src={ image.url }
														alt={ image.alt || '' }
														style={ { width: ( col.imageWidth || 48 ) + 'px', height: 'auto' } }
													/>
												) }
											</td>
										);
									}

									return (
										<td key={ col.key } data-label={ col.label } data-key={ col.key }>
											{ row[ col.key ] ?? '' }
										</td>
									);
								} ) }
							</tr>
						);
					} ) }
				</tbody>
			</table>
		</div>
	);
}