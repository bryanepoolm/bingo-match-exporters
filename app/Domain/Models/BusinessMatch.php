<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessMatch extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'matches';

    protected $fillable = [
        'producer_id',
        'exporter_id',
        'score',
        'status',
        'origin',
        'destination',
        'tentative_date',
        'message',
        'products',
        'is_read',
        'rejection_reason',
    ];

    protected $casts = [
        'products' => 'array',
        'tentative_date' => 'date',
        'is_read' => 'boolean',
    ];

    public function producer(): BelongsTo
    {
        return $this->belongsTo(Producer::class);
    }

    public function exporter(): BelongsTo
    {
        return $this->belongsTo(Exporter::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'business_match_id');
    }

    public function timelines()
    {
        return $this->hasMany(MatchTimeline::class, 'business_match_id');
    }
}
