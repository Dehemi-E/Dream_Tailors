<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dream Tailors</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc',
                            400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca',
                            800: '#3730a3', 900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Scripts -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
        :root {
            --chart-line: #4f46e5;
            --chart-fill: rgba(79, 70, 229, 0.1);
            --chart-grid: #e5e7eb;
            --chart-text: #374151;
            --chart-legend: #6b7280;
        }
        .dark {
            --chart-line: #818cf8;
            --chart-fill: rgba(129, 140, 248, 0.2);
            --chart-grid: #374151;
            --chart-text: #e5e7eb;
            --chart-legend: #9ca3af;
        }
        @keyframes blink-pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.1); }
        }
        .animate-blink { animation: blink-pulse 1.5s ease-in-out infinite; text-shadow: 0 0 10px rgba(99, 102, 241, 0.8); }
        
        @keyframes bell-ring {
            0%, 100% { transform: rotate(0deg); }
            10%, 30%, 50%, 70%, 90% { transform: rotate(15deg); }
            20%, 40%, 60%, 80% { transform: rotate(-15deg); }
        }
        .animate-bell {
            animation: bell-ring 1.5s ease-in-out infinite;
            transform-origin: top center;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
    </style>
</head>

<body x-data="{ 
        hover: false,
        notificationOpen: false,
        activeTab: 'measurements', 
        chatPanelOpen: false,
        activeChatOrderId: null,
        chatList: [],
        groupedChats: [],
        expandedCustomer: null, 
        chatMessages: [],
        newAdminMessage: '',
        newAdminImage: null,
        unreadChatCount: 0,
        darkMode: localStorage.getItem('darkMode') === 'true',
        
        init() {
            document.documentElement.classList.toggle('dark', this.darkMode);
            try { this.fetchUnreadChats(); } catch(e) {}
            
            setInterval(() => {
                try {
                    this.fetchUnreadChats();
                    if(this.chatPanelOpen && this.activeChatOrderId) this.loadChatMessages(false);
                    this.pollDashboardData(); // Real-time update function added
                } catch(e) {}
            }, 5000);
        },
        toggleDark() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('darkMode', this.darkMode);
            document.documentElement.classList.toggle('dark', this.darkMode);
            window.dispatchEvent(new CustomEvent('darkModeToggled'));
        },
        fetchUnreadChats() {
            fetch('/api/messages/unread/admin')
                .then(r => r.ok ? r.json() : null)
                .then(data => { if(data) this.unreadChatCount = data.count || 0; })
                .catch(e => console.warn('Chat check error'));
        },
        
        // Real-time Update function for Notifications
        pollDashboardData() {
            fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.text())
            .then(html => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                
                const newNotif = doc.getElementById('notifications-container');
                const oldNotif = document.getElementById('notifications-container');
                if(newNotif && oldNotif) oldNotif.innerHTML = newNotif.innerHTML;
                
                ['total-notification-badge', 'notification-bell-icon', 'total-notification-text', 'tab-req-count', 'feedback-count'].forEach(id => {
                    const newEl = doc.getElementById(id);
                    const oldEl = document.getElementById(id);
                    if(newEl && oldEl) oldEl.outerHTML = newEl.outerHTML;
                });
            }).catch(err => console.warn('Polling skipped'));
        },

        openChatPanel() {
            this.chatPanelOpen = true;
            this.activeChatOrderId = null;
            this.loadChatList();
        },
        loadChatList() {
            fetch('/api/messages/admin-chats')
                .then(r => r.ok ? r.json() : [])
                .then(data => { 
                    this.chatList = data || []; 
                    
                    let grouped = {};
                    this.chatList.forEach(chat => {
                        if(!grouped[chat.customer_id]) {
                            grouped[chat.customer_id] = {
                                customer_name: chat.customer_name,
                                customer_id: chat.customer_id,
                                total_unread: 0,
                                chats: []
                            };
                        }
                        grouped[chat.customer_id].chats.push(chat);
                        grouped[chat.customer_id].total_unread += (chat.unread_count || 0);
                    });
                    this.groupedChats = Object.values(grouped);
                })
                .catch(e => console.warn('Chat list error'));
        },
        openSingleChat(orderId) {
            this.activeChatOrderId = orderId;
            this.loadChatMessages(true);
        },
        loadChatMessages(forceScroll) {
            if(!this.activeChatOrderId) return;
            fetch('/api/messages/' + this.activeChatOrderId)
                .then(r => r.ok ? r.json() : [])
                .then(data => {
                    let oldLen = this.chatMessages.length;
                    this.chatMessages = data || [];
                    if(forceScroll || data.length > oldLen) setTimeout(() => this.scrollToBottom(), 100);
                    this.markAsRead();
                })
                .catch(e => console.warn('Messages load error'));
        },
        markAsRead() {
            fetch('/api/messages/read', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({order_id: this.activeChatOrderId, reader_type: 'admin'})
            }).then(() => { this.fetchUnreadChats(); this.loadChatList(); })
              .catch(e => console.warn('Mark read error'));
        },
        markFeedbackRead(id) {
            fetch('/admin/feedback/read/' + id, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
            }).then(() => {
                let el = document.getElementById('feedback-' + id);
                if(el) el.style.display = 'none';
                
                let countEl = document.getElementById('feedback-count');
                if(countEl) countEl.innerText = Math.max(0, parseInt(countEl.innerText) - 1);
                
                let totalBadge = document.getElementById('total-notification-badge');
                let totalText = document.getElementById('total-notification-text');
                let bellIcon = document.getElementById('notification-bell-icon');
                
                if(totalText) {
                    let currentTotal = parseInt(totalText.innerText);
                    let newTotal = currentTotal - 1;
                    
                    if(newTotal <= 0) {
                        if(totalBadge) totalBadge.style.display = 'none';
                        totalText.innerText = '0 New';
                        totalText.classList.remove('text-red-500', 'font-bold');
                        
                        if(bellIcon) {
                            bellIcon.classList.remove('animate-bell', 'text-red-500');
                        }
                    } else {
                        totalText.innerText = newTotal + ' New';
                    }
                }
            }).catch(e => console.warn('Feedback update error'));
        },
        sendAdminMessage() {
            if(!this.newAdminMessage && !this.$refs.adminChatImage.files[0]) return;
            let chatInfo = this.chatList.find(c => c.order_id === this.activeChatOrderId);
            if(!chatInfo) return;

            let formData = new FormData();
            formData.append('order_id', this.activeChatOrderId);
            formData.append('customer_id', chatInfo.customer_id);
            formData.append('sender', 'admin');
            formData.append('message', this.newAdminMessage);
            if(this.$refs.adminChatImage.files[0]) formData.append('image', this.$refs.adminChatImage.files[0]);

            fetch('/api/messages', {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: formData
            }).then(r => r.json()).then(data => {
                this.newAdminMessage = '';
                this.newAdminImage = null;
                this.$refs.adminChatImage.value = '';
                this.loadChatMessages(true);
            }).catch(e => console.warn('Message send error'));
        },
        scrollToBottom() {
            let box = document.getElementById('admin-chat-box');
            if(box) box.scrollTop = box.scrollHeight;
        },
        handleAdminImageUpload(e) {
            if(e.target.files[0]) this.newAdminImage = URL.createObjectURL(e.target.files[0]);
        }
       }"
     class="bg-gray-100 dark:bg-gray-950 min-h-screen">

    @php
        $pendingRequests = DB::table('measurement_requests')
            ->join('customers', 'measurement_requests.customer_id', '=', 'customers.id')
            ->select('measurement_requests.*', 'customers.name as customer_name', 'customers.mobile as customer_mobile')
            ->where('measurement_requests.status', 'pending')
            ->orderBy('measurement_requests.created_at', 'desc')
            ->get();

        $pendingFeedbacks = [];
        if (Schema::hasTable('feedbacks')) {
            $pendingFeedbacks = DB::table('feedbacks')
                ->join('orders', 'feedbacks.order_id', '=', 'orders.id')
                ->join('customers', 'orders.customer_id', '=', 'customers.id')
                ->select('feedbacks.*', 'customers.name as customer_name', 'orders.garment_name')
                ->where('feedbacks.is_read', 0)
                ->orderBy('feedbacks.created_at', 'desc')
                ->get();
        } else {
            $pendingFeedbacks = collect([]); 
        }

        $totalNotifications = $pendingRequests->count() + $pendingFeedbacks->count();
    @endphp

    <aside @mouseenter="hover = true" @mouseleave="hover = false" :class="hover ? 'w-56' : 'w-16'" class="fixed inset-y-0 left-0 z-40 flex flex-col transition-all duration-300 overflow-hidden bg-sky-100 dark:bg-sky-950 border-r border-sky-200 dark:border-sky-900 shadow-md">
        
        <!-- Logo Area -->
        <div class="h-16 flex items-center px-3 mb-2 border-b border-sky-200 dark:border-sky-900 bg-sky-200/50 dark:bg-sky-900/50">
            <div class="flex items-center gap-3 min-w-max">
                <div class="w-10 h-10 rounded-full flex items-center justify-center overflow-hidden bg-white dark:bg-sky-800 shadow-sm border border-sky-300 dark:border-sky-700">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Dream Tailors Logo" class="w-full h-full object-cover">
                </div>
                <div :class="hover ? 'opacity-100' : 'opacity-0'" class="transition-opacity duration-200 whitespace-nowrap">
                    <h1 class="text-sm font-bold text-sky-900 dark:text-sky-100">DREAM TAILORS</h1>
                    <span class="text-[10px] text-sky-700 dark:text-sky-300 font-bold uppercase tracking-wider">Admin</span>
                </div>
            </div>
        </div>

        <nav class="flex-1 p-2 space-y-1.5 mt-2">
            <a href="/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->is('dashboard') || request()->is('/') ? 'bg-sky-300 dark:bg-sky-700 text-sky-900 dark:text-sky-100 shadow-sm' : 'text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white hover:bg-sky-200 dark:hover:bg-sky-800' }}"><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg><span :class="hover ? 'opacity-100' : 'opacity-0'" class="transition-opacity duration-200 text-sm font-medium whitespace-nowrap">Dashboard</span></a>
            <a href="/customer-registration" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->is('customer-registration') ? 'bg-sky-300 dark:bg-sky-700 text-sky-900 dark:text-sky-100 shadow-sm' : 'text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white hover:bg-sky-200 dark:hover:bg-sky-800' }}"><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg><span :class="hover ? 'opacity-100' : 'opacity-0'" class="transition-opacity duration-200 text-sm font-medium whitespace-nowrap">Customer Registration</span></a>
            <a href="/place-order" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->is('place-order') ? 'bg-sky-300 dark:bg-sky-700 text-sky-900 dark:text-sky-100 shadow-sm' : 'text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white hover:bg-sky-200 dark:hover:bg-sky-800' }}"><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg><span :class="hover ? 'opacity-100' : 'opacity-0'" class="transition-opacity duration-200 text-sm font-medium whitespace-nowrap">Place Order</span></a>
            <a href="/order-management" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->is('order-management') ? 'bg-sky-300 dark:bg-sky-700 text-sky-900 dark:text-sky-100 shadow-sm' : 'text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white hover:bg-sky-200 dark:hover:bg-sky-800' }}"><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><span :class="hover ? 'opacity-100' : 'opacity-0'" class="transition-opacity duration-200 text-sm font-medium whitespace-nowrap">Order Management</span></a>
            <a href="/customer-management" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->is('customer-management') ? 'bg-sky-300 dark:bg-sky-700 text-sky-900 dark:text-sky-100 shadow-sm' : 'text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white hover:bg-sky-200 dark:hover:bg-sky-800' }}"><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2 2 0 11-4 0 2 2 0 014 0z"/></svg><span :class="hover ? 'opacity-100' : 'opacity-0'" class="transition-opacity duration-200 text-sm font-medium whitespace-nowrap">Customer Management</span></a>
            <a href="/payment-management" class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 {{ request()->is('payment-management') ? 'bg-sky-300 dark:bg-sky-700 text-sky-900 dark:text-sky-100 shadow-sm' : 'text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white hover:bg-sky-200 dark:hover:bg-sky-800' }}"><svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg><span :class="hover ? 'opacity-100' : 'opacity-0'" class="transition-opacity duration-200 text-sm font-medium whitespace-nowrap">Payment Management</span></a>
        </nav>

        <!-- Bottom Actions Area (Go Back & Logout) -->
        <div class="p-3 mb-2 border-t border-sky-200 dark:border-sky-900 flex flex-col gap-1.5">
            
            <!-- Go Back Button -->
            <a href="{{ url('/') }}" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all duration-200 text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white hover:bg-sky-200 dark:hover:bg-sky-800">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span :class="hover ? 'opacity-100' : 'opacity-0'" class="transition-opacity duration-200 text-sm font-bold whitespace-nowrap text-left">Go Back</span>
            </a>

            <!-- Logout Form -->
            <form action="{{ route('logout') }}" method="POST" class="w-full m-0 p-0">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 hover:text-red-600 dark:hover:text-red-400 transition-all duration-200">
                    <svg class="w-5 h-5 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span :class="hover ? 'opacity-100' : 'opacity-0'" class="transition-opacity duration-200 text-sm font-bold whitespace-nowrap text-left text-red-500 dark:text-red-400">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <div :class="hover ? 'ml-56' : 'ml-16'" class="flex flex-col min-h-screen transition-all duration-300">
        <!-- 🔷 SOLID Baby Blue Header -->
        <header class="sticky top-0 z-20 h-16 flex items-center px-6 bg-sky-100 dark:bg-sky-950 border-b border-sky-200 dark:border-sky-900 shadow-sm">
            <div class="ml-auto flex items-center gap-4">
                
                <!-- Chat Icon -->
                <button @click="openChatPanel()" class="relative p-2 rounded-xl text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white bg-white dark:bg-sky-900 border border-sky-200 dark:border-sky-800 shadow-sm transition-all duration-200 flex items-center gap-2">
                    <svg class="w-5 h-5" :class="unreadChatCount > 0 ? 'animate-blink text-sky-600' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                    <span x-show="unreadChatCount > 0" class="absolute -top-1 -right-1 flex h-3 w-3"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-sky-400 opacity-75"></span><span class="relative inline-flex rounded-full h-3 w-3 bg-sky-500"></span></span>
                </button>

                <!-- Bell Notification Icon -->
                <button @click="notificationOpen = true" class="relative p-2 rounded-xl text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white bg-white dark:bg-sky-900 border border-sky-200 dark:border-sky-800 shadow-sm transition-all duration-200 flex items-center gap-2">
                    @if($totalNotifications > 0)
                        <span id="total-notification-badge" class="absolute top-1.5 right-[3.5rem] w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white dark:border-sky-950"></span>
                        <svg id="notification-bell-icon" class="w-5 h-5 animate-bell text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span id="total-notification-text" class="text-xs font-bold text-red-500">{{ $totalNotifications }} New</span>
                    @else
                        <span id="total-notification-badge" class="absolute top-1.5 right-[3.5rem] w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white dark:border-sky-950" style="display: none;"></span>
                        <svg id="notification-bell-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span id="total-notification-text" class="text-xs font-medium text-sky-700 dark:text-sky-300">0 New</span>
                    @endif
                </button>

                <!-- Dark Mode Toggle -->
                <button @click="toggleDark()" class="p-2 rounded-xl text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white bg-white dark:bg-sky-900 border border-sky-200 dark:border-sky-800 transition-all duration-200">
                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </button>
            </div>
        </header>

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    <!-- 💬 Admin Chat Slide Panel (Baby Blue Solid Theme) -->
    <div x-show="chatPanelOpen" class="fixed inset-0 z-[100] overflow-hidden" x-cloak>
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="chatPanelOpen = false" x-show="chatPanelOpen" x-transition.opacity></div>
        <div class="fixed inset-y-0 right-0 max-w-md w-full flex">
            <!-- Changed Wrapper to Sky Theme -->
            <div class="w-full h-full bg-sky-100 dark:bg-sky-950 border-l border-sky-200 dark:border-sky-900 shadow-2xl transform transition-transform flex flex-col" x-show="chatPanelOpen" x-transition:enter="transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                
                <!-- Chat List View (Grouped by Customer) -->
                <div x-show="!activeChatOrderId" class="flex-1 flex flex-col h-full">
                    <!-- Updated Header -->
                    <div class="px-6 py-5 border-b border-sky-200 dark:border-sky-900 bg-sky-200/50 dark:bg-sky-900/50 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-sky-900 dark:text-sky-100">Customer Chats</h2>
                        <button @click="chatPanelOpen = false" class="text-sky-700 dark:text-sky-300 hover:text-sky-900 dark:hover:text-white transition"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                    </div>
                    
                    <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">
                        <template x-for="group in groupedChats" :key="group.customer_id">
                            <!-- Updated List Item -->
                            <div class="bg-white dark:bg-sky-900 border border-sky-200 dark:border-sky-800 rounded-xl overflow-hidden shadow-sm transition-all duration-200" :class="expandedCustomer === group.customer_id ? 'ring-2 ring-primary-500/50' : ''">
                                
                                <!-- Customer Header -->
                                <div @click="expandedCustomer = (expandedCustomer === group.customer_id ? null : group.customer_id)" class="p-4 cursor-pointer hover:bg-sky-50 dark:hover:bg-sky-800 flex justify-between items-center transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 text-primary-600 dark:text-primary-400 flex items-center justify-center font-bold text-sm border border-primary-200 dark:border-primary-800">
                                            <span x-text="group.customer_name.charAt(0).toUpperCase()"></span>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-sm text-sky-900 dark:text-sky-100" x-text="group.customer_name"></h4>
                                            <p class="text-[11px] font-medium text-sky-600 dark:text-sky-300 mt-0.5" x-text="group.chats.length + (group.chats.length > 1 ? ' Orders' : ' Order')"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span x-show="group.total_unread > 0" class="bg-primary-500 text-white text-[10px] font-bold px-2.5 py-1 rounded-full shadow-sm" x-text="group.total_unread + ' New'"></span>
                                        <svg class="w-5 h-5 text-sky-400 dark:text-sky-500 transition-transform duration-300" :class="expandedCustomer === group.customer_id ? 'rotate-180 text-primary-500' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </div>
                                </div>
                                
                                <!-- Expanded Chats Details -->
                                <div x-show="expandedCustomer === group.customer_id" class="border-t border-sky-100 dark:border-sky-800 bg-sky-50 dark:bg-sky-950">
                                    <template x-for="chat in group.chats" :key="chat.order_id">
                                        <div @click="openSingleChat(chat.order_id)" class="p-3.5 border-b last:border-b-0 border-sky-100 dark:border-sky-800 cursor-pointer transition-colors flex justify-between items-center pl-16 hover:bg-sky-100 dark:hover:bg-sky-800" :class="chat.unread_count > 0 ? 'bg-sky-100 dark:bg-sky-800' : ''">
                                            <div>
                                                <p class="text-xs font-mono font-bold text-primary-600 dark:text-primary-400">ORD-<span x-text="String(chat.order_id).padStart(3, '0')"></span></p>
                                                <p class="text-[11px] font-medium text-sky-600 dark:text-sky-400 mt-1 uppercase tracking-wide" x-text="chat.garment_name"></p>
                                            </div>
                                            <span x-show="chat.unread_count > 0" class="w-2.5 h-2.5 rounded-full bg-primary-500 shadow-sm animate-pulse"></span>
                                        </div>
                                    </template>
                                </div>

                            </div>
                        </template>
                        
                        <div x-show="groupedChats.length === 0" class="flex flex-col items-center justify-center py-16 text-sky-600 dark:text-sky-400">
                            <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                            <span class="text-sm font-medium">No messages found.</span>
                        </div>
                    </div>
                </div>

                <!-- Single Chat View -->
                <div x-show="activeChatOrderId" class="flex-1 flex flex-col h-full bg-sky-50 dark:bg-sky-950">
                    <div class="px-4 py-4 border-b border-sky-200 dark:border-sky-900 flex items-center gap-3 bg-sky-200/50 dark:bg-sky-900/50 shrink-0 shadow-sm z-10">
                        <button @click="activeChatOrderId = null; loadChatList()" class="text-sky-700 dark:text-sky-300 hover:text-primary-600 dark:hover:text-primary-400 transition bg-white dark:bg-sky-800 p-2 rounded-full"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                        <div class="flex-1">
                            <h2 class="text-sm font-bold text-sky-900 dark:text-sky-100 flex items-center gap-2">
                                ORD-<span x-text="String(activeChatOrderId).padStart(3, '0')"></span>
                                <span class="bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-300 text-[10px] px-2 py-0.5 rounded-full font-mono uppercase" x-text="chatList.find(c => c.order_id === activeChatOrderId)?.garment_name"></span>
                            </h2>
                            <p class="text-[11px] text-sky-600 dark:text-sky-400 font-medium mt-0.5" x-text="chatList.find(c => c.order_id === activeChatOrderId)?.customer_name"></p>
                        </div>
                    </div>
                    
                    <div id="admin-chat-box" class="flex-1 overflow-y-auto p-4 custom-scrollbar">
                        <template x-for="msg in chatMessages" :key="msg.id">
                            <div :class="msg.sender === 'admin' ? 'flex justify-end' : 'flex justify-start'" class="mb-4">
                                <div :class="msg.sender === 'admin' ? 'bg-primary-600 text-white rounded-l-2xl rounded-tr-2xl' : 'bg-white dark:bg-sky-800 border border-sky-200 dark:border-sky-700 text-sky-900 dark:text-sky-100 rounded-r-2xl rounded-tl-2xl'" class="max-w-[80%] p-3 shadow-sm">
                                    <template x-if="msg.image">
                                        <img :src="msg.image" class="w-full rounded-lg mb-2 cursor-pointer hover:opacity-90 border border-black/10" @click="window.open(msg.image, '_blank')">
                                    </template>
                                    <p class="text-sm whitespace-pre-wrap leading-relaxed" x-text="msg.message"></p>
                                    <p :class="msg.sender === 'admin' ? 'text-primary-200' : 'text-sky-500 dark:text-sky-400'" class="text-[9px] mt-1 text-right font-medium" x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="p-4 bg-sky-100 dark:bg-sky-950 border-t border-sky-200 dark:border-sky-900 shrink-0">
                        <div x-show="newAdminImage" class="mb-2 relative inline-block">
                            <img :src="newAdminImage" class="h-16 rounded border border-gray-300">
                            <button @click="newAdminImage = null; $refs.adminChatImage.value = ''" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-0.5"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                        </div>
                        <div class="flex items-end gap-2">
                            <label class="cursor-pointer text-sky-500 hover:text-primary-600 transition p-3 bg-white dark:bg-sky-900 border border-sky-200 dark:border-sky-800 rounded-full shrink-0 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                <input type="file" x-ref="adminChatImage" @change="handleAdminImageUpload($event)" accept="image/*" class="hidden">
                            </label>
                            <textarea x-model="newAdminMessage" rows="1" placeholder="Type a reply..." class="w-full bg-white dark:bg-sky-900 border border-sky-200 dark:border-sky-800 rounded-2xl p-3.5 text-sm focus:ring-2 focus:ring-primary-500/50 outline-none resize-none text-sky-900 dark:text-sky-100 custom-scrollbar max-h-24" @keydown.enter.prevent="sendAdminMessage()"></textarea>
                            <button @click="sendAdminMessage()" class="bg-primary-600 hover:bg-primary-700 text-white w-12 h-12 flex items-center justify-center rounded-full shrink-0 shadow-md transition transform hover:-translate-y-0.5"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification Drawer (Baby Blue Solid Theme) -->
    <div x-show="notificationOpen" class="fixed inset-0 z-[100] overflow-hidden" x-cloak>
        <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="notificationOpen = false" x-show="notificationOpen" x-transition.opacity></div>
        <div class="fixed inset-y-0 right-0 max-w-md w-full flex">
            <!-- Changed Wrapper to Sky Theme -->
            <div class="w-full h-full bg-sky-100 dark:bg-sky-950 shadow-2xl transform transition-transform border-l border-sky-200 dark:border-sky-900" x-show="notificationOpen" x-transition:enter="transform transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                
                <div class="h-full flex flex-col">
                    <!-- Updated Header -->
                    <div class="px-6 py-4 border-b border-sky-200 dark:border-sky-900 bg-sky-200/50 dark:bg-sky-900/50 flex justify-between items-center">
                        <div class="flex gap-5">
                            <button @click="activeTab = 'measurements'" :class="activeTab === 'measurements' ? 'text-primary-600 border-primary-600 border-b-2' : 'text-sky-600 hover:text-sky-800 dark:text-sky-300 dark:hover:text-sky-100 border-transparent border-b-2'" class="pb-1 text-sm font-bold transition-all">Requests (<span id="tab-req-count">{{ $pendingRequests->count() }}</span>)</button>
                            <button @click="activeTab = 'feedbacks'" :class="activeTab === 'feedbacks' ? 'text-primary-600 border-primary-600 border-b-2' : 'text-sky-600 hover:text-sky-800 dark:text-sky-300 dark:hover:text-sky-100 border-transparent border-b-2'" class="pb-1 text-sm font-bold transition-all">Feedbacks (<span id="feedback-count">{{ $pendingFeedbacks->count() }}</span>)</button>
                        </div>
                        <button @click="notificationOpen = false" class="p-2 text-sky-600 hover:text-sky-900 dark:text-sky-400 dark:hover:text-white rounded-full hover:bg-sky-300/50 dark:hover:bg-sky-800 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Added 'notifications-container' ID for real-time polling -->
                    <div id="notifications-container" class="flex-1 overflow-y-auto p-5 relative custom-scrollbar">
                        
                        <!-- Tab 1: PENDING MEASUREMENT REQUESTS -->
                        <div x-show="activeTab === 'measurements'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 absolute w-full" x-transition:enter-end="opacity-100 relative w-full" class="space-y-4">
                            @if($pendingRequests->count() > 0)
                                @foreach($pendingRequests as $req)
                                    <!-- Updated Card to Sky Theme -->
                                    <div class="bg-white dark:bg-sky-900 border border-sky-200 dark:border-sky-800 rounded-xl p-4 shadow-sm">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-bold text-sky-900 dark:text-sky-100">{{ $req->customer_name }}</h4>
                                                <p class="text-xs text-sky-600 dark:text-sky-300">{{ $req->customer_mobile }}</p>
                                            </div>
                                            <span class="text-[10px] text-sky-500 dark:text-sky-400">{{ \Carbon\Carbon::parse($req->created_at)->diffForHumans() }}</span>
                                        </div>
                                        @if($req->notes)
                                        <div class="mb-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/30 rounded-lg">
                                            <p class="text-[10px] font-bold text-amber-700 dark:text-amber-500 uppercase mb-1">Reason for update:</p>
                                            <p class="text-sm text-amber-900 dark:text-amber-200 italic">"{{ $req->notes }}"</p>
                                        </div>
                                        @endif
                                        <div class="mb-4">
                                            <p class="text-[10px] font-bold text-sky-700 dark:text-sky-300 uppercase mb-2 border-b border-sky-200 dark:border-sky-800 pb-1">Requested Measurements</p>
                                            <div class="grid grid-cols-2 gap-2">
                                                @php $measurements = json_decode($req->measurements, true); @endphp
                                                @foreach($measurements as $key => $val)
                                                    @if($val)
                                                    <div class="flex justify-between text-xs">
                                                        <span class="text-sky-600 dark:text-sky-400 uppercase">{{ str_replace('_', ' ', $key) }}:</span>
                                                        <span class="font-bold text-sky-900 dark:text-sky-100">{{ $val }}"</span>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="flex gap-2 border-t border-sky-200 dark:border-sky-800 pt-4 mt-2">
                                            <form action="{{ route('admin.measurement.reject', $req->id) }}" method="POST" class="flex-1">
                                                @csrf
                                                <button type="submit" class="w-full py-2 bg-sky-50 dark:bg-sky-800 border border-sky-200 dark:border-sky-700 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 text-sky-700 dark:text-sky-300 rounded-lg text-xs font-bold transition">Reject</button>
                                            </form>
                                            <form action="{{ route('admin.measurement.approve', $req->id) }}" method="POST" class="flex-1">
                                                @csrf
                                                <button type="submit" class="w-full py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-xs font-bold transition shadow-md shadow-primary-500/20">Approve & Update</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex flex-col items-center justify-center h-48 text-center">
                                    <div class="w-12 h-12 bg-white dark:bg-sky-900 rounded-full flex items-center justify-center mb-3 border border-sky-200 dark:border-sky-800"><svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                                    <p class="text-sm font-bold text-sky-700 dark:text-sky-300">All caught up!</p>
                                    <p class="text-xs text-sky-500 dark:text-sky-400">No pending measurement updates.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Tab 2: CUSTOMER FEEDBACKS -->
                        <div x-show="activeTab === 'feedbacks'" style="display: none;" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 absolute w-full" x-transition:enter-end="opacity-100 relative w-full" class="space-y-4">
                            @if($pendingFeedbacks->count() > 0)
                                @foreach($pendingFeedbacks as $fb)
                                    <!-- Updated Card to Sky Theme -->
                                    <div id="feedback-{{ $fb->id }}" class="bg-white dark:bg-sky-900 border border-sky-200 dark:border-sky-800 rounded-xl p-4 shadow-sm relative group">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="text-xs font-bold text-sky-900 dark:text-sky-100">{{ $fb->customer_name }}</p>
                                                <p class="text-[10px] text-sky-600 dark:text-sky-300 font-semibold mt-0.5">{{ $fb->garment_name }} <span class="font-mono">(ORD-#{{ str_pad($fb->order_id, 3, '0', STR_PAD_LEFT) }})</span></p>
                                            </div>
                                            <div class="flex text-amber-400">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3.5 h-3.5 {{ $i <= $fb->rating ? 'text-amber-400' : 'text-sky-200 dark:text-sky-700' }}" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                @endfor
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3 p-3 bg-sky-50 dark:bg-sky-950 border border-sky-100 dark:border-sky-800 rounded-lg">
                                            <p class="text-xs text-sky-800 dark:text-sky-200 italic">"{{ $fb->message ?? 'No additional comments.' }}"</p>
                                        </div>
                                        
                                        <div class="flex justify-between items-center mt-2 border-t border-sky-200 dark:border-sky-800 pt-3">
                                            <span class="text-[9px] text-sky-500 dark:text-sky-400 font-medium">{{ \Carbon\Carbon::parse($fb->created_at)->diffForHumans() }}</span>
                                            <button @click="markFeedbackRead({{ $fb->id }})" class="text-[10px] px-3 py-1 bg-primary-50 dark:bg-primary-900/30 font-bold text-primary-600 dark:text-primary-400 hover:bg-primary-100 rounded-md transition-colors">Mark as read</button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="flex flex-col items-center justify-center h-48 text-center">
                                    <div class="w-12 h-12 bg-white dark:bg-sky-900 rounded-full flex items-center justify-center mb-3 border border-sky-200 dark:border-sky-800"><svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                                    <p class="text-sm font-bold text-sky-700 dark:text-sky-300">All caught up!</p>
                                    <p class="text-xs text-sky-500 dark:text-sky-400">No pending feedbacks.</p>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    @yield('scripts')
</body>
</html>