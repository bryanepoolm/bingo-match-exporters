<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HsCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'chapter',
        'section',
        'parent_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
