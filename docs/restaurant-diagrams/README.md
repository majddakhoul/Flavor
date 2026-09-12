# Flavor Restaurant System — Diagrams Package

41 diagrams in **Mermaid** format, built directly from the actual Laravel schema:
`database/migrations`, `database/seeders` and the enums referenced in them
(`UserType`, `EmployeePosition`, `Gender`, `ReservationStatus`, `ReservationType`,
`OrderStatus`, `OrderType`, `TableLocation`, `MealAvailability`, `Allergy`).

## Running

Open **`index.html`** in the browser — all diagrams are embedded inside it; no server or build process is required.
(An internet connection is required once to load the Mermaid library from the CDN.)

- Switch diagrams from the sidebar
- Zoom in / out
- **Download SVG** to export any diagram and use it in documentation or a report
- Dark mode

### Adding or Modifying a Diagram

```bash
# Edit any .mmd file or add a new file inside usecase/ activity/ sequence/ state/
python3 build.py     # Automatically regenerates index.html
```

The `.mmd` files are the source files — their contents can be pasted directly into
GitHub / GitLab / Notion / VS Code (with the Mermaid Preview extension) or into
mermaid.live to export them as PNG.

## Structure

```text
restaurant-diagrams/
├── index.html                          # Generated viewer
├── build.py                            # Viewer generator
├── erd-core.mmd, erd.mmd               # Database
├── classes-core.mmd, classes-catalog.mmd, classes.mmd
├── usecase/    manager chef waiter delivery security customer guest + system
├── activity/   same roles + full-flow
├── sequence/   same roles + 7 e2e scenarios
└── state/      6 state diagrams (lifecycle of each entity)
```

## Roles and Coverage

| Role | Use Case | Activity | Sequence | Scope |
| --- | --- | --- | --- | --- |
| `manager` | ✔ | ✔ | ✔ | Staff, menu, offers, tables/locations, oversight of reservations and orders, maintenance, reports |
| `chef` | ✔ | ✔ | ✔ | Kitchen queue, recipe/ingredient stock, meal availability toggle, marking lines prepared |
| `waiter` | ✔ | ✔ | ✔ | Reservations, table assignment, opening and serving dine-in orders |
| `delivery` | ✔ | ✔ | ✔ | Assigned delivery orders, en route / delivered / failed delivery |
| `security` | ✔ | ✔ | ✔ | Reservation code verification, ban check, capacity check at the entrance |
| `customer` | ✔ | ✔ | ✔ | Browse menu/offers, book, order, track, rate |
| `guest` | ✔ | ✔ | ✔ | Public menu/offers browsing and registration, before becoming a customer |

## Cross-Role Scenarios (`sequence/e2e-*`)

| File | Covers |
| --- | --- |
| `e2e-ordering-dinein` | Reservation → seating → order → kitchen → served → completed |
| `e2e-delivery` | Delivery order → kitchen confirms → delivery employee → delivered or failed |
| `e2e-reservation` | Booking → confirmation → table assignment → entrance check by security |
| `e2e-offer-redemption` | Browsing active offers → applying an offer line → expiry edge case |
| `e2e-rating` | Completed order → meal/offer ratings → manager's ratings report |
| `e2e-maintenance` | Issue reported → maintenance logged → table taken out of / back into rotation |
| `e2e-staff-onboarding` | Manager creates the employee → account activation → role-specific first screen |

## State Diagrams (`state/`)

`order` · `reservation` · `table` · `meal` · `offer` · `customer-account`

Each transition is labeled with the action or condition that triggers it, including rules
inferred directly from the schema:

- A `Delivery` order needs an employee with `position=Delivery` assigned before it can move to `Confirmed`.
- A meal marked `unavailable` is hidden from the menu and cannot be added to a new order or offer bundle.
- An offer is only returned to customers when `is_active=true` **and** today falls within `start_date`/`end_date`.
- A banned customer (`ban=true`) is blocked at the door by security.

## Notes on Design Choices

- **No `price` column on `meals`.** The schema stores a `percentage` (margin) field instead.
  The class diagrams model `costPrice()` (from `ingredient_meal.quantity × ingredient.unit_cost`)
  and `sellingPrice()` (cost marked up by `percentage`) as computed accessors, not stored columns.
- **No payment/invoice table exists**, so no Stripe-style payment flow was invented — order
  `status=Completed` is treated as covering service and payment together (cash at the table,
  cash/COD on delivery), consistent with what the schema actually supports.
- **Framework-only tables** (`sessions`, `jobs`, `job_batches`, `failed_jobs`,
  `password_reset_tokens`) are intentionally left out of the ERD — they carry no business
  relationships worth diagramming. `personal_access_tokens`, `translations` and `notifications`
  are polymorphic and are shown as standalone entities in `erd.mmd` without a fixed
  relationship line, since Mermaid can't represent a variable polymorphic target cleanly.
- **ERD and class diagrams were split into smaller, domain-clustered files** (`*-core` /
  `*-catalog` / full) specifically to keep Mermaid's auto-layout from routing relationship
  lines through entity boxes on the larger schema.
