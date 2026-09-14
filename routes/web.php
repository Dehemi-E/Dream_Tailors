<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth; 

// Controllers
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController; 
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerAuthController; 

// =============================================
// MAIN LANDING PAGE (Welcome Page)
// =============================================
Route::get('/', function () {
    // 🟢 දැන් Admin හෝ Customer ලොග් වෙලා හිටියත් කෙලින්ම Landing Page එකටමයි යන්නේ
    return view('welcome');
})->name('welcome');

// Landing Page එකේ 'Sign In' බටන් එක වැඩ කරන්න Redirect එකක්
Route::redirect('/customer-login', '/login');


// =============================================
// UNIFIED LOGIN ROUTES (Admin & Customer)
// =============================================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// =============================================
// ADMIN DASHBOARD & MANAGEMENT
// =============================================
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Customer Management
Route::get('/customer-registration', function () { return view('customer-registration'); });
Route::post('/customers/store', [CustomerController::class, 'store'])->name('customers.store');
Route::get('/customer-management', [CustomerController::class, 'index'])->name('customers.index');
Route::delete('/customers/{id}', [CustomerController::class, 'destroy'])->name('customers.destroy');
Route::post('/customers/update/{id}', [CustomerController::class, 'update'])->name('customers.update');

// Order Management 
Route::get('/place-order', function () { 
    $customers = DB::table('customers')->get();
    return view('place-order', compact('customers')); 
});
Route::post('/orders/store', [OrderController::class, 'store'])->name('orders.store');
Route::get('/order-management', [OrderController::class, 'index'])->name('orders.index');
Route::post('/orders/update/{id}', [OrderController::class, 'update'])->name('orders.update');
Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');

// Payment Management
Route::get('/payment-management', [PaymentController::class, 'index'])->name('payments.index');
Route::post('/payments/store', [PaymentController::class, 'store'])->name('payments.store'); 
Route::post('/payments/update/{id}', [PaymentController::class, 'update'])->name('payments.update'); 
Route::delete('/payments/{id}', [PaymentController::class, 'destroy'])->name('payments.destroy');


// =============================================
// CUSTOMER PORTAL ROUTES 
// =============================================
Route::get('/customer/dashboard', function () {
    // Customer ලොග් වෙලාද කියලා බලනවා, නැත්නම් ආයේ ලොගින් එකට යවනවා
    if (!Auth::guard('customer')->check()) {
        return redirect('/login');
    }
    
    // ලොග් වෙලා ඉන්න Customer ගේ විස්තර ගන්නවා
    $customer = Auth::guard('customer')->user();
    
    // ඒ Customer ට අදාල Orders ටික Database එකෙන් ගන්නවා
    $orders = DB::table('orders')->where('customer_id', $customer->id)->orderBy('id', 'desc')->get();
    
    // Dashboard එකට දත්ත ටික යවනවා
    return view('customer-dashboard', compact('customer', 'orders'));
})->name('customer.dashboard');

Route::post('/customer/measurements/request', [CustomerController::class, 'submitMeasurementRequest'])->name('customer.measurement.request');

// 🟢 Firebase Device Token එක Save කරන Route එක
Route::post('/customer/save-device-token', [CustomerController::class, 'saveDeviceToken'])->name('customer.save.token');

// Admin Measurement Approval Routes
Route::post('/admin/measurements/approve/{id}', [CustomerController::class, 'approveMeasurement'])->name('admin.measurement.approve');
Route::post('/admin/measurements/reject/{id}', [CustomerController::class, 'rejectMeasurement'])->name('admin.measurement.reject');

// Get Customer Latest Measurements (AJAX)
Route::get('/api/customers/{id}/measurements', [OrderController::class, 'getCustomerMeasurements']);

// Fabric Management API Routes
Route::get('/api/fabrics', [OrderController::class, 'getFabrics']);
Route::post('/api/fabrics', [OrderController::class, 'storeFabric']);
Route::post('/api/fabrics/{id}/image', [OrderController::class, 'updateFabricImage']);
Route::post('/api/fabrics/{id}/toggle', [OrderController::class, 'toggleFabricStock']);
Route::delete('/api/fabrics/{id}', [OrderController::class, 'deleteFabric']);

// Messaging System Routes
Route::get('/api/messages/unread/{user_type}/{user_id?}', [OrderController::class, 'getUnreadCount']);
Route::get('/api/messages/admin-chats', [OrderController::class, 'getAdminChats']);
Route::get('/api/messages/{order_id}', [OrderController::class, 'getOrderMessages']);
Route::post('/api/messages', [OrderController::class, 'sendMessage']);
Route::post('/api/messages/read', [OrderController::class, 'markMessagesAsRead']);

// CUSTOMER FEEDBACK ROUTES
Route::get('/feedback/{order_id}', [PaymentController::class, 'showFeedbackForm'])->name('feedback.show');
Route::post('/feedback/submit', [PaymentController::class, 'submitFeedback'])->name('feedback.submit');
Route::post('/admin/feedback/read/{id}', [PaymentController::class, 'markFeedbackRead'])->name('admin.feedback.read');