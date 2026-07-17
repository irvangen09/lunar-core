/**
 * Lokasi: lunar-core/src/gallery/index.js
 * Entry point — mendaftarkan block Gallery (parent). Gallery Item
 * didaftarkan terpisah lewat gallery-item/index.js sendiri
 * (pengecualian historis — block lain memakai satu index.js gabungan).
 */

import { registerBlockType } from '@wordpress/blocks';

import metadata from './block.json';
import Edit from './edit';
import save from './save';
import './style.scss';
import './editor.scss';

registerBlockType( metadata.name, {
	icon: 'format-gallery',
	edit: Edit,
	save,
} );
