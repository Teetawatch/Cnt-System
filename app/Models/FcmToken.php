<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $fillable = [
        'token',
        'platform',
        'device_id',
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    /**
     * Register or update FCM token
     */
    public static function register(string $token, string $platform = 'android', ?string $deviceId = null): self
    {
        return self::updateOrCreate(
            ['token' => $token],
            [
                'platform' => $platform,
                'device_id' => $deviceId,
                'last_used_at' => now(),
            ]
        );
    }
}
