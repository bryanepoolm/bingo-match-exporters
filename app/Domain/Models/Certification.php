<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'issuing_authority',
        'required_for_markets',
        'icon',
        'category',
    ];

    protected $casts = [
        'required_for_markets' => 'array',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_certifications')
            ->withPivot(['certificate_number', 'issued_by', 'issued_at', 'expires_at', 'document_url', 'status'])
            ->withTimestamps();
    }
}
