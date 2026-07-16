/**
 * Lokasi: lunar-core/src/accordion/edit.js
 * Tampilan editor block induk Accordion — membungkus InnerBlocks
 * yang hanya menerima block anak "lunar-core/accordion-item".
 */

import { useBlockProps, useInnerBlocksProps, InnerBlocks } from '@wordpress/block-editor';

const ALLOWED_BLOCKS = [ 'lunar-core/accordion-item' ];

const TEMPLATE = [
	[ 'lunar-core/accordion-item' ],
	[ 'lunar-core/accordion-item' ],
];

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'lunar-accordion',
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: TEMPLATE,
		templateLock: false,
		renderAppender: InnerBlocks.ButtonBlockAppender,
	} );

	return <div { ...innerBlocksProps } />;
}
