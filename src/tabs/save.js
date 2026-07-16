/**
 * Lokasi: lunar-core/src/tabs/save.js
 * Markup statis block induk Tabs.
 *
 * Tidak ada role="tablist" atau atribut ARIA lain di sini — semua
 * ditambahkan oleh view.js saat runtime (lihat catatan arsitektur
 * sebelum Tahap 3.1). Markup ini murni wadah polos.
 */

import { useBlockProps, useInnerBlocksProps } from '@wordpress/block-editor';

export default function save() {
	const blockProps = useBlockProps.save( {
		className: 'lunar-tabs',
	} );

	const innerBlocksProps = useInnerBlocksProps.save( blockProps );

	return <div { ...innerBlocksProps } />;
}
