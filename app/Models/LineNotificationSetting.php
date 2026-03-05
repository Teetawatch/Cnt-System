<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineNotificationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel_access_token',
        'is_enabled',
        'send_mode',
        'destination_id',
        'destination_name',
        'schedule_enabled',
        'schedule_time',
        'message_template',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'schedule_enabled' => 'boolean',
        'schedule_time' => 'datetime:H:i',
    ];

    /**
     * Get or create the singleton settings instance
     */
    public static function instance(): self
    {
        $setting = self::first();

        if (!$setting) {
            $setting = self::create([
                'is_enabled' => false,
                'send_mode' => 'broadcast',
                'schedule_enabled' => false,
                'schedule_time' => '07:00',
                'message_template' => self::defaultTemplate(),
            ]);
        }

        return $setting;
    }

    /**
     * Default message template
     */
    public static function defaultTemplate(): string
    {
        return "📅 ตารางปฏิบัติงาน\n📆 วันที่ {date}\n━━━━━━━━━━━━━━━\n{events}\n━━━━━━━━━━━━━━━\n📊 รวมทั้งหมด {total} รายการ";
    }
}
