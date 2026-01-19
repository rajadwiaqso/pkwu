<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * User Model untuk Toko Digital Raja
 * 
 * Roles:
 * - buyer: Pembeli biasa, dapat membeli dengan harga verified jika email terverifikasi
 * - admin: Admin biasa, dapat mengatur produk, banner, dll
 * - super_admin: Super admin, dapat mengatur semua termasuk user management
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'role',
        'is_active',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Check if user is a buyer
     */
    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }

    /**
     * Check if user is an admin (admin or super_admin)
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user has verified email (untuk dapat harga verified)
     */
    public function isVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    /**
     * Get products owned by this admin
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'admin_id');
    }

    /**
     * Get orders made by this user (as buyer)
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id');
    }

    /**
     * Get orders that this admin needs to handle
     */
    public function adminOrders()
    {
        return $this->hasMany(Order::class, 'admin_id');
    }

    /**
     * Get announcements created by this user
     */
    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=10B981&color=fff';
    }
}
