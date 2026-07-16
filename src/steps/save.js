/**
 * Lokasi: lunar-core/src/steps/save.js
 * Markup statis block induk Steps.
 */

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function save() {
	const blockProps = useBlockProps.save( {
		className: 'lunar-steps',
	} );

	const innerBlocksProps = useInnerBlocksProps.save( blockProps );

	return <ol { ...innerBlocksProps } />;
}
