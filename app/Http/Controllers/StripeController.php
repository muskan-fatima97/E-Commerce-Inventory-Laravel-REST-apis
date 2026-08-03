<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class StripeController extends Controller
{
    protected StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    /**
     * Build a Stripe Checkout Session for an order.
     * Returns the Stripe-hosted checkout URL.
     */
    public function createCheckoutSession(Order $order): string
    {
        $lineItems = $order->items->map(function ($item) {
            return [
                'price_data' => [
                    'currency' => 'pkr',
                    'product_data' => [
                        'name' => $item->product_name,
                    ],
                    'unit_amount' => (int) round($item->price * 100),
                ],
                'quantity' => $item->quantity,
            ];
        })->toArray();

        $session = $this->stripe->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => route('stripe.checkout-success') . '?order_id=' . $order->id . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('stripe.checkout-cancel') . '?order_id=' . $order->id,
        ]);

        $order->update(['stripe_session_id' => $session->id]);

        return $session->url;
    }

    /**
     * Stripe redirects the customer here after successful payment.
     */
    public function checkoutSuccess(Request $request)
    {
        $order = Order::findOrFail($request->query('order_id'));

        $session = $this->stripe->checkout->sessions->retrieve($request->query('session_id'));

        if ($session->payment_status === 'paid') {
            $order->update(['payment_status' => 'paid']);
        }

        return redirect("/order/{$order->id}/confirmation");
    }

    /**
     * Stripe redirects the customer here if they cancel checkout.
     */
    public function checkoutCancel(Request $request)
    {
        $order = Order::find($request->query('order_id'));

        if ($order) {
            $order->update(['payment_status' => 'cancelled']);
        }

        return redirect('/checkout');
    }
}