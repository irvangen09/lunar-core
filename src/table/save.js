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
					{ rows.map( ( row, index ) => (
						<tr key={ index }>
							{ columns.map( ( col ) => (
								<td key={ col.key } data-label={ col.label } data-key={ col.key }>
									{ row[ col.key ] ?? '' }
								</td>
							) ) }
						</tr>
					) ) }
				</tbody>
			</table>
		</div>
	);
}