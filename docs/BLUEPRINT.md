# BLUEPRINT.md

> This document describes the structure of the Lunar Core project and how its architecture is translated into files, directories, and components.

---

## 1. Purpose

The Blueprint serves as the reference for organizing the project structure consistently.

It bridges the gap between architectural design and implementation without covering code-level details or business logic.

---

## 2. Scope

The Blueprint covers:

- Directory structure
- Directory responsibilities
- Bootstrap
- Lifecycle
- Block Registration
- Asset Loading
- Namespace
- Build Process
- Dependency Rules
- Extension Points

The Blueprint does not cover:

- PHP implementation
- JavaScript implementation
- CSS implementation
- Business logic
- User interface details
- Algorithms

---

## 3. Project Identity

| Item | Value |
|------|-------|
| Brand | Lunar |
| Plugin | Lunar Core |
| Plugin Slug | `lunar-core` |
| PHP Namespace | `Lunar\...` |
| CSS Prefix | `.lunar-` |
| Asset Handle | `lunar-` |

---

## 4. Project Structure

```text
lunar-core/
├── assets/
├── build/
├── includes/
├── languages/
├── src/
├── vendor/
├── composer.json
├── package.json
└── lunar-core.php
```

The project is organized into directories based on component responsibilities to improve maintainability and extensibility.

---

## 5. Directory Responsibilities

| Directory | Responsibility |
|-----------|----------------|
| assets | Static assets |
| build | Compiled block assets |
| includes | PHP classes and services |
| languages | Translation files |
| src | Block source code |
| vendor | Composer dependencies |

---

## 6. Bootstrap Flow

```text
Plugin Activated
        ↓
Environment Check
        ↓
Load Dependencies
        ↓
Initialize Core
        ↓
Register Services
        ↓
Register Content Types
        ↓
Register Blocks
        ↓
Plugin Ready
```

---

## 7. Lifecycle

```text
Activation
↓
Bootstrap
↓
Initialization
↓
Runtime
↓
Deactivation
↓
Uninstall
```

---

## 8. Block Registration

```text
Source Block
↓
Build
↓
Generated Assets
↓
Registration
↓
Editor
↓
Frontend
```

All Gutenberg Blocks are registered through the plugin's centralized registration system.

---

## 9. Asset Loading

```text
Editor
↓
Editor Assets

Frontend
↓
Frontend Assets
```

Assets are loaded only when required.

---

## 10. Build Process

```text
src/
↓
Build Process
↓
build/
↓
WordPress
```

The build process follows the standard WordPress development workflow.

---

## 11. Namespace

Use consistent namespaces throughout the project.

- `Lunar\`
- `Lunar\Blocks`
- `Lunar\Services`
- `Lunar\Content`
- `Lunar\Schema`

---

## 12. Naming Conventions

Use consistent naming for:

- Blocks
- Directories
- Namespaces
- CSS Classes
- Asset Handles
- Hooks
- Files

---

## 13. Dependency Rules

- Use WordPress APIs whenever possible.
- Avoid circular dependencies.
- Avoid tight coupling.
- Keep business logic out of the bootstrap process.
- Place shared functionality in shared services.

---

## 14. Extension Points

The project is designed to be extended through:

- Gutenberg Blocks
- Custom Post Types
- Taxonomies
- Metadata
- Services
- New integrations

without requiring changes to the project's core foundation.

---

## 15. Guiding Principles

The Blueprint is based on the following principles:

- Modular
- Maintainable
- Extensible
- Consistent
- Predictable
- Readable
- Minimal Coupling
- High Cohesion
- Separation of Concerns