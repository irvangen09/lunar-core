/**
 * Lokasi: lunar-core/src/table/edit.js
 * UI editor Table — mengikuti pola interaksi WP Table Block bawaan:
 * grid tabel yang bersih (cuma label header & isi sel), sisip/hapus
 * baris & kolom lewat toolbar kontekstual (table-toolbar.js) sesuai
 * sel yang sedang fokus, bukan tombol tambah/hapus di tiap kolom/baris.
 *
 * Beda sengaja dari WP Table Block asli: sel di sini TIDAK memakai
 * RichText (isi tetap teks polos / angka / gambar, bukan HTML) —
 * data Table adalah data terstruktur (harga, jadwal, stat item),
 * bukan teks naratif yang butuh format kaya (bold/italic/dll).
 * Keputusan ini disepakati eksplisit oleh Product Owner.
 */

import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import { PanelBody, ToggleControl, TextControl, TextareaControl, Button } from '@wordpress/components';
import TableToolbar from './table-toolbar';

const DEFAULT_IMAGE_WIDTH = 48;

function generateColumnKey() {
	return 'col_' + Math.random().toString( 36 ).slice( 2, 8 );
}

function buildEmptyRow( columns ) {
	const row = { isDivider: false };
	columns.forEach( ( col ) => {
		row[ col.key ] = '';
	} );
	return row;
}

export default function Edit( { attributes, setAttributes } ) {
	const { columns, rows, enableSort, enableFilter } = attributes;

	// { rowIndex, colIndex } dari sel yang terakhir difokus pengguna —
	// menentukan aksi mana yang aktif di TableToolbar. rowIndex -1
	// berarti sedang fokus di baris header (label kolom).
	const [ focusedCell, setFocusedCell ] = useState( { rowIndex: null, colIndex: null } );

	// State lokal untuk form "buat tabel" (belum jadi attributes
	// sampai tombol "Buat Tabel" ditekan).
	const [ pendingColumnCount, setPendingColumnCount ] = useState( '3' );
	const [ pendingRowCount, setPendingRowCount ] = useState( '3' );

	const blockProps = useBlockProps( {
		className: 'lunar-table-editor',
	} );

	function resetFocus() {
		setFocusedCell( { rowIndex: null, colIndex: null } );
	}

	function createTable() {
		const columnCount = Math.max( 1, parseInt( pendingColumnCount, 10 ) || 1 );
		const rowCount = Math.max( 1, parseInt( pendingRowCount, 10 ) || 1 );

		const newColumns = [];
		for ( let i = 0; i < columnCount; i++ ) {
			newColumns.push( { key: generateColumnKey(), label: '', type: 'text' } );
		}

		const newRows = [];
		for ( let i = 0; i < rowCount; i++ ) {
			newRows.push( buildEmptyRow( newColumns ) );
		}

		setAttributes( { columns: newColumns, rows: newRows } );
	}

	function updateColumnLabel( colIndex, label ) {
		const newColumns = columns.map( ( col, i ) => ( i === colIndex ? { ...col, label } : col ) );
		setAttributes( { columns: newColumns } );
	}

	function setColumnType( type ) {
		const colIndex = focusedCell.colIndex;

		if ( null === colIndex ) {
			return;
		}

		const changes = { type };

		if ( 'image' === type && ! columns[ colIndex ]?.imageWidth ) {
			changes.imageWidth = DEFAULT_IMAGE_WIDTH;
		}

		const newColumns = columns.map( ( col, i ) => ( i === colIndex ? { ...col, ...changes } : col ) );
		setAttributes( { columns: newColumns } );
	}

	function setColumnImageWidth( colIndex, width ) {
		const newColumns = columns.map( ( col, i ) => ( i === colIndex ? { ...col, imageWidth: width } : col ) );
		setAttributes( { columns: newColumns } );
	}

	function insertColumn( atIndex ) {
		const newColumn = { key: generateColumnKey(), label: '', type: 'text' };
		const newColumns = [ ...columns ];
		newColumns.splice( atIndex, 0, newColumn );

		const newRows = rows.map( ( row ) => ( { ...row, [ newColumn.key ]: '' } ) );

		setAttributes( { columns: newColumns, rows: newRows } );
		resetFocus();
	}

	function deleteColumn() {
		const colIndex = focusedCell.colIndex;

		if ( null === colIndex ) {
			return;
		}

		const key = columns[ colIndex ].key;
		const newColumns = columns.filter( ( _col, i ) => i !== colIndex );
		const newRows = rows.map( ( row ) => {
			const updated = { ...row };
			delete updated[ key ];
			return updated;
		} );

		setAttributes( { columns: newColumns, rows: newRows } );
		resetFocus();
	}

	function insertRow( atIndex ) {
		const newRows = [ ...rows ];
		newRows.splice( atIndex, 0, buildEmptyRow( columns ) );
		setAttributes( { rows: newRows } );
		resetFocus();
	}

	function deleteRow() {
		const rowIndex = focusedCell.rowIndex;

		if ( null === rowIndex || -1 === rowIndex ) {
			return;
		}

		setAttributes( { rows: rows.filter( ( _row, i ) => i !== rowIndex ) } );
		resetFocus();
	}

	function toggleDivider() {
		const rowIndex = focusedCell.rowIndex;

		if ( null === rowIndex || -1 === rowIndex ) {
			return;
		}

		const newRows = rows.map( ( row, i ) => ( i === rowIndex ? { ...row, isDivider: ! row.isDivider } : row ) );
		setAttributes( { rows: newRows } );
	}

	function updateRow( rowIndex, changes ) {
		const newRows = rows.map( ( row, i ) => ( i === rowIndex ? { ...row, ...changes } : row ) );
		setAttributes( { rows: newRows } );
	}

	function updateCell( rowIndex, key, value ) {
		updateRow( rowIndex, { [ key ]: value } );
	}

	function updateCellImage( rowIndex, key, media ) {
		updateCell( rowIndex, key, media ? { id: media.id, url: media.url, alt: media.alt || '' } : null );
	}

	const focusedColumn = null !== focusedCell.colIndex ? columns[ focusedCell.colIndex ] : null;

	return (
		<>
			{ columns.length > 0 && (
				<TableToolbar
					focusedCell={ focusedCell }
					rows={ rows }
					onInsertRowBefore={ () => insertRow( focusedCell.rowIndex ) }
					onInsertRowAfter={ () => insertRow( focusedCell.rowIndex + 1 ) }
					onDeleteRow={ deleteRow }
					onToggleDivider={ toggleDivider }
					onInsertColumnBefore={ () => insertColumn( focusedCell.colIndex ) }
					onInsertColumnAfter={ () => insertColumn( focusedCell.colIndex + 1 ) }
					onDeleteColumn={ deleteColumn }
					onSetColumnType={ setColumnType }
				/>
			) }

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

					{ focusedColumn && 'image' === focusedColumn.type && (
						<TextControl
							label={ __( 'Lebar Gambar — Kolom Aktif (px)', 'lunar-core' ) }
							help={ __( 'Kolom aktif ditentukan oleh sel yang terakhir Anda klik.', 'lunar-core' ) }
							type="number"
							value={ focusedColumn.imageWidth ?? DEFAULT_IMAGE_WIDTH }
							onChange={ ( value ) =>
								setColumnImageWidth( focusedCell.colIndex, parseInt( value, 10 ) || DEFAULT_IMAGE_WIDTH )
							}
						/>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ 0 === columns.length ? (
					<div className="lunar-table-editor__empty">
						<p>{ __( 'Buat tabel baru.', 'lunar-core' ) }</p>
						<div className="lunar-table-editor__empty-fields">
							<TextControl
								label={ __( 'Jumlah Kolom', 'lunar-core' ) }
								type="number"
								min="1"
								value={ pendingColumnCount }
								onChange={ setPendingColumnCount }
							/>
							<TextControl
								label={ __( 'Jumlah Baris', 'lunar-core' ) }
								type="number"
								min="1"
								value={ pendingRowCount }
								onChange={ setPendingRowCount }
							/>
						</div>
						<Button variant="primary" onClick={ createTable }>
							{ __( 'Buat Tabel', 'lunar-core' ) }
						</Button>
					</div>
				) : (
					<table className="lunar-table-editor__grid">
						<thead>
							<tr>
								{ columns.map( ( col, colIndex ) => (
									<th key={ col.key }>
										<TextControl
											label={ __( 'Label kolom', 'lunar-core' ) }
											hideLabelFromVision
											placeholder={ __( 'Label kolom', 'lunar-core' ) }
											value={ col.label }
											onChange={ ( value ) => updateColumnLabel( colIndex, value ) }
											onFocus={ () => setFocusedCell( { rowIndex: -1, colIndex } ) }
										/>
									</th>
								) ) }
							</tr>
						</thead>
						<tbody>
							{ rows.map( ( row, rowIndex ) => (
								<tr
									key={ rowIndex }
									className={ row.isDivider ? 'lunar-table-editor__row--divider' : undefined }
								>
									{ row.isDivider ? (
										<td colSpan={ columns.length }>
											<TextControl
												label={ __( 'Teks pembagi', 'lunar-core' ) }
												hideLabelFromVision
												placeholder={ __( 'Teks pembagi…', 'lunar-core' ) }
												value={ row.dividerLabel ?? '' }
												onChange={ ( value ) => updateRow( rowIndex, { dividerLabel: value } ) }
												onFocus={ () => setFocusedCell( { rowIndex, colIndex: null } ) }
											/>
										</td>
									) : (
										columns.map( ( col, colIndex ) => (
											<td key={ col.key }>
												{ 'image' === col.type && (
													<MediaUploadCheck>
														<MediaUpload
															onSelect={ ( media ) => updateCellImage( rowIndex, col.key, media ) }
															allowedTypes={ [ 'image' ] }
															value={ row[ col.key ]?.id }
															render={ ( { open } ) => (
																<div onFocus={ () => setFocusedCell( { rowIndex, colIndex } ) }>
																	{ row[ col.key ]?.url ? (
																		<div className="lunar-table-editor__image-cell">
																			<img
																				src={ row[ col.key ].url }
																				alt=""
																				style={ {
																					width: ( col.imageWidth || DEFAULT_IMAGE_WIDTH ) + 'px',
																				} }
																			/>
																			<Button variant="link" onClick={ open } isSmall>
																				{ __( 'Ganti', 'lunar-core' ) }
																			</Button>
																			<Button
																				variant="link"
																				isDestructive
																				isSmall
																				onClick={ () => updateCellImage( rowIndex, col.key, null ) }
																			>
																				{ __( 'Hapus Gambar', 'lunar-core' ) }
																			</Button>
																		</div>
																	) : (
																		<Button variant="secondary" isSmall onClick={ open }>
																			{ __( 'Pilih Gambar', 'lunar-core' ) }
																		</Button>
																	) }
																</div>
															) }
														/>
													</MediaUploadCheck>
												) }

												{ 'number' === col.type && (
													<TextControl
														label={ __( 'Nilai sel', 'lunar-core' ) }
														hideLabelFromVision
														type="number"
														value={ row[ col.key ] ?? '' }
														onChange={ ( value ) => updateCell( rowIndex, col.key, value ) }
														onFocus={ () => setFocusedCell( { rowIndex, colIndex } ) }
													/>
												) }

												{ 'text' === col.type && (
													<TextareaControl
														label={ __( 'Nilai sel', 'lunar-core' ) }
														hideLabelFromVision
														value={ row[ col.key ] ?? '' }
														onChange={ ( value ) => updateCell( rowIndex, col.key, value ) }
														onFocus={ () => setFocusedCell( { rowIndex, colIndex } ) }
														rows={ 2 }
													/>
												) }
											</td>
										) )
									) }
								</tr>
							) ) }
						</tbody>
					</table>
				) }
			</div>
		</>
	);
}