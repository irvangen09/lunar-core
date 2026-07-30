/**
 * Lokasi: lunar-core/src/infobox/save.js
 * Markup statis block induk Infobox.
 *
 * Refactor styling (lihat Refactor_Proposal_Lunar_Infobox.md):
 * pembungkus InnerBlocks diubah dari <div> menjadi <dl>, karena tiap
 * anak (infobox-item) sekarang merender pasangan <dt>/<dd> langsung
 * (lihat item/save.js) — <dl> adalah elemen semantik yang tepat untuk
 * membungkus daftar pasangan label-nilai tersebut.
 */

import { useBlockProps, useInnerBlocksProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { name, headingLevel, icon, imageUrl, imageAlt } = attributes;

	const blockProps = useBlockProps.save( {
		className: 'lunar-infobox',
	} );

	const innerBlocksProps = useInnerBlocksProps.save( {
		className: 'lunar-infobox__fields',
	} );

	const nameTagName = 'none' === headingLevel ? 'p' : headingLevel;

	// Sama seperti edit.js — deteksi otomatis dashicons butuh class dasar tambahan.
	const iconClassName = icon && icon.startsWith( 'dashicons-' ) ? `dashicons ${ icon }` : icon;

	return (
		<div { ...blockProps }>
			{ imageUrl && (
				<div className="lunar-infobox__media">
					<img src={ imageUrl } alt={ imageAlt } />
				</div>
			) }

			<div className="lunar-infobox__header">
				{ icon && <span className={ `lunar-infobox__icon ${ iconClassName }` } aria-hidden="true" /> }
				<RichText.Content tagName={ nameTagName } className="lunar-infobox__name" value={ name } />
			</div>

			<dl { ...innerBlocksProps } />
		</div>
	);
}