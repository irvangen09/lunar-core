<?php
/**
 * Lokasi: lunar-core/includes/Services/class-heading-anchors.php
 *
 * Shared Service — menghasilkan slug/ID unik dari teks heading,
 * dipakai di 3 tempat berbeda (ARCHITECTURE.md §11, Shared Services):
 * 1. Menyuntik id ke Heading asli di badan artikel (class-heading-injector.php)
 * 2. Menyuntik id ke judul Accordion Item (class-heading-injector.php)
 * 3. Dipakai TOC (render.php) untuk membangun link yang cocok
 *
 * Setiap pemakai membuat instance-nya sendiri (bukan singleton global) —
 * karena yang penting cuma ALGORITMA & URUTAN pemrosesannya konsisten,
 * bukan berbagi state antar pemakai secara langsung.
 *
 * @package Lunar\Services
 */

namespace Lunar\Services;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Cegah akses langsung.
}

/**
 * Class Heading_Anchors
 */
class Heading_Anchors {

	/**
	 * Slug yang sudah dipakai dalam sesi berjalan (satu halaman/request),
	 * supaya heading dengan teks sama tidak menghasilkan id yang bentrok.
	 *
	 * @var string[]
	 */
	private array $used_slugs = array();

	/**
	 * Reset daftar slug — dipanggil sebelum mulai memproses satu artikel,
	 * supaya tidak "nyangkut" dari pemrosesan sebelumnya.
	 */
	public function reset(): void {
		$this->used_slugs = array();
	}

	/**
	 * Mendaftarkan anchor yang SUDAH ditentukan manual (mis. field
	 * "HTML Anchor" bawaan Heading block), supaya auto-generate di
	 * heading lain tidak kebetulan bentrok dengannya.
	 *
	 * @param string $anchor Anchor yang sudah ditentukan manual.
	 * @return string Anchor final (sama persis, kecuali ternyata bentrok
	 *                dengan yang sudah dipakai — maka diberi turunan unik).
	 */
	public function use_manual( string $anchor ): string {
		$anchor = sanitize_title( $anchor );

		if ( '' === $anchor ) {
			return $this->generate( '' );
		}

		if ( ! in_array( $anchor, $this->used_slugs, true ) ) {
			$this->used_slugs[] = $anchor;
			return $anchor;
		}

		return $this->generate( $anchor );
	}

	/**
	 * Generate slug unik dari teks heading.
	 *
	 * @param string $text Teks heading (boleh mengandung HTML, akan dibersihkan).
	 * @return string Slug unik, siap dipakai sebagai id/#fragment.
	 */
	public function generate( string $text ): string {
		$base = sanitize_title( wp_strip_all_tags( $text ) );

		if ( '' === $base ) {
			$base = 'section';
		}

		$slug   = $base;
		$suffix = 2;

		while ( in_array( $slug, $this->used_slugs, true ) ) {
			$slug = $base . '-' . $suffix;
			++$suffix;
		}

		$this->used_slugs[] = $slug;

		return $slug;
	}
}
