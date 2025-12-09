<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MatchTimeline extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_match_id',
        'user_id',
        'previous_status',
        'new_status',
        'notes',
    ];

    public function match()
    {
        return $this->belongsTo(BusinessMatch::class, 'business_match_id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Domain\Models\User::class);
    }
}
