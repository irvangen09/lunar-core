/**
 * Lokasi: lunar-core/src/gallery/gallery-item/index.js
 * Entry point terpisah untuk Gallery Item (pengecualian historis —
 * block child lain umumnya digabung ke index.js milik parent).
 */

import { registerBlockType } from '@wordpress/blocks';

import metadata from './block.json';
import Edit from './edit';
import save from './save';

registerBlockType( metadata.name, {
	icon: 'format-image',
	edit: Edit,
	save,
} );
