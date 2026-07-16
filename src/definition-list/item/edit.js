/**
 * Lokasi: lunar-core/src/definition-list/item/edit.js
 * Tampilan editor untuk satu item Definition List (Istilah + Definisi).
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function Edit( { attributes, setAttributes } ) {
	const { term, definition } = attributes;

	const blockProps = useBlockProps( {
		className: 'lunar-definition-item',
	} );

	return (
		<div { ...blockProps }>
			<RichText
				tagName="dt"
				className="lunar-definition-item__term"
				placeholder={ __( 'Istilah…', 'lunar-core' ) }
				value={ term }
				onChange={ ( value ) => setAttributes( { term: value } ) }
				allowedFormats={ [ 'core/bold', 'core/italic' ] }
			/>
			<RichText
				tagName="dd"
				className="lunar-definition-item__definition"
				placeholder={ __( 'Definisi…', 'lunar-core' ) }
				value={ definition }
				onChange={ ( value ) => setAttributes( { definition: value } ) }
			/>
		</div>
	);
}
