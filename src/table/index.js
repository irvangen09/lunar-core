/**
 * Lokasi: lunar-core/src/table/index.js
 * Entry point — mendaftarkan block Table.
 */

import { registerBlockType } from '@wordpress/blocks';

import metadata from './block.json';
import Edit from './edit';
import save from './save';

import './style.scss';
import './editor.scss';

registerBlockType( metadata.name, {
	icon: 'editor-table',
	edit: Edit,
	save,
} );
