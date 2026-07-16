/**
 * Lokasi: lunar-core/src/version-tag/index.js
 * Mendaftarkan RichText Format "Version/Patch Tag" — badge inline
 * yang bisa disisipkan di tengah kalimat lewat toolbar seleksi teks,
 * BUKAN lewat Block Inserter (berbeda dari Callout/Definition List).
 *
 * Revisi: sebelumnya sempat memakai `isFormatActive` yang diimpor manual
 * dari @wordpress/rich-text untuk menghitung status aktif — ini salah,
 * menyebabkan TypeError karena bukan begitu cara kerja Format API.
 * WordPress SUDAH otomatis memberi prop `isActive` ke fungsi edit
 * setiap Format yang terdaftar; tidak perlu dihitung ulang manual.
 */

import { __ } from '@wordpress/i18n';
import { useState } from '@wordpress/element';
import { registerFormatType, applyFormat, removeFormat } from '@wordpress/rich-text';
import { RichTextToolbarButton } from '@wordpress/block-editor';
import { Popover, MenuGroup, MenuItem } from '@wordpress/components';

import './style.scss';

const FORMAT_NAME = 'lunar-core/version-tag';

const VARIANTS = [
	{ value: 'ditambahkan', label: __( 'Ditambahkan', 'lunar-core' ) },
	{ value: 'diubah', label: __( 'Diubah', 'lunar-core' ) },
	{ value: 'dihapus', label: __( 'Dihapus', 'lunar-core' ) },
];

function VersionTagEdit( { value, onChange, isActive } ) {
	const [ isOpen, setIsOpen ] = useState( false );

	function applyVariant( variant ) {
		onChange(
			applyFormat( value, {
				type: FORMAT_NAME,
				attributes: { 'data-variant': variant },
			} )
		);
		setIsOpen( false );
	}

	function removeTag() {
		onChange( removeFormat( value, FORMAT_NAME ) );
		setIsOpen( false );
	}

	return (
		<>
			<RichTextToolbarButton
				icon="tag"
				title={ __( 'Version/Patch Tag', 'lunar-core' ) }
				onClick={ () => setIsOpen( ! isOpen ) }
				isActive={ isActive }
			/>
			{ isOpen && (
				<Popover onClose={ () => setIsOpen( false ) } placement="bottom-start">
					<MenuGroup label={ __( 'Pilih Tipe', 'lunar-core' ) }>
						{ VARIANTS.map( ( { value: variant, label } ) => (
							<MenuItem key={ variant } onClick={ () => applyVariant( variant ) }>
								{ label }
							</MenuItem>
						) ) }
						{ isActive && (
							<MenuItem onClick={ removeTag } isDestructive>
								{ __( 'Hapus Tag', 'lunar-core' ) }
							</MenuItem>
						) }
					</MenuGroup>
				</Popover>
			) }
		</>
	);
}

registerFormatType( FORMAT_NAME, {
	title: __( 'Version/Patch Tag', 'lunar-core' ),
	tagName: 'span',
	className: 'lunar-version-tag',
	attributes: {
		variant: 'data-variant',
	},
	edit: VersionTagEdit,
} );
