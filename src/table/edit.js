/**
 * Lokasi: lunar-core/src/table/edit.js
 * UI editor kustom untuk Table — mini-spreadsheet: kolom & baris bisa
 * ditambah/dihapus/diedit langsung di badan block, bukan InnerBlocks.
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl, Button, TextControl, SelectControl } from '@wordpress/components';

const COLUMN_TYPES = [
	{ label: __( 'Teks', 'lunar-core' ), value: 'text' },
	{ label: __( 'Angka', 'lunar-core' ), value: 'number' },
];

function generateColumnKey() {
	return 'col_' + Math.random().toString( 36 ).slice( 2, 8 );
}

export default function Edit( { attributes, setAttributes } ) {
	const { columns, rows, enableSort, enableFilter } = attributes;

	const blockProps = useBlockProps( {
		className: 'lunar-table-editor',
	} );

	function addColumn() {
		const newKey = generateColumnKey();
		const newColumns = [ ...columns, { key: newKey, label: __( 'Kolom Baru', 'lunar-core' ), type: 'text' } ];
		const newRows = rows.map( ( row ) => ( { ...row, [ newKey ]: '' } ) );
		setAttributes( { columns: newColumns, rows: newRows } );
	}

	function removeColumn( key ) {
		const newColumns = columns.filter( ( col ) => col.key !== key );
		const newRows = rows.map( ( row ) => {
			const updated = { ...row };
			delete updated[ key ];
			return updated;
		} );
		setAttributes( { columns: newColumns, rows: newRows } );
	}

	function updateColumn( key, changes ) {
		const newColumns = columns.map( ( col ) => ( col.key === key ? { ...col, ...changes } : col ) );
		setAttributes( { columns: newColumns } );
	}

	function addRow() {
		const newRow = {};
		columns.forEach( ( col ) => {
			newRow[ col.key ] = '';
		} );
		setAttributes( { rows: [ ...rows, newRow ] } );
	}

	function removeRow( index ) {
		setAttributes( { rows: rows.filter( ( _row, i ) => i !== index ) } );
	}

	function updateCell( index, key, value ) {
		const newRows = rows.map( ( row, i ) => ( i === index ? { ...row, [ key ]: value } : row ) );
		setAttributes( { rows: newRows } );
	}

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Pengaturan Tabel', 'lunar-core' ) }>
					<ToggleControl
						label={ __( 'Bisa diurutkan (sort)', 'lunar-core' ) }
						checked={ enableSort }
						onChange={ ( value ) => setAttributes( { enableSort: value } ) }
					/>
					<ToggleControl
						label={ __( 'Bisa dicari (filter)', 'lunar-core' ) }
						checked={ enableFilter }
						onChange={ ( value ) => setAttributes( { enableFilter: value } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ 0 === columns.length ? (
					<div className="lunar-table-editor__empty">
						<p>{ __( 'Tabel masih kosong.', 'lunar-core' ) }</p>
						<Button variant="primary" onClick={ addColumn }>
							{ __( '+ Tambah Kolom Pertama', 'lunar-core' ) }
						</Button>
					</div>
				) : (
					<>
						<div className="lunar-table-editor__toolbar">
							<Button variant="secondary" onClick={ addColumn }>
								{ __( '+ Tambah Kolom', 'lunar-core' ) }
							</Button>
						</div>

						<table className="lunar-table-editor__grid">
							<thead>
								<tr>
									{ columns.map( ( col ) => (
										<th key={ col.key }>
											<TextControl
												label={ __( 'Label Kolom', 'lunar-core' ) }
												value={ col.label }
												onChange={ ( value ) => updateColumn( col.key, { label: value } ) }
											/>
											<SelectControl
												label={ __( 'Tipe', 'lunar-core' ) }
												value={ col.type }
												options={ COLUMN_TYPES }
												onChange={ ( value ) => updateColumn( col.key, { type: value } ) }
											/>
											<Button
												icon="trash"
												label={ __( 'Hapus Kolom', 'lunar-core' ) }
												onClick={ () => removeColumn( col.key ) }
												isDestructive
												isSmall
											/>
										</th>
									) ) }
								</tr>
							</thead>
							<tbody>
								{ rows.map( ( row, index ) => (
									<tr key={ index }>
										{ columns.map( ( col ) => (
											<td key={ col.key }>
												<TextControl
													value={ row[ col.key ] ?? '' }
													onChange={ ( value ) => updateCell( index, col.key, value ) }
												/>
											</td>
										) ) }
										<td>
											<Button
												icon="trash"
												label={ __( 'Hapus Baris', 'lunar-core' ) }
												onClick={ () => removeRow( index ) }
												isDestructive
												isSmall
											/>
										</td>
									</tr>
								) ) }
							</tbody>
						</table>
						<Button variant="secondary" onClick={ addRow }>
							{ __( '+ Tambah Baris', 'lunar-core' ) }
						</Button>
					</>
				) }
			</div>
		</>
	);
}