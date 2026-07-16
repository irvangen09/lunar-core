/**
 * Lokasi: lunar-core/src/definition-list/save.js
 * Markup statis block induk Definition List.
 */

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function save() {
	const blockProps = useBlockProps.save( {
		className: 'lunar-definition-list',
	} );

	const innerBlocksProps = useInnerBlocksProps.save( blockProps );

	return <dl { ...innerBlocksProps } />;
}
