# Lunar Core

The companion plugin for Lunar (LunarThemes), responsible for managing data, Custom Post Types, Taxonomies, and all Gutenberg Blocks used by the documentation website.

Lunar Core is responsible solely for **data and functionality**. Presentation and styling are handled entirely by the companion theme (Lunar). This separation is intentional—see [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md) for details.

## Features

- Custom Post Type: **Article**
- Taxonomies: **Game** and **Content Type**
- 11 ready-to-use Gutenberg Blocks:

| Block | Purpose |
| --- | --- |
| Callout | Highlight important information, warnings, tips, or notes |
| Definition List | Display terms and their definitions |
| Version/Patch Tag | Inline badge for version or update information |
| Infobox | Flexible information box with manual fields and post meta synchronization for filtering |
| Accordion | Collapsible content sections |
| Tabs | Tabbed content |
| Steps | Numbered instructions |
| Table of Contents | Automatically generated table of contents from headings (dynamic rendering) |
| Table | Data table with sorting and filtering |
| Gallery | Image grid with lightbox support |
| Timeline | Chronological timeline |

- Infobox field synchronization to post meta for search filtering without requiring mandatory fields
- Custom block category: `lunar-blocks`

## Requirements

- WordPress with the Gutenberg editor enabled
- PHP 8.0 or later
- Node.js and `@wordpress/scripts` to build from source (`npm install && npm run build`)

## Installation

1. Clone or download this repository into `wp-content/plugins/lunar-core`.
2. Run `npm install`.
3. Run `npm run build`.
4. Activate the **Lunar Core** plugin from the WordPress **Plugins** screen.

## Project Structure

```text
lunar-core/
├── src/                # Block source code (one directory per block)
├── includes/           # PHP: CPTs, Taxonomies, Shared Services, Block Registry
├── docs/               # Official architecture and project documentation
├── lunar-core.php      # Plugin entry point
└── CHANGELOG.md
```

## Architecture Documentation

All architectural decisions, design guidelines, and coding standards are documented in the [`docs/`](docs/) directory:

- `PROJECT_BRIEF.md` — Project overview and development workflow
- `PRODUCT_VISION.md` — Product vision and philosophy
- `ENGINEERING_PRINCIPLES.md` — Engineering principles
- `DESIGN_SYSTEM.md` — Design system
- `ARCHITECTURE.md` — Technical architecture
- `BLUEPRINT.md` — Project structure
- `CODING_STANDARD.md` — Coding standards
- `BLOCK_DEVELOPMENT_GUIDE.md` — Gutenberg Block development guide

## Contributing

See [`CONTRIBUTING.md`](CONTRIBUTING.md) for contribution guidelines and [`MAINTENANCE.md`](MAINTENANCE.md) for maintenance and support policies.

## License

See the `LICENSE` file in this repository.