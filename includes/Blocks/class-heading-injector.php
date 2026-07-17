<?php
/**
 * Lokasi: lunar-core/includes/Blocks/class-heading-injector.php
 *
 * Menyuntik atribut `id` ke heading asli di badan artikel (core/heading)
 * dan judul Accordion Item, supaya link yang dibangun TOC_Builder
 * benar-benar bisa "lompat" ke section yang dituju.
 *
 * Memakai algoritma & urutan pemrosesan yang SAMA PERSIS seperti
 * TOC_Builder (lihat class-toc-builder.php) — keduanya berjalan
 * independen, tapi karena memproses heading dalam urutan dokumen yang
 * sama dengan Heading_Anchors yang di-reset di titik yang sama
 * (awal 'the_content'), hasil id yang dihasilkan akan selalu cocok.
 *
 * @package Lunar\Blocks
 */

namespace Lunar\Blocks;

use Lunar\Services\Heading_Anchors;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Heading_Injector
 */
class Heading_Injector {

	/**
	 * Instance Heading_Anchors yang dipakai bersama sepanjang satu
	 * request render halaman (di-reset tiap kali the_content dimulai).
	 *
	 * @var Heading_Anchors
	 */
	private Heading_Anchors $anchors;

	public function __construct() {
		$this->anchors = new Heading_Anchors();
	}

	/**
	 * Mendaftarkan hook WordPress.
	 */
	public function init(): void {
		// Prioritas 8 — sebelum do_blocks() (prioritas 9) memulai proses
		// render block, supaya tracker anchor selalu bersih di awal tiap
		// pemanggilan the_content (mis. saat ada multiple post di halaman arsip).
		add_filter( 'the_content', array( $this, 'reset_anchors' ), 8 );

		add_filter( 'render_block_core/heading', array( $this, 'inject_heading_anchor' ), 10, 2 );
		add_filter( 'render_block_lunar-core/accordion-item', array( $this, 'inject_accordion_item_anchor' ), 10, 2 );
	}

	/**
	 * Reset tracker anchor. Dikembalikan apa adanya (passthrough) —
	 * fungsi ini cuma numpang di filter 'the_content' untuk efek sampingnya.
	 *
	 * @param string $content Konten yang tidak diubah oleh fungsi ini.
	 * @return string
	 */
	public function reset_anchors( string $content ): string {
		$this->anchors->reset();
		return $content;
	}

	/**
	 * Menyuntik id ke <h2>-<h6> hasil render core/heading, HANYA bila
	 * belum ada id (mis. dari HTML Anchor manual yang sudah didaftarkan
	 * WordPress sendiri).
	 *
	 * @param string $block_content Markup hasil render heading.
	 * @param array  $block Data block (attrs, dsb).
	 * @return string
	 */
	public function inject_heading_anchor( string $block_content, array $block ): string {
		if ( '' === trim( $block_content ) ) {
			return $block_content;
		}

		$manual_anchor = $block['attrs']['anchor'] ?? '';

		if ( '' !== $manual_anchor ) {
			// Sudah ada id dari HTML Anchor manual (WordPress core sudah
			// merender id ini sendiri) — cukup daftarkan biar tidak dipakai
			// ulang oleh heading lain, TIDAK perlu disuntik lagi.
			$this->anchors->use_manual( $manual_anchor );
			return $block_content;
		}

		if ( false !== strpos( $block_content, ' id=' ) ) {
			// Jaga-jaga: sudah ada id dari sumber lain yang tidak kita duga.
			return $block_content;
		}

		$text = trim( wp_strip_all_tags( $block_content ) );

		if ( '' === $text ) {
			return $block_content;
		}

		$anchor = $this->anchors->generate( $text );

		return (string) preg_replace(
			'/<h([1-6])\b/',
			'<h$1 id="' . esc_attr( $anchor ) . '"',
			$block_content,
			1
		);
	}

	/**
	 * Menyuntik id ke judul Accordion Item, dengan cara yang sama.
	 * Accordion Item tidak punya opsi HTML Anchor manual, jadi selalu
	 * auto-generate (kecuali headingLevel "none" — tidak ada heading
	 * sama sekali untuk disuntik).
	 *
	 * @param string $block_content Markup hasil render Accordion Item.
	 * @param array  $block Data block (attrs, dsb).
	 * @return string
	 */
	public function inject_accordion_item_anchor( string $block_content, array $block ): string {
		$heading_level = $block['attrs']['headingLevel'] ?? 'h2';

		if ( 'none' === $heading_level ) {
			return $block_content;
		}

		// Tidak bisa pakai $block['attrs']['title'] — attribute itu
		// bersumber dari HTML (rich-text), tidak tersedia di $block['attrs']
		// lewat filter render_block_* (sama seperti masalah di TOC_Builder).
		// Ekstrak langsung dari markup yang sudah dirender.
		if ( ! preg_match( '/<h[1-6][^>]*class="[^"]*lunar-accordion-item__title[^"]*"[^>]*>(.*?)<\/h[1-6]>/s', $block_content, $matches ) ) {
			return $block_content;
		}

		$text = trim( wp_strip_all_tags( $matches[1] ) );

		if ( '' === $text ) {
			return $block_content;
		}

		$anchor = $this->anchors->generate( $text );

		// Cari tag heading yang punya class "lunar-accordion-item__title"
		// secara spesifik — jangan asal tag heading pertama yang ketemu,
		// supaya tidak salah suntik kalau ada heading lain di konten bebas
		// Accordion Item tersebut.
		return (string) preg_replace(
			'/(<h[1-6][^>]*class="[^"]*lunar-accordion-item__title[^"]*"[^>]*)(>)/',
			'$1 id="' . esc_attr( $anchor ) . '"$2',
			$block_content,
			1
		);
	}
}
