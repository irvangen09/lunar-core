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
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue_dashicons' ) );
	}

	/**
	 * Dashicons TIDAK dimuat otomatis di frontend oleh WordPress
	 * (hanya tersedia bawaan di wp-admin/editor). Infobox mendukung
	 * field "icon" bebas isi nama class apa pun, dan dashicons adalah
	 * opsi paling native/mudah dipakai (tanpa dependency eksternal) —
	 * tanpa ini, class dashicons-* akan tampil sebagai kotak kosong
	 * ("tofu") di frontend karena font-nya tidak ter-load.
	 *
	 * Dimuat KONDISIONAL, hanya saat halaman benar-benar memakai
	 * block Infobox (ARCHITECTURE.md §14, Conditional Asset Loading).
	 * Icon dari library lain (mis. Font Awesome) tetap jadi tanggung
	 * jawab tema/situs sendiri untuk memuat font/library-nya.
	 */
	public function maybe_enqueue_dashicons(): void {
		if ( is_singular() && has_block( 'lunar-core/infobox' ) ) {
			wp_enqueue_style( 'dashicons' );
		}
	}

	/**
	 * Memindai folder build/ secara REKURSIF dan mendaftarkan tiap block
	 * yang ditemukan — termasuk block anak di dalam sub-folder
	 * (mis. build/definition-list/item/block.json untuk pola parent-child).
	 *
	 * Revisi dari versi awal yang hanya memindai satu level folder;
	 * direvisi karena beberapa block (Definition List, Accordion, dst)
	 * memakai struktur parent-child sesuai BLOCK_DEVELOPMENT_GUIDE.md §4.
	 * Disetujui Product Owner sebagai penyesuaian teknis yang diperlukan
	 * (CODING_STANDARD.md §20).
	 */
	public function register_blocks(): void {
		if ( ! is_dir( $this->build_path ) ) {
			return;
		}

		$iterator = new \RecursiveIteratorIterator(
			new \RecursiveDirectoryIterator( $this->build_path, \FilesystemIterator::SKIP_DOTS )
		);

		foreach ( $iterator as $file ) {
			if ( 'block.json' === $file->getFilename() ) {
				register_block_type( $file->getPath() );
			}
		}
	}
}
