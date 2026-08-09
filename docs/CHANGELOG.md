# Changelog

All notable changes made while rebuilding the original project into the current application.

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
