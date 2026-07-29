/**
 * Lokasi: lunar-core/src/table/table-toolbar.js
 * Toolbar block untuk Table — kontrol sisip/hapus baris & kolom serta
 * ubah tipe kolom, mengikuti pola interaksi WP Table Block (aksi
 * kontekstual berdasarkan sel yang sedang fokus, bukan tombol tambah
 * di akhir tabel).
 *
 * Sengaja pakai prop "text" (bukan "icon") pada DropdownMenu — supaya
 * tidak perlu dependency @wordpress/icons (lihat catatan di edit.js).
 */

import { __ } from '@wordpress/i18n';
import { BlockControls } from '@wordpress/block-editor';
import { ToolbarGroup, DropdownMenu, MenuGroup, MenuItem } from '@wordpress/components';

const COLUMN_TYPE_LABELS = {
	text: __( 'Teks', 'lunar-core' ),
	number: __( 'Angka', 'lunar-core' ),
	image: __( 'Gambar', 'lunar-core' ),
};

export default function TableToolbar( {
	focusedCell,
	rows,
	onInsertRowBefore,
	onInsertRowAfter,
	onDeleteRow,
	onToggleDivider,
	onInsertColumnBefore,
	onInsertColumnAfter,
	onDeleteColumn,
	onSetColumnType,
} ) {
	const hasFocusedRow = null !== focusedCell.rowIndex && -1 !== focusedCell.rowIndex;
	const hasFocusedColumn = null !== focusedCell.colIndex;
	const focusedRowIsDivider = hasFocusedRow && !! rows[ focusedCell.rowIndex ]?.isDivider;

	return (
		<BlockControls>
			<ToolbarGroup>
				<DropdownMenu text={ __( 'Baris', 'lunar-core' ) } label={ __( 'Aksi Baris', 'lunar-core' ) }>
					{ ( { onClose } ) => (
						<>
							<MenuGroup>
								<MenuItem
									disabled={ ! hasFocusedRow }
									onClick={ () => {
										onInsertRowBefore();
										onClose();
									} }
								>
									{ __( 'Sisipkan baris sebelum', 'lunar-core' ) }
								</MenuItem>
								<MenuItem
									disabled={ ! hasFocusedRow }
									onClick={ () => {
										onInsertRowAfter();
										onClose();
									} }
								>
									{ __( 'Sisipkan baris sesudah', 'lunar-core' ) }
								</MenuItem>
								<MenuItem
									disabled={ ! hasFocusedRow }
									isDestructive
									onClick={ () => {
										onDeleteRow();
										onClose();
									} }
								>
									{ __( 'Hapus baris ini', 'lunar-core' ) }
								</MenuItem>
							</MenuGroup>
							<MenuGroup label={ __( 'Divider', 'lunar-core' ) }>
								<MenuItem
									disabled={ ! hasFocusedRow }
									onClick={ () => {
										onToggleDivider();
										onClose();
									} }
								>
									{ focusedRowIsDivider
										? __( 'Batalkan sebagai Divider', 'lunar-core' )
										: __( 'Jadikan Baris Ini Divider', 'lunar-core' ) }
								</MenuItem>
							</MenuGroup>
						</>
					) }
				</DropdownMenu>

				<DropdownMenu text={ __( 'Kolom', 'lunar-core' ) } label={ __( 'Aksi Kolom', 'lunar-core' ) }>
					{ ( { onClose } ) => (
						<>
							<MenuGroup>
								<MenuItem
									disabled={ ! hasFocusedColumn }
									onClick={ () => {
										onInsertColumnBefore();
										onClose();
									} }
								>
									{ __( 'Sisipkan kolom sebelum', 'lunar-core' ) }
								</MenuItem>
								<MenuItem
									disabled={ ! hasFocusedColumn }
									onClick={ () => {
										onInsertColumnAfter();
										onClose();
									} }
								>
									{ __( 'Sisipkan kolom sesudah', 'lunar-core' ) }
								</MenuItem>
								<MenuItem
									disabled={ ! hasFocusedColumn }
									isDestructive
									onClick={ () => {
										onDeleteColumn();
										onClose();
									} }
								>
									{ __( 'Hapus kolom ini', 'lunar-core' ) }
								</MenuItem>
							</MenuGroup>
							<MenuGroup label={ __( 'Ubah Tipe Kolom', 'lunar-core' ) }>
								{ Object.keys( COLUMN_TYPE_LABELS ).map( ( type ) => (
									<MenuItem
										key={ type }
										disabled={ ! hasFocusedColumn }
										onClick={ () => {
											onSetColumnType( type );
											onClose();
										} }
									>
										{ COLUMN_TYPE_LABELS[ type ] }
									</MenuItem>
								) ) }
							</MenuGroup>
						</>
					) }
				</DropdownMenu>
			</ToolbarGroup>
		</BlockControls>
	);
}