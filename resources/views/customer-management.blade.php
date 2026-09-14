@extends('layouts.app')

@section('content')
@php 
    $totalCustomers = count($customers);
    $vipCount = collect($customers)->where('tier','VIP')->count();
    $corpCount = collect($customers)->where('tier','Corporate')->count();
    $stdCount = collect($customers)->where('tier','Standard')->count(); 
@endphp

<div x-data="{ expanded: null, tierFilter: 'all', search: '' }" class="space-y-8 max-w-7xl mx-auto">
    
    <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
        <!-- 💖 Total Clients Card -->
        <div class="bg-indigo-50 dark:bg-indigo-500/10 backdrop-blur-xl rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md hover:-translate-y-0.5 duration-300">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Clients</p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white mt-0.5">{{ $totalCustomers }}</p>
                </div>
            </div>
        </div>

        <!-- 💖 VIP Clients Card -->
        <div class="bg-purple-50 dark:bg-purple-500/10 backdrop-blur-xl rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md hover:-translate-y-0.5 duration-300">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">VIP Clients</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-0.5">{{ $vipCount }}</p>
                </div>
            </div>
        </div>

        <!-- 💖 Corporate Clients Card -->
        <div class="bg-blue-50 dark:bg-blue-500/10 backdrop-blur-xl rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md hover:-translate-y-0.5 duration-300">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Corporate Clients</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-0.5">{{ $corpCount }}</p>
                </div>
            </div>
        </div>

        <!-- 💖 Standard Clients Card -->
        <div class="bg-slate-100 dark:bg-slate-700/50 backdrop-blur-xl rounded-2xl p-5 shadow-sm border border-slate-200 dark:border-slate-700/60 transition-all hover:shadow-md hover:-translate-y-0.5 duration-300">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-slate-100 dark:bg-slate-700/50 flex items-center justify-center">
                    <svg class="w-6 h-6 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
                <div>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider">Standard Clients</p>
                    <p class="text-2xl font-bold text-slate-700 dark:text-slate-300 mt-0.5">{{ $stdCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 bg-white dark:bg-slate-800/80 backdrop-blur-xl p-4 rounded-2xl border border-slate-200 dark:border-slate-700/60 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
            Client Directory
        </h2>
        <div class="flex w-full sm:w-auto gap-3">
            <div class="relative w-full sm:w-64">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="search" placeholder="Search by name or ID..." class="w-full pl-10 pr-4 py-2.5 bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 outline-none transition-all">
            </div>
            <select x-model="tierFilter" class="bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all cursor-pointer">
                <option value="all">All Tiers</option>
                <option value="VIP">VIP</option>
                <option value="Corporate">Corporate</option>
                <option value="Standard">Standard</option>
            </select>
        </div>
    </div>

    <div class="space-y-4">
        @foreach($customers as $customer)
        @php 
            $totalOrders = DB::table('orders')->where('customer_id',$customer->id)->count();
            $activeOrders = DB::table('orders')->where('customer_id',$customer->id)->whereNotIn('status',['Completed'])->count();
            
            $orderLimit = $customer->tier === 'VIP' ? 5 : ($customer->tier === 'Corporate' ? 10 : 2);
            $leftOrders = max(0, $orderLimit - $activeOrders); 
            
            $latestOrder = DB::table('orders')->where('customer_id', $customer->id)->orderBy('id', 'desc')->first();
        @endphp
        
        <div x-data="{
                editing: false,
                showMeasurements: false,
                id: '{{ $customer->id }}',
                name: '{{ addslashes($customer->name) }}',
                mobile: '{{ $customer->mobile }}',
                tier: '{{ $customer->tier }}',
                email: '{{ $customer->email }}',
                gender: '{{ $customer->gender }}',
                address: '{{ addslashes($customer->address) }}'
            }" 
            x-show="(tierFilter === 'all' || tier === tierFilter) && (search === '' || name.toLowerCase().includes(search.toLowerCase()) || id.includes(search))" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="bg-white dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden hover:border-indigo-300 dark:hover:border-indigo-500/50 transition-colors duration-300">
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-5 gap-4">
                <div class="flex items-center gap-4">
                    <div class="font-mono font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-3.5 py-2 rounded-xl text-xs border border-indigo-100 dark:border-indigo-500/20 shadow-inner">
                        #{{ str_pad($customer->id,3,'0',STR_PAD_LEFT) }}
                    </div>
                    <div>
                        <div class="text-base font-semibold text-slate-800 dark:text-white tracking-tight" x-text="name"></div>
                        <div class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span x-text="mobile"></span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3 w-full sm:w-auto justify-end">
                    <span :class="tier === 'VIP' ? 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20' : (tier === 'Corporate' ? 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20' : 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-700/50 dark:text-slate-400 dark:border-slate-600')" class="px-3 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider border shadow-sm" x-text="tier"></span>
                    
                    <button @click="
                            expanded = (expanded === {{ $customer->id }}) ? null : {{ $customer->id }};
                            if (expanded === {{ $customer->id }}) {
                                setTimeout(() => {
                                    $el.closest('[x-data]').scrollIntoView({ behavior: 'smooth', block: 'start' });
                                }, 250);
                            }
                        " 
                        class="px-4 py-2 bg-slate-50 hover:bg-slate-100 dark:bg-slate-900/50 dark:hover:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-medium flex items-center gap-2 transition-all duration-200">
                        <span x-text="expanded === {{ $customer->id }} ? 'Close' : 'View Profile'"></span>
                        <svg class="w-4 h-4 transition-transform duration-300" :class="expanded === {{ $customer->id }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>
            </div>

            <div x-show="expanded === {{ $customer->id }}" x-cloak x-collapse class="px-5 pb-5 border-t border-slate-100 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-900/20">
                
                <div x-show="!editing" class="grid grid-cols-3 gap-0 my-6 p-1 bg-white dark:bg-slate-900 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="text-center py-4 px-2 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-widest font-semibold mb-1.5">Active</p>
                        <p class="text-xl font-bold text-slate-800 dark:text-white">{{ $activeOrders }}<span class="text-xs text-slate-400 font-medium"> / {{ $orderLimit }}</span></p>
                    </div>
                    <div class="text-center py-4 px-2 border-x border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-widest font-semibold mb-1.5">Available Slots</p>
                        <p class="text-xl font-bold {{ $leftOrders > 0 ? 'text-emerald-500 dark:text-emerald-400' : 'text-rose-500 dark:text-rose-400' }}">{{ $leftOrders }}</p>
                    </div>
                    <div class="text-center py-4 px-2 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-widest font-semibold mb-1.5">Total Orders</p>
                        <p class="text-xl font-bold text-indigo-600 dark:text-indigo-400">{{ $totalOrders }}</p>
                    </div>
                </div>

                <div x-show="editing" class="grid grid-cols-1 sm:grid-cols-2 gap-5 my-6">
                    <div>
                        <label class="block text-[11px] font-medium uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Email Address</label>
                        <input x-model="email" class="w-full p-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all dark:text-white" placeholder="Email">
                    </div>
                    <div>
                        <label class="block text-[11px] font-medium uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Mobile Number</label>
                        <input x-model="mobile" maxlength="10" oninput="let v = this.value.replace(/[^0-9]/g, ''); if(v.length > 0 && v[0] !== '0') { v = '0' + v; } this.value = v;" class="w-full p-3.5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl text-sm focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all dark:text-white" placeholder="07X XXX XXXX">
                    </div>
                </div>

                <div x-show="!editing" class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6 px-2">
                    <div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-widest font-semibold mb-1.5">Email</p>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300" x-text="email || 'Not Provided'"></p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-widest font-semibold mb-1.5">Gender</p>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300" x-text="gender"></p>
                    </div>
                    <div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-widest font-semibold mb-1.5">Address</p>
                        <p class="text-sm font-medium text-slate-700 dark:text-slate-300" x-text="address || 'Not Provided'"></p>
                    </div>
                </div>

                <div x-show="showMeasurements && !editing" x-collapse class="mb-6 bg-slate-50 dark:bg-slate-900/50 p-6 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <h4 class="text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-4">Latest Saved Measurements (Inches)</h4>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        @foreach(['collar','chest','shoulder','sleeve_length','full_length','armhole','upper_waist','lower_waist','hip','thigh','outseam','inseam','bottom_hem','knee'] as $m)
                        <div class="bg-white dark:bg-slate-800 p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm flex justify-between items-center">
                            <span class="text-[10px] text-slate-400 uppercase tracking-widest font-semibold">{{ str_replace('_', ' ', $m) }}</span>
                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">{{ ($latestOrder && $latestOrder->$m) ? $latestOrder->$m : '--' }}"</span>
                        </div>
                        @endforeach
                    </div>
                    @if(!$latestOrder)
                    <div class="mt-4 p-3 bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20 rounded-lg">
                        <p class="text-xs text-rose-600 dark:text-rose-400 font-medium">No previous orders found for this customer. Measurements are not available yet.</p>
                    </div>
                    @endif
                </div>

                <div class="flex items-center justify-end gap-3 pt-5 border-t border-slate-200 dark:border-slate-700">
                    
                    <button x-show="!editing" @click="showMeasurements = !showMeasurements" class="px-5 py-2.5 bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:hover:bg-indigo-500/20 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 rounded-xl text-xs font-semibold transition-colors shadow-sm">
                        <span x-text="showMeasurements ? 'Hide Measurements' : 'View Measurements'"></span>
                    </button>

                    <button x-show="!editing" @click="editing = true" class="px-5 py-2.5 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 rounded-xl text-xs font-semibold transition-colors shadow-sm">
                        Edit Details
                    </button>
                    
                    <form x-show="editing" action="{{ route('customers.update', $customer->id) }}" method="POST" class="inline" onsubmit="document.getElementById('loading-overlay').classList.remove('hidden');">
                        @csrf
                        <input type="hidden" name="email" x-model="email">
                        <input type="hidden" name="mobile" x-model="mobile">
                        <input type="hidden" name="name" x-model="name">
                        <input type="hidden" name="tier" x-model="tier">
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-semibold shadow-sm transition-colors">
                            Save Changes
                        </button>
                    </form>

                    <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" onsubmit="if(confirm('Are you sure you want to completely remove this client?')){ document.getElementById('loading-overlay').classList.remove('hidden'); return true; } return false;" class="inline">
                        @csrf 
                        @method('DELETE')
                        <button class="px-5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 dark:text-rose-400 rounded-xl text-xs font-semibold transition-colors">
                            Delete Client
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        @if(count($customers) === 0)
        <div class="py-16 text-center bg-white dark:bg-slate-800/80 rounded-3xl border border-dashed border-slate-300 dark:border-slate-600 shadow-sm">
            <div class="w-16 h-16 mx-auto mb-4 bg-slate-50 dark:bg-slate-700/50 rounded-full flex items-center justify-center text-slate-400 dark:text-slate-500">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">No Clients Found</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Your client directory is currently empty.</p>
        </div>
        @endif
    </div>
</div>

<div id="loading-overlay" class="fixed inset-0 z-[100] hidden bg-gray-900/80 backdrop-blur-sm flex items-center justify-center">
    <div class="flex flex-col items-center">
        <div class="w-20 h-20 bg-white rounded-full overflow-hidden flex items-center justify-center animate-scissor-cut mb-4 shadow-2xl border-4 border-white/20">
            <img src="/images/logo.jpeg" alt="Loading..." class="w-full h-full object-cover">
        </div>
        <h3 class="text-xl font-bold text-white mt-2">Please wait...</h3>
    </div>
</div>

<style>
    @keyframes scissor-cut { 0%,100% { transform: scale(1) rotate(0deg); } 50% { transform: scale(1.1) rotate(15deg); } }
    .animate-scissor-cut { animation: scissor-cut 1s ease-in-out infinite; }
</style>
@endsection
