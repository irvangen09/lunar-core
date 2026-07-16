/**
 * Lokasi: lunar-core/src/definition-list/edit.js
 * Tampilan editor block induk Definition List — membungkus InnerBlocks
 * yang hanya menerima block anak "lunar-core/definition-item".
 */

import { useBlockProps, useInnerBlocksProps, InnerBlocks } from '@wordpress/block-editor';

const ALLOWED_BLOCKS = [ 'lunar-core/definition-item' ];

const TEMPLATE = [
	[ 'lunar-core/definition-item' ],
	[ 'lunar-core/definition-item' ],
];

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'lunar-definition-list',
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: TEMPLATE,
		templateLock: false,
		renderAppender: InnerBlocks.ButtonBlockAppender,
	} );

	return <dl { ...innerBlocksProps } />;
}
