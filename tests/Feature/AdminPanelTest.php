<?php

use App\Models\Admin;
use App\Models\Category;
use App\Models\Mcq;
use App\Models\Quiz;
use App\Models\Record;
use App\Models\User;

beforeEach(function () {
    $this->admin = Admin::factory()->create(['name' => 'quizmaster']);
    $this->actingAsAdmin = fn () => $this->withSession(['admin' => $this->admin]);
});

it('renders the admin dashboard', function () {
    User::factory()->count(3)->create();
    Category::factory()->count(2)->create();
    Quiz::factory()->count(2)->create();
    Mcq::factory()->count(4)->create();

    ($this->actingAsAdmin)()->get('/dashboard')->assertOk()->assertSee('quizmaster');
});

it('renders the admin categories page', function () {
    Category::factory()->create(['name' => 'Web Development']);

    ($this->actingAsAdmin)()->get('/admin-categories')->assertOk()->assertSee('Web Development');
});

it('adds a category', function () {
    ($this->actingAsAdmin)()
        ->post('/add-category', ['category_name' => 'Astronomy'])
        ->assertRedirect('/admin-categories');

    expect(Category::where('name', 'Astronomy')->exists())->toBeTrue();
    expect(Category::where('name', 'Astronomy')->first()->creator)->toBe('quizmaster');
});

it('rejects a duplicate or too short category name', function () {
    Category::factory()->create(['name' => 'Astronomy']);

    ($this->actingAsAdmin)()
        ->post('/add-category', ['category_name' => 'Astronomy'])
        ->assertSessionHasErrors('category_name');

    ($this->actingAsAdmin)()
        ->post('/add-category', ['category_name' => 'ab'])
        ->assertSessionHasErrors('category_name');
});

it('deletes a category', function () {
    $category = Category::factory()->create();

    ($this->actingAsAdmin)()->get("/category/delete/{$category->id}")->assertRedirect('/admin-categories');

    expect(Category::whereKey($category->id)->exists())->toBeFalse();
});

it('creates a quiz and adds questions to it', function () {
    $category = Category::factory()->create();

    ($this->actingAsAdmin)()
        ->post('/add-quiz', ['quiz_name' => 'Astronomy Basics', 'category_id' => $category->id])
        ->assertRedirect('/add-quiz');

    $quiz = Quiz::where('name', 'Astronomy Basics')->first();
    expect($quiz)->not->toBeNull();

    ($this->actingAsAdmin)()
        ->post('/add-mcq', [
            'question' => 'Which planet is known as the Red Planet?',
            'option_a' => 'Venus',
            'option_b' => 'Mars',
            'option_c' => 'Jupiter',
            'option_d' => 'Saturn',
            'correct_ans' => 'b',
            'submit' => 'add-more',
        ])
        ->assertRedirect('/add-quiz');

    expect(Mcq::where('quiz_id', $quiz->id)->count())->toBe(1);
    expect(Mcq::first()->category_id)->toBe($category->id);
    expect(Mcq::first()->admin_id)->toBe($this->admin->id);
});

it('validates the question form', function () {
    Category::factory()->create();
    ($this->actingAsAdmin)()->post('/add-quiz', ['quiz_name' => 'Quiz', 'category_id' => 1]);

    ($this->actingAsAdmin)()
        ->post('/add-mcq', ['question' => 'no'])
        ->assertSessionHasErrors(['question', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_ans']);
});

it('shows the quiz management screen', function () {
    $quiz = Quiz::factory()->create(['name' => 'Astronomy Basics']);
    Mcq::factory()->count(2)->create(['quiz_id' => $quiz->id]);

    ($this->actingAsAdmin)()->get("/show-quiz/{$quiz->id}/{$quiz->name}")->assertOk()->assertSee('Astronomy Basics');
});

it('lists the quizzes of a category', function () {
    $category = Category::factory()->create();
    Quiz::factory()->create(['name' => 'Astronomy Basics', 'category_id' => $category->id]);

    ($this->actingAsAdmin)()->get("/quiz-list/{$category->id}/Astronomy")->assertOk()->assertSee('Astronomy Basics');
});

it('deletes a quiz together with its questions and attempts', function () {
    $quiz = Quiz::factory()->create();
    Mcq::factory()->count(2)->create(['quiz_id' => $quiz->id]);
    $user = User::factory()->create();
    Record::create(['quiz_id' => $quiz->id, 'user_id' => $user->id, 'status' => 1]);

    ($this->actingAsAdmin)()->get("/quiz/delete/{$quiz->id}")->assertRedirect();

    expect(Quiz::whereKey($quiz->id)->exists())->toBeFalse();
    expect(Mcq::where('quiz_id', $quiz->id)->count())->toBe(0);
    expect(Record::where('quiz_id', $quiz->id)->count())->toBe(0);
});

it('deletes a single question', function () {
    $quiz = Quiz::factory()->create();
    $mcq = Mcq::factory()->create(['quiz_id' => $quiz->id]);

    ($this->actingAsAdmin)()->get("/mcq/delete/{$mcq->id}")->assertRedirect();

    expect(Mcq::whereKey($mcq->id)->exists())->toBeFalse();
});

it('keeps the admin area closed to regular users', function () {
    $user = User::factory()->create();

    $this->withSession(['user' => $user])->get('/dashboard')->assertRedirect('/login');
});

it('links every dashboard card to its management page', function () {
    ($this->actingAsAdmin)()->get('/dashboard')
        ->assertOk()
        ->assertSee('href="/admin-users"', false)
        ->assertSee('href="/admin-categories"', false)
        ->assertSee('href="/admin-quizzes"', false)
        ->assertSee('href="/admin-questions"', false)
        ->assertSee('href="/admin-attempts"', false);
});

it('renders the user management page', function () {
    User::factory()->create(['name' => 'Ada Lovelace', 'email' => 'ada@example.com']);

    ($this->actingAsAdmin)()->get('/admin-users')
        ->assertOk()
        ->assertSee('Ada Lovelace')
        ->assertSee('ada@example.com');
});

it('renders the quiz management page', function () {
    $category = Category::factory()->create(['name' => 'Astronomy']);
    Quiz::factory()->create(['name' => 'Star Signs', 'category_id' => $category->id]);

    ($this->actingAsAdmin)()->get('/admin-quizzes')
        ->assertOk()
        ->assertSee('Star Signs')
        ->assertSee('Astronomy');
});

it('renders the question management page', function () {
    $quiz = Quiz::factory()->create(['name' => 'Star Signs']);
    Mcq::factory()->create(['quiz_id' => $quiz->id, 'question' => 'How many stars are in the sky?']);

    ($this->actingAsAdmin)()->get('/admin-questions')
        ->assertOk()
        ->assertSee('How many stars are in the sky?')
        ->assertSee('Star Signs');
});

it('renders the attempt management page', function () {
    $quiz = Quiz::factory()->create(['name' => 'Star Signs']);
    $user = User::factory()->create(['name' => 'Ada Lovelace']);
    $record = Record::create(['quiz_id' => $quiz->id, 'user_id' => $user->id, 'status' => 1]);

    ($this->actingAsAdmin)()->get('/admin-attempts')
        ->assertOk()
        ->assertSee('Ada Lovelace')
        ->assertSee('Star Signs')
        ->assertSee('In Progress');
});

it('keeps the management pages closed to guests', function () {
    foreach (['/admin-users', '/admin-quizzes', '/admin-questions', '/admin-attempts'] as $route) {
        $this->get($route)->assertRedirect('/login');
    }
});
