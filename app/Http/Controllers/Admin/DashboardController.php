<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * DashboardController - Dashboard untuk admin
 */
class DashboardController extends Controller
{
    /**
     * Show admin dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();

        // Get stats based on role
        if ($isSuperAdmin) {
            $totalProducts = Product::count();
            $totalOrders = Order::count();
            $pendingOrders = Order::pending()->count();
            $totalRevenue = Order::completed()->sum('total_price');
            $totalUsers = User::where('role', 'buyer')->count();
            
            $recentOrders = Order::with(['product', 'user'])
                ->latest()
                ->take(5)
                ->get();
        } else {
            // Admin biasa hanya melihat produk dan order miliknya
            $totalProducts = Product::where('admin_id', $user->id)->count();
            $totalOrders = Order::where('admin_id', $user->id)->count();
            $pendingOrders = Order::where('admin_id', $user->id)->pending()->count();
            $totalRevenue = Order::where('admin_id', $user->id)->completed()->sum('total_price');
            $totalUsers = null;
            
            $recentOrders = Order::with(['product', 'user'])
                ->where('admin_id', $user->id)
                ->latest()
                ->take(5)
                ->get();
        }

        // Order stats for chart
        $orderStats = $this->getOrderStats($user, $isSuperAdmin);

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalProducts' => $totalProducts,
                'totalOrders' => $totalOrders,
                'pendingOrders' => $pendingOrders,
                'totalRevenue' => $totalRevenue,
                'totalUsers' => $totalUsers,
            ],
            'recentOrders' => $recentOrders,
            'orderStats' => $orderStats,
            'isSuperAdmin' => $isSuperAdmin,
        ]);
    }

    /**
     * Get order statistics for chart
     */
    protected function getOrderStats($user, $isSuperAdmin): array
    {
        $stats = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $query = Order::whereDate('created_at', $date);
            
            if (!$isSuperAdmin) {
                $query->where('admin_id', $user->id);
            }
            
            $stats[] = [
                'date' => $date->format('d M'),
                'count' => $query->count(),
                'revenue' => $query->completed()->sum('total_price'),
            ];
        }
        
        return $stats;
    }
}
