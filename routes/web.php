<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [UserController::class, 'welcome']);
Route::get('categories', [UserController::class, 'categories']);
Route::get('user-quiz-list/{id}/{category}', [UserController::class, 'userQuizList'])->where('id', '[0-9]+');
Route::get('start-quiz/{id}/{name}', [UserController::class, 'startQuiz'])->where('id', '[0-9]+');
Route::get('user-signup-quiz', [UserController::class, 'userSignupQuiz']);
Route::post('user-signup', [UserController::class, 'userSignup']);

// ---- Unified authentication (users + admins) ----
Route::get('login', [UserController::class, 'loginView']);
Route::post('login', [UserController::class, 'login']);
Route::get('user-login', function () {
    return redirect('/login');
});
Route::get('admin-login', function () {
    return redirect('/login');
});
Route::get('user-login-quiz', [UserController::class, 'loginQuiz']);
Route::get('logout', [UserController::class, 'logoutSession']);
Route::get('user-logout', [UserController::class, 'logoutSession']);
Route::get('admin-logout', [UserController::class, 'logoutSession']);
// --------------------------------------------------

Route::get('search-quiz', [UserController::class, 'searchQuiz']);
Route::get('verify-user/{email}', [UserController::class, 'verifyUser']);
Route::get('verify-email', [UserController::class, 'verifyEmailView']);
Route::post('resend-verification', [UserController::class, 'resendVerificationEmail']);
Route::view('forgot-password', 'forgot-password');
Route::post('user-forgot-password', [UserController::class, 'userForgotPassword']);
Route::get('user-forgot-password/{email}', [UserController::class, 'userResetForgotPassword']);
Route::post('user-set-forgot-password', [UserController::class, 'userSetForgotPassword']);

Route::get('user-signup', function () {
    if (! Session()->has('user')) {
        return view('user-signup');
    } else {
        return redirect('/');
    }
});

Route::middleware('CheckUserAuth')->group(function () {
    Route::get('mcq/{id}/{name}', [UserController::class, 'mcq'])->where('id', '[0-9]+');
    Route::post('submit-next/{id}', [UserController::class, 'submitAndNext'])->where('id', '[0-9]+');
    Route::get('user-details', [UserController::class, 'userDetails']);
    Route::get('resume-quiz/{id}', [UserController::class, 'resumeQuiz'])->where('id', '[0-9]+');
    Route::get('certificate/{id?}', [UserController::class, 'certificate'])->where('id', '[0-9]+');
    // The trailing ".pdf" segment is optional and only there so browsers and
    // download managers can detect the file type from the URL itself.
    Route::get('download-certificate/{id?}/{file?}', [UserController::class, 'downloadCertificate'])->where('id', '[0-9]+');
});

Route::middleware('CheckAdminAuth')->group(function () {
    Route::get('dashboard', [AdminController::class, 'dashboard']);
    Route::get('admin-categories', [AdminController::class, 'categories']);
    Route::get('admin-users', [AdminController::class, 'users']);
    Route::get('admin-quizzes', [AdminController::class, 'quizzes']);
    Route::get('admin-questions', [AdminController::class, 'questions']);
    Route::get('admin-attempts', [AdminController::class, 'attempts']);

    Route::post('add-category', [AdminController::class, 'addCategory']);

    // Quiz routes
    Route::get('add-quiz', [AdminController::class, 'showAddQuizForm']); // GET → show form
    Route::post('add-quiz', [AdminController::class, 'addQuiz']);        // POST → submit form
    Route::post('add-mcq', [AdminController::class, 'addMCQS']);
    Route::get('end-quiz', [AdminController::class, 'endQuiz']);
    Route::get('show-quiz/{id}/{quizName}', [AdminController::class, 'showQuiz'])->where('id', '[0-9]+');
    Route::get('quiz-list/{id}/{category}', [AdminController::class, 'quizList'])->where('id', '[0-9]+');

    // Delete management — registered with literal prefixes so they can never
    // be shadowed by the parameterised routes above.
    Route::get('category/delete/{id}', [AdminController::class, 'deleteCategory'])->where('id', '[0-9]+');
    Route::get('quiz/delete/{id}', [AdminController::class, 'deleteQuiz'])->where('id', '[0-9]+');
    Route::get('mcq/delete/{id}', [AdminController::class, 'deleteMCQ'])->where('id', '[0-9]+');
});
