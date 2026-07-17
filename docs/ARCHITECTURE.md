# ARCHITECTURE.md

> This document describes the technical architecture of Lunar Core as the foundation for building a modular, maintainable plugin that follows WordPress standards.

---

## 1. Architecture Philosophy

The architecture of Lunar Core is built on the following principles:

- Maintainability
- Scalability
- Separation of Concerns
- Performance
- WordPress Native
- Portability
- Consistency

---

## 2. Architectural Goals

Lunar Core is designed to:

- separate data from presentation;
- preserve content portability;
- support long-term development;
- minimize coupling between components;
- optimize performance;
- follow the WordPress architecture.

---

## 3. Core Responsibilities

Lunar Core is responsible for:

- Custom Post Types
- Taxonomies
- Gutenberg Blocks
- Metadata
- Schema Integration
- Business Logic
- Shared Services

The plugin is not responsible for website layout or styling.

---

## 4. Separation of Concerns

Lunar Core focuses on data and functionality.

Presentation, layout, and styling are delegated to the theme, ensuring that content remains portable and easy to maintain.

---

## 5. Information Architecture

The information architecture follows native WordPress mechanisms, including:

- Custom Post Types
- Taxonomies
- Post Meta
- Attachment Meta

New features should be built on top of these existing structures whenever possible.

---

## 6. Directory Structure

The directory structure is organized into modular components based on their responsibilities, making the project easier to maintain and extend.

---

## 7. Bootstrap Strategy

The bootstrap process is responsible only for initializing the required components.

Initialization is performed in stages to maintain optimal performance.

---

## 8. Block Architecture

All Gutenberg Blocks follow a consistent structure.

Each block has a clearly defined responsibility and should not depend on other blocks, except for intentionally designed parent-child relationships.

---

## 9. Block Registration

Blocks are registered through a centralized registration system to keep management simple and consistent.

---

## 10. Asset Loading

CSS, JavaScript, and other assets are loaded only when required.

The frontend should never load unused assets.

---

## 11. Shared Services

Functionality shared across multiple components should be placed in shared services to reduce code duplication.

---

## 12. Data Management

All data should use standard WordPress mechanisms.

Avoid creating custom storage systems when WordPress already provides an appropriate solution.

---

## 13. Performance

Performance is considered a core architectural concern from the beginning.

Key principles include:

- Conditional Asset Loading
- Lazy Loading where appropriate
- Semantic HTML
- Minimal Database Queries
- Minimal Dependencies

---

## 14. Security

All input must be:

- validated;
- sanitized;
- escaped according to the output context.

Use WordPress security APIs whenever possible.

---

## 15. Extensibility

The architecture should make it easy to add:

- Gutenberg Blocks
- Custom Post Types
- Taxonomies
- Metadata
- New Integrations

without requiring major changes to the core architecture.

---

## 16. Naming Conventions

Use consistent naming for:

- Namespaces
- Classes
- Functions
- Hooks
- CSS Classes
- Block Names
- Slugs
- Files

---

## 17. Dependency Policy

Prefer native WordPress APIs whenever possible.

External dependencies should only be introduced when they provide clear value and are genuinely necessary.

---

## 18. Lifecycle

The general initialization sequence is as follows:

1. Plugin Initialization
2. Shared Services Initialization
3. Custom Post Type Registration
4. Taxonomy Registration
5. Block Registration
6. Hook Registration
7. Frontend Rendering

---

## 19. Future Expansion

New features should follow the established architecture.

Changes to the architectural foundation should only be made when they provide clear improvements in maintainability, performance, or scalability.