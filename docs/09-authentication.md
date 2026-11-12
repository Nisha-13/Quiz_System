# 9 — Authentication & authorisation

## 9.1 The model

QuizSphere does **not** use Laravel's authentication guard. Instead it stores
the signed-in account directly in the session:

```php
Session::put('user', $user);   // App\Models\User
Session::put('admin', $admin); // App\Models\Admin
```

and reads it back with `Session::get('user')` / `Session('admin')`.

Two middleware classes implement the access control:

| Middleware | Checks | On failure |
|---|---|---|
| `App\Http\Middleware\CheckUserAuth` | A **verified** user is in the session | Redirects to `/login`, or to `/verify-email` when the account exists but is unverified |
| `App\Http\Middleware\CheckAdminAuth` | An admin is in the session | Redirects to `/login` |

Both are registered as middleware groups in `bootstrap/app.php`:

```php
$middleware->appendToGroup('CheckUserAuth', [CheckUserAuth::class]);
$middleware->appendToGroup('CheckAdminAuth', [CheckAdminAuth::class]);
```

and applied in `routes/web.php`:

```php
Route::middleware('CheckUserAuth')->group(function () { … });
Route::middleware('CheckAdminAuth')->group(function () { … });
```

### Why the session and not `Auth::login()`?

The application predates the guard-based approach and the session model is
used consistently everywhere — the Blade templates read `Session('user')` to
render the avatar, the middleware reads the same key, and the tests set the
same key with `withSession(['user' => $user])`. Switching to `Auth::login()`
would touch every view and every test.

`User` still extends `Illuminate\Foundation\Auth\User`, so password hashing,
`remember_token` and the `Notifiable` trait work exactly as usual.

## 9.2 Sign-in

`UserController@login()` handles both account types with one form field named
`account`:

```php
$validate = $request->validate([
    'account'   => 'required',
    'password'  => 'required',
]);

// 1) Admin account
$admin = Admin::where('name', $request->account)->first();
if ($admin && Hash::check($request->password, $admin->password)) {
    Session::put('admin', $admin);
    return redirect('dashboard')->with('success', 'Welcome back, '.$admin->name.'!');
}

// 2) Regular user account
$user = User::where('email', $request->account)->first();
if ($user && Hash::check($request->password, $user->password)) {
    if (! $user->isVerified()) {
        Session::put('pending_verification_email', $user->email);
        return redirect('verify-email')->with('email', $user->email)
            ->with('message-error', 'Please verify your email address before signing in. …');
    }

    Session::put('user', $user);
    Session::forget('pending_verification_email');

    if (Session::has('quiz_url')) {
        $url = Session::get('quiz_url');
        Session::forget('quiz_url');
        return redirect($url)->with('message-success', 'Welcome back, '.$user->name.'!');
    }

    return redirect('/')->with('message-success', 'Welcome back, '.$user->name.'!');
}

return redirect('login')
    ->with('message-error', 'Invalid credentials, please check your email/username and password.')
    ->withInput($request->only('account'));
```

Notes:

* The **admin is checked first**, so an administrator cannot be shadowed by a
  user with the same identifier.
* A failed attempt re-fills the `account` field but never the password.
* `Hash::check()` is used, so a stored hash can never be used as the literal
  password (there is a test for exactly this).

## 9.3 Registration and e-mail verification

```
POST /user-signup
   │
   ├─ validate: name ≥ 3, e-mail unique + valid, password ≥ 3 + confirmed
   ├─ create the user with active = User::STATUS_UNVERIFIED (1)
   ├─ Session::put('pending_verification_email', $user->email)
   ├─ mail the verification link
   └─ redirect to /verify-email
```

The verification link is built from the encrypted e-mail address:

```php
$link = url('/verify-user/'.Crypt::encryptString($user->email));
```

`Crypt::encryptString()` is authenticated encryption (AES-256-CBC with an HMAC
in Laravel 12), so a tampered link fails to decrypt and the user is sent back
to `/login` with *"Invalid verification link."*.

Opening the link:

```php
$user->active = User::STATUS_VERIFIED;   // 2
$user->email_verified_at = now();
$user->save();
Session::forget('pending_verification_email');
return redirect('login')->with('message-success', 'Your account has been verified. Please sign in.');
```

The link only proves the address — the user still signs in with their
password.

### Resending

`POST /resend-verification` with an e-mail address:

| Situation | Result |
|---|---|
| Unknown address | *"We could not find an account with that email address."* (back) |
| Already verified | Redirect to `/login` — *"That account is already verified."* |
| Unverified | A new link is sent and the user is returned to `/verify-email` |

### Mail failures

`sendVerificationEmail()` wraps the send in a `try / catch (\Throwable)`,
reports the exception with `report($e)` and returns `false`. The caller then
shows *"Account created, but we could not send the verification email. Please
request a new link below."* — a broken SMTP server never blocks the sign-up.

## 9.4 The stale-session guard

`CheckUserAuth` re-reads the user row from the database on every request:

```php
$sessionUser = Session::get('user');

if (! $sessionUser) {
    return redirect('login')->with('message-error', 'Please login to continue.');
}

// Re-read the account so a stale session copy cannot be used to skip
// the email verification step.
$user = User::find($sessionUser->id);

if (! $user) {
    Session::forget('user');
    return redirect('login')->with('message-error', 'Please login to continue.');
}

if (! $user->isVerified()) {
    Session::forget('user');
    Session::put('pending_verification_email', $user->email);
    return redirect('verify-email')
        ->with('email', $user->email)
        ->with('message-error', 'Please verify your email address before continuing.');
}

Session::put('user', $user);

return $next($request);
```

Two consequences:

* If the account is deleted while the session is alive, the next request logs
  the user out.
* If the account's verification state changes, the change takes effect
  immediately — a stale session copy cannot be used to skip the verification
  step.

`CheckAdminAuth` is simpler — it only checks that the session contains an
admin:

```php
if (! Session('admin')) {
    return redirect('login')->with('message-error', 'Please login with an admin account to access this area.');
}
```

The admin controllers additionally re-check `Session::get('admin')` in most of
their actions and redirect to `/login` when it is missing.

## 9.5 Sign-out

`UserController@logoutSession()` clears **both** session keys:

```php
Session::forget('user');
Session::forget('admin');

return redirect('login')->with('message-success', 'Logged out successfully.');
```

`/logout`, `/user-logout` and `/admin-logout` all point at it.

`UserController@userLogout()` is a second, older implementation that only
clears the `user` key. **It is not referenced by any route** — see
[Maintenance notes](./12-maintenance-notes.md).

## 9.6 Password storage

| Concern | Implementation |
|---|---|
| Hashing algorithm | bcrypt, `BCRYPT_ROUNDS=12` |
| Where it is applied | The `hashed` cast on both `User` and `Admin`, plus explicit `Hash::make()` calls in `userSignup()` and `userSetForgotPassword()` |
| Verification | `Hash::check($plain, $hash)` in `login()` |
| Reset | `userSetForgotPassword()` re-hashes and saves |
| The `password_reset_tokens` table | Created by the migration but **not used** — the reset flow uses its own encrypted-link mechanism |

The `hashed` cast leaves an already-hashed value untouched, so the explicit
`Hash::make()` calls are safe.

## 9.7 Password reset flow

```
GET  /forgot-password
     → the form asks for an e-mail address

POST /user-forgot-password
     → validates the address
     → builds /user-forgot-password/{Crypt::encryptString($email)}
     → mails it (App\Mail\UserForgotPassword)
     → redirect '/' with 'Please check your email to set a new password.'

GET  /user-forgot-password/{email}
     → decrypts the address
     → renders the "set a new password" form

POST /user-set-forgot-password
     → validates password + confirmation
     → finds the user by e-mail and saves Hash::make(…) as the new password
     → redirect '/login'
```

The reset form is protected only by the encrypted address in the URL. There is
no token table and no expiry.

## 9.8 Deferred intent after sign-in

When a user hits a guarded page while signed out, the middleware sends them to
`/login` with a generic message. To return them to where they were going, two
routes remember the previous URL:

```php
Session::put('quiz_url', url()->previous());
```

* `GET /user-login-quiz` → `loginQuiz()` — stores the previous URL and redirects
  to `/login`.
* `GET /user-signup-quiz` → `userSignupQuiz()` — stores the previous URL and
  renders the sign-up form.

After a successful sign-in, `login()` consumes `quiz_url` and redirects there.

## 9.9 Authorisation summary

| Area | Who may access | Enforced by |
|---|---|---|
| Public catalogue, search, sign-up, sign-in | Anyone | — |
| `/mcq/…`, `/submit-next/…`, `/resume-quiz/…`, `/user-details`, `/certificate/…`, `/download-certificate/…` | A verified user | `CheckUserAuth` middleware |
| One specific attempt | Its owner | `resumeQuiz()` and `loadCertificateRecord()` compare `$record->user_id` |
| `/dashboard` and every other admin route | An admin | `CheckAdminAuth` middleware + a per-action re-check |

There are no Laravel policies, gates or abilities — authorisation is entirely
middleware plus explicit ownership checks.
