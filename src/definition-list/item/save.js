/**
 * Lokasi: lunar-core/src/definition-list/item/save.js
 * Markup statis satu item Definition List (Istilah + Definisi).
 */

import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { term, definition } = attributes;

	const blockProps = useBlockProps.save( {
		className: 'lunar-definition-item',
	} );

	return (
		<div { ...blockProps }>
			<RichText.Content tagName="dt" className="lunar-definition-item__term" value={ term } />
			<RichText.Content tagName="dd" className="lunar-definition-item__definition" value={ definition } />
		</div>
	);
}
