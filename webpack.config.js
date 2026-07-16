/**
 * Lokasi: lunar-core/webpack.config.js
 *
 * wp-scripts secara default hanya membuat build entry untuk folder
 * yang punya block.json (deteksi otomatis Block API). Version/Patch Tag
 * bukan block (RichText Format), jadi entry-nya perlu ditambahkan manual
 * di sini supaya ikut ter-build ke build/version-tag/index.js.
 */

const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );

const defaultEntry =
	typeof defaultConfig.entry === 'function' ? defaultConfig.entry() : defaultConfig.entry;

module.exports = {
	...defaultConfig,
	entry: {
		...defaultEntry,
		'version-tag/index': path.resolve( process.cwd(), 'src/version-tag', 'index.js' ),
	},
};
