# QuizSphere — Quiz System

A Laravel 12 quiz platform where admins build quizzes, users take them, and passing
users earn a downloadable PDF certificate.

Built with Laravel 12, Tailwind CSS 4, Vite, MySQL and Pest.

---

## Documentation

Full technical documentation lives in [`docs/`](docs/README.md):

| Document | Contents |
|---|---|
| [Overview](docs/01-overview.md) | What the application does and every feature |
| [Architecture](docs/02-architecture.md) | Layers, request lifecycle, directory map, design decisions |
| [Tech stack & languages](docs/03-tech-stack.md) | PHP, Blade, JavaScript, CSS, SQL and every dependency with versions |
| [Modules](docs/04-modules.md) | The seven functional modules and their responsibilities |
| [Data model](docs/05-data-model.md) | Every table, column, relationship and status flag |
| [Routing reference](docs/06-routing.md) | All 46 routes with their middleware and controller action |
| [Frontend](docs/07-frontend.md) | Blade templates, components, Tailwind theme, build pipeline |
| [Certificates](docs/08-certificates.md) | The certificate and PDF generation pipeline |
| [Authentication](docs/09-authentication.md) | Session model, e-mail verification, password reset |
| [Configuration & operations](docs/10-configuration.md) | Environment variables, commands, deployment |
| [Testing](docs/11-testing.md) | The 72-test suite and how to extend it |
| [Maintenance notes](docs/12-maintenance-notes.md) | Dead code, quirks and where to add new features |

---

## Features

### Public
- Landing page with featured categories and quizzes
- Category browsing with pagination
- Quiz search across categories and quiz names
- Responsive dark UI with a mobile nav drawer

### Users
- Sign up with email verification — **an account cannot sign in until the email address is confirmed**
- After signing up you land on a "check your inbox" screen; the verification link
  confirms the account and sends you to the sign-in form
- If the email never arrives, request a new verification link from that screen
  (or from the hint on the sign-in page)
- Sign in / sign out, forgot-password and reset-password flows
- Inline validation: each error renders under the field that caused it, and the
  values you already typed stay filled in (passwords are never re-filled)
- Attempt a quiz question by question with a live progress bar
- Resume an unfinished attempt exactly where it was left off
- Answer review after finishing, with the correct answer per question
- Attempt history with score, percentage and pass/fail state
- Certificate page and downloadable PDF certificate (earned at **70 % or higher**)
- Attempts are never duplicated by reloading or re-entering a quiz

### Admins
- Dashboard with platform statistics — every card links to its own management page
- Manage **users** (attempts per member, verification status)
- Manage **categories** (create, browse, delete)
- Manage **quizzes** (per category, question and attempt counts, edit, delete)
- Manage **questions** (every question of every quiz, with its correct answer, delete)
- Manage **quiz attempts** (who attempted what, score, pass/fail state)
- Create a quiz, then add multiple-choice questions to it
- Review and manage the questions of any quiz
- Delete quizzes (questions and attempts cascade) or single questions

---

## Requirements

| | |
|---|---|
| PHP | 8.4+ (with `pdo_mysql`, `mbstring`, `openssl`, `dom`, `gd`) |
| Composer | 2.x |
| Node.js | 20+ |
| Database | MySQL 8 / MariaDB 10.4+ |

---

## Installation

### 1. Install the dependencies

```bash
composer install
npm install
```

### 2. Configure the environment

```bash
cp .env.example .env
php artisan key:generate
```

Set your database credentials in `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=quiz_system
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Create the database and load the demo data

```bash
php artisan migrate --seed
```

That creates every table and loads the demo admin, a demo user, four categories,
five quizzes and twenty questions.

> **Prefer phpMyAdmin / shared hosting?** Import `database/quiz_system.sql`
> instead. It creates the same schema and demo data. Afterwards run
> `php artisan migrate` once so Laravel records the schema baseline.

### 4. Build the front-end assets

```bash
npm run build       # production
npm run dev         # hot reloading during development
```

### 5. Serve the application

```bash
php artisan serve
```

The app is now available at <http://127.0.0.1:8000>.

---

## Demo credentials

| Role | Login | Password |
|---|---|---|
| Admin | `admin` | `admin123` |
| User | `demoUser@example.com` | `password123` |

---

## Testing

The test suite runs against an in-memory SQLite database, so it never touches
your development database.

```bash
php artisan test
```

| File | Covers |
|---|---|
| `tests/Feature/PublicPagesTest.php` | Public pages, search, guest redirects |
| `tests/Feature/AuthenticationTest.php` | Signup, login, logout, password reset, inline validation |
| `tests/Feature/QuizAttemptTest.php` | Attempt lifecycle, resume, answer review, certificates |
| `tests/Feature/AdminPanelTest.php` | Categories, quizzes, questions, deletions, management pages |

---

## Certificates

The certificate page (`/certificate/{id}`) and the PDF download
(`/download-certificate/{id}`) are rendered from **one** template, so the
downloaded file looks exactly like the certificate shown on screen.

| File | Purpose |
|---|---|
| `resources/views/partials/certificate-card.blade.php` | The card itself, shared by the page and the PDF |
| `public/css/certificate.css` | Card styles, shared by the page and the PDF |
| `public/css/certificate-fonts.css` | `@font-face` rules for the certificate fonts |
| `public/fonts/` | Great Vibes and Playfair Display (TrueType), embedded in the PDF |
| `public/images/cert-*.svg` | Logo and icons used on the card |
| `public/images/cert-edge.png` | Gradient strip used as the left/right border of the card |
| `resources/views/components/gradient-bar.blade.php` | Draws the gradient strips (the PDF renderer has no CSS gradients) |

The PDF renderer (Dompdf) supports neither flexbox nor CSS gradients nor web
fonts, so the card is built with tables, the gradients are drawn as rows of
coloured cells and the fonts are read from `public/fonts` instead of a CDN.
The vertical border of the card is a small PNG that is stretched over the
full height of the card; regenerate it with `php artisan certificate:edge-image`
after changing the brand colours. When the card changes, change these shared
files — never only one of the two renderings.

The two stylesheets live in `public/css` and are loaded directly by the
certificate page, so the certificate never depends on a frontend build
(`npm run build`). The PDF inlines those very same files.

---

## Project layout

```
app/
  Http/Controllers/AdminController.php   Admin panel (categories, quizzes, questions)
  Http/Controllers/UserController.php    Public site, auth, quiz attempts, certificates
  Http/Middleware/                       CheckUserAuth / CheckAdminAuth guards
  Models/                                Admin, User, Category, Quiz, Mcq, Record, MCQ_Record
database/
  migrations/                            Schema for every table
  seeders/QuizSphereSeeder.php           Demo data
  factories/                             Model factories used by the tests
  quiz_system.sql                        Standalone phpMyAdmin installer
resources/views/                         Blade templates
routes/web.php                           All routes
tests/Feature/                           Pest feature tests
```

---

## How the data model fits together

```
Category ──< Quiz ──< Mcq
                    │
                    └──< Record (one quiz attempt) ──< MCQ_Record (one answer)
```

- A **Record** is a single attempt at a quiz. `status = 1` means in progress,
  `status = 2` means finished.
- An **MCQ_Record** stores the answer the user picked for one question and
  whether it was correct.
- A **user** is only allowed to sign in once `active = 2` (email verified);
  `active = 1` means the address has not been confirmed yet.
- A **certificate** is only issued once an attempt is finished *and* the score
  is at least 70 %.

See [docs/05-data-model.md](docs/05-data-model.md) for the full schema, every
column and the ER diagram.

---

## License

MIT
