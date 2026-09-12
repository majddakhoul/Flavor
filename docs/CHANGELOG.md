# Changelog

All notable changes made while rebuilding the original project into the current application.

---

## 2.2.3 — One seeder system, not several

Removed three orphaned seeder files (`UserSeeder`, `TestDatabaseSeeder`, `FocusedRestaurantSeeder`) left over from before the clean rebuild. None were called from `DatabaseSeeder` or referenced anywhere else — `UserSeeder` in particular predated the current model structure entirely (hardcoded `gmail.com` test accounts, Arabic comments describing code that no longer matched the schema). `php artisan migrate:fresh --seed` now runs exactly one, fully verified seeder chain, in dependency order, covering every table:

| Seeder | Produces |
| --- | --- |
| `LocationSeeder` | 6 delivery areas across Damascus, Aleppo, Latakia and Homs |
| `CategorySeeder` | 7 menu categories, fully translated (en/ar/fr) |
| `IngredientSeeder` | 30 ingredients with stock and unit cost |
| `MealSeeder` | 18 meals, each with a real photo, a costed recipe, and full translations |
| `OfferSeeder` | 4 bundle offers, each with a real photo and its meal lineup |
| `TableSeeder` | 24 tables across indoor, outdoor, VIP and rooftop, translated |
| `StaffSeeder` | 7 staff accounts covering every position, password `password` |
| `CustomerSeeder` | 8 customers with varied allergies and one pre-banned account |
| `ReservationSeeder` | 9 reservations spanning six days in the past to six days ahead, every status |
| `OrderSeeder` | 42 orders across delivery/takeaway/reservation types and every status, 2-4 line items each |
| `MaintenanceSeeder` | 6 maintenance records tied to staff |
| `RatingSeeder` | A star rating from four customers on every meal, three on every offer |

Every seeder uses `firstOrCreate`/`updateOrCreate` and is safe to run more than once, and every one that depends on other tables checks for empty prerequisites and returns cleanly instead of throwing.

---

## 2.2.2 — Local setup no longer requires Redis

- `.env.example` defaulted `CACHE_DRIVER` and `QUEUE_CONNECTION` to `redis`, which crashes any request that touches the cache (including the `throttle:auth` rate limiter on `/login`) with `Predis\Connection\Resource\Exception\StreamInitException` unless a Redis server happens to be running locally. Redis is genuinely optional — the framework's own defaults (`file` cache, `sync` queue) need nothing extra installed and are now what `.env.example` ships. Redis remains fully supported for anyone who sets `CACHE_DRIVER=redis` / `QUEUE_CONNECTION=redis` themselves, e.g. in production.
- Updated `docs/PACKAGES.md` and this README's Caching/Background work sections to match, and clarified that the catalog/dashboard cache wrapper already degrades gracefully if Redis is unreachable, but framework-level features like rate limiting talk to the cache directly and correctly fail hard instead of silently disabling a security control.

---

## 2.2.1 — Logo clarity and browser cache-busting

- Found and fixed a real gap the earlier logo pass missed: `.sidebar__brand img` (the staff workspace sidebar header) had its own separate, hardcoded `38px` size that was never updated when the rest of the brand mark was resized — it now matches the same clear, consistent sizing as everywhere else (46px).
- Increased the base `.brand` mark size (header, footer, auth page) from 40px to 46px, and the auth page's large variant from 76px to 80px, for better legibility across the board.
- Added cache-busting (`?v=<file modified time>`) to the `app.css` and `app.js` tags. Static assets like these are aggressively cached by browsers under their exact URL — a CSS-only change (like the sizing fix above) can silently keep showing the old, cached stylesheet even after the server-side file is updated and the page is reloaded normally. This makes every future asset change take effect on the next page load, no hard refresh or manual cache-clear required.

---

## 2.2.0 — Real photography, image consistency, and a click-to-enlarge viewer

### Photos

- Every one of the 18 seeded meals and 4 seeded offers now ships with a real, correctly-cropped photograph, wired automatically through `MealSeeder`/`OfferSeeder` into the `pictures` table — nothing to source or configure after `php artisan migrate --seed`.
- Added a `picture_id` column, a `picture()` relationship and an `image_url` accessor to `Offer`, mirroring `Meal` — offers never had photo support before.

### Visual consistency

- Fixed the meal detail page's hero photo sizing to use a fixed `aspect-ratio` instead of `max-height`, so every dish's hero image renders at the same size regardless of the source photo's proportions.
- Added a hero photo to the offer detail page, which previously showed no image of the offer at all, only its bundled meals.
- Added photo thumbnails to the offers admin list, matching the meals admin list, now that offers have photos.
- Introduced two small reusable CSS patterns, `.media-hero` and `.media-square`, so any future image placement gets the same consistent, cropped-to-fit treatment for free.

### Click-to-enlarge viewer

- Added a lightweight, dependency-free lightbox (`initLightbox()` in `app.js`, `.lightbox` in `app.css`): clicking any meal or offer photo — in a grid, a detail hero, a bundle thumbnail, or an admin table row — opens it full-size over the interface. Closes on backdrop click, the close button, or Escape.

### Logo

- Finished the logo audit: the six error pages (403/404/419/429/500/503) were still using the old compressed lockup image; they now use the same scalable vector mark as the rest of the interface.

---

## 2.1.0 — API layer, notifications, and polish

### API

- Reinstalled Laravel Sanctum for token authentication, scoped entirely to a new `api` middleware group and `sanctum` guard; the web application's session guard is untouched.
- Rebuilt the JSON API from scratch under `app/Http/Controllers/Api`, versioned at `/api/v1`, organised into `Auth`, `Catalog`, `Account`, `Manage` and a shared `NotificationController` — mirroring the web controller layout exactly.
- Every API controller is a thin adapter over the existing `Service` layer; no business logic was duplicated between web and API.
- Added 16 `Http/Resources` classes for consistent JSON shaping, and an `ApiResponse` trait for a uniform `{ success, message, data, meta }` envelope.
- Added search, filter, sort and pagination to every list endpoint via the existing `QueryOptions` support class — no new query logic was needed, it was already shared infrastructure.
- Added `most-consumed ingredients`, `top-selling meals` and `top-selling offers` endpoints and repository methods.
- Added a fully documented, generated Postman collection (`docs/flavor-api.postman_collection.json`) covering all 117 requests.
- Deleted the original ~100 single-action legacy API controllers and their form requests, which had been orphaned since the 2.0.0 rebuild removed `routes/api.php` from the router without deleting the files on disk.

### Notifications

- Added a database notification channel alongside the existing email notifications, via a shared `FlavorNotification` base class that reuses the existing `Mailable` classes for the mail half.
- Added nine notification types across four domains (Sales, Reservations, Inventory, People), each routed to the actor who actually needs it — including two new staff-facing, database-only alerts (new order, new reservation) that did not exist before.
- Added `preferred_locale` to `users`; every notification — mail and in-app — now renders in the *recipient's* language, not the acting user's.
- Added a notification bell to both the staff and customer headers, and a full notification centre page.

### Fixes and cleanup

- Fixed the `auto-fit` grid on the menu and offers pages producing a full-width card when only one result matched a filter; introduced a dedicated `.grid-cards` class using `auto-fill`.
- Reprocessed the supplied logo artwork: removed its flat background and re-hued it to the site's amber/olive palette.
- Swapped the compressed full logo lockup for the existing scalable vector mark in every navigation and auth context, and enlarged it on the sign-in/register screen.
- Moved the shopping cart's storage from the PHP session to the cache layer, keyed by user ID — the session dependency was incompatible with a stateless token API, and the change has no effect on the web experience.
- Removed the unused `ichtrojan/laravel-otp` dependency (superseded by `AuthService`'s own cache-based verification codes since 2.0.0, but never actually removed from `composer.json`).
- Removed an orphaned, unbranded `ReservationCodeMail` class and its unused view.
- Moved the `Auth::logout()` call out of `AuthService::deactivate()` and into the web controller, since session handling is a transport concern, not a domain one — the service can now be safely called from the token-based API too.

---

## 2.0.0 — Rebuild

### Identity and interface

- Replaced the provisional blue and teal palette with the burnt orange identity: `#d97706` primary in light mode, `#f59e0b` in dark mode, ivory `#fffbf5` background, olive `#4d7c0f` secondary.
- Kept the primary hue constant across both modes so the brand does not shift when the theme changes; only surfaces and text invert.
- Installed the supplied Flavor logo and generated 512, 192 and 64 pixel variants for the header, touch icon and favicon. Drew a matching vector mark for scalable contexts.
- Rewrote `public/assets/css/app.css` as a token driven design system: colour, spacing, radius, shadow and type scales declared once and consumed everywhere.
- Restricted status colours to soft background and matching foreground pairs rather than saturated fills, so a busy list stays readable.
- Authored a 45 icon SVG sprite covering navigation, actions, states and domain concepts.
- Drew four illustrations for empty states and the hero, a meal placeholder and a background pattern, all as SVG.
- Introduced the kitchen ticket motif: a notched card with a monospaced code, used for order receipts, reservation confirmations, bundle summaries and cost breakdowns.
- Added dark mode with the preference stored in a cookie and read server side, which removes the flash of the wrong palette on first paint.
- Added right to left support through logical CSS properties rather than a mirrored stylesheet, with IBM Plex Sans Arabic for Arabic typography.
- Added reveal on scroll, hover lift and toast animations, all suppressed under `prefers-reduced-motion`.
- Added print styles for order tickets and reports.

### Architecture

- Replaced roughly one hundred single action API controllers with 29 resource controllers that translate HTTP into a service call and nothing more.
- Introduced a service layer of 20 classes grouped by domain: catalog, sales, reservations, inventory, people, dashboard, media and support.
- Introduced a repository layer with 13 interface and implementation pairs, bound in a dedicated service provider.
- Introduced 14 DTOs so validated input reaches services as typed objects rather than arrays.
- Introduced 12 enums carrying labels, allowed transitions, display tones and, for employee positions, ability sets.
- Added 11 policies, one per model, checked on every action.
- Added seven domain events with queued listeners so mail and stock alerts never delay a transaction.
- Added `QueryOptions`, `Money` and `Ticket` support classes, and the `Filterable`, `HasTranslations` and `InteractsWithCache` traits.

### Web conversion

- Deleted `routes/api.php` and `routes/channels.php`.
- Rewrote `routes/web.php` covering public pages, guest routes, the customer area and the staff workspace.
- Removed Laravel Sanctum; the session guard now handles all authentication.
- Replaced the third party OTP package with a six digit verification code held in the cache with an explicit expiry, keeping the logic visible in `AuthService`.
- Added 25 form requests grouped by area, each carrying its own authorisation check.

### Data integrity

- Wrapped checkout, stock consumption, stock restoration and table assignment in database transactions.
- Added `lockForUpdate` on ingredient rows before availability decisions, preventing two concurrent checkouts from consuming the same last portion.
- Added `lockForUpdate` on table rows during conflict detection, preventing double booking.
- Enforced status changes against the transition maps declared on the order and reservation enums; invalid transitions raise a domain exception.
- Kept soft deletes on orders so a cancellation can be reviewed and restored, with stock handled correctly in both directions.
- Sanitised search input and escaped `%`, `_` and `\` before any `LIKE` clause.
- Clamped page size at 100.

### Localisation

- Added English, Arabic and French across the whole application.
- Split interface strings into six namespaces per locale: `app`, `flash`, `errors`, `domain`, `enums` and `mail`.
- Added a polymorphic `translations` table and the `HasTranslations` trait so database content is translated per field per locale, with fallback to the base column.
- Added a tabbed translation control to every management form, writing all locales in one submission.
- Cached resolved translations for twelve hours under a dedicated tag, flushed on write.
- Resolved the active locale from cookie, then session, then `Accept-Language`, then the configured default.
- Verified the key set: 529 interface keys present in all three locales, with no missing and no orphaned entries.
- Added French versions of the framework language files and a shared `custom.phone.regex` message in all three locales.

### Cookies and access control

- Added `flavor_locale`, `flavor_theme` and `flavor_cookie_consent`, each with a one year lifetime.
- Excluded the three preference cookies from encryption so the browser can read and write them without a round trip; they carry no personal data.
- Added a consent bar offering all cookies or essentials only, recording the choice so it does not reappear.
- Added `PreferredTheme` middleware sharing the theme with every view.
- Added `SetLocale` middleware writing the locale cookie whenever it differs from the resolved locale.
- Added `EnsureAbility`, `EnsureCustomer`, `EnsureUserIsActive` and `EnsureEmailIsVerified` middleware.
- Guarded workspace route groups with `ability:<name>` derived from the employee position.
- Added rate limiting on sign in, registration, verification resend and checkout.
- Added a remember me option backed by Laravel's persistent login cookie.

### Background work and caching

- Added `SyncMenuAvailability`, `LiftExpiredBans` and `BuildDailySnapshot` jobs with matching Artisan commands and scheduler entries.
- Routed mail, reports and maintenance work onto named queues.
- Added a Redis backed cache service with tagged groups and named profiles, falling through to the live query if Redis is unavailable.

### Data and tooling

- Added 12 model factories.
- Added 13 seeders building a complete demo restaurant with catalog content in all three languages, and printing demo credentials at the end of the run.
- Rewrote `composer.json`: removed Sanctum and the OTP package, added `predis/predis`.
- Rewrote `.env.example` with every application setting the project reads.
- Added `config/flavor.php` holding locales, currency, brand details, inventory thresholds, order and reservation rules, media settings, cache profiles, queue names and pagination sizes.

### Fixes carried over from the original project

- Corrected the ratings relationship, which referenced a column name that does not exist in the schema.
- Changed a rating relationship from `hasOne` to `hasMany`, matching the actual cardinality of the data.
- Corrected a reservation type comparison against a string absent from the column definition.
- Added the missing model import in two models that would have failed to resolve at runtime.

### Documentation

- Added `README.md` and its Arabic counterpart `README.ar.md`.
- Added `docs/PACKAGES.md`, `docs/REQUIREMENTS.md` and this changelog.
- Added a Postman collection for the web routes, handling CSRF tokens and the session cookie.

---

## 1.0.0 — Original project

Laravel 10 with Sanctum, exposing a JSON API for restaurant management. Fifteen models, 23 migrations and roughly one hundred single action controllers holding business logic and query construction directly. No service or repository layer, no localisation, no interface.
