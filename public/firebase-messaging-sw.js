importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js');
importScripts('https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging.js');

const firebaseConfig = {
  apiKey: "AIzaSyCOPhRea0WNy_sr0AbVI3Th_W650Z5m3yM",
  authDomain: "tailors-559ab.firebaseapp.com",
  projectId: "tailors-559ab",
  storageBucket: "tailors-559ab.firebasestorage.app",
  messagingSenderId: "276284439886",
  appId: "1:276284439886:web:eb69930fdfb5fdf9faf21f"
};

firebase.initializeApp(firebaseConfig);
const messaging = firebase.messaging();

// Background එකේදී මැසේජ් එකක් ආවම පෙන්වන විදිය
messaging.onBackgroundMessage(function(payload) {
  console.log('[firebase-messaging-sw.js] Received background message ', payload);
  
  const notificationTitle = payload.data?.title || payload.notification?.title || 'Dream Tailors';
  const notificationOptions = {
    body: payload.data?.body || payload.notification?.body,
    icon: '/images/logo.jpeg',
    data: payload.data // Backend එකෙන් එවන Order ID වගේ දත්ත
  };
  
  self.registration.showNotification(notificationTitle, notificationOptions);
});

// Notification එක Click කරාම වෙන දේ
self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const data = event.notification.data;
    let url = '/customer/dashboard';
    
    // මැසේජ් එකක් නම් කෙලින්ම Chat එක ඕපන් වෙන්න ලින්ක් එක හදනවා
    if (data && data.is_message === 'true' && data.order_id) {
        url = '/customer/dashboard?open_chat=' + data.order_id;
    }

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(windowClients => {
            for (let i = 0; i < windowClients.length; i++) {
                const client = windowClients[i];
                // දැනටමත් Dashboard එක ඕපන් කරල නම් තියෙන්නේ, ඒකට Focus කරනවා
                if (client.url.includes('/customer/dashboard') && 'focus' in client) {
                    client.postMessage(data); 
                    return client.focus();
                }
            }
            // Dashboard එක ඕපන් කරලා නැත්නම් අලුතින් අදාළ ලින්ක් එක ඕපන් කරනවා
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});