/**
 * Lokasi: lunar-core/src/toc/index.js
 * Entry point — mendaftarkan block TOC.
 */

import { registerBlockType } from '@wordpress/blocks';

import metadata from './block.json';
import Edit from './edit';
import save from './save';

import './style.scss';
import './editor.scss';

registerBlockType( metadata.name, {
	icon: 'text-page',
	edit: Edit,
	save,
} );
