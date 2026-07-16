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
} from '@wordpress/block-editor';
import { Button } from '@wordpress/components';

const ALLOWED_BLOCKS = [ 'lunar-core/infobox-field' ];

const TEMPLATE = [
	[ 'lunar-core/infobox-field' ],
	[ 'lunar-core/infobox-field' ],
	[ 'lunar-core/infobox-field' ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { name, imageId, imageUrl, imageAlt } = attributes;

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

	return (
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

			<RichText
				tagName="p"
				className="lunar-infobox__name"
				placeholder={ __( 'Nama…', 'lunar-core' ) }
				value={ name }
				onChange={ ( value ) => setAttributes( { name: value } ) }
				allowedFormats={ [] }
			/>

			<div { ...innerBlocksProps } />
		</div>
	);
}
