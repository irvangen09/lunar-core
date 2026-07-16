/**
 * Lokasi: lunar-core/src/accordion/item/edit.js
 * Tampilan editor satu Accordion Item — judul (heading + icon opsional)
 * dan konten bebas (InnerBlocks tanpa pembatasan block).
 *
 * Catatan: di EDITOR, konten selalu ditampilkan penuh (bukan <details>
 * interaktif) supaya penulisan konten tidak terganggu oleh perilaku
 * buka/tutup — perilaku collapsible sungguhan hanya berlaku di
 * frontend (lihat save.js), sesuai konsep desktop-flat/mobile-collapsible
 * yang disepakati.
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, useInnerBlocksProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';

const HEADING_LEVEL_OPTIONS = [
	{ label: __( 'Tanpa Heading (paragraf biasa)', 'lunar-core' ), value: 'none' },
	{ label: 'H2', value: 'h2' },
	{ label: 'H3', value: 'h3' },
	{ label: 'H4', value: 'h4' },
	{ label: 'H5', value: 'h5' },
	{ label: 'H6', value: 'h6' },
];

const CONTENT_TEMPLATE = [ [ 'core/paragraph' ] ];

export default function Edit( { attributes, setAttributes } ) {
	const { title, headingLevel, icon } = attributes;

	const blockProps = useBlockProps( {
		className: 'lunar-accordion-item',
	} );

	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'lunar-accordion-item__content' },
		{ template: CONTENT_TEMPLATE }
	);

	const titleTagName = 'none' === headingLevel ? 'p' : headingLevel;
	const iconClassName = icon && icon.startsWith( 'dashicons-' ) ? `dashicons ${ icon }` : icon;

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Pengaturan Judul', 'lunar-core' ) }>
					<SelectControl
						label={ __( 'Heading Level', 'lunar-core' ) }
						value={ headingLevel }
						options={ HEADING_LEVEL_OPTIONS }
						onChange={ ( value ) => setAttributes( { headingLevel: value } ) }
						help={ __(
							'Judul section ini akan ikut terdeteksi Table of Contents (beda dari judul Infobox yang bersifat dekoratif).',
							'lunar-core'
						) }
					/>
					<TextControl
						label={ __( 'Icon (opsional)', 'lunar-core' ) }
						value={ icon }
						onChange={ ( value ) => setAttributes( { icon: value } ) }
						placeholder={ __( 'mis. dashicons-clock', 'lunar-core' ) }
						help={ __( 'Boleh dikosongkan.', 'lunar-core' ) }
					/>
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				<div className="lunar-accordion-item__header">
					{ icon && (
						<span className={ `lunar-accordion-item__icon ${ iconClassName }` } aria-hidden="true" />
					) }
					<RichText
						tagName={ titleTagName }
						className="lunar-accordion-item__title"
						placeholder={ __( 'Judul section…', 'lunar-core' ) }
						value={ title }
						onChange={ ( value ) => setAttributes( { title: value } ) }
						allowedFormats={ [] }
					/>
				</div>

				<div { ...innerBlocksProps } />
			</div>
		</>
	);
}
