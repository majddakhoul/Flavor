# Requirements Analysis

Source document: [`report/Main_Report.docx`](report/Main_Report.docx) / [`report/Main_Report.pdf`](report/Main_Report.pdf) — the original project report submitted for the Software Engineering Project course, Faculty of Informatics Engineering, Damascus University, 2024/2025, supervised by Eng. Ghaidaa Bakoura. This document translates and reorganises that report into English as a standalone engineering reference; it does not replace the original submission.

Authors: Osama Ahmad Al-Mohammad, Tasnim Ezzat Marei, Abdulrahman Nabil Dadou, Ghofran Ghassan Shaghri, Majd Azzam Al-Dakhoul, Maysaa Ismail Al-Hajj Ali.

---

## 1. Summary

Flavor is a restaurant management system covering table reservations, dine-in/delivery/pickup ordering, staff and inventory management, and reporting, for three audiences:

- **The business owner / manager** — better oversight of the restaurant.
- **Staff** — less day-to-day chaos.
- **Customers** — orders fulfilled accurately and on time.

The original submission scoped the backend to Laravel and the frontend to Flutter (mobile). This repository's Laravel backend now serves both a server-rendered web application and a versioned JSON API (`/api/v1`, see [`API.md`](API.md)) intended for that Flutter client and any other API consumer.

## 2. Problem statement

### 2.1 Actors

- **Customer** — dines in, orders for delivery, or orders for pickup.
- **Staff**, split into:
  - **Management** — the owner/manager: strategy and financial/operational responsibility.
  - **Customer service** — waiters: greet guests, present the menu, take orders, serve.
  - **Kitchen** — the chef: prepares dishes to quality and safety standards.
  - **Support functions** — cleaning and security staff.
  - **Delivery** — couriers who bring orders to the customer.

### 2.2 Problems observed

**For customers:**

- Long waits for the menu, the order, or the food, with no visibility into what is happening.
- Order mistakes with no way to correct them once placed.
- Food that is not fresh, from poor stockroom discipline.
- Reservation chaos — tables shown as occupied when they are not, or guests seated at unclean tables.
- Unclear pricing — hidden service charges, mismatches between the menu, the app and the final bill.
- Billing delays or errors.

**For staff:**

- Difficulty handling order volume at peak times.
- No way to track an order's state (preparing, out for delivery, delivered).
- No visibility into reservation timing or table availability.
- Poor oversight of headcount and task distribution; departmental supervision is difficult.
- Friction between teams from poor communication, low morale from unfair task distribution.
- No systematic way to know what the kitchen stockroom needs, so shortages appear without warning.

These map directly onto the notification and inventory-alerting work in this repository: see [`NOTIFICATIONS.md`](NOTIFICATIONS.md) for how staff now get a real-time in-app alert the moment a new order or reservation needs attention, or stock drops below its threshold — the exact gaps the original report identifies.

## 3. Competitive review

The original report evaluated three restaurant/retail POS products before scoping Flavor:

| Product | Strengths noted | Weaknesses noted |
| --- | --- | --- |
| **Poster POS** | Thorough, well-organised feature set; works offline; also manages inventory and suppliers | Weak notification system; no electronic payments; cluttered home screen; no customer-facing ordering |
| **Loyverse POS** | Simple, approachable interface; free tier; per-employee PINs | Plain design; inventory/staff management locked behind a paid tier; no bulk quantity entry; no customer-facing app |
| **RePOS** | Clean customer-facing interface; simple inventory and order management; multiple payment methods; per-party table booking | No multi-language support; no order-tracking; requires a constant connection; limited device support |

A feature-by-feature matrix (login/logout, order management, staff management, inventory, menu management, languages, notifications, analytics, reviews, UI, reservations, payments) is preserved in the original report and is not reproduced here; the short version is that no single competitor covered notifications, multi-language support, and full order/reservation tracking at once — which became Flavor's design target.

## 4. Requirements

The original report enumerated 89 functional requirements as a flat numbered list (login, logout, password reset, email verification, CRUD for every entity, cart operations, reservations, ratings, and reporting), each mapped to a named use case and an interacting actor (`All Users`, `Customer`, `Manager`, `Chef`, `Waiter`, or a combination). That full table is preserved in the original document. In this codebase, each of those 89 requirements is implemented as:

- a **Service** method (business rule),
- a **Repository** method (data access) where the requirement is a query,
- a **Policy** check (who is allowed to do it), and
- both a **Web** controller action and an **API** controller action (see [`API.md`](API.md) for the full current endpoint list, which supersedes the original flat requirement numbering with a versioned, RESTful shape).

Non-functional requirements from the original report — usability, security, response time, maintainability, cross-platform compatibility, and periodic review — are addressed as described in the root [`README.md`](../README.md) (architecture, caching, and security sections) rather than repeated here.

## 5. Technical design

The original report's class diagram and database diagram describe the same fifteen entities this codebase migrates and models today: `User`, `Customer`, `Employee`, `Location`, `Category`, `Meal`, `Ingredient`, `Table`, `Offer`, `Order`, `Reservation`, `Maintenance`, `Picture`, `MealRating`, `OfferRating`, plus the pivot tables connecting them (`ingredient_meal`, `meal_order`, `offer_order`, `meal_offer`, `reservation_table`). The live, authoritative version of both diagrams is the code itself — `app/Models/*.php` for the class relationships and `database/migrations/*.php` for the schema — since those are guaranteed to stay in sync with the running application, unlike a static diagram.

## 6. What changed since the original report

The original report scoped a single messy API layer (documented in [`CHANGELOG.md`](CHANGELOG.md) as "roughly one hundred single-action controllers holding business logic and query construction directly"). Since then the codebase has been rebuilt twice:

1. A clean, web-only application (session auth, Services/Repositories/DTOs/Policies, server-rendered Blade) — see the `2.0.0` entry in [`CHANGELOG.md`](CHANGELOG.md).
2. A clean, versioned JSON API layer added alongside it (Sanctum token auth, the same Services/Repositories/DTOs/Policies reused underneath, API Resources for JSON shaping) — see the `2.1.0` entry in [`CHANGELOG.md`](CHANGELOG.md) — restoring the API the original Flutter-frontend scope always intended, without the original implementation's problems.
