# Changelog

All notable changes to Lunar Core are documented in this file.

This project follows the format defined by [Keep a Changelog](https://keepachangelog.com/) and uses [Semantic Versioning](https://semver.org/) for the overall plugin version (see `MAINTENANCE.md`).

> **Note:** During early development, each block maintained its own internal version (e.g. Timeline `v0.1.0` → `v0.1.2`) throughout the Deliverable/Revision stages before being marked as **LOCKED**. These versions were **not** plugin releases. This changelog records only plugin-wide releases, not the internal iteration history of individual blocks.

## [0.2.0] - 2026-07-18

### Added
- **Game Menu CPT**: Registered new `game-menu` Custom Post Type to handle gaming documentation structures.
- **Custom Taxonomies**: Added `Game` and `Content Type` taxonomies support specifically for the Game Menu ecosystem.
- **Custom Meta Fields**: Implemented secure metadata storage with the addition of `class-game-menu-meta.php` and `class-meta-fields.php`.

### Changed
- **Core Bootstrap**: Updated `lunar-core.php` core file to boot and wire up the new Game Menu features.
- **Git Configurations**: Updated `.gitignore` rules to maintain clean tracking flags for core files.

## [0.1.0] - 2026-07-18

### Added

- Custom Post Type: **Article**
- Taxonomies: **Game** and **Content Type**
- Block: Callout
- Block: Definition List
- Block: Version/Patch Tag (RichText Format)
- Block: Infobox, including field synchronization with post meta for search filtering
- Block: Accordion
- Block: Tabs
- Block: Steps
- Block: Table of Contents (dynamic rendering)
- Block: Table (custom implementation with sorting and filtering)
- Block: Gallery (with lightbox)
- Block: Timeline
- Custom block category: `lunar-blocks`
- Official Design Tokens (`DESIGN_TOKENS.md`), including the color palette, typography, and Cozy Almanac border radius

### Deferred (Not Cancelled)

- Block: Link Preview — no practical content requirement yet
- Block: Comparison/Versus — no practical content requirement yet

### Removed

- Hover Preview feature — removed because the technical complexity outweighed its benefits
- Parent-level captions for Gallery (a description shared by the entire gallery)