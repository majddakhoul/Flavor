# Packages and commands

This document lists every dependency the project relies on, the reason it is present, and the full set of commands needed to install, run and maintain the application.

---

## 1. Runtime requirements

| Requirement | Version | Notes |
| --- | --- | --- |
| PHP | 8.1 or later | Enums, readonly properties and `Attribute` accessors are used throughout |
| Composer | 2.x | Dependency management |
| MySQL or MariaDB | 8.0 / 10.6 or later | The schema uses enum columns and foreign keys |
| Redis | 6 or later | Optional. The default `.env.example` uses the `sync` queue and `file` cache driver, which need no extra services. Switch to Redis (`QUEUE_CONNECTION=redis`, `CACHE_DRIVER=redis`) for production-style, non-blocking queues |
| Web server | PHP built in server, Nginx or Apache | No special modules required |

There is no Node.js requirement. CSS and JavaScript are plain files served from `public/assets`, and Chart.js is loaded from a CDN on the two pages that draw charts.

---

## 2. PHP dependencies

### Production

| Package | Purpose in this project |
| --- | --- |
| `laravel/framework` | Application framework: routing, Eloquent, queues, validation, mail, localisation |
| `laravel/sanctum` | Token authentication for the JSON API (`/api/v1`); entirely separate from the web session guard |
| `laravel/tinker` | Interactive shell used for inspecting models and debugging seeded data |
| `guzzlehttp/guzzle` | HTTP client required by the framework's mail and notification transports |
| `predis/predis` | Optional Redis client for the cache and queue connections, chosen over the `phpredis` extension so no compilation step is needed. Not required by default — see [Runtime requirements](#1-runtime-requirements) |

### Development

| Package | Purpose in this project |
| --- | --- |
| `fakerphp/faker` | Generates data inside the model factories |
| `laravel/pint` | Formats PHP to a single style across the codebase |
| `laravel/sail` | Optional Docker environment for MySQL and Redis |
| `mockery/mockery` | Test doubles for service level tests |
| `nunomaduro/collision` | Readable console error output |
| `phpunit/phpunit` | Test runner |
| `spatie/laravel-ignition` | Browser error page during development |

### Packages changed since the original project

| Package | History |
| --- | --- |
| `laravel/sanctum` | Removed during the web-only rebuild (`2.0.0` in [CHANGELOG.md](CHANGELOG.md)), since the session guard covered every need at that stage. Reinstated in `2.1.0` to issue the personal access tokens the JSON API authenticates with; it now ships in the Production table above, scoped entirely to the `api` middleware group and independent of the web session guard. |
| OTP package | Removed permanently. Email verification stores a six digit code in the cache with an explicit expiry, which removes a dependency and keeps the logic visible in `AuthService`. |

---

## 3. Front end libraries

| Library | Delivery | Used for |
| --- | --- | --- |
| Chart.js 4.4.1 | CDN, loaded only on the dashboard and finance report | Line, bar and doughnut charts that repaint on theme change |
| Fraunces, Plus Jakarta Sans, JetBrains Mono, IBM Plex Sans Arabic | Google Fonts | Display, interface, numeric and Arabic typography |

Everything else, including the icon sprite and all illustrations, is authored in the repository as SVG.

---

## 4. Installation

```bash
# 1. Install PHP dependencies
composer install

# 2. Create the environment file
cp .env.example .env

# 3. Generate the application key
php artisan key:generate

# 4. Edit .env and set at least:
#    DB_DATABASE, DB_USERNAME, DB_PASSWORD
#    REDIS_HOST, REDIS_PORT
#    MAIL_* if you want real mail rather than the log driver

# 5. Create the schema and demo data
php artisan migrate --seed

# 6. Link the public storage directory so uploaded meal photos resolve
php artisan storage:link
```

---

## 5. Running the application

```bash
# Development server
php artisan serve

# Queue worker — only needed if QUEUE_CONNECTION is switched away from the sync default
php artisan queue:work --queue=mail,reports,maintenance,default

# Scheduler in the foreground during development
php artisan schedule:work
```

In production, register the scheduler with cron:

```
* * * * * cd /path/to/flavor && php artisan schedule:run >> /dev/null 2>&1
```

and supervise the queue worker with Supervisor or systemd.

---

## 6. Database commands

```bash
php artisan migrate                 # Apply pending migrations
php artisan migrate:fresh --seed    # Drop everything and rebuild with demo data
php artisan migrate:rollback        # Undo the last batch
php artisan db:seed                 # Re-run seeders on an existing schema
php artisan db:seed --class=MealSeeder
composer fresh                      # Shortcut for migrate:fresh --seed
```

---

## 7. Application commands

| Command | Schedule | Effect |
| --- | --- | --- |
| `php artisan flavor:sync-availability` | Hourly | Marks meals unavailable when an ingredient runs out and restores them when stock returns |
| `php artisan flavor:lift-bans` | Daily at 00:10 | Clears expired customer booking bans |
| `php artisan flavor:daily-snapshot` | Daily at 00:30 | Builds and caches the dashboard aggregates |

---

## 8. Cache and optimisation

```bash
php artisan optimize            # Cache config, routes and events
php artisan optimize:clear      # Clear every cached artefact
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan cache:clear         # Clear the application cache, including translation tags
```

Run `php artisan optimize:clear` after changing anything in `config/` or `routes/`.

---

## 9. Queue maintenance

The `.env.example` default (`QUEUE_CONNECTION=sync`) runs every queued job immediately, inline, with no worker process — nothing in this section applies until that is changed to `database` or `redis`.

```bash
php artisan queue:work --queue=mail,reports,maintenance,default
php artisan queue:listen                    # Restart automatically on code change
php artisan queue:failed                    # List failures
php artisan queue:retry all                 # Retry every failure
php artisan queue:restart                   # Ask running workers to finish and exit
```

---

## 10. Code quality

```bash
./vendor/bin/pint                # Format the whole codebase
./vendor/bin/pint --test         # Report without writing
php artisan test                 # Run the test suite
php -l path/to/File.php          # Syntax check a single file
```

---

## 11. Troubleshooting

| Symptom | Cause | Fix |
| --- | --- | --- |
| `Predis\Connection\Resource\Exception\StreamInitException` / `Connection refused` on any page | `CACHE_DRIVER` or `QUEUE_CONNECTION` was set to `redis` but no Redis server is running | Start Redis, or set both back to the `.env.example` defaults (`CACHE_DRIVER=file`, `QUEUE_CONNECTION=sync`) |
| Meal photos return 404 | The storage symlink is missing | `php artisan storage:link` |
| Mail is never delivered | `QUEUE_CONNECTION` is set to something other than `sync` and no worker is running | Start `queue:work`, or set `QUEUE_CONNECTION=sync` |
| Language switch has no effect | Config cache is stale | `php artisan optimize:clear` |
| Theme flashes on first paint | The theme cookie is being encrypted | Confirm `flavor_theme` is listed in `EncryptCookies::$except` |
| Charts render but never change with the theme | Chart.js failed to load from the CDN | Check the network, or self host `chart.umd.min.js` under `public/assets/js` |
