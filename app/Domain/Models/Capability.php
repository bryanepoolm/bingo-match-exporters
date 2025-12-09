<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Capability extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
    ];

    public function exporters(): BelongsToMany
    {
        return $this->belongsToMany(Exporter::class);
    }
}
