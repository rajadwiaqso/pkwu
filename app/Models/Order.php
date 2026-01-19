<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Order Model
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'product_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'address',
        'quantity',
        'price',
        'shipping_cost',
        'total_price',
        'is_verified_price',
        'status',
        'notes',
        'tracking_number',
        'payment_proof',
        'paid_at',
        'shipped_at',
        'completed_at',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'total_price' => 'decimal:2',
        'is_verified_price' => 'boolean',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    // Append derived attributes for frontend
    protected $appends = ['payment_proof_url', 'unit_price'];

    /**
     * Boot method - Generate order number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'TDR'; // Toko Digital Raja
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));
        
        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Get user (buyer)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get payment proof URL
     */
    public function getPaymentProofUrlAttribute(): ?string
    {
        if ($this->payment_proof) {
            return asset('storage/' . $this->payment_proof);
        }
        return null;
    }

    /**
     * Generate WhatsApp message
     */
    public function getWhatsAppMessage(): string
    {
        $productName = $this->product ? $this->product->name : 'Produk';
        return "Halo, saya ingin mengkonfirmasi pesanan:\n\nOrder ID: {$this->order_number}\nProduk: {$productName}\nJumlah: {$this->quantity}\nTotal: Rp " . number_format($this->total_price, 0, ',', '.');
    }

    /**
     * Get WhatsApp URL for customer
     */
    public function getWhatsAppUrl(): string
    {
        $adminPhone = config('app.whatsapp_number', '6281234567890');
        $message = urlencode($this->getWhatsAppMessage());
        
        return "https://wa.me/{$adminPhone}?text={$message}";
    }

    /**
     * Alias accessor for unit price (frontend expects `unit_price`).
     */
    public function getUnitPriceAttribute()
    {
        return $this->price;
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for shipped orders
     */
    public function scopeShipped($query)
    {
        return $query->where('status', 'shipped');
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
}
