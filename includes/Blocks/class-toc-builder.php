<?php
/**
 * Lokasi: lunar-core/includes/Blocks/class-toc-builder.php
 *
 * Logic inti block TOC — memindai post_content mencari heading
 * (termasuk judul Accordion Item), lalu membangun struktur bersarang
 * berdasarkan level heading.
 *
 * Sengaja dipisah dari render.php (bukan fungsi global di dalamnya)
 * supaya tidak berisiko "Cannot redeclare function" bila block ini
 * ter-render lebih dari sekali dalam satu request.
 *
 * @package Lunar\Blocks
 */

namespace Lunar\Blocks;

use Lunar\Services\Heading_Anchors;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class TOC_Builder
 */
class TOC_Builder {

	/**
	 * Mengambil seluruh heading dari sebuah post, lengkap dengan anchor unik.
	 *
	 * @param int $post_id ID post yang akan dipindai.
	 * @return array<int, array{level:int, text:string, anchor:string}>
	 */
	public function get_headings( int $post_id ): array {
		$post_content = get_post_field( 'post_content', $post_id );

		if ( ! is_string( $post_content ) || '' === $post_content ) {
			return array();
		}

		$blocks = parse_blocks( $post_content );
		$raw    = array();

		$this->collect_headings( $blocks, $raw );

		if ( empty( $raw ) ) {
			return array();
		}

		$anchors = new Heading_Anchors();
		$anchors->reset();

		foreach ( $raw as &$heading ) {
			$manual_anchor = $heading['manual_anchor'] ?? '';

			$heading['anchor'] = ( '' !== $manual_anchor )
				? $anchors->use_manual( $manual_anchor )
				: $anchors->generate( $heading['text'] );

			unset( $heading['manual_anchor'] );
		}
		unset( $heading );

		return $raw;
	}

	/**
	 * Memindai array block hasil parse_blocks() secara REKURSIF.
	 *
	 * Mengenali 2 sumber heading:
	 * - Block "core/heading" biasa, di mana pun posisinya (termasuk
	 *   nested di dalam Accordion/Tabs/Steps).
	 * - Judul "lunar-core/accordion-item" (kecuali headingLevel diset
	 *   "none") — karena judul ini bukan block Heading terpisah,
	 *   melainkan attribute di block itu sendiri.
	 *
	 * Infobox TIDAK butuh pengecualian khusus — secara struktur block
	 * itu tidak mungkin memuat "core/heading" atau "accordion-item" di
	 * dalamnya (field Infobox berupa rich-text, bukan InnerBlocks).
	 *
	 * @param array $blocks Array block (dari parse_blocks() atau innerBlocks).
	 * @param array $results Dikumpulkan lewat reference, bukan return value.
	 */
	private function collect_headings( array $blocks, array &$results ): void {
		foreach ( $blocks as $block ) {
			$block_name = $block['blockName'] ?? '';

			if ( 'core/heading' === $block_name ) {
				$text = trim( wp_strip_all_tags( $block['innerHTML'] ?? '' ) );

				if ( '' !== $text ) {
					$results[] = array(
						'level'         => (int) ( $block['attrs']['level'] ?? 2 ),
						'text'          => $text,
						'manual_anchor' => $block['attrs']['anchor'] ?? '',
					);
				}
			} elseif ( 'lunar-core/accordion-item' === $block_name ) {
				$heading_level = $block['attrs']['headingLevel'] ?? 'h2';

				if ( 'none' !== $heading_level ) {
					$text = trim( wp_strip_all_tags( $block['attrs']['title'] ?? '' ) );

					if ( '' !== $text ) {
						$results[] = array(
							'level' => (int) substr( $heading_level, 1 ),
							'text'  => $text,
						);
					}
				}
			}

			if ( ! empty( $block['innerBlocks'] ) ) {
				$this->collect_headings( $block['innerBlocks'], $results );
			}
		}
	}

	/**
	 * Mengubah daftar heading rata (flat) jadi struktur pohon bersarang
	 * berdasarkan level — H3 otomatis jadi anak dari H2 sebelumnya, dst.
	 *
	 * @param array $headings Hasil dari get_headings().
	 * @return array Struktur pohon: setiap node punya 'text', 'anchor', 'children'.
	 */
	public function build_tree( array $headings ): array {
		$root = array();

		// Sentinel level 0 di dasar stack — lebih rendah dari heading
		// manapun (minimal H2), supaya logic "while level lebih dalam"
		// bekerja seragam tanpa perlu pengecualian untuk item pertama.
		$stack   = array();
		$stack[] = array(
			'level'    => 0,
			'children' => &$root,
		);

		foreach ( $headings as $heading ) {
			while ( count( $stack ) > 1 && $stack[ count( $stack ) - 1 ]['level'] >= $heading['level'] ) {
				array_pop( $stack );
			}

			$top_index = count( $stack ) - 1;

			$stack[ $top_index ]['children'][] = array(
				'text'     => $heading['text'],
				'anchor'   => $heading['anchor'],
				'children' => array(),
			);

			$new_index = count( $stack[ $top_index ]['children'] ) - 1;

			$stack[] = array(
				'level'    => $heading['level'],
				'children' => &$stack[ $top_index ]['children'][ $new_index ]['children'],
			);
		}

		return $root;
	}

	/**
	 * Merender struktur pohon jadi HTML <ul><li> bersarang.
	 *
	 * @param array $nodes Struktur pohon dari build_tree().
	 * @return string
	 */
	public function render_tree( array $nodes ): string {
		if ( empty( $nodes ) ) {
			return '';
		}

		$html = '<ul class="lunar-toc__list">';

		foreach ( $nodes as $node ) {
			$html .= '<li class="lunar-toc__item">';
			$html .= '<a href="#' . esc_attr( $node['anchor'] ) . '">' . esc_html( $node['text'] ) . '</a>';
			$html .= $this->render_tree( $node['children'] );
			$html .= '</li>';
		}

		$html .= '</ul>';

		return $html;
	}
}
