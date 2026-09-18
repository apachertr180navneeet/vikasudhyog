<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Display the admin login view.
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle authentication for admin/login.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $credentials['username'];
        $password = $credentials['password'];

        // Support demo login
        if (strtolower($username) === 'admin' && $password === 'admin123') {
            session([
                'vu_logged_in' => true,
                'vu_user_name' => 'Administrator',
                'vu_user_role' => 'Super Administrator'
            ]);
            return redirect()->route('admin.dashboard')->with('success', 'Logged in successfully.');
        }

        // Support database users by email or name
        $user = User::where('email', $username)->orWhere('name', $username)->first();
        if ($user && Hash::check($password, $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            session([
                'vu_logged_in' => true,
                'vu_user_name' => $user->name,
                'vu_user_role' => 'Admin'
            ]);
            return redirect()->route('admin.dashboard')->with('success', 'Logged in successfully.');
        }

        return back()->withInput()->with('error', 'Invalid username or password. Please try again.');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('info', 'You have been logged out.');
    }
}
