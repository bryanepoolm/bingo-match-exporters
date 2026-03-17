<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exporter_id',
        'company_id',
        'name',
        'description',
        'price',
        'currency',
        'weight_limit',
        'destinations',
        'status',
    ];

    protected $casts = [
        'destinations' => 'array',
        'price' => 'decimal:2',
    ];

    public function exporter(): BelongsTo
    {
        return $this->belongsTo(Exporter::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
