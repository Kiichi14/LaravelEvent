<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    ];

    public function locations() : BelongsToMany
    {
        return $this->belongsToMany(Location::class)->withPivot('theater', 'place_number', 'date');
    }

    public function comments() : HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
