<?php
/**
 * Lokasi: lunar-core/includes/Content/class-meta-sync.php
 *
 * Membaca block Infobox di dalam post_content saat artikel disimpan,
 * lalu menyinkronkan Infobox Field bermode "Dikenali" ke post meta
 * (Dokumen-Perencanaan-LunarThemes.md §3.4, Architecture Review 2.1).
 *
 * @package Lunar\Content
 */

namespace Lunar\Content;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Meta_Sync
 */
class Meta_Sync {

	private const INFOBOX_BLOCK = 'lunar-core/infobox';
	private const FIELD_BLOCK   = 'lunar-core/infobox-field';

	/**
	 * Mendaftarkan hook WordPress.
	 *
	 * Sengaja di-scope ke "save_post_{$post_type}" (bukan "save_post"
	 * generik) — supaya sync tidak ikut berjalan (parse_blocks +
	 * update/delete_post_meta yang sia-sia) di setiap penyimpanan
	 * WP Page atau Post native, konsisten dengan pola Update_Notes_Meta.
	 */
	public function init(): void {
		add_action( 'save_post_' . Post_Types::get_slug(), array( $this, 'sync' ), 10, 2 );
	}

	/**
	 * Entry point sync — dipanggil tiap post disimpan.
	 *
	 * @param int      $post_id ID post yang disimpan.
	 * @param \WP_Post $post    Object post yang disimpan.
	 */
	public function sync( int $post_id, \WP_Post $post ): void {
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		$recognized_values = $this->extract_recognized_values( $post->post_content );

		foreach ( Meta_Fields::get_recognized_fields() as $field ) {
			$meta_key = Meta_Fields::get_meta_key( $field );

			if ( null === $meta_key ) {
				continue; // Fail gracefully — tidak seharusnya terjadi.
			}

			if ( isset( $recognized_values[ $field ] ) && '' !== $recognized_values[ $field ] ) {
				update_post_meta( $post_id, $meta_key, $recognized_values[ $field ] );
			} else {
				// Field dihapus dari Infobox (atau Infobox dihapus dari artikel)
				// -> hapus meta juga, supaya tidak ada data basi yang tetap
				// muncul di hasil filter pencarian.
				delete_post_meta( $post_id, $meta_key );
			}
		}
	}

	/**
	 * Memindai post_content, cari block Infobox, ambil seluruh Infobox
	 * Field bermode "dikenali" beserta nilainya.
	 *
	 * @param string $post_content Konten post (format block markup).
	 * @return array<string, string> Key = slug field (hasil resolusi Term ID
	 *                                lewat Meta_Fields::get_slug_by_term_id()),
	 *                                value = teks polos.
	 */
	private function extract_recognized_values( string $post_content ): array {
		$blocks  = parse_blocks( $post_content );
		$infobox = $this->find_block( $blocks, self::INFOBOX_BLOCK );

		if ( null === $infobox || empty( $infobox['innerBlocks'] ) ) {
			return array();
		}

		$values = array();

		foreach ( $infobox['innerBlocks'] as $field_block ) {
			if ( self::FIELD_BLOCK !== ( $field_block['blockName'] ?? '' ) ) {
				continue;
			}

			$attrs = $field_block['attrs'] ?? array();

			if ( 'dikenali' !== ( $attrs['mode'] ?? '' ) ) {
				continue; // Field mode "bebas" tidak di-sync — sesuai desain.
			}

			$term_id = (int) ( $attrs['recognizedField'] ?? 0 );

			if ( $term_id <= 0 ) {
				continue; // Belum ada field dipilih di Inspector -> abaikan.
			}

			$field_key = Meta_Fields::get_slug_by_term_id( $term_id );

			if ( null === $field_key ) {
				continue; // Term sudah dihapus/tidak valid -> abaikan (fail gracefully).
			}

			$values[ $field_key ] = $this->extract_value_text( $field_block['innerHTML'] ?? '' );
		}

		return $values;
	}

	/**
	 * Mencari satu block berdasarkan nama di level teratas array hasil parse_blocks().
	 *
	 * @param array  $blocks     Hasil parse_blocks().
	 * @param string $block_name Nama block yang dicari, mis. "lunar-core/infobox".
	 * @return array|null
	 */
	private function find_block( array $blocks, string $block_name ): ?array {
		foreach ( $blocks as $block ) {
			if ( ( $block['blockName'] ?? '' ) === $block_name ) {
				return $block;
			}
		}

		return null;
	}

	/**
	 * Mengambil teks polos dari markup <dd class="lunar-infobox-field__value">.
	 *
	 * Sengaja pakai regex sederhana, BUKAN HTML parser penuh — karena
	 * markup ini sudah pasti berasal dari save.js kita sendiri (Tahap 3.6),
	 * bukan HTML pihak ketiga yang perlu ditangani secara umum/defensif.
	 *
	 * Catatan perbaikan bug (refactor decoupling, sesi ini): regex ini
	 * sebelumnya masih mencari tag <span>, padahal markup sudah berubah
	 * jadi <dd> sejak refactor styling Infobox. Akibatnya, setiap artikel
	 * yang disimpan ulang setelah refactor tersebut gagal ter-sync -- regex
	 * tidak pernah cocok, nilai dianggap kosong, dan meta lama justru ikut
	 * terhapus lewat delete_post_meta() di sync(). Ditemukan lewat
	 * pengecekan langsung ke save.js saat menelusuri dampak refactor Term ID
	 * ini terhadap filter pencarian, bukan bagian dari rencana Tugas semula.
	 *
	 * @param string $html innerHTML block Infobox Field.
	 * @return string Teks polos (tag format seperti bold/italic dibuang).
	 */
	private function extract_value_text( string $html ): string {
		if ( preg_match( '/<dd[^>]*class="[^"]*lunar-infobox-field__value[^"]*"[^>]*>(.*?)<\/dd>/s', $html, $matches ) ) {
			return trim( wp_strip_all_tags( $matches[1] ) );
		}

		return '';
	}
}