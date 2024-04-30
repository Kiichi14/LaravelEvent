<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'city',
        'country'
    ];

    // public function events() : HasMany
    // {
    //     return $this->hasMany(Event::class, 'id', 'location_id');
    // }

    public function events() : BelongsToMany
    {
        return $this->belongsToMany(Event::class);
    }
}
