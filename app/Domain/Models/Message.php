<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_match_id',
        'sender_id',
        'content',
        'attachment_path',
        'type',
        'is_read',
    ];

    public function match()
    {
        return $this->belongsTo(BusinessMatch::class, 'business_match_id');
    }

    public function sender()
    {
        return $this->belongsTo(\App\Domain\Models\User::class, 'sender_id');
    }
}
