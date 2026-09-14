<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class AuthController extends Controller
{
    // ලොගින් පේජ් එක පෙන්වීම
    public function showLogin()
    {
        if (View::exists('auth.login')) {
            return view('auth.login');
        }
        return view('login');
    }

    // ලොගින් එක Process කිරීම (Unified Login for Admin & Customer)
    public function login(Request $request)
    {
        // Validation චෙක් කිරීම
        $request->validate([
            'login' => 'required', // මේක Email එකක් හෝ Mobile Number එකක් වෙන්න පුළුවන්
            'password' => 'required|string',
        ]);

        $login = $request->input('login');
        $password = $request->input('password');
        $remember = $request->has('remember');

        // Email එකක්ද ගහලා තියෙන්නේ කියලා චෙක් කරනවා
        $isEmail = filter_var($login, FILTER_VALIDATE_EMAIL);

        // 1. මුලින්ම Admin ද කියලා බලනවා (Admin ලොග් වෙන්නේ Email එකෙන්)
        if ($isEmail && Auth::guard('web')->attempt(['email' => $login, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            // 🟢 Admin ලොග් වුණාම දැන් කෙලින්ම Landing Page එකට යනවා
            return redirect()->intended('/')->with('success', 'Welcome back!');
        }

        // 2. Admin නෙවෙයි නම්, Customer ද කියලා බලනවා (Mobile හරි Email හරි දෙකෙන්ම පුළුවන්)
        $customerField = $isEmail ? 'email' : 'mobile';
        if (Auth::guard('customer')->attempt([$customerField => $login, 'password' => $password], $remember)) {
            $request->session()->regenerate();
            // 🟢 Customer ලොග් වුණාමත් දැන් කෙලින්ම Landing Page එකට යනවා
            return redirect()->intended('/');
        }

        // 3. ලොගින් වැරදුණොත් එන Error එක
        return back()->withErrors([
            'login' => 'The provided credentials do not match our records.',
        ])->onlyInput('login');
    }

    // සිස්ටම් එකෙන් Logout වීම
    public function logout(Request $request)
    {
        // Check which guard is active and logout
        if(Auth::guard('customer')->check()){
            Auth::guard('customer')->logout();
        } else {
            Auth::logout();
        }
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // 🟢 දැන් /welcome නෙවෙයි, කෙලින්ම / එකට යන්න ඕනේ (Landing Page)
        return redirect('/');  
    }
}