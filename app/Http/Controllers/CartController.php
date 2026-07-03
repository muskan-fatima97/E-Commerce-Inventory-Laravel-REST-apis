<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;
use Inertia\Inertia;

class CartController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
        ]);

        $sessionId = $request->session()->getId();

        $cartItem = Cart::where('session_id', $sessionId)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            $cartItem = Cart::create([
                'session_id' => $sessionId,
                'product_id' => $validated['product_id'],
                'quantity'   => $validated['quantity'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'data'    => $cartItem,
        ]);
    }

    public function index(Request $request)
    {
        $sessionId = $request->session()->getId();

        $items = Cart::with('product')
            ->where('session_id', $sessionId)
            ->get();

        return Inertia::render('Cart', [
            'items' => $items,
        ]);
    }

    public function updateQuantity(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $sessionId = $request->session()->getId();

        $cartItem = Cart::where('session_id', $sessionId)->where('id', $id)->firstOrFail();
        $cartItem->update(['quantity' => $validated['quantity']]);

        return response()->json([
            'success' => true,
            'message' => 'Quantity updated',
            'data'    => $cartItem,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $sessionId = $request->session()->getId();

        Cart::where('session_id', $sessionId)->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
        ]);
    }

    public function count(Request $request)
    {
        $sessionId = $request->session()->getId();

        $count = Cart::where('session_id', $sessionId)->sum('quantity');

        return response()->json(['count' => $count]);
    }
}