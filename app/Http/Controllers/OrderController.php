<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Customer: place order from cart (COD)
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'customer_name'     => 'required|string|max:255',
        'customer_phone'    => 'required|string|max:20',
        'shipping_address'  => 'required|string|max:1000',
        'city'              => 'required|string|max:255',
        'payment_method'    => 'required|in:cod,stripe',
    ]);

    $sessionId = $request->session()->getId();

    $cartItems = Cart::with('product')
        ->where('session_id', $sessionId)
        ->get();

    if ($cartItems->isEmpty()) {
        return response()->json([
            'success' => false,
            'message' => 'Your cart is empty',
        ], 422);
    }

    $order = DB::transaction(function () use ($validated, $cartItems, $sessionId, $request) {
        $total = $cartItems->sum(fn ($item) => $item->product->price * $item->quantity);

        $order = Order::create([
            'session_id'        => $sessionId,
            'user_id'           => $request->user()?->id,
            'customer_name'     => $validated['customer_name'],
            'customer_phone'    => $validated['customer_phone'],
            'shipping_address'  => $validated['shipping_address'],
            'city'              => $validated['city'],
            'total_amount'      => $total,
            'status'            => 'pending',
            'payment_method'    => $validated['payment_method'],
            'payment_status'    => 'unpaid',
        ]);

        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id'     => $order->id,
                'product_id'   => $item->product_id,
                'product_name' => $item->product->name,
                'price'        => $item->product->price,
                'quantity'     => $item->quantity,
            ]);

            $item->product->decrement('stock_quantity', $item->quantity);
        }

        Cart::where('session_id', $sessionId)->delete();

        return $order;
    });

    // Stripe — return checkout URL, frontend will redirect to it
    if ($validated['payment_method'] === 'stripe') {
        $order->load('items');
        $checkoutUrl = app(StripeController::class)->createCheckoutSession($order);

        return response()->json([
            'success'      => true,
            'checkout_url' => $checkoutUrl,
        ]);
    }

    // COD — straight to confirmation
    return response()->json([
        'success' => true,
        'message' => 'Order placed successfully',
        'data'    => $order->load('items'),
    ], 201);
}

    /**
     * Customer: order confirmation / success page
     */
    public function confirmation(Request $request, $id)
{
    $order = Order::with('items')->findOrFail($id);

    $sessionId = $request->session()->getId();
    $userId = $request->user()?->id;

    if ($order->session_id !== $sessionId && $order->user_id !== $userId) {
        abort(403, 'You are not authorized to view this order.');
    }

    return Inertia::render('OrderConfirmation', [
        'order' => $order,
    ]);
}

    /**
     * Admin: list all orders
     */
    public function index(Request $request)
    {
        $query = Order::with('items')->latest();

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only('status'),
        ]);
    }

    /**
     * Admin: view single order
     */
    public function show($id)
    {
        $order = Order::with('items.product')->findOrFail($id);

        return Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Admin: update order status
     */
    /**
 * Admin: update order status
 */
public function updateStatus(Request $request, $id)
{
    $validated = $request->validate([
        'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
    ]);

    $order = Order::findOrFail($id);

    $updateData = ['status' => $validated['status']];

    // COD order delivered ho to payment automatically paid mark karo
    if ($validated['status'] === 'delivered' && $order->payment_method === 'cod') {
        $updateData['payment_status'] = 'paid';
    }

    // Order cancel ho aur abhi tak paid nahi hua, to payment bhi cancelled mark karo
    if ($validated['status'] === 'cancelled' && $order->payment_status !== 'paid') {
        $updateData['payment_status'] = 'cancelled';
    }

    $order->update($updateData);

    return response()->json([
        'success' => true,
        'message' => 'Order status updated',
        'data'    => $order,
    ]);
}
    public function checkoutPage(Request $request)
{
    $sessionId = $request->session()->getId();

    $cartItems = \App\Models\Cart::with('product')
        ->where('session_id', $sessionId)
        ->get();

    return Inertia::render('Checkout', [
        'items' => $cartItems,
    ]);
}
public function myOrders(Request $request)
{
    $orders = Order::with('items')
        ->where('user_id', $request->user()->id)
        ->latest()
        ->paginate(10);

    return Inertia::render('OrderHistory', [
        'orders' => $orders,
    ]);
}
}