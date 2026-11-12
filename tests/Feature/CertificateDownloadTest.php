<?php

use App\Models\Category;
use App\Models\Mcq;
use App\Models\MCQ_Record;
use App\Models\Quiz;
use App\Models\Record;
use App\Models\User;

/**
 * Create a finished attempt that is eligible for a certificate.
 */
function certificateAttempt(User $user, float $correctRatio = 1.0): Record
{
    $quiz = Quiz::factory()->create(['category_id' => Category::factory()->create()->id]);
    $mcq = Mcq::factory()->create(['quiz_id' => $quiz->id, 'category_id' => $quiz->category_id]);

    $record = Record::create(['quiz_id' => $quiz->id, 'user_id' => $user->id, 'status' => 2]);

    $answer = new MCQ_Record;
    $answer->record_id = $record->id;
    $answer->mcq_id = $mcq->id;
    $answer->user_id = $user->id;
    $answer->is_correct = $correctRatio >= 1 ? 1 : 0;
    $answer->save();

    return $record;
}

beforeEach(function () {
    $this->user = User::factory()->create(['active' => 2]);
    $this->record = certificateAttempt($this->user);
});

it('serves the certificate as a downloadable pdf', function () {
    $response = $this->withSession(['user' => $this->user])
        ->get("/download-certificate/{$this->record->id}");

    $response->assertOk();

    expect($response->headers->get('Content-Type'))->toBe('application/pdf');
    expect($response->headers->get('Content-Disposition'))
        ->toContain('attachment')
        ->toContain('certificate-quizsphere-'.str_pad($this->record->id, 5, '0', STR_PAD_LEFT).'.pdf');
    expect($response->getContent())->toStartWith('%PDF-');
});

it('advertises the file size and byte range support so download managers can size the file', function () {
    $response = $this->withSession(['user' => $this->user])
        ->get("/download-certificate/{$this->record->id}");

    expect($response->headers->get('Content-Length'))->toBe((string) strlen($response->getContent()));
    expect($response->headers->get('Accept-Ranges'))->toBe('bytes');
});

it('serves a partial file when a range is requested', function () {
    $full = $this->withSession(['user' => $this->user])
        ->get("/download-certificate/{$this->record->id}")
        ->getContent();

    $response = $this->withSession(['user' => $this->user])
        ->get("/download-certificate/{$this->record->id}", ['Range' => 'bytes=0-99']);

    $response->assertStatus(206);

    expect($response->headers->get('Content-Range'))
        ->toBe('bytes 0-99/'.strlen($full));
    expect($response->headers->get('Content-Length'))->toBe('100');
    expect($response->getContent())->toBe(substr($full, 0, 100));
});

it('rejects a range that lies outside of the file', function () {
    $response = $this->withSession(['user' => $this->user])
        ->get("/download-certificate/{$this->record->id}", ['Range' => 'bytes=99999999-']);

    $response->assertStatus(416);

    expect($response->headers->get('Content-Range'))->toStartWith('bytes */');
    expect($response->headers->get('Content-Length'))->toBe('0');
    expect($response->getContent())->toBe('');
});

it('ignores a malformed range header and sends the whole file', function () {
    $response = $this->withSession(['user' => $this->user])
        ->get("/download-certificate/{$this->record->id}", ['Range' => 'bytes=abc']);

    $response->assertOk();

    expect($response->getContent())->toStartWith('%PDF-');
});

it('embeds the certificate fonts so the pdf matches the typography of the page', function () {
    $pdf = $this->withSession(['user' => $this->user])
        ->get("/download-certificate/{$this->record->id}")
        ->getContent();

    expect($pdf)->toContain('GreatVibes');
    expect($pdf)->toContain('PlayfairDisplay');
});

it('renders the certificate page and the pdf from the same card', function () {
    $data = [
        'quiz' => 'Demo Quiz',
        'name' => 'Demo User',
        'id' => 1,
        'correct' => 8,
        'total' => 10,
        'score' => 80,
        'date' => now(),
    ];

    $card = trim(view('partials.certificate-card', ['data' => $data, 'assetBase' => ''])->render());

    $page = str_replace(asset(''), '', view('certificate', ['data' => $data])->render());
    $pdf = view('download-certificate', ['data' => $data, 'certificateCss' => ''])->render();

    expect($page)->toContain($card);
    expect($pdf)->toContain($card);
});
