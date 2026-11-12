# 12 — Maintenance notes

Things a future maintainer should know before changing the code. None of them
are bugs to be fixed immediately — they are deliberate choices or leftovers that
should not be "cleaned up" by accident.

## 12.1 Dead code

| Item | Location | Note |
|---|---|---|
| `UserController::userLogout()` | `app/Http/Controllers/UserController.php` | **Not referenced by any route.** `/logout`, `/user-logout` and `/admin-logout` all point at `logoutSession()`. `userLogout()` only clears the `user` key, so it would leave an admin signed in. |
| `UserController::userLoginViewRedirect()` | same file | **Not referenced by any route.** It would redirect to `login`. |
| `app/View/` | `app/View/` | An empty directory. The class components (`navbar.php`, `user-footer.php`, `user-navbar.php`) were removed; the equivalent anonymous components now live in `resources/views/components/`. |
| `query` | project root | A stray 8-byte file containing `IDMWFP`. It looks like a leftover from a download test and is not used by anything. |
| `database/database.sqlite` | `database/` | A leftover from an earlier SQLite-based version. The default `.env.example` uses MySQL. |
| `password_reset_tokens` table | migration | Created by the migration but the password-reset flow uses its own encrypted-link mechanism instead. |
| `resources/views/admin-login.blade.php`, `resources/views/user-login.blade.php`, `resources/views/components/navbar.blade.php` | — | Deleted; the unified `/login` page replaced them. The routes `/admin-login` and `/user-login` still exist and simply redirect to `/login`. |

## 12.2 Configuration quirks

| Quirk | Explanation |
|---|---|
| `config/database.php` defaults to `sqlite` | `env('DB_CONNECTION', 'sqlite')`. `.env.example` sets `DB_CONNECTION=mysql`, so the fallback only matters if `.env` is missing. |
| `.env.example` lists `CACHE_STORE=database` | The `cache` and `cache_locks` tables exist, but nothing in the application currently uses the cache. |
| `QUEUE_CONNECTION=sync` | Jobs run inline. `composer dev` still starts `queue:listen`, which is harmless. |
| `APP_MAINTENANCE_STORE=database` is commented out | The maintenance driver is `file`. |
| The mail templates call `@vite(...)` | If the front end has never been built, the e-mails render without styling — the link still works. |

## 12.3 Things that are easy to break

### The certificate must stay in sync

The on-screen page and the PDF share `partials/certificate-card.blade.php` and
`public/css/certificate.css`. **Change the shared files, never one of the two
renderings.** A test asserts that both contain the same card markup.

If you add a CSS feature to the certificate, remember that Dompdf supports
**no flexbox, no grid, no CSS gradients and no box shadows**. Use table layout
and `<x-gradient-bar>`.

### The brand colours live in three places in the certificate pipeline

`#0d9488` / `#10b981` appear in:

* `app/Console/Commands/CertificateEdgeImage.php` (the constants `FROM`, `VIA`, `TO`)
* `resources/views/partials/certificate-card.blade.php` (the `<x-gradient-bar>` calls)
* `public/css/certificate.css` (the `.cert-edge` background colour — `#0d9488`)

Outside the certificate the same teal is also used as `--color-brand-600` in
`resources/css/app.css`, as the default `$from` of the `gradient-bar`
component and in the password-reset e-mail.

After changing them, re-run `php artisan certificate:edge-image`.

### `Record::scopeWithQuiz()` lists its columns explicitly

A `select *` join on `records` and `quizzes` produces two `id`, `created_at`
and `updated_at` columns; the second silently overwrites the first. Keep the
explicit column list when adding a joined column.

### The migrations are idempotent on purpose

`Schema::hasTable()` guards exist so the schema can be created either with
`artisan migrate` or by importing `database/quiz_system.sql`. Do not remove
them, and do not add a `dropColumn` to a `hasColumn()` branch without
thinking about the SQL-import path.

### The delete routes are `GET` requests

`/category/delete/{id}`, `/quiz/delete/{id}` and `/mcq/delete/{id}` are plain
links. They are protected by the `CheckAdminAuth` middleware, but they are not
CSRF-protected beyond the session cookie. If you convert them to `DELETE` or
`POST`, update the links in `resources/views/categories.blade.php`,
`resources/views/quiz-list.blade.php`, `resources/views/admin-quizzes.blade.php`,
`resources/views/admin-questions.blade.php` and `resources/views/show-quiz.blade.php`.

### `addMCQS()` reads the quiz from the session

`POST /add-mcq` attaches the question to the quiz stored in
`Session::put('quizDetails', $quiz)`, unless the request carries a `quiz_id`
field (which the `show-quiz` form does). The session entry is only cleared by
`endQuiz()` or by publishing the quiz, so an abandoned authoring session can
survive for the lifetime of the session.

### The `active` column is a tinyint, not a boolean

`users.active` uses `1 = unverified` and `2 = verified`. The constants live on
the model (`User::STATUS_UNVERIFIED`, `User::STATUS_VERIFIED`); use them
instead of the literals.

### The password is hashed twice over

`User` and `Admin` both use the `hashed` cast, and the controllers also call
`Hash::make()` explicitly. The cast leaves an already-hashed value alone, so
this is safe — but adding a third `Hash::make()` around an existing hash would
break the login.

## 12.4 Git state

The repository has a single commit (`0396db8 Initial commit - Laravel quiz
system`) on `main`, and the working tree contains the whole current
application — many files are modified and many are untracked. Before making a
release, review `git status` and commit the outstanding work.

`.env` is git-ignored; `.env.example` is tracked.

## 12.5 Useful commands

```bash
# Code style
vendor/bin/pint
vendor/bin/pint --test

# Tests
php artisan test --compact
vendor/bin/pest --filter=certificate

# Routes
php artisan route:list
php artisan route:list --path=admin

# Database
php artisan migrate:fresh --seed
php artisan db:seed

# Certificate
php artisan certificate:edge-image

# Front end
npm run dev
npm run build
```

## 12.6 Where to add new features

| Feature | Where |
|---|---|
| A new page for the public site | A method in `UserController` + a route in `routes/web.php` + a template in `resources/views/` |
| A new page for the admin panel | A method in `AdminController` + a route inside the `CheckAdminAuth` group + a template that uses `<x-admin-layout>` |
| A new table | A migration in `database/migrations/` (guard it with `Schema::hasTable()`) + a model in `app/Models/` + a factory in `database/factories/` |
| A new relationship | Add it to the model and document it in [Data model](./05-data-model.md) |
| A new route | Add it to `routes/web.php` and document it in [Routing reference](./06-routing.md) |
| A new e-mail | A mailable in `app/Mail/` + a template in `resources/views/mail/` |
| A new design token | The `@theme` block in `resources/css/app.css` |
