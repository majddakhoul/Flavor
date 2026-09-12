# Requirements

This document records each requirement stated for the rebuild, how it was interpreted, and exactly where it is satisfied in the codebase. It is intended as a review aid: every row can be checked against a file.

---

## 1. Clean architecture

**Requirement.** Rewrite the original project as clean code following a layered architecture.

**Interpretation.** Separate HTTP concerns, business rules and data access into distinct layers, each with one reason to change.

**Where it lives.**

| Layer | Location | Count |
| --- | --- | --- |
| Enums | `app/Enums` | 12 |
| DTOs | `app/DTOs` | 14 |
| Form Requests | `app/Http/Requests` | 25 |
| Controllers | `app/Http/Controllers/Web` | 29 |
| Services | `app/Services` | 20 |
| Repository contracts and implementations | `app/Repositories` | 13 pairs |
| Policies | `app/Policies` | 11 |
| Events and Listeners | `app/Events`, `app/Listeners` | 7 each |
| Support classes and traits | `app/Support` | 3 classes, 3 traits |

**Rules enforced.** Controllers do not build queries. Services do not read the request. Repositories do not decide policy. Every dependency is injected through the constructor and every repository interface is bound in `RepositoryServiceProvider`.

---

## 2. Schema preserved

**Requirement.** Do not change the tables or the models.

**Interpretation.** The original migrations and column definitions stay exactly as they were. Only additive migrations are permitted.

**Result.** All 23 original migrations are untouched. Three migrations were added:

| Migration | Reason |
| --- | --- |
| `create_translations_table` | Polymorphic storage for database content translations |
| `create_jobs_table` | Required by the Redis and database queue drivers |
| `create_sessions_table` | Required by the database session driver |

Four defects in the original models were corrected without touching the schema: the ratings relationship pointed at the wrong column name, a rating relationship was declared `hasOne` where the data is one to many, and an enum value was compared against a string that did not exist in the column definition.

---

## 3. Web only, then a versioned API restored alongside it

**Requirement.** Convert everything from API to web, for every kind of user.

**Interpretation.** No JSON endpoints, no token authentication. Every action is a form submission or a link, authenticated by session, protected by CSRF.

**Result at the `2.0.0` web-only rebuild.** `routes/api.php` and `routes/channels.php` were deleted. `routes/web.php` defined public pages, guest routes, the customer area under `/account` and the staff workspace under `/manage`. Laravel Sanctum was removed from the dependency list. Roughly one hundred single action API controllers were consolidated into resource controllers.

**What changed in `2.1.0`.** The original project's own scope always paired this Laravel backend with a Flutter mobile client (see [ANALYSIS.md](ANALYSIS.md)), which needs a JSON API to talk to. A versioned API was rebuilt from scratch at `/api/v1` — organised into `Auth`, `Catalog`, `Account` and `Manage`, mirroring the web controller layout — authenticated by reinstated Sanctum personal access tokens, entirely independent of the web session guard. No business rule was duplicated: every API controller is a thin adapter over the same `Service`/`Repository`/`Policy` classes the web controllers already called. See [API.md](API.md) for the full endpoint reference and the `2.1.0` entry in [CHANGELOG.md](CHANGELOG.md) for the complete list of additions.

---

## 4. Eloquent attribute casting

**Requirement.** Use `Illuminate\Database\Eloquent\Casts\Attribute` inside the models with get and set behaviour, including `ucfirst` and `strtoupper` style normalisation.

**Where it lives.** Every model in `app/Models`. Examples:

| Model | Accessor or mutator | Behaviour |
| --- | --- | --- |
| `User` | `firstName`, `lastName` | Trim and `ucfirst` on write |
| `User` | `email` | Lowercase on write |
| `User` | `fullName`, `initials` | Computed on read |
| `Meal` | `price` | Preparation cost plus margin |
| `Meal` | `maxPortions`, `isOrderable` | Derived from the recipe and current stock |
| `Order` | `reference`, `total`, `itemsCount` | Computed on read |
| `Employee` | `nationalId` | `strtoupper` on write |
| `Reservation` | `startsAt`, `endsAt`, `durationHours` | Derived from the pivot times |

---

## 5. No comments in the code

**Requirement.** Formatted, ordered code with no inline comments.

**Result.** No PHP file in `app/` contains a comment. Naming carries the intent: `assertAvailable`, `revertToCart`, `freeBetween`, `requirementsForOrder`. Documentation lives in this directory rather than in the source. Formatting is consistent and enforceable with Pint.

---

## 6. Trilingual support

**Requirement.** Translate the database data, the responses and the front end into English, Arabic and French.

**Interpretation.** Three separate problems: interface strings, content stored in the database, and correct direction and typography for Arabic.

**Result.**

| Layer | Mechanism | Location |
| --- | --- | --- |
| Interface | Seven namespaced language files per locale | `lang/{en,ar,fr}/{app,flash,errors,domain,enums,mail,notifications}.php` |
| Framework strings | Laravel defaults per locale | `lang/{en,ar,fr}/{auth,passwords,pagination,validation}.php` |
| Database content | Polymorphic `translations` table plus the `HasTranslations` trait | `app/Support/Traits/HasTranslations.php` |
| Editing | Tabbed control writing every locale in one submission | `resources/views/components/translations-tabs.blade.php` |
| Direction | Logical CSS properties and a `dir` attribute driven by the locale enum | `public/assets/css/app.css`, `app/Enums/Locale.php` |
| Resolution order | Cookie, session, `Accept-Language`, configured default | `app/Http/Middleware/SetLocale.php` |

The interface key set was extracted from the templates and verified: 529 keys, present in all three locales, with no missing and no orphaned entries.

---

## 7. Job queue, transactions and locking

**Requirement.** Use a job queue, database transactions and `lockForUpdate`.

**Where it lives.**

| Concern | Location |
| --- | --- |
| Stock consumption and restoration inside a transaction with row locks | `app/Services/Inventory/StockService.php` |
| Checkout in a single transaction | `app/Services/Sales/CheckoutService.php` |
| Table conflict detection with row locks | `app/Services/Reservations/TableAvailabilityService.php` |
| Queued listeners for mail and alerts | `app/Listeners` |
| Scheduled jobs | `app/Jobs`, scheduled in `app/Console/Kernel.php` |

Locking prevents the two race conditions that matter in this domain: two checkouts consuming the last portion of an ingredient, and two bookings taking the same table for overlapping times.

---

## 8. Redis caching

**Requirement.** Cache with Redis.

**Result.** `CacheService` wraps tagged cache access behind named profiles declared in `config/flavor.php`. A write to a meal flushes the catalog tag only. Translation lookups are cached for twelve hours under their own tag. If the cache is unreachable the service falls through to the live query rather than raising an error, so a cache outage degrades performance instead of breaking the site.

Redis is fully supported and was the original driver, but `.env.example` now defaults `CACHE_DRIVER` and `QUEUE_CONNECTION` to `file` and `sync` respectively (see `2.2.2` in [CHANGELOG.md](CHANGELOG.md)), so a fresh checkout runs with no extra service to install. Setting `CACHE_DRIVER=redis` and `QUEUE_CONNECTION=redis` restores the original Redis-backed behaviour for production. This fallback is specific to `CacheService`: framework-level features that talk to the cache directly, such as the `throttle:auth` rate limiter on sign in, always use whatever `CACHE_DRIVER` is configured and fail loudly if it points at a Redis server that is not running — the correct behaviour for a security control.

---

## 9. Filtering, searching and sorting

**Requirement.** Provide filter, search and sort across listings.

**Result.** `QueryOptions` builds a normalised object from the request: search term, filter map, sort column, direction and page size. The `Filterable` trait applies it through `scopeApplyOptions`, and each model declares which columns are searchable, filterable and sortable; a model can also define a `filterXxx` method for a filter that needs custom logic. Search input is sanitised and `LIKE` wildcards are escaped. Page size is clamped at 100. The `x-toolbar` and `x-filter-select` components render the controls consistently on every listing.

---

## 10. Interface, icons and design

**Requirement.** Interactive interfaces, generated imagery and icons, an admin dashboard with full statistics.

**Result.**

| Item | Detail |
| --- | --- |
| Blade templates | 103 files across layouts, components, partials and pages |
| Reusable components | 23, including card, stat, badge, ticket, toolbar, empty state and rating |
| Icon set | 45 icons authored as a single SVG sprite, `public/assets/img/icons/sprite.svg` |
| Illustrations | Four empty state and hero illustrations, plus a meal placeholder and a background pattern, all SVG |
| Dashboard | Four KPI cards, five Chart.js charts, top selling list, recent orders, upcoming reservations, stock watchlist |
| Reports | Finance with a date range, inventory with inline restocking, and menu performance |
| Motion | Reveal on scroll, hover lift, animated toasts, all suppressed under `prefers-reduced-motion` |
| Print | Dedicated print styles for order tickets and reports |

---

## 11. Brand identity

**Requirement.** Change the identity to burnt orange and apply the supplied logo.

**Result.** The palette in `public/assets/css/app.css` uses `#d97706` in light mode and `#f59e0b` in dark mode, with an ivory `#fffbf5` page background and an olive `#4d7c0f` secondary. Status colours are functional and applied as soft backgrounds rather than saturated fills. The supplied logo was installed at `public/assets/img/brand/logo.png` and downscaled to 512, 192 and 64 pixel variants for the header, the touch icon and the favicon. A vector mark was drawn to match for use where a scalable asset is preferable.

---

## 12. Cookies, credibility and permissions

**Requirement.** Work on cookies alongside authentication and permissions.

**Result.**

| Cookie | Contents | Lifetime | Encrypted |
| --- | --- | --- | --- |
| `flavor_session` | Session identifier | 120 minutes | yes |
| `remember_web_*` | Persistent sign in | 5 years | yes |
| `XSRF-TOKEN` | CSRF token | session | yes |
| `flavor_locale` | Language code | 1 year | no |
| `flavor_theme` | `light` or `dark` | 1 year | no |
| `flavor_cookie_consent` | `all` or `essential` | 1 year | no |

The three preference cookies are excluded from encryption so JavaScript can read them, and they carry no personal data. Reading the theme server side removes the flash of the wrong palette on first paint. A consent bar records the choice and does not reappear.

Permissions are enforced at three levels: route groups guarded by `ability:<name>`, policies checked per model action, and navigation that renders only reachable sections. `EnsureUserIsActive` blocks disabled accounts on every request, and rate limiting protects sign in, registration, verification resend and checkout.

---

## 13. Seed data

**Requirement.** An excellent seeder.

**Result.** Thirteen seeders build a complete demo restaurant: six delivery locations, seven categories, thirty ingredients with realistic units and costs, eighteen dishes with full recipes, four offers, twenty four tables across four areas, seven staff covering every position, eight customers including one banned, nine reservations spanning past and future, forty two orders across every status and type, six maintenance records and ratings on every dish and offer. All catalog content is seeded in all three languages. Demo account credentials are printed to the console at the end of the run.

---

## 14. Documentation

**Requirement.** Professional formal README files without emoji, a packages and commands reference, and a record of every change.

**Result.**

| Document | Contents |
| --- | --- |
| `README.md` | Architecture, layout, modules, roles, cookies, the API, notifications, diagrams, localisation, theming, integrity, installation |
| `README.ar.md` | The same document in Arabic |
| `docs/screenshots/README.md` / `README.ar.md` | Interface gallery — every screen in the application, captured and captioned by area and by staff position, in English and Arabic |
| `docs/API.md` | Full API reference: every endpoint, the response envelope, search/filter/sort/pagination, auth flow, error catalogue |
| `docs/NOTIFICATIONS.md` | How the mail and database notification system is built, and how to add a new notification type |
| `docs/ANALYSIS.md` | The original requirements analysis, translated and mapped onto this codebase |
| `docs/PACKAGES.md` | Every dependency with its justification, and every command needed to run the project |
| `docs/REQUIREMENTS.md` | This document |
| `docs/CHANGELOG.md` | Every change made during both rebuilds |
| `docs/diagrams/`, `docs/restaurant-diagrams/`, `docs/Draw io/` | Hand-authored UML diagrams, a second schema-derived Mermaid set, and their editable `.drawio` sources |
| `docs/flavor-api.postman_collection.json` | Postman collection covering the JSON API |
| `docs/flavor.postman_collection.json` | Postman collection covering the web routes, kept from the `2.0.0` web-only rebuild |

---

## 15. Postman collection

**Requirement.** Provide a Postman collection.

**Interpretation at the `2.0.0` web-only stage.** Since the application was web only at that point, the collection exercised form submissions rather than JSON endpoints.

**Result.** Two collections now ship, kept for their different purposes:

| Collection | Exercises | Notes |
| --- | --- | --- |
| `docs/flavor-api.postman_collection.json` | The versioned JSON API (`/api/v1`) | 117 requests, added in `2.1.0`; uses a bearer token obtained from **Auth > Login**, set as the collection's `token` variable. This is the primary collection referenced from the root README. |
| `docs/flavor.postman_collection.json` | The session-authenticated web routes | Kept from the `2.0.0` rebuild; uses a cookie jar for the session, extracts the CSRF token from the sign in page into a collection variable, and sends every mutation as `x-www-form-urlencoded` with the `_token` field and the correct `_method` override. |
