<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TPKQuestion extends Model
{
    //
    protected $table = 'tpk_questions';
     protected $fillable = [
        'question_text',
        'question_image',
        'difficulty',
        'options',
        'is_correct',
    ];

    protected $casts = [
        'options' => 'array', // Mengubah JSON ke array saat diakses
    ];
}
