# 6 — Routing reference

All application routes live in `routes/web.php`. The health route `/up` is
registered by the framework through `bootstrap/app.php`.

Middleware groups:

| Alias | Class | Effect |
|---|---|---|
| `CheckUserAuth` | `App\Http\Middleware\CheckUserAuth` | Redirects to `/login` unless a **verified** user is in the session |
| `CheckAdminAuth` | `App\Http\Middleware\CheckAdminAuth` | Redirects to `/login` unless an admin is in the session |

Flash-message keys used by the application:

| Key | Tone |
|---|---|
| `message-success` | Success |
| `message-error` | Error |
| `success` / `error` | Success / error (admin panel) |
| `category_name`, `category_delete`, `quiz_info`, `no_mcq` | Specific admin and catalogue notices |
| `email` | The address that is awaiting confirmation. It is written on sign-up, on an unverified sign-in attempt and on a resend, but no view reads it — the verify-email page uses the `pending_verification_email` session key instead |

---

## 6.1 Public routes

No authentication required.

| Method | URI | Action | Purpose |
|---|---|---|---|
| GET | `/` | `UserController@welcome` | Landing page |
| GET | `/categories` | `UserController@categories` | Category grid (paginated) |
| GET | `/user-quiz-list/{id}/{category}` | `UserController@userQuizList` | Quizzes of one category (`id` must be numeric) |
| GET | `/start-quiz/{id}/{name}` | `UserController@startQuiz` | Start / continue screen |
| GET | `/search-quiz` | `UserController@searchQuiz` | Search by `?search=` over categories and quizzes |
| GET | `/user-signup` | closure | Sign-up form; redirects to `/` if already signed in |
| POST | `/user-signup` | `UserController@userSignup` | Register an account (unverified) |
| GET | `/user-signup-quiz` | `UserController@userSignupQuiz` | Sign-up form that remembers the intended quiz URL |
| GET | `/login` | `UserController@loginView` | Unified sign-in form; redirects to `/dashboard` or `/` when already signed in |
| POST | `/login` | `UserController@login` | Sign in as admin **or** user |
| GET | `/user-login` | closure | Redirects to `/login` (legacy link) |
| GET | `/admin-login` | closure | Redirects to `/login` (legacy link) |
| GET | `/user-login-quiz` | `UserController@loginQuiz` | Redirects to `/login`, remembering the previous page |
| GET | `/logout` | `UserController@logoutSession` | Clears both session keys |
| GET | `/user-logout` | `UserController@logoutSession` | Alias of `/logout` |
| GET | `/admin-logout` | `UserController@logoutSession` | Alias of `/logout` |
| GET | `/verify-email` | `UserController@verifyEmailView` | "Check your inbox" screen + resend form |
| GET | `/verify-user/{email}` | `UserController@verifyUser` | Opens the verification link (`{email}` is the encrypted address) |
| POST | `/resend-verification` | `UserController@resendVerificationEmail` | Sends a new verification link |
| GET | `/forgot-password` | `Route::view` | Forgot-password form |
| POST | `/user-forgot-password` | `UserController@userForgotPassword` | Mails the reset link |
| GET | `/user-forgot-password/{email}` | `UserController@userResetForgotPassword` | The reset form (`{email}` is encrypted) |
| POST | `/user-set-forgot-password` | `UserController@userSetForgotPassword` | Stores the new password |

## 6.2 User routes — `CheckUserAuth`

| Method | URI | Action | Purpose |
|---|---|---|---|
| GET | `/mcq/{id}/{name}` | `UserController@mcq` | The question screen; opens or reuses the attempt |
| POST | `/submit-next/{id}` | `UserController@submitAndNext` | Stores the answer and loads the next question |
| GET | `/resume-quiz/{id}` | `UserController@resumeQuiz` | Continues an open attempt |
| GET | `/user-details` | `UserController@userDetails` | Attempt history with scores |
| GET | `/certificate/{id?}` | `UserController@certificate` | The certificate page |
| GET | `/download-certificate/{id?}/{file?}` | `UserController@downloadCertificate` | The certificate PDF |

Both certificate routes accept an optional id. Without one, the most recent
completed attempt of the quiz stored in the `currentQuiz` session key is used.

The `{file?}` segment of the download route exists **only** so the URL ends
with `.pdf` — the real file name is always derived from the attempt id.

## 6.3 Admin routes — `CheckAdminAuth`

| Method | URI | Action | Purpose |
|---|---|---|---|
| GET | `/dashboard` | `AdminController@dashboard` | Dashboard with platform statistics |
| GET | `/admin-categories` | `AdminController@categories` | Category management |
| GET | `/admin-users` | `AdminController@users` | User management |
| GET | `/admin-quizzes` | `AdminController@quizzes` | Quiz management |
| GET | `/admin-questions` | `AdminController@questions` | Question management |
| GET | `/admin-attempts` | `AdminController@attempts` | Attempt management |
| POST | `/add-category` | `AdminController@addCategory` | Create a category |
| GET | `/add-quiz` | `AdminController@showAddQuizForm` | The quiz-creation form |
| POST | `/add-quiz` | `AdminController@addQuiz` | Create or reopen a quiz |
| POST | `/add-mcq` | `AdminController@addMCQS` | Add a question to the current quiz |
| GET | `/end-quiz` | `AdminController@endQuiz` | Close the authoring session |
| GET | `/show-quiz/{id}/{quizName}` | `AdminController@showQuiz` | The question list of one quiz |
| GET | `/quiz-list/{id}/{category}` | `AdminController@quizList` | The quizzes of one category |
| GET | `/category/delete/{id}` | `AdminController@deleteCategory` | Delete a category |
| GET | `/quiz/delete/{id}` | `AdminController@deleteQuiz` | Delete a quiz, its questions and its attempts |
| GET | `/mcq/delete/{id}` | `AdminController@deleteMCQ` | Delete one question |

The three delete routes are `GET` requests and are therefore link-following —
they can be triggered by a plain link. They are protected by the admin
middleware, but they are not CSRF-protected beyond the session cookie.

## 6.4 Framework routes

| Method | URI | Purpose |
|---|---|---|
| GET | `/up` | Health check — returns 200 while the application boots |
| GET | `/storage/{path}` | Serves files from `storage/app/public` |
| PUT | `/storage/{path}` | Same, with a `PUT` method |

## 6.5 Route constraints

Numeric route parameters are constrained with `->where('id', '[0-9]+')`:

`user-quiz-list`, `start-quiz`, `mcq`, `submit-next`, `resume-quiz`,
`certificate`, `download-certificate`, `show-quiz`, `quiz-list`,
`category/delete`, `quiz/delete`, `mcq/delete`.

The optional parameters of the certificate routes are constrained as well, so
`/certificate/abc` does not match.

## 6.6 URL summary by audience

| Audience | URLs |
|---|---|
| Visitor | `/`, `/categories`, `/user-quiz-list/…`, `/start-quiz/…`, `/search-quiz`, `/user-signup`, `/login`, `/forgot-password`, `/verify-email`, `/verify-user/…` |
| Signed-in user | `/mcq/…`, `/submit-next/…`, `/resume-quiz/…`, `/user-details`, `/certificate/…`, `/download-certificate/…`, `/logout` |
| Administrator | `/dashboard`, `/admin-categories`, `/admin-users`, `/admin-quizzes`, `/admin-questions`, `/admin-attempts`, `/add-quiz`, `/show-quiz/…`, `/quiz-list/…`, `/add-category`, `/add-mcq`, `/end-quiz`, `/category/delete/…`, `/quiz/delete/…`, `/mcq/delete/…` |
