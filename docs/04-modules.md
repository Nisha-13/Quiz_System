# 4 — Modules

The application is organised into **seven functional modules**. Each module
owns a set of routes, a slice of the controllers, a group of views and (where
relevant) a part of the data model.

| # | Module | Owner | Guards |
|---|---|---|---|
| 1 | Public catalogue | `UserController` | none |
| 2 | Accounts & authentication | `UserController` | none (the session is created here) |
| 3 | Quiz attempts | `UserController` | `CheckUserAuth` |
| 4 | Certificates | `UserController` + `app/Support` + `app/Console` | `CheckUserAuth` |
| 5 | Admin panel | `AdminController` | `CheckAdminAuth` |
| 6 | Mail | `app/Mail` | — |
| 7 | Infrastructure | `bootstrap/`, `config/`, `database/` | — |

---

## 4.1 Module 1 — Public catalogue

**Purpose.** Let a visitor discover quizzes without an account.

**Files**

| File | Role |
|---|---|
| `app/Http/Controllers/UserController.php` | `welcome()`, `categories()`, `userQuizList()`, `startQuiz()`, `searchQuiz()` |
| `resources/views/welcome.blade.php` | Landing page (163 lines) |
| `resources/views/categories-list.blade.php` | Paginated category grid |
| `resources/views/user-quiz-list.blade.php` | Paginated quiz list of one category |
| `resources/views/quiz-search.blade.php` | Search results |
| `resources/views/start-quiz.blade.php` | Start/continue screen with the pass mark |

**Behaviour**

* `welcome()` loads the six categories with the most quizzes and the six newest
  quizzes, each with an `mcqs_count` column added by `withCount()`.
* `categories()` paginates 9 per page, ordered by quiz count descending.
* `userQuizList()` paginates 9 per page, ordered by quiz id descending.
* `searchQuiz()` runs two `LIKE '%…%'` queries — one over `categories`, one over
  `quizzes` — and renders both result sets on the same page.
* `startQuiz()` refuses a quiz without questions
  (`'Oops! This quiz has no MCQs yet.'`) and looks for an open attempt so the
  button can read **Start Quiz** or **Continue Quiz**.

**Routes**

```
GET  /                                     welcome
GET  /categories                           categories
GET  /user-quiz-list/{id}/{category}       userQuizList
GET  /start-quiz/{id}/{name}               startQuiz
GET  /search-quiz                          searchQuiz
```

---

## 4.2 Module 2 — Accounts & authentication

**Purpose.** Registration with mandatory e-mail verification, sign-in for two
account types, sign-out and the password-reset flow.

**Files**

| File | Role |
|---|---|
| `app/Http/Controllers/UserController.php` | `userSignup()`, `loginView()`, `login()`, `logoutSession()`, `verifyUser()`, `verifyEmailView()`, `resendVerificationEmail()`, `userForgotPassword()`, `userResetForgotPassword()`, `userSetForgotPassword()` |
| `app/Http/Middleware/CheckUserAuth.php` | Guard for the user area |
| `app/Http/Middleware/CheckAdminAuth.php` | Guard for the admin area |
| `app/Models/User.php` | `STATUS_UNVERIFIED = 1`, `STATUS_VERIFIED = 2`, `isVerified()` |
| `app/Models/Admin.php` | The administrator account |
| `app/Mail/VerifyUser.php` + `resources/views/mail/verify-user.blade.php` | Verification e-mail |
| `app/Mail/UserForgotPassword.php` + `resources/views/mail/user-forgot-password.blade.php` | Reset e-mail |
| `resources/views/user-signup.blade.php` | Sign-up form |
| `resources/views/login.blade.php` | Unified sign-in form |
| `resources/views/verify-email.blade.php` | "Check your inbox" screen + resend form |
| `resources/views/forgot-password.blade.php` | Forgot-password form |
| `resources/views/user-set-forgot-password.blade.php` | New-password form |

**Key flows**

*Registration* → the account is created with `active = 1` (unverified), the
verification link is e-mailed, and the user is redirected to `/verify-email`.
**No session is created**, so the account cannot sign in yet.

*Verification* → the link carries the e-mail address encrypted with
`Crypt::encryptString()`. `verifyUser()` decrypts it, sets `active = 2` and
`email_verified_at`, then redirects to `/login` — the link proves the address,
it does not sign the user in.

*Sign-in* → `login()` looks for an `admin` with that name first, then a `user`
with that e-mail. An unverified user is redirected to `/verify-email` with an
explanatory flash message.

*Deferred intent* → `loginQuiz()` and `userSignupQuiz()` store the page the user
was trying to reach in `Session::put('quiz_url', url()->previous())`. After a
successful sign-in the user is sent there instead of to `/`.

*Password reset* → the e-mail address is encrypted into a link, mailed, and
decrypted again on the reset page. The new password is validated with
`confirmed` and stored with `Hash::make()`.

*Inline validation* → `withConfirmationError()` copies a "password
confirmation" error onto the `password_confirmation` key so the message appears
under the confirmation field rather than the password field.

**Session keys used**

| Key | Meaning |
|---|---|
| `user` | The signed-in `User` model |
| `admin` | The signed-in `Admin` model |
| `pending_verification_email` | The address awaiting confirmation |
| `quiz_url` | The page the user wanted to reach before signing in |

**Routes**

```
GET  /login                                   loginView
POST /login                                   login
GET  /user-login, /admin-login                redirect to /login
GET  /user-login-quiz                         loginQuiz
GET  /logout, /user-logout, /admin-logout     logoutSession
GET  /user-signup-quiz                         userSignupQuiz
GET  /user-signup                             signup form (redirects if already signed in)
POST /user-signup                             userSignup
GET  /verify-email                            verifyEmailView
GET  /verify-user/{email}                     verifyUser
POST /resend-verification                     resendVerificationEmail
GET  /forgot-password                         (Route::view)
POST /user-forgot-password                    userForgotPassword
GET  /user-forgot-password/{email}            userResetForgotPassword
POST /user-set-forgot-password                userSetForgotPassword
```

---

## 4.3 Module 3 — Quiz attempts

**Purpose.** Let a user answer a quiz one question at a time, resume where they
left off, and review the result.

**Files**

| File | Role |
|---|---|
| `app/Http/Controllers/UserController.php` | `mcq()`, `submitAndNext()`, `resumeQuiz()`, `userDetails()`, plus the private helpers `findOrCreateAttempt()`, `attemptResult()`, `attemptScore()` |
| `app/Models/Record.php` | One attempt; `scopeWithQuiz()` |
| `app/Models/MCQ_Record.php` | One answer; `scopeWithMCQ()` |
| `resources/views/mcq-page.blade.php` | Question screen with a live progress bar |
| `resources/views/result-page.blade.php` | Score ring + answer review table |
| `resources/views/user-details.blade.php` | Attempt history |

**The attempt lifecycle**

```
GET  /start-quiz/{id}/{name}
      │  (looks for an open attempt and shows "Start Quiz" or "Continue Quiz";
      │   no attempt is created here)
      ▼
GET  /mcq/{id}/{name}
      │  findOrCreateAttempt()  →  reuses the open attempt, or creates one
      │  picks the first question that has no MCQ_Record yet
      ▼
POST /submit-next/{id}   (option = a|b|c|d)
      │  validates the option, inserts an MCQ_Record if the question is not
      │  answered yet, then picks the next unanswered question
      ▼
   (no questions left)
      │
      ▼
attemptResult()  →  sets status = 2  →  renders result-page
```

**Session keys used**

| Key | Contents |
|---|---|
| `currentQuiz` | `totalMcq`, `currentMcq`, `quizName`, `quizId`, `recordId` |
| `firstMCQ` | The `Mcq` model that should be shown next |

**Guarantees**

* *No duplicate attempts* — `findOrCreateAttempt()` looks for
  `status = 1` for `(user_id, quiz_id)` and reuses the newest one.
* *No duplicate answers* — `submitAndNext()` checks for an existing
  `MCQ_Record` for `(record_id, mcq_id)` before inserting, and the table also
  carries a unique index on that pair.
* *No cross-quiz answers* — the submitted question must belong to
  `currentQuiz.quizId`, otherwise the request is rejected with
  *"That question does not belong to this quiz."*
* *No cross-user access* — `resumeQuiz()` compares `$record->user_id` with the
  signed-in user.

**Scoring**

`attemptScore($recordId)` returns `['correct' => n, 'total' => n, 'pct' => n]`
where `pct = round(correct / total * 100)`. `userDetails()` computes the same
numbers for all of a user's attempts in one grouped query:

```sql
SELECT record_id, COUNT(*) AS total, SUM(is_correct) AS correct
FROM mcq_records
WHERE record_id IN (...)
GROUP BY record_id
```

**Routes**

```
GET  /mcq/{id}/{name}        mcq
POST /submit-next/{id}        submitAndNext
GET  /resume-quiz/{id}        resumeQuiz
GET  /user-details            userDetails
```

---

## 4.4 Module 4 — Certificates

**Purpose.** Issue and deliver a certificate for a passing attempt, on screen
and as a PDF.

This module is documented in detail in [Certificates](./08-certificates.md).
In short:

| Piece | File |
|---|---|
| Shared card template | `resources/views/partials/certificate-card.blade.php` |
| On-screen page | `resources/views/certificate.blade.php` |
| PDF source template | `resources/views/download-certificate.blade.php` |
| Card styles | `public/css/certificate.css` |
| Font declarations | `public/css/certificate-fonts.css` |
| Gradient helper | `app/Support/Gradient.php` |
| Edge-strip generator | `app/Console/Commands/CertificateEdgeImage.php` |
| Download response | `app/Http/Responses/FileDownloadResponse.php` |
| Eligibility logic | `UserController::loadCertificateRecord()` |

**Rules**

* The attempt must exist, belong to the signed-in user and have `status = 2`.
* The score must be at least `UserController::CERTIFICATE_THRESHOLD` (70 %).
* The file name is always derived from the attempt:
  `certificate-quizsphere-00007.pdf`.

**Routes**

```
GET /certificate/{id?}               certificate
GET /download-certificate/{id?}/{file?}   downloadCertificate
```

---

## 4.5 Module 5 — Admin panel

**Purpose.** Author and manage the content and monitor the activity.

**Files**

| File | Role |
|---|---|
| `app/Http/Controllers/AdminController.php` | 16 actions (see below) |
| `app/Http/Middleware/CheckAdminAuth.php` | Session guard |
| `resources/views/components/admin-layout.blade.php` | The admin shell: sidebar, topbar, flash messages |
| `resources/views/admin.blade.php` | Dashboard |
| `resources/views/admin-users.blade.php` | User management |
| `resources/views/categories.blade.php` | Category management (the admin list) |
| `resources/views/categories-list.blade.php` | The public category grid (module 1) |
| `resources/views/admin-quizzes.blade.php` + `quiz-list.blade.php` | Quiz management (all quizzes) and the per-category list |
| `resources/views/admin-questions.blade.php` | Every question of every quiz |
| `resources/views/admin-attempts.blade.php` | Every attempt |
| `resources/views/add-quiz.blade.php` | Create a quiz, then add questions to it |
| `resources/views/show-quiz.blade.php` | Add more questions to an existing quiz |
| `resources/views/components/pagination.blade.php` | Shared paginator |

**Actions**

| Action | Method | Purpose |
|---|---|---|
| `dashboard()` | GET | Platform counters + the five newest users |
| `categories()` | GET | Every category with its quiz and question counts |
| `addCategory()` | POST | Create a category (`required`, `min:5`, unique) |
| `deleteCategory()` | GET | Delete a category (cascades to quizzes and questions) |
| `showAddQuizForm()` | GET | The quiz-creation form |
| `addQuiz()` | POST | Create the quiz, or open an existing one of the same name in the same category |
| `addMCQS()` | POST | Attach a question to the quiz held in the session |
| `endQuiz()` | GET | Close the quiz session (`Session::forget('quizDetails')`) |
| `showQuiz()` | GET | The question list of one quiz, with an "add question" form |
| `deleteQuiz()` | GET | Delete a quiz, its questions and its attempts |
| `deleteMCQ()` | GET | Delete a single question |
| `quizList()` | GET | The quizzes of one category |
| `users()` | GET | Every user with the number of attempts |
| `quizzes()` | GET | Every quiz with question and attempt counts |
| `questions()` | GET | Every question with its quiz |
| `attempts()` | GET | Every attempt with its score |

**Authoring flow**

1. `GET /admin-categories` → *Create Quiz*.
2. `POST /add-quiz` with a name and a category. If a quiz with the same name
   already exists in that category, the existing one is reused and the session
   key `quizDetails` is refreshed instead of creating a duplicate.
3. `POST /add-mcq` adds a question. The button value decides the next step:
   * `submit = "add-more"` → stay on the form and add another question;
   * anything else → publish the quiz and return to the category list.
4. `GET /end-quiz` closes the authoring session without publishing.

The `category_id` of a question is copied from its quiz, and the `admin_id` from
the signed-in administrator, so a question always knows where it came from.

**Dashboard statistics**

`AdminController::stats()` returns the counters shown in the sidebar and the
dashboard cards: `users`, `categories`, `quizzes`, `mcqs`, `attempts`.

**Routes**

```
GET  /dashboard                 GET  /admin-categories      GET  /admin-users
GET  /admin-quizzes             GET  /admin-questions        GET  /admin-attempts
POST /add-category              GET  /add-quiz               POST /add-quiz
POST /add-mcq                   GET  /end-quiz
GET  /show-quiz/{id}/{quizName} GET  /quiz-list/{id}/{category}
GET  /category/delete/{id}      GET  /quiz/delete/{id}      GET  /mcq/delete/{id}
```

> The three delete routes are registered with literal prefixes
> (`category/delete/…`) so they can never be shadowed by the parameterised
> routes above them.

---

## 4.6 Module 6 — Mail

**Purpose.** Deliver the two transactional e-mails.

| Mailable | Subject | Template | Triggered by |
|---|---|---|---|
| `App\Mail\VerifyUser` | *Verify User* | `resources/views/mail/verify-user.blade.php` | Sign-up and *resend verification* |
| `App\Mail\UserForgotPassword` | *User Forgot Password* | `resources/views/mail/user-forgot-password.blade.php` | *Forgot password* |

Both mailables use the legacy `build()` method rather than the modern
`content()` method, and both carry a single `$link` property.

Mail failures are caught and turned into a friendly flash message, so a broken
SMTP server never blocks the sign-up. The verification mail additionally reports
the exception with `report($e)` before returning `false`; the password-reset
mail only flashes the error message.

The default mailer is `log`, so in local development the e-mails are written to
`storage/logs/laravel.log` instead of being sent.

> The two mail templates use `@vite(...)`, which resolves to the built asset
> URLs. If the front end has never been built, the e-mail renders without
> styling — the link itself still works.

---

## 4.7 Module 7 — Infrastructure

| Concern | Implementation |
|---|---|
| Application bootstrap | `bootstrap/app.php` (slim Laravel 11+ style) |
| Service provider | `app/Providers/AppServiceProvider.php` — empty hooks, ready for custom bindings |
| Health check | `GET /up` (Laravel's built-in health route) |
| Schema | 9 migrations in `database/migrations/` |
| Demo data | `database/seeders/QuizSphereSeeder.php` |
| Test data | 5 factories in `database/factories/` |
| Standalone installer | `database/quiz_system.sql` for phpMyAdmin-based hosting |
| Configuration | 10 files in `config/` |
| Logging | `config/logging.php` — `single` channel to `storage/logs/laravel.log` |
| Queue | `QUEUE_CONNECTION=sync` — jobs run inline, no worker is needed |
| Cache | `CACHE_STORE=database` — uses the `cache` / `cache_locks` tables |
| Sessions | `SESSION_DRIVER=file` — files in `storage/framework/sessions` |
| Password hashing | `BCRYPT_ROUNDS=12`, applied by the `hashed` cast |
| E-mail encryption | `Crypt::encryptString()` (AES-256-CBC, `APP_KEY`) |

### Custom artisan command

```bash
php artisan certificate:edge-image
```

Regenerates `public/images/cert-edge.png` — the 8 × 400 px gradient strip used as
the left and right border of the certificate. It uses the same palette as the
card (`#0d9488 → #10b981 → #0d9488`), draws 200 gradient stops with the GD
extension and writes a transparent PNG.

Run it after changing the brand colours of the certificate.
