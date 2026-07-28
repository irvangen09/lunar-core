<?php
/**
 * Lokasi: lunar-core/includes/Content/class-meta-fields.php
 *
 * Registrasi 5 post meta field yang di-sync dari Infobox Field
 * mode "Dikenali" (Dokumen-Perencanaan-LunarThemes.md §3.4).
 *
 * Sengaja dipisah dari class Meta_Sync — class ini HANYA bertanggung
 * jawab mendaftarkan field-nya (agar muncul di REST API, punya
 * sanitasi & auth yang benar); class Meta_Sync yang mengisi nilainya.
 *
 * @package Lunar\Content
 */

namespace Lunar\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Meta_Fields
 */
class Meta_Fields {

	/**
	 * Daftar key field yang dikenali sistem (Dokumen Perencanaan §3.4).
	 * Sengaja TIDAK termasuk "Game" — Game sudah punya taxonomy sendiri,
	 * lihat pembahasan sebelum Tahap Tugas Infobox dimulai.
	 *
	 * PENTING — kamus ini juga punya salinan terpisah di sisi JS:
	 * src/infobox/item/recognized-fields.js (RECOGNIZED_FIELDS).
	 * Menambah/mengubah slug di sini TIDAK otomatis tersinkron ke JS —
	 * slug di kedua tempat wajib diperbarui bersamaan secara manual.
	 * Ini keterbatasan yang disengaja (save.js Gutenberg harus tetap
	 * berjalan sinkron/pure function, tidak boleh memanggil REST API),
	 * bukan sesuatu yang terlewat.
	 */
	private const FIELDS = array( 'peran', 'tier_alat', 'musim', 'waktu_muncul', 'jenis_hasil' );

	/**
	 * Prefix meta key, mengikuti konvensi LunarCore (BLUEPRINT.md §12).
	 */
	private const META_PREFIX = 'lunar_core_';

	/**
	 * Mendaftarkan hook WordPress.
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'register_fields' ) );
	}

	/**
	 * Mendaftarkan seluruh meta field lewat register_post_meta().
	 *
	 * Catatan: object_subtype kini diikat ke CPT "Wiki Artikel"
	 * (Post_Types::get_slug()) — sebelumnya dikosongkan ('') karena CPT
	 * tersebut belum dibangun. Field ini sekarang hanya berlaku untuk
	 * Wiki Artikel, tidak lagi ke semua post type (lebih ketat & sesuai
	 * maksud aslinya, Dokumen Perencanaan §3.4).
	 */
	public function register_fields(): void {
		foreach ( self::FIELDS as $field ) {
			register_post_meta(
				Post_Types::get_slug(),
				self::META_PREFIX . $field,
				array(
					'type'              => 'string',
					'single'            => true,
					'show_in_rest'      => true,
					'sanitize_callback' => 'sanitize_text_field',
					'auth_callback'     => function () {
						return current_user_can( 'edit_posts' );
					},
				)
			);
		}
	}

	/**
	 * Daftar key field yang dikenali — dipakai Meta_Sync untuk validasi
	 * (mencegah nilai recognizedField sembarangan dari post_content
	 * dianggap valid tanpa pengecekan).
	 *
	 * @return string[]
	 */
	public static function get_recognized_fields(): array {
		return self::FIELDS;
	}

	/**
	 * Mengubah key field (mis. "musim") jadi nama meta key lengkap
	 * (mis. "lunar_core_musim"). Return null kalau key tidak dikenali.
	 *
	 * @param string $field Key field, salah satu dari self::FIELDS.
	 * @return string|null
	 */
	public static function get_meta_key( string $field ): ?string {
		return in_array( $field, self::FIELDS, true ) ? self::META_PREFIX . $field : null;
	}
}