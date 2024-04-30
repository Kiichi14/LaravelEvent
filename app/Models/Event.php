<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'name',
        'description',
        'type',
        'event_location',
        'event_date'
    ];

    public function location() : BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
}
