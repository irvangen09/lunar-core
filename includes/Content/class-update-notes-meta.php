<?php
/**
 * Lokasi: lunar-core/includes/Content/class-update-notes-meta.php
 *
 * Field manual opsional "Catatan Update" — daftar kronologis catatan
 * perubahan penting pada Wiki Artikel (Dokumen Perencanaan §3.1).
 * Ditampilkan Theme di bawah tanggal "Terakhir diperbarui" (post_modified,
 * native WordPress, tidak butuh apapun dari sini).
 *
 * Bukan bagian dari block manapun — UI-nya meta box PHP klasik (bukan
 * sidebar panel Gutenberg berbasis JS), karena kebutuhannya sederhana
 * dan jarang dipakai (satu textarea bebas, satu baris = satu catatan).
 *
 * @package Lunar\Content
 */

namespace Lunar\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Update_Notes_Meta
 */
class Update_Notes_Meta {

	/**
	 * Key meta penyimpan catatan update.
	 */
	private const META_KEY = 'lunar_core_update_notes';

	/**
	 * Action & field nonce untuk keamanan form (CODING_STANDARD.md §14).
	 */
	private const NONCE_ACTION = 'lunar_core_update_notes_action';
	private const NONCE_FIELD  = 'lunar_core_update_notes_nonce';

	/**
	 * Mendaftarkan hook WordPress.
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'register_meta' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		$post_type = Post_Types::get_slug();
		add_action( "save_post_{$post_type}", array( $this, 'save_meta_box' ) );
	}

	/**
	 * Mendaftarkan meta lewat register_post_meta() supaya portable &
	 * tersedia di REST (ENGINEERING_PRINCIPLES.md §14, Portability).
	 */
	public function register_meta(): void {
		register_post_meta(
			Post_Types::get_slug(),
			self::META_KEY,
			array(
				'single'            => true,
				'type'              => 'string',
				'show_in_rest'      => true,
				'sanitize_callback' => 'sanitize_textarea_field',
				'auth_callback'     => function ( $allowed, $meta_key, $post_id ) {
					return current_user_can( 'edit_post', $post_id );
				},
			)
		);
	}

	/**
	 * Mendaftarkan meta box di kolom samping layar edit Wiki Artikel.
	 */
	public function add_meta_box(): void {
		add_meta_box(
			'lunar-core-update-notes',
			__( 'Catatan Update', 'lunar-core' ),
			array( $this, 'render_meta_box' ),
			Post_Types::get_slug(),
			'side',
			'default'
		);
	}

	/**
	 * Menampilkan textarea catatan update.
	 *
	 * @param \WP_Post $post Post yang sedang diedit.
	 */
	public function render_meta_box( \WP_Post $post ): void {
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );

		$value = get_post_meta( $post->ID, self::META_KEY, true );
		?>
		<p>
			<textarea
				name="lunar-core-update-notes"
				id="lunar-core-update-notes"
				rows="5"
				style="width: 100%;"
			><?php echo esc_textarea( $value ); ?></textarea>
		</p>
		<p class="description">
			<?php esc_html_e( 'Opsional. Satu baris = satu catatan. Tulis dari yang terbaru ke terlama. Kosongkan jika tidak ada catatan khusus untuk update ini.', 'lunar-core' ); ?>
		</p>
		<?php
	}

	/**
	 * Menyimpan catatan update saat Wiki Artikel disimpan.
	 *
	 * @param int $post_id ID post yang disimpan.
	 */
	public function save_meta_box( int $post_id ): void {
		if ( ! isset( $_POST[ self::NONCE_FIELD ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ), self::NONCE_ACTION )
		) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( ! isset( $_POST['lunar-core-update-notes'] ) ) {
			return;
		}

		$value = sanitize_textarea_field( wp_unslash( $_POST['lunar-core-update-notes'] ) );

		if ( '' === trim( $value ) ) {
			delete_post_meta( $post_id, self::META_KEY );
		} else {
			update_post_meta( $post_id, self::META_KEY, $value );
		}
	}

	/**
	 * Key meta — dipakai Theme (get_post_meta) untuk menampilkan catatan
	 * update di bawah tanggal "Terakhir diperbarui".
	 *
	 * @return string
	 */
	public static function get_meta_key(): string {
		return self::META_KEY;
	}
}
