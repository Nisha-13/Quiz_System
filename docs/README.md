# QuizSphere — Documentation

Complete technical documentation for the QuizSphere quiz platform.

QuizSphere is a Laravel 12 web application in which **admins** author quizzes and
**users** take them. A finished attempt with a score of 70 % or more earns a
certificate that can be viewed on screen and downloaded as a PDF.

---

## Table of contents

| # | Document | Contents |
|---|---|---|
| 1 | [Overview](./01-overview.md) | What the application does, feature inventory, at-a-glance facts |
| 2 | [Architecture](./02-architecture.md) | Layers, request lifecycle, directory map, design decisions |
| 3 | [Tech stack & languages](./03-tech-stack.md) | Every language, framework, library and tool used, with versions |
| 4 | [Modules](./04-modules.md) | The seven functional modules, their responsibilities and entry points |
| 5 | [Data model](./05-data-model.md) | Tables, columns, relationships, status flags, ER diagram |
| 6 | [Routing reference](./06-routing.md) | Every route, its method, middleware, controller action and view |
| 7 | [Frontend](./07-frontend.md) | Blade templates, Blade components, CSS design system, JS, asset build |
| 8 | [Certificates](./08-certificates.md) | The certificate + PDF generation pipeline in depth |
| 9 | [Authentication & authorisation](./09-authentication.md) | Session model, email verification, password reset, guards |
| 10 | [Configuration & operations](./10-configuration.md) | Environment variables, artisan commands, demo data, deployment |
| 11 | [Testing](./11-testing.md) | Test suite layout, coverage map, how to run |
| 12 | [Maintenance notes](./12-maintenance-notes.md) | Known quirks, dead code, and where to be careful when extending |

---

## How to read this documentation

| If you want to… | Read |
|---|---|
| Install and run the app locally | [Installation](../README.md#installation) and [Configuration & operations](./10-configuration.md) |
| Understand the code layout | [Architecture](./02-architecture.md) |
| Know exactly what is used and how | [Tech stack & languages](./03-tech-stack.md) |
| Find the code for a feature | [Modules](./04-modules.md) |
| Look up a table or column | [Data model](./05-data-model.md) |
| Look up a URL | [Routing reference](./06-routing.md) |
| Change the design | [Frontend](./07-frontend.md) |
| Change the certificate | [Certificates](./08-certificates.md) |
| Change how login works | [Authentication & authorisation](./09-authentication.md) |
| Add or fix a test | [Testing](./11-testing.md) |

---

## Conventions used in these documents

* Paths are relative to the project root, e.g. `app/Http/Controllers/UserController.php`.
* `UserController@mcq` means the `mcq()` method of `App\Http\Controllers\UserController`.
* Code blocks marked `php`, `blade`, `js`, `css`, `bash`, `dotenv` or `sql` show the
  language of the snippet.
* Tables named in **bold** are created by migrations in `database/migrations/`.

---

## Project at a glance

| | |
|---|---|
| Name | QuizSphere |
| Framework | Laravel 12 (`laravel/framework ^12.0`) |
| Language | PHP `^8.4` |
| Frontend | Blade templates, Tailwind CSS 4, Vite 7, vanilla JavaScript |
| PDF engine | Dompdf `^3.1` |
| Database | MySQL 8 / MariaDB 10.4+ (SQLite used by the test suite) |
| Test framework | Pest 3 + Pest Plugin for Laravel 3 |
| Code style | Laravel Pint (`preset: laravel`) |
| Licence | MIT |
