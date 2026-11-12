<?php

use App\Models\Category;
use App\Models\Mcq;
use App\Models\MCQ_Record;
use App\Models\Quiz;
use App\Models\Record;
use App\Models\User;

/**
 * Build a quiz with the given questions.
 *
 * @param  array<string, string>  $questions  question => correct answer
 */
function makeQuiz(array $questions, ?Category $category = null): Quiz
{
    $category ??= Category::factory()->create();
    $quiz = Quiz::factory()->create(['category_id' => $category->id]);

    foreach ($questions as $question => $correct) {
        Mcq::factory()->create([
            'quiz_id' => $quiz->id,
            'category_id' => $category->id,
            'question' => $question,
            'correct_ans' => $correct,
        ]);
    }

    return $quiz->fresh();
}

/**
 * Answer every question of a quiz through the real HTTP endpoints.
 */
function answerQuiz(User $user, Quiz $quiz, array $answers): void
{
    $this->withSession(['user' => $user]);

    foreach ($answers as $mcqId => $option) {
        $this->post("/submit-next/{$mcqId}", ['option' => $option])->assertOk();
    }
}

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('opens an attempt when the first question is loaded', function () {
    $quiz = makeQuiz(['Question one?' => 'a', 'Question two?' => 'b']);

    $this->withSession(['user' => $this->user])
        ->get("/mcq/{$quiz->mcqs()->first()->id}/{$quiz->name}")
        ->assertOk();

    expect(Record::count())->toBe(1);
    expect(Record::first()->status)->toBe(1);
    expect(Record::first()->user_id)->toBe($this->user->id);
});

it('does not create a duplicate attempt when the question page is reloaded', function () {
    $quiz = makeQuiz(['Question one?' => 'a', 'Question two?' => 'b']);
    $mcq = $quiz->mcqs()->first();

    $this->withSession(['user' => $this->user]);
    $this->get("/mcq/{$mcq->id}/{$quiz->name}")->assertOk();
    $this->get("/mcq/{$mcq->id}/{$quiz->name}")->assertOk();
    $this->get("/mcq/{$mcq->id}/{$quiz->name}")->assertOk();

    expect(Record::count())->toBe(1);
});

it('does not create a duplicate attempt when the start screen is revisited', function () {
    $quiz = makeQuiz(['Question one?' => 'a']);

    $this->withSession(['user' => $this->user]);
    $this->get("/start-quiz/{$quiz->id}/{$quiz->name}")->assertOk();
    $this->get("/mcq/{$quiz->mcqs()->first()->id}/{$quiz->name}")->assertOk();
    $this->get("/start-quiz/{$quiz->id}/{$quiz->name}")->assertOk();
    $this->get("/mcq/{$quiz->mcqs()->first()->id}/{$quiz->name}")->assertOk();

    expect(Record::count())->toBe(1);
});

it('records every answer and completes the attempt', function () {
    $quiz = makeQuiz([
        'Question one?' => 'a',
        'Question two?' => 'b',
        'Question three?' => 'c',
    ]);
    $mcqs = $quiz->mcqs()->orderBy('id')->get();

    $this->withSession(['user' => $this->user]);
    $this->get("/mcq/{$mcqs->first()->id}/{$quiz->name}")->assertOk();

    $this->post("/submit-next/{$mcqs[0]->id}", ['option' => 'a'])->assertOk();
    $this->post("/submit-next/{$mcqs[1]->id}", ['option' => 'b'])->assertOk();
    $response = $this->post("/submit-next/{$mcqs[2]->id}", ['option' => 'c'])->assertOk();

    $response->assertSee('Answer Review');

    expect(MCQ_Record::count())->toBe(3);
    expect(MCQ_Record::where('is_correct', 1)->count())->toBe(3);
    expect(Record::first()->status)->toBe(2);
});

it('marks wrong answers as incorrect', function () {
    $quiz = makeQuiz(['Question one?' => 'a']);
    $mcq = $quiz->mcqs()->first();

    $this->withSession(['user' => $this->user]);
    $this->get("/mcq/{$mcq->id}/{$quiz->name}")->assertOk();
    $this->post("/submit-next/{$mcq->id}", ['option' => 'd'])->assertOk();

    expect(MCQ_Record::first()->select_answer)->toBe('d');
    expect(MCQ_Record::first()->is_correct)->toBe(0);
});

it('rejects an answer that belongs to a different quiz', function () {
    $quiz = makeQuiz(['Question one?' => 'a']);
    $otherQuiz = makeQuiz(['Other question?' => 'b']);
    $otherMcq = $otherQuiz->mcqs()->first();

    $this->withSession(['user' => $this->user]);
    $this->get("/mcq/{$quiz->mcqs()->first()->id}/{$quiz->name}")->assertOk();

    $this->post("/submit-next/{$otherMcq->id}", ['option' => 'b'])
        ->assertRedirect('/')
        ->assertSessionHas('message-error');

    expect(MCQ_Record::count())->toBe(0);
});

it('rejects a submission without a valid option', function () {
    $quiz = makeQuiz(['Question one?' => 'a']);
    $mcq = $quiz->mcqs()->first();

    $this->withSession(['user' => $this->user]);
    $this->get("/mcq/{$mcq->id}/{$quiz->name}")->assertOk();

    $this->post("/submit-next/{$mcq->id}", ['option' => 'z'])->assertSessionHasErrors('option');
});

it('offers to continue when an attempt is already in progress', function () {
    $quiz = makeQuiz(['Question one?' => 'a', 'Question two?' => 'b']);
    $mcqs = $quiz->mcqs()->orderBy('id')->get();

    $this->withSession(['user' => $this->user]);
    $this->get("/mcq/{$mcqs[0]->id}/{$quiz->name}")->assertOk();
    $this->post("/submit-next/{$mcqs[0]->id}", ['option' => 'a'])->assertOk();

    $this->get("/start-quiz/{$quiz->id}/{$quiz->name}")
        ->assertOk()
        ->assertSee('Continue Quiz')
        ->assertSee('/resume-quiz/'.Record::first()->id);
});

it('offers to start when there is no attempt in progress', function () {
    $quiz = makeQuiz(['Question one?' => 'a']);

    $this->withSession(['user' => $this->user]);

    $this->get("/start-quiz/{$quiz->id}/{$quiz->name}")
        ->assertOk()
        ->assertSee('Start Quiz')
        ->assertDontSee('Continue Quiz');
});

it('resumes an in-progress attempt on the next unanswered question', function () {
    $quiz = makeQuiz([
        'Question one?' => 'a',
        'Question two?' => 'b',
        'Question three?' => 'c',
    ]);
    $mcqs = $quiz->mcqs()->orderBy('id')->get();

    $this->withSession(['user' => $this->user]);
    $this->get("/mcq/{$mcqs[0]->id}/{$quiz->name}")->assertOk();
    $this->post("/submit-next/{$mcqs[0]->id}", ['option' => 'a'])->assertOk();

    $record = Record::first();
    expect($record->status)->toBe(1);

    $this->get("/resume-quiz/{$record->id}")->assertOk()->assertSee('Question two?');
});

it('will not let a user resume somebody else\'s attempt', function () {
    $quiz = makeQuiz(['Question one?' => 'a']);
    $other = User::factory()->create();

    $this->withSession(['user' => $this->user]);
    $this->get("/mcq/{$quiz->mcqs()->first()->id}/{$quiz->name}")->assertOk();

    $record = Record::first();

    $this->withSession(['user' => $other])
        ->get("/resume-quiz/{$record->id}")
        ->assertRedirect('/user-details');
});

it('issues a certificate for a passing attempt', function () {
    $quiz = makeQuiz([
        'Question one?' => 'a',
        'Question two?' => 'b',
        'Question three?' => 'c',
        'Question four?' => 'd',
    ]);
    $mcqs = $quiz->mcqs()->orderBy('id')->get();

    $this->withSession(['user' => $this->user]);
    $this->get("/mcq/{$mcqs[0]->id}/{$quiz->name}")->assertOk();
    foreach ($mcqs as $mcq) {
        $this->post("/submit-next/{$mcq->id}", ['option' => $mcq->correct_ans])->assertOk();
    }

    $record = Record::first();

    $this->get("/certificate/{$record->id}")->assertOk()->assertSee($this->user->name);
    $this->get("/download-certificate/{$record->id}")->assertOk();
});

it('withholds a certificate for a failing attempt', function () {
    $quiz = makeQuiz([
        'Question one?' => 'a',
        'Question two?' => 'b',
        'Question three?' => 'c',
    ]);
    $mcqs = $quiz->mcqs()->orderBy('id')->get();

    $this->withSession(['user' => $this->user]);
    $this->get("/mcq/{$mcqs[0]->id}/{$quiz->name}")->assertOk();
    foreach ($mcqs as $mcq) {
        $wrong = $mcq->correct_ans === 'a' ? 'b' : 'a';
        $this->post("/submit-next/{$mcq->id}", ['option' => $wrong])->assertOk();
    }

    $record = Record::first();
    expect(MCQ_Record::where('is_correct', 1)->count())->toBe(0);

    $this->get("/certificate/{$record->id}")
        ->assertRedirect('/user-details')
        ->assertSessionHas('message-error');

    $this->get("/download-certificate/{$record->id}")
        ->assertRedirect('/user-details');
});

it('lists attempts with their score on the dashboard', function () {
    $quiz = makeQuiz(['Question one?' => 'a']);
    $mcq = $quiz->mcqs()->first();

    $this->withSession(['user' => $this->user]);
    $this->get("/mcq/{$mcq->id}/{$quiz->name}")->assertOk();
    $this->post("/submit-next/{$mcq->id}", ['option' => 'a'])->assertOk();

    $this->get('/user-details')
        ->assertOk()
        ->assertSee($quiz->name)
        ->assertSee('1/1');
});
