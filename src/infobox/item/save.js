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
 *
 * Refactor styling (lihat Refactor_Proposal_Lunar_Infobox.md):
 * - Markup diubah dari <div><span><span> menjadi pasangan <dt>/<dd> agar
 *   parent (infobox/save.js) bisa membungkusnya dengan <dl>, sesuai
 *   semantik "definition list" untuk pasangan label-nilai.
 * - Field dengan value kosong tidak dirender sama sekali (konsisten
 *   dengan pola Timeline/Gallery — hindari baris kosong nyasar di
 *   frontend apabila pengelola menambah field tapi belum mengisi nilai).
 */

import { useBlockProps, RichText } from '@wordpress/block-editor';
import { getRecognizedLabel } from './recognized-fields';

export default function save( { attributes } ) {
	const { mode, label, recognizedField, value } = attributes;

	// Field tanpa nilai tidak menghasilkan markup apa pun.
	if ( RichText.isEmpty( value ) ) {
		return null;
	}

	const isRecognized = mode === 'dikenali';
	const displayLabel = isRecognized ? getRecognizedLabel( recognizedField ) : label;

	const dtProps = useBlockProps.save( {
		className: isRecognized
			? 'lunar-infobox-field__label lunar-infobox-field__label--recognized'
			: 'lunar-infobox-field__label',
		'data-mode': mode,
		...( isRecognized && recognizedField ? { 'data-field': recognizedField } : {} ),
	} );

	return (
		<>
			<dt { ...dtProps }>{ displayLabel }</dt>
			<RichText.Content tagName="dd" className="lunar-infobox-field__value" value={ value } />
		</>
	);
}