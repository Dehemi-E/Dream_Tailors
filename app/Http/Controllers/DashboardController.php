<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Weekly Revenue & Growth
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        $weeklyRevenue = DB::table('payments')
            ->whereBetween('payment_date', [$startOfWeek, $endOfWeek])
            ->sum('amount');

        $lastWeekRevenue = DB::table('payments')
            ->whereBetween('payment_date', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])
            ->sum('amount');
            
        $revenueGrowthPercentage = $lastWeekRevenue > 0 
            ? round((($weeklyRevenue - $lastWeekRevenue) / $lastWeekRevenue) * 100, 1) 
            : 100;

        // 2. Active Orders Stats (🟢 Customers ලා ඉන්න Orders විතරක් ගණන් කරන්න Join එක දැම්මා)
        $activeOrdersQuery = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id');
            
        $activeOrdersTotal = (clone $activeOrdersQuery)
            ->whereNotIn('orders.status', ['Completed', 'completed', 'COMPLETED'])
            ->count();
            
        $processingCount = (clone $activeOrdersQuery)
            ->where('orders.status', 'Processing')
            ->count();
            
        $measuringCount = (clone $activeOrdersQuery)
            ->where('orders.status', 'Measuring')
            ->count();
            
        $readyOrdersCount = (clone $activeOrdersQuery)
            ->where('orders.status', 'Ready')
            ->count();

        // 3. Customers Stats
        $totalCustomers = DB::table('customers')->count();
        $newCustomersThisWeek = DB::table('customers')
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->count();

        // 4. Line Chart Data (Monday to Sunday Revenue)
        $chartRevenueData = [];
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
            $dailySum = DB::table('payments')->whereDate('payment_date', $date)->sum('amount');
            $chartRevenueData[] = (float)$dailySum;
        }

        // 5. Pie Chart Data (දැන් හරියටම garment_category එකෙන් ගන්නවා)
        $itemTypes = DB::table('orders')
            ->select('garment_category', DB::raw('count(*) as total'))
            ->groupBy('garment_category')
            ->get();
            
        $pieLabels = $itemTypes->pluck('garment_category')->toArray();
        $pieData = $itemTypes->pluck('total')->toArray();
        
        if (empty($pieLabels)) {
            $pieLabels = ['No Data Yet'];
            $pieData = [100];
        }

        // 6. Recent Orders
        $recentOrders = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->select('orders.*', 'customers.name as customer_name', 'customers.tier as customer_tier')
            ->orderBy('orders.id', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'weeklyRevenue', 'revenueGrowthPercentage', 
            'activeOrdersTotal', 'processingCount', 'measuringCount', 'readyOrdersCount',
            'totalCustomers', 'newCustomersThisWeek',
            'chartRevenueData', 'pieLabels', 'pieData',
            'recentOrders'
        ));
    }
}