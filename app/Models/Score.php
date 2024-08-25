<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'question_id',
        'target_score',
        'achieve_score'
    ];

    public function question()
    {
        return $this->belongsTo(Questions::class);
    }
}
