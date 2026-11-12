# 2 — Architecture

## 2.1 The stack in one picture

```
        Browser
           │  HTTP (session cookie + CSRF token)
           ▼
┌──────────────────────────────────────────────────────────────┐
│  Laravel 12  (bootstrap/app.php)                            │
│                                                              │
│   routes/web.php ──► Middleware groups                      │
│        │                    │                                │
│        │                    ├─ CheckUserAuth  (session)      │
│        │                    └─ CheckAdminAuth (session)      │
│        ▼                                                     │
│   Controllers  ──►  Eloquent models  ──►  MySQL             │
│        │                    (7 models, 7 tables)             │
│        │                                                     │
│        ├──► Blade views  ──►  Tailwind CSS 4 / Vite 7       │
│        ├──► Mailable     ──►  SMTP / log mailer             │
│        └──► Dompdf       ──►  certificate PDF               │
└──────────────────────────────────────────────────────────────┘
```

## 2.2 Layers

| Layer | Directory | Responsibility |
|---|---|---|
| Routing | `routes/web.php` | Declares every URL, its HTTP verb and its middleware group |
| HTTP | `app/Http/Controllers` | Validates input, orchestrates models, chooses a view |
| HTTP | `app/Http/Middleware` | Session-based access control for the two account types |
| HTTP | `app/Http/Responses` | `FileDownloadResponse` — a range-aware file download |
| Domain / persistence | `app/Models` | Seven Eloquent models, the relationships and two query scopes |
| Support | `app/Support` | `Gradient` — converts CSS gradients into rows of solid colours |
| Console | `app/Console/Commands` | `certificate:edge-image` — regenerates the certificate border PNG |
| Mail | `app/Mail` | `VerifyUser`, `UserForgotPassword` mailables |
| Presentation | `resources/views` | Blade templates, anonymous components and one shared partial |
| Schema | `database/migrations` | Nine migrations covering six domain tables plus the Laravel defaults |
| Tests | `tests` | 72 Pest tests over an in-memory SQLite database |

There is no service layer, no repository layer and no API layer: the
controllers talk to the models directly. For an application of this size that
is a deliberate simplification rather than an omission.

## 2.3 Request lifecycle

A typical user request looks like this:

1. `public/index.php` boots Laravel and hands the request to the kernel.
2. The `web` middleware group runs (sessions, cookies, CSRF, …).
3. `routes/web.php` matches the URI and applies any custom group
   (`CheckUserAuth` or `CheckAdminAuth`).
4. The middleware reads the session. If the account is missing, the request is
   redirected to `/login` with a flash message. `CheckUserAuth` additionally
   re-reads the user row from the database so a stale session copy cannot be
   used to bypass the e-mail verification.
5. The controller action validates the input, queries the models and renders a
   Blade view.
6. The response is returned; flash messages from the session are consumed and
   removed automatically by Laravel.

## 2.4 Directory map

```
.
├── app/
│   ├── Console/Commands/
│   │   └── CertificateEdgeImage.php      artisan certificate:edge-image
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php       admin panel (16 actions)
│   │   │   ├── Controller.php             abstract base class
│   │   │   └── UserController.php         public site, auth, attempts, certificates (23 routed actions)
│   │   ├── Middleware/
│   │   │   ├── CheckAdminAuth.php        session guard for admins
│   │   │   └── CheckUserAuth.php         session guard for users
│   │   └── Responses/
│   │       └── FileDownloadResponse.php  range-aware download response
│   ├── Mail/
│   │   ├── UserForgotPassword.php        password-reset mailable
│   │   └── VerifyUser.php                verification mailable
│   ├── Models/
│   │   ├── Admin.php                     table `admin`
│   │   ├── Category.php
│   │   ├── MCQ_Record.php                table `mcq_records`
│   │   ├── Mcq.php
│   │   ├── Quiz.php
│   │   ├── Record.php                    one quiz attempt
│   │   └── User.php
│   ├── Providers/AppServiceProvider.php  empty by default
│   └── Support/Gradient.php              gradient → solid colour stops
│
├── bootstrap/
│   ├── app.php                           routing + middleware registration
│   └── providers.php
│
├── config/                               app, auth, cache, database, filesystems,
│                                         logging, mail, queue, services, session
│
├── database/
│   ├── migrations/                       9 migrations
│   ├── factories/                        5 model factories
│   ├── seeders/                          DatabaseSeeder → QuizSphereSeeder
│   ├── quiz_system.sql                   standalone phpMyAdmin installer
│   └── database.sqlite                   legacy SQLite file
│
├── public/
│   ├── build/                            Vite output (git-ignored)
│   ├── css/                              certificate.css, certificate-fonts.css
│   ├── fonts/                            Great Vibes + Playfair Display (TTF)
│   └── images/                           cert-*.svg, cert-edge.png
│
├── resources/
│   ├── css/app.css                       Tailwind 4 entry point + theme
│   ├── js/                               app.js, bootstrap.js
│   └── views/                            Blade templates
│       ├── components/                   6 anonymous Blade components
│       ├── mail/                         2 mail templates
│       ├── partials/                     certificate-card (shared page + PDF)
│       └── *.blade.php                   24 page templates
│
├── routes/
│   ├── web.php                           every application route
│   └── console.php                       the `inspire` command
│
├── storage/                              logs, sessions, compiled views, font cache
├── tests/                                Pest suites (Feature + Unit)
├── .env.example                          environment template
├── composer.json / package.json          dependency manifests
├── phpunit.xml                           PHPUnit / Pest configuration
├── pint.json                             code-style preset (`laravel`)
└── vite.config.js                        Vite + Tailwind plugin
```

## 2.5 Application bootstrap

`bootstrap/app.php` uses the Laravel 11+ slim skeleton:

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('CheckUserAuth', [
            CheckUserAuth::class,
        ]);
        $middleware->appendToGroup('CheckAdminAuth', [
            CheckAdminAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
```

Two notes:

* `appendToGroup` is used rather than `alias()` so the middleware can also be
  applied with the full class name if a future route needs a per-route variant.
* `withExceptions` is an empty stub — the application uses Laravel's default
  error handling (HTML error pages in local, the log file in production).

## 2.6 Key design decisions

### Session-based authentication, not Laravel guards

The application stores the signed-in account directly in the session
(`Session::put('user', $user)` / `Session::put('admin', $admin)`) instead of
using `Auth::login()`. The consequences are deliberate and documented in
[Authentication & authorisation](./09-authentication.md):

* The middleware must check the session manually — hence `CheckUserAuth` and
  `CheckAdminAuth`.
* `User` still extends `Illuminate\Foundation\Auth\User` so password hashing,
  `remember_token` and the `Notifiable` trait work as usual.

### One template for the certificate and the PDF

`resources/views/partials/certificate-card.blade.php` is included by both
`certificate.blade.php` (the page) and `download-certificate.blade.php` (the
PDF source). A test asserts that both renderings contain the same markup. See
[Certificates](./08-certificates.md).

### CSS that works in the PDF renderer too

Dompdf supports neither flexbox, nor grid, nor CSS gradients, nor web fonts
from a CDN. The certificate therefore uses table layout, draws gradients as
rows of coloured cells (`App\Support\Gradient`) and reads its fonts from
`public/fonts`.

### Idempotent migrations

Every domain migration checks `Schema::hasTable()` first, so the schema can be
provisioned either with `php artisan migrate --seed` **or** by importing
`database/quiz_system.sql` through phpMyAdmin, without the two paths
colliding.

### Attempts are keyed on `(user_id, quiz_id, status = 1)`

There is a unique index on `mcq_records(record_id, mcq_id)`, and
`findOrCreateAttempt()` looks for an open attempt before creating one. Together
these guarantee that reloading a page or re-entering a quiz never produces a
duplicate attempt or a duplicate answer.
