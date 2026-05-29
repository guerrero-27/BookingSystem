<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'room_id', 'check_in', 'check_out',
        'guests', 'total_price', 'status',
        'booking_reference', 'special_requests'
    ];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking){
            $booking->booking_reference = 'HB-' . strtoupper(uniqid());
        });
    }

    public function room()
    {
        reuturn $this->belongTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function getNightsAttribute()
    {
        return $this->check_in->diffInDays($this->check_out);
    }
}
