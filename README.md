<div align="center">

# Flavor — Restaurant Management System

**A clean-architecture restaurant management platform**
Menu, ordering, reservations, inventory and staff operations in one Laravel application

[![Laravel](https://img.shields.io/badge/Laravel-10-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.1+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Redis](https://img.shields.io/badge/Redis-optional%20cache%20%2B%20queue-DC382D?style=flat-square&logo=redis&logoColor=white)](https://redis.io)

**117** API endpoints · **15** domain entities · **20** services · **11** policies · **26** migrations

[API Reference](docs/API.md) · [Postman Collection](docs/flavor-api.postman_collection.json) · [Interface Gallery](docs/screenshots/README.md) · [Diagrams](docs/diagrams/index.html) · [العربية](README.ar.md)

</div>

---

## Table of contents

1. [Overview](#overview)
2. [Architecture](#architecture)
3. [Directory layout](#directory-layout)
4. [Domain modules](#domain-modules)
5. [Roles and abilities](#roles-and-abilities)
6. [Web authentication and cookies](#web-authentication-and-cookies)
7. [The API](#the-api)
8. [Notifications](#notifications)
9. [Diagrams](#diagrams)
10. [Localisation](#localisation)
11. [Theming and design system](#theming-and-design-system)
12. [Data integrity](#data-integrity)
13. [Background work](#background-work)
14. [Caching](#caching)
15. [Installation](#installation)
16. [Demo accounts](#demo-accounts)
17. [Further reading](#further-reading)

---

## Overview

Flavor is a restaurant management system built on Laravel 10. It covers the public menu, customer ordering and table booking, and a staff workspace for the kitchen, the floor, inventory, people and reporting.

The application ships two delivery mechanisms over one shared domain layer: a server-rendered, session-authenticated web application for the browser, and a versioned, token-authenticated JSON API (`/api/v1`) for mobile and third-party clients. Neither duplicates the other's business logic — both call the same `Service` classes underneath.

---

## Architecture

The application follows a layered clean architecture. Each layer has a single responsibility and depends only on the layer below it.

```
HTTP request
    |
    v
Route (routes/web.php)
    |
    v
Middleware  (locale, theme, ability, account status)
    |
    v
Form Request (validation and authorisation)
    |
    v
Controller  (thin: translates HTTP into a service call)
    |
    v
Service     (business rules, transactions, events)
    |
    v
Repository  (query construction, locking, aggregates)
    |
    v
Model       (attributes, casts, relationships)
    |
    v
Database
```

Supporting elements sit alongside this pipeline:

| Element | Responsibility |
| --- | --- |
| DTO | Carries validated input from a Form Request into a service with typed properties |
| Enum | Encodes every fixed set of values, including its label, allowed transitions and display tone |
| Event and Listener | Decouples side effects such as mail and stock alerts from the main transaction |
| Policy | Authorises a single action on a single model |
| Job and Command | Runs scheduled or deferred work |
| Support class | Shared value objects and helpers such as `Money`, `QueryOptions` and `Ticket` |
| Trait | Cross cutting model behaviour such as `Filterable`, `HasTranslations` and `InteractsWithCache` |

Controllers never contain business rules, never build queries and never touch the database directly. Services never read the request object. Repositories never decide policy.

---

## Directory layout

```
app/
├── Console/Commands/        Artisan commands used by the scheduler
├── DTOs/                    Typed input objects built from validated request data
├── Enums/                   Fixed value sets with labels, transitions and tones
├── Events/                  Domain events raised by services
├── Exceptions/Domain/       Business rule exceptions rendered as user facing errors
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   ├── Auth/        Register, login/logout, verification, password reset
│   │   │   ├── Catalog/     Public menu, offers, categories, tables, locations
│   │   │   ├── Account/     Customer area: cart, checkout, orders, reservations, ratings
│   │   │   └── Manage/      Staff workspace, one controller per resource
│   │   └── Web/
│   │       ├── Account/     Customer area
│   │       ├── Auth/        Sign in, registration, verification, password reset
│   │       ├── Manage/      Staff workspace
│   │       └── Site/        Public pages
│   ├── Middleware/          Locale, theme, ability, account status (web and API variants)
│   ├── Requests/            Form requests grouped by area, shared between web and API
│   ├── Resources/           API Resource classes — one per model, JSON shaping only
│   └── Responses/           ApiResponse trait — the shared {success, message, data} envelope
├── Jobs/                    Queued work
├── Listeners/               Queued event handlers
├── Mail/                    Mailables built on a shared base
├── Models/                  Eloquent models
├── Notifications/           Database + mail notifications, grouped by domain
├── Policies/                Per model authorisation
├── Providers/               Service, auth, event, route and repository bindings
├── Repositories/
│   ├── Contracts/           Interfaces bound in the container
│   └── Eloquent/            Concrete implementations
├── Services/
│   ├── Catalog/             Meals, offers, categories, ingredients, tables, locations
│   ├── Dashboard/           Aggregation for the workspace and reports
│   ├── Inventory/           Stock requirements, consumption and restoration
│   ├── Media/               Picture handling
│   ├── People/              Customers, employees, auth, ratings, maintenance
│   ├── Reservations/        Availability and booking lifecycle
│   ├── Sales/               Cart, checkout and order lifecycle
│   └── Support/             Cache facade wrapper, notification helpers
└── Support/                 Value objects, query options and model traits

resources/views/
├── layouts/                 base, site, auth, account, manage
├── components/              24 reusable Blade components, including the notification bell
├── partials/                header, footer, sidebar, topbar, account navigation
├── site/                    Home, menu, offers
├── auth/                    Sign in, register, verify, password reset
├── account/                 Dashboard, cart, checkout, orders, reservations, profile
├── manage/                  Workspace dashboard, CRUD screens, reports
├── notifications/           The full notification centre page
├── emails/                  Shared transactional email template
└── errors/                  403, 404, 419, 429, 500, 503

public/assets/
├── css/app.css              Design tokens and every component style
├── js/app.js                Theme, menus, tabs, toasts, charts, cookie consent
└── img/                     Brand, icon sprite, illustrations, patterns

lang/{en,ar,fr}/             app, flash, errors, domain, enums, mail, notifications and Laravel defaults
database/
├── migrations/              Original schema plus translations, jobs, sessions, tokens and notifications
├── factories/               12 model factories
└── seeders/                 13 seeders producing a complete demo restaurant
docs/
├── diagrams/                 Class, object, state chart, use case, activity and sequence diagrams (hand-authored SVG, no tooling required)
├── restaurant-diagrams/      A second, schema-derived set of 41 Mermaid diagrams (use case, activity, sequence, state) covering every role
├── Draw io/                  Editable .drawio sources for the ERD and class diagrams
├── reports/                   The original project report (Word, PDF and the presentation slides)
├── screenshots/               Interface gallery — every screen in the application, captured and captioned by role
├── ANALYSIS.md                The original requirements, translated and mapped onto this codebase
├── API.md                     Full API reference
├── NOTIFICATIONS.md           How the notification system is built and how to extend it
├── PACKAGES.md                 Dependencies and every command needed to run the project
├── REQUIREMENTS.md            Each stated requirement and where it is satisfied in the codebase
├── CHANGELOG.md                Every change made during both rebuilds
└── flavor-api.postman_collection.json   117-request Postman collection for the API
```

---

## Domain modules

### Catalog

Meals carry a preparation cost derived from their recipe, a margin percentage and a computed selling price. A meal is orderable only when its availability flag is set and every ingredient in its recipe has enough stock for at least one portion. Offers bundle meals at a discount and inherit the same stock rules. Every meal and offer photo renders through the same `.media-hero`/`.dish__media`/`.media-square` treatment — a fixed aspect ratio with `object-fit: cover` — so cards and detail pages stay visually consistent no matter what size photo was uploaded, and clicking any of them opens a full-size lightbox view.

### Sales

The cart is keyed by the authenticated user's id in the cache layer — deliberately not the PHP session, so it works identically whether the request comes from the browser or a stateless API token. Checkout runs inside a database transaction: it locks the ingredient rows, validates availability, writes the order and its lines, consumes stock and raises the confirmation event. Order status follows an explicit state machine defined on the `OrderStatus` enum. Cancellation restores the consumed stock.

### Reservations

Table availability is resolved by comparing requested time ranges against existing bookings with a row lock, which prevents two concurrent requests from taking the same table. Party size is validated against the combined capacity of the selected tables. Customers who cancel more often than the configured limit are blocked from booking for a configurable number of days.

### Inventory

Every recipe line contributes to a requirement map. The stock service merges requirements across an entire order, checks them in one pass and then consumes or restores them atomically. When an ingredient falls to or below its threshold, a low stock event is raised and a notification is queued.

### People

Customers, employees and their user accounts are managed together. Creating an employee provisions the user account, assigns the position and mails the first sign in credentials.

### Dashboard and reporting

The dashboard aggregates revenue, order counts, occupancy, stock health and menu performance. Charts are rendered with Chart.js and repaint themselves when the theme changes. Finance, inventory and menu reports are printable.

---

## Roles and abilities

`user_type` separates managers, employees and customers. Employees additionally carry a `position`, and each position maps to a set of abilities:

| Position | catalog | inventory | floor | sales | people | reports |
| --- | --- | --- | --- | --- | --- | --- |
| Manager | yes | yes | yes | yes | yes | yes |
| Chef | yes | yes | no | no | no | no |
| Waiter | no | no | yes | yes | no | no |
| Delivery | no | no | no | yes | no | no |
| Security | no | no | yes | no | no | no |

Route groups under `/manage` are guarded by `ability:<name>`. The sidebar renders only the sections the signed in user can reach, and every model action is additionally checked by a policy. Customers reach `/account` through the `customer` middleware and never see the workspace.

---

## Web authentication and cookies

Authentication uses Laravel's session guard with a database session driver.

| Cookie | Purpose | Lifetime | Encrypted | Readable by JavaScript |
| --- | --- | --- | --- | --- |
| `flavor_session` | Session identifier | 120 minutes | yes | no |
| `remember_web_*` | Persistent sign in when the person ticks the box | 5 years | yes | no |
| `XSRF-TOKEN` | CSRF token for form submissions | session | yes | no |
| `flavor_locale` | Selected interface language | 1 year | no | yes |
| `flavor_theme` | Light or dark preference | 1 year | no | yes |
| `flavor_cookie_consent` | Records the consent choice | 1 year | no | yes |

The three preference cookies are listed in `EncryptCookies::$except` so the browser can read and write them without a round trip. They carry no personal data. `SetLocale` writes the locale cookie on the response whenever it differs from the resolved locale, and `PreferredTheme` shares the theme with every view so the correct palette is present in the first painted frame rather than flashing after JavaScript runs.

A consent bar appears until a choice is recorded. Choosing essentials only still keeps the session and CSRF cookies, which the application cannot operate without.

Additional account protections:

- Email verification through a six digit code held in the cache, not a third party package.
- Rate limiting on sign in, registration, verification resend and checkout.
- `EnsureUserIsActive` signs out and blocks any account whose status has been disabled.

---

## The API

Everything under `/api/v1` is a thin JSON layer over the same `Service`/`Repository`/`Policy` classes the web application uses — no business rule exists twice. Full reference: **[docs/API.md](docs/API.md)**. Importable, fully documented collection (117 requests): **[docs/flavor-api.postman_collection.json](docs/flavor-api.postman_collection.json)**.

- **Auth** — [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum) personal access tokens (`Authorization: Bearer <token>`), completely independent of the web session guard. Register, log in, verify email and reset a password without ever touching a cookie.
- **Response shape** — every endpoint returns `{ success, message, data }`, with a `meta` block on paginated lists. Errors carry a matching HTTP status and, for validation failures, a field-keyed `errors` object.
- **Search, filter, sort, pagination** — every list endpoint accepts `search`, `filters[column]`, `sort_by`/`sort_dir` and `per_page`, handled centrally by `App\Support\QueryOptions` and the `Filterable` model trait — the same mechanism the web workspace's toolbars already used, now exposed over HTTP.
- **Reporting** — the dashboard, finance/inventory/menu reports, and three analytics endpoints added in this pass: top-selling meals, top-selling offers, and most-consumed ingredients.
- **Language-aware by default** — every user carries a `preferred_locale`; validation errors, notifications and email all render in it automatically, no header required for an authenticated request.

---

## Notifications

Every domain event that matters to a person reaches them on two channels — email and an in-app notification feed — in their own language, not the acting user's. Full write-up: **[docs/NOTIFICATIONS.md](docs/NOTIFICATIONS.md)**.

- A shared `App\Notifications\FlavorNotification` base class means a new notification type is a handful of lines: it reuses the existing `Mailable` for email and resolves its own short copy for the database record.
- Recipients are resolved by **ability**, not a hard-coded role — a new order alert reaches everyone who currently holds the `sales` ability, automatically.
- High-frequency staff alerts (new order, new reservation) are database-only by design; customer lifecycle events (order confirmed, status changed, reservation confirmed, low stock, new hire) send both mail and an in-app entry.
- Reachable from the web (a bell in both the customer and staff headers, plus a full notification centre) and the API (`GET /notifications`, `.../unread-count`, `POST .../read`, `POST .../read-all`, `DELETE .../{id}`).

---

## Diagrams

**[docs/diagrams/index.html](docs/diagrams/index.html)** is a gallery of twelve UML diagrams covering the system's core scenarios — open it in any browser, no diagramming tool required. Every diagram is hand-authored SVG in a static HTML page, styled with the same colour tokens as the application.

| Type | Pages |
| --- | --- |
| Class diagram | All fifteen entities and how they relate |
| Object diagram | A concrete snapshot: one confirmed delivery order and everything attached to it |
| State charts | Order status, Reservation status |
| Use case diagrams | Guest & Customer, Staff workspace (by position) |
| Activity diagrams | Registration & verification, Checkout, Reservation booking |
| Sequence diagrams | Login (API token issuance), Checkout → notification fan-out, Order status update |

A second, wider set lives at **[docs/restaurant-diagrams/index.html](docs/restaurant-diagrams/index.html)**: 41 Mermaid diagrams generated directly from the migrations, seeders and enums, covering use case, activity, sequence and state diagrams for every role, including seven cross-role end-to-end scenarios. The `.mmd` source files can be pasted into any Mermaid-aware editor; editable `.drawio` sources for the ERD and class diagrams are kept in **[docs/Draw io/](docs/Draw%20io/)**.

For the interface itself rather than its models, **[docs/screenshots/README.md](docs/screenshots/README.md)** is a captioned gallery of every screen, grouped by area and by staff position.

---

## Localisation

Three locales ship with the application: English, Arabic and French. Arabic renders right to left; the layout mirrors through logical CSS properties rather than a separate stylesheet.

Two distinct kinds of text are translated.

**Interface strings** live in `lang/{locale}/`, split by namespace: `app` for the interface, `flash` for confirmations, `errors` for failures, `domain` for email and document labels, `enums` for enumerated values, `mail` for transactional messages and `notifications` for the in-app notification feed.

**Database content** is translated through a polymorphic `translations` table. Any model using the `HasTranslations` trait declares its translatable fields and gains `t('field')`, which resolves the current locale and falls back to the base column. Management forms expose a tabbed control that writes all locales in one submission. Resolved translations are cached for twelve hours under a tag that is flushed on write.

The active locale is resolved in this order: cookie, session, browser `Accept-Language`, configured default.

---

## Theming and design system

The palette is defined once as CSS custom properties and switched by a `data-theme` attribute on the root element.

| Token | Light | Dark |
| --- | --- | --- |
| Primary | `#d97706` | `#f59e0b` |
| Primary hover | `#b45309` | `#fbbf24` |
| Primary light | `#fef3c7` | `#451a03` |
| Secondary | `#4d7c0f` | `#a3e635` |
| Background | `#fffbf5` | `#111827` |
| Surface | `#ffffff` | `#1f2937` |
| Surface secondary | `#f9fafb` | `#374151` |
| Text | `#1f2937` | `#f9fafb` |
| Text secondary | `#6b7280` | `#d1d5db` |
| Border | `#e5e7eb` | `#374151` |
| Success | `#16a34a` | `#22c55e` |
| Warning | `#f59e0b` | `#fbbf24` |
| Danger | `#dc2626` | `#ef4444` |
| Info | `#2563eb` | `#60a5fa` |

The brand identity stays burnt orange in both modes; only surfaces and text invert. Status colours are functional rather than decorative and are always applied as a soft background with a matching foreground, never as a saturated fill.

Typography pairs Fraunces for headings, Plus Jakarta Sans for interface text and JetBrains Mono for figures, codes and references. Arabic uses IBM Plex Sans Arabic across all three roles.

The recurring visual motif is the kitchen ticket: a notched card with a monospaced code used for orders, reservations, bundles and cost breakdowns, which ties the customer receipt and the kitchen pass to the same object.

Assets are plain CSS and vanilla JavaScript. There is no build step and no Node dependency.

---

## Data integrity

- Checkout, stock consumption, stock restoration and table assignment all run inside transactions.
- Ingredient rows and table rows are locked with `lockForUpdate` before any availability decision, so concurrent requests cannot oversell stock or double book a table.
- Status changes are validated against the transition map on the relevant enum; an invalid move raises a domain exception rather than writing a bad row.
- Orders use soft deletes so a cancellation can be reviewed and restored, with stock handled correctly in both directions.
- Search input is sanitised and `%`, `_` and `\` are escaped before reaching a `LIKE` clause.
- Page size is clamped to protect the database from a crafted `per_page` value.

---

## Background work

| Job | Trigger | Purpose |
| --- | --- | --- |
| `SyncMenuAvailability` | Hourly | Flags meals whose ingredients ran out and restores them when stock returns |
| `LiftExpiredBans` | Daily | Clears customer booking bans that have run their course |
| `BuildDailySnapshot` | Nightly | Caches the aggregates the dashboard reads |

Mail and stock alerts are dispatched through queued listeners on dedicated queues so a slow mail server never delays a checkout. The default queue connection is `sync` (runs jobs immediately, inline, no worker or Redis needed); switch `QUEUE_CONNECTION` to `database` or `redis` for a production-style non-blocking queue, and run `php artisan queue:work`.

Commands: `flavor:sync-availability`, `flavor:lift-bans`, `flavor:daily-snapshot`.

---

## Caching

The `.env.example` default is the `file` cache driver, which needs no extra service. `App\Services\Support\CacheService`, used for the catalog/dashboard/statistics caching described below, catches any cache failure and falls through to the underlying query rather than failing the request — safe to point at Redis in production even if it hiccups. That fallback is specific to this wrapper, though: framework-level features that talk to the cache directly, like the `throttle:auth` rate limiter on login, always use whatever `CACHE_DRIVER` is configured and will fail loudly if it points at a Redis server that is not running — which is the correct behaviour for a security control, and the reason the default is `file` rather than `redis`. Cache entries are grouped by tag so a write to a meal flushes only the catalog group. Profiles and their durations in minutes are declared in `config/flavor.php`: menu 10, catalog 60, dashboard 5, statistics 5, reports 15, lookups 720.

---

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate

# configure DB_* and REDIS_* in .env, then
php artisan migrate --seed
php artisan storage:link

php artisan serve
php artisan queue:work --queue=mail,reports,maintenance,default
```

For scheduled work, add the Laravel scheduler to cron:

```
* * * * * cd /path/to/flavor && php artisan schedule:run >> /dev/null 2>&1
```

A full command reference is in [docs/PACKAGES.md](docs/PACKAGES.md).

To exercise the API without the web UI, import [docs/flavor-api.postman_collection.json](docs/flavor-api.postman_collection.json) into Postman, call **Auth > Login** with any demo account below, and copy the returned token into the collection's `token` variable.

---

## Demo accounts

Seeding creates a working restaurant: locations, categories, thirty ingredients, eighteen dishes with recipes and real photography, four photographed offers, twenty four tables, staff covering every position, eight customers, reservations across past and future dates, forty two orders and maintenance records.

Photos are wired in automatically — no manual step. `MealSeeder` and `OfferSeeder` look for `storage/app/public/{meals|offers}/<slug>.{jpg,jpeg,png,webp}` for each item and link whatever they find into the `pictures` table; every slug already has a matching photo bundled in this repository, so a fresh `php artisan migrate --seed` produces a fully photographed menu with nothing further to source. Anything still missing a photo prints a one-line warning at the end of the seed run instead of failing silently.

All demo accounts use the password `password`, on both the web and the API.

| Role | Email | Reaches |
| --- | --- | --- |
| Manager | `manager@flavor.test` | Everything |
| Chef | `chef@flavor.test` | Kitchen and inventory |
| Waiter | `waiter@flavor.test` | Floor and sales |
| Delivery | `delivery@flavor.test` | Sales |
| Security | `security@flavor.test` | Floor |
| Customer | `customer@flavor.test` | Customer area |

---

## Further reading

| Document | Contents |
| --- | --- |
| [README.ar.md](README.ar.md) | Arabic edition of this document |
| [docs/screenshots/README.md](docs/screenshots/README.md) | Interface gallery — every screen in the application, captured and captioned by area and by staff position |
| [docs/API.md](docs/API.md) | Full API reference: every endpoint, the response envelope, search/filter/sort/pagination, auth flow, error catalogue |
| [docs/NOTIFICATIONS.md](docs/NOTIFICATIONS.md) | How the mail + database notification system is built, and how to add a new notification type |
| [docs/diagrams/index.html](docs/diagrams/index.html) | Class, object, state chart, use case, activity and sequence diagrams for the system's core scenarios |
| [docs/restaurant-diagrams/index.html](docs/restaurant-diagrams/index.html) | A second, schema-derived set of 41 Mermaid diagrams covering every role and seven end-to-end scenarios |
| [docs/ANALYSIS.md](docs/ANALYSIS.md) | The original project's requirements analysis, translated to English and mapped onto this codebase |
| [docs/PACKAGES.md](docs/PACKAGES.md) | Dependencies, why each is present, and every command needed to run the project |
| [docs/REQUIREMENTS.md](docs/REQUIREMENTS.md) | Each stated requirement and where it is satisfied in the codebase |
| [docs/CHANGELOG.md](docs/CHANGELOG.md) | Every change made across both rebuilds — the clean web architecture, then the API and notification layer |
| [docs/flavor-api.postman_collection.json](docs/flavor-api.postman_collection.json) | 117-request, fully documented Postman collection for the API |
| [docs/reports/](docs/reports/) | The original project report this system was built from (Word, PDF and presentation slides) |
