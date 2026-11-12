<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MCQ_Record extends Model
{
    protected $table = 'mcq_records';

    public function mcq()
    {
        return $this->belongsTo(Mcq::class, 'mcq_id');
    }

    /**
     * Eager load the related question for answer reviews.
     */
    public function scopeWithMCQ($query)
    {
        return $query->with('mcq');
    }
}
