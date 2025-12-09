<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'product_categories',
        'production_volume',
    ];

    protected $casts = [
        'product_categories' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
