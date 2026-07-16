/**
 * Lokasi: lunar-core/src/tabs/edit.js
 * Tampilan editor block induk Tabs — di editor SEMUA Tab Item
 * ditampilkan penuh berurutan (bukan UI tab interaktif), supaya
 * menulis konten tidak terganggu. Perilaku tab sungguhan cuma
 * berlaku di frontend lewat view.js.
 */

import { useBlockProps, useInnerBlocksProps, InnerBlocks } from '@wordpress/block-editor';

const ALLOWED_BLOCKS = [ 'lunar-core/tabs-item' ];

const TEMPLATE = [
	[ 'lunar-core/tabs-item' ],
	[ 'lunar-core/tabs-item' ],
];

export default function Edit() {
	const blockProps = useBlockProps( {
		className: 'lunar-tabs',
	} );

	const innerBlocksProps = useInnerBlocksProps( blockProps, {
		allowedBlocks: ALLOWED_BLOCKS,
		template: TEMPLATE,
		templateLock: false,
		orientation: 'horizontal',
		renderAppender: InnerBlocks.ButtonBlockAppender,
	} );

	return <div { ...innerBlocksProps } />;
}
