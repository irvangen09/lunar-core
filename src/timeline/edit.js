/**
 * Lokasi: lunar-core/src/timeline/edit.js
 * Tampilan editor block induk Timeline — InnerBlocks berisi
 * Timeline Item, hanya menerima block anak tersebut.
 */

import {
	useBlockProps,
	useInnerBlocksProps,
} from '@wordpress/block-editor';

const ALLOWED_BLOCKS = [ 'lunar-core/timeline-item' ];

const TEMPLATE = [
	[ 'lunar-core/timeline-item' ],
	[ 'lunar-core/timeline-item' ],
];

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'lunar-timeline',
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: TEMPLATE,
		templateLock: false,
		renderAppender: useInnerBlocksProps.DefaultBlockAppender,
	} );

	return <ol { ...innerBlocksProps } />;
}
