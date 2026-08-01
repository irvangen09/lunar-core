<?php
/**
 * Lokasi: lunar-core/includes/Content/class-meta-fields.php
 *
 * Registrasi post meta field yang di-sync dari Infobox Field mode
 * "Dikenali" (Dokumen-Perencanaan-LunarThemes.md §3.4). Sejak refactor
 * decoupling (Lunar_Core_Themes_Decoupling_Proposal.md §9), daftar field
 * tidak lagi tetap berjumlah 5 -- diambil dinamis dari taxonomy Field
 * (wiki_field), sehingga bisa bertambah/berkurang kapan pun lewat wp-admin.
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
	 * Daftar field diambil dinamis dari taxonomy Field (lihat
	 * get_recognized_fields() di bawah) setiap kali "init" berjalan --
	 * bukan lagi array PHP statis. Konsekuensinya: field baru yang
	 * ditambahkan pengelola lewat wp-admin baru terdaftar sebagai post
	 * meta pada request berikutnya (bukan langsung saat term dibuat),
	 * karena register_post_meta() sendiri memang wajib dipanggil ulang
	 * tiap request lewat hook "init" -- ini konsekuensi normal WordPress
	 * Meta API, bukan keterbatasan khusus pendekatan ini.
	 *
	 * Catatan: object_subtype tetap diikat ke CPT "Wiki Artikel"
	 * (Post_Types::get_slug()), tidak berubah dari implementasi sebelumnya.
	 */
	public function register_fields(): void {
		foreach ( self::get_recognized_fields() as $field ) {
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
	 * Daftar slug field yang dikenali sistem -- dipakai Meta_Sync untuk
	 * validasi dan oleh public-api.php (lunar_core_get_recognized_fields())
	 * untuk dikonsumsi LunarThemes (filter pencarian, dst).
	 *
	 * Sejak refactor decoupling (Lunar_Core_Themes_Decoupling_Proposal.md §9),
	 * daftar ini dibaca dari taxonomy Field (wiki_field), BUKAN lagi array
	 * PHP statis -- pengelola situs bisa menambah/mengubah/menghapus field
	 * kapan pun lewat wp-admin tanpa perlu deploy kode baru.
	 *
	 * PENTING: signature dan bentuk return (array of string) SENGAJA tidak
	 * berubah dari implementasi sebelumnya -- ini kontrak publik yang sudah
	 * dikonsumsi LunarThemes (search-filters.php) berbasis slug string,
	 * bukan Term ID. Lihat get_slug_by_term_id() di bawah untuk resolusi
	 * Term ID, yang merupakan kebutuhan internal terpisah (block Infobox
	 * Field), bukan bagian dari kontrak ini.
	 *
	 * @return string[]
	 */
	public static function get_recognized_fields(): array {
		$terms = get_terms(
			array(
				'taxonomy'   => Taxonomies::get_slug_field(),
				'hide_empty' => false,
			)
		);

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return array(); // Fail gracefully -- taxonomy belum terdaftar/kosong.
		}

		return wp_list_pluck( $terms, 'slug' );
	}

	/**
	 * Mengubah slug field (mis. "role") jadi nama meta key lengkap
	 * (mis. "lunar_core_role"). Return null kalau slug tidak dikenali
	 * (bukan/tidak lagi term valid di taxonomy Field).
	 *
	 * @param string $field Slug field, salah satu dari get_recognized_fields().
	 * @return string|null
	 */
	public static function get_meta_key( string $field ): ?string {
		return in_array( $field, self::get_recognized_fields(), true ) ? self::META_PREFIX . $field : null;
	}

	/**
	 * Label tampilan asli untuk sebuah slug field -- diambil dari nama
	 * term taxonomy Field (persis seperti yang diketik pengelola di
	 * wp-admin), BUKAN kamus statis terpisah.
	 *
	 * Method ini sengaja dibangun supaya LunarThemes tidak perlu menyimpan
	 * kamus labelnya sendiri (yang akan mengulang persis masalah yang baru
	 * saja diselesaikan di sisi LunarCore, Lunar_Core_Themes_Decoupling_Proposal.md
	 * §9 -- kamus statis yang gampang lupa disinkronkan begitu field baru
	 * ditambahkan). Sejak fungsi ini ada, LunarThemes cukup memanggilnya,
	 * tidak perlu mendefinisikan label field apa pun sendiri.
	 *
	 * @param string $field Slug field, salah satu dari get_recognized_fields().
	 * @return string Nama term asli, atau versi title-case dari slug kalau
	 *                term tidak ditemukan (fail gracefully -- field yang
	 *                sudah dihapus/tidak dikenali tetap dapat label yang wajar).
	 */
	public static function get_label( string $field ): string {
		$term = get_term_by( 'slug', $field, Taxonomies::get_slug_field() );

		if ( $term instanceof \WP_Term ) {
			return $term->name;
		}

		return ucwords( str_replace( array( '_', '-' ), ' ', $field ) );
	}

	/**
	 * Resolusi Term ID (nilai attribute "recognizedField" di block Infobox
	 * Field sejak refactor decoupling §9) menjadi slug field.
	 *
	 * Method ini SENGAJA hanya dipakai secara internal oleh Meta_Sync --
	 * TIDAK diekspos lewat public-api.php. Kontrak publik untuk LunarThemes
	 * (get_recognized_fields()/get_meta_key() di atas) tetap sepenuhnya
	 * berbasis slug string, tidak pernah perlu tahu soal Term ID -- Term ID
	 * murni detail implementasi block Infobox Field, konsisten dengan
	 * BLUEPRINT.md §14 (Theme tidak bergantung pada implementasi internal
	 * plugin).
	 *
	 * @param int $term_id Term ID dari taxonomy Field.
	 * @return string|null Slug, atau null kalau term tidak ditemukan/sudah dihapus.
	 */
	public static function get_slug_by_term_id( int $term_id ): ?string {
		if ( $term_id <= 0 ) {
			return null;
		}

		$term = get_term( $term_id, Taxonomies::get_slug_field() );

		if ( ! ( $term instanceof \WP_Term ) ) {
			return null; // Term tidak valid/sudah dihapus -- fail gracefully.
		}

		return $term->slug;
	}
}