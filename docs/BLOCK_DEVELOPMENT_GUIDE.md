# BLOCK_DEVELOPMENT_GUIDE.md

> This document defines the Gutenberg Block development standards used throughout Lunar Core.

---

## 1. Purpose

This guide ensures that all Gutenberg Blocks are developed consistently, modularly, and in accordance with WordPress standards.

---

## 2. Scope

This guide covers:

- Block structure
- Naming conventions
- Rendering
- HTML
- CSS
- JavaScript
- PHP
- Asset loading
- Accessibility
- Internationalization
- Security
- Performance
- Testing

---

## 3. Block Philosophy

Each block should:

- have a single, well-defined purpose;
- solve a real-world use case;
- be modular;
- be easy to maintain;
- generate semantic HTML.

---

## 4. Block Structure

Each block should be placed in its own directory within the `src/` folder.

All blocks should follow a consistent file structure to simplify maintenance and future development.

---

## 5. File Responsibilities

Use only the files required by the block.

| File | Responsibility |
|------|----------------|
| `block.json` | Block metadata |
| `edit.*` | Editor interface |
| `save.*` | Static rendering |
| `render.*` | Dynamic rendering |
| `view.*` | Frontend behavior |
| `editor.*` | Editor styles |
| `style.*` | Frontend styles |

---

## 6. Rendering

Use either static or dynamic rendering based on the block's requirements.

The rendering strategy should consider:

- maintainability;
- performance;
- flexibility;
- content portability.

---

## 7. Editor Experience

Blocks should:

- follow Gutenberg design patterns;
- be easy to understand;
- be responsive;
- represent the frontend output as closely as possible;
- avoid unnecessary complexity.

---

## 8. Frontend Output

Frontend output should produce markup that is:

- clean;
- lightweight;
- consistent;
- semantic;
- accessible.

---

## 9. HTML Standards

- Use semantic HTML.
- Avoid unnecessary wrapper elements.
- Prioritize a structure that supports accessibility and SEO.

---

## 10. CSS Standards

- Use the `.lunar-` prefix.
- Follow a modular approach.
- Avoid overly specific selectors.
- Avoid code duplication.
- Avoid using `!important`.

---

## 11. JavaScript Standards

JavaScript should only be used when interactive behavior is required.

- Minimize side effects.
- Use WordPress APIs whenever available.
- Avoid excessive DOM manipulation.

---

## 12. PHP Standards

- Use the `Lunar\` namespace.
- Follow the WordPress Coding Standards.
- Use official WordPress APIs.
- Apply the Single Responsibility Principle where appropriate.

---

## 13. Asset Loading

- Load assets only when required.
- Separate editor and frontend assets.
- Avoid shipping unused assets.

---

## 14. Internationalization

All user-facing text must support internationalization using the official WordPress localization system.

---

## 15. Accessibility

All blocks should consider:

- keyboard navigation;
- visible focus states;
- screen reader support where appropriate;
- proper heading hierarchy;
- ARIA attributes where necessary.

---

## 16. Security

All blocks must:

- validate input;
- sanitize data;
- escape output according to its context;
- follow WordPress security best practices.

---

## 17. Performance

Each block should:

- generate efficient HTML;
- load the smallest possible assets;
- avoid unnecessary processing;
- avoid loading assets when the block is not in use.

---

## 18. Error Handling

Blocks should:

- fail gracefully;
- avoid breaking the Block Editor;
- avoid breaking the frontend;
- provide fallbacks whenever possible.

---

## 19. Backward Compatibility

Changes should not introduce Block Validation Errors without a clear migration strategy.

Compatibility with existing published content should always be maintained.

---

## 20. Reusability

Shared components and utilities may be extracted into reusable modules when appropriate.

---

## 21. Testing

Before a block is considered complete, it should be tested in the following areas:

- Block Editor
- Frontend
- Responsiveness
- Accessibility
- Compatibility

---

## 22. Definition of Done

A block is considered complete when it:

- meets the development standards;
- builds successfully;
- functions correctly in the editor;
- functions correctly on the frontend;
- passes essential testing;
- has updated documentation when necessary.

---

## 23. Best Practices

- Follow established patterns.
- Minimize code duplication.
- Prefer simple solutions.
- Optimize performance.
- Document important implementation decisions.