<?php
/**
 * Lokasi: lunar-core/includes/Content/class-game-tile-meta.php
 *
 * Menyimpan 2 field opsional pada term meta taxonomy Game (level Judul
 * Spesifik saja, sama seperti Menu Sekunder di Game_Menu_Meta): URL
 * tujuan kustom untuk Game Tile di Homepage, dan gambar kustom (lewat
 * Media Library) untuk menggantikan placeholder inisial default.
 *
 * Keduanya sepenuhnya opsional — kalau kosong, Theme tetap memakai
 * mekanisme default yang sudah ada (get_term_link() untuk URL, kotak
 * inisial untuk gambar). Class ini HANYA menangani sisi data (UI form
 * di layar edit term + penyimpanan term meta); membaca meta ini untuk
 * benar-benar merender Game Tile adalah presentation logic, dikerjakan
 * Theme (Separation of Concerns, ENGINEERING_PRINCIPLES.md §3).
 *
 * @package Lunar\Content
 */

namespace Lunar\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Game_Tile_Meta
 */
class Game_Tile_Meta {

	/**
	 * Key term meta penyimpan URL tujuan kustom.
	 */
	private const URL_META_KEY = 'lunar_core_game_tile_url';

	/**
	 * Key term meta penyimpan ID attachment gambar kustom.
	 */
	private const IMAGE_META_KEY = 'lunar_core_game_tile_image_id';

	/**
	 * Action & field nonce untuk keamanan form (CODING_STANDARD.md §14).
	 */
	private const NONCE_ACTION = 'lunar_core_game_tile_meta_action';
	private const NONCE_FIELD  = 'lunar_core_game_tile_meta_nonce';

	/**
	 * Mendaftarkan hook WordPress.
	 *
	 * Berbeda dari Game_Menu_Meta, field ini TIDAK ditampilkan di layar
	 * "Tambah Game Baru" — keduanya murni pemanis opsional yang wajar
	 * diisi belakangan setelah term dibuat, jadi layar tambah term tetap
	 * sederhana (ENGINEERING_PRINCIPLES.md §13, Simplicity Wins).
	 */
	public function init(): void {
		$taxonomy = Taxonomies::get_slug_game();

		add_action( "{$taxonomy}_edit_form_fields", array( $this, 'render_edit_fields' ) );
		add_action( "edited_{$taxonomy}", array( $this, 'save_meta' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	/**
	 * Field di layar "Edit Game" — hanya muncul di level Judul Spesifik
	 * (term dengan parent terisi), konsisten dengan Menu Sekunder,
	 * karena Franchise (parent) tidak pernah tampil sebagai Game Tile
	 * (lihat lunar_get_game_terms() di Theme).
	 *
	 * @param \WP_Term $term Term yang sedang diedit.
	 */
	public function render_edit_fields( \WP_Term $term ): void {
		if ( 0 === (int) $term->parent ) {
			return;
		}

		$url      = (string) get_term_meta( $term->term_id, self::URL_META_KEY, true );
		$image_id = (int) get_term_meta( $term->term_id, self::IMAGE_META_KEY, true );
		$image_src = $image_id > 0 ? wp_get_attachment_image_url( $image_id, 'medium' ) : '';

		wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );
		?>
		<tr class="form-field">
			<th scope="row">
				<label for="lunar-core-game-tile-url"><?php esc_html_e( 'URL Tujuan Kustom', 'lunar-core' ); ?></label>
			</th>
			<td>
				<input type="url" class="regular-text" id="lunar-core-game-tile-url" name="lunar-core-game-tile-url" value="<?php echo esc_attr( $url ); ?>">
				<p class="description">
					<?php esc_html_e( 'Kosongkan untuk memakai tautan arsip game default. Isi untuk mengarahkan Game Tile ke halaman lain, mis. Artikel Pilar.', 'lunar-core' ); ?>
				</p>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row">
				<label for="lunar-core-game-tile-select"><?php esc_html_e( 'Media Game Tile', 'lunar-core' ); ?></label>
			</th>
			<td>
				<div class="lunar-core-game-tile-media">
					<img
						id="lunar-core-game-tile-preview"
						src="<?php echo esc_url( $image_src ); ?>"
						style="max-width:120px;max-height:120px;display:<?php echo $image_src ? 'block' : 'none'; ?>;margin-bottom:8px;"
						alt=""
					>
					<input type="hidden" id="lunar-core-game-tile-image-id" name="lunar-core-game-tile-image-id" value="<?php echo esc_attr( $image_id ); ?>">
					<p>
						<button type="button" class="button" id="lunar-core-game-tile-select">
							<?php esc_html_e( 'Pilih Gambar', 'lunar-core' ); ?>
						</button>
						<button type="button" class="button" id="lunar-core-game-tile-remove" style="<?php echo $image_id ? '' : 'display:none;'; ?>">
							<?php esc_html_e( 'Hapus Gambar', 'lunar-core' ); ?>
						</button>
					</p>
				</div>
				<p class="description">
					<?php esc_html_e( 'Kosongkan untuk memakai kotak inisial default. Cocok diisi logo, ikon, atau cover game.', 'lunar-core' ); ?>
				</p>
			</td>
		</tr>
		<?php
	}

	/**
	 * Menyimpan kedua field sebagai term meta.
	 *
	 * @param int $term_id ID term yang diedit.
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

		if ( isset( $_POST['lunar-core-game-tile-url'] ) ) {
			$url = esc_url_raw( wp_unslash( $_POST['lunar-core-game-tile-url'] ) );

			if ( '' !== $url ) {
				update_term_meta( $term_id, self::URL_META_KEY, $url );
			} else {
				delete_term_meta( $term_id, self::URL_META_KEY );
			}
		}

		if ( isset( $_POST['lunar-core-game-tile-image-id'] ) ) {
			$image_id = absint( $_POST['lunar-core-game-tile-image-id'] );

			if ( $image_id > 0 ) {
				update_term_meta( $term_id, self::IMAGE_META_KEY, $image_id );
			} else {
				delete_term_meta( $term_id, self::IMAGE_META_KEY );
			}
		}
	}

	/**
	 * Memuat Media Library & script picker HANYA di layar edit term
	 * taxonomy Game — menghindari memuat aset yang tidak perlu di
	 * layar admin lain (ENGINEERING_PRINCIPLES.md §8, Performance).
	 *
	 * @param string $hook Nama layar admin saat ini.
	 */
	public function enqueue_admin_assets( string $hook ): void {
		$is_term_screen = in_array( $hook, array( 'term.php', 'edit-tags.php' ), true );

		if ( ! $is_term_screen ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- hanya dipakai untuk cek taxonomy aktif, bukan aksi ubah data.
		$taxonomy = isset( $_GET['taxonomy'] ) ? sanitize_key( wp_unslash( $_GET['taxonomy'] ) ) : '';

		if ( Taxonomies::get_slug_game() !== $taxonomy ) {
			return;
		}

		wp_enqueue_media();

		wp_enqueue_script(
			'lunar-core-game-tile-meta',
			LUNAR_CORE_URL . 'assets/admin/game-tile-meta.js',
			array(),
			LUNAR_CORE_VERSION,
			true
		);
	}
}