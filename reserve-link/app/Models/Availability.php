<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    /** @use HasFactory<\Database\Factories\AvailabilityFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'availabilities';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
