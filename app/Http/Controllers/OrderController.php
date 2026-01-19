<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

/**
 * OrderController - Menangani checkout dan order untuk guest/buyer
 */
class OrderController extends Controller
{
    /**
     * Show checkout page
     */
    public function checkout(Request $request, $slug)
    {
        $product = Product::with(['category', 'images'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->firstOrFail();

        $user = Auth::user();

        return Inertia::render('Checkout', [
            'product' => $product,
            'user' => $user,
        ]);
    }

    /**
     * Process order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($validated['product_id']);

        // Check stock
        if ($product->stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Stok tidak mencukupi.']);
        }

        $user = Auth::user();
        $isVerified = $user && $user->isVerified();
        
        // Determine price
        $unitPrice = $isVerified ? $product->verified_price : $product->price;
        $totalPrice = $unitPrice * $validated['quantity'];

        // Create order
        $order = Order::create([
            'user_id' => $user?->id,
            'product_id' => $product->id,
            'customer_name' => $validated['customer_name'],
            'customer_phone' => $validated['customer_phone'],
            'customer_email' => $validated['customer_email'],
            'address' => $validated['address'] ?? '',
            'quantity' => $validated['quantity'],
            'price' => $unitPrice,
            'total_price' => $totalPrice,
            'is_verified_price' => $isVerified,
            'notes' => $validated['notes'],
            'status' => 'pending',
        ]);

        // Decrease stock
        $product->decrement('stock', $validated['quantity']);

        // Get WhatsApp URL
        $whatsappUrl = $order->getWhatsAppUrl();

        return Inertia::render('OrderSuccess', [
            'order' => $order->load('product'),
            'whatsappUrl' => $whatsappUrl,
        ]);
    }

    /**
     * Show order history (for authenticated users)
     */
    public function history()
    {
        $user = Auth::user();

        $orders = Order::with(['product'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return Inertia::render('Order/History', [
            'orders' => $orders,
        ]);
    }

    /**
     * Show order detail
     */
    public function show($orderNumber)
    {
        $user = Auth::user();

        $order = Order::with(['product.images'])
            ->where('order_number', $orderNumber)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return Inertia::render('Order/Show', [
            'order' => $order,
        ]);
    }
}
