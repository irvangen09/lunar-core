<?php
/**
 * Lokasi: lunar-core/includes/Content/class-game-menu-meta.php
 *
 * Menyimpan relasi "Judul Game -> Menu WordPress" sebagai term meta pada
 * taxonomy Game (Dokumen Perencanaan sesi 4-5) — menggantikan pendekatan
 * hardcode name-matching di situs lama (lihat menu_changes.php sebagai
 * referensi apa yang dihindari).
 *
 * Class ini HANYA menangani sisi data: UI dropdown di layar edit term,
 * penyimpanan term meta, dan menandai taxonomy translatable ke Polylang.
 * Membaca term meta ini untuk benar-benar menukar menu yang tampil adalah
 * presentation logic — dikerjakan Theme lewat filter wp_nav_menu_args,
 * BUKAN di sini (Separation of Concerns, ENGINEERING_PRINCIPLES.md §3).
 *
 * @package Lunar\Content
 */

namespace Lunar\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Game_Menu_Meta
 */
class Game_Menu_Meta {

	/**
	 * Key term meta penyimpan ID menu WordPress terpilih.
	 */
	private const META_KEY = 'lunar_core_secondary_menu_id';

	/**
	 * Action & field nonce untuk keamanan form (CODING_STANDARD.md §14).
	 */
	private const NONCE_ACTION = 'lunar_core_game_menu_meta_action';
	private const NONCE_FIELD  = 'lunar_core_game_menu_meta_nonce';

	/**
	 * Mendaftarkan hook WordPress. Nama hook dinamis dibangun dari
	 * Taxonomies::get_slug_game() supaya tidak ada string literal
	 * "game" berulang di file ini.
	 */
	public function init(): void {
		$taxonomy = Taxonomies::get_slug_game();

		add_action( "{$taxonomy}_add_form_fields", array( $this, 'render_add_field' ) );
		add_action( "{$taxonomy}_edit_form_fields", array( $this, 'render_edit_field' ) );
		add_action( "created_{$taxonomy}", array( $this, 'save_meta' ) );
		add_action( "edited_{$taxonomy}", array( $this, 'save_meta' ) );
		add_filter( 'pll_get_taxonomies', array( $this, 'register_translatable' ) );
	}

	/**
	 * Field di layar "Tambah Game Baru".
	 *
	 * Ditampilkan untuk semua term baru (termasuk Franchise) karena saat
	 * membuat term, WordPress belum memuat ulang halaman setelah Parent
	 * dipilih — tidak bisa deteksi level Judul Spesifik vs Franchise
	 * tanpa JavaScript tambahan. Label menjelaskan agar dikosongkan untuk
	 * Franchise; pengelola cukup satu orang jadi risikonya kecil.
	 */
	public function render_add_field(): void {
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );
		?>
		<div class="form-field">
			<label for="lunar-core-secondary-menu"><?php esc_html_e( 'Menu Sekunder', 'lunar-core' ); ?></label>
			<?php $this->render_dropdown( 0 ); ?>
			<p>
				<?php esc_html_e( 'Menu navigasi yang ditampilkan Theme khusus untuk artikel dalam judul game ini. Kosongkan untuk Franchise (parent), atau kalau ingin memakai menu default.', 'lunar-core' ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Field di layar "Edit Game" — hanya muncul di level Judul Spesifik
	 * (term dengan parent terisi), bukan Franchise, karena menu sekunder
	 * memang per-judul-game (Dokumen Perencanaan §3.3).
	 *
	 * @param \WP_Term $term Term yang sedang diedit.
	 */
	public function render_edit_field( \WP_Term $term ): void {
		if ( 0 === (int) $term->parent ) {
			return;
		}

		$selected = (int) get_term_meta( $term->term_id, self::META_KEY, true );
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="lunar-core-secondary-menu"><?php esc_html_e( 'Menu Sekunder', 'lunar-core' ); ?></label>
			</th>
			<td>
				<?php
				wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );
				$this->render_dropdown( $selected );
				?>
				<p class="description">
					<?php esc_html_e( 'Menu navigasi yang ditampilkan Theme khusus untuk artikel dalam judul game ini. Kosongkan untuk memakai menu default.', 'lunar-core' ); ?>
				</p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Dropdown berisi seluruh menu WordPress yang sudah dibuat — pengelola
	 * memilih dari daftar, bukan mengetik nama manual (menghindari masalah
	 * fatal di pendekatan lama: rapuh terhadap rename/typo).
	 *
	 * @param int $selected ID menu yang sedang tersimpan, 0 jika belum ada.
	 */
	private function render_dropdown( int $selected ): void {
		$menus = wp_get_nav_menus();
		?>
		<select name="lunar-core-secondary-menu" id="lunar-core-secondary-menu">
			<option value="0"><?php esc_html_e( '— Gunakan menu default —', 'lunar-core' ); ?></option>
			<?php foreach ( $menus as $menu ) : ?>
				<option value="<?php echo esc_attr( $menu->term_id ); ?>" <?php selected( $selected, $menu->term_id ); ?>>
					<?php echo esc_html( $menu->name ); ?>
				</option>
			<?php endforeach; ?>
		</select>
		<?php
	}

	/**
	 * Menyimpan pilihan dropdown sebagai term meta.
	 *
	 * @param int $term_id ID term yang baru dibuat/diedit.
	 */
	public function save_meta( int $term_id ): void {
		if ( ! isset( $_POST[ self::NONCE_FIELD ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ), self::NONCE_ACTION )
		) {
			return;
		}

		if ( ! current_user_can( 'manage_categories' ) ) {
			return;
		}

		if ( ! isset( $_POST['lunar-core-secondary-menu'] ) ) {
			return;
		}

		$menu_id = absint( $_POST['lunar-core-secondary-menu'] );

		if ( $menu_id > 0 ) {
			update_term_meta( $term_id, self::META_KEY, $menu_id );
		} else {
			delete_term_meta( $term_id, self::META_KEY );
		}
	}

	/**
	 * Menandai taxonomy Game & Tipe Konten sebagai translatable ke Polylang.
	 * Konsekuensinya: tiap versi bahasa term jadi punya slotnya sendiri
	 * untuk field ini (term ID berbeda per bahasa), otomatis kompatibel
	 * tanpa perubahan lain (lihat kesimpulan sesi 4 soal Polylang).
	 *
	 * Dibungkus function_exists secara implisit oleh WordPress sendiri —
	 * filter ini hanya dipanggil Polylang kalau plugin tersebut aktif,
	 * jadi aman didaftarkan meski Polylang tidak terpasang.
	 *
	 * @param string[] $taxonomies Daftar slug taxonomy yang sudah translatable.
	 * @return string[]
	 */
	public function register_translatable( array $taxonomies ): array {
		$taxonomies[] = Taxonomies::get_slug_game();
		$taxonomies[] = Taxonomies::get_slug_tipe_konten();

		return array_unique( $taxonomies );
	}

	/**
	 * Key term meta — dipakai Theme (get_term_meta) untuk membaca menu
	 * terpilih saat merender wp_nav_menu_args.
	 *
	 * @return string
	 */
	public static function get_meta_key(): string {
		return self::META_KEY;
	}
}
