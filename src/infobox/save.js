/**
 * Lokasi: lunar-core/src/infobox/save.js
 * Markup statis block induk Infobox.
 */

import { useBlockProps, useInnerBlocksProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { name, imageUrl, imageAlt } = attributes;

	const blockProps = useBlockProps.save( {
		className: 'lunar-infobox',
	} );

	const innerBlocksProps = useInnerBlocksProps.save( {
		className: 'lunar-infobox__fields',
	} );

	return (
		<div { ...blockProps }>
			{ imageUrl && (
				<div className="lunar-infobox__media">
					<img src={ imageUrl } alt={ imageAlt } />
				</div>
			) }

			<RichText.Content tagName="p" className="lunar-infobox__name" value={ name } />

			<div { ...innerBlocksProps } />
		</div>
	);
}
