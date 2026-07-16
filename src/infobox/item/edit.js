/**
 * Lokasi: lunar-core/src/infobox/item/edit.js
 * Tampilan editor untuk satu Infobox Field — bisa mode "Bebas"
 * (label manual) atau "Dikenali" (label tetap dari 5 field yang
 * disepakati, otomatis tersinkron ke post meta lewat PHP saat disimpan).
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RadioControl, SelectControl } from '@wordpress/components';

const RECOGNIZED_FIELDS = [
	{ value: 'peran', label: __( 'Peran', 'lunar-core' ) },
	{ value: 'tier_alat', label: __( 'Tier Alat', 'lunar-core' ) },
	{ value: 'musim', label: __( 'Musim', 'lunar-core' ) },
	{ value: 'waktu_muncul', label: __( 'Waktu Muncul', 'lunar-core' ) },
	{ value: 'jenis_hasil', label: __( 'Jenis Hasil', 'lunar-core' ) },
];

export default function Edit( { attributes, setAttributes } ) {
	const { mode, label, recognizedField, value } = attributes;

	const blockProps = useBlockProps( {
		className: 'lunar-infobox-field',
	} );

	const recognizedLabel =
		RECOGNIZED_FIELDS.find( ( field ) => field.value === recognizedField )?.label ||
		__( '— Pilih field —', 'lunar-core' );

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
				{ mode === 'dikenali' ? (
					<span className="lunar-infobox-field__label lunar-infobox-field__label--recognized">
						{ recognizedLabel }
					</span>
				) : (
					<RichText
						tagName="span"
						className="lunar-infobox-field__label"
						placeholder={ __( 'Label…', 'lunar-core' ) }
						value={ label }
						onChange={ ( newLabel ) => setAttributes( { label: newLabel } ) }
						allowedFormats={ [] }
					/>
				) }

				<RichText
					tagName="span"
					className="lunar-infobox-field__value"
					placeholder={ __( 'Nilai…', 'lunar-core' ) }
					value={ value }
					onChange={ ( newValue ) => setAttributes( { value: newValue } ) }
				/>
			</div>
		</>
	);
}
