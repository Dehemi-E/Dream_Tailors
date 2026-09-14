@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    [x-cloak] { display: none !important; }
    @keyframes scissor-cut {
        0%, 100% { transform: scale(1) rotate(0deg); }
        50% { transform: scale(1.1) rotate(15deg); }
    }
    .animate-scissor-cut { animation: scissor-cut 1s ease-in-out infinite; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
</style>

<div x-data="{ 
    newPaymentModal: false, 
    enteredCustId: '', 
    searchQuery: '', 
    customersList: {{ Js::from($customers) }}, 
    readyOrdersList: {{ Js::from($readyOrders) }},
    invoiceModal: false,
    selectedPayment: null,
    
    get selectedCustomerObj() {
        return this.customersList.find(c => c.id == this.enteredCustId) || null;
    },

    viewInvoice(payment) {
        this.selectedPayment = payment;
        this.invoiceModal = true;
    },

    printInvoice() {
        const printContents = document.getElementById('invoice-print').innerHTML;
        const printWindow = window.open('', '_blank', 'width=800,height=900');
        
        if (printWindow) {
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Print Invoice</title>
                    <script src=\'https://cdn.tailwindcss.com\'><\/script>
                    <style>
                        @page { size: A4; margin: 10mm; } 
                        body { background-color: white !important; color: black !important; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
                        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                        #invoice-container { max-width: 210mm; margin: 0 auto; }
                    </style>
                </head>
                <body class='bg-white'>
                    <div id='invoice-container' class='p-8'>
                        ${printContents}
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
            
            setTimeout(() => {
                printWindow.focus();
                printWindow.print();
                printWindow.close();
            }, 800);
        } else {
            alert('Please allow popups in your browser to print the invoice.');
        }
    }
}" class="space-y-8 max-w-7xl mx-auto animate-[fadeIn_0.6s_ease-out]">
    
    <div class="bg-white dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 p-6 no-print transition-all">
        <div class="flex flex-col md:flex-row justify-between items-center gap-5">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-lg border border-slate-100 dark:border-slate-700 overflow-hidden">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Dream Tailors Logo" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">DREAM TAILORS</h1>
                    <p class="text-[11px] font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mt-0.5">Professional Tailoring & Payments</p>
                </div>
            </div>
            <button @click="newPaymentModal = true" 
                    class="w-full md:w-auto bg-indigo-600 text-white hover:bg-indigo-700 px-6 py-3.5 rounded-xl text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2 transition-all shadow-md hover:shadow-lg hover:-translate-y-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                New Payment
            </button>
        </div>
    </div>

    <!-- Payment History Section -->
    <div class="bg-slate-50 dark:bg-slate-900/30 rounded-2xl p-6 border border-slate-200 dark:border-slate-700/60 no-print">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-6">
            <h2 class="text-lg font-bold text-slate-800 dark:text-white tracking-tight">Customer Payment Records</h2>
            
            <div class="relative w-full sm:w-80">
                <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" x-model="searchQuery" placeholder="Search by ID, Name or Invoice..." 
                       class="w-full pl-10 pr-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-800 dark:text-white focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all shadow-sm">
            </div>
        </div>
        
        <div class="space-y-4">
            @php
                $groupedPayments = collect($payments)->filter(function($payment) {
                    return !empty($payment->customer_id); 
                })->groupBy('customer_id');
            @endphp

            @forelse($groupedPayments as $cId => $bills)
                @php
                    $matchedCustomer = collect($customers)->firstWhere('id', $cId);
                    $finalName = $matchedCustomer ? $matchedCustomer->name : 'Unknown Customer';
                    $formattedId = '#' . str_pad($cId, 3, '0', STR_PAD_LEFT);
                    $sortedBills = collect($bills)->sortByDesc('id')->values();
                    $latestBill = $sortedBills->first();
                    $historyBills = $sortedBills->skip(1);
                @endphp
                
                <div x-data="{
                        expanded: false,
                        cId: '{{ $cId }}',
                        cName: '{{ strtolower(addslashes($finalName)) }}',
                        invoices: '{{ strtolower(addslashes(collect($bills)->pluck('invoice_no')->implode(' '))) }}',
                        checkMatch(query) {
                            if (!query || query.trim() === '') return true;
                            let q = query.trim().toLowerCase();
                            let myId = String(this.cId);
                            let myFormattedId = '#' + myId.padStart(3, '0');
                            if (myId === q || myId === q.replace(/^0+/, '')) return true;
                            if (myFormattedId.includes(q)) return true;
                            if (('cust-' + myId).includes(q) || ('cust ' + myId).includes(q)) return true;
                            if (this.cName.includes(q)) return true;
                            if (this.invoices.includes(q)) return true;
                            return false;
                        }
                    }"
                    x-show="checkMatch(searchQuery)"
                    class="bg-white dark:bg-slate-800/80 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden transition-all hover:border-indigo-300 dark:hover:border-indigo-500/50"
                >
                    <div class="px-5 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50 dark:bg-slate-900/20">
                        <div class="flex items-center gap-4">
                            <div class="font-mono font-bold text-indigo-700 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-500/10 px-3.5 py-2 rounded-xl text-xs border border-indigo-100 dark:border-indigo-500/20 shadow-inner">
                                {{ $formattedId }}
                            </div>
                            <div>
                                <div class="text-base font-bold text-slate-800 dark:text-white tracking-tight">{{ $finalName }}</div>
                                <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5">{{ count($bills) }} Payment(s)</div>
                            </div>
                        </div>
                        
                        <button @click="expanded = !expanded" class="w-full sm:w-auto px-4 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-300 rounded-xl text-xs font-bold flex justify-center items-center gap-2 transition-all hover:bg-slate-50 dark:hover:bg-slate-700 shadow-sm focus:outline-none">
                            <span x-text="expanded ? 'Hide Bills' : 'View Bills'"></span>
                            <svg class="w-4 h-4 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>

                    <!-- Latest Bill -->
                    <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                        <div class="flex items-center gap-6 w-full md:w-auto">
                            <div class="w-32 shrink-0">
                                <span class="block font-mono font-bold text-slate-700 dark:text-slate-300 text-sm">{{ $latestBill->invoice_no ?? 'N/A' }}</span>
                                <span class="inline-block mt-1 text-[9px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 px-2 py-0.5 rounded shadow-sm uppercase tracking-widest">Latest</span>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $latestBill->garment_name ?? 'Custom Tailoring Order' }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">{{ $latestBill->payment_date ?? '' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between md:justify-end w-full md:w-auto gap-6">
                            <div class="text-right">
                                <!-- 🟢 FIXED: Shows Net Paid Amount -->
                                <p class="font-bold text-indigo-600 dark:text-indigo-400 text-lg">Rs. {{ number_format(($latestBill->amount ?? 0) - ($latestBill->discount ?? 0), 2) }}</p>
                                @if(($latestBill->discount ?? 0) > 0)
                                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">(Discount: -Rs. {{ number_format($latestBill->discount ?? 0, 2) }})</span>
                                @endif
                                <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">{{ $latestBill->method ?? '' }}</span>
                            </div>
                            <div class="flex gap-2">
                                <button @click='viewInvoice({{ json_encode($latestBill) }})' class="px-4 py-2 rounded-xl text-xs font-bold bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-500/10 dark:hover:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400 transition-colors border border-indigo-100 dark:border-indigo-500/20 shadow-sm">Invoice</button>
                                <form action="{{ route('payments.destroy', $latestBill->id ?? 0) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this payment?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-2 rounded-xl text-xs font-bold bg-rose-50 hover:bg-rose-100 dark:bg-rose-500/10 dark:hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 transition-colors border border-rose-200 dark:border-rose-500/20 shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    @if(count($historyBills) > 0)
                    <div class="bg-slate-50/80 dark:bg-slate-900/40 border-t border-slate-100 dark:border-slate-700/50">
                        <button @click="expanded = !expanded" class="w-full py-3 flex items-center justify-center gap-2 text-[11px] font-bold text-indigo-500 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 hover:bg-slate-100 dark:hover:bg-slate-900/60 transition-all uppercase tracking-widest focus:outline-none">
                            <span x-text="expanded ? 'Hide Past Invoices' : 'View {{ count($historyBills) }} Past Invoices'"></span>
                            <svg class="w-4 h-4 transition-transform duration-300" :class="expanded ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    
                    <div x-show="expanded" x-collapse x-cloak class="border-t border-slate-200 dark:border-slate-700">
                        @foreach($historyBills as $payment)
                        <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-slate-50/50 dark:bg-slate-900/20 border-b border-slate-100 dark:border-slate-700/50 last:border-0 hover:bg-slate-100 dark:hover:bg-slate-800/80 transition-colors">
                            <div class="flex items-center gap-6 w-full md:w-auto">
                                <div class="w-32 shrink-0">
                                    <span class="block font-mono font-bold text-slate-500 dark:text-slate-400 text-xs">{{ $payment->invoice_no ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">{{ $payment->garment_name ?? 'Custom Tailoring Order' }}</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $payment->payment_date ?? '' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between md:justify-end w-full md:w-auto gap-6">
                                <div class="text-right">
                                    <!-- 🟢 FIXED: Shows Net Paid Amount -->
                                    <p class="font-bold text-slate-600 dark:text-slate-300 text-sm">Rs. {{ number_format(($payment->amount ?? 0) - ($payment->discount ?? 0), 2) }}</p>
                                    @if(($payment->discount ?? 0) > 0)
                                        <span class="text-[9px] text-emerald-500 font-bold">(Disc: -Rs. {{ number_format($payment->discount ?? 0, 2) }})</span>
                                    @endif
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-400">{{ $payment->method ?? '' }}</span>
                                </div>
                                <div class="flex gap-2">
                                    <button @click='viewInvoice({{ json_encode($payment) }})' class="px-3 py-1.5 rounded-lg text-xs font-bold bg-white hover:bg-slate-50 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 transition-colors border border-slate-200 dark:border-slate-600 shadow-sm">Invoice</button>
                                    <form action="{{ route('payments.destroy', $payment->id ?? 0) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this payment?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="px-2 py-1.5 rounded-lg text-xs font-bold bg-rose-50/50 hover:bg-rose-100 dark:bg-rose-500/5 dark:hover:bg-rose-500/10 text-rose-500 dark:text-rose-400 transition-colors border border-rose-100 dark:border-rose-500/10 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            @empty
                <div class="p-16 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700/60 shadow-sm mt-4">
                    <div class="w-16 h-16 mx-auto bg-slate-100 dark:bg-slate-700/50 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white">No Payments Yet</h3>
                    <p class="text-slate-500 text-sm mt-1">Transactions will appear here once processed.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Chart Section -->
    <div class="no-print">
        <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-5 flex items-center gap-2 tracking-tight">
            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
            Top 20 Premium Clients (Revenue Analysis)
        </h2>
        <div class="bg-white dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700/60 p-6 h-[400px]">
            <canvas id="engagementChart"></canvas>
        </div>
    </div>

    <!-- 🟢 New Payment Modal -->
    <template x-teleport="body">
        <div x-show="newPaymentModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-md no-print" x-cloak 
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
             
            <div class="bg-white dark:bg-slate-800 p-6 lg:p-8 rounded-3xl w-full max-w-xl shadow-2xl border border-slate-200 dark:border-slate-700 relative mx-4" @click.away="newPaymentModal = false"
                 x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                
                <button @click="newPaymentModal = false" class="absolute top-5 right-5 text-slate-400 hover:text-slate-600 dark:hover:text-white bg-slate-100 dark:bg-slate-700 p-2.5 rounded-full transition-all shadow-sm border border-slate-200 dark:border-slate-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>

                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl border border-indigo-100 dark:border-indigo-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">Process Payment</h3>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-widest mt-0.5">Generate a new invoice</p>
                    </div>
                </div>

                <form action="{{ route('payments.store') }}" method="POST" class="space-y-5" onsubmit="document.getElementById('loading-overlay').classList.remove('hidden');">
                    @csrf
                    <input type="hidden" name="customer_id" :value="enteredCustId">
                    
                    <div>
                        <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Search Customer <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" x-model="enteredCustId" 
                                   oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/^0+/, '')" 
                                   placeholder="Enter Customer ID"
                                   class="w-full pl-11 pr-4 py-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-medium text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" required>
                        </div>
                    </div>
                    
                    <div x-show="selectedCustomerObj" class="p-4 bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-200 dark:border-indigo-500/20 rounded-xl" style="display:none;">
                        <p class="font-bold text-indigo-700 dark:text-indigo-400 text-sm flex items-center justify-between">
                            <span x-text="selectedCustomerObj ? selectedCustomerObj.name : ''"></span>
                            <span class="text-xs font-medium bg-white dark:bg-slate-800 px-2.5 py-1 rounded-lg border border-indigo-100 dark:border-indigo-500/20" x-text="selectedCustomerObj ? selectedCustomerObj.mobile : ''"></span>
                        </p>
                    </div>
                    
                    <div x-show="selectedCustomerObj" style="display:none;" class="space-y-5 border-t border-slate-100 dark:border-slate-700/60 pt-5 mt-5">
                        <div>
                            <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Select Order <span class="text-rose-500">*</span></label>
                            <select name="order_id" class="w-full p-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-medium text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all cursor-pointer" required>
                                <option value="">-- Select Ready Order --</option>
                                <template x-for="order in readyOrdersList.filter(o => o.customer_id == enteredCustId)" :key="order.id">
                                    <option :value="order.id" x-text="'ORD-' + String(order.id).padStart(3, '0') + ' | ' + (order.garment_name || order.garment_category)"></option>
                                </template>
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Payment Date <span class="text-rose-500">*</span></label>
                                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" 
                                       class="w-full p-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-medium text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all" required>
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Payment Method <span class="text-rose-500">*</span></label>
                                <select name="method" class="w-full p-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-medium text-sm text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all cursor-pointer" required>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- 🟢 Amount and Discount Fields -->
                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Total Amount (Bill) <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-slate-400">Rs.</span>
                                    <input type="text" name="amount" 
                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^0+/, '').replace(/^\./, '0.')" 
                                           placeholder="0.00" required
                                           class="w-full pl-12 pr-4 py-3.5 bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl font-bold text-lg text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-all">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Discount (Optional)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-sm font-bold text-emerald-500">Rs.</span>
                                    <input type="text" name="discount" 
                                           oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/^0+/, '').replace(/^\./, '0.')" 
                                           placeholder="0.00" 
                                           class="w-full pl-12 pr-4 py-3.5 bg-emerald-50 dark:bg-emerald-500/5 border border-emerald-200 dark:border-emerald-700/50 rounded-xl font-bold text-lg text-emerald-700 dark:text-emerald-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 transition-all">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="w-full bg-indigo-600 text-white py-4 rounded-xl font-bold uppercase tracking-widest hover:bg-indigo-700 hover:shadow-lg hover:-translate-y-0.5 transition-all text-xs mt-2">
                            Complete Transaction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- 🟢 FIXED Invoice Modal - DISCOUNT SUBTRACTS PROPERLY -->
    <template x-teleport="body">
        <div x-show="invoiceModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/80 backdrop-blur-md no-print" x-cloak
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
             
            <div class="bg-white rounded-xl w-full max-w-3xl shadow-2xl mx-4 flex flex-col my-8 max-h-[90vh] overflow-hidden" @click.away="invoiceModal = false"
                 x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
                
                <div id="invoice-print" class="px-10 py-8 flex-1 overflow-y-auto bg-white text-black relative">
                    
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.03]">
                        <span class="text-9xl font-black uppercase tracking-widest transform -rotate-45">PAID</span>
                    </div>

                    <div class="flex justify-between items-start border-b-2 border-slate-900 pb-6 mb-6 relative z-10">
                        <div class="flex items-center gap-5">
                            <img src="{{ asset('images/logo.jpeg') }}" alt="Dream Tailors Logo" class="w-20 h-20 object-cover rounded-lg border border-slate-200 shadow-sm">
                            <div>
                                <h1 class="text-3xl font-black text-slate-900 tracking-tighter uppercase">Dream Tailors</h1>
                                <p class="text-xs text-slate-600 font-bold mt-1 uppercase tracking-widest">Professional Tailoring Services</p>
                                <p class="text-xs text-slate-500 mt-1">Kurunegala, Sri Lanka</p>
                                <p class="text-xs text-slate-500">Tel: +94 7X XXX XXXX</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <h2 class="text-4xl font-black text-slate-300 uppercase tracking-widest">Invoice</h2>
                            <div class="mt-4">
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Invoice Number</p>
                                <p class="text-lg font-black text-slate-900" x-text="selectedPayment ? selectedPayment.invoice_no : ''"></p>
                            </div>
                            <div class="mt-2">
                                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Date of Issue</p>
                                <p class="text-sm font-bold text-slate-900" x-text="selectedPayment ? selectedPayment.payment_date : ''"></p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-6 mb-6 relative z-10">
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100">
                            <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest mb-1">Billed To:</p>
                            <p class="font-black text-slate-900 text-xl uppercase" x-text="selectedPayment ? selectedPayment.customer_name : ''"></p>
                        </div>
                        <div class="bg-slate-50 p-4 rounded-xl border border-slate-100 text-right flex flex-col justify-center items-end">
                            <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest mb-1">Payment Method:</p>
                            <div class="inline-flex items-center justify-center px-4 py-1.5 border-2 border-slate-800 rounded-lg">
                                <span class="font-black text-slate-900 text-base uppercase tracking-wider" x-text="selectedPayment ? selectedPayment.method : ''"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6 relative z-10">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b-2 border-slate-800">
                                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-slate-700 bg-slate-100 rounded-tl-lg">Item Description</th>
                                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-slate-700 bg-slate-100 w-24 text-center">Qty</th>
                                    <th class="py-2 px-4 text-[10px] font-bold uppercase tracking-widest text-slate-700 bg-slate-100 text-right rounded-tr-lg">Amount (LKR)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="border-b border-slate-200">
                                    <td class="py-5 px-4">
                                        <p class="font-black text-slate-900 text-2xl uppercase tracking-tight leading-none" x-text="selectedPayment && selectedPayment.garment_name ? selectedPayment.garment_name : 'Custom Tailoring Order'"></p>
                                        <p class="text-sm text-slate-500 mt-2 font-semibold bg-slate-100 inline-block px-3 py-1 rounded" x-text="selectedPayment && selectedPayment.order_id ? 'Order Ref: #ORD-' + String(selectedPayment.order_id).padStart(3, '0') : ''"></p>
                                    </td>
                                    <td class="py-5 px-4 text-center font-bold text-slate-700 text-lg">1</td>
                                    {{-- 🟢 FIXED: Shows strictly Original Gross Amount --}}
                                    <td class="py-5 px-4 text-right font-black text-slate-900 text-xl" 
                                        x-text="selectedPayment ? parseFloat(selectedPayment.amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) : ''">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 🟢 FIXED Totals Section - Discount SUBTRACTS from Subtotal -->
                    <div class="flex justify-end mb-8 relative z-10">
                        <div class="w-1/2">
                            {{-- Subtotal = Original Amount --}}
                            <div class="flex justify-between items-center py-2 border-b border-slate-200">
                                <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Subtotal</span>
                                <span class="font-bold text-slate-900 text-base" 
                                      x-text="selectedPayment ? 'Rs. ' + parseFloat(selectedPayment.amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) : ''">
                                </span>
                            </div>
                            
                            {{-- 🟢 Discount Row - SUBTRACTS (shows only if discount > 0) --}}
                            <div x-show="selectedPayment && parseFloat(selectedPayment.discount || 0) > 0" 
                                 class="flex justify-between items-center py-2 border-b border-slate-200 text-emerald-600">
                                <span class="text-xs font-bold uppercase tracking-wider">Discount Given</span>
                                <span class="font-bold text-base" 
                                      x-text="selectedPayment ? '- Rs. ' + parseFloat(selectedPayment.discount || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) : ''">
                                </span>
                            </div>

                            {{-- 🟢 Total Paid = Original Amount - Discount --}}
                            <div class="flex justify-between items-center py-4 mt-4 bg-slate-900 px-6 rounded-xl shadow-md">
                                <span class="text-base font-black text-white uppercase tracking-widest">Total Paid</span>
                                <span class="font-black text-2xl text-white" 
                                      x-text="selectedPayment ? 'Rs. ' + (parseFloat(selectedPayment.amount || 0) - parseFloat(selectedPayment.discount || 0)).toLocaleString('en-US', {minimumFractionDigits: 2}) : ''">
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-end pt-6 border-t border-slate-300 relative z-10">
                        <div>
                            <p class="text-sm font-black text-slate-900">Thank you for choosing Dream Tailors!</p>
                            <p class="text-[11px] font-medium text-slate-500 mt-1 mb-4">Scan the QR code below to share your feedback with us.</p>
                            <div class="flex items-center gap-4">
                                <div class="p-1 border-2 border-slate-200 rounded-lg bg-white">
                                    <!-- 🟢 FIXED: Ngrok URL with proper URL encoding for QR generation -->
                                    <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + encodeURIComponent('https://doing-childhood-doable.ngrok-free.dev/feedback/' + (selectedPayment ? selectedPayment.order_id : ''))" class="w-20 h-20" alt="Feedback QR">
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-900">Scan to Feedback</p>
                                    <p class="text-[10px] font-bold text-slate-500 mt-0.5">Tell us how we did!</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-center w-56">
                            <div class="border-b-2 border-slate-400 h-10 mb-2"></div>
                            <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Authorized Signature</p>
                        </div>
                    </div>
                </div>

                <div class="px-8 py-4 flex justify-end gap-4 no-print border-t border-slate-200 bg-slate-50">
                    <button @click="invoiceModal = false" class="px-6 py-2.5 rounded-lg border border-slate-300 text-slate-700 font-bold text-xs uppercase tracking-wider hover:bg-slate-200 transition-colors">Close</button>
                    <button @click="printInvoice()" class="px-6 py-2.5 bg-slate-900 text-white rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-black transition-all flex items-center gap-2 shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        Print Invoice
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<div id="loading-overlay" class="fixed inset-0 z-[110] hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center no-print">
    <div class="flex flex-col items-center">
        <div class="w-20 h-20 bg-white rounded-full overflow-hidden flex items-center justify-center animate-scissor-cut mb-4 shadow-2xl border-4 border-white/20">
            <img src="{{ asset('images/logo.jpeg') }}" alt="Loading..." class="w-full h-full object-cover">
        </div>
        <h3 class="text-xl font-bold text-white tracking-tight drop-shadow-md">Processing Payment...</h3>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentsData = {{ Js::from($payments) }};
        const customersData = {{ Js::from($customers) }};
        
        const customerTotals = {};
        const customerLabels = {};
        
        paymentsData.forEach(p => {
            if (!p.customer_id) return;
            
            const cId = p.customer_id;
            const matchedCustomer = customersData.find(c => c.id == cId);
            const name = matchedCustomer ? matchedCustomer.name : (p.customer_name || 'Unknown');
            
            const label = name + ' (#' + String(cId).padStart(3, '0') + ')';
            
            // 🟢 FIXED: Chart uses Net Paid Amount (Amount - Discount)
            customerTotals[cId] = (customerTotals[cId] || 0) + (parseFloat(p.amount || 0) - parseFloat(p.discount || 0));
            customerLabels[cId] = label;
        });

        const sortedIds = Object.keys(customerTotals).sort((a, b) => customerTotals[b] - customerTotals[a]);
        const top20Ids = sortedIds.slice(0, 20);

        const top20Labels = top20Ids.map(id => customerLabels[id]);
        const top20Data = top20Ids.map(id => customerTotals[id]);

        const ctx = document.getElementById('engagementChart')?.getContext('2d');
        if (!ctx) return;
        
        let chart;
        const isDark = document.documentElement.classList.contains('dark');

        const observer = new IntersectionObserver((entries) => {
            if (entries[0].isIntersecting && !chart) {
                chart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: top20Labels, 
                        datasets: [{
                            label: 'Total Revenue (Rs)',
                            data: top20Data, 
                            backgroundColor: isDark ? '#818cf8' : '#6366f1',
                            hoverBackgroundColor: isDark ? '#a5b4fc' : '#4f46e5',
                            borderRadius: 6,
                            barThickness: 'flex',
                            maxBarThickness: 40
                        }]
                    },
                    options: { 
                        responsive: true, 
                        maintainAspectRatio: false,
                        animation: { duration: 2000, easing: 'easeOutQuart' },
                        scales: {
                            y: { beginAtZero: true, grid: { color: isDark ? 'rgba(71, 85, 105, 0.3)' : 'rgba(226, 232, 240, 0.8)', drawBorder:false }, ticks: { font: { weight: '600', family: "'Inter', sans-serif" }, color: isDark ? '#94a3b8' : '#64748b', padding: 10 } },
                            x: { grid: { display: false }, ticks: { font: { weight: '600', family: "'Inter', sans-serif" }, color: isDark ? '#94a3b8' : '#64748b', padding: 10 } }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: { backgroundColor: isDark ? '#1e293b' : '#334155', titleFont: { family: "'Inter', sans-serif" }, bodyFont: { family: "'Inter', sans-serif" }, padding: 12, cornerRadius: 8, displayColors: false, callbacks: { label: ctx => 'Rs. ' + ctx.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2}) } }
                        }
                    }
                });
            }
        }, { threshold: 0.2 });

        const chartEl = document.getElementById('engagementChart');
        if (chartEl) observer.observe(chartEl);
        
        window.addEventListener('darkModeToggled', () => {
            if (chart) {
                const isDarkNow = document.documentElement.classList.contains('dark');
                chart.data.datasets[0].backgroundColor = isDarkNow ? '#818cf8' : '#6366f1';
                chart.data.datasets[0].hoverBackgroundColor = isDarkNow ? '#a5b4fc' : '#4f46e5';
                chart.options.scales.y.grid.color = isDarkNow ? 'rgba(71, 85, 105, 0.3)' : 'rgba(226, 232, 240, 0.8)';
                chart.options.scales.y.ticks.color = isDarkNow ? '#94a3b8' : '#64748b';
                chart.options.scales.x.ticks.color = isDarkNow ? '#94a3b8' : '#64748b';
                chart.options.plugins.tooltip.backgroundColor = isDarkNow ? '#1e293b' : '#334155';
                chart.update();
            }
        });
    });
</script>
@endsection