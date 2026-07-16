/**
 * Lokasi: lunar-core/src/infobox/index.js
 * Entry point — mendaftarkan block induk (Infobox) sekaligus
 * block anak (Infobox Field), mengikuti pola yang sama seperti
 * Definition List (satu file untuk satu block family).
 */

import { registerBlockType } from '@wordpress/blocks';

import boxMetadata from './block.json';
import BoxEdit from './edit';
import boxSave from './save';

import fieldMetadata from './item/block.json';
import FieldEdit from './item/edit';
import fieldSave from './item/save';

import './style.scss';
import './editor.scss';

registerBlockType( boxMetadata.name, {
	icon: 'id-alt',
	edit: BoxEdit,
	save: boxSave,
} );

registerBlockType( fieldMetadata.name, {
	icon: 'id-alt',
	edit: FieldEdit,
	save: fieldSave,
} );
