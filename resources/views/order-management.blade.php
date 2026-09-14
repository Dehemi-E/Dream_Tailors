@extends('layouts.app')

@section('content')
<style>
    .order-card:not([style*="display: none"]):not([style*="display:none"])~.empty-state-msg{display:none!important}
    [x-cloak]{display:none!important}
    @keyframes scissor-cut{0%,100%{transform:scale(1)rotate(0deg)}50%{transform:scale(1.1)rotate(15deg)}}
    .animate-scissor-cut{animation:scissor-cut 1s ease-in-out infinite}
    @keyframes fadeIn{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}}
</style>

<!-- 🟢 Main Alpine Wrapper -->
<div x-data="{ expanded: null, tierFilter: 'all', statusFilter: 'all', search: '', completedModal: false }" class="max-w-7xl mx-auto">
    
    <!-- Inner Wrapper -->
    <div class="space-y-8 animate-[fadeIn_0.6s_ease-out]">
        
        @php 
            $measuringCount = $orders->where('status','Measuring')->count();
            $processingCount = $orders->where('status','Processing')->count();
            $readyCount = $orders->where('status','Ready')->count();
            $activeCount = $orders->whereNotIn('status',['Completed','completed','COMPLETED'])->count(); 
        @endphp

        <!-- 📊 Stats Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
            <!-- Active -->
            <div class="bg-indigo-50 dark:bg-indigo-500/10 backdrop-blur-xl rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md hover:-translate-y-0.5 duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Active</p>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white mt-0.5">{{ $activeCount }}</p>
                    </div>
                </div>
            </div>
            <!-- Measuring -->
            <div class="bg-blue-50 dark:bg-blue-500/10 backdrop-blur-xl rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md hover:-translate-y-0.5 duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Measuring</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-0.5">{{ $measuringCount }}</p>
                    </div>
                </div>
            </div>
            <!-- Processing -->
            <div class="bg-amber-50 dark:bg-amber-500/10 backdrop-blur-xl rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md hover:-translate-y-0.5 duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Processing</p>
                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-0.5">{{ $processingCount }}</p>
                    </div>
                </div>
            </div>
            <!-- Ready -->
            <div class="bg-emerald-50 dark:bg-emerald-500/10 backdrop-blur-xl rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md hover:-translate-y-0.5 duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-medium text-slate-500 dark:text-slate-400 uppercase tracking-widest">Ready</p>
                        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $readyCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🔍 Header & Filters -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white dark:bg-slate-800/80 backdrop-blur-xl p-4 rounded-2xl border border-slate-200 dark:border-slate-700/60 shadow-sm">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white">Active Orders List</h2>
            <div class="flex flex-wrap sm:flex-nowrap gap-3 w-full md:w-auto">
                
                <div class="relative w-full sm:w-48 md:w-60">
                    <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input type="text" x-model="search" placeholder="Search Name or ID..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition-all">
                </div>

                <select x-model="tierFilter" class="w-full sm:w-auto bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all cursor-pointer">
                    <option value="all">All Tiers</option>
                    <option value="VIP">VIP</option>
                    <option value="Corporate">Corporate</option>
                    <option value="Standard">Standard</option>
                </select>
                <select x-model="statusFilter" class="w-full sm:w-auto bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all cursor-pointer">
                    <option value="all">All Status</option>
                    <option value="Measuring">Measuring</option>
                    <option value="Processing">Processing</option>
                    <option value="Ready">Ready</option>
                </select>
                <button @click="completedModal=true" class="w-full sm:w-auto bg-indigo-50 dark:bg-indigo-500/10 hover:bg-indigo-100 dark:hover:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400 px-5 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider border border-indigo-200 dark:border-indigo-500/20 transition-colors shadow-sm">
                    Archive
                </button>
            </div>
        </div>

        <!-- 📦 Orders List (Grouped by Customer) -->
        <div class="space-y-6">
            @php $groupedOrders = $orders->groupBy('customer_id'); @endphp
            
            @forelse($groupedOrders as $customerId => $customerOrders)
            <div x-data="{
                    tier: '{{ $customerOrders->first()->customer_tier }}',
                    name: '{{ addslashes($customerOrders->first()->customer_name) }}',
                    id: '{{ $customerId }}',
                    orderIds: {{ json_encode($customerOrders->pluck('id')->toArray()) }},
                    statuses: {{ json_encode($customerOrders->pluck('status')->toArray()) }},
                    isVisible() {
                        let tierMatch = (this.tierFilter === 'all' || this.tier === this.tierFilter);
                        let statusMatch = (this.statusFilter === 'all' || this.statuses.includes(this.statusFilter));
                        
                        // 🟢 Customer ID Search Bug එක හැදුවා
                        let s = this.search.toLowerCase().trim();
                        let custStr1 = 'cust-' + String(this.id).padStart(3, '0');
                        let custStr2 = 'cus-' + String(this.id).padStart(3, '0');

                        let searchMatch = (s === '' || 
                            this.name.toLowerCase().includes(s) || 
                            String(this.id) === s || 
                            custStr1.includes(s) ||
                            custStr2.includes(s) ||
                            this.orderIds.some(oid => String(oid) === s || ('ord-'+String(oid).padStart(3, '0')).toLowerCase().includes(s))
                        );

                        return tierMatch && statusMatch && searchMatch;
                    }
                 }"
                 x-show="isVisible()"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="order-card bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden">
                
                <!-- Customer Header -->
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800/50 flex flex-wrap justify-between items-center gap-4">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center font-bold text-indigo-600 dark:text-indigo-400">
                            {{ substr($customerOrders->first()->customer_name, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-bold text-slate-800 dark:text-white">{{ $customerOrders->first()->customer_name }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400">CUST-{{ str_pad($customerId, 3, '0', STR_PAD_LEFT) }} • {{ $customerOrders->first()->customer_tier }} Tier</p>
                        </div>
                    </div>
                    <div class="text-xs font-bold text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                        {{ $customerOrders->count() }} Active Order(s)
                    </div>
                </div>

                <!-- Customer's Orders List -->
                <div class="p-5 space-y-4">
                    @foreach($customerOrders as $order)
                    <!-- 🟢 Auto Scroll වෙන්න id එකකුයි scroll-mt-24 එකකුයි දැම්මා -->
                    <div x-data="{ expanded: false, status: '{{ $order->status }}' }"
                         id="order-box-{{ $order->id }}"
                         x-show="statusFilter === 'all' || status === statusFilter"
                         class="scroll-mt-24 border border-slate-200 dark:border-slate-700 rounded-xl overflow-hidden bg-white dark:bg-slate-800 hover:border-indigo-300 dark:hover:border-indigo-500/50 transition-colors duration-300 shadow-sm">
                        
                        <!-- Card Header (🟢 Click කරාම Auto Scroll වෙන්න හැදුවා) -->
                        <div @click="expanded = !expanded; if(expanded) setTimeout(() => document.getElementById('order-box-{{ $order->id }}').scrollIntoView({behavior: 'smooth', block: 'start'}), 250)" 
                             class="flex items-center justify-between p-4 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-5 w-full">
                                <div class="font-mono font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-3 py-1.5 rounded-lg text-xs border border-indigo-100 dark:border-indigo-500/20 shadow-inner inline-block w-max">
                                    {{ 'ORD-'.str_pad($order->id,3,'0',STR_PAD_LEFT) }}
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-semibold text-slate-800 dark:text-white">
                                        {{ $order->garment_name??$order->garment_category }}
                                        @if($order->fabric_name)<span class="text-indigo-500 dark:text-indigo-400 font-medium ml-1">| {{ $order->fabric_name }}</span>@endif
                                    </div>
                                </div>
                                <span :class="status==='Ready'?'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20':(status==='Processing'?'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20':'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20')" 
                                      class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border shadow-sm w-max" x-text="status"></span>
                            </div>
                            <div class="hidden sm:flex text-slate-400 text-[10px] font-bold uppercase tracking-widest items-center gap-2 ml-4">
                                <span x-text="expanded ? 'Hide' : 'Details'"></span>
                                <svg class="w-4 h-4 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>

                        <!-- Expanded Details -->
                        <div x-show="expanded" x-cloak x-collapse class="px-5 pb-5 border-t border-slate-100 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-900/20">
                            
                            <!-- Status Progress Bar -->
                            <div class="flex items-center justify-center gap-0 my-6 py-6 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all {{ in_array($order->status,['Measuring','Processing','Ready'])?'bg-indigo-500 text-white shadow-md shadow-indigo-500/30 ring-4 ring-indigo-50 dark:ring-indigo-900/20':'bg-slate-200 dark:bg-slate-800 text-slate-500'}}">1</div>
                                    <span class="text-[10px] uppercase tracking-widest font-semibold mt-2.5 text-slate-500 dark:text-slate-400">Measuring</span>
                                </div>
                                <div class="w-10 sm:w-16 h-1 mx-2 rounded-full transition-all {{ in_array($order->status,['Processing','Ready'])?'bg-indigo-500':'bg-slate-200 dark:bg-slate-800'}}"></div>
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all {{ in_array($order->status,['Processing','Ready'])?'bg-amber-500 text-white shadow-md shadow-amber-500/30 ring-4 ring-amber-50 dark:ring-amber-900/20':'bg-slate-200 dark:bg-slate-800 text-slate-500'}}">2</div>
                                    <span class="text-[10px] uppercase tracking-widest font-semibold mt-2.5 text-slate-500 dark:text-slate-400">Processing</span>
                                </div>
                                <div class="w-10 sm:w-16 h-1 mx-2 rounded-full transition-all {{ $order->status==='Ready'?'bg-amber-500':'bg-slate-200 dark:bg-slate-800'}}"></div>
                                <div class="flex flex-col items-center">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all {{ $order->status==='Ready'?'bg-emerald-500 text-white shadow-md shadow-emerald-500/30 ring-4 ring-emerald-50 dark:ring-emerald-900/20':'bg-slate-200 dark:bg-slate-800 text-slate-500'}}">3</div>
                                    <span class="text-[10px] uppercase tracking-widest font-semibold mt-2.5 text-slate-500 dark:text-slate-400">Ready</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Image -->
                                <div>
                                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Reference Image</h4>
                                    <div class="aspect-[4/3] rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 shadow-sm relative">
                                        @if($order->design_image)
                                            <img src="{{ asset(ltrim($order->design_image, '/')) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="flex flex-col items-center justify-center h-full text-slate-400">
                                                <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                <span class="text-[10px] uppercase tracking-wider font-medium">No Image</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Measurements -->
                                <div>
                                    <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Measurements</h4>
                                    <div class="grid grid-cols-2 gap-2 text-xs max-h-[14rem] overflow-y-auto pr-2 custom-scrollbar">
                                        @foreach(['collar','chest','shoulder','sleeve_length','full_length','armhole','upper_waist','lower_waist','hip','thigh','outseam','inseam','bottom_hem','knee'] as $f)
                                            @if($order->$f)
                                            <div class="bg-white dark:bg-slate-800 p-2.5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm flex justify-between items-center">
                                                <span class="text-slate-500 dark:text-slate-400 capitalize text-[11px] font-medium">{{ str_replace('_',' ',$f) }}</span>
                                                <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ $order->$f }}"</span>
                                            </div>
                                            @endif 
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Actions & Details -->
                                <div class="space-y-5">
                                    @if($order->fabric_source)
                                    <div>
                                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Fabric Selection</h4>
                                        <div class="bg-white dark:bg-slate-800 rounded-xl p-3 border border-slate-200 dark:border-slate-700 shadow-sm">
                                            <div class="flex items-center gap-3">
                                                @if($order->fabric_source==='shop')
                                                    @if(!empty($order->shop_fabric_image))
                                                        <img src="{{ asset(ltrim($order->shop_fabric_image, '/')) }}" class="w-10 h-10 rounded-lg object-cover border border-slate-200 dark:border-slate-600 shadow-sm">
                                                    @else
                                                        <div class="w-10 h-10 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center"><svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg></div>
                                                    @endif
                                                    <p class="text-sm font-bold text-indigo-700 dark:text-indigo-400">{{ $order->fabric_name }}</p>
                                                @else
                                                    <div class="w-10 h-10 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center"><svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg></div>
                                                    <p class="text-sm font-bold text-amber-700 dark:text-amber-400">Customer Provided</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <div>
                                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Styling Notes</h4>
                                        <div class="text-xs italic text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                            "{{ $order->styling_notes??'No additional styling notes provided.' }}"
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Update Status</h4>
                                        <select x-model="status" class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl p-3 text-sm font-medium focus:ring-2 focus:ring-indigo-500/50 outline-none dark:text-white transition-shadow cursor-pointer">
                                            <option value="Measuring">Measuring</option>
                                            <option value="Processing">Processing</option>
                                            <option value="Ready">Ready</option>
                                        </select>
                                    </div>

                                    <div class="flex gap-3 pt-2">
                                        <button @click="document.getElementById('loading-overlay').classList.remove('hidden');fetch('{{ route('orders.update',$order->id) }}',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({status:status})}).then(()=>window.location.reload())" 
                                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-xl text-[11px] font-bold uppercase tracking-widest shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all">
                                            Update Status
                                        </button>
                                        <form action="{{ route('orders.destroy',$order->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?')" class="flex-1">
                                            @csrf @method('DELETE')
                                            <button class="w-full py-3.5 bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 rounded-xl text-[11px] font-bold uppercase tracking-widest border border-rose-200 dark:border-rose-500/20 transition-all">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
                <!-- Laravel Empty State handled below -->
            @endforelse

            <!-- Empty State (Alpine) -->
            <div class="empty-state-msg py-16 text-center bg-white dark:bg-slate-800/80 rounded-3xl border border-dashed border-slate-300 dark:border-slate-600 shadow-sm">
                <div class="w-16 h-16 mx-auto mb-4 bg-slate-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center text-slate-400 dark:text-slate-500">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">All caught up!</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">No active orders matching this criteria.</p>
            </div>
        </div>
        
    </div> <!-- Inner Wrapper Ends -->

    <!-- 🗄️ Completed Archive Modal -->
    <div x-show="completedModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-md" x-cloak x-trap="completedModal" @keydown.escape.window="completedModal=false"
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
        
        <div class="bg-white dark:bg-slate-800 rounded-3xl w-full max-w-4xl max-h-[85vh] flex flex-col mx-4 shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden" 
             @click.away="completedModal=false"
             x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
            
            <!-- Modal Header -->
            <div class="px-8 py-6 border-b border-slate-100 dark:border-slate-700/60 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/50">
                <div>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-white">Completed Orders Archive</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">History of all fully completed tailoring jobs.</p>
                </div>
                <button @click="completedModal=false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-2.5 rounded-full bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 shadow-sm hover:shadow transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-8 overflow-y-auto custom-scrollbar flex-1 bg-slate-50 dark:bg-slate-900/50">
                <div class="space-y-6">
                    @forelse($completedOrders as $customerId=>$customerOrders)
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                        <div class="flex items-center gap-4 mb-5 pb-4 border-b border-slate-100 dark:border-slate-700/60">
                            <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center font-black text-emerald-600 dark:text-emerald-400 text-lg border border-emerald-100 dark:border-emerald-800/30">
                                {{ substr($customerOrders->first()->customer_name,0,1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-800 dark:text-white text-lg">{{ $customerOrders->first()->customer_name }}</h4>
                                <p class="text-xs font-mono font-medium text-slate-400 mt-0.5">CUS-{{ str_pad($customerId,3,'0',STR_PAD_LEFT) }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($customerOrders as $cOrder)
                            <div x-data="{open:false}" class="bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden hover:border-emerald-300 dark:hover:border-emerald-500/50 transition-colors">
                                <div @click="open=!open" class="flex items-center justify-between p-4 cursor-pointer">
                                    <div class="flex items-center gap-3">
                                        <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded text-xs border border-emerald-100 dark:border-emerald-500/20">{{ 'ORD-'.str_pad($cOrder->id,3,'0',STR_PAD_LEFT) }}</span>
                                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-300">{{ $cOrder->garment_name??$cOrder->garment_category }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Done</span>
                                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-300" :class="open?'rotate-180':''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                                <div x-show="open" x-cloak x-collapse class="p-4 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                                    <p class="text-[10px] uppercase tracking-widest font-bold text-slate-400 mb-1">Completed On</p>
                                    <p class="text-xs font-medium text-slate-700 dark:text-slate-300 mb-3">{{ \Carbon\Carbon::parse($cOrder->updated_at)->format('M d, Y') }}</p>
                                    
                                    <p class="text-[10px] uppercase tracking-widest font-bold text-slate-400 mb-1">Final Notes</p>
                                    <p class="text-xs italic text-slate-600 dark:text-slate-400 p-2.5 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-100 dark:border-slate-700/60">"{{ $cOrder->styling_notes??'No notes recorded.' }}"</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @empty
                    <div class="py-16 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center text-slate-300 dark:text-slate-600">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-700 dark:text-slate-300">No Archive Data</h3>
                        <p class="text-sm text-slate-500 mt-1">There are no completed orders yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- 🟢 Loading Overlay -->
    <div id="loading-overlay" class="fixed inset-0 z-[100] hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center">
        <div class="flex flex-col items-center">
            <div class="w-20 h-20 bg-white rounded-full overflow-hidden flex items-center justify-center animate-scissor-cut mb-4 shadow-2xl border-4 border-white/20">
                <img src="{{ asset('images/logo.jpeg') }}" alt="Loading..." class="w-full h-full object-cover">
            </div>
            <h3 class="text-xl font-bold text-white tracking-tight drop-shadow-md">Updating Status...</h3>
        </div>
    </div>
</div>

<style>
    /* Custom Scrollbar for inner elements */
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
</style>
@endsection