<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Product Model
 */
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'admin_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'verified_price',
        'stock',
        'main_image',
        'is_active',
        'is_featured',
        'views',
        'sold_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'verified_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected $appends = ['main_image_url', 'discount_percentage'];

    /**
     * Get category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get admin owner
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get additional images
     */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * Get orders for this product
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get main image URL
     */
    public function getMainImageUrlAttribute(): string
    {
        if ($this->main_image) {
            return asset('storage/' . $this->main_image);
        }
        return asset('images/no-image.png');
    }

    /**
     * Get discount percentage
     */
    public function getDiscountPercentageAttribute(): int
    {
        if ($this->price > 0) {
            return round((($this->price - $this->verified_price) / $this->price) * 100);
        }
        return 0;
    }

    /**
     * Get price for user
     */
    public function getPriceForUser($user = null): float
    {
        if ($user && $user->isVerified()) {
            return $this->verified_price;
        }
        return $this->price;
    }

    /**
     * Check if product is in stock
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    /**
     * Scope for active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for featured products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for in stock products
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('short_description', 'like', "%{$search}%");
        });
    }
}
