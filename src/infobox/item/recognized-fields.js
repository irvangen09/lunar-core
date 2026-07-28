/**
 * Lokasi: lunar-core/src/infobox/item/recognized-fields.js
 *
 * Satu-satunya sumber kebenaran di sisi JS untuk daftar field yang
 * "dikenali" sistem field-sync Infobox — dipakai bersama oleh edit.js
 * (dropdown pemilihan field di Inspector) dan save.js (label yang
 * dirender ke markup tersimpan).
 *
 * PENTING — kamus ini juga punya salinan terpisah di sisi PHP:
 * includes/Content/class-meta-fields.php (const FIELDS). Menambah
 * field baru di sini TIDAK otomatis membuatnya tersinkron ke post
 * meta — const FIELDS di PHP juga wajib diperbarui secara manual
 * dengan slug yang sama persis. Ini keterbatasan yang disengaja
 * (save.js harus tetap berjalan sinkron/pure function tanpa REST
 * call), bukan sesuatu yang terlewat.
 */

import { __ } from '@wordpress/i18n';

export const RECOGNIZED_FIELDS = [
	{ value: 'peran', label: __( 'Peran', 'lunar-core' ) },
	{ value: 'tier_alat', label: __( 'Tier Alat', 'lunar-core' ) },
	{ value: 'musim', label: __( 'Musim', 'lunar-core' ) },
	{ value: 'waktu_muncul', label: __( 'Waktu Muncul', 'lunar-core' ) },
	{ value: 'jenis_hasil', label: __( 'Jenis Hasil', 'lunar-core' ) },
];

/**
 * Ambil label tampilan untuk satu slug field yang dikenali.
 *
 * @param {string} fieldSlug Slug field, mis. "tier_alat".
 * @return {string} Label tampilan, atau string kosong kalau slug tidak dikenali.
 */
export function getRecognizedLabel( fieldSlug ) {
	return RECOGNIZED_FIELDS.find( ( field ) => field.value === fieldSlug )?.label || '';
}