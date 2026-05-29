<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'type',
        'price_per_night', 'capacity', 'beds',
        'bathrooms', 'is_available', 'is_featured'
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'is_featured' => 'boolean',
        'price_per_night' => 'decimal:2',
    ];

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class);
    }

    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function isAvailableFor($checkIn, $checkOut): bool
    {
        return !this->bookings()->whereIn('status', ['pending', 'confirmed'])
            ->where(function ($q) use ($checkIn, $checkOut){
                $q->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orwhere(function ($q) use ($checkIn, $checkOut){
                        $q->where('check_in', '<=', $checkIn)
                        ->where('check_out', '>=', $checkOut);
                    });
            })->exists();
    }

    public function getAverageRatingAttribute()
    {
        return $this->reviews()->avg('rating') ?? 0;
    }
}
