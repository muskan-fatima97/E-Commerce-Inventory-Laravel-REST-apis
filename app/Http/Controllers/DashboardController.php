<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', 'pending')->count();

        $totalRevenue = Order::whereIn('status', ['delivered', 'shipped', 'processing'])
            ->sum('total_amount');

        $lowStockProducts = Product::where('stock_quantity', '<=', 10)
            ->where('stock_quantity', '>', 0)
            ->orderBy('stock_quantity')
            ->take(5)
            ->get(['id', 'name', 'stock_quantity', 'image']);

        $outOfStockCount = Product::where('stock_quantity', 0)->count();

        $recentOrders = Order::with('items')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalProducts'   => $totalProducts,
                'totalOrders'     => $totalOrders,
                'pendingOrders'   => $pendingOrders,
                'totalRevenue'    => $totalRevenue,
                'outOfStockCount' => $outOfStockCount,
            ],
            'lowStockProducts' => $lowStockProducts,
            'recentOrders'     => $recentOrders,
        ]);
    }
}