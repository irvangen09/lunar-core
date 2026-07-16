/**
 * Lokasi: lunar-core/src/callout/index.js
 * Entry point — mendaftarkan block Callout ke Block Editor.
 */

import { registerBlockType } from '@wordpress/blocks';

import metadata from './block.json';
import Edit from './edit';
import save from './save';

import './style.scss';
import './editor.scss';

registerBlockType( metadata.name, {
	icon: 'megaphone',
	edit: Edit,
	save,
} );
