/**
 * Lokasi: lunar-core/src/infobox/item/edit.js
 * Tampilan editor untuk satu Infobox Field — bisa mode "Bebas"
 * (label manual) atau "Dikenali" (field dipilih dari taxonomy Field,
 * otomatis tersinkron ke post meta lewat PHP saat disimpan).
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
 *
 * Catatan refactor decoupling (Lunar_Core_Themes_Decoupling_Proposal.md §9):
 * daftar field "Dikenali" TIDAK lagi berasal dari kamus statis
 * (recognized-fields.js, sudah dihapus) — sekarang diambil lewat REST
 * dari taxonomy "wiki_field", supaya pengelola situs bisa menambah field
 * baru kapan pun lewat wp-admin tanpa perlu update kode. Attribute
 * "recognizedField" menyimpan Term ID (bukan lagi slug string), dan
 * "recognizedFieldLabel" menyimpan nama term saat dipilih — disimpan
 * terpisah supaya save.js (yang wajib pure function, tidak boleh
 * memanggil REST) tetap bisa merender label tanpa perlu resolusi apa pun.
 */

import { __ } from '@wordpress/i18n';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, RadioControl, SelectControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { store as coreStore } from '@wordpress/core-data';

const FIELD_TAXONOMY = 'wiki_field';

export default function Edit( { attributes, setAttributes } ) {
	const { mode, label, recognizedField, recognizedFieldLabel, value } = attributes;
	const isRecognized = mode === 'dikenali';

	const blockProps = useBlockProps( {
		className: 'lunar-infobox-field',
	} );

	const { fieldTerms, isLoadingTerms } = useSelect( ( select ) => {
		const query = { per_page: -1, hide_empty: false };
		const { getEntityRecords, isResolving } = select( coreStore );

		return {
			fieldTerms: getEntityRecords( 'taxonomy', FIELD_TAXONOMY, query ) || [],
			isLoadingTerms: isResolving( 'getEntityRecords', [ 'taxonomy', FIELD_TAXONOMY, query ] ),
		};
	}, [] );

	const fieldOptions = [
		{ label: __( '— Pilih —', 'lunar-core' ), value: 0 },
		...fieldTerms.map( ( term ) => ( { label: term.name, value: term.id } ) ),
	];

	const handleFieldChange = ( newValue ) => {
		const newTermId = Number( newValue );
		const matchedTerm = fieldTerms.find( ( term ) => term.id === newTermId );

		setAttributes( {
			recognizedField: newTermId,
			recognizedFieldLabel: matchedTerm ? matchedTerm.name : '',
		} );
	};

	const displayLabel = recognizedFieldLabel || __( '— Pilih field —', 'lunar-core' );

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
							options={ fieldOptions }
							onChange={ handleFieldChange }
							help={
								isLoadingTerms
									? __( 'Memuat daftar field…', 'lunar-core' )
									: undefined
							}
						/>
					) }
				</PanelBody>
			</InspectorControls>

			<div { ...blockProps }>
				{ isRecognized ? (
					<dt className="lunar-infobox-field__label lunar-infobox-field__label--recognized">
						{ displayLabel }
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