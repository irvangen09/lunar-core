/**
 * Lokasi: lunar-core/src/definition-list/index.js
 * Entry point — mendaftarkan block induk (Definition List) SEKALIGUS
 * block anak (Definition Item) dalam satu file, karena keduanya
 * merupakan satu kesatuan block family yang tidak dipakai terpisah.
 */

import { registerBlockType } from '@wordpress/blocks';

import listMetadata from './block.json';
import ListEdit from './edit';
import listSave from './save';

import itemMetadata from './item/block.json';
import ItemEdit from './item/edit';
import itemSave from './item/save';

import './style.scss';
import './editor.scss';

registerBlockType( listMetadata.name, {
	icon: 'list-view',
	edit: ListEdit,
	save: listSave,
} );

registerBlockType( itemMetadata.name, {
	icon: 'list-view',
	edit: ItemEdit,
	save: itemSave,
} );
