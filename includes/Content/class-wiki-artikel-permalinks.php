<?php
/**
 * Lokasi: lunar-core/includes/Content/class-wiki-artikel-permalinks.php
 *
 * Struktur permalink khusus Wiki Artikel: /wiki/{game}/{slug}/ apabila
 * artikel punya taxonomy Game, fallback ke /wiki/{slug}/ (bawaan CPT)
 * apabila tidak. Dibutuhkan karena banyak karakter/item lintas game
 * memakai nama yang sama (mis. "Popuri" di FoMT & BTN) — tanpa ini,
 * WordPress akan memaksa salah satunya menjadi "popuri-2" secara permanen.
 *
 * Ini merevisi keputusan lama (rewrite flat tanpa taxonomy, lihat catatan
 * di Post_Types::register()) — direvisi karena tabrakan nama sudah
 * terbukti nyata sejak awal pengujian, dan situs belum go-live sehingga
 * tidak ada biaya migrasi URL lama (PROJECT_BRIEF.md §14).
 *
 * @package Lunar\Content
 */

namespace Lunar\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Wiki_Artikel_Permalinks
 */
class Wiki_Artikel_Permalinks {

	/**
	 * Mendaftarkan seluruh hook WordPress yang dibutuhkan.
	 *
	 * Dua hook penyimpanan (save_post & rest_after_insert) sengaja
	 * didaftarkan berdua — lihat maybe_fix_slug() untuk alasannya.
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'add_rewrite_rule' ) );
		add_filter( 'post_type_link', array( $this, 'filter_permalink' ), 10, 2 );
		add_filter( 'wp_unique_post_slug', array( $this, 'filter_unique_slug' ), 10, 6 );
		add_action( 'save_post_' . Post_Types::get_slug(), array( $this, 'handle_save_post' ), 10, 2 );
		add_action( 'rest_after_insert_' . Post_Types::get_slug(), array( $this, 'handle_rest_after_insert' ) );
		add_action( 'template_redirect', array( $this, 'redirect_to_canonical' ) );
	}

	/**
	 * Rule tambahan untuk pola 2 segmen (wiki/{game}/{slug}/), diberi
	 * prioritas 'top' supaya dicoba SEBELUM rule 1-segmen bawaan yang
	 * otomatis dihasilkan dari registrasi CPT (itulah fallback tanpa
	 * game — tidak perlu disentuh sama sekali).
	 */
	public function add_rewrite_rule(): void {
		add_rewrite_rule(
			'^wiki/([^/]+)/([^/]+)/?$',
			'index.php?' . Taxonomies::get_slug_game() . '=$matches[1]&' . Post_Types::get_slug() . '=$matches[2]',
			'top'
		);
	}

	/**
	 * Membentuk permalink /wiki/{game}/{slug}/ untuk artikel yang punya
	 * term Game, atau membiarkan fallback bawaan /wiki/{slug}/ apabila
	 * tidak (mis. artikel yang belum ditandai Game sama sekali).
	 *
	 * @param string   $permalink Permalink yang sudah dibentuk WordPress.
	 * @param \WP_Post $post      Post yang sedang diproses.
	 * @return string
	 */
	public function filter_permalink( string $permalink, \WP_Post $post ): string {
		if ( Post_Types::get_slug() !== $post->post_type ) {
			return $permalink;
		}

		$game_term = $this->get_primary_game_term( $post->ID );

		if ( ! $game_term ) {
			return $permalink;
		}

		return home_url( 'wiki/' . $game_term->slug . '/' . $post->post_name . '/' );
	}

	/**
	 * Mengizinkan slug yang sama dipakai ulang di Game yang berbeda —
	 * WordPress secara default mengecek keunikan slug per post_type
	 * saja, tanpa peduli taxonomy. Filter ini mempersempit pengecekan
	 * itu supaya benar-benar per (post_type + Game).
	 *
	 * Catatan: untuk artikel BARU yang taxonomy Game-nya disimpan dalam
	 * request yang sama (umum terjadi lewat Gutenberg/REST), term itu
	 * belum tentu terbaca di titik ini — lihat maybe_fix_slug() sebagai
	 * jaring pengaman susulan untuk kasus tersebut.
	 *
	 * @param string $slug          Slug kandidat (mungkin sudah diberi akhiran angka oleh WordPress).
	 * @param int    $post_id       ID post yang sedang disimpan.
	 * @param string $post_status   Status post.
	 * @param string $post_type     Post type.
	 * @param int    $post_parent   ID induk (tidak dipakai CPT ini).
	 * @param string $original_slug Slug asli sebelum WordPress menambah akhiran angka.
	 * @return string
	 */
	public function filter_unique_slug( string $slug, int $post_id, string $post_status, string $post_type, int $post_parent, string $original_slug ): string {
		if ( Post_Types::get_slug() !== $post_type ) {
			return $slug;
		}

		if ( $this->has_scoped_collision( $original_slug, $post_id ) ) {
			return $slug;
		}

		return $original_slug;
	}

	/**
	 * Adapter untuk hook save_post_{post_type} (mencakup Classic Editor
	 * & Quick Edit — pada alur ini taxonomy Game sudah tersimpan saat
	 * hook ini berjalan).
	 *
	 * @param int      $post_id ID post.
	 * @param \WP_Post $post    Objek post.
	 */
	public function handle_save_post( int $post_id, \WP_Post $post ): void {
		$this->maybe_fix_slug( $post );
	}

	/**
	 * Adapter untuk hook rest_after_insert_{post_type} (mencakup Block
	 * Editor/REST — di alur ini taxonomy Game baru tersimpan SETELAH
	 * save_post_{post_type} selesai, jadi butuh hook terpisah ini yang
	 * dijamin berjalan belakangan).
	 *
	 * @param \WP_Post $post Objek post.
	 */
	public function handle_rest_after_insert( \WP_Post $post ): void {
		$this->maybe_fix_slug( $post );
	}

	/**
	 * Koreksi susulan: hanya menyentuh slug yang berakhiran angka buatan
	 * WordPress (pola "-2", "-3", dst) — supaya tidak pernah menimpa
	 * slug yang memang sengaja ditulis manual oleh pengelola situs. Kalau
	 * ternyata tabrakan itu cuma karena Game berbeda, slug dibetulkan
	 * diam-diam jadi versi bersihnya.
	 *
	 * @param \WP_Post $post Objek post yang baru disimpan.
	 */
	private function maybe_fix_slug( \WP_Post $post ): void {
		if ( Post_Types::get_slug() !== $post->post_type ) {
			return;
		}

		if ( wp_is_post_revision( $post->ID ) || wp_is_post_autosave( $post->ID ) ) {
			return;
		}

		if ( ! preg_match( '/^(.+)-(\d+)$/', $post->post_name, $match ) ) {
			return;
		}

		$base_slug = $match[1];

		if ( $this->has_scoped_collision( $base_slug, $post->ID ) ) {
			return;
		}

		remove_action( 'save_post_' . Post_Types::get_slug(), array( $this, 'handle_save_post' ), 10 );
		remove_action( 'rest_after_insert_' . Post_Types::get_slug(), array( $this, 'handle_rest_after_insert' ), 10 );

		wp_update_post(
			array(
				'ID'        => $post->ID,
				'post_name' => $base_slug,
			)
		);

		add_action( 'save_post_' . Post_Types::get_slug(), array( $this, 'handle_save_post' ), 10, 2 );
		add_action( 'rest_after_insert_' . Post_Types::get_slug(), array( $this, 'handle_rest_after_insert' ) );
	}

	/**
	 * Mengarahkan (301) permintaan yang segmen Game-nya di URL tidak
	 * cocok dengan Game asli artikel ke URL yang benar — mencegah URL
	 * "salah game" ikut terindeks sebagai konten duplikat. WordPress
	 * tidak menangani ini secara bawaan untuk struktur permalink
	 * berbasis taxonomy semacam ini (perilaku yang sama berlaku untuk
	 * %category% bawaan WordPress pada post biasa).
	 */
	public function redirect_to_canonical(): void {
		if ( ! is_singular( Post_Types::get_slug() ) ) {
			return;
		}

		$queried_post = get_queried_object();

		if ( ! $queried_post instanceof \WP_Post ) {
			return;
		}

		$canonical_url = get_permalink( $queried_post );

		if ( ! $canonical_url ) {
			return;
		}

		global $wp;

		$requested_path = untrailingslashit( (string) wp_parse_url( home_url( $wp->request ), PHP_URL_PATH ) );
		$canonical_path = untrailingslashit( (string) wp_parse_url( $canonical_url, PHP_URL_PATH ) );

		if ( $requested_path === $canonical_path ) {
			return;
		}

		wp_safe_redirect( $canonical_url, 301 );
		exit;
	}

	/**
	 * Cek apakah $slug (versi asli, tanpa akhiran angka) benar-benar
	 * bertabrakan dengan artikel LAIN yang berada di Game yang sama
	 * (atau sama-sama tanpa Game — keduanya tetap wajib unik satu sama
	 * lain karena sama-sama memakai fallback /wiki/{slug}/). Dipakai
	 * bersama oleh filter_unique_slug() dan maybe_fix_slug() supaya
	 * logikanya tidak dobel (DRY).
	 *
	 * @param string $slug             Slug asli yang ingin diperiksa.
	 * @param int    $excluded_post_id ID post yang sedang diproses (dikecualikan dari pengecekan).
	 * @return bool
	 */
	private function has_scoped_collision( string $slug, int $excluded_post_id ): bool {
		global $wpdb;

		$colliding_ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts}
				WHERE post_name = %s
				AND post_type = %s
				AND ID != %d
				AND post_status NOT IN ( 'trash', 'auto-draft' )",
				$slug,
				Post_Types::get_slug(),
				$excluded_post_id
			)
		);

		if ( empty( $colliding_ids ) ) {
			return false;
		}

		$current_game_id = $this->get_primary_game_term_id( $excluded_post_id );

		foreach ( $colliding_ids as $other_id ) {
			if ( $this->get_primary_game_term_id( (int) $other_id ) === $current_game_id ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Term Game utama sebuah post (term pertama yang ditemukan — sesuai
	 * kebijakan konten "1 Wiki Artikel = 1 Game", ini murni jaring
	 * pengaman untuk kasus tak terduga di mana lebih dari satu term
	 * ter-assign).
	 *
	 * @param int $post_id ID post.
	 * @return \WP_Term|null
	 */
	private function get_primary_game_term( int $post_id ): ?\WP_Term {
		$terms = get_the_terms( $post_id, Taxonomies::get_slug_game() );

		if ( ! is_array( $terms ) || empty( $terms ) ) {
			return null;
		}

		return $terms[0];
	}

	/**
	 * Versi ID-saja dari get_primary_game_term(), dipakai has_scoped_collision()
	 * untuk membandingkan dua post tanpa perlu memuat objek WP_Term penuh.
	 * Mengembalikan 0 (bukan null) untuk artikel tanpa Game, supaya dua
	 * artikel yang sama-sama tanpa Game tetap dianggap "kelompok yang sama"
	 * (harus tetap unik satu sama lain).
	 *
	 * @param int $post_id ID post.
	 * @return int
	 */
	private function get_primary_game_term_id( int $post_id ): int {
		$term = $this->get_primary_game_term( $post_id );

		return $term ? (int) $term->term_id : 0;
	}

	/**
	 * Dipanggil saat plugin diaktifkan (lihat lunar-core.php) — rule
	 * baru dari add_rewrite_rule() tidak akan terbaca WordPress sampai
	 * rewrite rules di-flush satu kali.
	 */
	public static function flush(): void {
		( new self() )->add_rewrite_rule();
		flush_rewrite_rules();
	}
}