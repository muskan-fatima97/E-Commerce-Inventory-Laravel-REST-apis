<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Order;
use App\Models\OrderItem;

class AnalyticalChartsController extends Controller
{
    /**
     * Revenue trend over the last N days.
     */
    public function revenue(Request $request)
    {
        $days = (int) $request->get('days', 30);
        $startDate = now()->subDays($days - 1)->startOfDay();

        $orders = Order::where('created_at', '>=', $startDate)
            ->whereIn('status', ['pending', 'processing', 'shipped', 'delivered'])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = [];
        $data = [];

        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('M d');
            $data[] = isset($orders[$date]) ? (float) $orders[$date]->total : 0;
        }

        return response()->json([
            'labels' => $labels,
            'data'   => $data,
        ]);
    }

    /**
     * Order count grouped by status.
     */
    public function ordersByStatus()
    {
        $counts = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

        return response()->json([
            'labels' => array_map('ucfirst', $statuses),
            'data'   => array_map(fn ($s) => (int) ($counts[$s] ?? 0), $statuses),
        ]);
    }

    /**
     * Top selling products by quantity sold.
     */
    public function topProducts(Request $request)
    {
        $limit = (int) $request->get('limit', 5);

        $topProducts = OrderItem::selectRaw('product_name, SUM(quantity) as total_sold')
            ->groupBy('product_name')
            ->orderByDesc('total_sold')
            ->take($limit)
            ->get();

        return response()->json([
            'labels' => $topProducts->pluck('product_name'),
            'data'   => $topProducts->pluck('total_sold'),
        ]);
    }
}