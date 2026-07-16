/**
 * Lokasi: lunar-core/src/steps/item/edit.js
 * Tampilan editor satu Step — cuma pembungkus <li> untuk InnerBlocks
 * bebas. Nomor urut TIDAK diatur di sini — murni CSS counter di
 * style.scss/editor.scss.
 */

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

const CONTENT_TEMPLATE = [ [ 'core/paragraph' ] ];

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'lunar-step',
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		template: CONTENT_TEMPLATE,
	} );

	return <li { ...innerBlocksProps } />;
}
