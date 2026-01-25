<?php

namespace App\Domain\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'image_path',
        'start_date',
        'end_date',
        'location_name',
        'address',
        'city',
        'state',
        'country',
        'latitude',
        'longitude',
        'website_url',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];
}
