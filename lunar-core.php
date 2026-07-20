<?php
/**
 * Lokasi: lunar-core/lunar-core.php
 *
 * Plugin Name:       Lunar Core
 * Description:       Plugin pendamping LunarThemes — menangani data, Custom Post Type, Taxonomy, dan Gutenberg Block untuk dokumentasi wiki game.
 * Version:           0.2.0
 * Requires PHP:      8.0
 * Text Domain:       lunar-core
 *
 * @package Lunar\Core
 */

namespace Lunar\Core;

use Lunar\Blocks\Registry as Block_Registry;
use Lunar\Blocks\Categories as Block_Categories;
use Lunar\Blocks\Formats as Block_Formats;
use Lunar\Blocks\Heading_Injector;
use Lunar\Content\Post_Types;
use Lunar\Content\Taxonomies;
use Lunar\Content\Game_Menu_Meta;
use Lunar\Content\Game_Tile_Meta;
use Lunar\Content\Update_Notes_Meta;
use Lunar\Content\Meta_Fields;
use Lunar\Content\Meta_Sync;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

define( 'LUNAR_CORE_VERSION', '0.1.0' );
define( 'LUNAR_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'LUNAR_CORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Autoloader sederhana untuk seluruh namespace Lunar\.
 *
 * Tidak memakai Composer — dihindari sebagai dependency yang tidak
 * diperlukan untuk kebutuhan sesederhana ini (ARCHITECTURE.md §19,
 * Dependency Policy). Mengikuti konvensi penamaan file WordPress:
 * class Foo\Bar\Baz_Qux -> includes/Bar/class-baz-qux.php
 *
 * Digeneralisasi (sebelumnya hanya mengenali Lunar\Core\ dan Lunar\Blocks\)
 * supaya namespace baru (Lunar\Content, Lunar\Services, dst — sesuai
 * BLUEPRINT.md §12) otomatis ter-autoload tanpa perlu revisi file ini lagi.
 *
 * @param string $class_name Nama class lengkap dengan namespace.
 */
function autoload( string $class_name ): void {
	if ( ! str_starts_with( $class_name, 'Lunar\\' ) ) {
		return;
	}

	$relative = str_starts_with( $class_name, 'Lunar\\Core\\' )
		? substr( $class_name, strlen( 'Lunar\\Core\\' ) )
		: substr( $class_name, strlen( 'Lunar\\' ) );

	$parts     = explode( '\\', $relative );
	$file_name = 'class-' . strtolower( str_replace( '_', '-', array_pop( $parts ) ) ) . '.php';
	$path      = LUNAR_CORE_PATH . 'includes/' . implode( '/', $parts ) . '/' . $file_name;

	if ( file_exists( $path ) ) {
		require_once $path;
	}
}
spl_autoload_register( __NAMESPACE__ . '\\autoload' );

/**
 * Menyalakan seluruh komponen plugin.
 * Urutan mengikuti Bootstrap Flow di BLUEPRINT.md §7.
 */
function bootstrap(): void {
	$post_types = new Post_Types();
	$post_types->init();

	$taxonomies = new Taxonomies();
	$taxonomies->init();

	$game_menu_meta = new Game_Menu_Meta();
	$game_menu_meta->init();

	$game_tile_meta = new Game_Tile_Meta();
	$game_tile_meta->init();

	$update_notes_meta = new Update_Notes_Meta();
	$update_notes_meta->init();

	$block_registry = new Block_Registry( LUNAR_CORE_PATH . 'build' );
	$block_registry->init();

	$block_categories = new Block_Categories();
	$block_categories->init();

	$block_formats = new Block_Formats( LUNAR_CORE_PATH . 'build', LUNAR_CORE_URL . 'build' );
	$block_formats->init();

	$meta_fields = new Meta_Fields();
	$meta_fields->init();

	$meta_sync = new Meta_Sync();
	$meta_sync->init();

	$heading_injector = new Heading_Injector();
	$heading_injector->init();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );

/**
 * Dijalankan sekali saat plugin diaktifkan.
 *
 * CPT & Taxonomy didaftarkan langsung di sini (memanggil register()
 * langsung, bukan lewat hook 'init' seperti biasa) supaya rewrite rule-nya
 * sudah dikenali WordPress SEBELUM flush_rewrite_rules() dipanggil.
 * Tanpa ini, permalink "/wiki/..." dan "/game/..." akan 404 sampai
 * pengelola resave manual lewat Settings -> Permalinks.
 */
function activate(): void {
	$post_types = new Post_Types();
	$post_types->register();

	$taxonomies = new Taxonomies();
	$taxonomies->register();

	flush_rewrite_rules();
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\\activate' );

/**
 * Dijalankan sekali saat plugin dinonaktifkan.
 * Membersihkan rewrite rule yang sempat ditambahkan (ENGINEERING_PRINCIPLES.md §13,
 * Simplicity Wins — hindari rewrite rule mati menumpuk di database).
 */
function deactivate(): void {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\\deactivate' );