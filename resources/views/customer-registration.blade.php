@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto animate-[fadeIn_0.6s_ease-out]">
    <!-- 💖 Soft Glass Background Applied Here -->
    <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-[2rem] shadow-sm border border-slate-200 dark:border-slate-700/60 overflow-hidden transition-all duration-300">
        
        <!-- Header -->
        <div class="px-8 py-6 border-b border-slate-200 dark:border-slate-700/60 bg-slate-50/50 dark:bg-slate-800/50">
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">New Customer Profile</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Enter complete details to create a new tailoring profile.</p>
        </div>

        @if ($errors->any() || session('error'))
            <div class="mx-8 mt-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800/50 rounded-xl text-red-600 dark:text-red-400 text-sm font-medium shadow-sm">
                <ul class="list-disc list-inside space-y-1">
                    @if(session('error')) <li>{{ session('error') }}</li> @endif
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('customers.store') }}" method="POST" autocomplete="off" class="p-8 space-y-6" onsubmit="document.getElementById('loading-overlay').classList.remove('hidden'); document.getElementById('loading-text').innerText = document.getElementById('wa-checkbox').checked ? 'Sending WhatsApp message...' : 'Saving Profile...';">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Full Name -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Kasun Perera" autocomplete="off" oninput="this.value = this.value.replace(/[^a-zA-Z\s]/g, '')"
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 text-slate-900 dark:text-white p-3.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 outline-none transition-all shadow-sm">
                </div>

                <!-- Mobile Number -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Mobile Number <span class="text-red-500">*</span></label>
                    <div class="flex rounded-xl overflow-hidden border {{ $errors->has('mobile') ? 'border-red-500 focus-within:ring-red-500' : 'border-slate-200 dark:border-slate-700 focus-within:ring-2 focus-within:ring-primary-500/50 focus-within:border-primary-500' }} transition-all shadow-sm bg-slate-50/50 dark:bg-slate-900/50">
                        <span class="px-4 flex items-center text-sm font-bold text-slate-600 dark:text-slate-300 border-r {{ $errors->has('mobile') ? 'border-red-500' : 'border-slate-200 dark:border-slate-700' }} bg-slate-100/50 dark:bg-slate-800/50">
                            🇱🇰 +94
                        </span>
                        <input type="text" name="mobile" value="{{ old('mobile') }}" required placeholder="07X XXX XXXX" maxlength="10" autocomplete="off" oninput="let v = this.value.replace(/[^0-9]/g, ''); if(v.length > 0 && v[0] !== '0') { v = '0' + v; } this.value = v;"
                               class="flex-1 border-0 bg-transparent text-slate-900 dark:text-white p-3.5 text-sm font-medium outline-none">
                    </div>
                    
                    @if ($errors->has('mobile'))
                        <p class="text-red-500 text-xs mt-2 font-medium flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                            </svg>
                            {{ $errors->first('mobile') }}
                        </p>
                    @endif
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="example@gmail.com" autocomplete="off"
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 text-slate-900 dark:text-white p-3.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 outline-none transition-all shadow-sm">
                </div>

                <!-- Gender -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Gender <span class="text-red-500">*</span></label>
                    <select name="gender" required autocomplete="off"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 text-slate-900 dark:text-white p-3.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 outline-none transition-all shadow-sm cursor-pointer">
                        <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select Gender</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>

                <!-- Tier -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Customer Tier</label>
                    <select name="tier" autocomplete="off"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 text-slate-900 dark:text-white p-3.5 text-sm font-medium focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 outline-none transition-all shadow-sm cursor-pointer">
                        <option value="Standard" {{ old('tier', 'Standard') == 'Standard' ? 'selected' : '' }}>Standard Customer</option>
                        <option value="VIP" {{ old('tier') == 'VIP' ? 'selected' : '' }}>VIP (Priority Tailoring)</option>
                        <option value="Corporate" {{ old('tier') == 'Corporate' ? 'selected' : '' }}>Corporate Client</option>
                    </select>
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400 mb-2">Address</label>
                    <textarea name="address" rows="2" placeholder="House No, Street, City..." autocomplete="off"
                              class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50 text-slate-900 dark:text-white p-4 text-sm font-medium focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 outline-none resize-none transition-all shadow-sm custom-scrollbar">{{ old('address') }}</textarea>
                </div>

                <!-- 💖 WhatsApp Notification Checkbox (Fixed with SVG) -->
                <div class="md:col-span-2 border-t border-slate-200 dark:border-slate-700/60 pt-6">
                    <label class="flex items-center gap-3 cursor-pointer group w-max">
                        <input type="checkbox" name="send_whatsapp" id="wa-checkbox" value="1" checked class="w-5 h-5 rounded border-slate-300 dark:border-slate-600 text-primary-600 focus:ring-primary-500 cursor-pointer">
                        <div class="flex items-center gap-2">
                            <!-- SVG Original WhatsApp Logo -->
                            <svg class="w-6 h-6 text-emerald-500 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 0 0-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/>
                            </svg>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-300">Send WhatsApp Message with Login Details</span>
                        </div>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-700/60">
                <button type="reset" class="px-6 py-3 rounded-xl border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-all font-bold text-xs uppercase tracking-wider focus:outline-none">Clear Form</button>
                <button type="submit" class="px-8 py-3 bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-all font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-lg hover:-translate-y-0.5 focus:outline-none flex items-center gap-2">
                    Save Profile
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="loading-overlay" class="fixed inset-0 z-[100] hidden bg-slate-900/80 backdrop-blur-md flex items-center justify-center">
    <div class="flex flex-col items-center">
        <div class="w-20 h-20 bg-white rounded-2xl overflow-hidden flex items-center justify-center animate-scissor-cut mb-4 shadow-2xl border-4 border-white/20">
            <img src="/images/logo.jpeg" alt="Loading..." class="w-full h-full object-cover">
        </div>
        <!-- 💖 Dynamic Loading Text -->
        <h3 id="loading-text" class="text-xl font-bold text-white tracking-tight drop-shadow-md">Please wait...</h3>
    </div>
</div>

<style>
    @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes scissor-cut { 0%,100% { transform: scale(1) rotate(0deg); } 50% { transform: scale(1.1) rotate(15deg); } }
    .animate-scissor-cut { animation: scissor-cut 1s ease-in-out infinite; }
    
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
</style>
@endsection