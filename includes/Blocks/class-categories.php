<?php
/**
 * Lokasi: lunar-core/includes/Blocks/class-categories.php
 *
 * Mendaftarkan kategori block khusus "Lunar" di Block Inserter,
 * supaya seluruh block LunarCore (Callout, Infobox, Accordion, dst)
 * terkumpul rapi dalam satu kategori — tidak tercampur ke kategori
 * bawaan WordPress seperti "Text" atau "Widgets".
 *
 * @package Lunar\Blocks
 */

namespace Lunar\Blocks;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Categories
 */
class Categories {

	/**
	 * Slug kategori block, dirujuk oleh "category" di tiap block.json.
	 *
	 * @var string
	 */
	private const SLUG = 'lunar-blocks';

	/**
	 * Mendaftarkan hook WordPress.
	 */
	public function init(): void {
		add_filter( 'block_categories_all', array( $this, 'register_category' ) );
	}

	/**
	 * Menambahkan kategori "Lunar" ke daftar kategori block yang sudah ada.
	 *
	 * @param array $categories Daftar kategori block bawaan WordPress.
	 * @return array Daftar kategori setelah ditambahkan kategori Lunar.
	 */
	public function register_category( array $categories ): array {
		return array_merge(
			array(
				array(
					'slug'  => self::SLUG,
					'title' => __( 'Lunar', 'lunar-core' ),
					'icon'  => null,
				),
			),
			$categories
		);
	}
}
