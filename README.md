# Flavor

Flavor is a restaurant management system built on Laravel 10. It covers the public menu, customer ordering and table booking, and a staff workspace for the kitchen, the floor, inventory, people and reporting.

The application is server rendered and web only. There is no public API surface: every capability is reached through session authenticated web routes and Blade views.

---

## Table of contents

1. [Architecture](#architecture)
2. [Directory layout](#directory-layout)
3. [Domain modules](#domain-modules)
4. [Roles and abilities](#roles-and-abilities)
5. [Authentication and cookies](#authentication-and-cookies)
6. [Localisation](#localisation)
7. [Theming and design system](#theming-and-design-system)
8. [Data integrity](#data-integrity)
9. [Background work](#background-work)
10. [Caching](#caching)
11. [Installation](#installation)
12. [Demo accounts](#demo-accounts)
13. [Further reading](#further-reading)

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
│   ├── Controllers/Web/
│   │   ├── Account/         Customer area
│   │   ├── Auth/            Sign in, registration, verification, password reset
│   │   ├── Manage/          Staff workspace
│   │   └── Site/            Public pages
│   ├── Middleware/          Locale, theme, ability, account status
│   └── Requests/            Form requests grouped by area
├── Jobs/                    Queued work
├── Listeners/               Queued event handlers
├── Mail/                    Mailables built on a shared base
├── Models/                  Eloquent models
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
│   └── Support/             Cache facade wrapper
└── Support/                 Value objects, query options and model traits

resources/views/
├── layouts/                 base, site, auth, account, manage
├── components/              23 reusable Blade components
├── partials/                header, footer, sidebar, topbar, account navigation
├── site/                    Home, menu, offers
├── auth/                    Sign in, register, verify, password reset
├── account/                 Dashboard, cart, checkout, orders, reservations, profile
├── manage/                  Workspace dashboard, CRUD screens, reports
├── emails/                  Shared transactional email template
└── errors/                  403, 404, 419, 429, 500, 503

public/assets/
├── css/app.css              Design tokens and every component style
├── js/app.js                Theme, menus, tabs, toasts, charts, cookie consent
└── img/                     Brand, icon sprite, illustrations, patterns

lang/{en,ar,fr}/             app, flash, errors, domain, enums, mail and Laravel defaults
database/
├── migrations/              Original schema plus translations, jobs and sessions
├── factories/               12 model factories
└── seeders/                 13 seeders producing a complete demo restaurant
docs/                        Packages, requirements, changelog, Postman collection
```

---

## Domain modules

### Catalog

Meals carry a preparation cost derived from their recipe, a margin percentage and a computed selling price. A meal is orderable only when its availability flag is set and every ingredient in its recipe has enough stock for at least one portion. Offers bundle meals at a discount and inherit the same stock rules.

### Sales

The cart lives in the session, so a customer can build an order before deciding to sign in. Checkout runs inside a database transaction: it locks the ingredient rows, validates availability, writes the order and its lines, consumes stock and raises the confirmation event. Order status follows an explicit state machine defined on the `OrderStatus` enum. Cancellation restores the consumed stock.

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

## Authentication and cookies

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

## Localisation

Three locales ship with the application: English, Arabic and French. Arabic renders right to left; the layout mirrors through logical CSS properties rather than a separate stylesheet.

Two distinct kinds of text are translated.

**Interface strings** live in `lang/{locale}/`, split by namespace: `app` for the interface, `flash` for confirmations, `errors` for failures, `domain` for email and document labels, `enums` for enumerated values and `mail` for transactional messages.

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

Mail and stock alerts are dispatched through queued listeners on dedicated queues so a slow mail server never delays a checkout.

Commands: `flavor:sync-availability`, `flavor:lift-bans`, `flavor:daily-snapshot`.

---

## Caching

Redis is the cache and queue driver. Cache entries are grouped by tag so a write to a meal flushes only the catalog group. Profiles and their durations in minutes are declared in `config/flavor.php`: menu 10, catalog 60, dashboard 5, statistics 5, reports 15, lookups 720. If Redis is unavailable the cache service falls through to the underlying query rather than failing the request.

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

---

## Demo accounts

Seeding creates a working restaurant: locations, categories, thirty ingredients, eighteen dishes with recipes, four offers, twenty four tables, staff covering every position, eight customers, reservations across past and future dates, forty two orders and maintenance records.

All demo accounts use the password `password`.

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
| [docs/PACKAGES.md](docs/PACKAGES.md) | Dependencies, why each is present, and every command needed to run the project |
| [docs/REQUIREMENTS.md](docs/REQUIREMENTS.md) | Each stated requirement and where it is satisfied in the codebase |
| [docs/CHANGELOG.md](docs/CHANGELOG.md) | Every change made during the rebuild, including the identity update |
| [docs/flavor.postman_collection.json](docs/flavor.postman_collection.json) | Postman collection for the web routes, including CSRF and session handling |
