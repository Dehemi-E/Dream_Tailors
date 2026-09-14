<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{

    public function showLogin()
    {
        return view('customer-login');
    }

    
    public function login(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'password' => 'required'
        ]);

        $customer = DB::table('customers')->where('mobile', $request->mobile)->first();

        
        if ($customer && Hash::check($request->password, $customer->password)) {
        
            $request->session()->put('customer_id', $customer->id);
            return redirect('/customer/dashboard');
        }

        return back()->withErrors(['error' => 'Invalid Mobile Number or Password.']);
    }


    public function dashboard(Request $request)
    {
        
        if (!$request->session()->has('customer_id')) {
            return redirect('/customer/login');
        }

        
        $customerId = $request->session()->get('customer_id');
        $customer = DB::table('customers')->where('id', $customerId)->first();

        // 🟢 මෙන්න මෙතන හරියටම 'customer_id' එකෙන් ඕඩර්ස් ටික ෆිල්ටර් කරලා ගන්නවා
        $orders = DB::table('orders')
                    ->where('customer_id', $customer->id) 
                    ->orderBy('id', 'desc')
                    ->get();

        // දැන් Customer සහ Orders දෙකම Dashboard එකට යවනවා
        return view('customer-dashboard', compact('customer', 'orders'));
    }

    
    public function logout(Request $request)
    {
        $request->session()->forget('customer_id');
        return redirect('/customer/login');
    }
}