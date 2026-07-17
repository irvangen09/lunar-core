# Contributing to Lunar Core

Thank you for your interest in contributing to Lunar Core. Although this project is primarily developed for a documentation website, external contributions are welcome as long as they align with the project's direction and standards.

## Before You Start

Please read the documents in the [`docs/`](docs/) directory, especially:

- `PROJECT_BRIEF.md` — project goals and priorities
- `ENGINEERING_PRINCIPLES.md` — principles behind every architectural decision
- `CODING_STANDARD.md` — required coding standards
- `BLOCK_DEVELOPMENT_GUIDE.md` — for Gutenberg Block contributions

Any decisions marked as **LOCKED** in these documents are considered final and should not be changed without a strong technical or architectural reason.

## Reporting Bugs

Open a new issue and include:

- WordPress, PHP, and Lunar Core versions
- Steps to reproduce
- Expected behavior and actual behavior
- Screenshots or error logs, if applicable

## Proposing Features

Open an issue with the `enhancement` label and explain the real use case behind the proposal (not simply "this feature would be nice"). This follows the principle defined in `ENGINEERING_PRINCIPLES.md` §1 (*Write with Purpose*).

Keep in mind that several features have intentionally been **deferred or skipped** (such as Spoiler Toggle, Link Preview, and Comparison/Versus) because there was no practical content requirement. New feature proposals should therefore include a concrete use case.

## Block Development Workflow

Every new block follows the development workflow defined in `PROJECT_BRIEF.md` §8:

```
Goal → Concept → Tasks → Deliverable → Review → Revision → LOCKED
```

Do not skip any stage. Pull requests for new blocks should indicate the current development stage.

## Coding Conventions (Required)

- Child blocks must always be placed inside their parent directory (`src/{block}/item/`), not as sibling folders.
- Each block family must have a single `index.js` that registers both the parent and child blocks. The block `icon` must be registered in `index.js`, not in `block.json`.
- The block category must always be `lunar-blocks`.
- RichText attributes must use `"type": "rich-text"` and `"source": "rich-text"`.
- Every JS, SCSS, and PHP file must include the header comment `/** Location: lunar-core/src/... */`.
- CSS custom properties must always include an explicit fallback value, for example: `var(--token, fallback-hex)`. See `DESIGN_SYSTEM.md` and `DESIGN_TOKENS.md` for the official values.
- Follow `CODING_STANDARD.md` for PHP, JavaScript, CSS, naming conventions, and security practices.

## Submitting a Pull Request

1. Fork the repository and create a branch from `main`.
2. Ensure `npm run build` completes without introducing new errors or warnings.
3. Clearly describe your changes, including which blocks or features are affected.
4. Any change that modifies stored data or saved block markup **must** describe its potential impact on existing published content (`BLOCK_DEVELOPMENT_GUIDE.md` §19, *Backward Compatibility*).
5. Changes outside the scope of the pull request (for example, unrelated code cleanup) should be submitted as a separate pull request (`CODING_STANDARD.md` §23, *Change Scope Policy*).

## Code of Conduct

Be respectful and constructive in issue and pull request discussions. Technical disagreements are normal—criticize ideas, not people.
