/**
 * Lokasi: lunar-core/src/infobox/item/save.js
 * Markup statis satu Infobox Field.
 *
 * Catatan penting untuk Tahap 3.11 (PHP meta sync): attribute "mode"
 * dan "recognizedField" tersimpan di comment block (bisa langsung
 * dibaca lewat parse_blocks() di PHP tanpa perlu scraping HTML).
 * Attribute "value" TIDAK ikut tersimpan di comment (sumbernya rich-text
 * dari innerHTML) — PHP nanti perlu membaca innerHTML block ini untuk
 * mengambil isinya.
 */

import { useBlockProps, RichText } from '@wordpress/block-editor';
import { getRecognizedLabel } from './recognized-fields';

export default function save( { attributes } ) {
	const { mode, label, recognizedField, value } = attributes;

	const isRecognized = mode === 'dikenali';
	const displayLabel = isRecognized ? getRecognizedLabel( recognizedField ) : label;

	const blockProps = useBlockProps.save( {
		className: 'lunar-infobox-field',
		'data-mode': mode,
		...( isRecognized && recognizedField ? { 'data-field': recognizedField } : {} ),
	} );

	return (
		<div { ...blockProps }>
			<span
				className={
					isRecognized
						? 'lunar-infobox-field__label lunar-infobox-field__label--recognized'
						: 'lunar-infobox-field__label'
				}
			>
				{ displayLabel }
			</span>
			<RichText.Content tagName="span" className="lunar-infobox-field__value" value={ value } />
		</div>
	);
}