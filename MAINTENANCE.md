# Maintenance Policy — Lunar Core

## Project Status

Lunar Core is actively maintained and primarily developed to support a documentation website. The project evolves based on real content requirements rather than feature count (see `PRODUCT_VISION.md`, *Non-Goals*).

## Maintainer

This project is maintained by a single developer. There is currently no separate maintenance team. Response times for issues and pull requests depend on the maintainer's availability. This project does not provide commercial support or a formal SLA.

## Versioning Policy

Lunar Core follows [Semantic Versioning](https://semver.org/) (`MAJOR.MINOR.PATCH`) for the overall plugin version.

- **MAJOR** — Changes that may introduce backward-incompatible behavior.
- **MINOR** — New blocks or features that maintain backward compatibility.
- **PATCH** — Bug fixes and visual or CSS improvements that do not affect the underlying data structure.

Each block also has its own internal development version (for example, `v0.1.0` → `v0.1.2`) during the Deliverable/Revision stages before being marked as **LOCKED**. These are internal iteration numbers and are **not** plugin release versions.

## Backward Compatibility Policy

As described in `BLOCK_DEVELOPMENT_GUIDE.md` §19, changes to **LOCKED** blocks that may affect saved markup or block attributes are **not** made without a clear migration strategy. Purely visual or CSS-only changes that do not affect block serialization are considered safe and do not require migration.

## Deprecation Policy

As defined in `CODING_STANDARD.md` §21, features are deprecated before removal. Deprecated features are documented in the changelog and/or source code comments, with a migration path provided when necessary, before being removed in a future **MAJOR** release.

## Supported WordPress and PHP Versions

Lunar Core supports the WordPress and PHP versions officially supported by WordPress.org at the time of each release (minimum PHP 8.0; see the `lunar-core.php` plugin header). Support for end-of-life WordPress or PHP versions is not guaranteed.

## Security

If you discover a security vulnerability, please do not open a public issue. Instead, contact the maintainer directly using the contact information provided in the `README.md` file or the repository owner's GitHub profile.

## Roadmap

The long-term roadmap follows `PROJECT_BRIEF.md` §1 (the Lunar ecosystem: LunarThemes, Lunar Core, Lunar Patterns, and Lunar Icons). No release dates are promised for planned roadmap items.