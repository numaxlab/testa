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
The host Laravel application is visitable in http://localhost/ (if the Laravel Sail development environment is
running).

### Key Entry Points

- `src/TestaServiceProvider.php` — Registers routes, views, Livewire components, observers, policies
- `src/Admin/Filament/TestaPlugin.php` — Registers all Filament admin resources
- `routes/storefront.php` — All public-facing routes
- `config/testa.php` — Package configuration (payment types, billing defaults, OG images)

### Source Structure (`src/`)

- **Models/** — 30 Eloquent models across domains: Education (Course, CourseModule, Topic), Content (Page, Banner,
  Slide, Tier), Media (Audio, Video, Document), Membership (MembershipTier, MembershipPlan, Subscription, Benefit),
  News (Article, Event), Editorial (Review)
- **Models/Product.php** extends `NumaxLab\Lunar\Geslib\Models\Product` (not Lunar's base Product directly)
- **Models/Customer.php** extends `Lunar\Models\Customer`
- **Admin/Filament/Resources/** — 22 Filament CRUD resources organized by domain (Content, Education, Media, Membership,
  News, Sales, Editorial)
- **Admin/Filament/Extension/** — Extensions to Lunar's built-in Product and Customer Filament resources
- **Storefront/Livewire/** — 90+ Livewire page components organized by domain: Account, Auth, Bookshop, Checkout,
  Education, Editorial, Media, Membership, News, plus reusable Components
- **Storefront/Http/Controllers/** — ProcessPaymentController for payment handling
- **Observers/** — OrderObserver, CourseObserver
- **Pipelines/Order/Creation/** — TagOrder pipeline for order processing

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
