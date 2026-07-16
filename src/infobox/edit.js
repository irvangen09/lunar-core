/**
 * Lokasi: lunar-core/src/infobox/edit.js
 * Tampilan editor block induk Infobox — gambar utama, nama, dan
 * InnerBlocks berisi field-field (hanya menerima "Infobox Field").
 */

import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	useInnerBlocksProps,
	InnerBlocks,
	RichText,
	MediaUpload,
	MediaUploadCheck,
	InspectorControls,
} from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl } from '@wordpress/components';

const ALLOWED_BLOCKS = [ 'lunar-core/infobox-field' ];

const TEMPLATE = [
	[ 'lunar-core/infobox-field' ],
	[ 'lunar-core/infobox-field' ],
	[ 'lunar-core/infobox-field' ],
];

const HEADING_LEVEL_OPTIONS = [
	{ label: __( 'Tanpa Heading (paragraf biasa)', 'lunar-core' ), value: 'none' },
	{ label: 'H2', value: 'h2' },
	{ label: 'H3', value: 'h3' },
	{ label: 'H4', value: 'h4' },
	{ label: 'H5', value: 'h5' },
	{ label: 'H6', value: 'h6' },
];

export default function Edit( { attributes, setAttributes } ) {
	const { name, headingLevel, icon, imageId, imageUrl, imageAlt } = attributes;

	const blockProps = useBlockProps( {
		className: 'lunar-infobox',
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'lunar-infobox__fields' },
		{
			allowedBlocks: ALLOWED_BLOCKS,
			template: TEMPLATE,
			templateLock: false,
			renderAppender: InnerBlocks.ButtonBlockAppender,
		}
	);

	function onSelectImage( media ) {
		setAttributes( {
			imageId: media.id,
			imageUrl: media.url,
			imageAlt: media.alt || '',
		} );
	}

	function onRemoveImage() {
		setAttributes( { imageId: 0, imageUrl: '', imageAlt: '' } );
	}

	const nameTagName = 'none' === headingLevel ? 'p' : headingLevel;

	// Dashicons butuh class dasar "dashicons" + class spesifik (mis. "dashicons-admin-users")
	// supaya font ikonnya benar-benar aktif — tanpa ini, browser menampilkan kotak kosong
	// ("tofu") karena tidak tahu harus pakai font apa. Dideteksi otomatis supaya penulis
	// cukup ketik nama dashicon-nya saja, tanpa perlu tahu detail teknis ini.
	const iconClassName = icon && icon.startsWith( 'dashicons-' ) ? `dashicons ${ icon }` : icon;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Pengaturan Judul Infobox', 'lunar-core' ) }>
					<SelectControl
						label={ __( 'Heading Level', 'lunar-core' ) }
						value={ headingLevel }
						options={ HEADING_LEVEL_OPTIONS }
						onChange={ ( value ) => setAttributes( { headingLevel: value } ) }
						help={ __(
							'Pilih "Tanpa Heading" bila judul infobox ini tidak perlu ikut terdeteksi Table of Contents.',
							'lunar-core'
						) }
					/>
					<TextControl
						label={ __( 'Icon (opsional)', 'lunar-core' ) }
						value={ icon }
						onChange={ ( value ) => setAttributes( { icon: value } ) }
						placeholder={ __( 'mis. dashicons-admin-users, fa fa-user', 'lunar-core' ) }
						help={ __(
							'Masukkan nama class icon (dashicons bawaan WordPress, atau library lain seperti Font Awesome bila sudah dimuat tema). Boleh dikosongkan.',
							'lunar-core'
						) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="lunar-infobox__media">
					<MediaUploadCheck>
						<MediaUpload
							onSelect={ onSelectImage }
							allowedTypes={ [ 'image' ] }
							value={ imageId }
							render={ ( { open } ) =>
								imageUrl ? (
									<div className="lunar-infobox__media-preview">
										<img src={ imageUrl } alt={ imageAlt } />
										<Button variant="secondary" onClick={ open }>
											{ __( 'Ganti Gambar', 'lunar-core' ) }
										</Button>
										<Button variant="tertiary" isDestructive onClick={ onRemoveImage }>
											{ __( 'Hapus Gambar', 'lunar-core' ) }
										</Button>
									</div>
								) : (
									<Button variant="secondary" onClick={ open }>
										{ __( 'Pilih Gambar', 'lunar-core' ) }
									</Button>
								)
							}
						/>
					</MediaUploadCheck>
				</div>

				<div className="lunar-infobox__header">
					{ icon && <span className={ `lunar-infobox__icon ${ iconClassName }` } aria-hidden="true" /> }
					<RichText
						tagName={ nameTagName }
						className="lunar-infobox__name"
						placeholder={ __( 'Judul infobox… (mis. Informasi Dasar, atau nama karakter)', 'lunar-core' ) }
						value={ name }
						onChange={ ( value ) => setAttributes( { name: value } ) }
						allowedFormats={ [] }
					/>
				</div>

				<div { ...innerBlocksProps } />
			</div>
		</>
	);
}
