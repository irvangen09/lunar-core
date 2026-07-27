<?php
/**
 * Lokasi: lunar-core/includes/Users/class-author-fields.php
 *
 * Field tambahan di layar profil WordPress untuk kebutuhan Author Box
 * & Author Archive di LunarThemes: "Role/Jabatan" (teks bebas, sengaja
 * tidak memakai Role teknis WordPress supaya bebas diisi apa saja dan
 * tetap fleksibel untuk perubahan jangka panjang) dan "Link Sosial
 * Media" (satu baris = satu entri, format "Label | URL" — pola yang
 * sama seperti field "Catatan Update" milik Wiki Artikel, supaya
 * platform baru bisa ditambahkan pengelola sendiri tanpa rilis kode).
 *
 * Ini namespace baru (Lunar\Users\...) karena field ini soal akun
 * WordPress (user), bukan soal konten Wiki Artikel — beda tanggung
 * jawab dari class-class lain di Lunar\Content.
 *
 * @package Lunar\Users
 */

namespace Lunar\Users;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Author_Fields
 */
class Author_Fields {

	/**
	 * Key meta penyimpan role/jabatan.
	 */
	private const ROLE_META_KEY = 'lunar_core_author_role';

	/**
	 * Key meta penyimpan daftar link sosial media.
	 */
	private const SOCIAL_META_KEY = 'lunar_core_author_social_links';

	/**
	 * Action & field nonce untuk keamanan form (CODING_STANDARD.md §14).
	 */
	private const NONCE_ACTION = 'lunar_core_author_fields_action';
	private const NONCE_FIELD  = 'lunar_core_author_fields_nonce';

	/**
	 * Mendaftarkan hook WordPress.
	 *
	 * show_user_profile jalan saat user mengedit profilnya sendiri,
	 * edit_user_profile jalan saat admin mengedit profil user lain —
	 * keduanya perlu didaftarkan supaya field muncul di kedua kondisi.
	 * Pola yang sama berlaku untuk pasangan hook penyimpanannya.
	 */
	public function init(): void {
		add_action( 'show_user_profile', array( $this, 'render_fields' ) );
		add_action( 'edit_user_profile', array( $this, 'render_fields' ) );

		add_action( 'personal_options_update', array( $this, 'save_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_fields' ) );
	}

	/**
	 * Menampilkan field "Role/Jabatan" dan "Link Sosial Media" di
	 * layar profil, mengikuti struktur tabel bawaan WordPress supaya
	 * tampilannya menyatu dengan field profil lain di sekitarnya.
	 *
	 * @param \WP_User $user User yang sedang ditampilkan profilnya.
	 */
	public function render_fields( \WP_User $user ): void {
		if ( ! current_user_can( 'edit_user', $user->ID ) ) {
			return;
		}

		$role_value   = get_user_meta( $user->ID, self::ROLE_META_KEY, true );
		$social_value = get_user_meta( $user->ID, self::SOCIAL_META_KEY, true );

		wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );
		?>
		<h2><?php esc_html_e( 'Author Box (Lunar)', 'lunar-core' ); ?></h2>
		<table class="form-table">
			<tr>
				<th><label for="lunar-core-author-role"><?php esc_html_e( 'Role / Jabatan', 'lunar-core' ); ?></label></th>
				<td>
					<input
						type="text"
						name="lunar-core-author-role"
						id="lunar-core-author-role"
						class="regular-text"
						value="<?php echo esc_attr( $role_value ); ?>"
					/>
					<p class="description">
						<?php esc_html_e( 'Opsional. Bebas diisi apa saja, mis. "Penulis", "Editor", "Kontributor Komunitas".', 'lunar-core' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th><label for="lunar-core-author-social"><?php esc_html_e( 'Link Sosial Media', 'lunar-core' ); ?></label></th>
				<td>
					<textarea
						name="lunar-core-author-social"
						id="lunar-core-author-social"
						rows="5"
						class="large-text"
					><?php echo esc_textarea( $social_value ); ?></textarea>
					<p class="description">
						<?php esc_html_e( 'Opsional. Satu baris = satu link, format: Label | URL (mis. "Instagram | https://instagram.com/namaakun"). Ikon dikenali otomatis dari Label; platform yang tidak dikenali tetap tampil dengan ikon link generik.', 'lunar-core' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Menyimpan kedua field saat profil disimpan.
	 *
	 * @param int $user_id ID user yang profilnya disimpan.
	 */
	public function save_fields( int $user_id ): void {
		if ( ! isset( $_POST[ self::NONCE_FIELD ] )
			|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) ), self::NONCE_ACTION )
		) {
			return;
		}

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		if ( isset( $_POST['lunar-core-author-role'] ) ) {
			$role = sanitize_text_field( wp_unslash( $_POST['lunar-core-author-role'] ) );

			if ( '' === $role ) {
				delete_user_meta( $user_id, self::ROLE_META_KEY );
			} else {
				update_user_meta( $user_id, self::ROLE_META_KEY, $role );
			}
		}

		if ( isset( $_POST['lunar-core-author-social'] ) ) {
			$social = sanitize_textarea_field( wp_unslash( $_POST['lunar-core-author-social'] ) );

			if ( '' === trim( $social ) ) {
				delete_user_meta( $user_id, self::SOCIAL_META_KEY );
			} else {
				update_user_meta( $user_id, self::SOCIAL_META_KEY, $social );
			}
		}
	}

	/**
	 * Role/jabatan penulis — dipakai Theme di Author Box.
	 *
	 * @param int $user_id ID user.
	 * @return string Kosong kalau belum diisi.
	 */
	public static function get_role( int $user_id ): string {
		return (string) get_user_meta( $user_id, self::ROLE_META_KEY, true );
	}

	/**
	 * Daftar link sosial media penulis, sudah diproses jadi array siap
	 * pakai — Theme tinggal loop, tidak perlu parsing format sendiri.
	 *
	 * Baris yang tidak mengikuti format "Label | URL", atau yang
	 * URL-nya tidak valid, dilewati (fail gracefully — bukan fatal
	 * error, bukan juga menampilkan link rusak ke pembaca).
	 *
	 * @param int $user_id ID user.
	 * @return array<int, array{label: string, url: string, icon: string}>
	 */
	public static function get_social_links( int $user_id ): array {
		$raw = (string) get_user_meta( $user_id, self::SOCIAL_META_KEY, true );

		if ( '' === trim( $raw ) ) {
			return array();
		}

		$links = array();

		foreach ( explode( "\n", $raw ) as $line ) {
			$line = trim( $line );

			if ( '' === $line || ! str_contains( $line, '|' ) ) {
				continue;
			}

			list( $label, $url ) = array_map( 'trim', explode( '|', $line, 2 ) );
			$url                 = esc_url_raw( $url );

			if ( '' === $label || '' === $url ) {
				continue;
			}

			$links[] = array(
				'label' => $label,
				'url'   => $url,
				'icon'  => self::detect_icon( $label ),
			);
		}

		return $links;
	}

	/**
	 * Menebak dashicon yang sesuai berdasarkan kata kunci di Label.
	 *
	 * Dashicons dipilih (bukan set ikon baru) karena sudah tersedia
	 * bawaan WordPress tanpa asset tambahan — pola yang sama seperti
	 * ikon Accordion yang sudah dipakai.
	 *
	 * @param string $label Label yang diisi pengelola.
	 * @return string Nama class dashicon.
	 */
	private static function detect_icon( string $label ): string {
		$label = strtolower( $label );

		if ( 'x' === $label || str_contains( $label, 'twitter' ) ) {
			return 'dashicons-twitter';
		}

		$keyword_map = array(
			'facebook'  => 'dashicons-facebook',
			'instagram' => 'dashicons-instagram',
			'whatsapp'  => 'dashicons-whatsapp',
			'linkedin'  => 'dashicons-linkedin',
			'youtube'   => 'dashicons-youtube',
			'email'     => 'dashicons-email',
			'website'   => 'dashicons-admin-site',
		);

		foreach ( $keyword_map as $keyword => $icon ) {
			if ( str_contains( $label, $keyword ) ) {
				return $icon;
			}
		}

		return 'dashicons-admin-links';
	}
}