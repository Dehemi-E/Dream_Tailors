<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard · Dream Tailors</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Montserrat', 'sans-serif'], serif: ['Playfair Display', 'serif'] },
                    colors: {
                        themeAccent: { DEFAULT: '#4f46e5', dark: '#4338ca', light: '#6366f1' }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        
        body {
            background: linear-gradient(rgba(15, 23, 42, 0.85), rgba(15, 23, 42, 0.95)), url('https://images.unsplash.com/photo-1593032465175-481ac7f401a0?auto=format&fit=crop&q=80&w=1920') no-repeat center center fixed;
            background-size: cover;
            color: #ffffff;
        }

        .glass-card { 
            background: #1e293b; 
            border: 1px solid #334155; 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4); 
            border-radius: 1.5rem;
        }
        
        .glass-item { 
            background: #0f172a; 
            border: 1px solid #1e293b; 
            transition: all 0.3s ease;
        }
        
        .glass-item:hover { 
            background: #1e293b; 
            border-color: rgba(244, 235, 208, 0.3); 
        }
        
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 4px; }
        
        @keyframes blink-pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(1.1); } }
        .animate-blink { animation: blink-pulse 1.5s ease-in-out infinite; text-shadow: 0 0 10px rgba(79, 70, 229, 0.8); }
        
        @keyframes bell-ring {
            0%, 100% { transform: rotate(0deg); }
            20% { transform: rotate(15deg); }
            40% { transform: rotate(-10deg); }
            60% { transform: rotate(5deg); }
            80% { transform: rotate(-5deg); }
        }
        .animate-ring { 
            animation: bell-ring 1.5s infinite; 
            transform-origin: top center; 
        }

        .btn-accent { 
            background-color: var(--accent-copper, #4f46e5); 
            color: white !important; 
            border-radius: 25px; 
            font-weight: 700; 
            font-size: 11px; 
            letter-spacing: 1px; 
            box-shadow: 0 4px 15px rgba(79, 70, 229, 0.4); 
            transition: all 0.3s ease; 
            text-transform: uppercase; 
        }
        .btn-accent:hover { background-color: #4338ca; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(79, 70, 229, 0.6); }
        
        .btn-logout {
            background-color: #1e293b;
            color: #f43f5e !important;
            border: 1px solid rgba(244, 63, 94, 0.3);
            border-radius: 25px;
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            text-transform: uppercase;
            box-shadow: none;
        }
        .btn-logout:hover {
            background-color: #e11d48;
            color: white !important;
            border-color: #e11d48;
            transform: translateY(-2px);
            box-shadow: 0 0 20px rgba(225, 29, 72, 0.6);
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .toast-enter { animation: slideInRight 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

        @media print {
            body * { visibility: hidden; }
            #invoice-print, #invoice-print * { visibility: visible; color: black !important; }
            #invoice-print { position: absolute !important; left: 0 !important; top: 0 !important; width: 100% !important; background: white !important; margin: 0 !important; padding: 0 !important;}
        }
    </style>
</head>

@php
    $measurementRequests = \DB::table('measurement_requests')
        ->where('customer_id', $customer->id)
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();
    
    $pendingRequestsCount = \DB::table('measurement_requests')
        ->where('customer_id', $customer->id)
        ->where('status', 'pending')
        ->count();
@endphp

<body class="min-h-screen p-4 md:p-8 relative overflow-x-hidden font-sans" 
      x-data="{ 
          measurementModal: false, chatPanel: false, invoiceModal: false,
          selectedOrderId: '', selectedInvoice: null, selectedGarmentName: '',
          messages: [], newMessage: '', newImage: null, unreadCount: 0,
          customerId: {{ $customer->id }},
          
          notificationPanel: false,
          hasUnreadNotifications: {{ $measurementRequests->count() > 0 ? 'true' : 'false' }},
          notificationsSeen: sessionStorage.getItem('notifications_seen_{{ $customer->id }}') === 'true',
          
          get shouldAnimateBell() {
              return this.hasUnreadNotifications && !this.notificationsSeen;
          },
          
          openNotifications() {
              this.hasUnreadNotifications = false;
              this.notificationsSeen = true;
              sessionStorage.setItem('notifications_seen_{{ $customer->id }}', 'true');
              this.notificationPanel = true;
          },

          toasts: [],
          showSummaryModal: false,
          summaryItems: [],
          missedItems: [], 
          isJustReturned: false, // 🟢 FIXED: Flag for detecting Mobile app resume
          
          async init() {
              const urlParams = new URLSearchParams(window.location.search);
              const openChatId = urlParams.get('open_chat');
              if(openChatId) {
                  this.openChat(openChatId);
                  window.history.replaceState({}, document.title, window.location.pathname);
              }

              if ('serviceWorker' in navigator) {
                  navigator.serviceWorker.addEventListener('message', (event) => {
                      const data = event.data;
                      if (data && data.is_message === 'true' && data.order_id) {
                          this.openChat(data.order_id);
                      }
                  });
              }

              // 🟢 FIXED: Mobile App Resume Tracking
              document.addEventListener('visibilitychange', () => {
                  if (document.visibilityState === 'visible') {
                      this.isJustReturned = true; // Tell the system user just came back from background
                      
                      // Show any items that were caught in PC background before this
                      if (this.missedItems.length > 0) {
                          this.summaryItems = [...new Set(this.missedItems)];
                          this.showSummaryModal = true;
                          this.missedItems = []; 
                      }
                      
                      // Immediately poll the server to catch any updates that happened while JS was frozen on Phone
                      this.pollDashboardData().then(() => {
                          this.isJustReturned = false; // Reset flag after poll finishes
                          if (this.missedItems.length > 0) {
                              this.summaryItems = [...new Set([...this.summaryItems, ...this.missedItems])];
                              this.showSummaryModal = true;
                              this.missedItems = [];
                          }
                      });
                  }
              });

              @if(session('success'))
                  setTimeout(() => this.showToast('Success', '{!! addslashes(session('success')) !!}', 'success'), 500);
              @endif
              @if(session('error'))
                  setTimeout(() => this.showToast('Error', '{!! addslashes(session('error')) !!}', 'error'), 500);
              @endif

              await this.fetchUnreadCount();
              this.checkInitialSummary();
              
              if(this.notificationsSeen && {{ $pendingRequestsCount }} === 0 && {{ $measurementRequests->count() }} === 0) {
                  sessionStorage.removeItem('notifications_seen_{{ $customer->id }}');
                  this.notificationsSeen = false;
              }
              
              setInterval(() => { 
                  if(this.chatPanel && this.selectedOrderId) this.loadMessages(false); 
                  this.pollDashboardData(); 
              }, 5000);
          },

          showToast(title, message, type = 'info') {
              const id = Date.now();
              this.toasts.push({ id, title, message, type });
              setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 4000);
          },

          checkInitialSummary() {
              let items = [];
              if (this.unreadCount > 0) items.push(`You have ${this.unreadCount} unread message(s) from the Admin.`);
              
              document.querySelectorAll('.req-item').forEach(el => {
                  let status = el.getAttribute('data-status');
                  if(status === 'approved' || status === 'accepted') items.push('Your measurement update was Approved.');
                  if(status === 'rejected') items.push('Your measurement update was Rejected.');
              });

              if (items.length > 0 && !sessionStorage.getItem('dashboard_summary_seen')) {
                  this.summaryItems = [...new Set(items)]; 
                  this.showSummaryModal = true;
                  sessionStorage.setItem('dashboard_summary_seen', 'true');
              }
          },

          async fetchUnreadCount() { 
              try {
                  let r = await fetch('/api/messages/unread/customer/' + this.customerId);
                  let data = await r.json();
                  this.unreadCount = data.count;
                  return data.count;
              } catch(e) { return this.unreadCount; }
          },
          
          async pollDashboardData() {
              let oldReqs = {};
              document.querySelectorAll('.req-item').forEach(el => {
                  oldReqs[el.getAttribute('data-id')] = el.getAttribute('data-status');
              });

              // Track old order statuses
              let oldOrders = {};
              document.querySelectorAll('.order-item').forEach(el => {
                  oldOrders[el.getAttribute('data-id')] = el.getAttribute('data-status');
              });

              let oldUnread = this.unreadCount;

              try {
                  let res = await fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                  let html = await res.text();
                  const doc = new DOMParser().parseFromString(html, 'text/html');
                  
                  const newOrders = doc.getElementById('orders-list-container');
                  if(newOrders) document.getElementById('orders-list-container').innerHTML = newOrders.innerHTML;
                  
                  const newNotifications = doc.getElementById('notifications-list-container');
                  if(newNotifications) document.getElementById('notifications-list-container').innerHTML = newNotifications.innerHTML;

                  const newChatSelect = doc.getElementById('chat-order-select');
                  const oldChatSelect = document.getElementById('chat-order-select');
                  if(newChatSelect && oldChatSelect) {
                      oldChatSelect.innerHTML = newChatSelect.innerHTML;
                      if(this.selectedOrderId) oldChatSelect.value = this.selectedOrderId;
                  }

                  let newUnread = await this.fetchUnreadCount();
                  if (newUnread > oldUnread) {
                      // 🟢 FIXED: Push to missed if user was on another tab OR just opened the phone browser
                      if (document.hidden || this.isJustReturned) {
                          this.missedItems.push('Admin has sent you a new message.');
                      } else {
                          this.showToast('New Message', 'Admin has sent you a new message.', 'chat');
                      }
                  }

                  // Check Measurement Requests
                  document.querySelectorAll('.req-item').forEach(el => {
                      let id = el.getAttribute('data-id');
                      let newStatus = el.getAttribute('data-status');
                      if (oldReqs[id] && oldReqs[id] !== newStatus) {
                          this.hasUnreadNotifications = true;
                          this.notificationsSeen = false;
                          sessionStorage.removeItem('notifications_seen_{{ $customer->id }}');
                          
                          let msg = '';
                          let tTitle = '';
                          let tType = '';

                          if (newStatus === 'approved' || newStatus === 'accepted') {
                              msg = 'Your measurement request was approved!';
                              tTitle = 'Update Approved';
                              tType = 'success';
                          } else if (newStatus === 'rejected') {
                              msg = 'Your measurement request was rejected.';
                              tTitle = 'Update Rejected';
                              tType = 'error';
                          }

                          if (msg) {
                              // 🟢 FIXED: Push to missed if user was on another tab OR just opened the phone browser
                              if (document.hidden || this.isJustReturned) {
                                  this.missedItems.push(msg);
                              } else {
                                  this.showToast(tTitle, msg, tType);
                              }
                          }
                      }
                  });

                  // Check Order Statuses
                  document.querySelectorAll('.order-item').forEach(el => {
                      let id = el.getAttribute('data-id');
                      let newStatus = el.getAttribute('data-status');
                      if (oldOrders[id] && oldOrders[id] !== newStatus) {
                          let displayStatus = newStatus === 'completed' ? 'Finished' : newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                          let msg = `Order #ORD-${String(id).padStart(3, '0')} status updated to ${displayStatus}.`;
                          
                          // 🟢 FIXED: Push to missed if user was on another tab OR just opened the phone browser
                          if (document.hidden || this.isJustReturned) {
                              this.missedItems.push(msg);
                          } else {
                              this.showToast('Order Updated', msg, 'success');
                          }
                      }
                  });

              } catch(err) { console.log('Polling skipped'); }
          },

          openChat(orderId) { 
              this.selectedOrderId = orderId; 
              this.messages = []; 
              this.chatPanel = true; 
              this.loadMessages(true); 
          },
          
          loadMessages(forceScroll) {
              if(!this.selectedOrderId) return;
              fetch('/api/messages/' + this.selectedOrderId).then(r => r.json()).then(data => {
                  let oldLen = this.messages.length; this.messages = data;
                  if(forceScroll || data.length > oldLen) setTimeout(() => this.scrollToBottom(), 100);
                  this.markAsRead();
              });
          },
          markAsRead() { fetch('/api/messages/read', { method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'}, body: JSON.stringify({order_id: this.selectedOrderId, reader_type: 'customer'}) }).then(() => this.fetchUnreadCount()); },
          sendMessage() {
              if(!this.newMessage && !this.$refs.chatImage.files[0]) return;
              let formData = new FormData();
              formData.append('order_id', this.selectedOrderId); formData.append('customer_id', this.customerId);
              formData.append('sender', 'customer'); formData.append('message', this.newMessage);
              if(this.$refs.chatImage.files[0]) formData.append('image', this.$refs.chatImage.files[0]);
              fetch('/api/messages', { method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}, body: formData }).then(r => r.json()).then(data => {
                  this.newMessage = ''; this.newImage = null; this.$refs.chatImage.value = ''; this.loadMessages(true);
              });
          },
          scrollToBottom() { let box = document.getElementById('chat-box'); if(box) box.scrollTop = box.scrollHeight; },
          handleImageUpload(e) { if(e.target.files[0]) this.newImage = URL.createObjectURL(e.target.files[0]); },
          viewInvoice(payment, garmentName) { this.selectedInvoice = payment; this.selectedGarmentName = garmentName; this.invoiceModal = true; },
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
                              @page { size: A4 portrait; margin: 10mm; }
                              body { background-color: white !important; color: black !important; font-family: 'Montserrat', sans-serif; }
                              * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
                              #invoice-container { max-width: 210mm; margin: 0 auto; }
                          </style>
                      </head>
                      <body class='bg-white'>
                          <div id='invoice-container' class='p-8'>${printContents}</div>
                      </body>
                      </html>
                  `);
                  printWindow.document.close();
                  setTimeout(() => { printWindow.focus(); printWindow.print(); printWindow.close(); }, 800);
              } else { alert('Please allow popups in your browser to print the invoice.'); }
          }
      }" :class="{'overflow-hidden': measurementModal || chatPanel || invoiceModal || notificationPanel || showSummaryModal}">

    <!-- Fixed Toast Container -->
    <div class="fixed top-5 right-5 z-[200] flex flex-col gap-3 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true" x-transition.opacity.duration.500ms class="bg-[#1e293b] border border-slate-700 px-5 py-4 rounded-xl shadow-2xl flex items-start gap-3 pointer-events-auto border-l-4 toast-enter"
                 :class="{
                     'border-l-emerald-500': toast.type === 'success',
                     'border-l-rose-500': toast.type === 'error',
                     'border-l-themeAccent': toast.type === 'chat' || toast.type === 'info'
                 }">
                <div class="mt-0.5">
                    <i x-show="toast.type === 'success'" class="fa-solid fa-circle-check text-emerald-500"></i>
                    <i x-show="toast.type === 'error'" class="fa-solid fa-circle-exclamation text-rose-500"></i>
                    <i x-show="toast.type === 'chat'" class="fa-solid fa-comment-dots text-themeAccent"></i>
                    <i x-show="toast.type === 'info'" class="fa-solid fa-bell text-blue-400"></i>
                </div>
                <div>
                    <h4 class="text-xs font-bold text-white tracking-wide uppercase" x-text="toast.title"></h4>
                    <p class="text-[11px] text-slate-400 mt-1 font-medium" x-text="toast.message"></p>
                </div>
            </div>
        </template>
    </div>

    <!-- Summary Modal ("While you were away") -->
    <div x-show="showSummaryModal" class="fixed inset-0 z-[150] flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-sm" x-cloak>
        <div @click.away="showSummaryModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100" class="bg-[#1e293b] border border-slate-700 p-8 rounded-3xl shadow-2xl max-w-sm w-full text-center relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-themeAccent"></div>
            
            <button @click="showSummaryModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-white transition focus:outline-none">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <div class="w-16 h-16 bg-[#0f172a] rounded-full mx-auto flex items-center justify-center mb-5 border border-slate-700 shadow-inner mt-2">
                <i class="fa-solid fa-bell text-2xl text-themeAccent animate-bounce"></i>
            </div>
            <h3 class="text-xl font-serif font-bold text-white mb-1 tracking-wide">Here's what happened</h3>
            <p class="text-xs text-slate-400 mb-6 uppercase tracking-widest font-bold">While you were away</p>
            
            <div class="space-y-3 text-left mb-8 max-h-48 overflow-y-auto custom-scrollbar pr-2">
                <template x-for="item in summaryItems">
                    <div class="flex items-start gap-3 bg-[#0f172a] p-3.5 rounded-xl border border-slate-700/50 shadow-inner">
                        <i class="fa-solid fa-circle-info text-themeAccent mt-0.5 text-xs"></i>
                        <p class="text-xs text-slate-300 font-medium tracking-wide" x-text="item"></p>
                    </div>
                </template>
            </div>
            
            <button @click="showSummaryModal = false" class="w-full btn-accent py-3.5 text-xs">Got it, thanks!</button>
        </div>
    </div>

    <div class="w-full max-w-5xl mx-auto relative z-10 space-y-6 md:space-y-8 animate-[fadeIn_0.5s_ease-out]">

        <!-- Top Navigation / Welcome Bar -->
        <div class="glass-card p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-5 transition-all duration-300">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-full overflow-hidden border-2 border-slate-600 shadow-lg flex-shrink-0 bg-white">
                    <img src="{{ asset('images/logo.jpeg') }}" alt="Dream Tailors Logo" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-serif font-bold tracking-wide text-white">Welcome, {{ explode(' ', $customer->name)[0] }}</h1>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold mt-1">Exclusive Client Portal</p>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <button @click="openNotifications()" class="relative p-3 rounded-full bg-white/5 border border-white/10 text-white hover:text-themeAccent hover:bg-white/10 transition-all focus:outline-none">
                    <span x-show="shouldAnimateBell" class="absolute top-0 right-0 flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-themeAccent opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-themeAccent"></span>
                    </span>
                    <i class="fa-regular fa-bell text-lg transition-colors" :class="shouldAnimateBell ? 'animate-ring text-themeAccent' : ''"></i>
                </button>

                <button @click="chatPanel = true" class="relative p-3 rounded-full bg-white/5 border border-white/10 text-white hover:text-themeAccent hover:bg-white/10 transition-all focus:outline-none">
                    <span x-show="unreadCount > 0" class="absolute top-0 right-0 flex h-2.5 w-2.5"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-themeAccent opacity-75"></span><span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-themeAccent"></span></span>
                    <i class="fa-regular fa-comment-dots text-lg" :class="unreadCount > 0 ? 'animate-blink text-themeAccent' : ''"></i>
                </button>
                
                <a href="{{ url('/') }}" class="px-5 py-2.5 rounded-[25px] border border-white/20 bg-white/5 hover:bg-white/15 text-white font-bold text-[11px] uppercase tracking-widest transition-all flex items-center gap-2">
                    <i class="fa-solid fa-arrow-left"></i> <span class="hidden sm:inline">Go Back</span>
                </a>
                
                <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0 m-0">
                    @csrf
                    <button type="submit" class="btn-logout px-5 py-2.5 flex items-center gap-2">
                        <span class="hidden sm:inline">Sign out</span><i class="fa-solid fa-arrow-right-from-bracket"></i>
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
            <!-- Profile Column -->
            <div class="lg:col-span-1 glass-card p-6 md:p-8 flex flex-col relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-themeAccent/10 rounded-bl-full -mr-10 -mt-10 pointer-events-none"></div>
                <div class="flex items-center gap-3 mb-8 border-b border-slate-700 pb-4">
                    <i class="fa-solid fa-user-tag text-themeAccent text-lg"></i><h3 class="text-xs font-bold uppercase tracking-widest text-slate-100">Your Profile</h3>
                </div>
                <div id="profile-details-container" class="space-y-6 flex-1 flex flex-col">
                    <div><p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-1">Full name</p><p class="text-sm font-semibold text-white tracking-wide">{{ $customer->name }}</p></div>
                    <div><p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-1">Mobile</p><p class="text-sm font-semibold text-white tracking-wide">{{ $customer->mobile }}</p></div>
                    <div>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-2">Membership Tier</p>
                        <span class="inline-block text-[10px] font-bold uppercase tracking-widest px-4 py-1.5 rounded-full border {{ strtolower($customer->tier ?? 'Standard') == 'vip' ? 'bg-amber-500/20 text-amber-400 border-amber-500/50' : 'bg-themeAccent/20 text-indigo-300 border-themeAccent/50' }}"><i class="fa-solid fa-crown mr-1"></i> {{ ucfirst($customer->tier ?? 'Standard') }}</span>
                    </div>
                </div>
                
                <button @click="measurementModal = true" class="mt-6 w-full btn-accent py-3.5 flex justify-center items-center">Update Measurements</button>
            </div>

            <!-- Orders Column -->
            <div class="lg:col-span-2 glass-card p-6 md:p-8 flex flex-col">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-700 pb-4">
                    <i class="fa-solid fa-layer-group text-themeAccent text-lg"></i><h3 class="text-xs font-bold uppercase tracking-widest text-slate-100">Your Wardrobe Orders</h3>
                </div>
                
                <div id="orders-list-container" class="flex-1 max-h-[450px] overflow-y-auto pr-2 custom-scrollbar">
                    @if(isset($orders) && count($orders) > 0)
                        <div class="space-y-4">
                            @foreach($orders as $order)
                                @php
                                    $oId = $order->id ?? 0;
                                    $status = strtolower($order->status ?? 'processing');
                                    $displayStatus = $status === 'completed' ? 'Finished' : ucfirst($status);
                                    try { $payment = \DB::table('payments')->where('order_id', $oId)->first(); $price = $payment ? $payment->amount : 0; } catch(\Exception $e) { $payment = null; $price = 0; }
                                    $statusBadgeClass = match($status) { 'completed' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30', 'measuring' => 'bg-slate-700 text-white border-slate-600', default => 'bg-themeAccent/20 text-indigo-300 border-themeAccent/30', };
                                @endphp
                                
                                <div class="order-item p-4 sm:p-5 rounded-2xl glass-item relative overflow-hidden group" data-id="{{ $oId }}" data-status="{{ $status }}">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-themeAccent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    <div class="flex flex-col sm:flex-row gap-4 sm:gap-5">
                                        <div class="w-full sm:w-24 h-24 rounded-xl overflow-hidden bg-[#0f172a] border border-slate-700 shrink-0 relative shadow-inner">
                                            @if(isset($order->design_image) && $order->design_image) <img src="{{ asset(ltrim($order->design_image, '/')) }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition-opacity"> @else <div class="flex flex-col items-center justify-center h-full text-slate-500"><i class="fa-solid fa-shirt text-2xl"></i></div> @endif
                                        </div>
                                        <div class="flex-1 flex flex-col justify-between">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <span class="font-mono text-[10px] font-bold text-themeAccent bg-themeAccent/10 px-2.5 py-1 rounded-md border border-themeAccent/20 tracking-wider">#{{ str_pad($oId, 3, '0', STR_PAD_LEFT) }}</span>
                                                    <p class="text-sm font-bold text-slate-100 mt-2 tracking-wide">{{ $order->garment_name ?? 'Custom Garment' }}</p>
                                                </div>
                                                <span class="inline-block text-[9px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full border {{ $statusBadgeClass }}">{{ $displayStatus }}</span>
                                            </div>
                                            <div class="pt-3 border-t border-slate-700 flex justify-between items-end">
                                                <div><p class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Total Amount</p><p class="text-sm font-semibold text-white mt-0.5 tracking-wide">Rs. {{ number_format($price, 2) }}</p></div>
                                                
                                                <div class="flex items-center gap-2">
                                                    <!-- Chat button open for all states -->
                                                    <button @click="openChat('{{ $oId }}')" class="btn-accent px-4 py-2 flex items-center gap-2 !text-[10px]"><i class="fa-regular fa-comment-dots"></i> Chat</button>
                                                    
                                                    @if($payment) <button @click="viewInvoice({{ json_encode($payment) }}, '{{ $order->garment_name ?? 'Custom Garment' }}')" class="btn-accent px-4 py-2 flex items-center gap-2 !text-[10px] bg-white !text-slate-900 hover:bg-slate-200"><i class="fa-solid fa-file-invoice"></i> Invoice</button>
                                                    @elseif(isset($order->delivery_date) && $status !== 'completed') <div class="text-right ml-2"><p class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Delivery Date</p><p class="text-xs font-semibold text-white mt-0.5 tracking-wide">{{ $order->delivery_date }}</p></div>
                                                    @elseif(!$payment) <div class="text-right ml-2"><p class="text-[9px] font-bold uppercase tracking-widest text-slate-400">Status</p><p class="text-[10px] font-bold text-white mt-0.5 tracking-wide">{{ $status === 'completed' ? 'Finished' : 'In Production' }}</p></div> @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center h-full min-h-[200px] rounded-2xl border border-dashed border-slate-600 bg-slate-800/50"><i class="fa-solid fa-box-open text-3xl text-slate-500 mb-3"></i><p class="text-xs font-bold uppercase tracking-widest text-slate-400">No active orders found</p></div>
                    @endif
                </div>
            </div>
        </div>
        <div class="text-center pb-8"><span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">© {{ date('Y') }} Dream Tailors · Exclusive Client Portal</span></div>
    </div>

    <!-- 🔔 Notification Side Panel -->
    <div x-show="notificationPanel" class="fixed inset-0 z-[105] overflow-hidden" x-cloak>
        <div class="absolute inset-0 bg-[#0f172a]/80 backdrop-blur-sm transition-opacity" @click="notificationPanel = false" x-show="notificationPanel" x-transition.opacity></div>
        <div class="fixed inset-y-0 right-0 max-w-md w-full flex">
            <div class="w-full h-full bg-[#1e293b] shadow-2xl transform transition-transform flex flex-col border-l border-slate-700" x-show="notificationPanel" x-transition:enter="transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                
                <div class="px-5 py-5 border-b border-slate-700 bg-[#1e293b] shrink-0 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-themeAccent"></div>
                    <div class="flex items-center justify-between mb-1">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-white flex items-center gap-2">
                            <i class="fa-solid fa-bell text-themeAccent"></i> Notifications
                        </h2>
                        <button @click="notificationPanel = false" class="text-slate-400 hover:text-white transition focus:outline-none bg-white/5 p-2 rounded-full hover:bg-white/10">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2 font-medium">Your latest updates and alerts</p>
                </div>

                <div id="notifications-list-container" class="flex-1 overflow-y-auto p-4 custom-scrollbar bg-[#0f172a] space-y-4 relative">
                    
                    @if($measurementRequests->count() > 0)
                        @foreach($measurementRequests as $req)
                            @php
                                $reqStatus = strtolower($req->status ?? 'pending');
                                $colorClass = match($reqStatus) {
                                    'approved', 'accepted' => 'text-emerald-400 group-hover:bg-emerald-400',
                                    'rejected' => 'text-rose-400 group-hover:bg-rose-400',
                                    default => 'text-themeAccent group-hover:bg-themeAccent',
                                };
                                $iconClass = match($reqStatus) {
                                    'approved', 'accepted' => 'fa-check-circle',
                                    'rejected' => 'fa-times-circle',
                                    default => 'fa-clock',
                                };
                            @endphp
                            
                            <div class="req-item bg-[#1e293b] border border-slate-700 p-4 rounded-2xl shadow-sm hover:border-slate-500 transition-colors group relative overflow-hidden" data-id="{{ $req->id }}" data-status="{{ $reqStatus }}">
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-slate-600 {{ str_replace('text-', 'group-hover:bg-', explode(' ', $colorClass)[0]) }} transition-colors"></div>
                                
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-[9px] font-bold uppercase tracking-widest px-2.5 py-1 rounded-md bg-[#0f172a] border border-slate-700 text-slate-300 shadow-inner">Measurement Update</span>
                                    <div class="text-right">
                                        <p class="text-[9px] font-bold text-slate-400 tracking-wide">{{ \Carbon\Carbon::parse($req->created_at)->format('Y-m-d') }}</p>
                                        <p class="text-[8px] text-slate-500 font-semibold mt-0.5">{{ \Carbon\Carbon::parse($req->created_at)->format('h:i A') }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex gap-3.5 items-start mt-2">
                                    <div class="w-9 h-9 rounded-full bg-[#0f172a] border border-slate-700 flex items-center justify-center shrink-0 shadow-inner">
                                        <i class="fa-solid {{ $iconClass }} text-sm {{ explode(' ', $colorClass)[0] }}"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-white tracking-wide">Request {{ ucfirst($reqStatus) }}</h4>
                                        <p class="text-[10px] text-slate-400 mt-1.5 leading-relaxed">
                                            @if($reqStatus == 'approved' || $reqStatus == 'accepted')
                                                Your recent measurement update has been reviewed and approved by the master tailor.
                                            @elseif($reqStatus == 'rejected')
                                                Your measurement update was rejected. Please contact the tailor for clarification.
                                            @else
                                                Your measurements have been submitted and are currently awaiting review.
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="h-full flex flex-col items-center justify-center text-slate-500 opacity-80 pt-10">
                            <i class="fa-regular fa-bell-slash text-4xl mb-4 opacity-40"></i>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">All caught up!</p>
                            <p class="text-[9px] text-slate-500 mt-1">No new notifications</p>
                        </div>
                    @endif
                </div>
                
                <div class="p-4 bg-[#1e293b] border-t border-slate-700 shrink-0 text-center">
                    <button class="text-[10px] font-bold text-slate-400 hover:text-white uppercase tracking-widest transition-colors">
                        <i class="fa-solid fa-check-double mr-1"></i> Mark all as read
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- 💬 Live Chat Panel -->
    <div x-show="chatPanel" class="fixed inset-0 z-[100] overflow-hidden" x-cloak>
        <div class="absolute inset-0 bg-[#0f172a]/80 backdrop-blur-sm transition-opacity" @click="chatPanel = false" x-show="chatPanel" x-transition.opacity></div>
        <div class="fixed inset-y-0 right-0 max-w-md w-full flex">
            <div class="w-full h-full bg-[#1e293b] shadow-2xl transform transition-transform flex flex-col border-l border-slate-700" x-show="chatPanel" x-transition:enter="transition ease-in-out duration-300" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in-out duration-300" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
                <div class="px-5 py-5 border-b border-slate-700 bg-[#1e293b] shrink-0">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-white flex items-center gap-2"><i class="fa-solid fa-user-tie text-themeAccent"></i> Master Tailor Chat</h2>
                        <button @click="chatPanel = false" class="text-slate-400 hover:text-white transition focus:outline-none"><i class="fa-solid fa-xmark text-lg"></i></button>
                    </div>
                    <select id="chat-order-select" x-model="selectedOrderId" @change="loadMessages(true)" class="w-full bg-[#0f172a] border border-slate-700 rounded-xl p-3 text-xs font-bold tracking-wide focus:border-themeAccent outline-none text-white cursor-pointer shadow-inner">
                        <option value="" disabled>-- Select an Order --</option>
                        @if(isset($orders)) 
                            @foreach($orders as $order) 
                                <option value="{{ $order->id }}">Order #{{ str_pad($order->id, 3, '0', STR_PAD_LEFT) }} ({{ $order->garment_name ?? 'Custom' }})</option> 
                            @endforeach 
                        @endif
                    </select>
                </div>
                <div id="chat-box" class="flex-1 overflow-y-auto p-4 custom-scrollbar bg-[#0f172a]">
                    <div x-show="!selectedOrderId" class="h-full flex flex-col items-center justify-center text-slate-500 opacity-80">
                        <i class="fa-regular fa-comments text-4xl mb-3 opacity-50"></i>
                        <p class="text-[10px] font-bold uppercase tracking-widest">Select an order to start</p>
                    </div>

                    <div x-show="selectedOrderId && messages.length === 0" class="h-full flex flex-col items-center justify-center text-slate-500 opacity-80" x-cloak>
                        <i class="fa-regular fa-paper-plane text-4xl mb-3 opacity-50"></i>
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">No messages yet</p>
                        <p class="text-[9px] mt-1 text-slate-500">Send a message below to start chatting.</p>
                    </div>

                    <template x-for="msg in messages" :key="msg.id">
                        <div :class="msg.sender === 'customer' ? 'flex justify-end' : 'flex justify-start'" class="mb-4">
                            <div :class="msg.sender === 'customer' ? 'bg-themeAccent text-white rounded-l-2xl rounded-tr-2xl' : 'bg-[#1e293b] border border-slate-700 text-slate-200 rounded-r-2xl rounded-tl-2xl'" class="max-w-[85%] p-3.5 shadow-sm">
                                <template x-if="msg.image"><img :src="msg.image" class="w-full rounded-lg mb-2 cursor-pointer hover:opacity-90 border border-slate-700 shadow-sm" @click="window.open(msg.image, '_blank')"></template>
                                <p class="text-xs whitespace-pre-wrap leading-relaxed font-medium tracking-wide" x-text="msg.message"></p>
                                <p :class="msg.sender === 'customer' ? 'text-indigo-200' : 'text-slate-400'" class="text-[9px] mt-1.5 font-bold tracking-widest text-right" x-text="new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></p>
                            </div>
                        </div>
                    </template>
                </div>
                <div x-show="selectedOrderId" class="p-4 bg-[#1e293b] border-t border-slate-700 shrink-0 relative">
                    <div x-show="newImage" class="absolute bottom-[100%] right-4 mb-2 bg-[#0f172a] p-1.5 rounded-xl border border-slate-700 shadow-xl"><img :src="newImage" class="h-16 w-16 object-cover rounded-lg border border-slate-700"><button @click="newImage = null; $refs.chatImage.value = ''" class="absolute -top-2 -right-2 bg-rose-500 text-white rounded-full w-5 h-5 flex items-center justify-center shadow-sm focus:outline-none"><i class="fa-solid fa-xmark text-[10px]"></i></button></div>
                    <div class="flex items-end gap-3">
                        <label class="cursor-pointer text-slate-400 hover:text-white transition p-3 bg-[#0f172a] rounded-full shrink-0 shadow-inner border border-slate-700 flex items-center justify-center"><i class="fa-solid fa-paperclip"></i><input type="file" x-ref="chatImage" @change="handleImageUpload($event)" accept="image/*" class="hidden"></label>
                        <textarea x-model="newMessage" rows="1" placeholder="Message..." class="w-full bg-[#0f172a] border border-slate-700 rounded-2xl px-4 py-3 text-xs font-medium focus:border-themeAccent outline-none resize-none text-white custom-scrollbar max-h-24 shadow-inner tracking-wide"></textarea>
                        <button @click="sendMessage()" class="bg-themeAccent hover:bg-themeAccent-dark text-white w-10 h-10 rounded-full shrink-0 shadow-md transition transform hover:-translate-y-0.5 focus:outline-none flex items-center justify-center"><i class="fa-solid fa-paper-plane text-xs"></i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 📏 Update Measurements Modal -->
    <div x-show="measurementModal" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true" x-cloak>
        <div x-show="measurementModal" x-transition.opacity class="fixed inset-0 bg-[#0f172a]/80 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="measurementModal" @click.away="measurementModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative transform overflow-hidden rounded-[2rem] bg-[#1e293b] text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl border border-slate-700">
                    <div class="px-6 py-5 sm:px-8 sm:py-6 border-b border-slate-700 bg-[#1e293b] flex justify-between items-center">
                        <div><h3 class="text-lg font-serif text-gold tracking-wide" id="modal-title">Request Measurement Update</h3><p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Submit new measurements for master tailor review.</p></div>
                        <button type="button" @click="measurementModal = false" class="rounded-full w-8 h-8 flex items-center justify-center bg-white/5 text-slate-400 hover:text-white hover:bg-white/10 transition-all focus:outline-none"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form action="{{ route('customer.measurement.request') }}" method="POST" class="p-6 sm:p-8 space-y-8 bg-[#0f172a]">
                        @csrf
                        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-5">
                            @foreach(['collar', 'chest', 'shoulder', 'sleeve_length', 'full_length', 'armhole', 'upper_waist', 'lower_waist', 'hip', 'thigh', 'outseam', 'inseam', 'bottom_hem', 'knee'] as $m)
                            <div>
                                <label class="block text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-1.5">{{ str_replace('_', ' ', $m) }}</label>
                                <div class="relative">
                                    <input type="number" step="0.25" min="0" name="measurements[{{ $m }}]" placeholder="0.0" class="w-full bg-[#1e293b] border border-slate-700 rounded-xl p-3 pr-8 text-xs font-bold text-white focus:border-themeAccent outline-none transition-all shadow-inner">
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-[9px] font-bold text-slate-500">in</span>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div>
                            <label class="block text-[9px] font-bold uppercase tracking-widest text-slate-400 mb-2">Special Instructions / Notes</label>
                            <textarea name="notes" rows="2" placeholder="Explain why you are changing the measurements..." class="w-full bg-[#1e293b] border border-slate-700 rounded-xl p-4 text-xs font-medium focus:border-themeAccent outline-none transition-all text-white resize-none shadow-inner tracking-wide"></textarea>
                        </div>
                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-700">
                            <button type="button" @click="measurementModal = false" class="px-6 py-2.5 rounded-full bg-slate-800 border border-slate-700 text-slate-300 text-[10px] font-bold uppercase tracking-widest hover:bg-slate-700 transition-colors focus:outline-none">Cancel</button>
                            <button type="submit" class="btn-accent px-8 py-2.5"><i class="fa-solid fa-paper-plane mr-2"></i> Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- 🟢 Customer Invoice View/Print Modal -->
    <div x-show="invoiceModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-[#0f172a]/90 backdrop-blur-md no-print p-2 sm:p-4" x-cloak
         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
         
        <div class="bg-[#1e293b] rounded-2xl sm:rounded-[2rem] w-full max-w-3xl shadow-2xl flex flex-col max-h-[95vh] sm:max-h-[90vh] border border-slate-700" @click.away="invoiceModal = false"
             x-transition:enter="transition ease-out duration-300 delay-100" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
            
            <div id="invoice-print" class="px-4 sm:px-10 py-6 sm:py-8 flex-1 overflow-y-auto bg-white text-black relative rounded-t-2xl sm:rounded-t-[2rem]">
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.03]">
                    <span class="text-6xl sm:text-9xl font-black uppercase tracking-widest transform -rotate-45">PAID</span>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start gap-4 sm:gap-0 border-b-2 border-slate-900 pb-4 sm:pb-6 mb-4 sm:mb-6 relative z-10">
                    <div class="flex items-center gap-3 sm:gap-5">
                        <img src="{{ asset('images/logo.jpeg') }}" alt="Dream Tailors Logo" class="w-14 h-14 sm:w-20 sm:h-20 object-cover rounded-lg border border-slate-200 shadow-sm">
                        <div>
                            <h1 class="text-xl sm:text-3xl font-black tracking-tighter uppercase text-slate-900" style="font-family: 'Playfair Display', serif;">Dream Tailors</h1>
                            <p class="text-[9px] sm:text-xs text-slate-600 mt-0.5 sm:mt-1 font-bold tracking-widest uppercase">Professional Tailoring Services</p>
                            <p class="text-[9px] sm:text-xs text-slate-500 mt-0.5">Kurunegala, Sri Lanka</p>
                            <p class="text-[9px] sm:text-xs text-slate-500">Tel: +94 72 970 0050</p>
                        </div>
                    </div>
                    <div class="text-left sm:text-right w-full sm:w-auto">
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-300 uppercase tracking-widest">Invoice</h2>
                        <div class="mt-2 sm:mt-4 flex sm:block gap-4">
                            <div>
                                <p class="text-[9px] sm:text-[10px] font-bold text-slate-500 uppercase tracking-widest">Invoice Number</p>
                                <p class="text-base sm:text-lg font-black text-slate-900" x-text="selectedInvoice ? selectedInvoice.invoice_no : ''"></p>
                            </div>
                            <div class="sm:mt-2">
                                <p class="text-[9px] sm:text-[10px] font-bold text-slate-500 uppercase tracking-widest">Date of Issue</p>
                                <p class="text-sm sm:text-sm font-bold text-slate-900" x-text="selectedInvoice ? selectedInvoice.payment_date : ''"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-6 mb-4 sm:mb-6 relative z-10">
                    <div class="bg-slate-50 p-3 sm:p-4 rounded-xl border border-slate-100">
                        <p class="text-[9px] sm:text-[10px] text-slate-500 uppercase font-bold tracking-widest mb-1">Billed To:</p>
                        <p class="font-black text-slate-900 text-lg sm:text-xl uppercase">{{ $customer->name }}</p>
                    </div>
                    <div class="bg-slate-50 p-3 sm:p-4 rounded-xl border border-slate-100 flex sm:flex-col justify-between sm:justify-center items-center sm:items-end">
                        <p class="text-[9px] sm:text-[10px] text-slate-500 uppercase font-bold tracking-widest mb-0 sm:mb-1">Payment Method:</p>
                        <div class="inline-flex items-center justify-center px-3 sm:px-4 py-1 sm:py-1.5 border-2 border-slate-800 rounded-lg">
                            <span class="font-black text-slate-900 text-sm sm:text-base uppercase tracking-wider" x-text="selectedInvoice ? selectedInvoice.method : ''"></span>
                        </div>
                    </div>
                </div>

                <div class="mb-4 sm:mb-6 relative z-10 overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[300px]">
                        <thead>
                            <tr class="border-b-2 border-slate-800">
                                <th class="py-2 px-3 sm:px-4 text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-slate-700 bg-slate-100 rounded-tl-lg">Item Description</th>
                                <th class="py-2 px-3 sm:px-4 text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-slate-700 bg-slate-100 w-16 sm:w-24 text-center">Qty</th>
                                <th class="py-2 px-3 sm:px-4 text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-slate-700 bg-slate-100 text-right rounded-tr-lg">Amount (LKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-slate-200">
                                <td class="py-3 sm:py-5 px-3 sm:px-4">
                                    <p class="font-black text-slate-900 text-lg sm:text-2xl uppercase tracking-tight leading-none" x-text="selectedGarmentName"></p>
                                    <p class="text-[10px] sm:text-sm text-slate-500 mt-1 sm:mt-2 font-semibold bg-slate-100 inline-block px-2 sm:px-3 py-0.5 sm:py-1 rounded" x-text="selectedInvoice && selectedInvoice.order_id ? 'Order Ref: #ORD-' + String(selectedInvoice.order_id).padStart(3, '0') : ''"></p>
                                </td>
                                <td class="py-3 sm:py-5 px-3 sm:px-4 text-center font-bold text-slate-700 text-base sm:text-lg">1</td>
                                <td class="py-3 sm:py-5 px-3 sm:px-4 text-right font-black text-slate-900 text-base sm:text-xl" 
                                    x-text="selectedInvoice ? (parseFloat(selectedInvoice.amount) + parseFloat(selectedInvoice.discount || 0)).toLocaleString('en-US', {minimumFractionDigits: 2}) : ''">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end mb-6 sm:mb-8 relative z-10">
                    <div class="w-full sm:w-1/2">
                        <div class="flex justify-between items-center py-2 border-b border-slate-200">
                            <span class="text-[10px] sm:text-xs font-bold text-slate-600 uppercase tracking-wider">Subtotal</span>
                            <span class="font-bold text-slate-900 text-sm sm:text-base" x-text="selectedInvoice ? 'Rs. ' + (parseFloat(selectedInvoice.amount) + parseFloat(selectedInvoice.discount || 0)).toLocaleString('en-US', {minimumFractionDigits: 2}) : ''"></span>
                        </div>
                        <div x-show="selectedInvoice && parseFloat(selectedInvoice.discount || 0) > 0" class="flex justify-between items-center py-2 border-b border-slate-200 text-emerald-600">
                            <span class="text-[10px] sm:text-xs font-bold uppercase tracking-wider">Discount Given</span>
                            <span class="font-bold text-sm sm:text-base" x-text="selectedInvoice ? '- Rs. ' + parseFloat(selectedInvoice.discount || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) : ''"></span>
                        </div>
                        <div class="flex justify-between items-center py-3 sm:py-4 mt-3 sm:mt-4 bg-slate-900 px-4 sm:px-6 rounded-xl shadow-md">
                            <span class="text-sm sm:text-base font-black text-white uppercase tracking-widest">Total Paid</span>
                            <span class="font-black text-lg sm:text-2xl text-white" x-text="selectedInvoice ? 'Rs. ' + parseFloat(selectedInvoice.amount).toLocaleString('en-US', {minimumFractionDigits: 2}) : ''"></span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 sm:gap-0 pt-4 sm:pt-6 border-t border-slate-300 relative z-10">
                    <div class="w-full sm:w-auto">
                        <p class="text-xs sm:text-sm font-black text-slate-900">Thank you for choosing Dream Tailors!</p>
                        <p class="text-[9px] sm:text-[11px] font-medium text-slate-500 mt-1 mb-3 sm:mb-4">Scan the QR code below to share your feedback with us.</p>
                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="p-1 border-2 border-slate-200 rounded-lg bg-white">
                                <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=100x100&data={{ url('/feedback') }}/' + (selectedInvoice ? selectedInvoice.order_id : '')" class="w-12 h-12 sm:w-16 sm:h-16" alt="Feedback QR">
                            </div>
                            <div>
                                <p class="text-[8px] sm:text-[10px] font-black uppercase tracking-widest text-slate-900">Scan to Feedback</p>
                                <p class="text-[8px] sm:text-[10px] font-bold text-slate-500 mt-0.5">Tell us how we did!</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center w-full sm:w-56">
                        <div class="border-b-2 border-slate-400 h-8 sm:h-10 mb-1 sm:mb-2"></div>
                        <p class="text-[8px] sm:text-[10px] font-black text-slate-500 uppercase tracking-widest">Authorized Signature</p>
                    </div>
                </div>
            </div>

            <div class="px-4 sm:px-8 py-4 sm:py-5 flex flex-col sm:flex-row justify-end gap-2 sm:gap-3 bg-[#1e293b] rounded-b-2xl sm:rounded-b-[2rem] border-t border-slate-700">
                <button @click="invoiceModal = false" class="w-full sm:w-auto px-6 py-2.5 rounded-full border border-slate-600 text-slate-300 font-bold text-[10px] uppercase tracking-widest hover:bg-slate-700 transition-colors focus:outline-none">Close</button>
                <button @click="printInvoice()" class="w-full sm:w-auto btn-accent px-6 py-2.5 bg-white !text-slate-900 hover:bg-slate-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-print"></i> Print Invoice
                </button>
            </div>
        </div>
    </div>
    
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/10.5.0/firebase-app.js";
        import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/10.5.0/firebase-messaging.js";

        const firebaseConfig = {
            apiKey: "AIzaSyCOPhRea0WNy_sr0AbVI3Th_W650Z5m3yM",
            authDomain: "tailors-559ab.firebaseapp.com",
            projectId: "tailors-559ab",
            storageBucket: "tailors-559ab.firebasestorage.app",
            messagingSenderId: "276284439886",
            appId: "1:276284439886:web:eb69930fdfb5fdf9faf21f"
        };

        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        const vapidKey = 'BMNbWZVH_uxer3oHJn9JpN8JPkEoo1SJTaikxM5Sk7HTIAqqcB1C1GPNwkJS3B1zz9_JLq8Kw8eoYcxWcWjiBCU';

        Notification.requestPermission().then((permission) => {
            if (permission === 'granted') {
                getToken(messaging, { vapidKey: vapidKey }).then((currentToken) => {
                    if (currentToken) {
                        saveTokenToServer(currentToken);
                    }
                }).catch((err) => console.log('Error getting token', err));
            }
        });

        function saveTokenToServer(token) {
            fetch('/customer/save-device-token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ token: token })
            });
        }

        onMessage(messaging, (payload) => {
            const title = payload.data?.title || payload.notification?.title || 'Dream Tailors';
            const body = payload.data?.body || payload.notification?.body || '';
            const orderId = payload.data?.order_id;
            const isMessage = payload.data?.is_message;

            if(confirm(`🔔 ${title}\n\n${body}\n\nClick OK to view.`)) {
                if (isMessage === 'true' && orderId) {
                    window.location.href = `/customer/dashboard?open_chat=${orderId}`;
                } else {
                    window.dispatchEvent(new Event('pollDashboardData'));
                }
            }
        });
    </script>
</body>
</html>