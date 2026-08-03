<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class CancelExpiredOrders extends Command
{
    protected $signature = 'orders:cancel-expired';
    protected $description = 'Cancel JazzCash orders whose payment window has expired without payment';

    public function handle()
    {
        $expired = Order::where('payment_method', 'jazzcash')
            ->where('payment_status', 'pending')
            ->where('payment_expires_at', '<', now())
            ->get();

        foreach ($expired as $order) {
            $order->update(['status' => 'cancelled', 'payment_status' => 'failed']);
        }

        $this->info("Cancelled {$expired->count()} expired order(s).");
    }
}c