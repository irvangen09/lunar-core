<?php
/**
 * Lokasi: lunar-core/includes/public-api.php
 *
 * Fungsi publik yang boleh dipanggil LunarThemes (atau tema/plugin lain)
 * untuk membaca data dari LunarCore, tanpa perlu mengetahui atau
 * bergantung pada class internal (Meta_Fields, Author_Fields, dst).
 *
 * Setiap fungsi di sini adalah "kontrak publik" yang stabil — Theme
 * cukup memanggil fungsi ini dan mengecek function_exists() sebagai
 * jaring pengaman kalau plugin nonaktif, tanpa pernah menyentuh nama
 * class atau struktur internal LunarCore secara langsung. Ini sengaja
 * dipisah dari class-class aslinya (bukan menambah method public baru
 * di sana) supaya "kontrak" yang Theme pegang jelas letaknya di satu
 * file, terpisah dari implementasi yang boleh berubah kapan saja.
 *
 * Sengaja berupa fungsi global (bukan method static di sebuah class)
 * dan sengaja TIDAK di-autoload lewat spl_autoload_register() plugin
 * ini (yang hanya mengenali class) — file ini di-require_once langsung
 * dari lunar-core.php supaya seluruh fungsinya selalu tersedia begitu
 * plugin aktif, tanpa syarat class mana pun sudah diakses lebih dulu.
 *
 * @package Lunar\Core
 */

use Lunar\Content\Meta_Fields;
use Lunar\Content\Game_Menu_Meta;
use Lunar\Content\Game_Tile_Meta;
use Lunar\Users\Author_Fields;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Daftar key field-sync yang dikenali sistem (Dokumen Perencanaan §3.4).
 *
 * @return string[]
 */
function lunar_core_get_recognized_fields(): array {
	return Meta_Fields::get_recognized_fields();
}

/**
 * Mengubah key field-sync (mis. "musim") jadi nama meta key lengkap
 * (mis. "lunar_core_musim"). Null kalau key tidak dikenali.
 *
 * @param string $field Key field, salah satu dari lunar_core_get_recognized_fields().
 * @return string|null
 */
function lunar_core_get_field_meta_key( string $field ): ?string {
	return Meta_Fields::get_meta_key( $field );
}

/**
 * Role/jabatan penulis untuk Author Box LunarThemes.
 *
 * @param int $user_id ID user.
 * @return string Kosong kalau belum diisi.
 */
function lunar_core_get_author_role( int $user_id ): string {
	return Author_Fields::get_role( $user_id );
}

/**
 * Daftar link sosial media penulis, sudah diproses jadi array siap
 * pakai untuk Author Box LunarThemes.
 *
 * Catatan: field 'icon' berupa nama class dashicons (mis.
 * "dashicons-twitter") — pemuatan font dashicons di frontend adalah
 * tanggung jawab pemanggil (Theme), LunarCore tidak memuatnya untuk
 * konteks Author Box.
 *
 * @param int $user_id ID user.
 * @return array<int, array{label: string, url: string, icon: string}>
 */
function lunar_core_get_author_social_links( int $user_id ): array {
	return Author_Fields::get_social_links( $user_id );
}

/**
 * Key term meta untuk Menu Sekunder per Judul Game (dropdown pilihan
 * menu WordPress di layar edit term taxonomy Game).
 *
 * Catatan: LunarThemes saat ini membaca key ini sebagai string literal
 * langsung (diperlakukan sebagai stable public data contract, lihat
 * komentar di game-context.php/game-queries.php) — fungsi ini murni
 * opsi tambahan yang aditif, bukan keharusan migrasi.
 *
 * @return string
 */
function lunar_core_get_game_menu_meta_key(): string {
	return Game_Menu_Meta::get_meta_key();
}

/**
 * Key term meta untuk URL tujuan kustom Game Tile di Homepage.
 *
 * @return string
 */
function lunar_core_get_game_tile_url_meta_key(): string {
	return Game_Tile_Meta::get_url_meta_key();
}

/**
 * Key term meta untuk ID attachment gambar kustom Game Tile di Homepage.
 *
 * @return string
 */
function lunar_core_get_game_tile_image_meta_key(): string {
	return Game_Tile_Meta::get_image_meta_key();
}