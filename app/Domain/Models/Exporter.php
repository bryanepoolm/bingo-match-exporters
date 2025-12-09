<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Database\Factories\ExporterFactory;

class Exporter extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return ExporterFactory::new();
    }

    protected $fillable = [
        'company_id',
        'coverage_area',
        'years_experience',
    ];

    protected $casts = [
        'coverage_area' => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function capabilities(): BelongsToMany
    {
        return $this->belongsToMany(Capability::class);
    }
}
