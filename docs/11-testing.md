# 11 — Testing

## 11.1 The suite at a glance

| | |
|---|---|
| Framework | Pest 3 (`pestphp/pest ^3.8`) + `pestphp/pest-plugin-laravel ^3.2` |
| Runner | PHPUnit under the hood (`phpunit.xml`) |
| Tests | **72** |
| Assertions | **263** |
| Duration | ≈ 10–60 s, depending on the machine |
| Database | In-memory SQLite (`DB_DATABASE=:memory:`) |
| Result | 72 passed, 0 failed |

```bash
php artisan test          # the whole suite
php artisan test --compact
vendor/bin/pest            # directly
vendor/bin/pest tests/Feature/QuizAttemptTest.php
php artisan test --filter="certificate"   # by name
```

`composer test` runs `config:clear` first and then `artisan test`.

## 11.2 Test isolation

`phpunit.xml` overrides the environment for every test run:

| Variable | Value | Why |
|---|---|---|
| `APP_ENV` | `testing` | |
| `DB_CONNECTION` | `sqlite` | |
| `DB_DATABASE` | `:memory:` | The suite never touches the development database |
| `CACHE_STORE` | `array` | No cache table is needed |
| `SESSION_DRIVER` | `array` | Sessions live in memory |
| `QUEUE_CONNECTION` | `sync` | Jobs run inline |
| `MAIL_MAILER` | `array` | Mails are captured, not sent |
| `BCRYPT_ROUNDS` | `4` | Fast hashing |
| `PULSE_ENABLED` / `TELESCOPE_ENABLED` / `NIGHTWATCH_ENABLED` | `false` | Optional tooling off |

`tests/Pest.php` binds the `Feature` suite to the application test case and
adds `RefreshDatabase`:

```php
pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');
```

So every feature test starts from a freshly migrated, empty database.

## 11.3 Test files

| File | Tests | Covers |
|---|---|---|
| `tests/Feature/PublicPagesTest.php` | 8 | Landing page, category listing, login/signup/forgot-password pages, search results, the per-category quiz list, the public start-quiz screen, and the guest redirects for the user area and the admin area |
| `tests/Feature/AuthenticationTest.php` | 23 | Sign-up, unverified sign-in refusal, verification, resending, password reset, admin and user sign-in, invalid credentials, sign-out, inline validation, old input retention |
| `tests/Feature/QuizAttemptTest.php` | 14 | Attempt creation, no duplicate attempts, answer recording, wrong answers, cross-quiz rejection, invalid options, start vs. continue, resume, ownership, certificates for passing and failing attempts, the attempt history |
| `tests/Feature/AdminPanelTest.php` | 18 | Dashboard, the five management pages, category/quiz/question creation and deletion, validation, the dashboard links, the admin area closed to guests and to regular users |
| `tests/Feature/CertificateDownloadTest.php` | 7 | The PDF content type and file name, `Content-Length`, `Accept-Ranges`, partial (206) responses, unsatisfiable ranges (416), malformed ranges, the embedded fonts, the shared card markup |
| `tests/Feature/ExampleTest.php` | 1 | The landing page returns 200 |
| `tests/Unit/ExampleTest.php` | 1 | A trivial unit test |

## 11.4 The interesting test cases

### Attempt lifecycle

```php
it('opens an attempt when the first question is loaded', …);
it('does not create a duplicate attempt when the question page is reloaded', …);
it('does not create a duplicate attempt when the start screen is revisited', …);
it('records every answer and completes the attempt', …);
it('resumes an in-progress attempt on the next unanswered question', …);
it('will not let a user resume somebody else\'s attempt', …);
```

The helper `answerQuiz()` in `QuizAttemptTest.php` drives the **real HTTP
endpoints** rather than writing to the database, so the tests exercise the
controllers, the session and the middleware together.

### Certificate download

```php
it('serves the certificate as a downloadable pdf', …);
it('advertises the file size and byte range support so download managers can size the file', …);
it('serves a partial file when a range is requested', …);
it('rejects a range that lies outside of the file', …);
it('ignores a malformed range header and sends the whole file', …);
it('embeds the certificate fonts so the pdf matches the typography of the page', …);
it('renders the certificate page and the pdf from the same card', …);
```

The last one is the guard against the page and the PDF drifting apart:

```php
$card = trim(view('partials.certificate-card', ['data' => $data, 'assetBase' => ''])->render());
$page = str_replace(asset(''), '', view('certificate', ['data' => $data])->render());
$pdf  = view('download-certificate', ['data' => $data, 'certificateCss' => ''])->render();

expect($page)->toContain($card);
expect($pdf)->toContain($card);
```

### Authentication

```php
it('registers a new user but does not sign them in yet', …);
it('refuses to sign in an unverified account', …);
it('verifies an account and asks the user to sign in', …);
it('sends the user to the quiz they wanted once they sign in after verifying', …);
it('does not accept a plaintext admin password', …);
it('keeps the typed values and shows the error on the failing signup field', …);
it('shows a password confirmation mismatch on the confirmation field', …);
```

## 11.5 Factories used by the tests

| Factory | Typical usage in a test |
|---|---|
| `User::factory()->create(['active' => 2])` | A verified user |
| `User::factory()->unverified()->create([…])` | An account awaiting verification |
| `Admin::factory()->create(['name' => 'quizmaster'])` | An administrator |
| `Category::factory()->create(['name' => 'Astronomy'])` | A category |
| `Quiz::factory()->create(['category_id' => $category->id])` | A quiz |
| `Mcq::factory()->create(['quiz_id' => $quiz->id])` | A question |

Signing in is done by writing the session key directly — this is what the
application's middleware reads:

```php
$this->withSession(['user' => $user]);
$this->withSession(['admin' => $admin]);
```

## 11.6 Adding a test

1. Put feature tests in `tests/Feature/`, unit tests in `tests/Unit/`.
2. Use the Pest syntax: `it('…', function () { … });`.
3. The closure is bound to the test case, so `$this->get(…)`, `$this->post(…)`
   and `$this->withSession(…)` are available.
4. Use the factories rather than raw inserts.
5. Run `vendor/bin/pint` afterwards.

Example:

```php
it('lets an admin delete a category', function () {
    $admin = Admin::factory()->create();
    $category = Category::factory()->create();

    $this->withSession(['admin' => $admin])
        ->get("/category/delete/{$category->id}")
        ->assertRedirect('/admin-categories');

    expect(Category::whereKey($category->id)->exists())->toBeFalse();
});
```
