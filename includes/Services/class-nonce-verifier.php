<?php
/**
 * Lokasi: lunar-core/includes/Services/class-nonce-verifier.php
 *
 * Shared Service — verifikasi nonce form (CODING_STANDARD.md §14),
 * dipakai di 3 tempat berbeda yang sebelumnya masing-masing menulis
 * baris pengecekan yang identik persis:
 * 1. Game_Menu_Meta::save_meta()
 * 2. Game_Tile_Meta::save_meta()
 * 3. Update_Notes_Meta::save_meta_box()
 *
 * Sengaja HANYA menangani nonce, bukan capability check — capability
 * yang dibutuhkan berbeda bentuk antar pemakai (mis. "manage_categories"
 * untuk term meta vs "edit_post" + $post_id untuk post meta), sehingga
 * tetap sengaja dibiarkan sebagai baris terpisah di masing-masing class.
 *
 * @package Lunar\Services
 */

namespace Lunar\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Nonce_Verifier
 */
class Nonce_Verifier {

	/**
	 * Cek apakah nonce pada $_POST valid untuk action tertentu.
	 *
	 * @param string $nonce_field  Nama field nonce di $_POST.
	 * @param string $nonce_action Action string yang dipakai saat wp_nonce_field().
	 * @return bool
	 */
	public static function is_valid( string $nonce_field, string $nonce_action ): bool {
		return isset( $_POST[ $nonce_field ] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing
			&& wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST[ $nonce_field ] ) ), $nonce_action );
	}
}