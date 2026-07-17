/**
 * Lokasi: lunar-core/src/timeline/item/edit.js
 * Tampilan editor block anak Timeline Item — Label (opsional),
 * Judul, dan Deskripsi (opsional).
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText } from '@wordpress/block-editor';

export default function Edit( { attributes, setAttributes } ) {
	const { label, title, description } = attributes;

	const blockProps = useBlockProps( {
		className: 'lunar-timeline-item',
	} );

	return (
		<li { ...blockProps }>
			<RichText
				tagName="span"
				className="lunar-timeline-item__label"
				value={ label }
				onChange={ ( value ) => setAttributes( { label: value } ) }
				placeholder={ __( 'Label (opsional \u2014 mis. tanggal, versi, atau penanda bebas)', 'lunar-core' ) }
				allowedFormats={ [] }
			/>
			<RichText
				tagName="h3"
				className="lunar-timeline-item__title"
				value={ title }
				onChange={ ( value ) => setAttributes( { title: value } ) }
				placeholder={ __( 'Judul', 'lunar-core' ) }
				allowedFormats={ [ 'core/bold', 'core/italic' ] }
			/>
			<RichText
				tagName="div"
				className="lunar-timeline-item__description"
				value={ description }
				onChange={ ( value ) => setAttributes( { description: value } ) }
				placeholder={ __( 'Deskripsi (opsional)', 'lunar-core' ) }
				allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
				multiline="p"
			/>
		</li>
	);
}
