<?php

use App\Models\Category;
use App\Models\Mcq;
use App\Models\Quiz;

it('renders the public landing page', function () {
    $this->get('/')->assertOk();
});

it('renders the categories listing', function () {
    Category::factory()->count(3)->create();

    $this->get('/categories')->assertOk()->assertSee('Categories');
});

it('renders the login and signup pages', function () {
    $this->get('/login')->assertOk();
    $this->get('/user-signup')->assertOk();
    $this->get('/forgot-password')->assertOk();
});

it('renders the search results page', function () {
    Category::factory()->create(['name' => 'Web Development']);
    Quiz::factory()->create(['name' => 'HTML-Basics']);

    $this->get('/search-quiz?search=HTML')->assertOk()->assertSee('HTML-Basics');
});

it('shows the quiz list for a category', function () {
    $category = Category::factory()->create(['name' => 'Science']);
    Quiz::factory()->create(['name' => 'Human-Body', 'category_id' => $category->id]);
    Mcq::factory()->count(2)->create();

    $this->get("/user-quiz-list/{$category->id}/Science")->assertOk()->assertSee('Human-Body');
});

it('redirects guests away from the start-quiz screen', function () {
    $quiz = Quiz::factory()->create();
    Mcq::factory()->count(2)->create(['quiz_id' => $quiz->id]);

    $this->get("/start-quiz/{$quiz->id}/{$quiz->name}")->assertOk();
});

it('does not leak the admin area to guests', function () {
    foreach (['/dashboard', '/admin-categories', '/add-quiz'] as $route) {
        $this->get($route)->assertRedirect('/login');
    }
});

it('does not leak user pages to guests', function () {
    foreach (['/user-details', '/certificate', '/download-certificate'] as $route) {
        $this->get($route)->assertRedirect('/login');
    }
});
