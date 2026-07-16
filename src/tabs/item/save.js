/**
 * Lokasi: lunar-core/src/tabs/item/save.js
 * Markup statis satu Tab Item.
 *
 * Label sengaja dirender sebagai <div> biasa (BUKAN <button>) —
 * karena tanpa JS, label ini cuma teks penanda section, bukan tombol
 * yang benar-benar bisa diklik (kalau dijadikan <button> tapi tidak
 * berfungsi tanpa JS, itu lebih membingungkan daripada teks polos).
 * view.js yang akan mengubahnya jadi kontrol tab interaktif sungguhan
 * saat runtime.
 */

import { useBlockProps, useInnerBlocksProps, RichText } from '@wordpress/block-editor';

export default function save( { attributes } ) {
	const { label } = attributes;

	const blockProps = useBlockProps.save( {
		className: 'lunar-tabs-item',
	} );

	const innerBlocksProps = useInnerBlocksProps.save( {
		className: 'lunar-tabs-item__content',
	} );

	return (
		<div { ...blockProps }>
			<RichText.Content tagName="div" className="lunar-tabs-item__label" value={ label } />
			<div { ...innerBlocksProps } />
		</div>
	);
}
