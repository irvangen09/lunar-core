/**
 * Lokasi: lunar-core/src/callout/edit.js
 * Tampilan block Callout di Block Editor.
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';

const VARIANT_OPTIONS = [
	{ label: __( 'Info', 'lunar-core' ), value: 'info' },
	{ label: __( 'Tips', 'lunar-core' ), value: 'tips' },
	{ label: __( 'Peringatan', 'lunar-core' ), value: 'peringatan' },
	{ label: __( 'Penting', 'lunar-core' ), value: 'penting' },
];

export default function Edit( { attributes, setAttributes } ) {
	const { variant, content } = attributes;

	const blockProps = useBlockProps( {
		className: `lunar-callout lunar-callout--${ variant }`,
	} );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Pengaturan Callout', 'lunar-core' ) }>
					<SelectControl
						label={ __( 'Tipe Callout', 'lunar-core' ) }
						value={ variant }
						options={ VARIANT_OPTIONS }
						onChange={ ( value ) => setAttributes( { variant: value } ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<RichText
					tagName="div"
					className="lunar-callout__text"
					placeholder={ __( 'Tulis catatan di sini…', 'lunar-core' ) }
					value={ content }
					onChange={ ( value ) => setAttributes( { content: value } ) }
				/>
			</div>
		</>
	);
}
