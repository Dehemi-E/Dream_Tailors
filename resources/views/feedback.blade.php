<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="theme-color" content="#f8fafc">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <title>Provide Feedback · Dream Tailors</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            -webkit-tap-highlight-color: transparent;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        /* Premium Glass Effect */
        .glass-panel { 
            background: rgba(255, 255, 255, 0.6); 
            backdrop-filter: blur(20px); 
            -webkit-backdrop-filter: blur(20px); 
            border: 1px solid rgba(255, 255, 255, 0.7); 
            box-shadow: 0 8px 32px -8px rgba(0,0,0,0.08);
        }
        
        /* Animated Background Blobs */
        .animate-blob { animation: blob 8s infinite alternate; }
        @keyframes blob { 
            0% { transform: scale(1) translate(0, 0); } 
            100% { transform: scale(1.1) translate(20px, -20px); } 
        }

        /* Smooth tap highlight removal */
        * { -webkit-tap-highlight-color: transparent; }
        
        /* Better touch targets for mobile */
        button, a { 
            min-height: 44px; 
            min-width: 44px; 
            cursor: pointer;
        }

        /* Star button specific */
        .star-btn {
            min-height: unset;
            min-width: unset;
            padding: 4px;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-50 flex items-center justify-center p-4 sm:p-6 relative overflow-hidden">
    
    <!-- 🟢 Animated Colorful Background - Optimized for Mobile -->
    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none">
        <div class="absolute top-[-15%] left-[-15%] w-48 sm:w-72 md:w-96 h-48 sm:h-72 md:h-96 bg-purple-300 rounded-full mix-blend-multiply filter blur-3xl opacity-40 sm:opacity-50 animate-blob"></div>
        <div class="absolute bottom-[-15%] right-[-15%] w-48 sm:w-72 md:w-96 h-48 sm:h-72 md:h-96 bg-indigo-300 rounded-full mix-blend-multiply filter blur-3xl opacity-40 sm:opacity-50 animate-blob" style="animation-delay: 2s"></div>
        <div class="absolute top-[30%] right-[30%] w-32 sm:w-48 h-32 sm:h-48 bg-pink-300 rounded-full mix-blend-multiply filter blur-3xl opacity-30 sm:opacity-50 animate-blob" style="animation-delay: 4s"></div>
    </div>

    <div class="w-full max-w-md relative z-10 mx-auto" x-data="{ rating: 0, hoverRating: 0 }">
        
        <!-- 🟢 Main Card - Mobile Optimized -->
        <div class="glass-panel rounded-2xl sm:rounded-[2rem] p-5 sm:p-8 shadow-lg sm:shadow-xl">
            
            <!-- Header Section -->
            <div class="text-center mb-6 sm:mb-8">
                <!-- Logo -->
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-white rounded-xl sm:rounded-2xl mx-auto shadow-sm flex items-center justify-center mb-3 sm:mb-4 border border-white/60 overflow-hidden">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Dream Tailors" class="w-full h-full object-cover">
                </div>
                
                <h1 class="text-xl sm:text-2xl font-black text-slate-800 tracking-tight leading-tight">
                    How did we do?
                </h1>
                <p class="text-[10px] sm:text-xs font-bold text-slate-500 uppercase tracking-widest mt-2 px-2">
                    Order #{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }}
                </p>
                <p class="text-[11px] sm:text-sm font-semibold text-slate-600 mt-0.5 px-2">
                    {{ $order->garment_name }}
                </p>
            </div>

            <!-- 🟢 Success Message -->
            @if(session('success'))
                <div class="text-center py-6 sm:py-8 animate-[fadeIn_0.5s_ease-out]">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-emerald-100 rounded-full mx-auto flex items-center justify-center mb-3 sm:mb-4 text-emerald-600 shadow-sm border border-emerald-200">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <h2 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight">Thank You!</h2>
                    <p class="text-xs sm:text-sm font-medium text-slate-500 mt-2 px-4">Your feedback helps us deliver perfection.</p>
                </div>

            <!-- 🟢 Already Submitted -->
            @elseif($existing)
                <div class="text-center py-6 sm:py-8 animate-[fadeIn_0.5s_ease-out]">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-indigo-100 rounded-full mx-auto flex items-center justify-center mb-3 sm:mb-4 text-indigo-600 shadow-sm border border-indigo-200">
                        <svg class="w-7 h-7 sm:w-8 sm:h-8" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    <h2 class="text-lg sm:text-xl font-black text-slate-800 tracking-tight">Feedback Received</h2>
                    <p class="text-xs sm:text-sm font-medium text-slate-500 mt-2 px-4">You have already submitted feedback for this order.</p>
                </div>

            <!-- 🟢 Feedback Form -->
            @else
                <form action="{{ route('feedback.submit') }}" method="POST" class="space-y-5 sm:space-y-6">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="rating" :value="rating" required>

                    <!-- Interactive Star Rating -->
                    <div class="text-center">
                        <label class="block text-xs sm:text-sm font-bold text-slate-700 mb-3 sm:mb-4">
                            Rate the fit and quality
                        </label>
                        <div class="flex justify-center gap-1.5 sm:gap-2">
                            <template x-for="i in 5">
                                <button type="button" 
                                        @click="rating = i" 
                                        @mouseenter="hoverRating = i" 
                                        @mouseleave="hoverRating = 0"
                                        @touchend.prevent="rating = i"
                                        class="star-btn focus:outline-none transition-transform hover:scale-110 active:scale-95">
                                    <svg class="w-9 h-9 sm:w-10 sm:h-10 transition-colors duration-200 drop-shadow-sm" 
                                         :class="{'text-amber-400': hoverRating >= i || rating >= i, 'text-slate-300': hoverRating < i && rating < i}" 
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </button>
                            </template>
                        </div>
                        <!-- Rating text indicator -->
                        <p class="text-[10px] sm:text-xs font-semibold text-slate-400 mt-2" x-show="rating > 0" x-text="rating + ' out of 5 stars'"></p>
                    </div>

                    <!-- Optional Message -->
                    <div x-show="rating > 0" x-transition.opacity class="pt-1 sm:pt-2">
                        <label class="block text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-slate-500 mb-2 ml-1">
                            How can we improve? (Optional)
                        </label>
                        <textarea name="message" rows="3" 
                                  class="w-full bg-white/70 border border-white/80 rounded-xl p-3 sm:p-4 text-xs sm:text-sm font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500/50 outline-none resize-none shadow-inner placeholder-slate-400 transition-all" 
                                  placeholder="Tell us what you loved or what needs fixing..."></textarea>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            :disabled="rating === 0" 
                            :class="rating === 0 ? 'opacity-50 cursor-not-allowed' : 'hover:-translate-y-0.5 hover:shadow-xl shadow-indigo-500/30 active:scale-[0.98]'" 
                            class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-bold uppercase tracking-widest text-[10px] sm:text-xs py-3.5 sm:py-4 rounded-xl shadow-lg transition-all flex items-center justify-center gap-2">
                        <span>Submit Feedback</span>
                        <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            @endif
        </div>
        
        <!-- Footer -->
        <p class="text-center text-[9px] sm:text-[10px] font-bold text-slate-400 mt-4 sm:mt-6 uppercase tracking-widest">
            © {{ date('Y') }} Dream Tailors
        </p>
    </div>

    <!-- 🟢 Additional Mobile Optimizations -->
    <style>
        /* Prevent zoom on input focus for iOS */
        @supports (-webkit-touch-callout: none) {
            input, textarea, select {
                font-size: 16px !important;
            }
        }
        
        /* Custom scrollbar for textarea */
        textarea::-webkit-scrollbar {
            width: 4px;
        }
        textarea::-webkit-scrollbar-track {
            background: transparent;
        }
        textarea::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        /* Safe area padding for notched devices */
        @supports (padding: max(0px)) {
            body {
                padding-left: max(16px, env(safe-area-inset-left));
                padding-right: max(16px, env(safe-area-inset-right));
                padding-bottom: max(16px, env(safe-area-inset-bottom));
            }
        }

        /* S22 Ultra specific (1440px wide) */
        @media (min-width: 1440px) {
            .glass-panel {
                padding: 2.5rem 3rem;
            }
        }

        /* Very small devices */
        @media (max-width: 350px) {
            .glass-panel {
                padding: 1rem;
                border-radius: 1rem;
            }
            .star-btn svg {
                width: 2rem;
                height: 2rem;
            }
        }
    </style>
</body>
</html>