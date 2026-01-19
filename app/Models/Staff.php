<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Staff extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'position',
        'department',
        'photo',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Get the photo URL attribute
     * Photos are stored in public/uploads/staff-photos for shared hosting compatibility
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if ($this->photo) {
            // Handle old photos that don't have 'uploads/' prefix
            $path = $this->photo;
            if (!str_starts_with($path, 'uploads/')) {
                $path = 'uploads/' . $path;
            }
            return asset($path);
        }
        return null;
    }

    /**
     * Get calendar events for this staff member
     */
    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    /**
     * Scope to get only active staff
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by sort_order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
