# DESIGN_SYSTEM.md

> This document defines the design system used throughout the development of Lunar Core to ensure a consistent user experience across all blocks, components, and plugin interfaces.

---

## 1. Design Philosophy

Lunar Core prioritizes a design that is simple, consistent, and aligned with the WordPress interface.

Every component is designed to help users create and manage content efficiently without introducing unnecessary complexity.

---

## 2. Design Principles

All design decisions in Lunar Core follow these principles:

- Content First
- Simplicity
- Consistency
- Accessibility
- Performance
- WordPress Native

---

## 3. Editor Experience

The Block Editor experience should feel natural for WordPress users.

Each block should:

- be easy to understand;
- provide logical settings;
- use consistent terminology;
- avoid excessive configuration options.

---

## 4. Block Design

All Gutenberg Blocks should follow consistent patterns for:

- structure;
- behavior;
- placeholders;
- validation;
- user messaging.

New blocks should follow the same design patterns established by existing blocks.

---

## 5. Inspector Controls

Block settings panels should:

- follow a consistent order;
- group options by functionality;
- use native WordPress components whenever available;
- avoid unnecessary nested panels.

---

## 6. Color System

Lunar Core uses design tokens to define interface colors.

Color categories include:

- Background
- Surface
- Border
- Text
- Accent
- Success
- Warning
- Error
- Information

---

## 7. Typography

Typography within the editor and plugin interface prioritizes readability.

Visual hierarchy is established through consistent sizing, weight, and spacing.

---

## 8. Spacing

Use a consistent spacing system across all blocks and plugin interfaces.

Spacing improves readability while maintaining visual consistency.

---

## 9. Border Radius

Border radius should be applied consistently across all components.

Different radius values should only be used when there is a clear functional reason.

---

## 10. Icons

Icons should be used to help users understand interface functionality.

They should not be used as decorative elements.

---

## 11. Motion

Animations and transitions should only be used when they improve the user experience.

All animations should be:

- brief;
- unobtrusive;
- performance-friendly;
- respectful of users' reduced motion preferences.

---

## 12. Accessibility

All components should consider:

- color contrast;
- keyboard navigation;
- focus states;
- screen reader support;
- interactive target size.

Accessibility is an integral part of the design process from the very beginning.

---

## 13. Component Consistency

All Lunar Core components should follow consistent patterns for:

- structure;
- naming;
- behavior;
- styling;
- validation;
- user experience.

---

## 14. CSS Architecture

CSS in Lunar Core follows these principles:

- use CSS Custom Properties;
- use the `.lunar-` prefix;
- avoid overly specific selectors;
- support both light and dark modes;
- prioritize reuse over duplication.

---

## 15. Design Tokens

Shared design values are managed through design tokens, including:

- colors;
- spacing;
- typography;
- border radius;
- transitions;
- component sizing.

---

## 16. Future Expansion

New components should follow the existing design system.

When new design patterns are required, they should be introduced in a way that preserves overall consistency and maintainability.