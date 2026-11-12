# 1 — Overview

## 1.1 What QuizSphere is

QuizSphere is a full-stack quiz platform built on Laravel 12. It has two front
doors that share one data model:

* A **public site** where visitors browse categories and quizzes, search, register
  and take quizzes.
* An **admin panel** where administrators author categories, quizzes and
  multiple-choice questions, and monitor every user attempt.

The two are joined by the **certificate**: when a user finishes a quiz with a
score of at least 70 %, the application issues a personalised certificate that can
be viewed in the browser and downloaded as a PDF. The PDF is rendered from the
very same template as the on-screen certificate, so the two are pixel-identical.

## 1.2 Feature inventory

### Public site (no sign-in required)

| Feature | Route | Notes |
|---|---|---|
| Landing page | `GET /` | Six most-popular categories and six newest quizzes |
| Category browser | `GET /categories` | Paginated, 9 per page, ordered by quiz count |
| Quiz list per category | `GET /user-quiz-list/{id}/{category}` | Paginated, 9 per page |
| Quiz start screen | `GET /start-quiz/{id}/{name}` | Shows question count, pass mark, and "Start"/"Continue" |
| Quiz search | `GET /search-quiz?search=…` | `LIKE` match on category **and** quiz names |
| Sign up | `GET /user-signup`, `POST /user-signup` | Creates an **unverified** account, sends a verification link |
| Sign in | `GET /login`, `POST /login` | One unified form for users *and* admins |
| Sign out | `GET /logout` | Clears both the `user` and `admin` session keys |
| Forgot / reset password | `GET /forgot-password`, `POST /user-forgot-password`, … | Link delivered by e-mail |

### Signed-in users

| Feature | Route | Notes |
|---|---|---|
| Take a quiz | `GET /mcq/{id}/{name}`, `POST /submit-next/{id}` | One question per screen, live progress bar |
| Resume an attempt | `GET /resume-quiz/{id}` | Continues at the first unanswered question |
| Answer review | (rendered by `result-page`) | Shows the user's answer and the correct one |
| Attempt history | `GET /user-details` | Score, percentage, pass/fail, certificate link |
| Certificate page | `GET /certificate/{id?}` | The certificate rendered in the browser |
| Certificate PDF | `GET /download-certificate/{id?}/{file?}` | A4 landscape PDF, streamed as a download |

### Admins

| Feature | Route | Notes |
|---|---|---|
| Dashboard | `GET /dashboard` | Platform counters; every card links to its page |
| Users | `GET /admin-users` | Attempts per member, verification status |
| Categories | `GET /admin-categories`, `POST /add-category`, `GET /category/delete/{id}` | Create, browse, delete |
| Quizzes | `GET /admin-quizzes`, `GET /quiz-list/{id}/{category}` | Per-category lists with question counts; the overview page also shows attempt counts |
| Questions | `GET /admin-questions` | Every question of every quiz with its correct answer |
| Attempts | `GET /admin-attempts` | Who attempted what, score, pass/fail |
| Create a quiz | `GET /add-quiz`, `POST /add-quiz`, `POST /add-mcq`, `GET /end-quiz` | Quiz first, then questions |

### Cross-cutting behaviours

* **Email verification is mandatory** — an account cannot sign in until the address is confirmed.
* **Attempts are never duplicated** — reloading a question page or re-entering a
  quiz reuses the open attempt instead of creating a second one.
* **Inline validation** — every error renders underneath the input that caused it
  and the values already typed stay filled in (passwords are never re-filled).
* **Cascade deletes** — deleting a quiz removes its questions and its attempts.
* **Byte-range PDF downloads** — the certificate endpoint supports `Range`
  requests so download managers (IDM, wget, aria2) can size, resume and split
  the file.

## 1.3 Roles and accounts

There are exactly two kinds of account, stored in two different tables:

| | User | Admin |
|---|---|---|
| Table | `users` | `admin` |
| Identifier | e-mail address | user name |
| Session key | `user` | `admin` |
| Landing page after sign-in | `/` | `/dashboard` |
| E-mail verification | **required** | not applicable |
| Model | `App\Models\User` | `App\Models\Admin` |

The sign-in form at `/login` accepts either — it looks for an admin first, then
for a user (`UserController@login`).

## 1.4 Seeded demo content

`database/seeders/QuizSphereSeeder.php` loads:

| Entity | Value |
|---|---|
| Admin | `admin` / `admin123` |
| User | `demoUser@example.com` / `password123` (already verified) |
| Categories | Web Development, Programming, General Knowledge, Science |
| Quizzes | HTML-Basics, CSS-Fundamentals, PHP-Basics, World-Capitals, Human-Body |
| Questions | 20 in total (5 + 4 + 4 + 4 + 3) |

The seeder uses `updateOrCreate`, so it is safe to run repeatedly.

## 1.5 At-a-glance facts

| Metric | Value |
|---|---|
| Routes | 46 application routes (23 public, 6 user-guarded, 16 admin-guarded, 1 health check) |
| Eloquent models | 7 |
| Migrations | 9 |
| Blade templates | 24 pages + 6 components + 1 partial + 2 mail templates |
| Factories | 5 |
| Pest tests | 72 (263 assertions) |
| Artisan commands added by the project | 1 (`certificate:edge-image`) |
| Custom middleware | 2 (`CheckUserAuth`, `CheckAdminAuth`) |
| Custom response classes | 1 (`FileDownloadResponse`) |
| Custom support classes | 1 (`App\Support\Gradient`) |
