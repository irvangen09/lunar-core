<?php
/**
 * Lokasi: lunar-core/lunar-core.php
 *
 * Plugin Name:       Lunar Core
 * Description:       Plugin pendamping LunarThemes — menangani data, Custom Post Type, Taxonomy, dan Gutenberg Block untuk dokumentasi wiki game.
 * Version:           0.1.0
 * Requires PHP:      8.0
 * Text Domain:       lunar-core
 *
 * @package Lunar\Core
 */

namespace Lunar\Core;

use Lunar\Blocks\Registry as Block_Registry;
use Lunar\Blocks\Categories as Block_Categories;
use Lunar\Blocks\Formats as Block_Formats;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

define( 'LUNAR_CORE_VERSION', '0.1.0' );
define( 'LUNAR_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'LUNAR_CORE_URL', plugin_dir_url( __FILE__ ) );

/**
 * Autoloader sederhana untuk namespace Lunar\.
 *
 * Tidak memakai Composer — dihindari sebagai dependency yang tidak
 * diperlukan untuk kebutuhan sesederhana ini (ARCHITECTURE.md §19,
 * Dependency Policy). Mengikuti konvensi penamaan file WordPress:
 * class Foo\Bar\Baz_Qux -> includes/Bar/class-baz-qux.php
 *
 * @param string $class_name Nama class lengkap dengan namespace.
 */
function autoload( string $class_name ): void {
	if ( ! str_starts_with( $class_name, __NAMESPACE__ . '\\' ) && ! str_starts_with( $class_name, 'Lunar\\Blocks\\' ) ) {
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
	$block_registry = new Block_Registry( LUNAR_CORE_PATH . 'build' );
	$block_registry->init();

	$block_categories = new Block_Categories();
	$block_categories->init();

	$block_formats = new Block_Formats( LUNAR_CORE_PATH . 'build', LUNAR_CORE_URL . 'build' );
	$block_formats->init();
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\bootstrap' );
