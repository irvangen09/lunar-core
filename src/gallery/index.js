/**
 * Lokasi: lunar-core/src/gallery/index.js
 * Entry point — mendaftarkan block induk (Gallery) sekaligus block
 * anak (Gallery Item), mengikuti pola yang sama seperti Accordion,
 * Definition List & Infobox. Sebelumnya Gallery Item didaftarkan
 * lewat index.js terpisah miliknya sendiri (pengecualian historis);
 * digabungkan di sini agar konsisten dengan 6 block parent-child lain.
 */

import { registerBlockType } from '@wordpress/blocks';

import galleryMetadata from './block.json';
import GalleryEdit from './edit';
import gallerySave from './save';

import itemMetadata from './gallery-item/block.json';
import ItemEdit from './gallery-item/edit';
import itemSave from './gallery-item/save';

import './style.scss';
import './editor.scss';

registerBlockType( galleryMetadata.name, {
	icon: 'format-gallery',
	edit: GalleryEdit,
	save: gallerySave,
} );

registerBlockType( itemMetadata.name, {
	icon: 'format-image',
	edit: ItemEdit,
	save: itemSave,
} );