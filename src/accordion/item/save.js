/**
 * Lokasi: lunar-core/src/accordion/item/save.js
 * Markup statis satu Accordion Item — di sinilah struktur semantik
 * <details>/<summary> sungguhan dirender (beda dari edit.js yang
 * pakai <div> biasa demi kenyamanan menulis konten).
 *
 * Sengaja TIDAK menyertakan attribute "open" — defaultnya tertutup,
 * sesuai perilaku native <details> tanpa atribut tersebut. Perilaku
 * "selalu terbuka di desktop" ditangani lewat CSS (style.scss),
 * bukan lewat markup di sini.
 */

import { useBlockProps, useInnerBlocksProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { title, headingLevel, icon } = attributes;

	const blockProps = useBlockProps.save( {
		className: 'lunar-accordion-item',
	} );

	const innerBlocksProps = useInnerBlocksProps.save( {
		className: 'lunar-accordion-item__content',
	} );

	// Fallback "span" (bukan "p" seperti di Infobox) — karena elemen ini
	// akan berada di dalam <summary>, dan spesifikasi HTML membatasi isi
	// <summary> jadi "phrasing content" ATAU satu elemen heading saja.
	const titleTagName = 'none' === headingLevel ? 'span' : headingLevel;
	const iconClassName = icon && icon.startsWith( 'dashicons-' ) ? `dashicons ${ icon }` : icon;

	return (
		<details { ...blockProps }>
			<summary className="lunar-accordion-item__summary">
				{ icon && (
					<span className={ `lunar-accordion-item__icon ${ iconClassName }` } aria-hidden="true" />
				) }
				<RichText.Content tagName={ titleTagName } className="lunar-accordion-item__title" value={ title } />
			</summary>

			<div { ...innerBlocksProps } />
		</details>
	);
}
