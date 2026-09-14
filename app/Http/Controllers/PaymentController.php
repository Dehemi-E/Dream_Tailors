<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = DB::table('payments')
            ->leftJoin('orders', 'payments.order_id', '=', 'orders.id')
            ->select('payments.*', 'orders.garment_name')
            ->orderBy('payments.id', 'desc')
            ->get();

        $weeklyRevenue = DB::table('payments')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->sum(DB::raw('amount - discount'));

        $customers = DB::table('customers')->get();
        $readyOrders = DB::table('orders')
            ->where('status', 'Ready')
            ->select('id', 'customer_id', 'garment_name', 'garment_category') 
            ->get();

        $completedOrders = DB::table('orders')
            ->where('status', 'Completed')
            ->orderBy('updated_at', 'desc')
            ->get();

        // 🟢 1. Table එකට ගන්නේ Read නොකරපු ඒවා (is_read = 0) විතරයි
        $feedbacks = DB::table('feedbacks')
            ->join('orders', 'feedbacks.order_id', '=', 'orders.id')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->select('feedbacks.*', 'customers.name as customer_name', 'orders.garment_name')
            ->where('feedbacks.is_read', 0) // <--- මෙතනින් තමයි Read කරපු ඒවා අයින් කරන්නේ
            ->orderBy('feedbacks.created_at', 'desc')
            ->get();

        // 🟢 2. හැබැයි Average Rating එක හදන්න Read කරපු/නොකරපු ඔක්කොම ගන්නවා (නැත්නම් average එක අවුල් යනවා)
        $allFeedbacks = DB::table('feedbacks')->get();
        $totalFeedbacks = $allFeedbacks->count();
        $averageRating = $totalFeedbacks > 0 ? round($allFeedbacks->avg('rating'), 1) : 0;
        
        $ratingCounts = [
            5 => $allFeedbacks->where('rating', 5)->count(),
            4 => $allFeedbacks->where('rating', 4)->count(),
            3 => $allFeedbacks->where('rating', 3)->count(),
            2 => $allFeedbacks->where('rating', 2)->count(),
            1 => $allFeedbacks->where('rating', 1)->count(),
        ];

        return view('payments', compact(
            'payments', 'weeklyRevenue', 'readyOrders', 'customers', 'completedOrders', 
            'feedbacks', 'totalFeedbacks', 'averageRating', 'ratingCounts' 
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'method' => 'required|in:Cash,Card,Bank Transfer', 
            'payment_date' => 'required|date' 
        ]);

        $order = DB::table('orders')->where('id', $request->order_id)->first();
        if ($order->status !== 'Ready') {
            return back()->with('error', 'This order is already processed!');
        }

        $customer = DB::table('customers')->where('id', $order->customer_id)->first();
        $customerName = $customer ? $customer->name : 'Unknown Customer';
        $discountAmount = $request->input('discount') ? $request->input('discount') : 0;

        DB::table('payments')->insert([
            'customer_id' => $order->customer_id, 
            'order_id' => $request->order_id,
            'invoice_no' => 'INV-' . date('Y') . '-' . str_pad($request->order_id, 3, '0', STR_PAD_LEFT),
            'customer_name' => $customerName,
            'amount' => $request->amount,
            'discount' => $discountAmount,
            'payment_date' => $request->payment_date, 
            'status' => 'Paid',
            'method' => $request->method,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        DB::table('orders')->where('id', $request->order_id)->update(['status' => 'Completed', 'updated_at' => now()]);

        return back()->with('success', 'Payment processed successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'method' => 'required|in:Cash,Card,Bank Transfer',
        ]);

        $discountAmount = $request->input('discount') ? $request->input('discount') : 0;

        DB::table('payments')->where('id', $id)->update([
            'amount' => $request->amount,
            'discount' => $discountAmount,
            'method' => $request->method,
            'updated_at' => now()
        ]);

        return back()->with('success', 'Payment updated successfully!');
    }

    public function destroy($id)
    {
        $payment = DB::table('payments')->where('id', $id)->first();
        if ($payment) {
            DB::table('orders')->where('id', $payment->order_id)->update(['status' => 'Ready']);
            DB::table('payments')->where('id', $id)->delete();
        }
        return back()->with('success', 'Payment cancelled and order restored!');
    }

    public function showFeedbackForm($id)
    {
        $order = DB::table('orders')->where('id', $id)->first();
        
        if (!$order) {
            abort(404, 'Order not found.');
        }

        $existing = DB::table('feedbacks')->where('order_id', $id)->exists();

        return view('feedback', compact('order', 'existing'));
    }

    public function submitFeedback(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'rating' => 'required|integer|min:1|max:5',
            'message' => 'nullable|string'
        ]);

        DB::table('feedbacks')->insert([
            'order_id' => $request->order_id,
            'rating' => $request->rating,
            // 🟢 Null Error එක හදපු තැන
            'message' => $request->message ?? '', 
            'is_read' => 0, 
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Feedback submitted successfully!');
    }

    // 🟢 Mark as Read Button එක ක්ලික් කරාම වැඩ කරන Function එක
    public function markFeedbackRead($id)
    {
        DB::table('feedbacks')->where('id', $id)->update(['is_read' => 1]);
        return back()->with('success', 'Feedback marked as read and removed from the list!');
    }
}