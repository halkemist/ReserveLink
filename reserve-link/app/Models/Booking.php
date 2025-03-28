<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'guest_email',
        'start_time',
        'end_time',
        'status',
        'meet_link',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Scope to check if a slot is already booked
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $ownerId ID of the calendar owner
     * @param string $startTime Start hour time of the slot
     * @param string $endTime End hour time of the slot
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOverlappingSlot($query, $ownerId, $startTime, $endTime)
    {
        return $query->where('owner_id', $ownerId)
            ->where('status', '!=', 'canceled')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->where(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                })->orWhere(function ($q2) use ($startTime, $endTime) {
                    $q2->where('start_time', '=', $startTime)
                        ->where('end_time', '=', $endTime);
                });
            });
    }
}
