<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Url extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'original_url',
        'short_code',
        'is_active',
    ];

    protected $appends = ['shortened_url'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function shortenedUrl(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => route('url.access', $attributes['short_code'])
        );
    }
}
