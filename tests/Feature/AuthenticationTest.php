<?php

use App\Mail\VerifyUser;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

it('registers a new user but does not sign them in yet', function () {
    $this->post('/user-signup', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ])->assertRedirect('/verify-email');

    $user = User::where('email', 'jane@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->active)->toBe(User::STATUS_UNVERIFIED);
    expect(session()->has('user'))->toBeFalse();
});

it('refuses to sign in an unverified account', function () {
    User::factory()->unverified()->create(['email' => 'jane@example.com', 'password' => 'secret123']);

    $this->post('/login', [
        'account' => 'jane@example.com',
        'password' => 'secret123',
    ])->assertRedirect('/verify-email');

    expect(session()->has('user'))->toBeFalse();
});

it('verifies an account and asks the user to sign in', function () {
    $user = User::factory()->unverified()->create(['email' => 'jane@example.com']);

    $this->get('/verify-user/'.Crypt::encryptString($user->email))
        ->assertRedirect('/login');

    expect($user->fresh()->active)->toBe(User::STATUS_VERIFIED);
    expect(session()->has('user'))->toBeFalse();
});

it('lets a verified user sign in right after verifying', function () {
    $user = User::factory()->unverified()->create(['email' => 'jane@example.com']);

    $this->get('/verify-user/'.Crypt::encryptString($user->email));

    $this->post('/login', [
        'account' => 'jane@example.com',
        'password' => 'password',
    ])->assertRedirect('/');

    expect(session('user')->id)->toBe($user->id);
});

it('sends the user to the quiz they wanted once they sign in after verifying', function () {
    $user = User::factory()->unverified()->create(['email' => 'jane@example.com']);

    $this->withSession(['quiz_url' => 'http://localhost/start-quiz/1/HTML-Basics'])
        ->get('/verify-user/'.Crypt::encryptString($user->email))
        ->assertRedirect('/login');

    $this->post('/login', [
        'account' => 'jane@example.com',
        'password' => 'password',
    ])->assertRedirect('http://localhost/start-quiz/1/HTML-Basics');
});

it('rejects an invalid verification link', function () {
    $this->get('/verify-user/not-a-valid-token')
        ->assertRedirect('/login')
        ->assertSessionHas('message-error');
});

it('re-sends the verification email on request', function () {
    Mail::fake();

    $user = User::factory()->unverified()->create(['email' => 'jane@example.com']);

    $this->post('/resend-verification', ['email' => 'jane@example.com'])
        ->assertRedirect('/verify-email')
        ->assertSessionHas('message-success');

    Mail::assertSent(VerifyUser::class);
});

it('will not re-send a verification email for an unknown address', function () {
    $this->post('/resend-verification', ['email' => 'nobody@example.com'])
        ->assertSessionHas('message-error');
});

it('tells an already verified user to sign in instead', function () {
    User::factory()->create(['email' => 'jane@example.com']);

    $this->post('/resend-verification', ['email' => 'jane@example.com'])
        ->assertRedirect('/login')
        ->assertSessionHas('message-success');
});

it('blocks an unverified session from the quiz pages', function () {
    $user = User::factory()->unverified()->create();

    $this->withSession(['user' => $user])
        ->get('/user-details')
        ->assertRedirect('/verify-email');

    expect(session()->has('user'))->toBeFalse();
});

it('rejects a signup with mismatched password confirmation', function () {
    $this->post('/user-signup', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'different',
    ])->assertSessionHasErrors('password');

    expect(User::count())->toBe(0);
});

it('rejects a signup with a duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->post('/user-signup', [
        'name' => 'Jane Doe',
        'email' => 'taken@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ])->assertSessionHasErrors('email');
});

it('logs a user in with the right credentials', function () {
    $user = User::factory()->create(['email' => 'jane@example.com', 'password' => 'secret123']);

    $this->post('/login', [
        'account' => 'jane@example.com',
        'password' => 'secret123',
    ])->assertRedirect('/');

    expect(session('user')->id)->toBe($user->id);
});

it('logs an admin in with the right credentials', function () {
    $admin = Admin::factory()->create(['name' => 'quizmaster', 'password' => 'secret123']);

    $this->post('/login', [
        'account' => 'quizmaster',
        'password' => 'secret123',
    ])->assertRedirect('/dashboard');

    expect(session('admin')->id)->toBe($admin->id);
});

it('rejects invalid credentials', function () {
    User::factory()->create(['email' => 'jane@example.com', 'password' => 'secret123']);

    $this->post('/login', [
        'account' => 'jane@example.com',
        'password' => 'wrong-password',
    ])->assertRedirect('/login');

    expect(session()->has('user'))->toBeFalse();
});

it('does not accept a plaintext admin password', function () {
    Admin::factory()->create(['name' => 'quizmaster', 'password' => 'secret123']);

    $this->post('/login', [
        'account' => 'quizmaster',
        'password' => 'secret123',
    ])->assertRedirect('/dashboard');

    $this->post('/logout');

    // A stored hash must never be usable as the literal password.
    $this->post('/login', [
        'account' => 'quizmaster',
        'password' => Hash::make('secret123'),
    ])->assertRedirect('/login');
});

it('logs out and clears both sessions', function () {
    $this->post('/login', [
        'account' => 'admin',
        'password' => 'admin123',
    ]);

    $this->get('/logout')->assertRedirect('/login');

    expect(session()->has('user'))->toBeFalse();
    expect(session()->has('admin'))->toBeFalse();
});

it('redirects an authenticated user away from the login page', function () {
    $user = User::factory()->create();
    $this->withSession(['user' => $user])->get('/login')->assertRedirect('/');
});

it('lets a user reset a forgotten password', function () {
    $user = User::factory()->create(['email' => 'jane@example.com']);

    $this->post('/user-set-forgot-password', [
        'email' => $user->email,
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertRedirect('/login');

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
});

it('keeps the typed values and shows the error on the failing signup field', function () {
    $this->post('/user-signup', [
        'name' => 'Ja',
        'email' => 'jane@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'secret123',
    ])->assertSessionHasErrors('name');

    $html = $this->get('/user-signup')->getContent();

    // The name failed validation, so the email the user typed stays put.
    expect($html)->toContain('value="jane@example.com"');
    expect($html)->toContain('The name field must be at least 3 characters.');
});

it('shows a password confirmation mismatch on the confirmation field', function () {
    $response = $this->post('/user-signup', [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'secret123',
        'password_confirmation' => 'different',
    ]);

    $response->assertSessionHasErrors('password');
    $response->assertSessionHasErrors('password_confirmation');
});

it('keeps the account name when the login fails', function () {
    User::factory()->create(['email' => 'jane@example.com', 'password' => 'secret123']);

    $response = $this->post('/login', [
        'account' => 'jane@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect('/login');
    $response->assertSessionHasInput('account', 'jane@example.com');
});

it('shows the login validation error underneath the field', function () {
    $this->post('/login', [
        'account' => '',
        'password' => '',
    ])->assertSessionHasErrors(['account', 'password']);

    $html = $this->get('/login')->getContent();

    expect($html)->toContain('The account field is required.');
    expect($html)->toContain('The password field is required.');
});
