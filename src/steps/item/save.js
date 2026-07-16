/**
 * Lokasi: lunar-core/src/steps/item/save.js
 * Markup statis satu Step — <li> polos, nomor urut sepenuhnya
 * dihasilkan CSS counter di frontend (lihat style.scss).
 */

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function save() {
	const blockProps = useBlockProps.save( {
		className: 'lunar-step',
	} );

	const innerBlocksProps = useInnerBlocksProps.save( blockProps );

	return <li { ...innerBlocksProps } />;
}
