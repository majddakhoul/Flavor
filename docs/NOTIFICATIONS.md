# Notification System

Every domain event in Flavor that matters to a human — an order placed, a reservation booked, stock running low, a new hire — reaches the right person on two channels at once: **email** and an **in-app notification feed**, both in that person's own preferred language.

## Design

```
Event  →  Listener  →  Notification (extends FlavorNotification)  →  mail channel   → reused Mailable class
                                                                    →  database channel → notifications table
```

- **`App\Notifications\FlavorNotification`** is the shared base class. A concrete notification is a few lines: which language keys to use, which existing `Mailable` to hand off to for email, an icon, and a colour tone. It never duplicates copy — the mail channel delegates straight to the `Mailable` classes under `app/Mail`, and the database channel resolves its own short title/body from `lang/{locale}/notifications.php`.
- **Recipients are resolved by ability, not by hard-coded role.** A "new order" alert goes to every active user who holds the `sales` ability (managers, waiters, delivery staff) via `UserRepository::activeStaffWithAbility()` — add a new position with that ability later and it starts receiving the alert automatically, no code change required.
- **Language is resolved per recipient, not per actor.** Every user has a `preferred_locale` column, set automatically at registration and refreshed every time they switch language in the UI or via `PUT /profile`. When a notification is built, both the email and the database record are rendered in **the recipient's** language — a manager working in English changing an order's status does not cause the Arabic-speaking customer who placed it to receive an English email. This is enforced centrally in `FlavorNotification::recipientLocale()`, so no individual notification class can get it wrong.
- **Not every notification sends mail.** Operational, high-frequency alerts aimed at staff (new order, new reservation) are **database-only** — nobody wants an inbox full of "new order" emails during a lunch rush. Customer-facing lifecycle events (order confirmed, order status changed, reservation confirmed, reservation status changed, low stock, new hire credentials) send both. This is a deliberate per-notification choice (`channels()`), not a global switch.

## Catalogue

| Notification | Recipient(s) | Channels | Trigger |
| --- | --- | --- | --- |
| `Sales\OrderConfirmed` | The customer | mail + database | `OrderPlaced` |
| `Sales\NewOrderReceived` | Staff with the `sales` ability | database only | `OrderPlaced` |
| `Sales\OrderStatusUpdated` | The customer | mail + database | `OrderStatusChanged` |
| `Reservations\ReservationConfirmed` | The customer | mail + database | `ReservationCreated` |
| `Reservations\NewReservationReceived` | Staff with the `floor` ability | database only | `ReservationCreated` |
| `Reservations\ReservationStatusUpdated` | The customer | mail + database | `ReservationStatusChanged` |
| `Inventory\LowStockAlert` | Staff with the `inventory` ability | mail + database | `LowStockDetected` (latched for 12h per ingredient so it cannot spam) |
| `People\EmployeeCredentialsIssued` | The new employee | mail + database | `EmployeeHired` |
| `People\NewHireAnnounced` | Every active manager | database only | `EmployeeHired` |

Each row is one small class under `app/Notifications/<Domain>/`, wired to its event through a one-purpose `Listener` in `app/Listeners`, registered in `App\Providers\EventServiceProvider`. Adding a tenth notification means adding one class and one line in that provider — nothing else changes.

## Storage

`notifications` is Laravel's standard polymorphic table (`database/migrations/2025_06_02_000100_create_notifications_table.php`): a UUID primary key, the notification's class name as `type`, a `notifiable` morph pair, a `data` JSON payload (`key`, `title`, `body`, `icon`, `tone`, `url`), and `read_at`. Nothing bespoke — any tool that already understands Laravel's notification table works here unmodified.

## Reading notifications

- **Web** — a bell in both the customer header and the staff topbar (`x-notification-bell`), backed by `App\Services\Support\NotificationService`, plus a full `/notifications` page.
- **API** — `GET /api/v1/notifications` (paginated), `GET /api/v1/notifications/unread-count`, `POST /api/v1/notifications/{id}/read`, `POST /api/v1/notifications/read-all`, `DELETE /api/v1/notifications/{id}`. See the **Profile & Notifications** folder in the Postman collection.

## Adding a new notification

1. Add or reuse a domain `Event` and fire it from the `Service` that performs the action.
2. Add a `Notification` class under `app/Notifications/<Domain>/` extending `FlavorNotification`: implement `subjectKey()`, and `mailable()` only if it should send mail.
3. Add its `title`/`body` strings to `lang/en|ar|fr/notifications.php` under that same key.
4. Add a `Listener` that resolves the recipient(s) and calls `->notify()` (single recipient) or `Notification::send()` (many), and register it against the event in `EventServiceProvider`.

No changes are needed anywhere else — the mail channel, the database channel, the bell, the notifications page, and the API endpoints all pick it up automatically.
