<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LineNotificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'notification_date',
        'send_type',
        'status',
        'message_sent',
        'error_message',
        'events_count',
        'sent_by',
    ];

    protected $casts = [
        'notification_date' => 'date',
    ];

    /**
     * Get the user who sent this notification
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Get status label in Thai
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'success' => 'สำเร็จ',
            'failed' => 'ล้มเหลว',
            default => $this->status,
        };
    }

    /**
     * Get send type label in Thai
     */
    public function getSendTypeLabelAttribute(): string
    {
        return match ($this->send_type) {
            'manual' => 'ส่งเอง',
            'scheduled' => 'อัตโนมัติ',
            default => $this->send_type,
        };
    }
}
