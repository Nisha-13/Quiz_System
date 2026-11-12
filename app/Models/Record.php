<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    protected $fillable = ['quiz_id', 'user_id', 'status'];

    /**
     * Join the related quiz so attempt lists can show the quiz name.
     *
     * Columns are selected explicitly to avoid the duplicate `id`,
     * `created_at` and `updated_at` columns a `*` join would produce.
     */
    public function scopeWithQuiz($query)
    {
        return $query->join('quizzes', 'records.quiz_id', '=', 'quizzes.id')
            ->select([
                'records.id',
                'records.quiz_id',
                'records.user_id',
                'records.status',
                'records.created_at',
                'records.updated_at',
                'quizzes.name as quiz_name',
                'quizzes.category_id',
            ]);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The per-question answers that make up this attempt.
     */
    public function answers()
    {
        return $this->hasMany(MCQ_Record::class);
    }
}
