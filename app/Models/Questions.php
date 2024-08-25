<?php

namespace App\Models;

use App\Models\SubCriteria;
use App\Models\QuestionInputs;
use App\Models\QuestionChoices;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Questions extends Model
{
    use HasFactory;

    protected $fillable = [
        'sub_criteria_id',
        'code',
        'type',
        'main_question'
    ];

    public function subCriteria()
    {
        return $this->belongsTo(SubCriteria::class);
    }

    public function inputs()
    {
        return $this->hasMany(QuestionInputs::class, 'question_id');
    }

    public function choices()
    {
        return $this->hasMany(QuestionChoices::class, 'question_id');
    }

    public function weights()
    {
        return $this->hasOne(Weight::class, 'question_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'question_id');
    }

    public function targets()
    {
        return $this->hasMany(Target::class, 'question_id');
    }

    public function ahieves()
    {
        return $this->hasMany(Achievement::class, 'question_id');
    }
}
