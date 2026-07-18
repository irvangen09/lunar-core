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
	 */
	private const SLUG_TIPE_KONTEN = 'tipe_konten';

	/**
	 * Mendaftarkan hook WordPress.
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Mendaftarkan kedua taxonomy.
	 */
	public function register(): void {
		$this->register_game();
		$this->register_tipe_konten();
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
	private function register_tipe_konten(): void {
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
			self::SLUG_TIPE_KONTEN,
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
					'slug'       => 'tipe-konten',
					'with_front' => false,
				),
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
	public static function get_slug_tipe_konten(): string {
		return self::SLUG_TIPE_KONTEN;
	}
}
