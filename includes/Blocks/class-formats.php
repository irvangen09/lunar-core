<?php
/**
 * Lokasi: lunar-core/includes/Blocks/class-formats.php
 *
 * Mendaftarkan RichText Format LunarCore (mis. Version/Patch Tag).
 * Terpisah dari class Registry karena Format tidak memakai Block API
 * (tidak punya block.json), sehingga jalur enqueue-nya berbeda:
 * script hanya perlu dimuat di editor, sedangkan style perlu dimuat
 * di editor MAUPUN frontend (supaya badge tetap tampil ke pembaca).
 *
 * @package Lunar\Blocks
 */

namespace Lunar\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Formats
 */
class Formats {

	/**
	 * Path absolut ke folder build/.
	 *
	 * @var string
	 */
	private string $build_path;

	/**
	 * URL ke folder build/.
	 *
	 * @var string
	 */
	private string $build_url;

	/**
	 * @param string $build_path Path absolut ke folder build/.
	 * @param string $build_url  URL ke folder build/.
	 */
	public function __construct( string $build_path, string $build_url ) {
		$this->build_path = untrailingslashit( $build_path );
		$this->build_url  = untrailingslashit( $build_url );
	}

	/**
	 * Mendaftarkan hook WordPress.
	 */
	public function init(): void {
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_style' ) );
	}

	/**
	 * Memuat script (untuk tombol toolbar) & style di Block Editor.
	 *
	 * Catatan: CSS dari style.scss otomatis diberi nama "style-index.css"
	 * oleh wp-scripts (konvensi sama seperti block biasa), BUKAN "index.css"
	 * — sempat salah tulis di revisi sebelumnya, terbukti dari log build.
	 */
	public function enqueue_editor_assets(): void {
		$asset = $this->get_asset_file( 'version-tag' );

		if ( null === $asset ) {
			return;
		}

		wp_enqueue_script(
			'lunar-core-version-tag',
			$this->build_url . '/version-tag/index.js',
			$asset['dependencies'],
			$asset['version'],
			true
		);

		wp_enqueue_style(
			'lunar-core-version-tag',
			$this->build_url . '/version-tag/style-index.css',
			array(),
			$asset['version']
		);
	}

	/**
	 * Memuat style saja di frontend — badge harus tetap tampil ke
	 * pembaca meski tombol toolbar (JS) tidak relevan di luar editor.
	 */
	public function enqueue_frontend_style(): void {
		$asset = $this->get_asset_file( 'version-tag' );

		if ( null === $asset ) {
			return;
		}

		wp_enqueue_style(
			'lunar-core-version-tag',
			$this->build_url . '/version-tag/style-index.css',
			array(),
			$asset['version']
		);
	}

	/**
	 * Membaca file *.asset.php hasil build wp-scripts.
	 * Fail gracefully bila belum di-build (BLOCK_DEVELOPMENT_GUIDE.md §18).
	 *
	 * @param string $entry_name Nama folder entry di build/.
	 * @return array{dependencies: array, version: string}|null
	 */
	private function get_asset_file( string $entry_name ): ?array {
		$path = $this->build_path . '/' . $entry_name . '/index.asset.php';

		if ( ! file_exists( $path ) ) {
			return null;
		}

		return require $path;
	}
}
