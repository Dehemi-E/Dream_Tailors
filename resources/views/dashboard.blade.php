@extends('layouts.app')

@section('content')
<div x-data="{ revenueModal: false }" class="space-y-8 max-w-7xl mx-auto animate-[fadeIn_0.6s_ease-out]">
    
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Dashboard</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Tailor shop overview & analytics.</p>
        </div>
        <span class="self-start px-4 py-2 bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 text-xs font-bold rounded-xl border border-indigo-200 dark:border-indigo-500/20 shadow-sm">
            {{ now()->format('M d, Y') }}
        </span>
    </div>

    <!-- Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Weekly Revenue -->
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700/60 hover:shadow-md transition-all hover:-translate-y-0.5 duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Weekly Revenue</p>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white">Rs. {{ number_format($weeklyRevenue ?? 145500, 2) }}</h3>
            <p class="text-xs font-medium text-emerald-600 dark:text-emerald-400 mt-2">+{{ $revenueGrowthPercentage ?? '12.5' }}% from last week</p>
            <div class="mt-4 h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden"><div class="h-full rounded-full bg-indigo-500" style="width:72%"></div></div>
        </div>

        <!-- Active Orders -->
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700/60 hover:shadow-md transition-all hover:-translate-y-0.5 duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Active Orders</p>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $activeOrdersTotal ?? 34 }}</h3>
            <div class="flex gap-2 mt-2">
                <span class="text-[10px] uppercase tracking-wider border border-amber-200 dark:border-amber-500/20 bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 px-2 py-0.5 rounded-md font-bold">{{ $processingCount ?? 15 }} Proc.</span>
                <span class="text-[10px] uppercase tracking-wider border border-blue-200 dark:border-blue-500/20 bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 px-2 py-0.5 rounded-md font-bold">{{ $measuringCount ?? 19 }} Meas.</span>
            </div>
            <div class="mt-3.5 h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden"><div class="h-full rounded-full bg-amber-500" style="width:55%"></div></div>
        </div>

        <!-- Total Customers -->
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700/60 hover:shadow-md transition-all hover:-translate-y-0.5 duration-300 group">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Customers</p>
                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $totalCustomers ?? 856 }}</h3>
            <p class="text-xs font-medium text-blue-600 dark:text-blue-400 mt-2">+{{ $newCustomersThisWeek ?? 18 }} new this week</p>
            <div class="mt-4 h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden"><div class="h-full rounded-full bg-blue-500" style="width:85%"></div></div>
        </div>

        <!-- Total Revenue (Clickable) -->
        <div @click="revenueModal = true" class="cursor-pointer bg-white dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl p-5 shadow-sm border border-emerald-200 dark:border-emerald-700/60 hover:shadow-lg transition-all hover:-translate-y-1 duration-300 group relative overflow-hidden">
            <div class="absolute top-0 right-0 p-4 opacity-0 group-hover:opacity-100 transition-opacity">
                <svg class="w-4 h-4 text-emerald-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122"/></svg>
            </div>

            <div class="flex items-center justify-between mb-3">
                <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Total Revenue</p>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800 dark:text-white">Rs. {{ number_format($totalRevenue ?? 325500, 2) }}</h3>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-2">All time earnings (Click to view daily)</p>
            <div class="mt-4 h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden"><div class="h-full rounded-full bg-emerald-500" style="width:100%"></div></div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700/60">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 tracking-tight">Weekly Revenue Dynamics</h3>
            <div class="h-72"><canvas id="revenueChart"></canvas></div>
        </div>
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl p-6 shadow-sm border border-slate-200 dark:border-slate-700/60">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white mb-6 tracking-tight">Garment Categories</h3>
            <div class="h-64 flex items-center justify-center"><canvas id="orderTypePieChart"></canvas></div>
        </div>
    </div>

    <!-- 🟢 Recent Orders Table -->
    <div class="bg-white dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-700/60 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white tracking-tight">Recent Orders</h3>
            <a href="/order-management" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 transition-colors flex items-center gap-1">
                View All <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-500 dark:text-slate-400 uppercase text-[11px] font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4 text-left">Order ID</th>
                        <th class="px-6 py-4 text-left">Customer</th>
                        <th class="px-6 py-4 text-left">Item</th>
                        <th class="px-6 py-4 text-left">Tier</th>
                        <th class="px-6 py-4 text-left">Price</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @forelse($recentOrders ?? [] as $order)
                    @php 
                        $oId = is_array($order) ? ($order['id'] ?? 0) : ($order->id ?? 0);
                        $cName = is_array($order) ? ($order['name'] ?? $order['customer_name'] ?? 'Customer') : ($order->name ?? $order->customer_name ?? 'Customer');
                        $item = is_array($order) ? ($order['garment_name'] ?? $order['garment_category'] ?? $order['item'] ?? 'Item') : ($order->garment_name ?? $order->garment_category ?? $order->item ?? 'Item');
                        $tier = is_array($order) ? ($order['tier'] ?? $order['customer_tier'] ?? 'Standard') : ($order->tier ?? $order->customer_tier ?? 'Standard');
                        $status = is_array($order) ? ($order['status'] ?? 'Processing') : ($order->status ?? 'Processing');
                        
                        $price = is_array($order) ? ($order['total_amount'] ?? $order['amount'] ?? $order['price'] ?? 0) : ($order->total_amount ?? $order->amount ?? $order->price ?? 0);
                        
                        if ($price == 0 && $oId > 0) {
                            try {
                                $price = \DB::table('payments')->where('order_id', $oId)->sum('amount');
                            } catch(\Exception $e) {
                                $price = 0;
                            }
                        }
                    @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition-colors">
                        <td class="px-6 py-4 font-mono font-bold text-indigo-600 dark:text-indigo-400 text-xs">
                            #ORD-{{ str_pad($oId, 3, '0', STR_PAD_LEFT) }}
                        </td>
                        <td class="px-6 py-4 font-medium text-slate-800 dark:text-white">
                            {{ $cName }}
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400 capitalize">
                            {{ $item }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border shadow-sm
                                {{ strtoupper($tier) === 'VIP' ? 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20' : 
                                   (strtoupper($tier) === 'CORPORATE' ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20' : 
                                   'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-700/50 dark:text-slate-400 dark:border-slate-600') }}">
                                {{ $tier }}
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-emerald-600 dark:text-emerald-400">
                            Rs. {{ number_format($price, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border shadow-sm
                                {{ strtoupper($status) === 'READY' || strtoupper($status) === 'COMPLETED' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20' : 
                                   (strtoupper($status) === 'PROCESSING' ? 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20' : 
                                   'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20') }}">
                                {{ $status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="/order-management" class="px-4 py-2 bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-500/20 hover:text-indigo-700 dark:hover:text-indigo-400 transition-colors text-xs font-semibold shadow-sm inline-block">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="w-12 h-12 mx-auto bg-slate-100 dark:bg-slate-700/50 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 font-medium">No recent orders found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Daily Revenue Modal -->
    <div x-show="revenueModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm" x-cloak
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-2xl max-h-[80vh] flex flex-col mx-4 shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden" 
             @click.away="revenueModal = false"
             x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
            
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-700/60 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 dark:text-white">Daily Revenue Breakdown</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Total income separated day by day</p>
                    </div>
                </div>
                <button @click="revenueModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors focus:outline-none">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto custom-scrollbar flex-1 bg-slate-50 dark:bg-slate-900/50">
                <div class="space-y-3">
                    @php
                        $dailyData = $dailyRevenue ?? [
                            now()->format('Y-m-d') => 18500,
                            now()->subDay()->format('Y-m-d') => 24000,
                            now()->subDays(2)->format('Y-m-d') => 15200,
                            now()->subDays(3)->format('Y-m-d') => 42000,
                            now()->subDays(4)->format('Y-m-d') => 9500,
                            now()->subDays(5)->format('Y-m-d') => 31000,
                        ];
                    @endphp

                    @forelse($dailyData as $date => $amount)
                        <div class="flex items-center justify-between p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm hover:border-emerald-300 dark:hover:border-emerald-500/50 transition-colors">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-100 dark:border-slate-700 flex flex-col items-center justify-center">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase">{{ \Carbon\Carbon::parse($date)->format('M') }}</span>
                                    <span class="text-sm font-black text-slate-700 dark:text-slate-200 leading-none">{{ \Carbon\Carbon::parse($date)->format('d') }}</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 dark:text-white">{{ \Carbon\Carbon::parse($date)->format('l') }}</h4>
                                    <p class="text-[10px] text-slate-500 font-medium mt-0.5">{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-lg font-black text-emerald-600 dark:text-emerald-400">Rs. {{ number_format($amount, 2) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <div class="w-16 h-16 mx-auto bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </div>
                            <p class="text-slate-500 dark:text-slate-400 font-medium">No daily revenue data available yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function initCharts() {
        const isDark = document.documentElement.classList.contains('dark');
        const styles = getComputedStyle(document.documentElement);
        
        const lineColor = isDark ? '#818cf8' : '#4f46e5';
        const fillColor = isDark ? 'rgba(129, 140, 248, 0.15)' : 'rgba(79,70,229,0.1)';
        const gridColor = isDark ? '#334155' : '#e2e8f0';
        const textColor = isDark ? '#94a3b8' : '#64748b';
        const legendColor = isDark ? '#cbd5e1' : '#475569';
        
        // 🟢 Pie Chart Colors Changed: Yellow, Blue, Red, Green
        const pieColors = isDark ? ['#fbbf24', '#60a5fa', '#f87171', '#34d399'] : ['#eab308', '#3b82f6', '#ef4444', '#10b981'];

        if (window.revenueChart instanceof Chart) window.revenueChart.destroy();
        if (window.pieChart instanceof Chart) window.pieChart.destroy();

        const revenueCanvas = document.getElementById('revenueChart');
        const pieCanvas = document.getElementById('orderTypePieChart');
        if (!revenueCanvas || !pieCanvas) return;

        const weeklyRevenueData = {!! json_encode($chartRevenueData ?? [15000,28000,18000,42000,24000,55000,30000]) !!};
        const pieLabels = {!! json_encode($pieLabels ?? ['Upper Body','Lower Body','Full Body','Custom']) !!};
        const pieData = {!! json_encode($pieData ?? [45,25,20,10]) !!};

        const ctx = revenueCanvas.getContext('2d');
        const gradient = ctx.createLinearGradient(0,0,0,400);
        gradient.addColorStop(0,fillColor);
        gradient.addColorStop(1,'rgba(0,0,0,0)');

        window.revenueChart = new Chart(ctx,{
            type:'line',
            data:{
                labels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
                datasets:[{
                    data:weeklyRevenueData,
                    borderColor:lineColor,
                    backgroundColor:gradient,
                    borderWidth:3,
                    tension:0.4,
                    fill:true,
                    pointBackgroundColor:lineColor,
                    pointBorderColor:isDark?'#1e293b':'#ffffff',
                    pointRadius:4,
                    pointHoverRadius:6
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                animation:{duration:2000, easing:'easeOutQuart'},
                plugins:{legend:{display:false}},
                scales:{
                    y:{beginAtZero:true,grid:{color:gridColor, drawBorder:false},ticks:{color:textColor, padding:10}},
                    x:{grid:{display:false},ticks:{color:textColor, padding:10}}
                }
            }
        });

        window.pieChart = new Chart(pieCanvas.getContext('2d'),{
            type:'doughnut',
            data:{
                labels:pieLabels,
                datasets:[{
                    data:pieData,
                    backgroundColor:pieColors,
                    borderWidth:isDark ? 0 : 2,
                    borderColor:isDark?'#1e293b':'#ffffff',
                    hoverOffset: 4
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:false,
                cutout:'70%',
                animation:{duration:2000, easing:'easeOutQuart'},
                plugins:{
                    legend:{
                        position:'bottom',
                        labels:{color:legendColor,padding:20,font:{weight:'600', family: "'Inter', sans-serif"}}
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded',() => setTimeout(initCharts,300));
    window.addEventListener('darkModeToggled',() => setTimeout(initCharts,300));
</script>
@endsection

<style>
    @keyframes fadeIn{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}}
</style>