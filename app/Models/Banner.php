<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Banner Model - Untuk slideshow/hero section
 */
class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'image',
        'link',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    protected $appends = ['image_url'];

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-banner.jpg');
    }

    /**
     * Scope for active banners
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered banners
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
