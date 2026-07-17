# CODING_STANDARD.md

> This document defines the coding standards used throughout the development of Lunar Core.

---

## 1. Coding Philosophy

Code should be easy to read, maintain, test, and extend.

Every implementation should support the project's architecture while prioritizing quality and consistency.

---

## 2. General Principles

- Prioritize readability.
- Avoid unnecessary complexity.
- Give each function a single responsibility.
- Avoid code duplication.
- Apply the Separation of Concerns principle.
- Write code that is easy to maintain.

---

## 3. PHP Standards

- Follow the WordPress Coding Standards.
- Use PHP 8 or later.
- Use object-oriented programming where appropriate.
- Use official WordPress APIs.
- Avoid unnecessary global state.

---

## 4. JavaScript Standards

- Use modern JavaScript.
- Use ES Modules.
- Follow Gutenberg development standards.
- Avoid additional dependencies when WordPress already provides an appropriate solution.

---

## 5. CSS Standards

- Use simple selectors.
- Avoid `!important`.
- Use the `.lunar-` prefix.
- Organize styles by component.
- Prioritize maintainability.

---

## 6. HTML Standards

- Use semantic HTML.
- Avoid unnecessary markup.
- Support accessibility and SEO.

---

## 7. Gutenberg Block Standards

All blocks should:

- follow the official Block API;
- use a consistent structure;
- generate semantic HTML;
- avoid unnecessary markup;
- follow the project's Design System.

---

## 8. Plugin Standards

Lunar Core is responsible for:

- Gutenberg Blocks
- Custom Post Types
- Taxonomies
- Metadata
- Business Logic
- Shared Services

The plugin is not responsible for website layout or styling.

---

## 9. File Organization

- Each file should have a single purpose.
- Use a consistent directory structure.
- Avoid files with multiple responsibilities.

---

## 10. Naming Conventions

Use names that are:

- descriptive;
- consistent;
- easy to understand.

These conventions apply to namespaces, classes, functions, hooks, slugs, CSS classes, and files.

---

## 11. Documentation

Comments should explain **why**, not **what**, the code does.

Important architectural decisions and significant changes should be documented.

---

## 12. Error Handling

- Fail gracefully.
- Provide fallbacks whenever possible.
- Avoid unnecessary fatal errors.

---

## 13. Security

All input must be:

- validated;
- sanitized;
- escaped according to its output context.

Use nonces, capability checks, and WordPress security APIs whenever appropriate.

---

## 14. Performance

- Load assets conditionally.
- Minimize database queries.
- Use lazy loading where appropriate.
- Avoid heavy dependencies.
- Eliminate unnecessary processing.

---

## 15. Accessibility

All components should consider:

- keyboard navigation;
- focus states;
- ARIA attributes where appropriate;
- screen reader support;
- color contrast.

---

## 16. Testing

Before a change is considered complete, test it in the following areas:

- Frontend
- Block Editor
- Responsiveness
- Accessibility
- Compatibility

---

## 17. Code Review

Every change should be reviewed for:

- Architecture
- Coding Standards
- Performance
- Security
- Consistency

---

## 18. Refactoring

Refactoring should be performed to:

- improve maintainability;
- reduce complexity;
- increase consistency.

Refactoring should not change system behavior without a clear justification.

---

## 19. Deprecation

Follow a proper deprecation process before removing existing features.

Provide a migration path when appropriate.

---

## 20. Code Quality

High-quality code should be:

- easy to read;
- easy to maintain;
- easy to test;
- easy to extend;
- loosely coupled;
- highly cohesive;
- free from unnecessary side effects.