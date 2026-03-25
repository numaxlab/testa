# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Testa** is a Laravel package (`numaxlab/testa`) that extends LunarPHP e-commerce for bookshops using Geslib as their
management system. It adds education, membership, media library, news/events, and editorial features on top of Lunar's
core e-commerce.

Built for: PHP 8.4, Laravel 12, LunarPHP, Filament 3, Livewire 3, TailwindCSS 4 (via `@numaxlab/atomic`).

## Commands

```bash
# Run tests
composer test
# or
./vendor/bin/pest

# Run a single test file
./vendor/bin/pest tests/Unit/Models/Content/PageTest.php

# Run a single test by name
./vendor/bin/pest --filter="test name"
```

## Architecture

This is a **Laravel package** (not a standalone app). It is installed into a host Laravel application via Composer.
The host Laravel application is visitable in http://localhost/. Artisan commands are available in the host app's
context (../traficantes.net) with sail.

### Key Entry Points

- `src/TestaServiceProvider.php` — Registers routes, views, Livewire components, observers, policies
- `src/Admin/Filament/TestaPlugin.php` — Registers all Filament admin resources
- `routes/storefront.php` — All public-facing routes
- `config/testa.php` — Package configuration (payment types, billing defaults, OG images)

### Application layer

Business logic lives in `src/Storefront/UseCases/`. One class per operation, one public
method (`execute`). Class names are verb-noun phrases: `SignupMember`,
`PlaceOrder`, `CancelMembership`.

Use Cases accept DTOs or Eloquent models as arguments. They never accept
Livewire Form Objects or raw request arrays directly.

### Data transfer

DTOs live in `src/Storefront/Data/`. They are `final readonly` classes with a static
`fromForm(FormObject $form): self` factory when a Livewire form is the source.

### Queries

Read-only data retrieval lives in `src/Storefront/Queries/`. No mutations. Methods return
Collections, Paginators, or Eloquent models.

### Livewire components

Components are responsible for: validation, building DTOs from Form Objects,
calling Use Cases, and redirecting or dispatching events.
Components must NOT contain: Eloquent queries inline, business logic, or
array-building for meta/payload structures.

### What stays in models

Relationships, scopes, casts, and accessors only.
No business logic in models.

### Testing expectations

Every UseCase must have a corresponding unit test in `tests/Unit/Storefront/UseCases/`.
Use Cases are tested without booting Livewire.

### Filament

Filament CRUD resources, extensions, pages and actions live in `src/Admin/Filament/`.

### Route Domains

Routes in `routes/storefront.php` are organized by URL prefix:

- `/libreria` — Bookshop (products, sections, topics, search)
- `/editorial` — Publisher/author pages, collections
- `/formacion` — Education/courses
- `/mediateca` — Media library (audio, video, documents)
- `/actualidad` — News and events
- `/info` — Static pages
- Auth, account, checkout, and membership routes at root level

### Dependencies on Internal Packages

- `numaxlab/lunar-geslib` — Core Geslib integration, provides Author model and ProductIndexer
- `@numaxlab/atomic` — NPM design system with TailwindCSS components and Alpine.js utilities
- `@numaxlab/atomic-laravel` — Laravel Blade components for the atomic design system

### Testing

Uses Pest 3 with Orchestra Testbench. Tests use SQLite in-memory database. Factories exist for Course, Topic, Page, and
Banner. When running the hole Pest test suite better run it in parallel mode to avoid reaching memory limit.

### Multi-language

Models use `spatie/laravel-translatable` for multi-language support. Filament admin uses
`SpatieLaravelTranslatablePlugin` with configurable locales.
