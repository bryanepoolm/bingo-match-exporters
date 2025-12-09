<?php

namespace App\Domain\Models;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Database\Factories\CompanyFactory;

class Company extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return CompanyFactory::new();
    }

    protected $fillable = [
        'user_id',
        'name',
        'tax_id',
        'website',
        'description',
        'logo_path',
        'is_verified',
        'verification_documents',
        'type',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verification_documents' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function exporter(): HasOne
    {
        return $this->hasOne(Exporter::class);
    }

    public function producer(): HasOne
    {
        return $this->hasOne(Producer::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
