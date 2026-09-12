# API Reference

Base URL: `{APP_URL}/api/v1`. Every route below is relative to that prefix. The full, importable collection is [`flavor-api.postman_collection.json`](flavor-api.postman_collection.json) — start there for worked examples; this document is the systematic reference.

## Architecture

The API is a thin JSON layer over the same `Service` / `Repository` / `Policy` / `DTO` classes the web application uses (`app/Services`, `app/Repositories`, `app/Policies`, `app/DTOs`). An API controller never builds a query or writes business logic itself — it validates the request with a `FormRequest` (the very same class the web form uses, wherever the rules are identical), asks a service to do the work, and hands the result to an `App\Http\Resources` class for JSON shaping. Web and API are two delivery mechanisms sharing one domain layer; fixing a bug or a rule in the service layer fixes it for both at once.

```
routes/api.php  →  Http/Controllers/Api/**  →  Http/Requests/**  →  Services/**  →  Repositories/**  →  Models
                                             ↘  Http/Resources/** (response shaping)
                                             ↘  Policies/**       (authorisation)
```

Authentication is [Laravel Sanctum](https://laravel.com/docs/10.x/sanctum) personal access tokens (`Authorization: Bearer <token>`), entirely independent of the web application's session cookies. The `web` and `api` route files, guards and middleware stacks do not intersect.

## Response envelope

```jsonc
// Success
{ "success": true, "message": "Meal created.", "data": { "id": 1, "...": "..." } }

// Paginated list
{ "success": true, "message": null, "data": [ /* ... */ ], "meta": { "current_page": 1, "per_page": 15, "total": 42, "last_page": 3 } }

// Failure
{ "success": false, "message": "The given data was invalid.", "errors": { "email": ["The email has already been taken."] } }
```

HTTP status codes are meaningful and match the body: `200` read/update, `201` created, `422` validation or domain rule violation, `401` missing/invalid token, `403` authenticated but not permitted, `404` not found, `429` rate limited.

## Search, filter, sort, pagination

Implemented once in `App\Support\QueryOptions` and the `Filterable` model trait, applied identically to every `index` endpoint:

| Query param | Notes |
| --- | --- |
| `search` | Matches the resource's `$searchable` columns (see the relevant model). Sanitised and capped at 120 characters; `%`, `_` and `\` are escaped before the `LIKE`. |
| `filters[column]` | Only columns listed in the model's `$filterable` are honoured; unknown keys are silently ignored, never a 500. Array filters: `filters[category_id][]=1&filters[category_id][]=2`. Some filters are custom logic (e.g. `filters[low_stock]=1` on ingredients) — see each model's `filter*` methods. |
| `sort_by`, `sort_dir` | Only columns in `$sortable` are honoured; `sort_dir` is `asc` or `desc` (default `desc`). |
| `per_page` | 1–100, default 15 (12 for menu browsing). |
| `page` | Standard Laravel pagination. |

## Language

`preferred_locale` (`en`, `ar`, `fr`) lives on the `users` table, set at registration from the caller's current locale and updated automatically whenever the user changes language in the app. All validation messages, notification content, and transactional email use it. Anonymous/guest requests resolve locale from, in order: `?locale=`, the `X-Locale` header, then `Accept-Language`, then `config('app.locale')`.

## Authentication flow

1. `POST /auth/register` — creates the account, emails a 6-digit verification code.
2. `POST /auth/verify-email` with `{ "code": "123456" }` (requires the token from step 3, i.e. log in first, or register then immediately login).
3. `POST /auth/login` with `{ "email", "password" }` → `data.token`. Send it as `Authorization: Bearer <token>` from here on.
4. `POST /auth/logout` revokes only the token used for that request — other devices stay signed in.

Password reset (`POST /auth/forgot-password`, `POST /auth/reset-password`) uses Laravel's standard token broker and revokes all of that user's tokens on success.

## Endpoints by domain

### Catalog — public, no token required

| Method | Path | Description |
| --- | --- | --- |
| GET | `/catalog/meals` | Paginated menu |
| GET | `/catalog/meals/featured` | Top-rated meals |
| GET | `/catalog/meals/top-selling` | Meals ranked by quantity sold |
| GET | `/catalog/meals/{meal}` | Meal detail |
| GET | `/catalog/offers` | Paginated bundle offers |
| GET | `/catalog/offers/running` | Offers active right now |
| GET | `/catalog/offers/top-selling` | Offers ranked by quantity sold |
| GET | `/catalog/offers/{offer}` | Offer detail |
| GET | `/catalog/categories` | Paginated categories |
| GET | `/catalog/categories/tree` | Root categories with children nested |
| GET | `/catalog/categories/{category}` | Category detail |
| GET | `/catalog/tables` | Paginated dining tables |
| GET | `/catalog/locations` | Paginated delivery locations |

### Auth

| Method | Path | Auth |
| --- | --- | --- |
| POST | `/auth/register` | none |
| POST | `/auth/login` | none |
| POST | `/auth/forgot-password` | none |
| POST | `/auth/reset-password` | none |
| POST | `/auth/logout` | token |
| POST | `/auth/verify-email` | token |
| POST | `/auth/resend-verification` | token |

### Profile & notifications — any authenticated user

| Method | Path |
| --- | --- |
| GET | `/profile` |
| PUT | `/profile` *(requires verified email)* |
| PUT | `/profile/password` *(requires verified email)* |
| DELETE | `/profile` *(requires verified email)* |
| GET | `/notifications` |
| GET | `/notifications/unread-count` |
| POST | `/notifications/{notification}/read` |
| POST | `/notifications/read-all` |
| DELETE | `/notifications/{notification}` |

### Account — verified customers only

| Method | Path |
| --- | --- |
| GET | `/account/dashboard` |
| GET / POST / PUT / DELETE | `/account/cart`, `/account/cart/{type}/{id}` |
| POST | `/account/checkout` |
| GET | `/account/orders`, `/account/orders/{order}` |
| DELETE | `/account/orders/{order}` (cancel) |
| POST | `/account/orders/{order}/revert`, `/account/orders/{order}/restore` |
| GET | `/account/reservations`, `/account/reservations/{reservation}` |
| GET | `/account/reservations/availability` |
| POST | `/account/reservations` |
| DELETE | `/account/reservations/{reservation}` (cancel) |
| POST | `/account/ratings/meals/{meal}`, `/account/ratings/offers/{offer}` |

### Manage — verified staff only, gated per folder by ability

| Ability | Positions that hold it by default | Covers |
| --- | --- | --- |
| `catalog` | Manager, Chef | Categories, Meals, Offers |
| `inventory` | Manager, Chef | Ingredients |
| `floor` | Manager, Waiter, Security | Tables, Reservations |
| `sales` | Manager, Waiter, Delivery | Orders |
| `people` | Manager | Employees, Customers, Locations, Maintenance |
| `reports` | Manager | Dashboard, finance/inventory/menu reports, most-consumed ingredients |

Every resource follows the same shape: `GET /manage/{resource}` (list), `POST /manage/{resource}` (create), `GET /manage/{resource}/{id}` (show, where the web equivalent has one), `PUT /manage/{resource}/{id}` (update), `DELETE /manage/{resource}/{id}` (delete), plus a handful of resource-specific actions (`/meals/{meal}/ingredients`, `/offers/{offer}/toggle`, `/ingredients/{ingredient}/stock`, `/reservations/{reservation}/status`, `/reservations/{reservation}/tables`, `/orders/{order}/status`, `/orders/{order}/restore`, `/customers/{customer}/ban` and `/unban`, `/employees/payroll`). The full, exact list is the Postman collection — it is generated from this same route file, so the two never drift apart.

## Errors worth knowing about

| Situation | Status | Body |
| --- | --- | --- |
| Validation failure | 422 | `{ "success": false, "message": "...", "errors": { "field": ["..."] } }` |
| Domain rule violated (e.g. cancel an order that already shipped) | the exception's own status, usually 422 | `{ "success": false, "message": "human readable reason" }` |
| No token / expired token | 401 | Laravel's default unauthenticated response |
| Token valid, but missing the required ability | 403 | `{ "success": false, "message": "..." }` |
| Account disabled | 403 | Token is revoked server-side in the same response cycle |
| Email not verified, hitting a verified-only route | 403 | `{ "success": false, "message": "Verify your email address before continuing." }` |

## What is intentionally not in the API

The API never touches the PHP session or issues cookies — it is fully stateless, which is why the shopping cart (`App\Services\Sales\CartService`) is keyed in cache by the authenticated user's ID rather than a session ID, and works identically whether the request came from the browser or a mobile client.
