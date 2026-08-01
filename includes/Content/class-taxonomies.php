<?php
/**
 * Lokasi: lunar-core/includes/Content/class-taxonomies.php
 *
 * Registrasi Taxonomy "Game" (hierarkis: Franchise > Judul Spesifik) dan
 * "Tipe Konten" — fondasi Information Architecture situs (PROJECT_BRIEF.md §7,
 * ARCHITECTURE.md §5). Keduanya dikaitkan ke CPT Wiki Artikel dari sini
 * (bukan dari class Post_Types) — lihat class-post-types.php untuk alasannya.
 *
 * @package Lunar\Content
 */

namespace Lunar\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Taxonomies
 */
class Taxonomies {

	/**
	 * Slug taxonomy Game. Dipakai class lain (mis. term meta menu-per-game
	 * yang akan menyusul) supaya tidak ada string literal berulang.
	 */
	private const SLUG_GAME = 'game';

	/**
	 * Slug taxonomy Tipe Konten.
	 *
	 * Catatan migrasi: slug ini sebelumnya "tipe_konten" (Bahasa
	 * Indonesia). Diganti ke Bahasa Inggris sebagai bagian dari refactor
	 * decoupling LunarCore <-> LunarThemes (Lunar_Core_Themes_Decoupling_Proposal.md
	 * §8). Rewrite slug URL publik SUDAH ikut diubah ke Bahasa Inggris
	 * ("content-type", lihat register_content_type()) sesuai permintaan
	 * lanjutan Product Owner supaya seluruh slug publik konsisten
	 * berbahasa universal — bukan lagi pengecualian seperti draf §8 awal.
	 */
	private const SLUG_CONTENT_TYPE = 'content_type';

	/**
	 * Slug taxonomy Field (dahulu kamus recognized-field hardcode di
	 * Meta_Fields — lihat Lunar_Core_Themes_Decoupling_Proposal.md §9).
	 *
	 * Sengaja TIDAK public/publicly_queryable — taxonomy ini murni untuk
	 * kebutuhan internal (sumber data dropdown Inspector Infobox Field +
	 * kamus valid untuk Meta_Sync), tidak pernah dimaksudkan punya
	 * halaman archive sendiri. Mendaftarkannya sebagai public akan
	 * menghasilkan URL archive kosong secara SEO (mis. /wiki_field/role/)
	 * tanpa manfaat apa pun bagi pembaca — risiko thin/duplicate content
	 * yang sengaja dihindari sejak tahap Konsep.
	 *
	 * PENTING (ditemukan lewat pengujian staging): register_field() TIDAK
	 * mengikat taxonomy ini ke CPT manapun (object_type dikosongkan) --
	 * mengikatnya ke Wiki Artikel membuat Gutenberg otomatis merender
	 * panel taxonomy generik di sidebar Block Editor (terpisah dari
	 * dropdown Inspector milik block Infobox Field sendiri), yang
	 * membuka celah pengelola tidak sengaja membuat & meng-assign term
	 * langsung dari panel itu (bukan lewat block Infobox Field yang
	 * dimaksud). Dropdown Inspector tetap berfungsi normal tanpa binding
	 * ini karena ia fetch lewat endpoint term (/wp/v2/wiki_field)
	 * langsung, tidak bergantung pada relasi taxonomy<->CPT.
	 */
	private const SLUG_FIELD = 'wiki_field';

	/**
	 * Mendaftarkan hook WordPress.
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Mendaftarkan seluruh taxonomy.
	 */
	public function register(): void {
		$this->register_game();
		$this->register_content_type();
		$this->register_field();
	}

	/**
	 * Taxonomy "Game" — hierarkis (Franchise > Judul Spesifik).
	 *
	 * Catatan keputusan:
	 * - Slug tiap term (mis. "sos", "fomt") diisi MANUAL oleh pengelola lewat
	 *   field Slug bawaan WordPress saat membuat/edit term — bukan hardcode
	 *   di kode, supaya fleksibel menampung judul game baru kapan pun tanpa
	 *   perubahan kode (ARCHITECTURE.md §17, Extensibility).
	 * - rewrite['hierarchical'] => true membuat URL otomatis mengikuti
	 *   struktur parent-child term, mis. /game/sos/fomt/.
	 * - Term meta untuk menu-per-judul-game (Dokumen Perencanaan sesi 5)
	 *   akan didaftarkan di class terpisah menyusul — TIDAK di sini, supaya
	 *   class ini tetap murni definisi struktur taxonomy.
	 */
	private function register_game(): void {
		$labels = array(
			'name'          => __( 'Game', 'lunar-core' ),
			'singular_name' => __( 'Game', 'lunar-core' ),
			'search_items'  => __( 'Cari Game', 'lunar-core' ),
			'all_items'     => __( 'Semua Game', 'lunar-core' ),
			'parent_item'   => __( 'Franchise Induk', 'lunar-core' ),
			'edit_item'     => __( 'Edit Game', 'lunar-core' ),
			'add_new_item'  => __( 'Tambah Game Baru', 'lunar-core' ),
			'menu_name'     => __( 'Game', 'lunar-core' ),
		);

		register_taxonomy(
			self::SLUG_GAME,
			array( Post_Types::get_slug() ),
			array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'query_var'         => true,
				'rewrite'           => array(
					'slug'         => 'game',
					'hierarchical' => true,
					'with_front'   => false,
				),
			)
		);
	}

	/**
	 * Taxonomy "Tipe Konten" (mis. Karakter, Item, Lokasi).
	 *
	 * Sengaja hierarkis (bukan tag-style free-tagging) — pengelola memilih
	 * dari daftar tetap lewat checkbox, mencegah typo/duplikat term yang
	 * rawan terjadi pada tag-style meta box. Tidak ada rencana sub-level
	 * (parent/child) untuk taxonomy ini, hierarchical => true murni untuk
	 * UI-nya saja.
	 */
	private function register_content_type(): void {
		$labels = array(
			'name'          => __( 'Tipe Konten', 'lunar-core' ),
			'singular_name' => __( 'Tipe Konten', 'lunar-core' ),
			'search_items'  => __( 'Cari Tipe Konten', 'lunar-core' ),
			'all_items'     => __( 'Semua Tipe Konten', 'lunar-core' ),
			'edit_item'     => __( 'Edit Tipe Konten', 'lunar-core' ),
			'add_new_item'  => __( 'Tambah Tipe Konten Baru', 'lunar-core' ),
			'menu_name'     => __( 'Tipe Konten', 'lunar-core' ),
		);

		register_taxonomy(
			self::SLUG_CONTENT_TYPE,
			array( Post_Types::get_slug() ),
			array(
				'labels'            => $labels,
				'hierarchical'      => true,
				'public'            => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'query_var'         => true,
				'rewrite'           => array(
					'slug'       => 'content-type',
					'with_front' => false,
				),
			)
		);
	}

	/**
	 * Taxonomy "Field" (dahulu recognized-field hardcode, mis. "Peran",
	 * "Tier Alat") — dikelola bebas oleh pengelola situs lewat wp-admin,
	 * tanpa perlu deploy kode untuk menambah/mengubah/menghapus field
	 * (Lunar_Core_Themes_Decoupling_Proposal.md §9).
	 *
	 * Catatan keputusan:
	 * - non-hierarchical (flat, seperti Tag) — field-field ini tidak
	 *   punya relasi parent-child satu sama lain, berbeda dari taxonomy
	 *   Game yang memang butuh struktur Franchise > Judul Spesifik.
	 * - public => false & publicly_queryable => false — lihat penjelasan
	 *   di komentar SLUG_FIELD di atas.
	 * - show_in_rest => true tetap wajib meski tidak public — ini yang
	 *   memungkinkan dropdown Inspector block Infobox Field mengambil
	 *   daftar term terkini lewat @wordpress/core-data, pola yang sama
	 *   seperti dropdown Category/Tag bawaan Gutenberg.
	 * - Term awal ("Role") di-seed otomatis oleh Field_Terms_Seeder saat
	 *   plugin diaktifkan/diperbarui — lihat class-field-terms-seeder.php
	 *   (Tugas 4.3), TIDAK didaftarkan manual di sini supaya class ini
	 *   tetap murni definisi struktur taxonomy (konsisten dengan alasan
	 *   term meta menu-per-game dipisah dari register_game()).
	 */
	private function register_field(): void {
		$labels = array(
			'name'          => __( 'Field', 'lunar-core' ),
			'singular_name' => __( 'Field', 'lunar-core' ),
			'search_items'  => __( 'Cari Field', 'lunar-core' ),
			'all_items'     => __( 'Semua Field', 'lunar-core' ),
			'edit_item'     => __( 'Edit Field', 'lunar-core' ),
			'add_new_item'  => __( 'Tambah Field Baru', 'lunar-core' ),
			'menu_name'     => __( 'Field', 'lunar-core' ),
		);

		register_taxonomy(
			self::SLUG_FIELD,
			array(), // Sengaja TIDAK diikat ke CPT manapun -- lihat catatan di atas class SLUG_FIELD.
			array(
				'labels'             => $labels,
				'hierarchical'       => false,
				'public'             => false,
				'publicly_queryable' => false,
				'show_ui'            => true,
				'show_admin_column'  => false,
				'show_in_rest'       => true,
				'query_var'          => false,
			)
		);
	}

	/**
	 * Slug taxonomy Game — dipakai class lain (term meta menu-per-game, dst).
	 *
	 * @return string
	 */
	public static function get_slug_game(): string {
		return self::SLUG_GAME;
	}

	/**
	 * Slug taxonomy Tipe Konten — dipakai class lain (filter pill dinamis
	 * di Archive per Game, dst).
	 *
	 * @return string
	 */
	public static function get_slug_content_type(): string {
		return self::SLUG_CONTENT_TYPE;
	}

	/**
	 * Slug taxonomy Field — dipakai Meta_Fields untuk membaca daftar
	 * recognized-field terkini (Tugas 4.4) dan Meta_Sync untuk resolusi
	 * Term ID -> slug saat menyimpan artikel (Tugas 4.5).
	 *
	 * @return string
	 */
	public static function get_slug_field(): string {
		return self::SLUG_FIELD;
	}
}