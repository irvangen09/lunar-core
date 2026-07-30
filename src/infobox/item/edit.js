/**
 * Lokasi: lunar-core/src/infobox/item/edit.js
 * Tampilan editor untuk satu Infobox Field — bisa mode "Bebas"
 * (label manual) atau "Dikenali" (label tetap dari 5 field yang
 * disepakati, otomatis tersinkron ke post meta lewat PHP saat disimpan).
 *
 * Catatan penting (bug fix pasca-refactor pertama, lihat
 * Refactor_Proposal_Lunar_Infobox.md): blockProps HARUS dipasang pada
 * satu elemen pembungkus yang stabil (bukan langsung pada salah satu
 * RichText), karena block ini punya 2 RichText bersaudara (Label &
 * Value). Menempelkan blockProps langsung ke RichText Label
 * menyebabkan WordPress menganggap Label sebagai anchor fokus utama
 * block, sehingga fokus "lompat" kembali ke Label setiap kali mengetik
 * di Value. Wrapper di sini diberi "display:contents" (lihat
 * editor.scss) supaya tidak merusak layout grid dt/dd di
 * .lunar-infobox__fields — wrapper ini HANYA ada di editor, save.js
 * (frontend) tetap merender dt/dd tanpa wrapper sama sekali.
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RadioControl, SelectControl } from '@wordpress/components';
import { RECOGNIZED_FIELDS, getRecognizedLabel } from './recognized-fields';

export default function Edit( { attributes, setAttributes } ) {
	const { mode, label, recognizedField, value } = attributes;
	const isRecognized = mode === 'dikenali';

	const blockProps = useBlockProps( {
		className: 'lunar-infobox-field',
	} );

	const recognizedLabel = getRecognizedLabel( recognizedField ) || __( '— Pilih field —', 'lunar-core' );

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Pengaturan Field', 'lunar-core' ) }>
					<RadioControl
						label={ __( 'Mode Field', 'lunar-core' ) }
						selected={ mode }
						options={ [
							{ label: __( 'Bebas (label manual)', 'lunar-core' ), value: 'bebas' },
							{
								label: __( 'Dikenali (tersinkron ke filter)', 'lunar-core' ),
								value: 'dikenali',
							},
						] }
						onChange={ ( newMode ) => setAttributes( { mode: newMode } ) }
					/>

					{ mode === 'dikenali' && (
						<SelectControl
							label={ __( 'Field', 'lunar-core' ) }
							value={ recognizedField }
							options={ [
								{ label: __( '— Pilih —', 'lunar-core' ), value: '' },
								...RECOGNIZED_FIELDS,
							] }
							onChange={ ( newField ) => setAttributes( { recognizedField: newField } ) }
						/>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ isRecognized ? (
					<dt className="lunar-infobox-field__label lunar-infobox-field__label--recognized">
						{ recognizedLabel }
					</dt>
				) : (
					<RichText
						tagName="dt"
						className="lunar-infobox-field__label"
						placeholder={ __( 'Label…', 'lunar-core' ) }
						value={ label }
						onChange={ ( newLabel ) => setAttributes( { label: newLabel } ) }
						allowedFormats={ [] }
					/>
				) }

				<RichText
					tagName="dd"
					className="lunar-infobox-field__value"
					placeholder={ __( 'Nilai…', 'lunar-core' ) }
					value={ value }
					onChange={ ( newValue ) => setAttributes( { value: newValue } ) }
				/>
			</div>
		</>
	);
}