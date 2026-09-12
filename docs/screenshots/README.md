# Interface Gallery

This gallery is a visual walkthrough of every screen in Flavor, captured from a running instance seeded with the demo data described in the root [README](../../README.md). It is a reference document only: nothing here changes or replaces the application, and the running interface remains the single source of truth if a screen and its description ever drift apart.

Screens are grouped exactly the way the application itself is grouped: the public site, authentication, the customer account, and the staff workspace — the last one split further by the position that was signed in for each capture, since the sidebar and the abilities behind it change per role. This split mirrors the [Roles and abilities](../../README.md#roles-and-abilities) table in the root README.

---

## Contents

1. [How to read this gallery](#how-to-read-this-gallery)
2. [Public site](#public-site)
3. [Authentication](#authentication)
4. [Customer account](#customer-account)
5. [Staff workspace — Manager](#staff-workspace--manager)
6. [Staff workspace — Chef](#staff-workspace--chef)
7. [Staff workspace — Waiter](#staff-workspace--waiter)
8. [Further reading](#further-reading)

---

## How to read this gallery

Every image lives under `docs/screenshots/<area>/`, numbered in the order a person would naturally move through that area. The three staff sections use the exact demo accounts documented in the root README's [Demo accounts](../../README.md#demo-accounts) table:

| Section | Signed in as | Position | Abilities |
| --- | --- | --- | --- |
| Staff workspace — Manager | Rania Haddad | Manager | catalog, inventory, floor, sales, people, reports |
| Staff workspace — Chef | Samer Khoury | Chef | catalog, inventory |
| Staff workspace — Waiter | Nour Abbas | Waiter | floor, sales |

The Chef and Waiter sections therefore show a genuinely smaller sidebar and dashboard, not a cropped version of the manager's — the workspace only ever renders the sections a signed in position can reach, as described under [Roles and abilities](../../README.md#roles-and-abilities).

A handful of public pages were captured once per interface language (English, French, Arabic) and once per theme (light, dark), to illustrate the [Localisation](../../README.md#localisation) and [Theming and design system](../../README.md#theming-and-design-system) sections of the root README. The remaining screens are captured in English, light theme, since every component renders identically once translated or switched to dark mode.

Order references, customer names, totals and dates visible in these captures come from the seeded demo dataset and will differ after a fresh `php artisan migrate --seed`.

---

## Public site

Reached without signing in. Covers the home page, the menu, and offers, in all three supported languages and both themes.

| Preview | Screen | Notes |
| --- | --- | --- |
| <img src="site/01-home-en-dark.png" width="480" alt="Home page, English copy, dark theme"> | Home — English, dark theme | Hero band with the restaurant's live counts (dishes, active offers, tables) above a top-rated dishes teaser. |
| <img src="site/02-home-fr-light.png" width="480" alt="Home page, French copy, light theme"> | Home — French, light theme | Same layout in French, light theme. |
| <img src="site/03-home-ar-light.png" width="480" alt="Home page, Arabic copy, right to left, light theme"> | Home — Arabic, right to left | The page mirrors completely through logical CSS properties rather than a separate stylesheet, as described under [Localisation](../../README.md#localisation). |
| <img src="site/04-home-fr-light-alt.png" width="480" alt="Home page, French copy, light theme, second capture"> | Home — French, light theme (second capture) | A second capture of the French home page. |
| <img src="site/05-home-top-rated-en.png" width="480" alt="Home page scrolled to the top rated dishes section"> | Home, scrolled — Top rated dishes | The dish cards use the shared `.dish__media` treatment described under [Domain modules](../../README.md#domain-modules), so photos of any source size stay visually consistent. |
| <img src="site/06-home-offers-en.png" width="480" alt="Home page scrolled to the running offers section"> | Home, scrolled — Running offers | Bundle offers currently active, pulled the same way the [offers listing](#site07) is. |
| <img src="site/07-menu-categories-en.png" width="480" alt="Menu page showing the category overview grid"> | Menu — category overview | The `Explore the menu` grid groups dishes by category before the full listing is shown. |
| <img src="site/08-menu-listing-en.png" width="480" alt="Menu listing with search, category, availability and sort controls"> | Menu — full listing | Search, category, availability and sort controls, backed by the same `QueryOptions`/`Filterable` mechanism documented in [docs/API.md](../API.md). |
| <img src="site/09-menu-listing-ar.png" width="480" alt="Menu listing in Arabic, right to left"> | Menu — Arabic, right to left | |
| <img src="site/10-offers-listing-en.png" width="480" alt="Offers listing page"> | Offers — full listing | Every active bundle, with its discounted price and validity window. |
| <img src="site/11-offer-detail-en.png" width="480" alt="Public offer detail page for the Working Lunch bundle"> | Offer detail — public page | The customer-facing view of the `Working Lunch` bundle, including its own hero photo and an add-to-cart action. Compare with the staff-side offer editor under [Staff workspace — Manager](#staff-workspace--manager). |

---

## Authentication

Session based sign in, registration, and password recovery, shared by customers and staff alike.

| Preview | Screen | Notes |
| --- | --- | --- |
| <img src="auth/01-login.png" width="480" alt="Sign in page"> | Sign in | Email and password, with a remember me option backed by the `remember_web_*` cookie documented under [Web authentication and cookies](../../README.md#web-authentication-and-cookies). |
| <img src="auth/02-forgot-password-request.png" width="480" alt="Forgot password request form"> | Forgot password — request | |
| <img src="auth/03-forgot-password-sent.png" width="480" alt="Forgot password confirmation message"> | Forgot password — confirmation | |
| <img src="auth/04-register-step1.png" width="480" alt="Create account form, first section"> | Create account — first section | Name, email and phone. |
| <img src="auth/05-register-step2.png" width="480" alt="Create account form, second section"> | Create account — second section | Gender, delivery area (drawn from [Locations](#locations)), and password. |

---

## Customer account

Reached under `/account` through the `customer` middleware described under [Roles and abilities](../../README.md#roles-and-abilities); customers never see the staff workspace.

| Preview | Screen | Notes |
| --- | --- | --- |
| <img src="account/01-overview.png" width="480" alt="Customer account overview"> | Overview | Open orders, items in the cart, and upcoming reservations at a glance, plus recent orders. |
| <img src="account/02-overview-book-table-cta.png" width="480" alt="Customer account overview scrolled, showing the upcoming reservations empty state"> | Overview, scrolled | Empty state for upcoming reservations with a `Book a table` call to action. |
| <img src="account/03-profile-details.png" width="480" alt="Customer profile, personal details section"> | Profile — personal details | Name, phone, gender, delivery area, allergies and favourite categories. |
| <img src="account/04-profile-password-danger-zone.png" width="480" alt="Customer profile, password change and account deactivation"> | Profile — password and deactivation | The danger zone deactivates the account, which is what `EnsureUserIsActive` checks on every subsequent request. |
| <img src="account/05-orders-list.png" width="480" alt="Customer order history list"> | My orders | Order history with status, search and sort. |
| <img src="account/06-reservations-list.png" width="480" alt="Customer reservation list"> | My reservations | |
| <img src="account/07-book-table-time.png" width="480" alt="Book a table, choosing a date and time range"> | Book a table — choose a time | Date, time range and party size. |
| <img src="account/08-book-table-select.png" width="480" alt="Book a table, selecting from available tables"> | Book a table — available tables | Tables that satisfy the requested slot, resolved by `TableAvailabilityService` under a row lock, as described under [Data integrity](../../README.md#data-integrity). |
| <img src="account/09-cart-empty.png" width="480" alt="Empty shopping cart"> | Cart — empty | |
| <img src="account/10-reservations-list-alt.png" width="480" alt="Customer reservation list, a second account"> | My reservations — second account | A second customer's reservation list, for comparison. |
| <img src="account/11-cart-with-item.png" width="480" alt="Shopping cart containing one item"> | Cart — with an item | The cart is keyed by the signed in user's id in the cache layer, not the PHP session, as described under [Domain modules](../../README.md#domain-modules). |
| <img src="account/12-order-detail.png" width="480" alt="Customer order detail page"> | Order detail | The customer's own view of an order, using the same kitchen-ticket motif described under [Theming and design system](../../README.md#theming-and-design-system). |

---

## Staff workspace — Manager

Signed in as Rania Haddad, the seeded Manager account, which reaches every section of `/manage`.

### Dashboard and notifications

| Preview | Screen | Notes |
| --- | --- | --- |
| <img src="manage-manager/01-dashboard-overview.png" width="480" alt="Manager dashboard, headline metrics"> | Dashboard — headline metrics | Orders today, reservations today, floor occupancy, and orders over the last 14 days. |
| <img src="manage-manager/02-dashboard-orders-charts.png" width="480" alt="Manager dashboard, orders by status and type"> | Dashboard — orders by status and type | Two donut charts plus the current menu mix. |
| <img src="manage-manager/03-dashboard-revenue-topselling.png" width="480" alt="Manager dashboard, revenue over 12 months and top selling items"> | Dashboard — revenue and top selling | A 12 month revenue chart and the current top sellers, rendered with Chart.js as described under [Domain modules](../../README.md#domain-modules). |
| <img src="manage-manager/04-dashboard-recent-upcoming.png" width="480" alt="Manager dashboard, recent orders, upcoming reservations and stock watchlist"> | Dashboard — recent activity | Recent orders, upcoming reservations, and a stock watchlist for ingredients approaching their threshold. |
| <img src="manage-manager/37-notifications.png" width="480" alt="Notification bell dropdown open on the manager dashboard"> | Notification centre | The bell shared by every staff position, backed by [docs/NOTIFICATIONS.md](../NOTIFICATIONS.md). |

### Sales and floor — orders, reservations and tables

| Preview | Screen | Notes |
| --- | --- | --- |
| <img src="manage-manager/05-orders-list.png" width="480" alt="Orders list with filters"> | Orders — list | Search, status, type, sort and direction, backed by the same `Filterable` trait the API exposes over HTTP. |
| <img src="manage-manager/06-cancelled-orders-empty.png" width="480" alt="Cancelled orders list, empty state"> | Cancelled orders — empty state | Orders are soft deleted rather than removed, so a cancellation can be reviewed here and restored. |
| <img src="manage-manager/07-reservations-list.png" width="480" alt="Reservations list"> | Reservations — list | |
| <img src="manage-manager/08-reservation-new.png" width="480" alt="New reservation form with table availability grid"> | New reservation | Table availability for the chosen date and time, resolved under a row lock as described under [Data integrity](../../README.md#data-integrity). |
| <img src="manage-manager/09-order-detail.png" width="480" alt="Order detail page with status change control"> | Order detail | Line items, the order's status transition control, and its danger zone action. |
| <img src="manage-manager/10-reservation-detail.png" width="480" alt="Reservation detail page with table change and status control"> | Reservation detail | Status change, table reassignment, and the computed table charge. |
| <img src="manage-manager/11-tables-edit.png" width="480" alt="Edit table form"> | Tables — edit | |
| <img src="manage-manager/12-tables-new.png" width="480" alt="New table form"> | Tables — new | Number, area, capacity and price per hour. |

### Catalog — meals, categories and offers

| Preview | Screen | Notes |
| --- | --- | --- |
| <img src="manage-manager/13-meals-list.png" width="480" alt="Meals list with stock and availability"> | Meals — list | Preparation cost, margin and computed selling price, as described under [Domain modules](../../README.md#domain-modules). |
| <img src="manage-manager/14-meals-new.png" width="480" alt="New meal form"> | Meals — new | |
| <img src="manage-manager/15-categories-list.png" width="480" alt="Categories list"> | Categories — list | |
| <img src="manage-manager/16-categories-edit.png" width="480" alt="Edit category form"> | Categories — edit | The tabbed control writes all three locales in one submission, as described under [Localisation](../../README.md#localisation). |
| <img src="manage-manager/17-offers-list.png" width="480" alt="Offers list"> | Offers — list | |
| <img src="manage-manager/18-offer-edit-details.png" width="480" alt="Offer editor, details panel"> | Offer editor — details | Title, description, discount and validity window, alongside the same bundle ticket shown to the customer. |
| <img src="manage-manager/19-offer-edit-bundle.png" width="480" alt="Offer editor, bundle contents"> | Offer editor — bundle contents | |
| <img src="manage-manager/20-offer-edit-bundle-items.png" width="480" alt="Offer editor, bundle contents scrolled to current items"> | Offer editor — current items | |

### Inventory — ingredients and maintenance

| Preview | Screen | Notes |
| --- | --- | --- |
| <img src="manage-manager/21-ingredients-list.png" width="480" alt="Ingredients list with stock levels"> | Ingredients — list | Stock, status and unit price; the same rows the low-stock alert in [docs/NOTIFICATIONS.md](../NOTIFICATIONS.md) watches. |
| <img src="manage-manager/22-ingredients-new.png" width="480" alt="New ingredient form"> | Ingredients — new | |
| <img src="manage-manager/23-ingredients-edit.png" width="480" alt="Edit ingredient form"> | Ingredients — edit | |
| <img src="manage-manager/24-maintenance-list.png" width="480" alt="Maintenance records list"> | Maintenance — list | Cost, downtime and the staff member responsible for each record. |

### People — employees and customers

| Preview | Screen | Notes |
| --- | --- | --- |
| <img src="manage-manager/25-employees-list.png" width="480" alt="Employees list"> | Employees — list | |
| <img src="manage-manager/26-employees-new.png" width="480" alt="New employee form"> | Employees — new | Creating an employee provisions the user account and mails the first sign in credentials, per [Domain modules](../../README.md#domain-modules). |
| <img src="manage-manager/27-employees-edit.png" width="480" alt="Edit employee form"> | Employees — edit | |
| <img src="manage-manager/28-customers-list.png" width="480" alt="Customers list"> | Customers — list | Standing (in good standing, or banned) alongside contact details. |
| <img src="manage-manager/29-customer-detail.png" width="480" alt="Customer detail profile with order and reservation history"> | Customer detail | Recent orders, reservation history and profile information for a single customer. |

### Locations

| Preview | Screen | Notes |
| --- | --- | --- |
| <img src="manage-manager/30-locations-list.png" width="480" alt="Delivery locations list"> | Locations — list | The delivery areas offered on registration and checkout. |
| <img src="manage-manager/31-locations-new.png" width="480" alt="New location form"> | Locations — new | Country, region, city, street and delivery time. |
| <img src="manage-manager/32-locations-edit.png" width="480" alt="Edit location form"> | Locations — edit | |

### Reports

| Preview | Screen | Notes |
| --- | --- | --- |
| <img src="manage-manager/33-report-finance.png" width="480" alt="Finance report with a date range and KPI cards"> | Finance | Revenue, payroll and net result for a chosen date range; printable, as described under [Domain modules](../../README.md#domain-modules). |
| <img src="manage-manager/34-report-finance-chart.png" width="480" alt="Finance report chart section"> | Finance — chart section | Revenue over 12 months against headcount by position. |
| <img src="manage-manager/35-report-stock.png" width="480" alt="Stock report with a restock watchlist"> | Stock report | Inventory value alongside a restock watchlist for ingredients near their threshold. |
| <img src="manage-manager/36-report-menu-performance.png" width="480" alt="Menu performance report"> | Menu performance | Top selling items against the current menu mix by category. |

---

## Staff workspace — Chef

Signed in as Samer Khoury, the seeded Chef account. The `catalog` and `inventory` abilities mean the sidebar and the dashboard only ever offer Meals, Categories, Offers, Ingredients and Maintenance — the workspace renders no Orders, Reservations, Tables, Employees, Customers, Locations or Reports section for this position, as described under [Roles and abilities](../../README.md#roles-and-abilities).

| Preview | Screen | Notes |
| --- | --- | --- |
| <img src="manage-chef/01-dashboard.png" width="480" alt="Chef dashboard with a reduced sidebar"> | Dashboard | Reduced to the metrics relevant to the kitchen. |
| <img src="manage-chef/02-meals.png" width="480" alt="Meals list, chef view"> | Meals | The same screen shown in [Staff workspace — Manager](#staff-workspace--manager), reached here by a different position. |
| <img src="manage-chef/03-categories.png" width="480" alt="Categories list, chef view"> | Categories | |
| <img src="manage-chef/04-offers.png" width="480" alt="Offers list, chef view"> | Offers | |
| <img src="manage-chef/05-ingredients.png" width="480" alt="Ingredients list, chef view"> | Ingredients | |
| <img src="manage-chef/06-maintenance.png" width="480" alt="Maintenance list, chef view"> | Maintenance | |

---

## Staff workspace — Waiter

Signed in as Nour Abbas, the seeded Waiter account. The `floor` and `sales` abilities mean the sidebar only ever offers Orders, Cancelled orders, Reservations and Tables.

| Preview | Screen | Notes |
| --- | --- | --- |
| <img src="manage-waiter/01-dashboard.png" width="480" alt="Waiter dashboard with a reduced sidebar"> | Dashboard | Reduced to the metrics relevant to the floor. |
| <img src="manage-waiter/02-orders.png" width="480" alt="Orders list, waiter view"> | Orders | |
| <img src="manage-waiter/03-cancelled-orders-empty.png" width="480" alt="Cancelled orders list, empty state, waiter view"> | Cancelled orders | |
| <img src="manage-waiter/04-reservations.png" width="480" alt="Reservations list, waiter view"> | Reservations | |
| <img src="manage-waiter/05-tables.png" width="480" alt="Tables list, waiter view"> | Tables | |

---

## Further reading

| Document | Contents |
| --- | --- |
| [README.ar.md](README.ar.md) | Arabic edition of this gallery |
| [../../README.md](../../README.md) | Root README: architecture, domain modules, roles and abilities |
| [../../README.ar.md](../../README.ar.md) | Arabic edition of the root README |
| [../API.md](../API.md) | Full API reference |
| [../NOTIFICATIONS.md](../NOTIFICATIONS.md) | The notification system behind the bell shown in this gallery |
| [../diagrams/index.html](../diagrams/index.html) | UML diagrams for the system's core scenarios |
