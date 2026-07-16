<?php
/**
 * Lokasi: lunar-core/includes/Blocks/class-registry.php
 *
 * Mendaftarkan seluruh Gutenberg Block LunarCore ke WordPress.
 *
 * Sengaja dibuat generik (memindai folder build/), bukan menulis
 * register_block_type() satu-satu per block — supaya 13 block
 * berikutnya (Definition List, Infobox, dst) otomatis ikut terdaftar
 * begitu folder build-nya ada, tanpa perlu menyentuh file ini lagi.
 *
 * @package Lunar\Blocks
 */

namespace Lunar\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Registry
 */
class Registry {

	/**
	 * Path absolut ke folder build hasil kompilasi block (berisi block.json per block).
	 *
	 * @var string
	 */
	private string $build_path;

	/**
	 * @param string $build_path Path absolut ke folder build/.
	 */
	public function __construct( string $build_path ) {
		$this->build_path = untrailingslashit( $build_path );
	}

	/**
	 * Mendaftarkan hook WordPress.
	 */
	public function init(): void {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	/**
	 * Memindai folder build/ dan mendaftarkan tiap block yang ditemukan.
	 */
	public function register_blocks(): void {
		if ( ! is_dir( $this->build_path ) ) {
			return;
		}

		$block_folders = glob( $this->build_path . '/*', GLOB_ONLYDIR );

		if ( empty( $block_folders ) ) {
			return;
		}

		foreach ( $block_folders as $folder ) {
			$this->register_single_block( $folder );
		}
	}

	/**
	 * Mendaftarkan satu block dari folder build-nya.
	 * Fail gracefully bila block.json tidak ditemukan (BLOCK_DEVELOPMENT_GUIDE.md §18).
	 *
	 * @param string $folder Path folder build satu block.
	 */
	private function register_single_block( string $folder ): void {
		if ( ! file_exists( $folder . '/block.json' ) ) {
			return;
		}

		register_block_type( $folder );
	}
}
