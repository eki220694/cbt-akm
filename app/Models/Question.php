<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    // Menentukan kolom yang boleh diisi
    protected $fillable = [
        'stimulus', 
        'content', 
        'type', 
        'options', 
        'answer_key', 
        'points'
    ];

    // Penting untuk AKM: Mengubah JSON di database menjadi Array PHP otomatis
    protected $casts = [
        'options' => 'array',
        'answer_key' => 'array',
    ];
}