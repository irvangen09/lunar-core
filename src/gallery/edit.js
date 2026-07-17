/**
 * Lokasi: lunar-core/src/gallery/edit.js
 * Tampilan editor block induk Gallery — InnerBlocks berisi
 * Gallery Item, grid kolom diatur lewat context 'lunar-core/gallery-columns'.
 */

import { __ } from '@wordpress/i18n';
import {
	useBlockProps,
	InspectorControls,
	InnerBlocks,
	useInnerBlocksProps,
} from '@wordpress/block-editor';
import { PanelBody, __experimentalToggleGroupControl as ToggleGroupControl, __experimentalToggleGroupControlOption as ToggleGroupControlOption } from '@wordpress/components';

const ALLOWED_BLOCKS = [ 'lunar-core/gallery-item' ];

const GALLERY_ITEM_TEMPLATE = [
	[ 'lunar-core/gallery-item' ],
	[ 'lunar-core/gallery-item' ],
	[ 'lunar-core/gallery-item' ],
];

export default function Edit( { attributes, setAttributes } ) {
	const { columns } = attributes;

	const blockProps = useBlockProps( {
		className: `lunar-gallery lunar-gallery--columns-${ columns }`,
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: GALLERY_ITEM_TEMPLATE,
		orientation: 'horizontal',
		renderAppender: InnerBlocks.ButtonBlockAppender,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Pengaturan Galeri', 'lunar-core' ) }>
					<ToggleGroupControl
						label={ __( 'Jumlah Kolom', 'lunar-core' ) }
						value={ columns }
						isBlock
						onChange={ ( value ) =>
							setAttributes( { columns: Number( value ) } )
						}
					>
						<ToggleGroupControlOption value={ 2 } label="2" />
						<ToggleGroupControlOption value={ 3 } label="3" />
						<ToggleGroupControlOption value={ 4 } label="4" />
					</ToggleGroupControl>
				</PanelBody>
			</InspectorControls>
			<div { ...innerBlocksProps } />
		</>
	);
}
