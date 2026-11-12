# 3 — Tech stack & languages

## 3.1 Languages

| Language | Version | Where it is used | Notes |
|---|---|---|---|
| **PHP** | `^8.4` (constraint in `composer.json`; developed on 8.5) | All server-side code: controllers, models, middleware, mailables, migrations, factories, seeders, the artisan command and the test suite | The project uses modern PHP: typed properties and return types, `str_contains()` (`UserController.php`), the null-safe operator `?->` (`McqFactory.php`), `sprintf()`, and filesystem access through `file_get_contents()`, `is_dir()` and `mkdir()` (the certificate stylesheets and the Dompdf font cache). `composer.json` requires `^8.4`. |
| **Blade** (Laravel's templating language) | Laravel 12 | `resources/views/**` — 24 page templates, 6 anonymous components, 1 shared partial, 2 mail templates | Compiled by Laravel into plain PHP under `storage/framework/views`. |
| **JavaScript** (ES modules) | ES2022+ | `resources/js/app.js`, `resources/js/bootstrap.js`, plus inline `<script>` blocks in the navbar, the admin layout and the quiz-list view | No framework — the app is server-rendered; JavaScript only drives the mobile menu, the avatar dropdown, the admin sidebar drawer and a small dismiss handler. |
| **CSS** | CSS3 + Tailwind CSS 4 utility classes | `resources/css/app.css` (Tailwind entry point) and `public/css/certificate.css` + `certificate-fonts.css` (plain CSS for the certificate) | Tailwind is compiled by Vite; the certificate CSS is plain CSS, served directly from `public/` so the certificate never depends on a frontend build. |
| **SQL** | MySQL 8 / MariaDB 10.4+ dialect | `database/migrations/*.php` (schema builder) and `database/quiz_system.sql` (standalone installer) | The test suite runs the same migrations on SQLite in memory. |
| **HTML** | HTML5 | Every Blade template | Two layouts: a full page per view (no `x-layout` wrapper) and the admin shell component. |

> **Localisation:** the application is English-only. There is **no** `lang/`
> directory and no `resources/lang/` folder, no translation files and no
> `__()` / `@lang` calls. `APP_LOCALE` is set to `en` in `.env.example`. Adding
> a second language means creating `lang/` and replacing the hard-coded strings
> in the Blade templates.

## 3.2 PHP dependencies

### Production (`composer.json` → `require`)

| Package | Constraint | Installed | Purpose |
|---|---|---|---|
| `php` | `^8.4` | — | Runtime requirement |
| `laravel/framework` | `^12.0` | v12.71.0 | The framework |
| `dompdf/dompdf` | `^3.1` | v3.1.6 | Renders the certificate PDF |
| `laravel/tinker` | `^2.10.1` | v2.10.1 | Interactive REPL for `artisan tinker` |

Dompdf is the **only** third-party production package. Everything else —
authentication, validation, sessions, mail, routing, the queue, the cache, the
scheduler — comes from the framework itself.

### Development (`composer.json` → `require-dev`)

| Package | Constraint | Installed | Purpose |
|---|---|---|---|
| `pestphp/pest` | `^3.8` | v3.8.7 | Test framework (PHPUnit wrapper with a functional API) |
| `pestphp/pest-plugin-laravel` | `^3.2` | v3.2.0 | Pest helpers for Laravel (model factories, HTTP assertions) |
| `laravel/pint` | `^1.24` | v1.25.1 | Code-style fixer (`preset: laravel`) |
| `fakerphp/faker` | `^1.23` | v1.24.1 | Fake data for the model factories |
| `mockery/mockery` | `^1.6` | 1.6.12 | Mock objects (available to Pest) |
| `nunomaduro/collision` | `^8.6` | v8.9.5 | Prettier error output for Artisan commands |
| `laravel/sail` | `^1.41` | v1.46.0 | Optional Docker development environment |
| `laravel/pail` | `^1.2.2` | v1.2.3 | `artisan pail` — tail the log file |

### PSR-4 autoloading

| Prefix | Directory |
|---|---|
| `App\` | `app/` |
| `Database\Factories\` | `database/factories/` |
| `Database\Seeders\` | `database/seeders/` |
| `Tests\` | `tests/` (autoload-dev) |

## 3.3 JavaScript dependencies

`package.json` declares **no** runtime dependencies — everything is a
devDependency, because the frontend is compiled by Vite.

| Package | Constraint | Installed | Purpose |
|---|---|---|---|
| `vite` | `^7.0.7` | 7.1.10 | Bundler / dev server |
| `laravel-vite-plugin` | `^2.0.0` | 2.0.1 | Bridges Vite and Laravel's `@vite` directive |
| `tailwindcss` | `^4.0.0` | 4.1.14 | Utility CSS framework |
| `@tailwindcss/vite` | `^4.0.0` | 4.1.14 | Tailwind 4's official Vite plugin (no `tailwind.config.js` needed) |
| `axios` | `^1.11.0` | 1.12.2 | HTTP client exposed as `window.axios` |
| `concurrently` | `^9.0.1` | 9.2.1 | Runs the PHP server, the queue worker and Vite side by side |

`package.json` is an ES module (`"type": "module"`).

### npm scripts

| Script | Command | What it does |
|---|---|---|
| `npm run dev` | `vite` | Vite dev server with hot module replacement |
| `npm run build` | `vite build` | Production build into `public/build` |

## 3.4 Composer scripts

| Script | What it does |
|---|---|
| `composer setup` | `composer install` → copy `.env` → `key:generate` → `migrate --force` → `npm install` → `npm run build` |
| `composer dev` | Runs `php artisan serve`, `php artisan queue:listen --tries=1` and `npm run dev` concurrently (Colours: blue / purple / orange) |
| `composer test` | `config:clear` then `artisan test` |

## 3.5 Configuration files

| File | Purpose |
|---|---|
| `vite.config.js` | Vite entry points (`resources/css/app.css`, `resources/js/app.js`) + the Tailwind plugin |
| `tailwind` config | **None** — Tailwind 4 is configured with CSS-first `@theme` inside `resources/css/app.css` |
| `pint.json` | `{"preset": "laravel"}` — the code-style rules |
| `phpunit.xml` | Test suites (`Unit`, `Feature`) and the testing environment variables |
| `.editorconfig` | UTF-8, LF, 4-space indent, final newline, trimmed whitespace |
| `.gitattributes` | `* text=auto eol=lf` plus per-extension diff drivers |
| `.gitignore` | Ignores `.env`, `vendor/`, `node_modules/`, `public/build`, `storage/*.key`, … |

## 3.6 Runtime requirements

| | Minimum | Notes |
|---|---|---|
| PHP | 8.4 | Extensions: `pdo_mysql`, `mbstring`, `openssl`, `dom`, `gd` (`gd` is needed by `certificate:edge-image`) |
| Composer | 2.x | |
| Node.js | 20+ | Only needed to build the frontend |
| Database | MySQL 8 / MariaDB 10.4+ | The test suite uses in-memory SQLite instead |

## 3.7 Code style

The project follows the **Laravel** preset. Run the fixer before committing:

```bash
vendor/bin/pint            # fix
vendor/bin/pint --test     # check only
```

Style conventions visible throughout the code base:

* Four spaces for indentation, never tabs.
* `use` imports sorted alphabetically, one per line.
* Opening brace on the same line for classes and methods, on the next line for
  control structures.
* Trailing commas in multi-line arrays and argument lists.
* Doc-blocks on every public method of a class.
