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
	 * Catatan: object_subtype dikosongkan ('') sehingga berlaku untuk
	 * SEMUA post type untuk saat ini, karena CPT "Wiki Artikel" belum
	 * dibangun (masih di antrian Kelompok berikutnya). Setelah CPT ada,
	 * ini tinggal diganti jadi nama CPT-nya — perubahan satu baris,
	 * tidak memengaruhi struktur class ini.
	 */
	public function register_fields(): void {
		foreach ( self::FIELDS as $field ) {
			register_post_meta(
				'',
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
