<?php
/**
 * Lokasi: lunar-core/includes/Services/class-accordion-item-title.php
 *
 * Ekstraksi teks judul dari markup Accordion Item — dipakai bersama
 * oleh Heading_Injector (menyuntik id ke markup hasil render_block)
 * dan TOC_Builder (mengambil teks dari innerHTML hasil parse_blocks()).
 * Sebelumnya regex ini diduplikasi persis di kedua file tersebut;
 * ditarik ke sini supaya hanya ada satu tempat yang perlu diubah
 * kalau nama class CSS "lunar-accordion-item__title" pernah berubah.
 *
 * @package Lunar\Services
 */

namespace Lunar\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Accordion_Item_Title
 */
class Accordion_Item_Title {

	/**
	 * Ekstrak teks judul dari markup yang mengandung heading dengan
	 * class "lunar-accordion-item__title".
	 *
	 * Tidak bisa memakai attribute "title" block secara langsung —
	 * attribute itu bersumber dari rich-text/HTML, tidak tersedia
	 * sebagai attrs polos baik lewat parse_blocks() (TOC_Builder)
	 * maupun lewat filter render_block_* (Heading_Injector).
	 *
	 * @param string $html Markup yang akan dipindai (innerHTML block atau hasil render).
	 * @return string Teks judul (sudah di-strip tag), atau string kosong bila tidak ditemukan.
	 */
	public static function extract( string $html ): string {
		if ( ! preg_match( '/<h[1-6][^>]*class="[^"]*lunar-accordion-item__title[^"]*"[^>]*>(.*?)<\/h[1-6]>/s', $html, $matches ) ) {
			return '';
		}

		return trim( wp_strip_all_tags( $matches[1] ) );
	}
}