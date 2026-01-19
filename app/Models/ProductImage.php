<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * ProductImage Model - Untuk foto alternatif produk
 */
class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'image_path',
        'sort_order',
    ];

    protected $appends = ['image_url'];

    /**
     * Get product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get image URL
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return asset('images/no-image.png');
    }
}
