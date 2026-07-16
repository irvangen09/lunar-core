/**
 * Lokasi: lunar-core/src/steps/edit.js
 * Tampilan editor block induk Steps — membungkus InnerBlocks
 * yang hanya menerima block anak "lunar-core/step".
 */

import { useBlockProps, useInnerBlocksProps, InnerBlocks } from '@wordpress/block-editor';

const ALLOWED_BLOCKS = [ 'lunar-core/step' ];

const TEMPLATE = [
	[ 'lunar-core/step' ],
	[ 'lunar-core/step' ],
];

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'lunar-steps',
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: TEMPLATE,
		templateLock: false,
		renderAppender: InnerBlocks.ButtonBlockAppender,
	} );

	return <ol { ...innerBlocksProps } />;
}
