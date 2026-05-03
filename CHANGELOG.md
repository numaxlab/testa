# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0-beta.10] - 2026-04-25

### Fixed
- Null-safe operator on `$product->brand` access in product body view to prevent errors when brand is absent.
- `SearchPage` Livewire component: initialise `$q` property to `null` to avoid uninitialized property warnings.
- `SearchProducts` query: skip Meilisearch-specific filter callback when Scout driver is not `meilisearch`, enabling the package to work with other search drivers without runtime errors.
- `testa.css`: removed inline `@import` for icomoon font (must be imported by the host app to avoid Vite vendor path resolution issues).

### Changed
- Pinned `numaxlab/lunar-geslib` dependency to `^1.0@beta` (was `*`).
- Updated `pestphp/pest-plugin-livewire` dev dependency to `^3.0` (was `3.x-dev`).

## [1.0.0-beta.9] - 2025-01-01

### Notes
- Earlier beta releases. See git history for details.
