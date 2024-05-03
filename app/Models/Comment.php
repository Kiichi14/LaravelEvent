<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Comment extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'comment',
        'rating',
        'user_id',
        'event_id'
    ];

    public function user() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function event() : BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
