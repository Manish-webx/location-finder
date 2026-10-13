<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class LocationLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'title',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    public function visits(): HasMany
    {
        return $this->hasMany(LocationVisit::class)->orderBy('created_at', 'desc');
    }

    public function latestVisit(): HasOne
    {
        return $this->hasOne(LocationVisit::class)->latestOfMany();
    }

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && Carbon::now()->isAfter($this->expires_at)) {
            return false;
        }

        return true;
    }

    /**
     * Generate a unique, secure alphanumeric token containing both letters and digits.
     */
    public static function generateUniqueToken(int $length = 10): string
    {
        $letters = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $numbers = '23456789';
        $pool = $letters . $numbers;

        do {
            $tokenArr = [];
            // Ensure at least 2 uppercase letters and 2 digits for a guaranteed alphanumeric combination
            $tokenArr[] = $letters[random_int(0, strlen($letters) - 1)];
            $tokenArr[] = $letters[random_int(0, strlen($letters) - 1)];
            $tokenArr[] = $numbers[random_int(0, strlen($numbers) - 1)];
            $tokenArr[] = $numbers[random_int(0, strlen($numbers) - 1)];

            for ($i = 4; $i < $length; $i++) {
                $tokenArr[] = $pool[random_int(0, strlen($pool) - 1)];
            }

            shuffle($tokenArr);
            $token = implode('', $tokenArr);
        } while (self::where('token', $token)->exists());

        return $token;
    }
}
