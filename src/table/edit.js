/**
 * Lokasi: lunar-core/src/table/edit.js
 * UI editor kustom untuk Table — mini-spreadsheet: kolom & baris bisa
 * ditambah/dihapus/diedit langsung di badan block, bukan InnerBlocks.
 */

import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	PanelBody,
	ToggleControl,
	Button,
	TextControl,
	TextareaControl,
	SelectControl,
} from '@wordpress/components';

const COLUMN_TYPES = [
	{ label: __( 'Teks', 'lunar-core' ), value: 'text' },
	{ label: __( 'Angka', 'lunar-core' ), value: 'number' },
	{ label: __( 'Gambar', 'lunar-core' ), value: 'image' },
];

const DEFAULT_IMAGE_WIDTH = 48;

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

	function updateColumnType( key, type ) {
		// Kolom yang baru diubah jadi tipe Gambar otomatis diberi lebar
		// default, supaya pengguna tidak perlu mengisi manual dari 0.
		const changes = { type };

		const col = columns.find( ( item ) => item.key === key );

		if ( 'image' === type && ! col?.imageWidth ) {
			changes.imageWidth = DEFAULT_IMAGE_WIDTH;
		}

		updateColumn( key, changes );
	}

	function addRow() {
		const newRow = { isDivider: false };
		columns.forEach( ( col ) => {
			newRow[ col.key ] = '';
		} );
		setAttributes( { rows: [ ...rows, newRow ] } );
	}

	function removeRow( index ) {
		setAttributes( { rows: rows.filter( ( _row, i ) => i !== index ) } );
	}

	function updateRow( index, changes ) {
		const newRows = rows.map( ( row, i ) => ( i === index ? { ...row, ...changes } : row ) );
		setAttributes( { rows: newRows } );
	}

	function updateCell( index, key, value ) {
		updateRow( index, { [ key ]: value } );
	}

	function updateCellImage( index, key, media ) {
		updateCell( index, key, media ? { id: media.id, url: media.url, alt: media.alt || '' } : null );
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
												onChange={ ( value ) => updateColumnType( col.key, value ) }
											/>
											{ 'image' === col.type && (
												<TextControl
													label={ __( 'Lebar Gambar (px)', 'lunar-core' ) }
													type="number"
													value={ col.imageWidth ?? DEFAULT_IMAGE_WIDTH }
													onChange={ ( value ) =>
														updateColumn( col.key, {
															imageWidth: parseInt( value, 10 ) || DEFAULT_IMAGE_WIDTH,
														} )
													}
												/>
											) }
											<Button
												onClick={ () => removeColumn( col.key ) }
												isDestructive
												isSmall
											>
												{ __( 'Hapus', 'lunar-core' ) }
											</Button>
										</th>
									) ) }
								</tr>
							</thead>
							<tbody>
								{ rows.map( ( row, index ) => (
									<tr key={ index } className={ row.isDivider ? 'lunar-table-editor__row--divider' : undefined }>
										{ row.isDivider ? (
											<td colSpan={ columns.length }>
												<TextControl
													label={ __( 'Teks Pembagi (melebar penuh)', 'lunar-core' ) }
													value={ row.dividerLabel ?? '' }
													onChange={ ( value ) => updateRow( index, { dividerLabel: value } ) }
												/>
											</td>
										) : (
											columns.map( ( col ) => (
												<td key={ col.key }>
													{ 'image' === col.type && (
														<MediaUploadCheck>
															<MediaUpload
																onSelect={ ( media ) => updateCellImage( index, col.key, media ) }
																allowedTypes={ [ 'image' ] }
																value={ row[ col.key ]?.id }
																render={ ( { open } ) =>
																	row[ col.key ]?.url ? (
																		<div className="lunar-table-editor__image-cell">
																			<img
																				src={ row[ col.key ].url }
																				alt=""
																				style={ { width: ( col.imageWidth || DEFAULT_IMAGE_WIDTH ) + 'px' } }
																			/>
																			<Button variant="link" onClick={ open } isSmall>
																				{ __( 'Ganti', 'lunar-core' ) }
																			</Button>
																			<Button
																				variant="link"
																				isDestructive
																				isSmall
																				onClick={ () => updateCellImage( index, col.key, null ) }
																			>
																				{ __( 'Hapus Gambar', 'lunar-core' ) }
																			</Button>
																		</div>
																	) : (
																		<Button variant="secondary" isSmall onClick={ open }>
																			{ __( 'Pilih Gambar', 'lunar-core' ) }
																		</Button>
																	)
																}
															/>
														</MediaUploadCheck>
													) }

													{ 'number' === col.type && (
														<TextControl
															type="number"
															value={ row[ col.key ] ?? '' }
															onChange={ ( value ) => updateCell( index, col.key, value ) }
														/>
													) }

													{ 'text' === col.type && (
														<TextareaControl
															value={ row[ col.key ] ?? '' }
															onChange={ ( value ) => updateCell( index, col.key, value ) }
															rows={ 2 }
														/>
													) }
												</td>
											) )
										) }
										<td className="lunar-table-editor__row-actions">
											<ToggleControl
												label={ __( 'Baris pembagi', 'lunar-core' ) }
												checked={ !! row.isDivider }
												onChange={ ( value ) => updateRow( index, { isDivider: value } ) }
											/>
											<Button
												onClick={ () => removeRow( index ) }
												isDestructive
												isSmall
											>
												{ __( 'Hapus', 'lunar-core' ) }
											</Button>
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