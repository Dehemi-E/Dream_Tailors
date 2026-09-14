<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Log;  
use Illuminate\Support\Facades\Hash; 
use Illuminate\Support\Facades\Auth; 

class CustomerController extends Controller
{
    public function index()
    {
        $customers = DB::table('customers')->get();
        return view('customer-management', compact('customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mobile' => 'required|unique:customers,mobile',
            'name' => 'required',
        ], [
            'mobile.unique' => 'This mobile number is already registered in the system.' 
        ]);

        $rawPassword = rand(100000, 999999); 
        
        DB::table('customers')->insert([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email, 
            'gender' => $request->gender,
            'address' => $request->address,
            'tier' => $request->tier,
            'password' => Hash::make($rawPassword), 
            'fcm_token' => '', // 🟢 Registration එකේදී SQL Error එක නවත්තන්න
            'created_at' => now(),
            'updated_at' => now()
        ]);

        if ($request->has('send_whatsapp')) {
            try {
                $loginLink = 'https://doing-childhood-doable.ngrok-free.dev/login';
                $loginUsername = $request->email ? "{$request->mobile} or {$request->email}" : "{$request->mobile}";

                $message = "Dear {$request->name},\n\nWelcome to Dream Tailors! Your customer profile has been successfully created.\n\n*Your Login Details:*\nUsername: {$loginUsername}\nPassword: {$rawPassword}\n\nLogin here: {$loginLink}\n\nYou can now log in to our Client Portal to track your orders, view invoices, and share your tailoring ideas directly with our master tailor.\n\nThank you for choosing Dream Tailors. We look forward to serving you.\n\nBest Regards,\nDream Tailors Team";

                $idInstance = '7107662196'; 
                $apiTokenInstance = '4bb786e9950a4a208e29e5225c00e5fb947a732a56774dc69a';

                $formattedNumber = preg_replace('/^0/', '94', $request->mobile) . '@c.us';

                Http::post("https://api.green-api.com/waInstance{$idInstance}/sendMessage/{$apiTokenInstance}", [
                    'chatId' => $formattedNumber,
                    'message' => $message
                ]);
            } catch (\Exception $e) {
                Log::error('WhatsApp Send Failed: ' . $e->getMessage());
            }
        }

        return back()->with('success', 'Customer saved successfully!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'mobile' => 'required|string|max:10',
            'email' => 'nullable|email|max:255',
            'tier' => 'required|string'
        ]);

        DB::table('customers')->where('id', $id)->update([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'tier' => $request->tier,
            'updated_at' => now()
        ]);

        return back()->with('success', 'Customer details updated successfully!');
    }

    public function destroy($id)
    {
        DB::table('customers')->where('id', $id)->delete();
        return back()->with('success', 'Customer deleted successfully!');
    }

    public function submitMeasurementRequest(Request $request)
    {
        $request->validate([
            'measurements' => 'required|array',
            'notes' => 'nullable|string'
        ]);

        $customerId = Auth::guard('customer')->id(); 

        if (!$customerId) {
            return back()->with('error', 'Session expired. Please login again.');
        }

        DB::table('measurement_requests')->insert([
            'customer_id' => $customerId,
            'measurements' => json_encode($request->measurements),
            'notes' => $request->notes,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Your measurement update request has been sent to the Master Tailor for approval!');
    }

    public function approveMeasurement($id)
    {
        $request = DB::table('measurement_requests')->where('id', $id)->first();
        
        if ($request) {
            $measurements = json_decode($request->measurements, true);
            
            $cleanMeasurements = array_filter($measurements, function($val) {
                return $val !== null && $val !== '';
            });

            $latestOrder = DB::table('orders')
                             ->where('customer_id', $request->customer_id)
                             ->orderBy('id', 'desc')
                             ->first();
            
            if ($latestOrder && count($cleanMeasurements) > 0) {
                DB::table('orders')->where('id', $latestOrder->id)->update($cleanMeasurements);
            }

            DB::table('measurement_requests')->where('id', $id)->update([
                'status' => 'approved', 
                'updated_at' => now()
            ]);
        }
        return back()->with('success', 'Measurements Approved & Updated successfully!');
    }

    public function rejectMeasurement($id)
    {
        DB::table('measurement_requests')->where('id', $id)->update([
            'status' => 'rejected', 
            'updated_at' => now()
        ]);
        return back()->with('success', 'Measurement request canceled.');
    }

    public function saveDeviceToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $customer = Auth::guard('customer')->user();
        
        if ($customer) {
            DB::table('customers')
                ->where('id', $customer->id)
                ->update(['fcm_token' => $request->token]);
                
            return response()->json(['success' => true, 'message' => 'Token saved successfully!']);
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
    }
}