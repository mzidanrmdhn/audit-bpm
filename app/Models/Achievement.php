<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'question_id',
        'achievement_answer'
    ];

    public function question()
    {
        return $this->belongsTo(Questions::class);
    }
}
