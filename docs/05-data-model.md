# 5 — Data model

## 5.1 Entity relationship diagram

```
┌──────────────┐          ┌──────────────┐          ┌──────────────┐
│   categories │ 1      * │    quizzes   │ 1      * │     mcqs     │
│──────────────│─────────>│──────────────│─────────>│──────────────│
│ id           │          │ id           │          │ id           │
│ name (uniq)  │          │ name         │          │ question     │
│ creator      │          │ category_id  │          │ option_a..d  │
│ timestamps   │          │ timestamps   │          │ correct_ans  │
└──────────────┘          └──────┬───────┘          │ admin_id ────┼──> admins
                                  │ 1                │ quiz_id      │
                                  │                  │ category_id  │
                                  │                  │ timestamps   │
                                  │ v                └──────────────┘
                            ┌──────────┐
                            │ records  │ 1
                            │──────────│──────────┐
                            │ id       │          │
                            │ quiz_id  │          │ 1
                            │ user_id  │          │
                            │ status   │          v
                            │ timestamps│     ┌──────────────┐
                            └────┬─────┘     │ mcq_records  │
                                 │           │──────────────│
                                 │           │ id           │
                                 │           │ record_id ───┼─┐
                                 │           │ user_id      │ │
                                 │           │ mcq_id ──────┼─┼──> mcqs
                                 │           │ select_answer│ │
                                 │           │ is_correct   │ │
                                 │           │ timestamps   │ │
                                 │           └──────────────┘ │
                                 └──────────────> users <────┘
                                                    │
                                                    │ 1
                                              ┌─────┴─────┐
                                              │   users   │
                                              │───────────│
                                              │ id        │
                                              │ name      │
                                              │ email     │
                                              │ password  │
                                              │ active    │
                                              │ verified  │
                                              └───────────┘

┌──────────────┐
│    admin     │
│──────────────│
│ id           │
│ name (uniq)  │
│ password     │
│ timestamps   │
└──────────────┘
```

## 5.2 Tables

The application owns **six** domain tables. The `users`, `password_reset_tokens`,
`sessions`, `cache`, `cache_locks`, `jobs`, `job_batches` and `failed_jobs`
tables are the Laravel defaults.

### `categories`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `name` | varchar(100) | **unique** |
| `creator` | varchar(100) | Default `'admin'`; holds the name of the administrator who created it |
| `created_at`, `updated_at` | timestamp | |

### `quizzes`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `name` | varchar(150) | Not unique on its own — the same name may exist in two categories |
| `category_id` | FK → `categories.id` | `cascadeOnUpdate`, `cascadeOnDelete` |
| `created_at`, `updated_at` | timestamp | |

### `mcqs`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `question` | text | |
| `option_a` … `option_d` | varchar(255) | The four answer choices |
| `correct_ans` | enum(`a`,`b`,`c`,`d`) | The key of the right option |
| `admin_id` | FK → `admin.id` | Default `1`; `cascadeOnUpdate` |
| `quiz_id` | FK → `quizzes.id` | `cascadeOnUpdate`, `cascadeOnDelete` |
| `category_id` | FK → `categories.id` | `cascadeOnUpdate`, `cascadeOnDelete` — denormalised copy of the quiz's category |
| `created_at`, `updated_at` | timestamp | |

### `records` — one quiz attempt

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `quiz_id` | FK → `quizzes.id` | `cascadeOnUpdate`, `cascadeOnDelete` |
| `user_id` | FK → `users.id` | `cascadeOnUpdate`, `cascadeOnDelete` |
| `status` | tinyint | **1 = in progress, 2 = completed** (default `1`) |
| `created_at`, `updated_at` | timestamp | |
| *index* | `(user_id, quiz_id)` | Speeds up "find my open attempt for this quiz" |

### `mcq_records` — one answer

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `record_id` | FK → `records.id` | `cascadeOnUpdate`, `cascadeOnDelete` |
| `user_id` | FK → `users.id` | `cascadeOnUpdate`, `cascadeOnDelete` |
| `mcq_id` | FK → `mcqs.id` | `cascadeOnUpdate`, `cascadeOnDelete` |
| `select_answer` | enum(`a`,`b`,`c`,`d`), nullable | What the user picked |
| `is_correct` | tinyint | Default `0`; `1` when the pick matched `correct_ans` |
| `created_at`, `updated_at` | timestamp | |
| *unique* | `(record_id, mcq_id)` | One answer per question per attempt |

### `users`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `name` | varchar(255) | |
| `email` | varchar(255) | **unique** |
| `email_verified_at` | timestamp, nullable | Set when the verification link is opened |
| `password` | varchar(255) | bcrypt hash |
| `active` | tinyint | **1 = unverified, 2 = verified** (default `1`) |
| `remember_token` | varchar(100), nullable | |
| `created_at`, `updated_at` | timestamp | |

### `admin`

| Column | Type | Notes |
|---|---|---|
| `id` | bigint, PK | |
| `name` | varchar(100) | **unique** — this is the administrator's sign-in name |
| `password` | varchar(255) | bcrypt hash |
| `created_at`, `updated_at` | timestamp | |

## 5.3 Status flags

Two tinyint columns carry the application state. Both are documented as SQL
comments in the migrations.

### `users.active`

| Value | Constant | Meaning |
|---|---|---|
| `1` | `User::STATUS_UNVERIFIED` | The e-mail address has not been confirmed. **The account cannot sign in.** |
| `2` | `User::STATUS_VERIFIED` | The address is confirmed. Sign-in is allowed. |

`User::isVerified()` is the only accessor; both `login()` and `CheckUserAuth`
use it.

### `records.status`

| Value | Meaning |
|---|---|
| `1` | The attempt is **in progress** — it can be resumed |
| `2` | The attempt is **completed** — a certificate may be requested |

A record is created with `status = 1` when the first question is opened and
flipped to `2` in `attemptResult()`, which runs when the last question is
answered.

## 5.4 Eloquent relationships

| Model | Relation | Target |
|---|---|---|
| `Category` | `hasMany` | `Quiz` |
| `Category` | `hasManyThrough` | `Mcq` (via `Quiz`) |
| `Quiz` | `belongsTo` | `Category` |
| `Quiz` | `hasMany` | `Mcq` |
| `Quiz` | `hasMany` | `Record` |
| `Mcq` | `belongsTo` | `Quiz` |
| `Mcq` | `belongsTo` | `Category` |
| `Record` | `belongsTo` | `Quiz` |
| `Record` | `belongsTo` | `User` |
| `Record` | `hasMany` | `MCQ_Record` (as `answers()`) |
| `MCQ_Record` | `belongsTo` | `Mcq` |
| `User` | `hasMany` | `Record` |

`Admin` has no relationship to the other models — the link between an
administrator and a question is the plain `mcqs.admin_id` column.

## 5.5 Query scopes

Two local scopes avoid repeating eager-loading or joins.

### `Record::scopeWithQuiz()`

```php
public function scopeWithQuiz($query)
{
    return $query->join('quizzes', 'records.quiz_id', '=', 'quizzes.id')
        ->select([
            'records.id', 'records.quiz_id', 'records.user_id', 'records.status',
            'records.created_at', 'records.updated_at',
            'quizzes.name as quiz_name', 'quizzes.category_id',
        ]);
}
```

The columns are listed explicitly on purpose: a `select *` join would produce
two `id`, `created_at` and `updated_at` columns and the second would overwrite
the first.

### `MCQ_Record::scopeWithMCQ()`

```php
public function scopeWithMCQ($query)
{
    return $query->with('mcq');
}
```

Used by the answer review to avoid an N+1 query when rendering every question
of an attempt.

## 5.6 Casts, fillable and hidden attributes

| Model | `$fillable` | `$hidden` | Casts |
|---|---|---|---|
| `Admin` | `name`, `password` | `password` | `password` → `hashed` |
| `User` | `name`, `email`, `password`, `active` | `password`, `remember_token` | `email_verified_at` → `datetime`, `password` → `hashed` |
| `Category` | `name`, `creator` | — | — |
| `Quiz` | `name`, `category_id` | — | — |
| `Record` | `quiz_id`, `user_id`, `status` | — | — |
| `Mcq` | *(none — always assigned explicitly)* | — | — |
| `MCQ_Record` | *(none — always assigned explicitly)* | — | — |

> The `hashed` cast means a plain-text password assigned to a model is hashed
> automatically. The application nevertheless calls `Hash::make()` explicitly
> in `userSignup()` and `userSetForgotPassword()` — hashing twice would break
> the login, but the `hashed` cast detects an already-hashed value and leaves
> it alone.

## 5.7 Migrations

| File | Creates |
|---|---|
| `0001_01_01_000000_create_users_table.php` | `users`, `password_reset_tokens`, `sessions` |
| `0001_01_01_000001_create_cache_table.php` | `cache`, `cache_locks` |
| `0001_01_01_000002_create_jobs_table.php` | `jobs`, `job_batches`, `failed_jobs` |
| `2024_01_01_000001_create_admin_table.php` | `admin` |
| `2024_01_01_000002_create_categories_table.php` | `categories` |
| `2024_01_01_000003_create_quizzes_table.php` | `quizzes` |
| `2024_01_01_000004_create_mcqs_table.php` | `mcqs` |
| `2024_01_01_000005_create_records_table.php` | `records` |
| `2024_01_01_000006_create_mcq_records_table.php` | `mcq_records` |

Every domain migration is **idempotent**: it checks `Schema::hasTable()` (and,
for `users.active`, `Schema::hasColumn()`) and returns early when the table is
already there. This makes it safe to import `database/quiz_system.sql` first
and then run `php artisan migrate`.

## 5.8 Factories

| Factory | Model | Notable behaviour |
|---|---|---|
| `AdminFactory` | `Admin` | `name` from a unique user name, password `password` |
| `CategoryFactory` | `Category` | Unique two-word name, `creator = 'admin'` |
| `QuizFactory` | `Quiz` | Unique two-word name, belongs to a new category |
| `McqFactory` | `Mcq` | `configure()` copies the quiz's `category_id` onto the question; `admin_id` from `AdminFactory` |
| `UserFactory` | `User` | Verified by default; the `unverified()` state flips `active` to `1` and clears `email_verified_at` |

## 5.9 Data integrity rules enforced in code

| Rule | Where it is enforced |
|---|---|
| A question belongs to exactly one quiz and one category | `AdminController::addMCQS()` copies `quiz.category_id` into `mcq.category_id` |
| A question always records its author | `mcq.admin_id = $admin->id` in `addMCQS()` |
| One answer per question per attempt | Unique index `(record_id, mcq_id)` **and** an explicit existence check in `submitAndNext()` |
| One open attempt per user per quiz | Lookup in `findOrCreateAttempt()` |
| A user only sees their own attempts | `resumeQuiz()` and `loadCertificateRecord()` compare `$record->user_id` |
| A submitted answer belongs to the current quiz | `submitAndNext()` filters the MCQ by `currentQuiz.quizId` |
| Deleting a quiz removes its questions and attempts | `AdminController::deleteQuiz()` deletes explicitly, and the foreign keys also cascade |
