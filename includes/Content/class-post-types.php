<?php
/**
 * Lokasi: lunar-core/includes/Content/class-post-types.php
 *
 * Registrasi Custom Post Type "Wiki Artikel" — fondasi struktur konten
 * utama situs (PROJECT_BRIEF.md §7, ARCHITECTURE.md §5).
 *
 * Sengaja dipisah dari class Taxonomies (lihat class-taxonomies.php) —
 * relasi CPT ke taxonomy didaftarkan dari sisi taxonomy (object_type),
 * bukan di sini, supaya class ini tidak perlu tahu apapun soal taxonomy
 * yang akan menempel padanya (Separation of Concerns, ENGINEERING_PRINCIPLES.md §3).
 *
 * @package Lunar\Content
 */

namespace Lunar\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Post_Types
 */
class Post_Types {

	/**
	 * Slug CPT. Dipakai class lain (mis. Taxonomies, Meta_Fields)
	 * lewat get_slug() supaya tidak ada string literal berulang.
	 */
	private const SLUG = 'wiki_artikel';

	/**
	 * Mendaftarkan hook WordPress.
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'register' ) );
	}

	/**
	 * Mendaftarkan CPT "Wiki Artikel".
	 *
	 * Catatan keputusan:
	 * - rewrite flat "/wiki/%postname%/" (bukan menyisipkan taxonomy ke URL
	 *   artikel) — breadcrumb 5 level tetap ditampilkan Theme secara terpisah,
	 *   tanpa bergantung pada struktur permalink (Simplicity Wins, ENGINEERING_PRINCIPLES.md §13).
	 * - 'revisions' sengaja TIDAK didukung — situs tidak memakai revision
	 *   history WordPress core (Dokumen Perencanaan §3.1), tanggal "Terakhir
	 *   diperbarui" cukup diambil Theme dari post_modified secara native.
	 * - 'excerpt' didukung — dipakai Theme sebagai tagline di Single Post
	 *   (lihat mockup single-post-lunarthemes.html).
	 * - has_archive => false — tidak ada halaman archive generik CPT yang
	 *   direncanakan, hanya Archive per Game lewat taxonomy (Dokumen Perencanaan §3.2).
	 */
	public function register(): void {
		$labels = array(
			'name'                  => __( 'Wiki Artikel', 'lunar-core' ),
			'singular_name'         => __( 'Wiki Artikel', 'lunar-core' ),
			'add_new'               => __( 'Tambah Wiki Artikel', 'lunar-core' ),
			'add_new_item'          => __( 'Tambah Wiki Artikel Baru', 'lunar-core' ),
			'edit_item'             => __( 'Edit Wiki Artikel', 'lunar-core' ),
			'new_item'              => __( 'Wiki Artikel Baru', 'lunar-core' ),
			'view_item'             => __( 'Lihat Wiki Artikel', 'lunar-core' ),
			'view_items'            => __( 'Lihat Wiki Artikel', 'lunar-core' ),
			'search_items'          => __( 'Cari Wiki Artikel', 'lunar-core' ),
			'not_found'             => __( 'Wiki Artikel tidak ditemukan.', 'lunar-core' ),
			'not_found_in_trash'    => __( 'Wiki Artikel tidak ditemukan di Sampah.', 'lunar-core' ),
			'all_items'             => __( 'Semua Wiki Artikel', 'lunar-core' ),
			'archives'              => __( 'Arsip Wiki Artikel', 'lunar-core' ),
			'menu_name'             => __( 'Wiki Artikel', 'lunar-core' ),
		);

		register_post_type(
			self::SLUG,
			array(
				'labels'             => $labels,
				'public'             => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'show_in_rest'       => true,
				'menu_icon'          => 'dashicons-book-alt',
				'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
				'has_archive'        => false,
				'query_var'          => true,
				'capability_type'    => 'post',
				'rewrite'            => array(
					'slug'       => 'wiki',
					'with_front' => false,
				),
			)
		);
	}

	/**
	 * Slug CPT — dipakai class lain agar tidak ada string literal berulang
	 * (mis. Taxonomies::register() saat mengaitkan object_type).
	 *
	 * @return string
	 */
	public static function get_slug(): string {
		return self::SLUG;
	}
}
