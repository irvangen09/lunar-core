<?php
/**
 * Lokasi: lunar-core/includes/Content/class-field-terms-seeder.php
 *
 * Menyisipkan satu term awal ("Role") ke taxonomy Field (wiki_field)
 * begitu plugin ini aktif — mekanisme transisi dari kamus recognized-field
 * hardcode (Meta_Fields) menuju taxonomy native yang bebas dikelola
 * pengelola situs lewat wp-admin (Lunar_Core_Themes_Decoupling_Proposal.md §9).
 *
 * Sengaja BUKAN register_activation_hook(): situs ini bukan plugin
 * WordPress.org yang di-update lewat mekanisme updater bawaan (yang akan
 * memicu ulang activation hook) — pembaruan dilakukan manual lewat
 * penggantian file/deploy Git, sehingga activation hook hanya pernah
 * terpanggil satu kali di awal dan tidak bisa diandalkan untuk migrasi
 * data yang ditambahkan belakangan seperti ini. Sebagai gantinya, class
 * ini memeriksa sendiri (lewat satu opsi boolean) apakah proses seeding
 * sudah pernah berjalan, terlepas dari kapan situs terakhir kali
 * mengaktifkan/memperbarui plugin.
 *
 * @package Lunar\Content
 */

namespace Lunar\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Field_Terms_Seeder
 */
class Field_Terms_Seeder {

	/**
	 * Nama opsi penanda "seeding term awal sudah pernah dijalankan".
	 *
	 * Sengaja berupa flag boolean sederhana, BUKAN sistem pembanding versi
	 * plugin (mis. "lunar_core_db_version" vs LUNAR_CORE_VERSION) — proses
	 * ini hanya perlu berjalan tepat satu kali sepanjang umur situs (sisipkan
	 * satu term lalu selesai), jadi flag sederhana sudah cukup dan lebih
	 * mudah dipahami dibanding sistem versi yang sebenarnya tidak diperlukan
	 * untuk kasus ini (ENGINEERING_PRINCIPLES.md §13, Simplicity Wins).
	 */
	private const SEEDED_OPTION = 'lunar_core_field_terms_seeded';

	/**
	 * Term awal yang disisipkan — satu-satunya recognized-field lama yang
	 * dipertahankan sebagai contoh (Dokumen Perencanaan, keputusan migrasi
	 * §8: 4 field lain sengaja TIDAK di-seed, akan ditambahkan manual oleh
	 * pengelola lewat wp-admin sesuai kebutuhan nyata tiap franchise).
	 */
	private const INITIAL_TERM_NAME = 'Role';

	/**
	 * Mendaftarkan hook WordPress.
	 *
	 * Di-hook ke "admin_init" (bukan "init") — proses ini murni housekeeping
	 * data satu kali, tidak perlu ikut berjalan di setiap request frontend.
	 * Aman dijalankan setelah taxonomy terdaftar karena Taxonomies::init()
	 * sudah hook ke "init", yang selalu berjalan lebih dulu daripada
	 * "admin_init" pada request yang sama.
	 */
	public function init(): void {
		add_action( 'admin_init', array( $this, 'maybe_seed' ) );
	}

	/**
	 * Menyisipkan term awal apabila belum pernah dilakukan sebelumnya.
	 *
	 * Fail gracefully apabila taxonomy belum sempat terdaftar (mis. urutan
	 * hook yang tidak terduga) — tidak menandai opsi sebagai selesai supaya
	 * percobaan berikutnya (request admin_init selanjutnya) mencoba lagi.
	 */
	public function maybe_seed(): void {
		if ( get_option( self::SEEDED_OPTION ) ) {
			return;
		}

		if ( ! taxonomy_exists( Taxonomies::get_slug_field() ) ) {
			return;
		}

		$existing = term_exists( self::INITIAL_TERM_NAME, Taxonomies::get_slug_field() );

		if ( ! $existing ) {
			$result = wp_insert_term( self::INITIAL_TERM_NAME, Taxonomies::get_slug_field() );

			if ( is_wp_error( $result ) ) {
				return; // Fail gracefully -- coba lagi di request admin berikutnya.
			}
		}

		update_option( self::SEEDED_OPTION, true );
	}
}