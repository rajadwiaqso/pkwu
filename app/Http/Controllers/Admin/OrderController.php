<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

/**
 * OrderController - Menangani order untuk admin
 */
class OrderController extends Controller
{
    /**
     * List all orders
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isSuperAdmin = $user->isSuperAdmin();

        $query = Order::with(['product', 'user']);

        // Admin biasa hanya melihat order
        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Filter by date
        if ($date = $request->get('date')) {
            $query->whereDate('created_at', $date);
        }

        // Search by order number or customer name
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return Inertia::render('Admin/Order/Index', [
            'orders' => $orders,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'date']),
        ]);
    }

    /**
     * Show order detail
     */
    public function show(Order $order)
    {
        $order->load(['product.images', 'user']);

        return Inertia::render('Admin/Order/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
        ]);

        $oldStatus = $order->status;
        $order->status = $validated['status'];

        // Track timestamps
        if ($validated['status'] === 'processing' && $oldStatus === 'pending') {
            $order->paid_at = now();
        }

        if ($validated['status'] === 'shipped') {
            $order->shipped_at = now();
        }

        if ($validated['status'] === 'completed') {
            $order->completed_at = now();
            // Increment sold count
            if ($order->product) {
                $order->product->increment('sold_count', $order->quantity);
            }
        }

        // If cancelled, restore stock
        if ($validated['status'] === 'cancelled' && $oldStatus !== 'cancelled') {
            if ($order->product) {
                $order->product->increment('stock', $order->quantity);
            }
        }

        $order->save();

        return back()->with('success', 'Status order berhasil diperbarui.');
    }

    /**
     * Update tracking number
     */
    public function updateTracking(Request $request, Order $order)
    {
        $validated = $request->validate([
            'tracking_number' => 'required|string|max:100',
        ]);

        $order->update([
            'tracking_number' => $validated['tracking_number'],
        ]);

        return back()->with('success', 'Nomor resi berhasil disimpan.');
    }
    /**
     * Upload proof of delivery
     */
    public function uploadProof(Request $request, Order $order)
    {
        $request->validate([
            'proof' => 'required|image|max:2048',
        ]);

        // Delete old proof
        if ($order->proof_of_delivery) {
            Storage::disk('public')->delete($order->proof_of_delivery);
        }

        $path = $request->file('proof')->store('proofs', 'public');
        $order->update(['proof_of_delivery' => $path]);

        return back()->with('success', 'Bukti pengiriman berhasil diupload.');
    }

    /**
     * Delete proof of delivery
     */
    public function deleteProof(Order $order)
    {
        if ($order->proof_of_delivery) {
            Storage::disk('public')->delete($order->proof_of_delivery);
            $order->update(['proof_of_delivery' => null]);
        }

        return back()->with('success', 'Bukti pengiriman berhasil dihapus.');
    }
}
